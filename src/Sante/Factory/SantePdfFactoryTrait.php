<?php

namespace AcMarche\Mercredi\Sante\Factory;

use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class SantePdfFactoryTrait
{
    use PdfDownloaderTrait;

    public function __construct(
        private SanteQuestionRepository $santeQuestionRepository,
        private OrganisationRepository $organisationRepository,
        private SanteChecker $santeChecker,
        private Environment $environment
    ) {
    }

    public function santeFiche(SanteFiche $santeFiche): Response
    {
        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        $organisation = $this->organisationRepository->getOrganisation();
        $enfant = $santeFiche->getEnfant();
        $html = $this->environment->render(
            '@AcMarcheMercredi/sante/pdf/fiche.html.twig',
            [
                'enfant' => $enfant,
                'sante_fiche' => $santeFiche,
                'is_complete' => $isComplete,
                'questions' => $questions,
                'organisation' => $organisation,
            ]
        );

        // return new Response($html);
        return $this->downloadPdf($html, $enfant->getSlug().'-sante.pdf');
    }
}
