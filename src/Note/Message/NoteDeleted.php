<?php

namespace AcMarche\Mercredi\Note\Message;

final class NoteDeleted
{
    private int $noteId;

    public function __construct(int $noteId)
    {
        $this->noteId = $noteId;
    }

    public function getNoteId(): int
    {
        return $this->noteId;
    }
}
