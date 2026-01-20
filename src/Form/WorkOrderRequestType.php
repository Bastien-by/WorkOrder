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

            ->add('intervention_requester', TextType::class, [
                'label' => 'Demandeur',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Renseignez le demandeur',
                    'class' => 'form-control',
                ],
            ])
            ->add('intervention_request_date', DateType::class,[
                'label' => "Date",
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['style' => 'width: 150px;'],
            ])
            ->add('sector', ChoiceType::class,[
                'choices' => [
                    'BATIMENT' => 'BATIMENT',
                    'MOYEN GENERAUX' => 'MOYEN_GENERAUX',
                    'EXTERIEUR' => 'EXTERIEUR',
                    'SOUFFLAGE' => 'SOUFFLAGE',
                    'BLOCK 1' => 'BLOCK_1',
                    'BLOCK 2' => 'BLOCK_2',
                    'BLOCK 3' => 'BLOCK_3',
                    'BLOCK 4' => 'BLOCK_4',
                    'SSL1' => 'SSL1',
                    'MACHINE TEST' => 'MACHINE_TEST',
                    'ASSEMBLAGE' => 'ASSEMBLAGE',
                    'HPV1' => 'HPV1',
                ],
                'placeholder' => 'Sélectionnez un secteur',
                'label' => "Secteur",
                'required' => true,
                'attr' => ['style' => 'width: 220px;'],
            ])
            ->add('machine_name', ChoiceType::class,[
                'choices' => [
                    'BMM 242' => 'BMM_242',
                    'BMM H20' => 'BMM_H20',
                    'CENTRALE MATIERE PIOVAN' => 'CENTRALE_MATIERE_PIOVAN',
                    'MILLING HPV1' => 'MILLING_HPV1',
                    'MILLING SSL1' => 'MILLING_SSL1',
                    'MILLING HPV2' => 'MILLING_HPV2',
                    'ANNEALING HPV1' => 'ANNEALING_HPV1',
                    'ANNEALING HPV2' => 'ANNEALING_HPV2',
                    'FOURS HPV2' => 'FOURS_HPV2',
                    'FOURS HPV1' => 'FOURS_HPV1',
                    'FWM HPV2' => 'FWM_HPV2',
                    'FWM HPV1' => 'FWM_HPV1',
                    'LOADING STATION SSL1' => 'LOADING_STATION_SSL1',
                    'BUFFER HPV2' => 'BUFFER_HPV2',
                    'PROOFTEST HPV2' => 'PROOFTEST_HPV2',
                    'PROOFTEST HPV1' => 'PROOFTEST_HPV1',
                    'GAZ LEAK TEST MAXIMATOR' => 'GAZ_LEAK_TEST_MAXIMATOR',
                    'GAZ LEAK TEST VES' => 'GAZ_LEAK_TEST_VES',
                    'CYCLING TEST FIVES' => 'CYCLING_TEST_FIVES',
                    'BURST TEST' => 'BURST_TEST',
                    'PICKING STATION DALMEC' => 'PICKING_STATION_DALMEC',
                    'ASSEMBLY VALVE HPV2' => 'ASSEMBLY_VALVE_HPV2',
                    'ASSEMBLY VALVE SSL1' => 'ASSEMBLY_VALVE_SSL1',
                    'LASER MARKING HPV2' => 'LASER_MARKING_HPV2',
                    'DAM STATION' => 'DAM_STATION',
                    'ASSEMBLY STATION FERROVIAIRE' => 'ASSEMBLY_STATION_FERROVIAIRE',
                    'DEFUELING STATION HPV1' => 'DEFUELING_STATION_HPV1',
                    'IMPACT PROTECTOR HPV1' => 'IMPACT_PROTECTOR_HPV1',
                    'ASSEMBLY LINE HKO' => 'ASSEMBLY_LINE_HKO',
                    'LEAK TEST HKO FIVES' => 'LEAK_TEST_HKO_FIVES',
                    'ROBOT TRACK HPV2' => 'ROBOT_TRACK_HPV2',
                    'CREEL SSL1' => 'CREEL_SSL1',
                    'CREEL HPV2' => 'CREEL_HPV2',
                    'COMPRESSEUR' => 'COMPRESSEUR',
                    'SECHEUR' => 'SECHEUR',
                    'STATION RECUPERATION EAU GRISE' => 'STATION_RECUPERATION_EAU_GRISE',
                    'GROUPE ELECTROGENE' => 'GROUPE_ELECTROGENE',
                    'POSTE AIR SPRINKLER' => 'POSTE_AIR_SPRINKLER',
                    'SPRINKLER' => 'SPRINKLER',
                    'RIA' => 'RIA',
                    'MOTEUR DIESEL SPRINKLER' => 'MOTEUR_DIESEL_SPRINKLER',
                    'CUVE DIESEL SPRINKLER' => 'CUVE_DIESEL_SPRINKLER',
                    'POTEAU INCENDIE STATION' => 'POTEAU_INCENDIE_STATION',
                    'POTEAU INCENDIE MOTEUR DIESEL' => 'POTEAU_INCENDIE_MOTEUR_DIESEL',
                    'SSI' => 'SSI',
                    'EXTINCTION GAZ' => 'EXTINCTION_GAZ',
                    'TGBT' => 'TGBT',
                    'CELLULE HT SCHENIDER' => 'CELLULE_HT_SCHENIDER',
                    'CLIMATISATION' => 'CLIMATISATION',
                    'ROOF TOP' => 'ROOF_TOP',
                    'AEROTHERME' => 'AEROTHERME',
                    'RESEAU EAU GLACE' => 'RESEAU_EAU_GLACE',
                    'RESEAU EAU FROIDE' => 'RESEAU_EAU_FROIDE',
                    'RESEAU AIR COMPRIME' => 'RESEAU_AIR_COMPRIME',
                    'RESEAU EAU GRISE' => 'RESEAU_EAU_GRISE',
                    'ADOUCISSEUR EAU GENERALE' => 'ADOUCISSEUR_EAU_GENERALE',
                    'DISCONNECTEUR' => 'DISCONNECTEUR',
                    'GROUPE FROID' => 'GROUPE_FROID',
                    'EQUIPEMENTS MANUTENTION' => 'EQUIPEMENTS_MANUTENTION',
                    'MOYEN DE LEVAGE' => 'MOYEN_DE_LEVAGE',
                    'PONTS ROULANTS' => 'PONTS_ROULANTS',
                    'PALAN ELECTRIQUE' => 'PALAN_ELECTRIQUE',
                    'MANIPULATEUR DALMEC HKO ASSEMBLY LINE' => 'MANIPULATEUR_DALMEC_HKO_ASSEMBLY_LINE',
                    'CENTRALE ETT' => 'CENTRALE_ETT',
                    'CENTRALE DAIKIN USINE' => 'CENTRALE_DAIKIN_USINE',
                    'CENTRALE DAIKIN DONUTS' => 'CENTRALE_DAIKIN_DONUTS',
                    'BALLON EAU CHAUDE USINE' => 'BALLON_EAU_CHAUDE_USINE',
                    'BALLON EAU CHAUDE DONUTS' => 'BALLON_EAU_CHAUDE_DONUTS',
                    'MACHINE A LAVER HPV2' => 'MACHINE_A_LAVER_HPV2',
                    'MACHINE A LAVER HPV1' => 'MACHINE_A_LAVER_HPV1',
                    'DMM HPV2' => 'DMM_HPV2',
                    'DMM SSL1' => 'DMM_SSL1',
                    'RCFS HPV2' => 'RCFS_HPV2',
                    'RCFS SSL1' => 'RCFS_SSL1',
                    'FIRE COATING HPV2' => 'FIRE_COATING_HPV2',
                    'FIRE COATING SSL1' => 'FIRE_COATING_SSL1',
                    'EXTRACTEUR VAPEUR' => 'EXTRACTEUR_VAPEUR',
                    'EXTRACTEUR FUMEE' => 'EXTRACTEUR_FUMEE',
                    'THERMOREGULATEUR BMM' => 'THERMOREGULATEUR_BMM',
                    'AUTRES' => 'AUTRES',
                ],


                'placeholder' => 'Sélectionnez une machine',
                'label' => "Machine",
                'required' => true,
                'attr' => ['style' => 'width: 220px;'],
            ])
            ->add('technical_details', TextareaType::class,[
                'label' => 'Description technique',
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
