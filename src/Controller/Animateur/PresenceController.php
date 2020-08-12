<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Form\SearchPresenceType;
use AcMarche\Mercredi\Presence\Handler\PresenceHandler;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_ECOLE")
 */
final class PresenceController extends AbstractController
{
    use GetAnimateurTrait;

    /**
     * @var string
     */
    private const UUID = 'uuid';
    /**
     * @var string
     */
    private const MERCREDI_PARENT_ENFANT_SHOW = 'mercredi_animateur_enfant_show';

    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PresenceHandler
     */
    private $presenceHandler;
    /**
     * @var RelationUtils
     */
    private $relationUtils;
    /**
     * @var SanteChecker
     */
    private $santeChecker;
    /**
     * @var SanteHandler
     */
    private $santeHandler;

    public function __construct(
        RelationUtils $relationUtils,
        PresenceRepository $presenceRepository,
        PresenceHandler $presenceHandler,
        SanteChecker $santeChecker,
        SanteHandler $santeHandler
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->presenceHandler = $presenceHandler;
        $this->relationUtils = $relationUtils;
        $this->santeChecker = $santeChecker;
        $this->santeHandler = $santeHandler;
    }

    /**
     * Route("/", name="mercredi_animateur_presence_index", methods={"GET","POST"}).
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchPresenceType::class);
        $form->handleRequest($request);
        $data = [];
        $search = $displayRemarque = false;
        $jour = $remarques = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            /** @var Jour $jour */
            $jour = $dataForm['jour'];
            $displayRemarque = $dataForm['displayRemarque'];

            $search = true;
            $data = $this->presenceHandler->handleForGrouping($jour, $dataForm['animateur'], $displayRemarque);
        }

        return $this->render(
            '@AcMarcheMercrediAnimateur/presence/index.html.twig',
            [
                'data' => $data,
                'form' => $form->createView(),
                'search' => $search,
                'jour' => $jour,
                'display_remarques' => $displayRemarque,
            ]
        );
    }
}
