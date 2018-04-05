<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use App\Subscription;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class SubscriptionsController extends Controller
{

    protected $client = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Goutte\Client $client)
    {
        $this->middleware('auth');
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return view('subscriptions.create');
        return view('subscriptions.search')->with('results', array());
    }

    public function search(Request $request)
    {
      $this->validate($request, [
        'text' => 'required',
        'index' => 'required'
      ]);
      //Do the search, parse the results
      $results = array();
      $url = "http://saman.fszek.hu/WebPac/CorvinaWeb?action=find&index0=" . urlencode($request->input('index')) . "&text0=" . urlencode($request->input('text')) . "&whichform=simplesearchpage&pagesize=1000";
      $crawler = $this->client->request('GET', $url);
      $crawler = $this->client->request('GET', $url);
      $crawler->filter('table')->eq(1)->filter('tr.short_item_block')->each(function ($node) use (&$results){
          $author = $node->filter('td')->eq(2)->text();
          $title = $node->filter('td')->eq(3)->text();
          $publishdate = $node->filter('td')->eq(4)->text();
          $type = $node->filter('td')->eq(5)->text();
          if(preg_match('/openLongOnclick\(\d*,(\d+),\'CorvinaWeb\'\)">Részletek/',$node->html(), $matches)) {
            $recnum = $matches[1];
            array_push($results, ['author' => $author, 'title' => $title, 'publishdate' => $publishdate, 'type' => $type, 'recnum' => $recnum]);
          } else {
            print "<pre>" . $node->html() . "</pre>";
          }
          //print $author . " : " . $title . " Date: " . $publishdate . " Type: " . $type . " Recnum: " . $recnum . "<br>";
      });
      return view('subscriptions.search')->with('results', $results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
          'title' => 'required',
          'recnum' => 'required|integer'
        ]);

        //Create post
        $subscription = new Subscription;
        $subscription->title = $request->input('title');
        $subscription->recnum = $request->input('recnum');
        $subscription->user_id = auth()->user()->id;
        $subscription->state_id = 1;
        $subscription->save();

        return redirect('/home')->with('success','Feliratkozás létrehozva');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subscription = Subscription::find($id);

        //Check for the correct user
        if(auth()->user()->id !== $subscription->user_id){
          return redirect('/home')->with('error','Unauthorized action');
        }

        if($subscription->state_id == 1) {
          $subscription->state_id = 2;
          $subscription->save();
          return redirect('/home')->with('success','"' . $subscription->title . '" figyelése felfüggesztve');
        } else {
          $subscription->state_id = 1;
          $subscription->save();
          return redirect('/home')->with('success','"' . $subscription->title . '" figyelése újraindítva');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $subscription = Subscription::find($id);

      //Check for the correct user
      if(auth()->user()->id !== $subscription->user_id){
        return redirect('/home')->with('error','Unauthorized action');
      }

      $subscription->delete();
      return redirect('/home')->with('success','"' . $subscription->title . '" figyelése törölve');
    }
}
