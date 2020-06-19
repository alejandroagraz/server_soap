<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customer;

class SoapService
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function registerCustomer($dni,$name,$last_name,$phone,$email) {

        $customerRepository = $this->em->getRepository(Customer::class);
        $existingCustomer = $customerRepository->findOneBy(
            array(
                'dni' => $dni,
                'phone' => $phone
            )
        );

        if($existingCustomer){
            $response = [
                'status' => 'err',
                'message' => 'Customer Is Already Registered'
            ];
            return json_encode($response);

        } else{
            $customer = new Customer();
            try {
                $customer->setDni($dni);
                $customer->setName($name);
                $customer->setLastName($last_name);
                $customer->setPhone($phone);
                $customer->setEmail($email);

                $this->em->persist($customer);
                $this->em->flush();

                $response = [
                    'status' => 'success',
                    'message' => 'Customer Successfully Registered'
                ];
                return json_encode($response);

            } catch (Exception $e) {
                $response = [
                    'status' => 'err',
                    'message' => 'Option Not Available At The Moment'
                ];
                return json_encode($response);
            }
        }        
    }

    public function rechargeWallet($dni,$phone,$balance) {

        $customerRepository = $this->em->getRepository(Customer::class);
        $customer = $customerRepository->findOneBy(
            array(
                'dni' => $dni,
                'phone' => $phone
            )
        );

        if(!$customer) {
            $response = [
                'status' => 'err',
                'message' => 'Customer Not Registered'
            ];
            return json_encode($response);
        }

        try {

            $balanceUpdate = is_null($customer->getBalance()) ? $balance : $customer->getBalance() + $balance;

            $customer->setBalance($balanceUpdate);
            $this->em->persist($customer);
            $this->em->flush();

            $response = [
                'status' => 'success',
                'message' => 'Recharge Wallet Done Successfully'
            ];
             return json_encode($response);

        } catch (Exception $e) {
            $response = [
                'status' => 'err',
                'message' => 'Option Not Available At The Moment'
            ];
             return json_encode($response);
        }        
    }

    public function payment(
        $dni=false,$phone=false,$amount_payable=false,
        $id=false,$token=false,$session_id=false
    ) {

        $customerRepository = $this->em->getRepository(Customer::class);

        if($id && $token && $session_id){

            try {

                $customer = $customerRepository->find($id);
                $customer->setToken($token);
                $customer->setSessionId($session_id);
                $this->em->persist($customer);
                $this->em->flush();

                $response = [
                    'status' => 'success',
                    'message' => 'Token and session_id successfully registered'
                ];
                return json_encode($response);

            } catch (Exception $e) {

                $response = [
                    'status' => 'err',
                    'message' => 'Option Not Available At The Moment'
                ];

                return json_encode($response);

            }
            
        } else {

            $customer = $customerRepository->findOneBy(
                array(
                    'dni' => $dni,
                    'phone' => $phone
                )
            );
    
            if(!$customer) {
                $response = [
                    'status' => 'err',
                    'message' => 'Customer Not Registered'
                ];
                return json_encode($response);
            }

            try {

                $data = [
                    'id' => (string)$customer->getId(),
                    'email' => $customer->getEmail(),
                    'balance' => $customer->getBalance()
                ];
    
                $response = [
                    'status' => 'success',
                    'response' => $data
                ];
                return json_encode($response);

            } catch (Exception $e) {

                $response = [
                    'status' => 'err',
                    'message' => 'Option Not Available At The Moment'
                ];

                return json_encode($response);

            }
            
        }
        
    }

    public function confirmPayment($id,$balance=false,$token=false,$session_id=false){

        $customerRepository = $this->em->getRepository(Customer::class);
        $customer = $customerRepository->find($id);
        if(!$customer) {
            $response = [
                'status' => 'err',
                'message' => 'Customer Not Registered'
            ];
            return json_encode($response);
        }

        if($id && $balance && $token == null && $session_id == null){

            try {

                $customer->setBalance($balance);
                $customer->setToken(null);
                $customer->setSessionId(null);
                $this->em->persist($customer);
                $this->em->flush();

                $response = [
                    'status' => 'success',
                    'message' => 'Payment Made Successfully'
                ];
                return json_encode($response);

            } catch (Exception $e) {

                $response = [
                    'status' => 'err',
                    'message' => 'Your Payment Could Not Be Confirmed'
                ];

                return json_encode($response);

            }

        } else {

            try {

                $data = [
                    'id' => $customer->getId(),
                    'balance' => $customer->getBalance(),
                    'token' => $customer->getToken(),
                    'session_id' => $customer->getSessionId()
                ];
    
                $response = [
                    'status' => 'success',
                    'response' => $data
                ];
                return json_encode($response);
    
            } catch (Exception $e) {
    
                $response = [
                    'status' => 'err',
                    'message' => 'Option Not Available At The Moment'
                ];
    
                return json_encode($response);
    
            }

        }


    }

    public function checkBalance($dni,$phone) {

        $customerRepository = $this->em->getRepository(Customer::class);
        $customer = $customerRepository->findOneBy(
            array(
                'dni' => $dni,
                'phone' => $phone
            )
        );
        if(!$customer) {
            $response = [
                'status' => 'err',
                'message' => 'Customer Not Registered'
            ];
            return json_encode($response);
        }


        $response = [
            'status' => 'success',
            'balance' => $customer->getBalance()
        ];
            return json_encode($response);
       
    }

}