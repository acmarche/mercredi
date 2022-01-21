<?php

namespace AcMarche\Mercredi\Sante\Utils;

use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

final class SanteBinder
{
    public function __construct(
        private SanteQuestionRepository $santeQuestionRepository,
        private SanteReponseRepository $santeReponseRepository
    ) {
    }

    /**
     * @return SanteQuestion[]
     */
    public function bindResponses(SanteFiche $santeFiche): array
    {
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
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
