<?php


namespace App\Services;

use App\Exceptions\APIException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ExchangeRateAPIClient
{
    /**
     * @return array
     * @throws APIException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function downloadExchangeRates(): array
    {
        try{
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', 'http://api.nbp.pl/api/exchangerates/tables/a/today/');

            return $response->toArray();
        }catch(\Exception $e){
            throw new APIException();
        }

    }
}
