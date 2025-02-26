<?php
namespace App\Form;
use Doctrine\ORM\EntityRepository;

use App\Entity\Consultation;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPatient')
            ->add('dateConsultation', null, [
                'widget' => 'single_text',
            ])
            ->add('diagnostic')
            ->add('user', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom', // Display only the name of the patient
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                              ->where('u.role = :role') // Assuming role is the attribute for user type
                              ->setParameter('role', 'patient'); // Filter only patients
                },
                'placeholder' => 'Select Patient',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
        ]);
    }
}