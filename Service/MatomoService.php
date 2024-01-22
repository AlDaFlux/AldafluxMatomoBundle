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
    protected $siteIds;
    
    protected $backgroundColor;

    public function __construct(ParameterBagInterface $parameterBag, ChartBuilderInterface $chartBuilder)
    {
        $this->nbErrorLogs = 0;
        $this->logs = array();
        $this->siteIds = array();
        $this->parameters = $parameterBag;
        $this->chartBuilder = $chartBuilder;
        $this->site= $this->parameters->Get("aldaflux_matomo.site");
        $this->tokenAuth= $this->parameters->Get("aldaflux_matomo.token_auth");
        $this->backgroundColor= "rgb(75, 111, 145)";
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
    
    public function getSiteIds() 
    {
        return($this->siteIds);
    }
    
    public function setSiteIds($siteIds) 
    {
        $this->siteIds=$siteIds;
    }
    
    
    public function addSiteId($siteId,$title) 
    {
        $this->siteIds[$siteId]=$title;
    }
    
    
    public function getBackgroudColor() 
    {
        return($this->backgroundColor);
    }
    
    public function setBackgroundColor($backgroundColor) 
    {
        $this->backgroundColor=$backgroundColor;
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
    
    
    public function getChartBarMultiSiteStatsDay() 
    {
        $args="period=day&date=last30";
        return($this->getChartBarMultisite($args, $title="Nombre de visites quotidiennes"));
    }
    
    public function getChartBarMultiSiteStatsMonth() 
    {
        $args="period=month&date=last12";
        return($this->getChartBarMultisite($args, $title="Nombre de visites mensuelles"));
    }
    
    public function getChartBarMultiSiteStatsYear() 
    {
        $args="period=year&date=last2";
        return($this->getChartBarMultisite($args, $title="Nombre de visites annuelles"));
    }
    
    
    
    
    function defaultColors()
    {
        $graph_colors_default=['#e6194b', '#3cb44b', '#ffe119', '#0082c8', '#f58231', '#f58231', '#911eb4', '#46f0f0', '#f032e6', '#d2f53c', '#fabebe','#008080','#e6beff','#aa6e28','#06be5f','#7afe08', '#203216','#f0c2d6'];
        return($graph_colors_default);
    }
      
    public function getChartBarMultisite($period="period=day&date=last15") 
    {
          $colors=$this->defaultColors();
        $datasets=array();
        $i=0;
        foreach ($this->siteIds as $siteId=>$title)
        {
            $i++;
            $args="method=VisitsSummary.getVisits&idSite=".$siteId."&".$period;
            $data=$this->getApi($args);
             $datasets[]=[
                        'label' => $title,
                        'backgroundColor' => $colors[$i],
                        'data' => $data,
                    ];
        }
        foreach ($data as $key => $value)
        {
            $labels[]=$key;
        }
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
            $chart->setData([
                'labels' => $labels,
                'datasets' => $datasets,
            ]);
            
              $chart->setOptions([
                      'scales' => 
                            ['xAxes' => ['stacked' => true],
                            'yAxes' => ['stacked' => true]
                      ]]);      
              
            return($chart);
    }
    
    
    
    
    public function getChartBarMultisites() 
    {

      $charts = New ArrayCollection();
      $charts->add($this->getChartBarMultiSiteStatsDay());
      $charts->add($this->getChartBarMultiSiteStatsMonth());
      $charts->add($this->getChartBarMultiSiteStatsYear());
      return($charts);
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
                        'backgroundColor' => $this->getBackgroudColor(),
                        'data' => $values,
                    ] 
                ],
            ]);
            
              $chart->setOptions([
                      'scales' => [
                          'xAxes' => [
                              ['stacked' => true],
                          ],
                          'yAxes' => [
                              ['stacked' => true],
                          ],
                      ],
                  ]);      
              
            return($chart);
        
    }
    
    
    
   
    
    
}