<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Salutation
{

    public function getSalutation(): string
    {
        $hour = (int) date('H'); // Récupère l'heure actuelle

        if ($hour >= 5 && $hour < 18) { // Entre 5 et 18 heures
            return 'Bonjour';
        } elseif ($hour >= 18 && $hour < 23) { // Entre 18 et 23 heures
            return 'Bonsoir';
        } else { // Sinon
            return 'Il est tard 😅';
        }
    }
}
