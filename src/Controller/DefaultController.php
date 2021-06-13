<?php


namespace App\Controller;


use App\Entity\Currency;
use App\Exceptions\APIException;
use App\Services\CurrencyHydrator;
use App\Services\ExchangeRateAPIClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @param ExchangeRateAPIClient $apiClient
     * @param CurrencyHydrator $hydrator
     * @return Response
     */
    public function homepage(ExchangeRateAPIClient $apiClient, CurrencyHydrator $hydrator): Response
    {
        $current_exchange_rates = null;

        $this->updateRates($apiClient, $hydrator);

        try{
            $repository = $this->getDoctrine()->getRepository(Currency::class);
            $current_exchange_rates = $repository->findAll();
        }catch(\Exception $e){
            $this->addFlash(
                'notice', 'Baza jest pusta'
            );
        }


        return $this->render('index.html.twig', [
            "currencies" => $current_exchange_rates
        ]);
    }

    /**
     * @param ExchangeRateAPIClient $apiClient
     * @param CurrencyHydrator $hydrator
     */
    private function updateRates(ExchangeRateAPIClient $apiClient, CurrencyHydrator $hydrator){
        try{
            $currencies = $apiClient->downloadExchangeRates();

            $hydrator->hydrateCurrencies($currencies[0]['rates']);

        }catch (APIException $e){
            $this->addFlash(
                'notice', 'Problem z połączeniem z API'
            );
        }catch(\Exception $e){
            $this->addFlash(
                'notice', 'Problem z przetworzeniem danych'
            );
        }
    }
}
