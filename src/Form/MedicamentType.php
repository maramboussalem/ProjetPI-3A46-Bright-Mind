<?php

namespace App\Form;

use App\Entity\Fournisseur;
use App\Entity\Medicament;
use DateTime;
 use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class MedicamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NomMedicament')
            ->add('Description')
            ->add('Quantite')
            ->add('Prix')
            ->add('Type', ChoiceType::class, [
                'choices' => [
                    'Analgésique' => 'analgesique',
                    'Antibiotique' => 'antibiotique',
                    'Antiviral' => 'antiviral',
                    'Antifongique' => 'antifongique',
                    'Antihypertenseur' => 'antihypertenseur',
                    'Antidiabétique' => 'antidiabetique',
                    'Antidépresseur' => 'antidepresseur',
                    'Anti-inflammatoire' => 'antiinflammatoire',
                    'Anticoagulant' => 'anticoagulant',
                ],
                'placeholder' => 'Choisissez un type de médicament',
            ])           
             ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'choice_label' => 'NomFournisseur',
             ])

             ->add('expireat', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control'],
                'data' => new DateTime()

            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false, // Set the image field as optional
                'attr' => ['class' => 'form-control'],

                'mapped' => false, // Important: tells Symfony not to try to map this field to an entity property
            ])   
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medicament::class,
        ]);
    }
}
