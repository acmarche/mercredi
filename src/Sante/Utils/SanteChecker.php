<?php

namespace AcMarche\Mercredi\Sante\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

use function count;

final class SanteChecker
{
    private SanteQuestionRepository $santeQuestionRepository;
    private SanteReponseRepository $santeReponseRepository;
    private SanteHandler $santeHandler;

    public function __construct(
        SanteQuestionRepository $santeQuestionRepository,
        SanteReponseRepository $santeReponseRepository,
        SanteHandler $santeHandler
    ) {
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->santeReponseRepository = $santeReponseRepository;
        $this->santeHandler = $santeHandler;
    }

    public function identiteEnfantIsComplete(Enfant $enfant): bool
    {
        if (!$enfant->getNom()) {
            return false;
        }

        if (!$enfant->getPrenom()) {
            return false;
        }

        if (null === $enfant->getEcole()) {
            return false;
        }

        if (null === $enfant->getPoids()) {
            return false;
        }

        if (null === $enfant->getRegistreNational()) {
            return false;
        }

        return (bool)$enfant->getAnneeScolaire();
    }

    public function isComplete(SanteFiche $santeFiche): bool
    {
        if (!$santeFiche->getId()) {
            return false;
        }

        $reponses = $this->santeReponseRepository->findBySanteFiche($santeFiche);
        $questions = $this->santeQuestionRepository->findAll();

        if (count($reponses) < count($questions)) {
            return false;
        }

        foreach ($reponses as $reponse) {
            $question = $reponse->getQuestion();
            if (!$this->checkQuestionOk($question)) {
                return false;
            }
        }
        return count($santeFiche->getAccompagnateurs()) >= 1;
    }

    /**
     * @param SanteFiche $santeFiche
     * @return SanteQuestion[]
     */
    public function getQuestionsNotOk(SanteFiche $santeFiche): array
    {
        $questionsnotOk = [];
        $reponses = $this->santeReponseRepository->findBySanteFiche($santeFiche);
        foreach ($reponses as $reponse) {
            $question = $reponse->getQuestion();
            if (!$this->checkQuestionOk($question)) {
                $questionsnotOk [] = $question;
            }
        }

        return $this->santeQuestionRepository->getQuestionsNonRepondues($questionsnotOk);
    }

    public function checkQuestionOk(SanteQuestion $santeQuestion): bool
    {
        if (!$santeQuestion->getComplement()) {
            return true;
        }
        if ($santeQuestion->getReponseTxt() === '') {
            return true;
        }
        return '' != trim($santeQuestion->getRemarque()) && null !== $santeQuestion->getRemarque();
    }

    /**
     * @param Enfant[] $enfants
     */
    public function isCompleteForEnfants(array $enfants): void
    {
        foreach ($enfants as $enfant) {
            $santeFiche = $this->santeHandler->init($enfant);
            $enfant->setFicheSanteIsComplete($this->isComplete($santeFiche));
        }
    }
}
