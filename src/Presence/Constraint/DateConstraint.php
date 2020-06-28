<?php

namespace AcMarche\Mercredi\Presence\Constraint;

use AcMarche\Mercredi\Entity\Jour;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Twig\Environment;

class DateConstraint
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(FlashBagInterface $flashBag, Environment $environment)
    {
        $this->flashBag = $flashBag;
        $this->environment = $environment;
    }

    /**
     * Verifie si le jour où on reserve,
     * la date de presence choisie n'est pas plus tard
     * que la veille a 12h00
     * Et pour les jours journees pedagogiques c'est une semaine.
     *
     * @param \DateTime $datePresence
     * @param null      $today
     *
     * @throws \Exception
     */
    public function check(\DateTimeInterface $datePresence, $today = null): bool
    {
        if (!$today) {
            $today = new \DateTime();
        }

        $cloneToday = clone $today;
        $todayPlusUneSemaine = $cloneToday->modify('+1 week');

        /*
         * Si journee pedagogique
         */
        if (3 != $datePresence->format('N')) {
            if ($todayPlusUneSemaine->format('Y-m-d') > $datePresence->format('Y-m-d')) {
                return false;
            }

            return true;
        }

        /*
         * La date de la presence est plus vieille que aujourd'hui
         */
        if ($today->format('Y-m-d') > $datePresence->format('Y-m-d')) {
            return false;
        }

        /*
         * si jour de garde egale aujourd'hui
         * trop tard
         */
        if ($today->format('Y-m-d') == $datePresence->format('Y-m-d')) {
            return false;
        }

        /*
         * Si on est un mardi la veille !
         * alors il faut qu'on soit max mardi 12h00
         * si on reserve un mardi 6 pour un admin 7
         */
        if (2 == $today->format('N')) {
            $lendemain = clone $today;
            $lendemain = $lendemain->modify('+1 day');
            //la veille ?
            if ($lendemain->format('d-m-Y') == $datePresence->format('d-m-Y')) {
                //si après 10h
                $heure = (int) $today->format('G');
                $minute = (int) $today->format('i');
                if ($heure > 10) {
                    return false;
                }
                if (10 == $heure) {
                    //si après 10h02
                    if ($minute > 02) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function addFlashError(Jour $jour)
    {
        $content = $this->environment->render('@AcMarcheMercrediParent/presence/_error_delais.txt.twig', ['jour' => $jour]);
        $this->flashBag->add('danger', $content);
    }
}
