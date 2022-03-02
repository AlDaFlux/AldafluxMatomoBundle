<?php

namespace Aldaflux\AldafluxMatomoBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\Collections\ArrayCollection;


use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;



class MatomoService  
{

    protected $parameter;
    
    protected $logs;
    protected $nbErrorLogs;
    
    protected $site;
    protected $tokenAuth;
    protected $chartBuilder;
    
    protected $siteId;

    public function __construct(ParameterBagInterface $parameterBag, ChartBuilderInterface $chartBuilder)
    {
        $this->nbErrorLogs = 0;
        $this->logs = array();
        $this->parameters = $parameterBag;
        $this->chartBuilder = $chartBuilder;
        $this->site= $this->parameters->Get("aldaflux_matomo.site");
        $this->tokenAuth= $this->parameters->Get("aldaflux_matomo.token_auth");
    } 
    
    
    
    public function getLogs() 
    {
        return($this->logs);
    }
    public function getNbLogs() 
    {
        return(count($this->logs));
    }
    
    public function getNbErrorLogs() 
    {
        return($this->nbErrorLogs);
    }
    
    public function getSiteId() 
    {
        return($this->siteId);
    }
    
    public function setSiteId($siteId) 
    {
        $this->siteId=$siteId;
    }
    
    
    public function getApi($args) 
    {
            
            $url = $this->site;
            $url .= "?module=API&";
            $url .= $args;
            $url .= "&format=json";
            $url .= "&token_auth=976b82996e8a81fbaf428002aa986b0b";
            $fetched = file_get_contents($url);
            $results=json_decode($fetched);
            return($results);
        
    }
    
    
    public function getChartsBarStats() 
    {
        $charts=array();
        $charts[]=$this->getChartBarStatsDay();
        $charts[]=$this->getChartBarStatsMonth();
        $charts[]=$this->getChartBarStatsYear();
        return($charts);
    }
    
    public function getChartBarStatsDay() 
    {
        $args="method=VisitsSummary.getVisits&idSite=".$this->siteId."&period=day&date=last30";
        return($this->getChartBar($args, $title="Nombre de visites quotidiennes"));
    }
    
    public function getChartBarStatsMonth() 
    {
        $args="method=VisitsSummary.getVisits&idSite=".$this->siteId."&period=month&date=last12";
        return($this->getChartBar($args, $title="Nombre de visites mensuelles"));
    }
    
    public function getChartBarStatsYear() 
    {
        $args="method=VisitsSummary.getVisits&idSite=".$this->siteId."&period=year&date=last2";
        return($this->getChartBar($args, $title="Nombre de visites annuelles"));
    }
    
    
    public function getChartBar($args, $title="Stats") 
    {
            $results=$this->getApi($args);
            $labels=array();
            $values=array();
            foreach ($results as $key =>  $result)
            {
                $labels[]=$key;
                $values[]=$result;
            }

            $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
            $chart->setData([
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $title,
                        'backgroundColor' => 'rgb(75, 111, 145)',
                        'data' => $values,
                    ],
                ],
            ]);
            
            return($chart);
        
    }
    
    
    
   
    
    
}