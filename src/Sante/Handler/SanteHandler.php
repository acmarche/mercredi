<?php

namespace AcMarche\Mercredi\Sante\Handler;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Factory\SanteFactory;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;
use AcMarche\Mercredi\Sante\Utils\SanteBinder;

final class SanteHandler
{
    private SanteFicheRepository $santeFicheRepository;
    private SanteFactory $santeFactory;
    private SanteBinder $santeBinder;
    private SanteReponseRepository $santeReponseRepository;

    public function __construct(
        SanteFicheRepository $santeFicheRepository,
        SanteReponseRepository $santeReponseRepository,
        SanteFactory $santeFactory,
        SanteBinder $santeBinder
    ) {
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeFactory = $santeFactory;
        $this->santeBinder = $santeBinder;
        $this->santeReponseRepository = $santeReponseRepository;
    }

    public function init(Enfant $enfant, bool $bind = true): SanteFiche
    {
        $santeFiche = $this->santeFactory->getSanteFicheByEnfant($enfant);
        if ($bind) {
            $this->santeBinder->bindResponses($santeFiche);
        }

        return $santeFiche;
    }

    /**
     * Si pas de reponse ou remarque on ne cree pas la reponse.
     *
     * @param SanteQuestion[] $questions
     *
     * @return void|null
     */
    public function handle(SanteFiche $santeFiche, array $questions)
    {
        $this->santeFicheRepository->flush();
        foreach ($questions as $question) {
            if (null === $question->getReponseTxt()) {
                return null;
            }
            if (null === ($reponse = $this->santeReponseRepository->getResponse($santeFiche, $question))) {
                $reponse = $this->santeFactory->createSanteReponse($santeFiche, $question);
            }
            $reponse->setReponse($question->getReponseTxt());
            $reponse->setRemarque($question->getRemarque());
            $this->santeReponseRepository->flush();
        }
    }
}
