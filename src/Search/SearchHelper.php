<?php

namespace AcMarche\Mercredi\Search;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SearchHelper
{
    public const ENFANT_LIST = 'enfant_list';
    public const TUTEUR_LIST = 'tuteur_list';
    public const PRESENCE_LIST = 'presence_list';
    public const PRESENCE_LIST_BY_MONTH = 'presence_list_by_month';
    public const MESSAGE_INDEX = 'message_index';

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function saveSearch(string $name, array $args): void
    {
        $this->session->set($name, $args);
    }

    public function getArgs(string $name): array
    {
        return $this->session->get($name, []);
    }

    public function deleteSearch(string $name): void
    {
        $this->session->remove($name);
    }
}
