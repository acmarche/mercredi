<?php

namespace AcMarche\Mercredi\Plaine\Factory;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PlainePdfFactory
{
    use PdfDownloaderTrait;

    private GroupingInterface $grouping;
    private PresenceRepository $presenceRepository;
    private ParameterBagInterface $parameterBag;
    private Environment $environment;

    public function __construct(
        GroupingInterface $grouping,
        PresenceRepository $presenceRepository,
        ParameterBagInterface $parameterBag,
        Environment $environment
    ) {
        $this->grouping = $grouping;
        $this->presenceRepository = $presenceRepository;
        $this->parameterBag = $parameterBag;
        $this->environment = $environment;
    }

    public function generate(Plaine $plaine): Response
    {
        $images = $this->getImagesBase64();
        $dates = $plaine->getJours();
        $firstDay = $plaine->getFirstDay()->getDateJour();

        $presences = $this->presenceRepository->findByPlaine($plaine);
        /**
         * par enfant je dois avoir quel tuteur en garde
         * jours presents
         */
        $data = [];
        $dataEnfants = [];
        $groupeForce = $plaine->getPlaineGroupes()[0]->getGroupeScolaire();
        $groupeForce->setNom('Non classé');
        $stats = [];
        foreach ($plaine->getPlaineGroupes() as $plaineGroupe) {
            foreach ($dates as $date) {
                $stats[$plaineGroupe->getGroupeScolaire()->getId()][$date->getId()]['total'] = 0;
                $stats[$plaineGroupe->getGroupeScolaire()->getId()][$date->getId()]['moins6'] = 0;
            }
        }
        foreach ($presences as $presence) {
            $enfant = $presence->getEnfant();
            $tuteur = $presence->getTuteur();
            $jour = $presence->getJour();
            $enfantId = $enfant->getId();
            $age = $enfant->getAge($firstDay, true);
            $groupeScolaire = $this->grouping->findGroupeScolaireByAge($age);
            if (!$groupeScolaire) {
                $groupeScolaire = $groupeForce;
            }
            $stats[$groupeScolaire->getId()][$jour->getId()]['total'] += 1;
            if ($age < 6) {
                $stats[$groupeScolaire->getId()][$jour->getId()]['moins6'] += 1;
            }
            $dataEnfants[$enfantId]['enfant'] = $enfant;
            $dataEnfants[$enfantId]['tuteur'] = $tuteur;
            $dataEnfants[$enfantId]['jours'][] = $jour;
            $data[$groupeScolaire->getId()]['groupe'] = $groupeScolaire;
            $data[$groupeScolaire->getId()]['enfants'] = $dataEnfants;
            $data[$groupeScolaire->getId()]['stats'] = $stats;
        }

        $html = $this->environment->render(
            '@AcMarcheMercrediAdmin/plaine/pdf/plaine_pdf.html.twig',
            [
                'plaine' => $plaine,
                'firstDay' => $firstDay,
                'dates' => $dates,
                'datas' => $data,
                'stats' => $stats,
                'images' => $images,
            ]
        );

        //  return new Response($html);
        $name = $plaine->getSlug();

        $this->pdf->setOption('footer-right', '[page]/[toPage]');
        if (count($dates) > 6) {
            $this->pdf->setOption('orientation', 'landscape');
        }

        return $this->downloadPdf($html, $name.'.pdf');
    }

    private function getImagesBase64()
    {
        $root = $this->parameterBag->get('kernel.project_dir').'/public/bundles/acmarchemercredi/images/';
        $ok = $root.'check_ok.jpg';
        $ko = $root.'check_ko.jpg';
        $data = [];
        $data['ok'] = base64_encode(file_get_contents($ok));
        $data['ko'] = base64_encode(file_get_contents($ko));

        return $data;
    }
}
