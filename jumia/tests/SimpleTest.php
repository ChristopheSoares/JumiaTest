<?php


namespace App\Tests;


use App\Repository\CustomerRepository;
use App\Service\PhoneNumberService;
use mysql_xdevapi\Result;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class SimpleTest extends WebTestCase
{

    /**
     * Test only sending start and length to PhoneNumberService
     */
    public function testSuccess(){
        self::bootKernel();

        // gets the special container that allows fetching private services
        $container = self::$container;

        /**
         *Declare filters
         */
        $filters=[];
        $filters['start']=0;
        $filters['length']=5;

        /**
         * @var JsonResponse $result
         */
        $result=$container->get('app.service.phoneNumberService')->fetchFilteredPhoneNumberData($filters);
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * Test not sending start lenght
     */
    public function testSuccessNotSendingStartLength(){
        self::bootKernel();

        // gets the special container that allows fetching private services
        $container = self::$container;

        /**
         *Declare filters
         */
        $filters=[];


        /**
         * @var JsonResponse $result
         */
        $result=$container->get('app.service.phoneNumberService')->fetchFilteredPhoneNumberData($filters);
        $this->assertEquals(422, $result->getStatusCode());
    }

    /**
     * Test only sending country
     */
    public function testSuccessWithCountry(){
        self::bootKernel();

        // gets the special container that allows fetching private services
        $container = self::$container;

        /**
         *Declare filters
         */
        $filters=[];
        $filters['start']=0;
        $filters['length']=5;
        $filters['selectedCountry']=1;

        /**
         * @var JsonResponse $result
         */
        $result=$container->get('app.service.phoneNumberService')->fetchFilteredPhoneNumberData($filters);
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * Test only sending valid phone number
     */
    public function testSuccessValidPhones(){
        self::bootKernel();

        // gets the special container that allows fetching private services
        $container = self::$container;

        /**
         *Declare filters
         */
        $filters=[];
        $filters['start']=0;
        $filters['length']=5;
        $filters['selectedState']=1;

        /**
         * @var JsonResponse $result
         */
        $result=$container->get('app.service.phoneNumberService')->fetchFilteredPhoneNumberData($filters);
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * Test only sending invalid country
     */
    public function testErrorInvalidCountry(){
        self::bootKernel();

        // gets the special container that allows fetching private services
        $container = self::$container;

        /**
         *Declare filters
         */
        $filters=[];
        $filters['start']=0;
        $filters['length']=5;
        $filters['selectedCountry']="country";

        /**
         * @var JsonResponse $result
         */
        $result=$container->get('app.service.phoneNumberService')->fetchFilteredPhoneNumberData($filters);
        $this->assertEquals(422, $result->getStatusCode());
    }

    /**
     * Test only sending no existing country
     */
    public function testErrorNoExistingCountry(){
        self::bootKernel();

        // gets the special container that allows fetching private services
        $container = self::$container;

        /**
         *Declare filters
         */
        $filters=[];
        $filters['start']=0;
        $filters['length']=5;
        $filters['selectedCountry']=100;

        /**
         * @var JsonResponse $result
         */
        $result=$container->get('app.service.phoneNumberService')->fetchFilteredPhoneNumberData($filters);
        $this->assertEquals(422, $result->getStatusCode());
    }

    /**
     * Test only sending invalid phone number
     */
    public function testErrorInvalidPhones(){
        self::bootKernel();

        // gets the special container that allows fetching private services
        $container = self::$container;

        /**
         *Declare filters
         */
        $filters=[];
        $filters['start']=0;
        $filters['length']=5;
        $filters['selectedState']="state";

        /**
         * @var JsonResponse $result
         */
        $result=$container->get('app.service.phoneNumberService')->fetchFilteredPhoneNumberData($filters);
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * Test only sending invalid phone number
     */
    public function testErrorBigStart(){
        self::bootKernel();

        // gets the special container that allows fetching private services
        $container = self::$container;

        /**
         *Declare filters
         */
        $filters=[];
        $filters['start']=1000;
        $filters['length']=5;
        $filters['selectedState']="state";

        /**
         * @var JsonResponse $result
         */
        $result=$container->get('app.service.phoneNumberService')->fetchFilteredPhoneNumberData($filters);
        $this->assertEquals(422, $result->getStatusCode());
    }
}