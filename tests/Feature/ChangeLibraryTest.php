<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Library;

class ChangeLibraryTest extends TestCase
{
  use \Illuminate\Foundation\Testing\DatabaseMigrations;

    public function testNoDefaultLibrarySelected()
    {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
             ->visit('/')
             ->see($user->name)
             ->see('<option value="10">1033 Fő tér 5.</option>')
             ->dontSee('<option value="10" selected>1033 Fő tér 5.</option>');
    }
    public function testSelectAndSaveLibrary()
    {
        $user = factory(\App\User::class)->create();
        $lib = Library::all()->random();
        $this->actingAs($user)
             ->visit('/')
             ->select($lib->id, "libraries")
             ->press("Könyvtárak frissítése");
        $library_ids = [];
        foreach($user->libraries()->get() as $library)
          array_push($library_ids, $library->id);
        $this->assertContains($lib->id, $library_ids);
        $this->assertNotContains($lib->id+1, $library_ids);
      }
      public function testSelectedLibraryShowedAsSelected()
      {
         $user = factory(\App\User::class)->create();
         $lib = Library::all()->random();
         $user->libraries()->attach($lib->id);
         $this->actingAs($user)
             ->visit('/')
             ->see('<option value="' . $lib->id . '" selected>' . $lib->name . "</option>")
             ->dontSee('<option value="' . $lib->id . '">' . $lib->name . "</option>");
    }
}
