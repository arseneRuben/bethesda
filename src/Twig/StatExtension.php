<?php

namespace App\Twig;

use App\Service\StatistiquesService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatExtension extends AbstractExtension
{
    private $statService;

    public function __construct(StatistiquesService $statService)
    {
        $this->statService = $statService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('teachers_function', [$this, 'teachers']),
        ];
    }

    public function teachers()
    {
        return $this->statService->teachers();
    }
}
