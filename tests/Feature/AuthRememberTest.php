<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthRememberTest extends TestCase
{
    public function test_login_uses_persistent_remember_cookie_when_requested(): void
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'user@example.com',
                'password' => 'secret',
                'active' => 1,
            ], true)
            ->andReturn(false);

        $this->post(route('login.process'), [
            'email' => 'user@example.com',
            'password' => 'secret',
            'remember' => '1',
        ])->assertSessionHasErrors('email');
    }

    public function test_login_does_not_remember_by_default(): void
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'user@example.com',
                'password' => 'secret',
                'active' => 1,
            ], false)
            ->andReturn(false);

        $this->post(route('login.process'), [
            'email' => 'user@example.com',
            'password' => 'secret',
        ])->assertSessionHasErrors('email');
    }
}
