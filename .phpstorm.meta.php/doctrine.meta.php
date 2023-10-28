<?php

namespace PHPSTORM_META {
    override(\Doctrine\ORM\EntityManagerInterface::getRepository(), map([
        '\App\Core\Entity\Post' => \App\Core\Repository\PostRepository::class,
    ]));
}
