<?php


namespace App\Services;


use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyHydrator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function hydrateCurrencies(array $data): bool
    {
        if(!$data){
            throw new \InvalidArgumentException();
        }

        foreach ($data as $k => $curr){

            $repository = $this->em->getRepository(Currency::class);
            $currency = $repository->findOneBy(['currency_code' => $curr['code']]);
            if($currency){
                $currency->setExchangeRate(round(floatval($curr['mid']),2));
            }else{
                $currency = new Currency();
                $currency->setName($curr['currency']);
                $currency->setCurrencyCode($curr['code']);
                $currency->setExchangeRate(round(floatval($curr['mid']),2));
            }

            $this->em->persist($currency);
        }


        $this->em->flush();

        return true;
    }
}
