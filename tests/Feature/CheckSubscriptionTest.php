<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use \App\User;
use \App\Subscription;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

class CheckSubscriptionTest extends TestCase
{
  use \Illuminate\Foundation\Testing\DatabaseMigrations;
  use HttpMockTrait;

  public static function setUpBeforeClass()
  {
      static::setUpHttpMockBeforeClass('8082', 'localhost');
  }

  public static function tearDownAfterClass()
  {
      static::tearDownHttpMockAfterClass();
  }

  public function setUp()
  {
      parent::setUp();
      $this->setUpHttpMock();
  }

  public function tearDown()
  {
      parent::tearDown();
      $this->tearDownHttpMock();
  }

  public function testMockWebservice() //test if webservice mocking properly works
  {
      $this->http->mock
          ->when()
              ->methodIs('GET')
              ->pathIs('/foo')
          ->then()
              ->body('mocked body')
          ->end();
      $this->http->setUp();

      $this->assertSame('mocked body', file_get_contents(env("FSZEK_URL") . 'foo'));
  }

  public function testSubscribeCheck_NotAvailable()
  {
    $this->http->mock
        ->when()
            ->methodIs('GET')
            ->pathIs($this->http->matches->regex('/WebPac\/CorvinaWeb\?.*1269697/'))
        ->then()
            ->body(file_get_contents(dirname(__FILE__) . "/mocked_websites/Martian_check.txt"))
        ->end();
    $this->http->setUp();

    $user = factory(\App\User::class)->create();
    $subscription = new Subscription();
    $subscription->user_id = $user->id;
    $subscription->recnum = 1269697;
    $subscription->title = "The Martian";
    $subscription->state_id = 1;
    $subscription->save();

    $user->libraries()->attach(1); //Központi Könyvtár (KK)
    $user->libraries()->attach(45); //1203 Bíró M. u.7.

    $this->actingAs($user)
         ->visit('/checkSubscriptions')
         ->see('1211 II. Rákóczi F. u. 106. -> Kölcsönözhető')
         ->see('1203 Bíró M. u. 7. -> Dobozban')
         ->dontSee('Találat!')
         ->dontSee('Sending mail');
  }

  public function testSubscribeCheck_Available()
  {
    $this->http->mock
        ->when()
            ->methodIs('GET')
            ->pathIs($this->http->matches->regex('/WebPac\/CorvinaWeb\?.*654064/'))
        ->then()
            ->body(file_get_contents(dirname(__FILE__) . "/mocked_websites/Hobbit_check.txt"))
        ->end();
    $this->http->setUp();

    $user = factory(\App\User::class)->create();
    $subscription = new Subscription();
    $subscription->user_id = $user->id;
    $subscription->recnum = 654064;
    $subscription->title = "A hobbit";
    $subscription->state_id = 1;
    $subscription->save();

    $user->libraries()->attach(1); //Központi Könyvtár (KK)
    $user->libraries()->attach(4); //KK Sárkányos Gyerekkönyvtár

    $this->actingAs($user)
         ->visit('/checkSubscriptions')
         ->see('KK Sárkányos Gyerekkönyvtár -> Kölcsönözhető')
         ->see('Találat!')
         ->see('Sending mail');

    //Test if this subscription was suspended
    $this->assertEquals('2',$user->subscriptions()->first()->state_id);
  }

}
