<?php

namespace Aldaflux\AldafluxMatomoBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;


class MatomoCollector extends AbstractDataCollector{

    public function __construct()
    {
    
    }
    
    
    public function getName() : string
    {
        return 'aldaflux.matomo_collector';
    }
    
    
     public function reset(): void
    {
        $this->data = [];
    }

    
    
//    Response $response
            
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
            $this->data = ['test' => "OK"];
    }
    
    

    public static function getTemplate(): ?string
    {
        return '@AldafluxMatomo/data_collector/matomo_collector.html.twig';
    }
    
    
    
 
    
    
    
    
}