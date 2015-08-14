
<?php
include ("class_query.php");
include ("constants.php");
$bname=$_POST['brand_name'];
$page_no=0;
if(!isset($_POST['search']))
{
$page_no= $_POST['from'];
}
$es_client 		 =  new SearchElastic();
$ret_array 		 =  $es_client->check_operation($page_no);
$list_of_filters =  $es_client->find_list_of_filters($ret_array);
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<!-- ###############################   search box ############################################################################## -->
<form action="result.php" method="post">
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
<form  name="my_form" form id = "brand-fliter" action ="result.php" method="post" >

<?php for ($i = 0; $i < count($list_of_filters); $i = $i+1)
    { ?>
    <input type="checkbox" name="brand_name[]" class="brand_id" onchange="my_function()" value="<?php echo $list_of_filters[$i][0]; ?>"<?php if(in_array($list_of_filters[$i][0],$bname)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php echo $list_of_filters[$i][0]; ?><br>
       
        <?php 
    }?>    
    <input type="hidden"   name="initial_query" value="<?php echo $ini_query?>">
    <input type="hidden"   name="initial_size_of_brand" value="<?php echo $ini_brand_size?>">
    <input type="hidden"    id="page_id" 	name="from" value="<?php echo $page_no ?>">
    <input class="range_id" type="checkbox" name="price_range[]" value="8888"  <?php if(isset($_POST['price_range'][0])) echo SHOWCHECKED; ?> >0-10000          (<?php echo $ret_array[2][0];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="18888" <?php if(isset($_POST['price_range'][1])) echo SHOWCHECKED; ?> >10000-20000      (<?php echo $ret_array[2][1];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="28888" <?php if(isset($_POST['price_range'][2])) echo SHOWCHECKED; ?> >20000-35000      (<?php echo $ret_array[2][2];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="38888" <?php if(isset($_POST['price_range'][3])) echo SHOWCHECKED; ?> >35000-50000      (<?php echo $ret_array[2][3];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="48888" <?php if(isset($_POST['price_range'][4])) echo SHOWCHECKED; ?> >50000 and above  (<?php echo $ret_array[2][4];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="-5"    <?php if(isset($_POST['price_range'][5])) echo SHOWCHECKED; ?> >price not listed (<?php echo $ret_array[2][5];?>)<br>
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