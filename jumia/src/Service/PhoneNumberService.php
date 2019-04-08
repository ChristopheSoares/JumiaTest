<?php


namespace App\Service;


use App\Entity\Country;
use App\Repository\CustomerRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;

class PhoneNumberService
{

    private $customerRepository;
    private $manager;

    public function __construct(CustomerRepository $customerRepository, ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->customerRepository = $customerRepository;

    }


    /**
     * Format and validate  filter send, and filter phone numbers from database using filters
     * @param $filters - info send from front end to filter records,
     * @return JsonResponse
     */
    public function fetchFilteredPhoneNumberData($filters)
    {

        $init = 0;
        $offset = 5;
        $selectedCountry = null;
        $selectedState = null;

        if (isset($filters['start'])) {
            if (!is_numeric($filters['length'])) {
                return new JsonResponse("Invalid start type(need be integer)", 422);
            }

            $init = $filters['start'];
        } else {
            return new JsonResponse("Missing start value", 422);
        }

        if (isset($filters['length'])) {
            if (!is_numeric($filters['length'])) {
                return new JsonResponse("Invalid length type(need be integer)", 422);
            }

            $offset = $filters['length'];
        } else {
            return new JsonResponse("Missing start length", 422);
        }



        if (isset($filters['selectedState']) && $filters['selectedState'] != "null") {
            $selectedState = intval($filters['selectedState']);

            if ($selectedState != 0 && $selectedState != 1) {
                return new JsonResponse("Invalid selected State", 422);
            }
        }

        if (isset($filters['selectedCountry']) && $filters['selectedCountry'] != "0") {
            $selectedCountry = $filters['selectedCountry'];

            if (!is_numeric($selectedCountry)) {
                return new JsonResponse("Invalid selected State", 422);
            }

            $country = $this->manager->getRepository(Country::class)->find($selectedCountry);
            if ($country == null) {
                return new JsonResponse("Country not found", 422);
            }
        }

        // see how many records exist using filter
        try {
            $count = $this->customerRepository->countFilterPhoneNumbers($selectedCountry, $selectedState);
        } catch (DBALException $e) {
            return new JsonResponse("Error fetching info from DB", 500);
        }
        $count = intval($count['total']);

        if(($init)>$count){
            return new JsonResponse("Start can not be bigger then existing records ", 422);
        }


        $return = [];

        // fetch records exist using filter
        try {
            $return['data'] = $this->customerRepository->fetchFilterPhoneNumbers($init, $offset, $selectedCountry, $selectedState);
        } catch (DBALException $e) {
            return new JsonResponse("Error fetching info from DB", 500);
        }
        $return['recordsTotal'] = $count;
        $return['recordsFiltered'] = $count;
        return new JsonResponse($return);
    }
}