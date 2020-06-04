<?php

namespace AcMarche\Mercredi\Sante\Utils;

use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

class SanteBinder
{
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;
    /**
     * @var SanteReponseRepository
     */
    private $santeReponseRepository;

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
        if (!$santeFiche->getId()) {
            $santeFiche->setQuestions($questions);

            return $questions;
        }
        foreach ($questions as $question) {
            if ($reponse = $this->santeReponseRepository->getResponse($santeFiche, $question)) {
                $reponse->getQuestion();
                $question->setReponse($reponse->getReponse());
                $question->setRemarque($reponse->getRemarque());
            } else {
                $question->setReponse(null);
            }
        }

        $santeFiche->setQuestions($questions);

        return $questions;
    }
}
