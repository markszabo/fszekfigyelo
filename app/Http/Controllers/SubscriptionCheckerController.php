<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use App\Subscription;
use App\User;

class SubscriptionCheckerController extends Controller
{
  protected $client = null;

  public function __construct(\Goutte\Client $client)
  {
      $this->client = $client;
  }

  public function index() {
    $returntext = "";
    foreach(User::all() as $user) {
      $sendmail = false;
      $mail_center = "";
      $subscriptions = Subscription::where('user_id', $user->id)->where('state_id', '1')->get();
      foreach($subscriptions as $subscription) {
        $libraries = $this->object2array($subscription->user->libraries, 'name');
        $url = env("FSZEK_URL") . "WebPac/CorvinaWeb?action=onelong&showtype=longlong&recnum=" . urlencode($subscription->recnum) . "&pos=1&showtype=long&showtype=cedula&showtype=marc&showtype=hunmarc&showtype=longlong";
        $returntext .= $url . "<br>";
        $crawler = $this->client->request('GET', $url);
        $crawler = $this->client->request('GET', $url);
        $crawler->filter('table .display tbody tr')->each(function ($node) use (&$subscription, $libraries, &$mail_center, &$sendmail, $url, &$returntext) {
            $library = $node->filter('td')->eq(1)->text();
            $status = $node->filter('td')->eq(7)->text();
            $returntext .= $library . " -> " . $status . "<br>";
            if($status == "Kölcsönözhető" && in_array($library, $libraries)) {
              $returntext .= "Találat!<br>";
              $sendmail = true;
              $mail_center .= $subscription->title . " (Lelőhely: " . $library . ") <a href='" . $url . "' target='_blank'>Ellenőrzés</a><br>";
              $subscription->state_id = 2;
              $subscription->save();
            }
        });
      }
      if($sendmail) {
        $returntext .= "Sending mail:<br>";
        $mail_text = "<!DOCTYPE html>\n<body>Szia " . $user->name . ",<br><br>Az általad figyelt könyvek közül a következő(k) elérhető(ek) lett(ek):<br><br>" . $mail_center . "<br><br>Üdv,<br>Fszek Figyelő</body></html>";
        $subject = "A figyelt könyv elérhető";
        mb_internal_encoding('UTF-8');
        $encoded_subject = mb_encode_mimeheader("Subject: $subject", 'UTF-8');
        $encoded_subject = substr($encoded_subject, strlen('Subject: '));
        if(env('APP_ENV') === "production")
          mail($user->email,$encoded_subject,$mail_text,"From: fszekfigyelo@szabo-simon.hu\r\nBCC: torin42@gmail.com\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n");
        $returntext .= "A mail would have been sent with the following content:\n\n" . $mail_text;
      }
    }
    if(env('APP_ENV') === "production")
      return "Success!";
    else
      return $returntext;
    //return view('pages.index')->with('title', $title);
  }

  private function object2array($object, $property) {
    $ret = array();
    foreach($object as $o) {
      /*$str = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
          return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
      }, html_entity_decode($o[$property]));*/
      array_push($ret,$o[$property]);
    }
    return $ret;
  }
}
