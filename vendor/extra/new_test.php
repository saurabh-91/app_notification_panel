
<?php
include ("clean_class_query.php");
include ("constants.php");
$es_client =  new SearchElastic();
if (isset($_POST["search"]))
    	{ 	//call search function;
    		$user_query_string =  $_POST["search"];
    		$ret_array         =  $es_client->user_query_search($user_query_string);
    	}
	    else
	    {	//call filtered search function
	    	$ini_query            =  $_POST['initial_query'];
	    	$selected_brand_name  =  $_POST['brand_name'];
	    	$selected_price_range =  $_POST['price_range'];
	    	$ret_array            =  $es_client->filtered_query($selected_brand_name, $ini_query, $selected_price_range);
	    }

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
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
<form  name="my_form" form id = "brand-fliter" action ="new_test.php" method="post" >

<?php while (current($ret_array[1]))
    { ?>
    <input type="checkbox" name="brand_name[]" class="brand_id" value="<?php echo key($ret_array[1]); ?>"<?php if($set_brand_flag) echo SHOWCHECKED ; ?>><?php echo key($ret_array[1])." = ".current($ret_array[1]); ?><br>
       
        <?php next($ret_array[1]);
    }?>

   <?php echo "kknkn"?>
<?php echo $ini_query?>
    <input type="hidden"   name="initial_query" value="<?php echo $ini_query?>">
    <input class="range_id" type="checkbox" name="price_range[]" value="8888"  <?php if(isset($_GET['price_range'][0])) echo SHOWCHECKED; ?> >0-10000          (<?php echo $ret_array[2][0];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="18888" <?php if(isset($_GET['price_range'][1])) echo SHOWCHECKED; ?> >10000-20000      (<?php echo $ret_array[2][1];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="28888" <?php if(isset($_GET['price_range'][2])) echo SHOWCHECKED; ?> >20000-35000      (<?php echo $ret_array[2][2];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="38888" <?php if(isset($_GET['price_range'][3])) echo SHOWCHECKED; ?> >35000-50000      (<?php echo $ret_array[2][3];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="48888" <?php if(isset($_GET['price_range'][4])) echo SHOWCHECKED; ?> >50000 and above  (<?php echo $ret_array[2][4];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="-5"    <?php if(isset($_GET['price_range'][5])) echo SHOWCHECKED; ?> >price not listed (<?php echo $ret_array[2][5];?>)<br>
    <input type="submit" value="submit" class="buttons"/>
    
</form>

</body>
</html>

</body>
</html>