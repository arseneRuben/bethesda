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
        ];
    }

    public function pluralize(int $count, string $sing, ?string $plu = null): string
    {
        $plu ??= $sing . 's' ;
        return $count == 1 ? "$count $sing"  :  "$count $plu" ;
    }
}
