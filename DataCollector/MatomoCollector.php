<?php

namespace Aldaflux\AldafluxMatomoBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;

use Aldaflux\AldafluxMatomoBundle\Service\MatomoService;

class MatomoCollector extends AbstractDataCollector{

    private $matomoService;

    public function __construct(MatomoService $matomoService)
    {
        $this->matomoService = $matomoService;
    }
    
    
    public function getName() : string
    {
        return 'aldaflux.matomo_collector';
    }
    
    
     public function reset(): void
    {
        $this->data = [];
    }

    
    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $this->data = [
            'logs' => $this->matomoService->getLogs(),
            'nb_logs' => $this->matomoService->getNbLogs(),
            'nb_error_logs' => $this->matomoService->getNbErrorLogs(),
        ];
    }
    
    public function getLogs(): array
    {
        return $this->data['logs'] ?? [];
    }
    
    public function getNbLogs(): int
    {
        return $this->data['nb_logs'] ?? 0;
    }
    
    public function getNbErrorLogs(): int
    {
        return $this->data['nb_error_logs'] ?? 0;
    }

    public static function getTemplate(): ?string
    {
        return '@AldafluxMatomo/data_collector/matomo_collector.html.twig';
    }
    
    
    
 
    
    
    
    
}
