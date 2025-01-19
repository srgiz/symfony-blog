<?php

declare(strict_types=1);

namespace App\Domain\Blog\Message;

readonly class TestMessage
{
    public function __construct(
        public string $testValue,
        public string $type = 'test',
    ) {
    }
}

/*
Redis

s:665:"{"body":"O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:35:\\\"App\\\\Domain\\\\Blog\\\\Message\\\\TestMessage\\\":2:{s:9:\\\"testValue\\\";s:16:\\\"val678c1374b5d65\\\";s:4:\\\"type\\\";s:4:\\\"test\\\";}}","headers":[]}";
*/
