<?php

namespace AcMarche\Mercredi\Sante\Validator;

use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ResponseIsCompleteValidator extends ConstraintValidator
{
    /**
     * @var SanteChecker
     */
    private $santeChecker;

    public function __construct(SanteChecker $santeChecker)
    {
        $this->santeChecker = $santeChecker;
    }

    /**
     * Si une question demande un complement
     * Si la reponse est oui
     * Si champ remarque remplis.
     *
     * @param SanteQuestion[] $questions
     */
    public function validate($questions, Constraint $constraint)
    {
        foreach ($questions as $question) {
            if (!$this->santeChecker->checkQuestionOk($question)) {
                $order = $question->getDisplayOrder() ? $question->getDisplayOrder() : 0;
                $this->context->buildViolation($constraint->message_question)
                    ->atPath('sante_fiche[questions]['.$order.'][remarque]')
                    ->setParameter('{{ string }}', $question->getNom().' : '.$question->getComplementLabel())
                    ->addViolation();
            }
        }
    }
}
