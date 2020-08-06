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
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;
    /**
     * @var SanteReponseRepository
     */
    private $santeReponseRepository;
    /**
     * @var SanteHandler
     */
    private $santeHandler;

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

        if ($enfant->getEcole() === null) {
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

        return true;
    }

    /**
     * @param SanteQuestion $santeQuestion
     * @return bool
     */
    public function checkQuestionOk(SanteQuestion $santeQuestion): bool
    {
        if (!$santeQuestion->getComplement()) {
            return true;
        }
        if (!$santeQuestion->getReponseTxt()) {
            return true;
        }
        if (trim('' === $santeQuestion->getRemarque()) !== '') {
            return false;
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
