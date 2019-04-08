<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Customer;
use App\Service\PhoneNumberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class PhoneNumbersController extends AbstractController
{


    /**
     * @Route("/", name="phoneNumbers")
     */
    public function index()
    {
        $countries = $this->getDoctrine()->getRepository(Country::class)->fetchCountryForSelect();
        return $this->render('base.html.twig',[
            "countries"=>$countries
        ]);
    }

    /**
     * @Route("/filtered", name="filter_number")
     * @param Request $request
     * @param PhoneNumberService $phoneNumberService
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchPhoneNumber(Request $request, PhoneNumberService $phoneNumberService){
        $filters=$request->request->all();
        return $phoneNumberService->fetchFilteredPhoneNumberData($filters);
    }
}
