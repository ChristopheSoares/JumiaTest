<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Query\ResultSetMapping;
use PDO;
use Symfony\Bridge\Doctrine\RegistryInterface;


class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Customer::class);

        /**
         * Add custom function regex to entity manager
         */
        $this->getEntityManager()->getConnection()->getWrappedConnection()->sqliteCreateFunction('REGEXP', function ($pattern, $value) {
            mb_regex_encoding('UTF-8');
            return (mb_ereg($pattern, $value)) !== false ? 1 : 0;
        });
    }


    /**
     * Fetch phone numbers records from database according to the given filters
     *
     * @param int $init
     * @param int $offset -
     * @param null|int $countryId - if need to filter by country - id in country table
     * @param null|boolean $state - if is to filter by regex validation(true -valid regex , false - invalid regex)
     * @return array - filtered phone number
     * @throws DBALException
     */
    public function fetchFilterPhoneNumbers($init = 0, $offset = 5, $countryId = null, $state = null)
    {

        $sql = "SELECT 
                phone REGEXP country.regex AS state,
                country.name AS countryName,
                country.code AS  countryCode, 
                substr(phone,instr(phone,' ') +1) AS number
                FROM customer
                LEFT JOIN  country ON instr(substr(phone, 1,instr(phone,' ') -1),country.code)>0";

        $where = '';
        if ($countryId !== null) {
            $where = ' WHERE country.id=:country';
        }

        if ($state !== null) {
            if (empty($where)) {
                $where .= ' WHERE state=:state';
            } else {
                $where .= ' AND state=:state';
            }
        }

        $sql .= "$where LIMIT :init,:offset;";


        $manager = $this->getEntityManager();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->bindValue('init', $init);
        $stmt->bindValue('offset', $offset);

        if ($countryId !== null) {
            $stmt->bindValue('country', $countryId);
        }

        if ($state !== null) {
            $stmt->bindValue('state', $state, PDO::PARAM_BOOL);
        }


        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count phone numbers records from database according to the given filters
     * @param null $countryId
     * @param null $state
     * @return mixed
     * @throws DBALException
     */
    public function countFilterPhoneNumbers($countryId = null, $state = null)
    {

        $sql = "SELECT COUNT (*) AS total
                FROM customer
                LEFT JOIN  country ON instr(substr(phone, 1,instr(phone,' ') -1),country.code)>0";

        $where = '';
        if ($countryId !== null) {
            $where = ' WHERE country.id=:country';
        }

        if ($state !== null) {
            if (empty($where)) {
                $where .= ' WHERE phone REGEXP country.regex =:state';
            } else {
                $where .= ' AND phone REGEXP country.regex =:state';
            }
        }


        $manager = $this->getEntityManager();
        $stmt = $manager->getConnection()->prepare($sql . $where);

        if ($countryId !== null) {
            $stmt->bindValue('country', $countryId);
        }

        if ($state !== null) {
            $stmt->bindValue('state', $state, PDO::PARAM_BOOL);
        }


        $stmt->execute();
        return $stmt->fetch();
    }
}
