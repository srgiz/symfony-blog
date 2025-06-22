<?php

declare(strict_types=1);

namespace App\Symfony\Controller;

use App\Infrastructure\CommandBus\CommandBus;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services[CommandBus::class] = CommandBus::class;

        return $services;
    }

    final protected function handleCommand(object $command, ...$middlewares): ?object
    {
        return ($this->container->get(CommandBus::class))($command, ...$middlewares);
    }
}
