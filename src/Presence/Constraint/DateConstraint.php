<?php

namespace AcMarche\Mercredi\Presence\Constraint;

use AcMarche\Mercredi\Contrat\Presence\PresenceConstraintInterface;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class DateConstraint implements PresenceConstraintInterface
{
    /**
     * @var string
     */
    private const FORMAT = 'Y-m-d';
    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack,
        private Environment $environment,
        private PresenceUtils $presenceUtils
    ) {
        $this->requestStack = $requestStack;
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

        /**
         * Si journee pedagogique
         */
        if ($jour->isPedagogique()) {
            return $deadLinePedagogique->format(self::FORMAT) <= $datePresence->format(self::FORMAT);
        }

        /**
         * Pas pédagogique
         */
        return $deadLinePresence->format(self::FORMAT) <= $datePresence->format(self::FORMAT);
    }

    public function addFlashError(Jour $jour): void
    {
        $content = $this->environment->render(
            '@AcMarcheMercrediParent/presence/_error_delais.txt.twig',
            [
                'jour' => $jour,
            ]
        );
        $flashBag = $this->requestStack->getSession()?->getFlashBag();
        $flashBag->add('danger', $content);
    }
}
