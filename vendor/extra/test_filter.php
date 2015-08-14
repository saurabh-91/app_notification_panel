<?php
require 'vendor/autoload.php';
$searchParams = array();
        $searchParams['index'] = "flipkart";
        $searchParams['type']  = "mobile";
        /*$searchParams['body']['query']['bool']['must']['match'] = array
                                                        (
                                                            //array('match' => array('name' => $ini_query)),
                                                            //array('match' => array('brand' =>$bname)),
                                                            ['name']=>$ini_query,
                                                            ['brand']=>$bname,
                                                        );*/
        $query=array();
        $query['term']['brand']="apple";
        $filter=array();
        $filter['range']['price']['gte']=45000;
        //$filter['range']['price']['gte']=1000;
        //$filter['range']['price']['lte']=5000;

        $searchParams['body']['query']['filtered']=array(
            "query"=>$query,"filter"=>$filter
            );
        $searchParams['body']['size']=2000;
         $client = new Elasticsearch\Client(); // create a elastcsearch client
        $retDoc = $client->search($searchParams);
        var_dump($retDoc);


?>