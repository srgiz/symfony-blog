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

    private ?Producer $producer = null;
    private ?KafkaConsumer $consumer = null;

    public function __construct(
        /** @var array{brokers: string, topic: string} */
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

        unset($options['brokers']);
        $configuration['brokers'] = $uri['host'].':'.($uri['port'] ?? 9092);

        return new self($options + $configuration);
    }

    public function producev(string $body, array $headers = [], ?string $key = null): void
    {
        $producer = $this->getProducer();
        $topic = $producer->newTopic($this->options['topic']);
        $produceWithHeaders = $this->options['produce_with_headers'] ?? true;

        $topic->producev(RD_KAFKA_PARTITION_UA, 0, $body, $key, $produceWithHeaders ? $headers : null);

        // trigger callback queues
        $producer->poll(1);

        $err = $producer->flush(5000);

        if (RD_KAFKA_RESP_ERR__TIMED_OUT === $err) {
            throw new \RuntimeException('Failed to flush producer. Messages might not have been delivered.');
        }
    }

    public function get(): Message
    {
        $consumer = $this->getConsumer();

        do {
            $message = $consumer->consume(100);

            if (RD_KAFKA_RESP_ERR_NO_ERROR !== $message->err) {
                break;
            }

            // Если нет headers.type в message, подставляем type из конфига для десериализации
            if (
                !isset($message->headers, $message->headers['type'])
                && isset($this->options['headers']['type'])
            ) {
                $message->headers['type'] = $this->options['headers']['type'];
            }

            // Обработка только определенных сообщений подходящих по фильтру
            $skip = false;

            if (isset($this->options['filter_value'])) {
                $data = json_decode($message->payload, true, flags: JSON_THROW_ON_ERROR);

                foreach ($this->options['filter_value'] as $key => $value) {
                    if (($data[$key] ?? null) !== $value) {
                        $skip = true;
                        break;
                    }
                }
            }

            // Если сообщение не подошло по фильтру
            if ($skip) {
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
        if ($this->producer) {
            return $this->producer;
        }

        $conf = $this->createConf();
        //$conf->set('socket.timeout.ms', (string) 50);
        //$conf->set('queue.buffering.max.messages', (string) 1000);
        //$conf->set('max.in.flight.requests.per.connection', (string) 1);
        /*$conf->setDrMsgCb(
            function (Producer $producer, Message $message): void {
                if ($message->err !== RD_KAFKA_RESP_ERR_NO_ERROR) {
                    // Perform your error handling here using $message->errstr()
                }
            }
        );*/
        /*$conf->setLogCb(
            function (Producer $producer, int $level, string $facility, string $message): void {
                // Perform your logging mechanism here
            }
        );*/
        //$conf->set('statistics.interval.ms', (string) 1000);
        //$conf->setStatsCb(
        //    function (\RdKafka\Producer $producer, string $json, int $json_len, $opaque): void {
        // Perform your stats mechanism here ...
        //    }
        //);

        return $this->producer = new Producer($conf);
    }

    private function getConsumer(): KafkaConsumer
    {
        if ($this->consumer) {
            return $this->consumer;
        }

        $conf = $this->createConf();
        $conf->set('enable.auto.commit', 'false');
        $conf->set('auto.offset.reset', 'latest');
        $conf->set('enable.partition.eof', 'true');

        foreach (self::CONF_CONSUMER_OPTIONS as $name) {
            if (isset($this->options[$name])) {
                $conf->set($name, $this->options[$name]);
            }
        }

        //$conf->setLogCb(
        //    function (KafkaConsumer $consumer, int $level, string $facility, string $message): void {
        // Perform your logging mechanism here
        //echo $message;
        //    }
        //);

        $this->consumer = new KafkaConsumer($conf);
        $this->consumer->subscribe([$this->options['topic']]);

        return $this->consumer;
    }

    private function createConf(): Conf
    {
        $conf = new Conf();
        $conf->set('bootstrap.servers', $this->options['brokers']);
        $conf->set('log_level', (string) LOG_DEBUG);
        $conf->set('debug', 'all');

        return $conf;
    }
}
