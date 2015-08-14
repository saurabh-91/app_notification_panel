<?php
/**
 * Created by PhpStorm.
 * User: saurabh
 * Date: 14/8/15
 * Time: 12:44 PM
 */
require "predis/autoload.php";
include "constants.php"; // all constants used in this file is defined in file named "constants.php"
Predis\Autoloader::register();
class NotificationPanel
{

// ##############################################(check daily count )###################################################
    public function check_daily_limit($device_list_array)
    {
        $redis_client = $this->get_redis_client(REDIS_SCHEME, REDIS_HOST, REDIS_PORT, REDIS_DB);
        //$redis_client->del(REDIS_HASH);
        $final_device_list_array = array();
        foreach ($device_list_array as $temp_device_id)
        {
            $device_notification_count = intval($redis_client->hget(REDIS_HASH, $temp_device_id));
            if($device_notification_count<DAILY_NOTIFICATION_COUNT)
            {
                $redis_client->hset(REDIS_HASH, $temp_device_id, $device_notification_count+1);
                array_push($final_device_list_array,$temp_device_id);
            }
        }
        return $final_device_list_array;
    }
// #####################################################################################################################

// ########################################## get mongo client #########################################################
    public function get_mongo_client()
    {
        return new MongoClient(MONGO_HOST_PORT);
    }
// #####################################################################################################################

// ######################################### get redis client ##########################################################
    public function get_redis_client($redis_scheme, $redis_host, $redis_port, $redis_db)
    {
        $redis_client = new Predis\Client(array(
            "scheme" => $redis_scheme,
            "host"   => $redis_host,
            "port"   => $redis_port,
            "db"     => $redis_db));
        return $redis_client;

    }
// #####################################################################################################################


// #################################################### main function ##################################################
    public function get_device_list()
    {
        //get user input in pre-specified format as an array which contains two array [operational string, and operational data]
        // operational data contains Q1,Q2,Q3 and so on
        // each query Q contains product_name_array, associative array of event and its count
        $mongo_client  = $this->get_mongo_client();
        $db = $mongo_client->test; // mongo db database name = test
        $collection = $db->t1;  // collection name = t1
        $aggregation_array= array();
        // ###################################### (match) #########################################################
        $user_query_array = ['s6','s3','s4'];
        $query['$match'][PRODUCT_NAME]['$in'] =$user_query_array ;
        array_push($aggregation_array, $query);
        // #########################################(group by)#####################################################
        $groupby['$group']['_id']['did']    = '$device';
        // group by product name is only trigger if  a query having non zero count  of selected products
        $groupby['$group']['_id']['name']   = '$product name';
        // group by event name is only trigger if  a query having non zero  count of selected events
        $groupby['$group']['_id']['ename']  = '$event';
        $groupby['$group']['count']['$sum'] = 1;
        // ########################################################################################################
        array_push($aggregation_array,$groupby);
        // ###########################################(having)#####################################################
        $having_or_array= array();
        $temp['_id.ename']="detail page";
        //$temp['_id.name']['$in']=["s4","s6","s3"]; may be not require according to my architecture
        $temp['count']['$gte']=1;
        array_push($having_or_array,$temp);
        /*$temp =array();
        $temp['_id.ename']="app visit";
        $temp['_id.name']="s4";
        $temp['count']['$gte']=1;
        array_push($having_or_array,$temp);*/
        $having['$match']['$or']=$having_or_array;
        // ########################################################################################################
        array_push($aggregation_array,$having);
        $project['$project']['_id.did']=1;
        array_push($aggregation_array,$project);
        $group['$group']['_id']['device_id']='$_id.did';
        array_push($aggregation_array,$group);
        $cursor   = $collection->aggregate($aggregation_array);


        $device_list_array=array();
            foreach ($cursor['result'] as $document) {
                array_push($device_list_array,$document['_id']['device_id']);
            }
        //return $device_list_array;
        return $this->check_daily_limit($device_list_array); // send this list to user

    }
// #####################################################################################################################

}
$new = new NotificationPanel();
$device_list_array = $new->get_device_list();
var_dump($device_list_array);

?>