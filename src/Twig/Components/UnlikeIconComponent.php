<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('unlike_icon')]
final class UnlikeIconComponent
{
    public string $class;
}