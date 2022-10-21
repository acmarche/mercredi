<?php

namespace AcMarche\Mercredi\Plaine\Factory;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingMarche;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PlainePdfFactory
{
    use PdfDownloaderTrait;

    public function __construct(
        private GroupingInterface $grouping,
        private PresenceRepository $presenceRepository,
        private PlainePresenceRepository $plainePresenceRepository,
        private ParameterBagInterface $parameterBag,
        private Environment $environment,
        private ScolaireUtils $scolaireUtils,
        private GroupeScolaireRepository $groupeScolaireRepository
    ) {
    }

    public function generate(Plaine $plaine): Response
    {
        $images = $this->getImagesBase64();
        $dates = $plaine->getJours();
        $firstDay = $plaine->getFirstDay()->getDateJour();

        $enfants = $this->plainePresenceRepository->findEnfantsByPlaine($plaine);
        $this->grouping->setEnfantsForGroupesScolaire($plaine, $enfants);

        $presences = $this->plainePresenceRepository->findByPlaine($plaine);
        /**
         * par enfant je dois avoir quel tuteur en garde
         * jours presents.
         */
        $data = [];
        $dataEnfants = [];
        $stats = [];
        $groupesScolaire = $this->groupeScolaireRepository->findAll();
        $groupesScolaire[] = $this->scolaireUtils->createGroupeScolaireNonClasse();

        foreach ($groupesScolaire as $groupeScolaire) {
            foreach ($dates as $date) {
                $stats[$groupeScolaire->getId()][$date->getId()]['total'] = 0;
                $stats[$groupeScolaire->getId()][$date->getId()]['moins6'] = 0;
            }
        }

        foreach ($presences as $presence) {
            $enfant = $presence->getEnfant();
            $tuteur = $presence->getTuteur();
            $jour = $presence->getJour();
            $enfantId = $enfant->getId();
            $age = $enfant->getAge($firstDay, true);
            $groupeScolaireEnfant = $this->grouping->findGroupeScolaire($enfant);
            ++$stats[$groupeScolaireEnfant->getId()][$jour->getId()]['total'];
            if ($age < 6) {
                ++$stats[$groupeScolaireEnfant->getId()][$jour->getId()]['moins6'];
            }
            $dataEnfants[$enfantId]['enfant'] = $enfant;
            $dataEnfants[$enfantId]['tuteur'] = $tuteur;
            $dataEnfants[$enfantId]['jours'][] = $jour;
            $data[$groupeScolaireEnfant->getId()]['groupe'] = $groupeScolaireEnfant;
            $data[$groupeScolaireEnfant->getId()]['enfants'] = $dataEnfants;
            $data[$groupeScolaireEnfant->getId()]['stats'] = $stats;
        }

        $html = $this->environment->render(
            '@AcMarcheMercrediAdmin/plaine/pdf/plaine_pdf.html.twig',
            [
                'plaine' => $plaine,
                'firstDay' => $firstDay,
                'datesPlaine' => $dates,
                'datas' => $data,
                'stats' => $stats,
                'images' => $images,
            ]
        );

        //return new Response($html);
        $name = $plaine->getSlug();

        $this->pdf->setOption('footer-right', '[page]/[toPage]');
        if (\count($dates) > 6) {
            $this->pdf->setOption('orientation', 'landscape');
        }

        return $this->downloadPdf($html, $name.'.pdf');
    }

    private function getImagesBase64(): array
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
