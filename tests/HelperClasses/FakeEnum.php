<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HelperClasses;

enum FakeEnum: string
{
    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}
