<?php
$m = new MongoClient();

// select a database
$db = $m->test;

// select a collection (analogous to a relational database's table)
$collection = $db->t1;
//var_dump($collection->find());
//var_dump($collection);
$final_array= array();
// ###################################### (match) #########################################################
$match_ini['$match']['product name']['$in']=['s6','s3','s4'];
array_push($final_array,$match_ini);
// #########################################(group by)#####################################################
$groupby['$group'][_id][did]='$device';
$groupby['$group'][_id][name]='$product name';
$groupby['$group'][_id][ename]='$event';
$groupby['$group'][count]['$sum']=1;
// ############################################################################################################
array_push($final_array,$groupby);
//  ##########################################(having)#########################################################
$having_or_array= array();
$temp['_id.ename']="detail page";
$temp['_id.name']="s4";
$temp[count]['$gte']=1;
array_push($having_or_array,$temp);
$temp =array();
$temp['_id.ename']="app visit";
$temp['_id.name']="s4";
$temp[count]['$gte']=1;
array_push($having_or_array,$temp);
$having['$match']['$or']=$having_or_array;
// ############################################################################################################
array_push($final_array,$having);
$project['$project']['_id.did']=1;
array_push($final_array,$project);
$group['$group'][_id][device_id]='$_id.did';
array_push($final_array,$group);
//$x=array({$match:{"product name":{$in:["s3","s4","s6"]}}},{$group:{_id:{did:"$device", name:"$product name",ename:"$event"},count:{"$sum":1}}},{$match:{$or:[{"_id.ename":"app visit",count:{$gt:1}},{"_id.ename":"detail page",count:{$gte:1},"_id.name":"s6"},{"_id.ename":"fav",count:{$gte:1}}]}},{$project:{"_id.did":1}},{$group:{_id:{deid:"$_id.did"}}});

//$cursor = $collection->aggregate($x);
//$cursor   = $collection->find(array("product name"=>"s4"));
$cursor   = $collection->aggregate($final_array);
//var_dump($cursor);
//exit();
/*if(!$cursor['result'])
{
    echo "yeh \n";

    echo "fuck you \n";
    exit();
}*/

//var_dump($cursor);
//echo "cur";
// iterate through the results
foreach ($cursor['result'] as $document) {
    echo $document['_id']['device_id'] . "\n";
    //var_dump($document);
    echo "new \n";
}

?>