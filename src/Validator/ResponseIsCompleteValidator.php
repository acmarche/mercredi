<?php

namespace AcMarche\Mercredi\Validator;

use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\SanteManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ResponseIsCompleteValidator extends ConstraintValidator
{
    /**
     * @var SanteManager
     */
    private $santeManager;

    public function __construct(SanteManager $santeManager)
    {
        $this->santeManager = $santeManager;
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
            if (!$this->santeManager->checkQuestionOk($question)) {
                $order = $question->getDisplayOrder() ? $question->getDisplayOrder() : 0;
                $this->context->buildViolation($constraint->message_question)
                    ->atPath('sante_fiche[questions]['.$order.'][remarque]')
                    ->setParameter('{{ string }}', $question->getIntitule().' : '.$question->getComplementLabel())
                    ->addViolation();
            }
        }
    }

    public function validate22($value, Constraint $constraint)
    {
        /* @var $constraint \AcMarche\Mercredi\Validator\ResponseIsComplete */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
