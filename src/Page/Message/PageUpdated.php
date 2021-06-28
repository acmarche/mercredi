<?php

namespace AcMarche\Mercredi\Page\Message;

final class PageUpdated
{
    private int $pageId;

    public function __construct(int $pageId)
    {
        $this->pageId = $pageId;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }
}
