<?php

declare(strict_types=1);

namespace App\Symfony\Attribute;

use App\Symfony\ArgumentResolver\FormResolver;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class MapForm extends ValueResolver
{
    public function __construct(
        /** @var class-string<FormTypeInterface> */
        public string $formType,
        /** @var class-string */
        public string $objectClass,
        public string $id = 'id',
    ) {
        parent::__construct(FormResolver::class);
    }
}
