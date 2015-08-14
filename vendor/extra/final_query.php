<?php
include("constants.php");
require_once 'vendor/autoload.php';
class SearchElastic 
{
    public $ini_query;
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
    public function perform_es($user_query_string, $selected_brand_name, $iquery, $selected_price_range, $page_no, $selected_os_name)//depent on user input which opretion to perform
    {
    	if (isset($user_query_string))
    	{ 	//call search function;
    		$ret_array         =  $this->user_query_search($user_query_string);
    	}
	    else
	    {	//call filtered search function
	    	$ret_array            =  $this->filtered_query($selected_brand_name, $iquery, $selected_price_range, $page_no, $selected_os_name);
	    }
	    return $ret_array;
    }
// ###########################################################################################################################################
   
// ################################################# get results for html ####################################################################
    public function get_results($searchParams, $page_no)
    {

        $client = new Elasticsearch\Client(); 
        $retDoc = $client->search($searchParams);//echo json_encode($retDoc);
        $brand_list_temp = $retDoc['aggregations']['global_agg']['filter_scope']['brand_bucket']['buckets'];
        $price_range_list_temp = $retDoc['aggregations']['global_agg']['filter_scope']['price_range_bucket']['buckets'];
        $os_list_temp = $retDoc['aggregations']['global_agg']['filter_scope']['os_bucket']['buckets'];
        $retDoc = $retDoc[hits][hits];
    	$brand_list=array();
        for ($i=0; $i < 10; $i = $i+1)
        {
            $table_array[$i] = $retDoc[($i+10*$page_no)];
        }
        for ($i = 0; $i < count($brand_list_temp); $i = $i+1)
        {
            $brand_list[$i]=$brand_list_temp[$i]['key']; 
        }
        for ($i = 0; $i < count($os_list_temp); $i = $i+1)
        {
            $os_list[$i]=$os_list_temp[$i]['key']; 
        }
        
        //var_dump($brand_list);
        //die();
        for ($i = 0; $i < count($price_range_list_temp); $i = $i+1)
        {
            $price_range_list[$i]= $price_range_list_temp[$i]['doc_count'];  
        }
        return array($table_array, $brand_list, $price_range_list, $os_list);

    }
// ###########################################################################################################################################

// ######################################### user query search function ###################################################################### 
    public function user_query_search($user_query_string)
    {
    	$user_query_string=trim($user_query_string);

    	$this->ini_query = $user_query_string;
        $searchParams=$this->get_es_index_params(INDEX, TYPE, SIZE);
        if($user_query_string) // check condition for null search query
        {
        	$searchParams['body']['query']['match']['title'] = $user_query_string;
        }
        else
        {
        	$searchParams['body']['query']['match_all'] = new StdClass();
        }
        $aggregation_array            = $this->aggregations_builder($this->ini_query);   
        $searchParams['body']['aggs'] = $aggregation_array;
        var_dump(json_encode($searchParams));
        return $this->get_results($searchParams);
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
    public function initial_query_builder($iquery)
    {
        $this->ini_query=$iquery;
    	$initial_string=array();
    	if($iquery) // check condition for null search query
        {
        	$initial_string['match']['title'] = $iquery;
        }
        else
        {
        	$initial_string['match_all'] = new StdClass();
        }
        return $initial_string;
    }
// ###########################################################################################################################################

// ############################################## aggregation builder ########################################################################
    public function aggregations_builder($iquery)
    {
    	$aggregation_array =array();
    	$aggregation_array['global_agg']['global'] = new StdClass();
        if($iquery)
    	$aggregation_array['global_agg']['aggs']['filter_scope']['filter']['query']['match']['title'] = $iquery;
        else 
        $aggregation_array['global_agg']['aggs']['filter_scope']['filter']['query']['match_all'] = new StdClass();
    	//----------------------------------- brand aggregation ------------------------------------------------------------
        $aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['field'] = "brand";
    	$aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['size'] = 0;
        $aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['min_doc_count'] =1;
        //------------------------------------------------------------------------------------------------------------------
        //----------------------------------- os aggregation ------------------------------------------------------------
        $aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['os_bucket']['terms']['field'] = "os";
        $aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['os_bucket']['terms']['size'] = 0;
        $aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['os_bucket']['terms']['min_doc_count'] =1;
        //------------------------------------------------------------------------------------------------------------------
        //----------------------------------- price range aggregation ------------------------------------------------------------
        $aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['price_range_bucket']['range']['field'] = "price";
        $price_range_array=array();
        $temp['from']='0'; $temp['to']='10000';
        array_push($price_range_array, $temp);
        $temp['from']='10000'; $temp['to']='20000';
        array_push($price_range_array, $temp);
        $temp['from']='20000'; $temp['to']='35000';
        array_push($price_range_array, $temp);
        $temp['from']='35000'; $temp['to']='50000';
        array_push($price_range_array, $temp);
        $temp['from']='50000'; $temp['to']='1550000';
        array_push($price_range_array, $temp);
        $aggregation_array['global_agg']['aggs']['filter_scope']['aggs']['price_range_bucket']['range']['ranges'] = $price_range_array;
        //------------------------------------------------------------------------------------------------------------------
    	return $aggregation_array;
    }
// ###########################################################################################################################################

// ############################################## filtered query (for filtering user search) #################################################
    public function filtered_query($selected_brand_name, $iquery, $selected_price_range, $page_no, $selected_os_name)
    {
        $brand_filter      = $this->brand_filter_builder($selected_brand_name);		// call brand filter builder
        $range_filter      = $this->range_filter_builder($selected_price_range); 	// call range filter builder
        $os_filter 		   = $this->os_filter_builder($selected_os_name); 		// call os 	  filter builder
        $initial_query     = $this->initial_query_builder($iquery);				// call initial search builder
        $aggregation_array = $this->aggregations_builder($iquery);// call aggregations   builder
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
        return $this->get_results($searchParams, $page_no);
    }
// ###########################################################################################################################################
}
?>