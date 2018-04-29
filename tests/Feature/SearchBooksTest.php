<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use \App\User;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

class ManageSubscriptionTest extends TestCase
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
    public function testSubscriptionSeachAvailable()
    {
      $user = factory(\App\User::class)->create();
      $this->actingAs($user)
           ->visit('/')
           ->click('Új feliratkozás')
           ->seePageIs('/subscriptions/create')
           ->see('Keresés')
           ->see('Keresőkifejezés');
    }

    public function testSearch_title_gyuruk_ura()
    {
      $this->http->mock
          ->when()
              ->methodIs('GET')
              ->pathIs($this->http->matches->regex('/WebPac\/CorvinaWeb\?.*' . urlencode("gyűrűk.ura") . '/'))
          ->then()
              ->body(file_get_contents(dirname(__FILE__) . "/mocked_websites/gyuruk_ura_search_results.txt"))
          ->end();
      $this->http->setUp();

      $user = factory(\App\User::class)->create();
      $this->actingAs($user)
           ->visit('/subscriptions/create')
           ->type('gyűrűk ura', 'text')
           ->press('Keresés')
           ->see('J. R. R. Tolkien')
           ->see('Figyelés')
           ->dontSee('<pre>');
    }

    public function testSearch_author()
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
           ->dontSee('<pre>');
    }

    public function testSearch_title_harry_potter()
    {
      $this->http->mock
          ->when()
              ->methodIs('GET')
              ->pathIs($this->http->matches->regex('/WebPac\/CorvinaWeb\?.*harry.potter/'))
          ->then()
              ->body(file_get_contents(dirname(__FILE__) . "/mocked_websites/harry_potter_search_results.txt"))
          ->end();
      $this->http->setUp();

      $user = factory(\App\User::class)->create();
      $this->actingAs($user)
           ->visit('/subscriptions/create')
           ->type('harry potter', 'text')
           ->press('Keresés')
           ->see('Harry Potter and the Order of the Phoenix')
           ->see('Figyelés')
           ->dontSee('<pre>');
    }

    public function testSearch_no_result()
    {
      $this->http->mock
          ->when()
              ->methodIs('GET')
              ->pathIs($this->http->matches->regex('/WebPac\/CorvinaWeb\?.*this.does.not.exist/'))
          ->then()
              ->body(file_get_contents(dirname(__FILE__) . "/mocked_websites/no_result_search_results.txt"))
          ->end();
      $this->http->setUp();

      $user = factory(\App\User::class)->create();
      $this->actingAs($user)
           ->visit('/subscriptions/create')
           ->type('this does not exist', 'text')
           ->press('Keresés')
           ->see('Nincs találat, próbáld újra')
           ->dontSee('Figyelés')
           ->dontSee('<pre>');
    }

    public function testSearch_publisher()
    {
      $this->http->mock
          ->when()
              ->methodIs('GET')
              ->pathIs($this->http->matches->regex('/WebPac\/CorvinaWeb\?.*libri/'))
          ->then()
              ->body(file_get_contents(dirname(__FILE__) . "/mocked_websites/libri_search_results.txt"))
          ->end();
      $this->http->setUp();

      $user = factory(\App\User::class)->create();
      $this->actingAs($user)
           ->visit('/subscriptions/create')
           ->type('libri', 'text')
           ->select('PUBL','index')
           ->press('Keresés')
           ->see('Zöldségtermesztés')
           ->see('Figyelés')
           ->dontSee('<pre>');
    }
}
