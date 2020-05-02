<?php


namespace AcMarche\Mercredi\Search;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SearchHelper
{
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
}
