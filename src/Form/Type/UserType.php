<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Enums\Role;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'user.field.username',
            ])
            ->add('role', EnumType::class, [
                'label'        => 'user.field.role',
                'class'        => Role::class,
                'placeholder'  => '',
                'choice_label' => fn (Role $role): string => 'user.choices.role.' . $role->value,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'user.field.password',
                'help'  => 'user.help.password',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => User::class,
            'validation_groups'  => static function (FormInterface $form) {
                $data = $form->getData();
                if (null === $data->getId()) {
                    return ['Default', 'Create'];
                }

                return ['Default'];
            },
        ]);
    }
}
