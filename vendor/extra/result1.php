
<?php
include ("final_query.php");
include ("constants.php");
$page_no=0;
$user_query_string	  = $_POST['search'];// user query
$selected_brand_name  = $_POST['brand_name'];
$selected_os_name	  = $_POST['os_name'];
$selected_price_range = $_POST['price_range'];
if(!isset($user_query_string)) // if search is not perform
{
	$page_no        = $_POST['from'];
	$iquery      	= $_POST['initial_query'];
}
$es_client 		 =  new SearchElastic();
$ret_array 		 =  $es_client->perform_es($user_query_string, $selected_brand_name, $iquery, $selected_price_range, $page_no, $selected_os_name);
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<!-- ###############################   search box ############################################################################## -->
<form action="result1.php" method="post">
Search:<br>
<input type="text" name="search">
<br>
<input type="submit" value="Submit">
</form> 

<!-- ########################################################################################################################### -->
<table>
  <tr>
    <th>name</th>
    <th>brand</th>       
    <th>Price</th>
    <th>image</th>
    <th>view_details</th>
  </tr>
  <?php for ($i=0; $i<count($ret_array[0]); $i=$i+1){?>
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



<!-- #############################################################  display filter in checkbox format ####################################################### -->
<form  name="my_form" form id = "brand-fliter" action ="result1.php" method="post"  >
	<H1>Brands</H1>

<?php for ($i = 0; $i < count($ret_array[1]); $i = $i+1)
    { ?>
    <input type="checkbox" name="brand_name[]" class="brand_id" onchange="my_function()" value="<?php echo $ret_array[1][$i]; ?>"<?php if(in_array($ret_array[1][$i],$selected_brand_name)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php echo $ret_array[1][$i]; ?><br>
       
        <?php 
    }?>   

    <H1>os</H1>



    <?php for ($i = 0; $i < count($ret_array[3]); $i = $i+1)
    { ?>
    <input type="checkbox" name="os_name[]" class="os_id" onchange="my_function()" value="<?php echo $ret_array[3][$i]; ?>"<?php if(in_array($ret_array[3][$i],$selected_os_name)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php echo $ret_array[3][$i]; ?><br>
       
        <?php 
    }?>


    <input type="hidden"   name="initial_query" value="<?php echo $es_client->ini_query;?>">
    <input type="hidden"    id="page_id" 	name="from" value="<?php echo $page_no; ?>">
    <H1>price range</H1>
    <?php if($ret_array[2][0]) echo '<input class="range_id" type="checkbox" name="price_range[]" value="8888"   '?><?php if(isset($_POST["price_range"][0])) echo SHOWCHECKED;?> <?php if($ret_array[2][0]) echo ' >0-10000  <br>' ?>
    <?php if($ret_array[2][1]) echo '<input class="range_id" type="checkbox" name="price_range[]" value="18888"  '?><?php if(isset($_POST["price_range"][1])) echo SHOWCHECKED;?><?php if($ret_array[2][1]) echo '  >10000-20000 <br>' ?>
    <?php if($ret_array[2][2]) echo '<input class="range_id" type="checkbox" name="price_range[]" value="28888"  '?><?php if(isset($_POST["price_range"][2])) echo SHOWCHECKED;?><?php if($ret_array[2][2]) echo ' >20000-35000 <br>' ?>
    <?php if($ret_array[2][3]) echo '<input class="range_id" type="checkbox" name="price_range[]" value="38888"  '?><?php if(isset($_POST["price_range"][3])) echo SHOWCHECKED;?><?php if($ret_array[2][3]) echo '>35000-50000 <br>' ?>
    <?php if($ret_array[2][4]) echo '<input class="range_id" type="checkbox" name="price_range[]" value="48888"  '?><?php if(isset($_POST["price_range"][4])) echo SHOWCHECKED;?><?php if($ret_array[2][4]) echo '>50000 and above <br>' ?>
    <!--<input class="range_id" type="checkbox" name="price_range[]" value="-5"    <?php if(isset($_POST['price_range'][5])) echo SHOWCHECKED; ?> >price not listed (<?php echo $ret_array[2][5];?>)<br>-->
    <input type="submit" value="submit" class="buttons"  />
    <input type="submit" value="next" class="buttons" onclick="next_page()" />
    <?php if($page_no)echo '<input type="submit" value="prev" class="buttons" onclick="prev_page()" />';?>

    
</form>

<script type="text/javascript">
	var flag=0;
	function my_function () 
	{
		flag=1;
		var elem   = document.getElementById("page_id");
			elem.value=0;		
	}
	function next_page() 
	{
		if(!flag)
		{
			var elem   = document.getElementById("page_id");
			elem.value = parseInt(elem.value)+1;
			
		}
	}
	function prev_page () 
	{
		if(!flag)
		{
			var elem   = document.getElementById("page_id");
			elem.value = parseInt(elem.value)-1;
			
		}
	}


</script>
</body>
</html>

</body>
</html>