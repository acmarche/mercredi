<?php

namespace AcMarche\Mercredi\Sante\Validator;

use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ResponseIsCompleteValidator extends ConstraintValidator
{
    private SanteChecker $santeChecker;

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
    public function validate($questions, Constraint $constraint): void
    {
        if (is_iterable($questions)) {
            foreach ($questions as $question) {
                if (!$this->santeChecker->checkQuestionOk($question)) {
                    $order = $question->getDisplayOrder() ?: 0;
                    if ($question->getComplement() === true) {
                        $txt = $question->getNom().' : Indiquez => '.$question->getComplementLabel();
                    } else {
                        $txt = $question->getNom().' répondez par oui ou non';
                    }
                    $this->context->buildViolation($constraint->message_question)
                        ->atPath('sante_fiche_etape3[questions]['.$order.'][remarque]')
                        ->setParameter('{{ string }}', $txt)
                        ->addViolation();
                }
            }
        }
    }
}
