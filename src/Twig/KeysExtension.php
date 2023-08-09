<?php

// src/Twig/KeysExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class KeysExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_keys', [$this, 'getKeys']),
        ];
    }

    public function getKeys($array)
    {
        return array_keys($array);
    }
}
