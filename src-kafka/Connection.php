<?php

declare(strict_types=1);

namespace SerginhoLD\KafkaTransport;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\Producer;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Exception\TransportException;

/**
 * @internal
 */
class Connection implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const array CONF_CONSUMER_OPTIONS = [
        'group.id',
        'auto.offset.reset', // 'earliest', 'latest'
    ];

    private const array CONF_PRODUCER_OPTIONS = [
        'max.in.flight.requests.per.connection',
        'queue.buffering.max.messages',
        'socket.timeout.ms',
    ];

    private ?Producer $producer = null;
    private ?KafkaConsumer $consumer = null;
    private ?string $lastProduceError = null;

    public function __construct(
        /** @var array{brokers: string, topic: string, "group.id": string} */
        #[\SensitiveParameter]
        private readonly array $options,
    ) {
    }

    public static function fromDsn(#[\SensitiveParameter] string $dsn, array $options): self
    {
        if (false === $uri = parse_url($dsn)) {
            throw new InvalidArgumentException('The given Kafka Messenger DSN is invalid.');
        }

        $configuration = [];

        if (isset($uri['query'])) {
            parse_str($uri['query'], $configuration);
        }

        unset($options['metadata.broker.list']);
        $configuration['metadata.broker.list'] = $uri['host'].':'.($uri['port'] ?? 9092);

        return new self($options + $configuration);
    }

    public function producev(string $body, array $headers = [], ?string $key = null): void
    {
        $producer = $this->getProducer();
        $topic = $producer->newTopic($this->options['topic']);
        $produceWithHeaders = $this->options['produce_with_headers'] ?? true;

        $topic->producev(RD_KAFKA_PARTITION_UA, 0, $body, $key, $produceWithHeaders ? $headers : null);
        $producer->poll(1); // trigger callback queues
        $err = $producer->flush(5000);

        if (RD_KAFKA_RESP_ERR__TIMED_OUT === $err) {
            throw new TransportException('Failed to flush producer. Messages might not have been delivered.');
        }

        if (null !== $this->lastProduceError) {
            throw new TransportException($this->lastProduceError);
        }
    }

    public function get(): Message
    {
        do {
            $message = $this->getConsumer()->consume(100);
            $skip = false;

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR__PARTITION_EOF: // No more messages
                case RD_KAFKA_RESP_ERR__TIMED_OUT: // Attempt to connect again
                    $skip = true;
                    break;

                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    break;

                default:
                    throw new TransportException($message->errstr(), $message->err);
            }

            if (!$skip) {
                // Подставляем type из конфига для десериализации
                if (
                    isset($this->options['headers']['type'])
                    //&& !isset($message->headers, $message->headers['type'])
                ) {
                    $message->headers['type'] = $this->options['headers']['type'];
                }

                // Обработка только определенных сообщений подходящих по фильтру
                if (isset($this->options['filter_value'])) {
                    $data = json_decode($message->payload, true, flags: JSON_THROW_ON_ERROR);

                    foreach ($this->options['filter_value'] as $key => $value) {
                        if (($data[$key] ?? null) !== $value) {
                            $skip = true;
                            break;
                        }
                    }
                }
            }

            // Если сообщение не подошло по фильтру
            if ($skip && RD_KAFKA_RESP_ERR_NO_ERROR === $message->err) {
                $this->ack($message);
                $this->logger?->debug('Message skipped due to filter.', [
                    'topic' => $message->topic_name,
                    'group.id' => $this->options['group.id'] ?? null,
                    'key' => $message->key,
                    'partition' => $message->partition,
                    'offset' => $message->offset,
                    'len' => $message->len,
                ]);
            }
        } while ($skip);

        return $message;
    }

    public function ack(Message $message): void
    {
        $this->getConsumer()->commit($message);
    }

    private function getProducer(): Producer
    {
        $this->lastProduceError = null;

        if ($this->producer) {
            return $this->producer;
        }

        $conf = $this->createConf();
        $conf->set('max.in.flight.requests.per.connection', '1');
        $conf->set('queue.buffering.max.messages', '1000');
        $conf->set('socket.timeout.ms', '50');
        $conf->setDrMsgCb(
            function (Producer $producer, Message $message): void {
                if (RD_KAFKA_RESP_ERR_NO_ERROR !== $message->err) {
                    $this->lastProduceError = $message->errstr(); // Throwing from FFI callbacks is not allowed
                    $this->logger?->error($message->errstr(), [
                        'topic' => $message->topic_name,
                        'partition' => $message->partition,
                        'key' => $message->key,
                    ]);
                }
            }
        );
        //$conf->setLogCb(
        //    function (Producer $producer, int $level, string $facility, string $message): void {
        //        $this->logger?->debug($message, [
        //            'level' => $level,
        //            'facility' => $facility,
        //        ]);
        //    }
        //);

        foreach (self::CONF_PRODUCER_OPTIONS as $name) {
            if (isset($this->options[$name])) {
                $conf->set($name, (string) $this->options[$name]);
            }
        }

        $this->logConf('Producer configured.', $conf);

        return $this->producer = new Producer($conf);
    }

    private function getConsumer(): KafkaConsumer
    {
        if ($this->consumer) {
            return $this->consumer;
        }

        $conf = $this->createConf();
        $conf->set('auto.offset.reset', 'latest');
        $conf->set('enable.auto.commit', 'false');
        $conf->set('enable.partition.eof', 'true');

        foreach (self::CONF_CONSUMER_OPTIONS as $name) {
            if (isset($this->options[$name])) {
                $conf->set($name, (string) $this->options[$name]);
            }
        }

        $this->logConf('Consumer configured.', $conf);
        $this->consumer = new KafkaConsumer($conf);
        $this->consumer->subscribe([$this->options['topic']]);

        return $this->consumer;
    }

    private function createConf(): Conf
    {
        $conf = new Conf(); // https://github.com/confluentinc/librdkafka/blob/master/CONFIGURATION.md
        $conf->set('metadata.broker.list', $this->options['metadata.broker.list']);
        //$conf->set('log_level', (string) LOG_DEBUG);
        //$conf->set('debug', 'all');

        if (isset($this->options['sasl.username'], $this->options['sasl.password'])) {
            $conf->set('sasl.username', $this->options['sasl.username']);
            $conf->set('sasl.password', $this->options['sasl.password']);
        }

        return $conf;
    }

    private function logConf(string $message, Conf $conf): void
    {
        $options = $this->options;

        array_walk($options, static function (&$value, $key) {
            if (in_array($key, ['sasl.username', 'sasl.password'], true)) {
                $value = '<redacted>';
            }
        });

        $this->logger?->info($message, [
            'options' => $options,
            //'dump' => $conf->dump(),
        ]);
    }
}
