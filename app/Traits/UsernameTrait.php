<?php

namespace App\Traits;

trait UsernameTrait
{
    public function generateUsername(string $fullName): string
    {
        $username_parts = array_filter(explode(' ', strtolower($fullName)));
        $username_parts = array_slice($username_parts, -2);

        $part1 = (! empty($username_parts[0])) ? substr($username_parts[0], 0, 3) : '';
        $part2 = (! empty($username_parts[1])) ? substr($username_parts[1], 0, 8) : '';
        $part3 = random_int(999, 9999);

        return $part1.$part2.$part3;
    }
}
