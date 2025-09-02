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

class WorkOrderClosureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('downtimeStartTime', DateTimeType::class, [
                'label' => 'Début de la panne',
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['style' => 'width: 180px; display: inline-block;'],
            ])
            ->add('downtimeEndTime', DateTimeType::class, [
                'label' => 'Fin de la panne',
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['style' => 'width: 180px; display: inline-block;'],
            ])
            ->add('interventionStartTime', DateTimeType::class, [
                'label' => 'Début de l\'intervention',
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['style' => 'width: 180px; display: inline-block;'],
            ])
            ->add('interventionEndTime', DateTimeType::class, [
                'label' => 'Fin de l\'intervention',
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['style' => 'width: 180px; display: inline-block;'],
            ])
            ->add('fieldIntervention', TextType::class, [
                'label' => 'Domaine d\'intervention',
                'required' => true,
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('technicalPosition', TextType::class, [
                'label' => 'Poste technique',
                'required' => true,
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('technicianName', TextType::class, [
                'label' => 'Nom du/des technicien(s)',
                'required' => true,
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('maintenanceType', TextType::class, [
                'label' => 'Type de maintenance',
                'required' => true,
                'attr' => ['style' => 'width: 200px; display: inline-block;'],
            ])
            ->add('additionalDetails', TextareaType::class, [
                'label' => 'Commentaire / Observations',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('ElecPlan', ChoiceType::class, [
                'label' => "Est-ce qu'il y a un plan éléctrique ?",
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,  // boutons radio
                'multiple' => false, // une seule sélection
                'required' => true,
            ])
            ->add('changedElecPlan', ChoiceType::class, [
                'label' => 'Plan électrique modifié ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,  // boutons radio
                'multiple' => false, // une seule sélection
                'required' => false,
            ])
            ->add('elecPlanPicture', FileType::class, [
                'label' => 'Plan électrique (image)',
                'mapped' => false,
                'required' => false,
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
