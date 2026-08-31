<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_user_model_implements_jwt_subject(): void
    {
        $this->assertTrue(is_a(User::class, JWTSubject::class, true));
    }
}
