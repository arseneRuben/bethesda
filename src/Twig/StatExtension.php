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
            new TwigFunction('teachers_count', [$this, 'teachers']),
            new TwigFunction('students_count', [$this, 'students']),
            new TwigFunction('rooms_count', [$this, 'rooms']),
            new TwigFunction('insolvents', [$this, 'insolvents']),
        ];
    }

    public function teachers()
    {
        return $this->statService->teachers();
    }
    public function students()
    {
        return $this->statService->students();
    }
    public function rooms()
    {
        return $this->statService->rooms();
    }
    public function insolvents()
    {
        return $this->statService->insolvents();
    }
}
