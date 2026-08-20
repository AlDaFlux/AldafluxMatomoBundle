# AldafluxMatomoBundle

Ce bundle Symfony permet d'integrer facilement les statistiques de Matomo sous forme de graphiques generes avec **Symfony UX Chart.js**.

## Prerequis

- PHP >= 8.1
- Symfony >= 7.1
- Symfony UX Chart.js

## Installation

Ajoutez le depot dans votre fichier composer.json et installez le bundle :

 ```bash
composer require aldaflux/matomo-bundle
```
*(Si le bundle n'est pas configure avec Flex, ajoutez `Aldaflux\AldafluxMatomoBundle\AldafluxMatomoBundle` a la liste des bundles dans `config/bundles.php`)*

## Configuration

Creez le fichier de configuration `config/packages/aldaflux_matomo.yaml` :

```yaml
aldaflux_matomo*
    default:
        site: 'https://votre-instance-matomo.org/'
        token_auth: 'VOTRE_TOKEN_D_AUTHENTIFICATION_MATOMO'
```

## Utilisation

Le bundle expose le service `Aldaflux\AldafluxMatomoBundle\Service\MatomoService`. 

### Initialisation dans un controleur ou un composant

```php
use Aldaflux\AldafluxMatomoBundle\Service\MatomoService;

class MonController
{
    public function index(MatomoService $matomoService)
    {
        // Definir IT du`site pour lequel recuperer les statistiques
        $matomoService->setSiteId(12);

        // Recuperer un graphique de visites quotidiennes (renvoie un obiet Chart)
        $chart = $matomoService->getChartBarStatsDay();

        return $this->render('stats.html.twig', [
            'chart' => $chart,
        ]);    }
}
```

### Affichage dans le template Twig

Dans votre template Twig, utilisez la fonction fournie par Symfony UX Chart.js :

```twig
<div class="chart-container">
    { render_chart(chart) }}
</div>

```

---

### Gestion Multi-sites

Vous pouvez egalement cumuler et afficher les graphiques de plusieurs sites Matomo :

```php
// Ajouter des sites a suivre
$matomoService->addSiteId(1, 'Site Principal');
$matomoService->addSiteId(2, 'Blog');

// Recuperer la collection des graphiques (Day, Month, Year) regroupant ces sites
$charts = $matomoService->getChartBarMultisites();
```

Dans Twig :

```twig
{% for chart in charts %}
    {{ render_chart(chart) }}
{% endfor %}
```

## Fonctionnalites de MatomoService

Le service fournit plusieurs methodes pour obtenir des graphiques :

- `getChartBarStatsDay()` : Graphique des visites quotidiennes (30 derniers jours).
- `getChartBarStatsMonth()` : Graphique des visites mensuelles (12 derniers mois).
- `getChartBarStatsYear()` : Graphique des visites annuelles (5 dernieres annees).
- `getChartBarMultiSiteStatsDay() : Graphique multi-sites quotidien.
- `getChartBarMultiSiteStatsMonth()` : Graphique multi-sites mensuel.
- `getChartBarMultiSiteStatsYear() : Graphique multi-sites annuel.
- `getChartBarMultisites() : Retourne une `ArrayCollection` contenant les trois graphiques multi-sites (Day, Month, Year).

## Profiler / Web Debug Toolbar

Le bundle integre un collecteur de donnees pour la barre d'outils de deboggage de Symfony. Il permet de voir les appels a l'API Matomo lors du chargement de la page.
