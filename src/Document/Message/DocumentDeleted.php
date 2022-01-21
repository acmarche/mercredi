<?php

namespace AcMarche\Mercredi\Document\Message;

final class DocumentDeleted
{
    public function __construct(
        private int $documentId
    ) {
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }
}
