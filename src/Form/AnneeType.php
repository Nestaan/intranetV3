<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Form/AnneeType.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 11/10/2020 15:17

namespace App\Form;

use App\Entity\Annee;
use App\Entity\Constantes;
use App\Entity\Diplome;
use App\Form\Type\YesNoType;
use App\Repository\DiplomeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AnneeType
 * @package App\Form
 */
class AnneeType extends AbstractType
{
    protected $departement;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->departement = $options['departement'];

        $builder
            ->add('libelle', TextType::class, [
                'label' => 'label.libelle'
            ])
            ->add('libelle_long', TextType::class, [
                'label' => 'label.libelle_long'
            ])
            ->add('diplome', EntityType::class, [
                'class'         => Diplome::class,
                'required'      => true,
                'choice_label'  => 'libelle',
                'expanded'      => true,
                'query_builder' => function(DiplomeRepository $diplomeRepository) {
                    return $diplomeRepository->findByDepartementBuilder($this->departement);
                },
                'label'         => 'label.diplome'
            ])
            ->add('codeEtape', TextType::class, [
                'label' => 'label.code_etape'
            ])
            ->add('codeVersion', TextType::class, [
                'label' => 'label.code_version'
            ])
            ->add('codeDepartement', TextType::class, [
                'label' => 'label.code_departement'
            ])
            ->add('ordre', TextType::class, [
                'label' => 'label.ordre'
            ])
            ->add('couleur', ChoiceType::class, [
                'label'    => 'label.couleur',
                'required' => true,
                'choices'  => Constantes::COULEURS
            ])
            ->add('optAlternance', YesNoType::class, [
                'label' => 'label.opt_alternance'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => Annee::class,
            'translation_domain' => 'form',
            'departement'          => null
        ]);
    }
}
