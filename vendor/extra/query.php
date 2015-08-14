<!-- #################################################### start of main php program ##############################################-->
<?php


include("constants.php");

    
//#################################################   some global veriables ##########################################################
    require 'vendor/autoload.php';
    $ini_query="";// initial search query which will be maintained after applying filter
    $ret_array=array(); // global array for storing  arrays of results
//#################################################################################################################################





//############################################# function for home page search   ###################################################
     if (isset($_POST['search']))
    {
        $query=$_POST["search"];
        $ini_query=$query;
        $searchParams=search($query); 
        $ret_array=common($searchParams);

    }
    else
    {
        $bname=array();
        $ini_query=$_GET['initial_query'];
         foreach($_GET['brand_name'] as $temp)
        {
            array_push($bname,$temp);
        }
        $range_array=array();
        $lower_limit=0;
        $upper_limit=10000;
        foreach ($_GET['price_range'] as $temp) 
        {

            $filter=array();
            switch ($temp) {
                case ($temp>=0&&$temp<10000):
                    $lower_limit=0;
                    $upper_limit=10000;
                    break;
                case ($temp>=10000&&$temp<20000):
                    $lower_limit=10000;
                    $upper_limit=20000;
                    break;
                case ($temp>=20000&&$temp<35000):
                    $lower_limit=20000;
                    $upper_limit=35000;
                    break;
                case ($temp>=35000&&$temp<50000):
                    $lower_limit=35000;
                    $upper_limit=50000;
                    break;
                case ($temp>=50000):
                    $lower_limit=50000;
                    $upper_limit=1500000;
                    break;
                
                default:
                    $lower_limit=0;
                    $upper_limit=0;
                    break;
            }
            $filter["range"]['price']['gte']=$lower_limit;
            $filter["range"]['price']['lt']=$upper_limit;
            array_push($range_array, $filter);
           
        }
        $searchParams=filter_function($bname,$ini_query,$range_array);
        $ret_array=common($searchParams); 
    }


    function search($query) 
    {
        $searchParams = array();
        $searchParams['index'] = INDEX;
        $searchParams['type']  = TYPE;
        $searchParams['body']['query']['match']['title'] = $query;
        $searchParams['size']=SIZE;
        return $searchParams;        
    }

//##################################################################################################################################

//####################################################   filter function  ############################################################
    function filter_function($bname,$ini_query,$range_array)
    {
        $searchParams = array();
        $searchParams['index'] = INDEX;
        $searchParams['type']  = TYPE;
        $query=array();
        $must_array=array();
        
        if ($bname) 
        {
             $query['terms']['brand']=$bname;
             array_push($must_array,$query);
        }
        $filter=array();
        $filter['bool']['should']=$range_array;
        
        array_push($must_array,$filter);
        $searchParams['body']['query']['filtered']['filter']['bool']['must']=$must_array;
        $searchParams['body']['query']['filtered']['query']['match']['title']=$ini_query;
        $searchParams['body']['size']=SIZE;

        //var_dump(json_encode($searchParams));
        //die(3);
        return $searchParams;
    }
//####################################################################################################################################




//###################################### common function used by both search and filter function #####################################  
    function common($searchParams)
    {
        $client = new Elasticsearch\Client(); // create a elastcsearch client
        $retDoc = $client->search($searchParams);//echo json_encode($retDoc);
        $retDoc=$retDoc[hits][hits];
        for ($i=0;$i<10;$i=$i+1)
        {
            $table_array[$i]=$retDoc[$i];
        }
        for ($i=0;$i<count($retDoc);$i=$i+1)
        {    
            $temp=$retDoc[$i][_source][brand];
            $temp1=$retDoc[$i][_source][price];
            if(!array_key_exists($temp, $brand_list_count))
            {
                $brand_list_count[$temp]=0;
            }
            // switch case  for hardcoded price  range count
            switch ($temp1) 
            {
                case ($temp1<10000):
                    $price_range[0]+=1;
                    break;
                case ($temp1<20000&&$temp1>=10000):
                    $price_range[1]+=1;
                    break;
                case ($temp1<35000&&$temp1>=20000):
                    $price_range[2]+=1;
                    break;
                case ($temp1<50000&&$temp1>=35000):
                    $price_range[3]+=1;
                    break;
                case ($temp1>=50000):
                    $price_range[4]+=1;
                    break;
                default:
                    $price_range[5]+=1;
                    break;
            }
            // end of switch
            $brand_list_count[$temp]+=1;
        }
        return array($table_array,$brand_list_count,$price_range);
    }

//#########################################################################################################################################


?>


<!--##################################### end of php and start of html tables and checkboxes ############################################# -->





<!-- ###########################################   print results in tabular format ##################################################################-->

<table>
  <tr>
    <th>name</th>
    <th>brand</th>       
    <th>Price</th>
    <th>image</th>
    <th>view_details</th>
  </tr>
  <?php for ($i=0;$i<count($ret_array[0]);$i=$i+1){?>
      <tr> 
        <td><?php echo $ret_array[0][$i][_source][name]?></td>
      
        <td><?php echo $ret_array[0][$i][_source][brand]?></td>
        <td><?php echo $ret_array[0][$i][_source][price]?></td>      
        <td><img src="<?php echo $ret_array[0][$i][_source][i_link];?>" style="width:100px;height:228px"> </td>
        <?php $id =$ret_array[0][$i][_id]; ?>

        <td><a href="details_page.php?id=<?php echo $id ?>">click here</a> </td> 
        
      </tr>
      <?php } ?>
</table>



<!-- #############################################################  display filter in checkbox format ######################################################### -->
<form form id = "brand-fliter" action ="query.php" method="get">

<?php while (current($ret_array[1]))
    { ?>
    <input type="checkbox" name="brand_name[]" value="<?php echo key($ret_array[1]); ?>"><?php echo key($ret_array[1])." = ".current($ret_array[1]); ?><br>
       
        <?php next($ret_array[1]);
    }?>
    <input type="hidden"   name="initial_query" value="<?php echo $ini_query?>">
    <input type="checkbox" name="price_range[]" value="8888" >0-10000(<?php echo $ret_array[2][0];?>)<br>
    <input type="checkbox" name="price_range[]" value="18888" >10000-20000 (<?php echo $ret_array[2][1];?>)<br>
    <input type="checkbox" name="price_range[]" value="28888" >20000-35000 (<?php echo $ret_array[2][2];?>)<br>
    <input type="checkbox" name="price_range[]" value="38888" >35000-50000 (<?php echo $ret_array[2][3];?>)<br>
    <input type="checkbox" name="price_range[]" value="48888" >50000 and above (<?php echo $ret_array[2][4];?>)<br>
    <input type="checkbox" name="price_range[]" value="-5" >price not listed (<?php echo $ret_array[2][5];?>)<br>
    <input type="submit" value="submit" class="buttons"/>
    
</form>

<!-- ##########################################################################################################################################  -->


</body>
</html>