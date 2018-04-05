<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library;
use App\User;
use App\Subscription;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $libraries = Library::all();
        $subscriptions = Subscription::where('user_id', auth()->user()->id)->orderBy('created_at','desc')->paginate(10);
        return view('home')->with(['libraries' => $libraries, 'subscriptions' => $subscriptions]);
    }

    /**
    * Update the libraries of the given user
    */
    public function updateLibraries(Request $request)
    {
      $user = User::find(auth()->user()->id);
      $user->libraries()->sync($request->input('libraries'));
      $user->save();
      return redirect('/home')->with('success','Könyvtárak listája frissítve');
    }
}
