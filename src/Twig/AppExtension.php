<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Service\SchoolYearService;

class AppExtension extends AbstractExtension
{
    private $schoolYearService;

    public function __construct(SchoolYearService $service)
    {
        $this->schoolYearService = $service;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('filter_name', [$this, 'pluralize']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pluralize', [$this, 'pluralize']),
            new TwigFunction('get_env', [$this, 'getEnvironmentVariable']),
            new TwigFunction('years', [$this, 'years']),
            new TwigFunction('enabledYear', [$this, 'enabledYear']),
            new TwigFunction('sessionYearByCode', [$this, 'sessionYearByCode']),
            new TwigFunction('sessionYearById', [$this, 'sessionYearById']),

        ];
    }

    public function pluralize(int $count, string $sing, ?string $plu = null): string
    {
        $plu ??= $sing . 's' ;
        return $count == 1 ? "$count $sing"  :  "$count $plu" ;
    }

  

    public function years()
    {
        return $this->schoolYearService->years();
    }

    public function enabledYear($id)
    {
        return $this->schoolYearService->enabledYear($id);
    }

    public function sessionYearById()
    {
        return $this->schoolYearService->sessionYearById();
    }

    public function sessionYearByCode()
    {
        return $this->schoolYearService->sessionYearByCode();
    }



   
    
    /**
     * Return the value of the requested environment variable.
     * 
     * @param String $varname
     * @return String
     */
    public function getEnvironmentVariable($varname)
    {
        return $_ENV[$varname];
    }
}
