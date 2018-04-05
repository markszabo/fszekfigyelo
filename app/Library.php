<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
  /**
   * The users that has selected this library
   */
  public function users()
  {
      return $this->belongsToMany('App\User');
  }
}
