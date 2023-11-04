<?php
declare(strict_types=1);

namespace App\Symfony\ArgumentResolver;

use App\Symfony\Attribute\MapForm;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class FormResolver implements ValueResolverInterface
{
    public function __construct(
        private ManagerRegistry $registry,
        private FormFactoryInterface $formFactory,
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @var MapForm|null $options */
        $options = $argument->getAttributes(MapForm::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!$options) {
            return;
        }

        if ($manager = $this->registry->getManagerForClass($options->objectClass)) {
            $object = $this->findEntity($manager, $request, $options);
        }

        /** @psalm-suppress MixedMethodCall */
        yield $this->formFactory->create($options->formType, $object ?? new ($options->objectClass)())->handleRequest($request);
    }

    private function findEntity(ObjectManager $manager, Request $request, MapForm $options): ?object
    {
        /** @psalm-suppress all */
        $id = $request->get($options->id);

        if (!$id) {
            return null;
        }

        return $manager->find($options->objectClass, $id);
    }
}
