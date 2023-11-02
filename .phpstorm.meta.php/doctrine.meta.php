<?php

namespace PHPSTORM_META {
    override(\Doctrine\Persistence\ManagerRegistry::getConnection(), map([
        '' => \Doctrine\DBAL\Connection::class,
    ]));

    override(\Doctrine\ORM\EntityManagerInterface::getRepository(), map([
        '\App\Core\Entity\Post' => \App\Core\Repository\PostRepository::class,
    ]));
}
