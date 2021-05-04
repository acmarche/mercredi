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

    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;
    /**
     * @var SanteChecker
     */
    private $santeChecker;
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(
        SanteQuestionRepository $santeQuestionRepository,
        OrganisationRepository $organisationRepository,
        SanteChecker $santeChecker,
        Environment $environment
    ) {
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->organisationRepository = $organisationRepository;
        $this->santeChecker = $santeChecker;
        $this->environment = $environment;
    }

    public function santeFiche(SanteFiche $santeFiche): Response
    {
        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAll();
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

        return new Response($html);
        return $this->downloadPdf($html, $enfant->getSlug().'-sante.pdf');
    }
}
