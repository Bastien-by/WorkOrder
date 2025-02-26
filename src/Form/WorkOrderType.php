<?php

namespace App\Form;

use App\Entity\WorkOrder;
use Doctrine\DBAL\Types\BooleanType;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaintenanceType extends AbstractType
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
                'attr' => ['class' => 'form-control'],
                'format' => 'dd-MM-yyyy',
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
            ->add('downtime_start_time', DateType::class,[
                'label' => 'Heure début panne',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'format' => 'dd-MM-yyyy',
            ])
            ->add('downtime_end_time', DateType::class,[
                'label' => 'Heure fin panne',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'format' => 'dd-MM-yyyy',
            ])
            ->add('intervention_start_time', DateType::class,[
                'label' => 'Heure début intervention',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'format' => 'dd-MM-yyyy',
            ])
            ->add('intervention_end_time', DateType::class,[
                'label' => 'Heure fin panne',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'format' => 'dd-MM-yyyy',
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
                'attr' => ['class' => 'form-control'],
                'format' => 'dd-MM-yyyy',
            ])
            ->add('technical_details', TextType::class,[
                'label' => 'Descriprition technique',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('piece_issued', BooleanType::class,[
                'label' => "Pièce sortie ?",
                'required' => true,
                'attr' => ['class' => 'form-control'],
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
            ->add('additional_details', TextType::class,[
                'label' => 'Description supplémentaire',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrder::class,
        ]);
    }
}
