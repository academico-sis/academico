<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

trait UserAttributesTrait
{
    public function firstname(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? Str::title($this->user->firstname) : '',
            set: fn ($value) => $this->user->update(['firstname' => $value]),
        );
    }

    public function lastname(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? Str::title($this->user->lastname) : '',
            set: fn ($value) => $this->user->update(['lastname' => $value]),
        );
    }

    public function email(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this?->user?->email,
            set: fn ($value) => $this->user->update(['email' => $value]),
        );
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? "{$this->firstname} {$this->lastname}" : '',
            set: fn ($value) => $value * 100,
        );
    }
}
