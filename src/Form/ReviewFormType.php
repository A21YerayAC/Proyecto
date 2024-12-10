<?php
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Review;
use Symfony\Component\Validator\Constraints\File;

class ReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'attr' => ['placeholder' => 'Escribe el título aquí']
            ])
            ->add('contenido', TextareaType::class, [
                'label' => 'Contenido',
                'attr' => ['placeholder' => 'Escribe tu reseña']
            ])
            ->add('valoracion', ChoiceType::class, [
                'label' => 'Calificación',
                'choices' => [
                    '1 Estrella' => 1,
                    '2 Estrellas' => 2,
                    '3 Estrellas' => 3,
                    '4 Estrellas' => 4,
                    '5 Estrellas' => 5,
                ],
                'placeholder' => 'Selecciona una calificación'
            ])
            ->add('photos', FileType::class, [
                'label' => 'Imágenes',
                'mapped' => false,  // No se mapea al campo 'imagenes' de la entidad
                'multiple' => true,  // Permitir la selección de múltiples archivos
                'required' => false,
                'attr' => ['class' => 'form-control-file'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
