<?php

namespace App\Form;

use App\Entity\Diagnostic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class DiagnosticType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        $patientId = $options['patient_id']; // Get patient ID from options

        $builder
            ->add('name')
            ->add('description')
            ->add('dateDiagnostic', null, [
                'widget' => 'single_text',
                'data' => new \DateTime(), // Current date and time as default
            ])
            ->add('patientID', null, [
                'data' => $patientId, // Pre-fill with patient ID from URL
                'disabled' => true,   // Make it read-only
            ])
            ->add('medecinID', null, [
                'data' => $currentUser ? $currentUser->getId() : null, // Pre-fill with current user's ID
                'disabled' => true,   // Make it read-only
            ]);

        // Ensure patientID and medecinID are set before submission
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($currentUser, $patientId) {
            $data = $event->getData();
            $diagnostic = $event->getForm()->getData();

            // Ensure patientID is set from options if provided
            if ($patientId !== null) {
                $diagnostic->setPatientID($patientId);
                $data['patientID'] = $patientId;
            } elseif (!$diagnostic->getPatientID()) {
                throw new \LogicException('Patient ID is required to create a diagnostic.');
            }

            // Ensure medecinID is set from current user
            if ($currentUser && $currentUser->getId()) {
                $diagnostic->setMedecinID($currentUser->getId());
                $data['medecinID'] = $currentUser->getId();
            } else {
                throw new \LogicException('A logged-in doctor is required to create a diagnostic.');
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Diagnostic::class,
            'attr' => ['novalidate' => 'novalidate'],
            'patient_id' => null, // Add patient_id as an option
        ]);

        $resolver->setAllowedTypes('patient_id', ['int', 'null']); // Define allowed types
    }
}