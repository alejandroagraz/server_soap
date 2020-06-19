<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\SoapService;

class SoapServerController extends AbstractController
{
    /**
     * @Route("/soap/server", name="soap_server")
     */
    public function soapServer(SoapService $soapService)
    {

        $soapServer = new \SoapServer('http://wsdl.doc/soapServer.wsdl');
        $soapServer->setObject($soapService);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());

        return $response;


    }
}
