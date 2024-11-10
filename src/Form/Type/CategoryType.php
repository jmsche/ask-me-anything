<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use Leapt\SlugTypeBundle\Form\SlugType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'category.field.name',
            ])
            ->add('slug', SlugType::class, [
                'label'  => 'category.field.slug',
                'target' => 'name',
            ])
            ->add('icon', TextType::class, [
                'label' => 'category.field.icon',
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'category.field.weight',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
