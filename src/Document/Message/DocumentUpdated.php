<?php

namespace AcMarche\Mercredi\Document\Message;

final class DocumentUpdated
{
    private int $documentId;

    public function __construct(int $documentId)
    {
        $this->documentId = $documentId;
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }
}
