<?php

namespace AcMarche\Mercredi\Document\Message;

class DocumentDeleted
{
    /**
     * @var int
     */
    private $documentId;

    public function __construct(int $documentId)
    {
        $this->documentId = $documentId;
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }
}
