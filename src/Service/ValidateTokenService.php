<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customer;
use App\Entity\Token;

class ValidateTokenService
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager )
    {
        $this->em = $entityManager;
    }

    public function validateToken($token,$customer) {

        $customerRepository = $this->em->getRepository(Customer::class);
        $tokenRepository = $this->em->getRepository(Token::class);

        try{;

            $query = $tokenRepository->createQueryBuilder("t")
            ->select('t.id, t.activate', 't.expiration_date')
            ->where("t.token = :token")
            ->andWhere("t.customer = :customer")
            ->setParameter("token", $token)
            ->setParameter("customer", $customer)
            ->setMaxResults(1)
            ->getQuery();
            $token_validate = $query->getResult();

            if($token_validate){

                $now = new \DateTime("now", new \DateTimeZone('America/Caracas') );
                
                if($token_validate[0]['activate'] == "enable" && $token_validate[0]['expiration_date'] >= $now->format('Y-m-d H:i:s')) {
                    
                    $response = [
                        'status' => 'success',
                    ];
        
                    return $response;
        
                } else {

                    $token = $tokenRepository->find($token_validate[0]['id']);
        
                    $token->setActivate('disable');
                    $this->em->persist($token);
                    $this->em->flush();
        
                    $response = [
                        'status' => 'err',
                        'message' => 'Your session has expired',
                    ];
                    return $response;
                }
    
            } else {

                $response = [
                    'status' => 'err',
                    'message' => 'Invalid Token',
                ];
                return $response;
                
            }

        } catch (Exception $e) {
            $response = [
                'status' => 'err',
                'message' => 'Invalid Token',
            ];
            return json_encode($response);
        }
       
    }
}