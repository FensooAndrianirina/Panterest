<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pluralize', [$this, 'pluralizing']),
        ];
    }

    public function pluralizing(int $count, string $singular, ?string $plural = null): string
    {


        // $plural = $plural ?? $singular . 's'; OU 
        $plural ??= $singular . 's';
        // Si le pluriel n'est pas null on le prend SINON le singulier auquel on aura concaténé un 's'

        $str = $count === 1 ? $singular : $plural;
        // Ternaire = si count est égal à 1 alors on prend le singulier SINON le pluriel

        return "$count $str";
    }
}
