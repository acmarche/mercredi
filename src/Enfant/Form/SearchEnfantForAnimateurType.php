<?php


namespace AcMarche\Mercredi\Enfant\Form;


use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchEnfantForAnimateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $animateur = $options['animateur'];
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom'],
                ]
            )
            ->add(
                'jour',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'placeholder' => 'Jour d\'accueil',
                    'query_builder' => function (JourRepository $jourRepository) use ($animateur) {
                        return $jourRepository->getQbForListingAnimateur($animateur);
                    },
                    //todo display name day
                    'group_by' => function ($jour, $key, $id) {
                        return $jour->getDateJour()->format('Y');
                    },
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            array(
                'animateur' => null,
            )
        )
            ->setAllowedTypes('animateur', Animateur::class)
            ->setRequired('animateur');
    }
}
