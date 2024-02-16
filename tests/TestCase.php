<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function loginAs(?bool $isAdmin = false): self
    {
        $user = $this->getUser(isAdmin: $isAdmin);
        return $this->actingAs($user);
    }

    public function getUser(bool $isAdmin): User
    {
        if ($isAdmin) {
            return User::factory()->admin()->create();
        }

        return User::factory()->create();
    }
}
