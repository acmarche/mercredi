<?php

namespace AcMarche\Mercredi\Note\Message;

final class NoteCreated
{
    public function __construct(
        private int $noteId
    ) {
    }

    public function getNoteId(): int
    {
        return $this->noteId;
    }
}
