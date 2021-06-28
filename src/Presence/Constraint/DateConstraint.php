<?php

namespace AcMarche\Mercredi\Presence\Constraint;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Twig\Environment;

final class DateConstraint implements PresenceConstraintInterface
{
    /**
     * @var string
     */
    private const FORMAT = 'Y-m-d';
    private FlashBagInterface $flashBag;
    private Environment $environment;
    private PresenceUtils $presenceUtils;

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
     */
    public function check(Jour $jour): bool
    {
        $datePresence = $jour->getDateJour();

        $deadLinePedagogique = $this->presenceUtils->getDeadLineDatePedagogique();
        $deadLinePresence = $this->presenceUtils->getDeadLineDatePresence();

        /*
         * Si journee pedagogique
         */
        if ($jour->isPedagogique()) {
            return $deadLinePedagogique->format(self::FORMAT) <= $datePresence->format(self::FORMAT);
        }
        /*
         * Pas pédagogique
         */
        return $deadLinePresence->format(self::FORMAT) <= $datePresence->format(self::FORMAT);
    }

    public function addFlashError(Jour $jour): void
    {
        $content = $this->environment->render(
            '@AcMarcheMercrediParent/presence/_error_delais.txt.twig',
            ['jour' => $jour]
        );
        $this->flashBag->add('danger', $content);
    }
}
