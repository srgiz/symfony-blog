<?php

declare(strict_types=1);

namespace App\Symfony\Form\Type;

use App\Core\Blog\Enum\StatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', TextType::class)
            ->add('status', ChoiceType::class, [
                'choices' => [
                    StatusEnum::Draft->name => StatusEnum::Draft->value,
                    StatusEnum::Active->name => StatusEnum::Active->value,
                ],
            ])
            ->add('title', TextType::class)
            ->add('preview', TextareaType::class)
            ->add('content', TextareaType::class)
        ;
    }
}
