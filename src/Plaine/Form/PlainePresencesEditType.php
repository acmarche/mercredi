<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Plaine\Dto\PlainePresencesDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class PlainePresencesEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                /**
                 * @var PlainePresencesDto $dto
                 */
                $dto = $event->getData();
                $jours = $dto->daysOfPlaine;

                $form->add(
                    'jours',
                    EntityType::class,
                    [
                        'class' => Jour::class,
                        'label' => 'Sélectionnez les dates',
                        'choices' => $jours,
                        'required' => false,
                        'multiple' => true,
                        'expanded' => true,
                        'constraints' => [new Count(['min' => 1])],
                    ]
                );
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => PlainePresencesDto::class,
            ]
        );
    }
}
