<?php
/**
* 
*/
include("constants.php");
require 'vendor/autoload.php';

class SearchElastic 
{
	
	public $ini_query = "";// initial search query which will be maintained after applying filter
	public $ini_brand_size=0; // no of brands listed in user query
// ######################################### get the elasticsearch index params #############################################################
	public function get_es_index_params($index, $type, $size)
   {
   		$searchParams = array();
        $searchParams['index'] = $index;
        $searchParams['type']  = $type;
        $searchParams['size'] = $size;
        return $searchParams;
   }

// ###########################################################################################################################################



// ######################################### only this function depends on user input ########################################################	 
    public function check_operation()//depent on user input which opretion to perform
    {
    	global $ini_query;
    	global $ini_brand_size;
    	if (isset($_POST["search"]))
    	{ 	//call search function;
    		$user_query_string =  $_POST["search"];
    		$ret_array         =  $this->user_query_search($user_query_string);
    	}
	    else
	    {	//call filtered search function
	    	$ini_brand_size		  =  $_POST['initial_size_of_brand'];
	    	$ini_query            =  $_POST['initial_query'];
	    	$selected_brand_name  =  $_POST['brand_name'];
	    	$selected_price_range =  $_POST['price_range'];
	    	$selected_os_name	  =  $_POST['os_name'];
	    	$ret_array            =  $this->filtered_query($selected_brand_name, $ini_query, $selected_price_range, $ini_brand_size);
	    }
	    return $ret_array;
    }
// ###########################################################################################################################################

// ######################################### user query search function ###################################################################### 
    public function user_query_search($user_query_string)
    {
    	global $ini_query;
    	global $ini_brand_size;
    	$user_query_string=trim($user_query_string);
    	$ini_query = $user_query_string;
        $searchParams=$this->get_es_index_params(INDEX, TYPE, SIZE);
        if($user_query_string) // check condition for null search query
        {
        	$searchParams['body']['query']['match']['title'] = $user_query_string;
        }
        else
        {
        	$searchParams['body']['query']['match_all'] = new StdClass();
        }
        $client = new Elasticsearch\Client(); 
        $retDoc = $client->search($searchParams);//echo json_encode($retDoc);
        $retDoc = $retDoc[hits][hits];
        $brand_list=array();
        for ($i=0; $i < 10; $i = $i+1)
        {
            $table_array[$i] = $retDoc[$i];
        }
        for ($i = 0; $i < count($retDoc); $i = $i+1)
        {    
            $temp  = $retDoc[$i][_source][brand];
            $temp1 = $retDoc[$i][_source][price];
            if(!array_key_exists($temp, $brand_list_count))
            {
            	array_push($brand_list, $temp);
                $brand_list_count[$temp] = 0;
            }
            // switch case  for hardcoded price  range count
            switch ($temp1) 
            {
                case ($temp1 < 10000):
                    $price_range[0]+= 1;
                    break;
                case ($temp1<20000&&$temp1>=10000):
                    $price_range[1]+= 1;
                    break;
                case ($temp1<35000&&$temp1>=20000):
                    $price_range[2]+= 1;
                    break;
                case ($temp1<50000&&$temp1>=35000):
                    $price_range[3]+= 1;
                    break;
                case ($temp1>=50000):
                    $price_range[4]+= 1;
                    break;
                default:
                    $price_range[5]+= 1;
                    break;
            }
            // end of switch
            $brand_list_count[$temp]+= 1;
        }
        $ini_brand_size = count($brand_list_count);// set the count of brands in initial user query
        return array($table_array, $brand_list, $price_range);

    }
// ###########################################################################################################################################

// ########################################## bulid brand filter #############################################################################
    public function brand_filter_builder($selected_brand_name)
    {
    	$bname = array();
	    
	    foreach($selected_brand_name as $temp)
	    {
	        array_push($bname, $temp);
	    }
        $brand_filter          = array();
        
        if ($bname) 
        {
             $brand_filter['terms']['brand'] = $bname;
        }
        return $brand_filter;
    }
// ###########################################################################################################################################

// ########################################## bulid os filter #############################################################################
    public function os_filter_builder($selected_os_name)
    {
    	$oname = array();
	    
	    foreach($selected_os_name as $temp)
	    {
	        array_push($oname, $temp);
	    }
        $os_filter          = array();
        echo "os";
        if ($oname) 
        {
             $os_filter['terms']['os'] = $oname;
        }
        return $os_filter;
    }
// ###########################################################################################################################################



// ###########################################  build range filter ########################################################################### 
    public function range_filter_builder($selected_price_range)
    {
    	$range_array=array();
    	 foreach ($selected_price_range as $temp) 
	        {
	   
	            $filter = array();
	            switch ($temp)
	            {
	                case ($temp >= 0 && $temp < 10000):
	                    $lower_limit = 0;
	                    $upper_limit = 10000;
	                    break;
	                case ($temp >= 10000 && $temp < 20000):
	                    $lower_limit = 10000;
	                    $upper_limit = 20000;
	                    break;
	                case ($temp >= 20000 && $temp < 35000):
	                    $lower_limit = 20000;
	                    $upper_limit = 35000;
	                    break;
	                case ($temp >= 35000 && $temp < 50000):
	                    $lower_limit = 35000;
	                    $upper_limit = 50000;
	                    break;
	                case ($temp >= 50000):
	                    $lower_limit = 50000;
	                    $upper_limit = 1500000;
	                    break;
	                
	                default:
	                    $lower_limit = 0;
	                    $upper_limit = 0;
	                    break;
	            }
	            $filter["range"]['price']['gte'] = $lower_limit;
	            $filter["range"]['price']['lt']  = $upper_limit;
	            array_push($range_array, $filter);
	           
	        }

    	$range_filter                   = array();
        $range_filter['bool']['should'] = $range_array;
        return $range_filter;
       
    }
// ###########################################################################################################################################


// #####################################  build initial user search query ####################################################################
    public function initial_query_builder($ini_query)
    {
    	$initial_string=array();
    	if($ini_query) // check condition for null search query
        {
        	$initial_string['match']['title'] = $ini_query;
        }
        else
        {
        	$initial_string['match_all'] = new StdClass();
        }
        return $initial_string;
    }
// ###########################################################################################################################################

// ############################################## aggregation builder ########################################################################
    public function aggregations_builder($ini_brand_size, $ini_query)
    {
    	$aggregation_array =array();
    	$aggregation_array['global_agg']['global'] = new StdClass();
    	$aggregation_array['global_agg']['aggs']['filter_scope']['filter']['query']['match']['title'] = $ini_query;
    	$aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['field'] = "brand";
    	$aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['min_doc_count'] = 0;
    	$aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['size'] = $ini_brand_size;

    	/*$aggregation_array['my_agg']['terms']['field']="brand";
    	$aggregation_array['my_agg']['terms']['min_doc_count']=0;
    	$aggregation_array['my_agg']['terms']['size']=$ini_brand_size;*/
    	return $aggregation_array;


    }

// ###########################################################################################################################################

// ############################################## filtered query (for filtering user search) #################################################
    public function filtered_query($selected_brand_name, $ini_query, $selected_price_range, $ini_brand_size, $test)
    {
    	// call brand filter builder
    	// call range filter builder
    	// call os 	  filter builder
    	// call initial search builder
    	// call aggregations   builder
        $brand_filter      = $this->brand_filter_builder($selected_brand_name);
        $range_filter      = $this->range_filter_builder($selected_price_range);
        $os_filter 		   = $this->os_filter_builder($selected_os_name);
        $initial_query     = $this->initial_query_builder($ini_query);
        
        $aggregation_array = $this->aggregations_builder($ini_brand_size, $ini_query);
        
        //die();
        // combine all the filtered in must array
        $must_array = array();

        if($brand_filter)
        {
        	array_push($must_array, $brand_filter);
        }
        if($os_filter)
        {
        	array_push($must_array, $os_filter);
        }
        
        array_push($must_array, $range_filter);

        // combine aggregation ,filters,initial user query 
    	$searchParams=$this->get_es_index_params(INDEX, TYPE, SIZE);
        $searchParams['body']['query']['filtered']['filter']['bool']['must']  = $must_array;
        $searchParams['body']['query']['filtered']['query']					  = $initial_query;  	//initial user query builder
        $searchParams['body']['aggs'] 								  		  = $aggregation_array; // aggregation on brand
        //making of searchable json done
        //echo "yeh";
        var_dump(json_encode($searchParams));
        //die();
        $client = new Elasticsearch\Client(); 
        $retDoc = $client->search($searchParams);//echo json_encode($retDoc);
        $aggregation_result=$retDoc['aggregations']['global_agg']['filter_scope']['brand_bucket']['buckets']; // gets aggregation results
        
        $brand_list=array();
        $retDoc = $retDoc[hits][hits];
        for ($i = 0; $i < 10; $i = $i+1)
        {
            $table_array[$i] = $retDoc[$i];
        }
        for ($i = 0; $i < count($retDoc); $i = $i+1)
        {    
            $temp  = $retDoc[$i][_source][brand];
            $temp1 = $retDoc[$i][_source][price];
            if(!array_key_exists($temp, $brand_list_count))
            {
            	array_push($brand_list, $temp);
                $brand_list_count[$temp]=0;
            }
            // switch case  for hardcoded price  range count
            switch ($temp1) 
            {
                case ($temp1<10000):
                    $price_range[0]+= 1;
                    break;
                case ($temp1<20000&&$temp1>=10000):
                    $price_range[1]+= 1;
                    break;
                case ($temp1<35000&&$temp1>=20000):
                    $price_range[2]+= 1;
                    break;
                case ($temp1<50000&&$temp1>=35000):
                    $price_range[3]+= 1;
                    break;
                case ($temp1>=50000):
                    $price_range[4]+= 1;
                    break;
                default:
                    $price_range[5]+= 1;
                    break;
            }
            // end of switch
            $brand_list_count[$temp]+= 1;
        }
        return array($table_array, $brand_list, $price_range, $aggregation_result);

    }
// ###########################################################################################################################################


// #################################### find list of filter because it depends on condition that aggregation is applied or not ###############
    public function find_list_of_filters($ret_array)
    {
    	$list_of_filters=array();
    	if(count($ret_array) == 3) // only user query search is perform no aggregation is require
    	{
    		for ($i = 0; $i < count($ret_array[1]); $i = $i+1)
       		{
    			$list_of_filters[$i][0] = $ret_array[1][$i];
    			$list_of_filters[$i][1] = 50; //any non zero element
    		}
    	}
    	else 					// aggregation is require for user options
    	{

    		for ($i = 0; $i < count($ret_array[3]); $i = $i+1)
       		{
    			$list_of_filters[$i][0] = $ret_array[3][$i]['key'];
    			$list_of_filters[$i][1] = $ret_array[3][$i]['doc_count'];
    		}
    		

    	}

    	return $list_of_filters;

    }

// ############################################################################################################################################
 }
?>