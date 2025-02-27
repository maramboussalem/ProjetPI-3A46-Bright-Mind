<?php

namespace App\Form;

use App\Entity\ServiceMed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\File;
use App\Form\DisponibiliteType;

class ServiceMedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomService')
            ->add('descriptionMed')
            ->add('imageM', FileType::class, [
                'label'       => 'Image du service',
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new File([
                        'mimeTypes'         => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage'  => 'Veuillez télécharger une image au format JPG, PNG ou GIF.',
                        'maxSize'           => '2M',
                        'maxSizeMessage'    => 'L\'image ne doit pas dépasser 2 Mo.',
                    ]),
                ],
            ])
            ->add('disponibilites', CollectionType::class, [
                'entry_type'   => DisponibiliteType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false, // Pour que la méthode addDisponibilite() soit appelée
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceMed::class,
        ]);
    }
}

