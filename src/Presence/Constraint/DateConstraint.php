<?php

namespace AcMarche\Mercredi\Presence\Constraint;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Twig\Environment;

class DateConstraint implements PresenceConstraintInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var PresenceUtils
     */
    private $presenceUtils;

    public function __construct(
        FlashBagInterface $flashBag,
        Environment $environment,
        PresenceUtils $presenceUtils
    ) {
        $this->flashBag = $flashBag;
        $this->environment = $environment;
        $this->presenceUtils = $presenceUtils;
    }

    /**
     * Verifie si le jour où on reserve,
     * la date de presence choisie n'est pas plus tard
     * que la veille a 12h00
     * Et pour les jours journees pedagogiques c'est une semaine.
     *
     * @param Jour $jour
     *
     * @return bool
     */
    public function check(Jour $jour): bool
    {
        $datePresence = $jour->getDateJour();

        $deadLinePedagogique = $this->presenceUtils->getDeadLineDatePedagogique();
        $deadLinePresence = $this->presenceUtils->getDeadLineDatePresence();

        /**
         * Si journee pedagogique
         */
        if ($jour->isPedagogique()) {
            if ($deadLinePedagogique->format('Y-m-d') > $datePresence->format('Y-m-d')) {
                return false;
            }

            return true;
        }

        /**
         * Pas pédagogique
         */
        if ($deadLinePresence->format('Y-m-d') > $datePresence->format('Y-m-d')) {
            return false;
        }

        return true;
    }

    public function addFlashError(Jour $jour)
    {
        $content = $this->environment->render(
            '@AcMarcheMercrediParent/presence/_error_delais.txt.twig',
            ['jour' => $jour]
        );
        $this->flashBag->add('danger', $content);
    }
}
