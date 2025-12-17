<?php

namespace AcMarche\Mercredi\Plaine\Factory;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Plaine\Dto\PdfPlaine;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PlainePdfFactory
{
    use PdfDownloaderTrait;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $project_dir,
        private GroupingInterface $grouping,
        private PresenceRepository $presenceRepository,
        private PlainePresenceRepository $plainePresenceRepository,
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
        $pdfPlaine = new PdfPlaine($plaine, $dates->toArray(), $firstDay);

        $presences = $this->plainePresenceRepository->findByPlaine($plaine);
        /**
         * par enfant je dois avoir quel tuteur en garde
         * jours presents.
         */
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
            $age = $enfant->getAge($firstDay, true);
            $groupeScolaireEnfant = $this->grouping->findGroupeScolaire($enfant);
            if (!$groupeScolaireEnfant) {
                continue;
            }
            $groupeScolaireId = $groupeScolaireEnfant->getId();
            ++$stats[$groupeScolaireId][$jour->getId()]['total'];
            if ($age < 6) {
                ++$stats[$groupeScolaireId][$jour->getId()]['moins6'];
            }
            $enfant->tuteur = $tuteur;
            $pdfPlaine->addEnfant($groupeScolaireEnfant, $enfant, $jour);
        }

        $html = $this->environment->render(
            '@AcMarcheMercrediAdmin/plaine/pdf/plaine_pdf.html.twig',
            [
                'plaine' => $plaine,
                'firstDay' => $firstDay,
                'pdfPlaine' => $pdfPlaine,
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
        $root = $this->project_dir.'/public/bundles/acmarchemercredi/images/';
        $ok = $root.'check_ok.jpg';
        $ko = $root.'check_ko.jpg';
        $data = [];
        $data['ok'] = base64_encode(file_get_contents($ok));
        $data['ko'] = base64_encode(file_get_contents($ko));

        return $data;
    }
}
