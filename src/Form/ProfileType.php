<?php
// src/Form/ProfileType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', TextType::class, [
                'label' => 'Nombre de usuario',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña nueva',
                'required' => false, // La contraseña no es obligatoria
                'mapped' => false,   // No se mapea al campo 'password' de la entidad User
            ])
            ->add('fotoPerfil', FileType::class, [
                'label' => 'Foto de perfil',
                'required' => false,
                'mapped' => false, // No está directamente mapeado a la entidad
                'attr' => ['accept' => 'image/*'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Guardar cambios',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Asegúrate de que esté mapeado a la entidad correcta
        ]);
    }
}
