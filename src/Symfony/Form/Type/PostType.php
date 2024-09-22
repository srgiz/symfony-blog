<?php

declare(strict_types=1);

namespace App\Symfony\Form\Type;

use App\Domain\Blog\Entity\Post\Status;
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
            ->add('status', ChoiceType::class, [
                'choices' => [
                    Status::Draft->name => Status::Draft->value,
                    Status::Active->name => Status::Active->value,
                ],
            ])
            ->add('title', TextType::class)
            ->add('preview', TextareaType::class)
            ->add('content', TextareaType::class)
        ;
    }
}
