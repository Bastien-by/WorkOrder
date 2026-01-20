<?php

// src/Form/WorkOrderClosureType.php
namespace App\Form;

use App\Entity\WorkOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class WorkOrderClosureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('downtimeStartTime', DateTimeType::class, [
                'label' => 'Début de la panne',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('downtimeEndTime', DateTimeType::class, [
                'label' => 'Fin de la panne',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('interventionStartTime', DateTimeType::class, [
                'label' => 'Début de l\'intervention',
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('interventionEndTime', DateTimeType::class, [
                'label' => 'Fin de l\'intervention',
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('fieldIntervention', ChoiceType::class, [
                'choices' => [
                    'Automatisme' => 'Automatisme',
                    'Électricité' => 'Électricité',
                    'Mécanique' => 'Mécanique',
                    'Pneumatique' => 'Pneumatique',
                    'Hydraulique' => 'Hydraulique',
                    'Eau' => 'Eau',
                    'Détections Incendie' => 'Détections Incendie',
                    'Informatique' => 'Informatique',
                    'Extinction Gaz' => 'Extinction Gaz',
                    'Gamme de Maintenance' => 'Gamme de Maintenance',
                    'HSE' => 'HSE',
                ],
                'placeholder' => 'Sélectionnez un Domaine',
                'label' => 'Domaine d\'intervention',
                'required' => true,
                'attr' => ['style' => 'width: 210px; display: inline-block;'],
            ])
            ->add('technicianName', ChoiceType::class, [
                'choices' => [
                    ' IKHENTANE Hamid ' => ' IKHENTANE Hamid ',
                    ' JECSI Richard ' => ' JECSI Richard ',
                    ' DA COSTA Joao ' => 'DA COSTA Joao ',
                    'PARMENTIER Philippe ' => 'PARMENTIER Philippe ',
                    'HERVIEUX Baptiste ' => ' HERVIEUX Baptiste ',
                    'NEAU-CADOT Samuel ' => ' NEAU-CADOT Samuel ',
                    'BARRE Régis' => 'BARRE Régis'
                ],
                'placeholder' => 'Sélectionnez un technicien',
                'label' => 'Technicien',
                'required' => true,
                'attr' => ['style' => 'width: 250px; display: inline-block;'],
            ])
            ->add('maintenanceType', ChoiceType::class, [
                'choices' => [
                    'Corrective' => 'Corrective',
                    'Préventive' => 'Préventive',
                    'Amélioration Continue' => 'Amélioration Continue',
                    'Réglementaire' => 'Réglementaire'
                ],
                'label' => 'Type de maintenance',
                'required' => true,
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('interventionDescription', TextareaType::class, [
                'label' => 'Description Intervention Technique',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('descriptionPhoto', FileType::class, [
                'label' => 'Photo descriptive',
                'required' => false,
                'mapped' => false, // Important ! Le fichier n'est pas mappé directement à l'entité
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, GIF)',
                    ])
                ],
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'form-control'
                ],
                'help' => 'Photo explicative de l\'intervention (max 5Mo)'
            ])
            ->add('piece_issued', ChoiceType::class, [
                'label' => 'Sortie pièce ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,  // Affiche des radio buttons
                'multiple' => false, // Un seul choix possible
                'required' => true,
                'placeholder' => false, // Pas de choix vide
                'attr' => ['class' => 'form-check'],
                'label_attr' => ['class' => 'form-label fw-medium'],
            ])
            ->add('ifPieceNotIssued', ChoiceType::class, [
        'choices' => [
            'Pièces non crées' => 'Pièces non crées',
            'Plus de stock à recommander' => 'Plus de stock à recommander',
            'Pas nécessaire' => 'Pas nécessaire'
        ],
        'label' => 'Raison pièces non crées',
        'required' => true,
        'attr' => ['style' => 'width: 200px; display: inline-block;'],
        ]);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkOrder::class,
            'csrf_protection' => false,  // Ensure CSRF protection is enabled
            'csrf_field_name' => '_token',  // Default field name for CSRF
            'csrf_token_id'   => 'work_order',  // Unique ID for this form
            'allow_extra_fields' => true,
        ]);
    }
}
