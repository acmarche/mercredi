<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route(path: '/document')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private DownloadHandler $downloadHandler,
    ) {}

    #[Route(path: '/{id}', name: 'mercredi_font_document_download')]
    public function index(Document $document): StreamedResponse
    {
        return $this->downloadHandler->downloadObject($document, 'file');
    }
}
