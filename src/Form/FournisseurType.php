<?php

namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NomFournisseur', TextType::class, [
                'label' => 'Nom du Fournisseur',
                "empty_data" =>"",
                'attr' => ['placeholder' => 'Entrez le nom du fournisseur'],

            ])
            ->add('Adresse', TextType::class, [
                'label' => 'Adresse',
                "empty_data" =>"",

                'attr' => ['placeholder' => 'Entrez l\'adresse'],
            ])
            ->add('Telephone', TelType::class, [
                'label' => 'Téléphone',
                "empty_data" =>"",

                'attr' => ['placeholder' => 'Entrez le numéro de téléphone'],
            ])
            ->add('Email', EmailType::class, [
                'label' => 'Email',
                "empty_data" =>"",

                'attr' => ['placeholder' => 'Entrez l\'adresse email'],
            ]);
    }

    /**
     * Méthode de validation pour vérifier que les champs sont différents
     *
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        $data = $context->getObject();

        // Si le nom du fournisseur est identique à l'adresse
        if ($data->getNomFournisseur() === $data->getAdresse()) {
            $context->buildViolation('Le nom du fournisseur et l\'adresse ne peuvent pas être identiques.')
                ->atPath('Adresse')  // L'erreur sera liée au champ "Adresse"
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fournisseur::class,
        ]);
    }
}
