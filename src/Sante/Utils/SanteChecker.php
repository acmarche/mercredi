<?php

namespace AcMarche\Mercredi\Sante\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

final class SanteChecker
{
    public function __construct(
        private SanteQuestionRepository $santeQuestionRepository,
        private SanteReponseRepository $santeReponseRepository,
        private SanteHandler $santeHandler,
    ) {}

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

        $santeFiche->reasons = [];

        $reponses = $this->santeReponseRepository->findBySanteFiche($santeFiche);
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();

        if (\count($reponses) < \count($questions)) {
            $count = \count($questions) - \count($reponses);
            $santeFiche->reasons[] = 'Vous n\'avez pas répondu à '.$count.' questions';

            return false;
        }

        foreach ($reponses as $reponse) {
            $question = $reponse->getQuestion();
            if (!$this->checkQuestionOk($question)) {
                $santeFiche->reasons[] = 'La réponse à la question : '.$question->getNom().' n\'est pas complète';

                return false;
            }
        }

        if (null === $santeFiche->getEnfant()->getRegistreNational()) {
            $santeFiche->reasons[] = 'Il manque le numéro de registre national';

            return false;
        }

        if (\count($santeFiche->getAccompagnateurs()) === 0) {
            $santeFiche->reasons[] = 'Vous n\'avez pas mis remplis les personnes autorisées à reprendre l’enfant';
        }

        return true;
    }

    /**
     * @return SanteQuestion[]
     */
    public function getQuestionsNotOk(SanteFiche $santeFiche): array
    {
        $questionsnotOk = [];
        $reponses = $this->santeReponseRepository->findBySanteFiche($santeFiche);
        foreach ($reponses as $reponse) {
            $question = $reponse->getQuestion();
            if (!$this->checkQuestionOk($question)) {
                $questionsnotOk[] = $question;
            }
        }

        return $this->santeQuestionRepository->getQuestionsNonRepondues($questionsnotOk);
    }

    public function checkQuestionOk(SanteQuestion $santeQuestion): bool
    {
        //pas repondu par oui ou non
        if (null === $santeQuestion->getReponseTxt()) {
            return false;
        }
        //si complement on verifie si mis
        if ($santeQuestion->getComplement()) {
            //on repond non
            if (0 === (int)$santeQuestion->getReponseTxt()) {
                return true;
            }
            if (null === $santeQuestion->getRemarque()) {
                return false;
            }

            return '' !== trim($santeQuestion->getRemarque());
        }

        return true;
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
