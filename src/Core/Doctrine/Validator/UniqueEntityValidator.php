<?php
declare(strict_types=1);

namespace App\Core\Doctrine\Validator;

use App\Core\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception;

class UniqueEntityValidator extends ConstraintValidator
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var object $value */
        if (!$constraint instanceof UniqueEntity) {
            throw new Exception\UnexpectedTypeException($constraint, UniqueEntity::class);
        }

        if (empty($constraint->fields)) {
            throw new Exception\MissingOptionsException('Option "fields" cannot be empty', ['fields']);
        }

        /** @var array<string, string> $fields ['assertField' => 'entityField'] */
        $fields = [];

        foreach ($constraint->fields as $k => $entityField) {
            $assertField = is_numeric($k) ? $entityField : $k;
            $fields[$assertField] = $entityField;
        }

        /** @var class-string $entityClass */
        $entityClass = $constraint->entityClass ?? $value::class;
        $em = $this->doctrine->getManagerForClass($entityClass);

        if (null === $em) {
            throw new Exception\ConstraintDefinitionException(
                sprintf('Unable to find the object manager associated with an entity of class "%s".', $entityClass)
            );
        }

        // если указан класс сущности, то нужно брать ReflectionClass от dto $value, иначе из доктрины можно взять ReflectionClass сущности $value
        // @phpstan-ignore-next-line
        $reflectionClass = $constraint->entityClass ? new \ReflectionClass($value::class) : $em->getClassMetadata($value::class)->getReflectionClass();
        $criteria = Criteria::create();
        $invalidValue = [];

        foreach ($fields as $assertField => $entityField) {
            $assertProperty = $reflectionClass->getProperty($assertField);
            $assertValue = (string)$assertProperty->getValue($value);

            $criteria->andWhere(new Comparison($entityField, Comparison::EQ, $assertValue));
            $invalidValue[$entityField] = $assertValue;
        }

        /** @var EntityRepository $repository */
        $repository = $em->getRepository($entityClass);

        $count = (int)$repository
            ->createQueryBuilder('e')
            ->select(sprintf('count(e.%s)', $constraint->identifier))
            ->addCriteria($criteria)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (!$count) {
            return;
        }

        $invalidValue = (string)json_encode($invalidValue);

        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->fields[0])
            ->setParameter('{{ value }}', $invalidValue)
            ->setInvalidValue($invalidValue)
            ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
            ->setCause($count)
            ->addViolation();
    }
}
