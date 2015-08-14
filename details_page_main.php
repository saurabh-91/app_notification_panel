<?php
require "predis/autoload.php";
include "constants.php"; // all constants used in this file is defined in file named "constants.php"
Predis\Autoloader::register();

// ####################### common util class (it contains function which is used bt both class) ########################
class CommonUtil  // this class will used some of the function of CommonUtil class
{

//  ##################################### get full feature details (full spec) #########################################
    public function  get_details_of_product($product_details) // this function clean full spec and store into associative array
    {
        $detail = $product_details[FEATURE];
        $detail = str_replace("u'", "", $detail);
        $detail = str_replace("'", "", $detail);
        $detail = str_replace(": ,", "", $detail);
        $detail = str_replace("http://", "", $detail);
        $detail = str_replace("https://", "", $detail);
        $find_index = strpos($detail,"},");
        $product_details['Warranty:']=substr($detail,12,$find_index-12);
        $detail = str_replace("},", " {", $detail);
        $detail = str_replace("}", "", $detail);
        $detail=explode(" { ",$detail);
        for ($i=0;$i<count($detail);$i=$i+2)
        {
            $product_details[$detail[$i]]=$detail[$i+1];
        }
        return $product_details;
    }
//  ####################################################################################################################

}
// #####################################################################################################################

// ###################################### redis class for getting result from redis ####################################
class RedisResult extends CommonUtil
{
//  ##################################### get_redis_client #############################################################
    public function get_redis_client($redis_scheme, $redis_host, $redis_port, $redis_db)
    {
        $redis_client = new Predis\Client(array(
            "scheme" => $redis_scheme,
            "host"   => $redis_host,
            "port"   => $redis_port,
            "db"     => $redis_db));
        return $redis_client;

    }
//  ####################################################################################################################

//  #################################### main function (execution start from here) #####################################
    public function get_detail_from_redis($product_id)
    {
        $redis = $this->get_redis_client(REDIS_SCHEME, REDIS_HOST, REDIS_PORT, REDIS_DB);
        try      // try to get the details from redis
        {
            $product_details = $redis->hgetall($product_id);
            return $this->get_details_of_product($product_details);
        }
        catch (Exception $e)  // if redis server is failed than try to get the data from mysql
        {
            echo "Couldn't connected to Redis";
            echo $e->getMessage();
            // get data from mysql db
            // create a object of MysqlResult class
            $mysql_result_object= new MysqlResult();
            return $mysql_result_object->get_detail_from_mysql($product_id);
        }
    }
//  ####################################################################################################################
}
//  ####################################### end of redis class #########################################################



// ####################################### mysql class for getting result from mysql ###################################
class MysqlResult extends CommonUtil  // this class will used some of the function of CommonUtil class
{

//  ##################################### get_mysql_client #############################################################
    public function get_mysql_client($db_host, $db_user, $db_pass)
    {
       return mysql_connect($db_host, $db_user, $db_pass);
    }
//  ####################################################################################################################

//  ########################################   get details from mysql ##################################################
    public function get_detail_from_mysql($product_id)
    {
        $db           = MYSQL_DB;
        $db_table     = MYSQL_DB_TABLE;
        $mysql_client = $this->get_mysql_client(MYSQL_DB_SERVER, MYSQL_DB_USER, MYSQL_DB_PASS);
        if(! $mysql_client )
        {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($db);
        $make_query   = "SELECT * FROM $db_table where index_id = '$product_id'";
        $fetch_details =  mysql_query($make_query);
        while ($row = mysql_fetch_array($fetch_details))
        {
            $key_name = $row[KEY_NAME_COLUMN];
            $key_value = $row[KEY_VALUE_COLUMN];
            $product_details[$key_name]=$key_value;
        }
        return $this->get_details_of_product($product_details); // this function is defined in RedisResult parent class
    }
//  ####################################################################################################################
}
?>
