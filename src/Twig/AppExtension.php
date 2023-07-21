<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
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
        ];
    }

    public function pluralize(int $count, string $sing, ?string $plu = null): string
    {
        $plu ??= $sing . 's' ;
        return $count == 1 ? "$count $sing"  :  "$count $plu" ;
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
