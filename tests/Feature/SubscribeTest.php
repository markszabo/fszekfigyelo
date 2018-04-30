<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use \App\User;
use \App\Subscription;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

class SubscribeTest extends TestCase
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

    public function testSubscribeMartian()
    {
      $this->http->mock
          ->when()
              ->methodIs('GET')
              ->pathIs($this->http->matches->regex('/WebPac\/CorvinaWeb\?.*(Andy.Weir|AUTH).*(Andy.Weir|AUTH)/'))
          ->then()
              ->body(file_get_contents(dirname(__FILE__) . "/mocked_websites/Andy_Weir_search_results.txt"))
          ->end();
      $this->http->setUp();

      $user = factory(\App\User::class)->create();
      $this->actingAs($user)
           ->visit('/subscriptions/create')
           ->type('Andy Weir', 'text')
           ->select('AUTH','index')
           ->press('Keresés')
           ->see('The Martian')
           ->see('Figyelés')
           ->press('1269697') #id of the Feliratkozás button of The Martian
           ->see('The Martian')
           ->see('Figyelés folyamatban')
           ->dontSee('Még nincsen feliratkozásod.');

      //These checks the same just different way to preserv the intended data structure
      $this->assertEquals('1269697', $user->subscriptions->first()->recnum);
      $this->assertEquals('1269697', Subscription::where('user_id', auth()->user()->id)->first()->recnum);
    }

    public function testSubscriptionsDisplayed()
    {
      $user = factory(\App\User::class)->create();
      $subscription = factory(\App\Subscription::class)->create();
      $subscription->user_id = $user->id;
      $subscription->save();
      $this->actingAs($user)
           ->visit('/home')
           ->see($subscription->title)
           ->see($subscription->recnum)
           ->dontSee('Még nincsen feliratkozásod.');
    }

    public function testChangeSubscribtionStateToSuspended()
    {
      $user = factory(\App\User::class)->create();
      $subscription = factory(\App\Subscription::class)->create();
      $subscription->user_id = $user->id;
      $subscription->save();
      $this->assertEquals(1,$subscription->state_id);
      $this->actingAs($user)
           ->visit('/home')
           ->see($subscription->title)
           ->see($subscription->recnum)
           ->dontSee('Még nincsen feliratkozásod.')
           ->see('Figyelés folyamatban')
           ->dontSee('Figyelés felfüggesztve')
           ->click('Felfüggesztés')
           ->see('"' . $subscription->title . '" figyelése felfüggesztve')
           ->dontSee('Figyelés folyamatban')
           ->see('Figyelés felfüggesztve');
    }

    public function testChangeSubscribtionStateToActive()
    {
      $user = factory(\App\User::class)->create();
      $subscription = factory(\App\Subscription::class)->create();
      $subscription->user_id = $user->id;
      $subscription->state_id = 2; //suspended
      $subscription->save();
      $this->assertEquals(2,$subscription->state_id);
      $this->actingAs($user)
           ->visit('/home')
           ->see($subscription->title)
           ->see($subscription->recnum)
           ->dontSee('Még nincsen feliratkozásod.')
           ->dontSee('Figyelés folyamatban')
           ->see('Figyelés felfüggesztve')
           ->click('Újraindítás')
           ->see('"' . $subscription->title . '" figyelése újraindítva')
           ->see('Figyelés folyamatban')
           ->dontSee('Figyelés felfüggesztve');
    }

    public function testRemoveSubscription()
    {
      $user = factory(\App\User::class)->create();
      $subscription = factory(\App\Subscription::class)->create();
      $subscription->user_id = $user->id;
      $subscription->save();
      $this->actingAs($user)
           ->visit('/home')
           ->see($subscription->title)
           ->see($subscription->recnum)
           ->dontSee('Még nincsen feliratkozásod.')
           ->press("Törlés") //the id of the delete button
           ->see('"' . $subscription->title . '" figyelése törölve')
           ->see('Még nincsen feliratkozásod.')
           ->dontSee('Figyelés folyamatban')
           ->dontSee('Figyelés felfüggesztve');
    }

    public function testMultipleSubscriptions()
    {
      $user = factory(\App\User::class)->create();
      $subscription1 = factory(\App\Subscription::class)->create();
      $subscription1->user_id = $user->id;
      $subscription1->save();
      $subscription2 = factory(\App\Subscription::class)->create();
      $subscription2->user_id = $user->id;
      $subscription2->save();
      $this->actingAs($user)
           ->visit('/home')
           ->see($subscription1->title)
           ->see($subscription1->recnum)
           ->see($subscription2->title)
           ->see($subscription2->recnum)
           ->dontSee('Még nincsen feliratkozásod.')
           ->see('Figyelés folyamatban')
           ->dontSee('Figyelés felfüggesztve')
           //suspend one of them
           ->click('Felfüggesztés')
           ->see('Figyelés folyamatban')
           ->see('Figyelés felfüggesztve')
           //suspend the other one
           ->click('Felfüggesztés')
           ->dontSee('Figyelés folyamatban')
           ->see('Figyelés felfüggesztve')
           //delete the first one
           ->press((string)$subscription1->id) //the id of the delete button
           ->see('"' . $subscription1->title . '" figyelése törölve')
           ->dontSee('Még nincsen feliratkozásod.')
           ->dontSee($subscription1->recnum)
           ->see($subscription2->title)
           ->see($subscription2->recnum)
           ->dontSee('Figyelés folyamatban')
           ->see('Figyelés felfüggesztve')
           //restart the second one
           ->click('Újraindítás')
           ->see('"' . $subscription2->title . '" figyelése újraindítva')
           ->see('Figyelés folyamatban')
           ->dontSee('Figyelés felfüggesztve')
           //remove the second one
           ->press("Törlés")
           ->see('"' . $subscription2->title . '" figyelése törölve')
           ->see('Még nincsen feliratkozásod.')
           ->dontSee('Figyelés folyamatban')
           ->dontSee('Figyelés felfüggesztve');
    }
}
