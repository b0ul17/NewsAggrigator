<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require 'rb.php';

R::setup('mysql:host=localhost;dbname=newsDB',
    'username', 'password'); //for both mysql or mariaDB


require_once __DIR__ . '/Classes/XmlParser.php';

$sitesArray = [];
$sitesArray[0] = 'https://www.pickandroll.gr/category/eidiseis/feed/';
$sitesArray[1] = 'https://www.eurohoops.net/el/nba-news-el/feed/';
$sitesArray[2] = 'https://www.naftemporiki.gr/rssFeed?mode=tag&id=2514&atype=story';
$sitesArray[3] = 'https://www.sport24.gr/Basket/USA/NBA/?widget=rssfeed&view=feed';
//$sitesArray[3] = 'https://news.google.com/rss/topics/CAAqJggKIiBDQkFTRWdvSUwyMHZNRFp1ZEdvU0FtVnVHZ0pWVXlnQVAB/sections/CAQiQkNCQVNLd29JTDIwdk1EWnVkR29TQW1WdUdnSlZVeUlPQ0FRYUNnb0lMMjB2TURWcWRuZ3FCd29GRWdOT1FrRW9BQSoqCAAqJggKIiBDQkFTRWdvSUwyMHZNRFp1ZEdvU0FtVnVHZ0pWVXlnQVABUAE?hl=el&gl=GR&ceid=GR:el';
//$x = new \Helpers\XMLPARSER('https://www.pickandroll.gr/category/eidiseis/feed/');
//$x = new \Helpers\XMLPARSER('https://www.eurohoops.net/el/nba-news-el/feed/');
//$x = new \Helpers\XMLPARSER('https://www.fosonline.gr/basket/nva?format=feed&type=rss');
//$x = new \Helpers\XMLPARSER('https://rss.app/feeds/B7WHf5zOykj7A3Tf.xml');
//$x = new \Helpers\XMLPARSER('https://www.sport24.gr/Basket/USA/NBA/?widget=rssfeed&view=feed');
//$x = new \Helpers\XMLPARSER('https://rss.app/feeds/eP9yrQRrJ7fRxVvu.xml'); //on sports
//$x = new \Helpers\XMLPARSER('https://rss.app/feeds/xgZuPhJJqhomXGLr.xml'); //sdna


foreach ($sitesArray as $site) {
    $x = new \Helpers\XMLPARSER($site);


    $feedsData = $x->getFeeds();


//for each customer post create a new bean as a row/record


    foreach ($feedsData as $fd) {

       foreach ($fd->category as $xs) {

           $catExists = R::find( 'category', ' title LIKE ? ',[(string)$xs]);

           if(!$catExists){
               $cats = R::dispense('category');
               $cats->title =  (string)$xs;
               $cats->created_at = date("Y-m-d H:i:s");
               $cats->updated_at = date("Y-m-d H:i:s");

               R::store($cats);
           }


       }


        $exists = R::find( 'feeds', ' title LIKE ? ',[(string)$fd->title]);

        if($exists) break;

        $feeds = R::dispense('feeds');
        $feeds->title = (string)$fd->title;
        $feeds->link = (string)$fd->link;
        $feeds->description = (string)$fd->description;
        $feeds->image = (string)$fd->enclosure;
        $feeds->source = (string)$fd->source;
//        $feeds->category = (string)$fd->source;
        $feeds->pubDate = strtotime($fd->pubDate);
        $feeds->created_at = date("Y-m-d H:i:s");
        $feeds->updated_at = date("Y-m-d H:i:s");

        R::store($feeds);

    }
    $log = R::dispense('logs');
    $log->title = $site;
    $log->created_at = date("Y-m-d H:i:s");
    $log->updated_at = date("Y-m-d H:i:s");

    R::store($log);
}