<?php

namespace App\Form;

use App\Entity\WorkOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('technician_name', TextType::class, [
                'label' => 'Nom(s) Intervenant',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('maintenance_type', TextType::class, [
                'label' => 'Type de Maintenance',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('intervention_date', DateType::class, [
                'label' => "Date de l'intervention",
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('machine_name', TextType::class, [
                'label' => "Nom de la machine",
                'required' => true,
                'attr' => ['class' => 'format-control'],
            ])
            ->add('technical_position', TextType::class, [
                'label' => 'Poste Technique',
                'required' => false,
                'attr' => ['class' => 'format-control'],
            ])
            ->add('downtime_start_time', TimeType::class,[
                'label' => 'Heure début panne',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('downtime_end_time', TimeType::class,[
                'label' => 'Heure fin panne',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('intervention_start_time', TimeType::class,[
                'label' => 'Heure début intervention',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('intervention_end_time', TimeType::class,[
                'label' => 'Heure fin panne',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('field_intervention', TextType::class,[
                'label' => "Domaine d'intervention",
                'required' => true,
                'attr' => ['class' => 'format-control'],
            ])
            ->add('intervention_requester', TextType::class,[
                'label' => "Demandeur de l'intervention",
                'required' => true,
                'attr' => ['class' => 'format-control'],
            ])
            ->add('intervention_request_date', DateType::class,[
                'label' => "Date de l'intervention",
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('technical_details', TextareaType::class,[
                'label' => 'Descriprition technique',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('piece_issued', CheckboxType::class,[
                'label' => "Pièce sortie ?",
                'required' => true,
            ])
            ->add('piece_type', TextType::class,[
                'label' => "Type de pièce",
                'required' => true,
            ])
            ->add('piece_brand', TextType::class,[
                'label' => 'Marque/Fabricant',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('sap_reference', TextType::class,[
                'label' => 'Réference SAP',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('quantity', IntegerType::class,[
                'label' => 'Quantité',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('brand', TextType::class,[
                'label' => 'Marque',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', TextType::class,[
                'label' => 'Type',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('size', TextType::class,[
                'label' => 'Dimensions',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('manufacturer_reference', TextType::class,[
                'label' => 'Réference fabricant',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('created_piece_quantity', IntegerType::class,[
                'label' => 'Quantité',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('additional_details', TextareaType::class,[
                'label' => 'Description supplémentaire',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkOrder::class,
            'csrf_protection' => true,  // Ensure CSRF protection is enabled
            'csrf_field_name' => '_token',  // Default field name for CSRF
            'csrf_token_id'   => 'work_order',  // Unique ID for this form
        ]);
    }
}
