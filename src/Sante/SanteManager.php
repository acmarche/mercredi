<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 23/08/18
 * Time: 14:57.
 */

namespace AcMarche\Mercredi\Sante;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Entity\Sante\SanteReponse;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

class SanteManager
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var SanteFicheRepository
     */
    private $santeFicheRepository;
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;
    /**
     * @var SanteReponseRepository
     */
    private $santeReponseRepository;

    public function __construct(
        EnfantRepository $enfantRepository,
        SanteFicheRepository $santeFicheRepository,
        SanteQuestionRepository $santeQuestionRepository,
        SanteReponseRepository $santeReponseRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->santeReponseRepository = $santeReponseRepository;
    }

    /**
     * @return SanteFiche|null
     */
    public function getSanteFiche(Enfant $enfant): SanteFiche
    {
        if (!$santeFiche = $this->santeFicheRepository->findOneBy(['enfant' => $enfant])) {
            $santeFiche = new SanteFiche();
            $santeFiche->setEnfant($enfant);
        }

        return $santeFiche;
    }

    /**
     * @return SanteQuestion[]
     */
    public function bindResponses(SanteFiche $santeFiche)
    {
        $questions = $this->getAllQuestions();
        foreach ($questions as $question) {
            $reponse = $this->getSanteReponse($santeFiche, $question);

            if ($reponse instanceof SanteReponse) {
                $reponse->getQuestion();
                $question->setReponse($reponse->getReponse());
                $question->setRemarque($reponse->getRemarque());
            } else {
                $question->setReponse(null);
            }
        }

        return $questions;
    }

    /**
     * @return SanteReponse[]
     */
    public function getReponses(SanteFiche $santeFiche)
    {
        return $this->santeReponseRepository->findBy(['sante_fiche' => $santeFiche]);
    }

    /**
     * @return SanteQuestion[]
     */
    public function getAllQuestions()
    {
        return $this->santeQuestionRepository->findAll();
    }

    /**
     * Donne la reponse a une question ou pas.
     *
     * @return SanteReponse
     */
    public function getSanteReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        return $this->santeReponseRepository->findOneBy(
            ['sante_fiche' => $santeFiche, 'question' => $santeQuestion]
        );
    }

    /**
     * Si pas de reponse ou remarque on ne cree pas la reponse.
     *
     * @return void|null
     */
    public function handleReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        $santeReponse = $this->getSanteReponse($santeFiche, $santeQuestion);
        if (!$santeReponse) {
            if (null === $santeQuestion->getReponse() && !$santeQuestion->getRemarque()) {
                return null;
            }
            $santeReponse = $this->createSanteReponse($santeFiche, $santeQuestion);
        }

        $santeReponse->setReponse($santeQuestion->getReponse());
        $santeReponse->setRemarque($santeQuestion->getRemarque());
    }

    /**
     * @return SanteReponse
     */
    public function createSanteReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        $santeReponse = new SanteReponse();
        $santeReponse->setSanteFiche($santeFiche);
        $santeReponse->setQuestion($santeQuestion);
        $this->santeReponseRepository->persist($santeReponse);
        $this->santeReponseRepository->flush();

        return $santeReponse;
    }

    /**
     * @todo if else necessaire?
     */
    public function saveSanteFiche(SanteFiche $santeFiche)
    {
        if (!$santeFiche->getId()) {
            $this->santeFicheRepository->persist($santeFiche);
        } else {
            $santeFiche->setUpdatedAt(new \DateTime());
        }
        $this->santeFicheRepository->flush();
    }

    public function Reponsesflush()
    {
        $this->santeReponseRepository->flush();
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
