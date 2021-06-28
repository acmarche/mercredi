<?php

namespace AcMarche\Mercredi\Note\Message;

final class NoteUpdated
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
