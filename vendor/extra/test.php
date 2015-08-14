<?php
require "predis/autoload.php";
//use \Predis;
Predis\Autoloader::register();

 require 'vendor/autoload.php';
// since we connect to default setting localhost
// and 6379 port there is no need for extra
// configuration. If not then you can specify the
// scheme, host and port to connect as an array
// to the constructor.
echo strtolower("Hello");
/*


$string = "{'general_feature': {'': '', u'Model Name': u'2012D Flip Phone', u'SIM Type': u'Dual Sim', u'Brand': u'Alcatel', u'Handset Color': u'Gold', u'Model ID': u'2012D'}, 'name': u'Alcatel 2012D Flip Phone (Gold)', 'title': u'Alcatel 2012D Flip Phone Price in India - Buy Alcatel 2012D Flip Phone Gold 16 Online - Alcatel', 'price': 3499, 'i_link': u'http://img6a.flixcart.com/image/mobile/3/p/n/alcatel-2012d-flip-phone-2012d-1100x1100-imae92gzqptggzfg.jpeg', 'link': u'https://flipkart.com/alcatel-2012d-flip-phone/p/itme92nv37yyafhr?pid=MOBE93RXGMYTN3PN&al=H6nbOQa%2FK2k9I%2BZlNjJhW8ldugMWZuE7Qdj0IGOOVqvmc0A4lP6lRsf08Z4KdrP6AUFg1HT2dyQ%3D&ref=L%3A-5334139782539883126&srno=b_1113', 'brand': u'Alcatel'}";
$string=substr($string,28);
//$string=substr($string,-3);

$array = explode(',',$string);
//var_dump( $array);
$new_array = array();
//$value=explode(' : ',$array);
//die(3);
//var_dump($value);

foreach ($array as &$value)
{
	$v=explode(': ',$value);
	//var_dump($v);
	//die(5);
	
	$v[0]=substr($v[0],2);
	echo $v[0];
	$new_array[$v[0]]=$v[1];
}*/
//echo $new_array['price'];
//var_dump($new_array);
//$nums = explode(':',$array);
  //  $new_array[$nums[0]] = $nums[1];
//$array = explode(':',$array);
//var_dump($array);

/*array_walk($array,'walk', &$new_array);

function walk($val, $key, $new_array){
    $nums = explode(':',$val);
    $new_array[$nums[0]] = $nums[1];
}
print_r($new_array);


*/
?>



















try {
	$client = new Elasticsearch\Client();
echo "string";
}
catch(Exception $e){
	echo $e->getMessage();
}
try {
    $redis = new Predis\Client();
/*
    $redis = new PredisClient(array(
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => 6379));
*/
    echo "Successfully connected to Redis";
}
catch (Exception $e) {
    echo "Couldn't connected to Redis";
    echo $e->getMessage();
}