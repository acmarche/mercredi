<?php

namespace AcMarche\Mercredi\Http;

use Exception;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait ConnectionTrait
{
    private HttpClientInterface $httpClient;
    private ?string $code_query = null;
    private ?string $ws_key = null;
    public ?string $url_executed = null;
    public ?string $data_raw = null;

    public function connect(): void
    {
        $headers = [

        ];

        $this->httpClient = HttpClient::create($headers);
    }

    /**
     * @throws Exception
     */
    private function executeRequest(string $url, array $options = [], string $method = 'GET'): string
    {
        $this->url_executed = $url;
        try {
            $response = $this->httpClient->request(
                $method,
                $url,
                $options,
            );

            $this->data_raw = $response->getContent();

            return $this->data_raw;
        } catch (ClientException|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $exception) {
            throw  new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function debug(ResponseInterface $response)
    {
        var_dump($response->getInfo('debug'));
    }

}
