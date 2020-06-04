<?php


namespace AcMarche\Mercredi\Sante\Utils;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;

class SanteChecker
{
    public function ficheIsComplete(Enfant $enfant)
    {
        if (!$enfant->getNom()) {
            return false;
        }

        if (!$enfant->getPrenom()) {
            return false;
        }

        if (!$enfant->getEcole()) {
            return false;
        }

        if (!$enfant->getAnneeScolaire()) {
            return false;
        }

        return true;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function checkFicheEnfants($enfants)
    {
        foreach ($enfants as $enfant) {
            $enfant->setFicheComplete(self::ficheIsComplete($enfant));
        }
    }

    public function isComplete(Enfant $enfant): bool
    {
        $santeFiche = $this->getSanteFiche($enfant);
        $reponses = $this->getReponses($santeFiche);
        $questions = $this->getAllQuestions();

        if (count($reponses) < count($questions)) {
            return false;
        }

        foreach ($reponses as $reponse) {
            $question = $reponse->getQuestion();
            if (!$this->checkQuestionOk($question)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function checkQuestionOk(SanteQuestion $question)
    {
        if ($question->getComplement()) {
            if ($question->getReponse()) {
                if (trim('' == $question->getRemarque())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function isCompleteForEnfants(array $enfants)
    {
        foreach ($enfants as $enfant) {
            if ($this->isComplete($enfant)) {
                $enfant->setSanteFicheComplete(true);
            }
        }
    }
}
