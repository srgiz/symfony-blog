<?php

declare(strict_types=1);

namespace SerginhoLD\KafkaTransport;

use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\Producer;

class Connection
{
    private ?Producer $producer = null;

    public function producev(string $body, array $headers = []): void
    {
        $producer = $this->getProducer();
        $topic = $producer->newTopic('topic_test_2');

        $topic->producev(RD_KAFKA_PARTITION_UA, 0, $body, null, $headers);

        // trigger callback queues
        $producer->poll(1);

        $err = $producer->flush(5000);

        if ($err === RD_KAFKA_RESP_ERR__TIMED_OUT) {
            throw new \RuntimeException('Failed to flush producer. Messages might not have been delivered.');
        }
    }

    private function getProducer(): Producer
    {
        if ($this->producer) {
            return $this->producer;
        }

        $conf = new Conf();
        $conf->set('bootstrap.servers', 'kafka:9092');
        //$conf->set('socket.timeout.ms', (string) 50);
        //$conf->set('queue.buffering.max.messages', (string) 1000);
        //$conf->set('max.in.flight.requests.per.connection', (string) 1);
        $conf->setDrMsgCb(
            function (Producer $producer, Message $message): void {
                if ($message->err !== RD_KAFKA_RESP_ERR_NO_ERROR) {
                    // Perform your error handling here using $message->errstr()
                }
            }
        );
        $conf->set('log_level', (string) LOG_DEBUG);
        $conf->set('debug', 'all');
        $conf->setLogCb(
            function (Producer $producer, int $level, string $facility, string $message): void {
                // Perform your logging mechanism here
            }
        );
        //$conf->set('statistics.interval.ms', (string) 1000);
        //$conf->setStatsCb(
        //    function (\RdKafka\Producer $producer, string $json, int $json_len, $opaque): void {
                // Perform your stats mechanism here ...
        //    }
        //);

        return new Producer($conf);
    }
}
