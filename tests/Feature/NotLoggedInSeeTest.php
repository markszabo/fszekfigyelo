<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotLoggedInSeeTest extends TestCase
{
    public function testNotLoggedInSee()
    {
        $this->visit('/')-> see('Fszek figyelő');
    }

    public function testRedirectToHome()
    {
      $this->get('/')->assertRedirectedTo('/home');
    }

    public function testHomeRedirectToLogin()
    {
      $this->get('/home')->assertRedirectedTo('/login');
    }
}
