<?php
namespace App\Form;
 
use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
 
class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('content', TextareaType::class)
            ->add('date', DateTimeType::class, [
                'widget' => 'choice',
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour', 'hour' => 'Heure', 'minute' => 'Minute', 
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Charger une photo',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'maxSizeMessage' => 'Image trop volumineuse.',
                        'mimeTypesMessage' => 'Merci de choisir une image au format JPG, PNG ou GIF.',
                    ])
                ],
            ])
            ->add('address', TextType::class)
            ->add('latitude', TextType::class, [
                'required' => false,
            ])
            ->add('longitude', TextType::class, [
                'required' => false,
            ])
        ;
    }
 
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Event::class,
        ));
    }
}