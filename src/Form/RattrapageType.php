<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Form/RattrapageType.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 08/08/2020 10:14

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\Personnel;
use App\Entity\Rattrapage;
use App\Repository\MatiereRepository;
use App\Repository\PersonnelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RattrapageType
 * @package App\Form
 */
class RattrapageType extends AbstractType
{
    private $semestre;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->semestre = $options['semestre'];
        $locale = $options['locale'];

        $builder
            ->add('dateEval', DateType::class, [
                'label'    => 'label.date_evaluation',
                'required' => false,
                'format'   => 'dd/MM/yyyy',
                'widget'   => 'single_text',
                'html5'    => false,
                'attr'     => ['data-provide' => 'datepicker', 'data-language' => $locale]
            ])
            ->add('heureEval', TimeType::class, ['label' => 'label.heure_evaluation', 'required' => false])
            ->add('duree', TextType::class, ['label' => 'label.duree_evaluation', 'required' => false])
            ->add('matiere', EntityType::class, array(
                'class'         => Matiere::class,
                'label'         => 'label.matiere',
                'choice_label'  => 'display',
                'query_builder' => function(MatiereRepository $matiereRepository) {
                    return $matiereRepository->findBySemestreBuilder($this->semestre);
                },
                'required'      => false,
                'expanded'      => false,
                'multiple'      => false,
                'attr' => ['class' => 'form-control selectpicker']

            ))
            ->add('personnel', EntityType::class, array(
                'class'         => Personnel::class,
                'label'         => 'label.personnel',
                'choice_label'  => 'displayPr',
                'query_builder' => function(PersonnelRepository $personnelRepository) {
                    return $personnelRepository->findBySemestreBuilder($this->semestre);
                },
                'required'      => false,
                'expanded'      => false,
                'multiple'      => false,
                'attr' => ['class' => 'form-control selectpicker']

            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rattrapage::class,
            'semestre'   => null,
            'locale'     => 'fr'
        ]);
    }
}
