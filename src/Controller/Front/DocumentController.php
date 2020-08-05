<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * @Route("/document")
 */
class DocumentController extends AbstractController
{
    /**
     * @var DownloadHandler
     */
    private $downloadHandler;

    public function __construct(DownloadHandler $downloadHandler)
    {
        $this->downloadHandler = $downloadHandler;
    }

    /**
     * @Route("/{id}", name="mercredi_font_document_download")
     */
    public function index(Document $document)
    {
        return $this->downloadHandler->downloadObject($document, 'file');
    }
}
