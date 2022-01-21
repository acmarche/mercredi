<?php

namespace AcMarche\Mercredi\Page\Message;

final class PageDeleted
{
    public function __construct(
        private int $pageId
    ) {
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }
}
