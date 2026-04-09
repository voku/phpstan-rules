<?php

namespace voku\PHPStan\Rules\Test\fixtures;

enum MatchTestMembershipLevel
{
    case Free;
    case Standard;
    case Premium;
    case PremiumPlus;

    public function isPaid(): bool
    {
        return match ($this) {
            self::Free => false,
            self::Standard, self::Premium, self::PremiumPlus => true,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free',
            self::Standard => 'Standard',
            self::Premium => 'Premium',
            self::PremiumPlus => 'Premium+',
        };
    }
}
