<?php

namespace App\Form;

use App\Entity\WorkOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('intervention_requester', ChoiceType::class, [
                'choices' => array_combine(
                    $options['requesters'],
                    $options['requesters']
                ),
                'placeholder' => 'Sélectionnez un demandeur',
                'label' => "Demande",
                'required' => true,
                'attr' => ['style' => 'width: 225px;'],
            ])
            ->add('intervention_request_date', DateType::class,[
                'label' => "Date",
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['style' => 'width: 200px;'],
            ])->add('machine_name', ChoiceType::class,[
                'choices' => [
                    'BMM' => 'BMM',
                    'Proof Test' => 'Proof Test',
                ],
                'placeholder' => 'Sélectionnez une machine',
                'label' => "Machine",
                'required' => true,
                'attr' => ['style' => 'width: 200px;'],
            ])
            ->add('technical_details', TextareaType::class,[
                'label' => 'Descriprition technique',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkOrder::class,
            'csrf_protection' => false,  // Ensure CSRF protection is enabled
            'csrf_field_name' => '_token',  // Default field name for CSRF
            'csrf_token_id'   => 'work_order',  // Unique ID for this form
            'requesters' => [],
        ]);
    }
}
