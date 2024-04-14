<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('like_icon')]
final class LikeIconComponent
{
    public string $class;
}
