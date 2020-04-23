<?php

namespace AcMarche\Mercredi\Note\Message;

class NoteCreated
{
    /**
     * @var int
     */
    private $noteId;

    public function __construct(int $noteId)
    {
        $this->noteId = $noteId;
    }

    /**
     * @return int
     */
    public function getNoteId(): int
    {
        return $this->noteId;
    }
}
