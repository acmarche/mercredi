<?php

namespace AcMarche\Mercredi\Sante\Utils;

use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

final class SanteBinder
{
    private SanteQuestionRepository $santeQuestionRepository;
    private SanteReponseRepository $santeReponseRepository;

    public function __construct(
        SanteQuestionRepository $santeQuestionRepository,
        SanteReponseRepository $santeReponseRepository
    ) {
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->santeReponseRepository = $santeReponseRepository;
    }

    /**
     * @return SanteQuestion[]
     */
    public function bindResponses(SanteFiche $santeFiche): array
    {
        $questions = $this->santeQuestionRepository->findAll();
        if (! $santeFiche->getId()) {
            $santeFiche->setQuestions($questions);

            return $questions;
        }

        foreach ($questions as $question) {
            $question->setReponseTxt(null);
            if (null !== ($reponse = $this->santeReponseRepository->getResponse($santeFiche, $question))) {
                $question->setReponseTxt($reponse->getReponse());

                $question->setRemarque($reponse->getRemarque());
            }
        }

        $santeFiche->setQuestions($questions);

        return $questions;
    }
}
