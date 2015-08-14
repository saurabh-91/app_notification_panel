
<?php
include ("class_query.php");
$es_client=new SearchElastic();
$es_client->check_operation();
$set_brand_flag=isset($_GET['brand_name']);

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
<form  name="my_form" form id = "brand-fliter" action ="test_js.php" method="get" >

<?php while (current($ret_array[1]))
    { ?>
    <input type="checkbox" name="brand_name[]" class="brand_id" value="<?php echo key($ret_array[1]); ?>"<?php if($set_brand_flag) echo SHOWCHECKED ; ?>><?php echo key($ret_array[1])." = ".current($ret_array[1]); ?><br>
       
        <?php next($ret_array[1]);
    }?>

   

    <input type  = "hidden"   name  = "initial_query" value="<?php echo $ini_query?>">
    <input class = "range_id" type  = "checkbox" name="price_range[]" value="8888"  <?php if(isset($_GET['price_range'][0])) echo SHOWCHECKED; ?> >0-10000          (<?php echo $ret_array[2][0];?>)<br>
    <input class = "range_id" type  = "checkbox" name="price_range[]" value="18888" <?php if(isset($_GET['price_range'][1])) echo SHOWCHECKED; ?> >10000-20000      (<?php echo $ret_array[2][1];?>)<br>
    <input class = "range_id" type  = "checkbox" name="price_range[]" value="28888" <?php if(isset($_GET['price_range'][2])) echo SHOWCHECKED; ?> >20000-35000      (<?php echo $ret_array[2][2];?>)<br>
    <input class = "range_id" type  = "checkbox" name="price_range[]" value="38888" <?php if(isset($_GET['price_range'][3])) echo SHOWCHECKED; ?> >35000-50000      (<?php echo $ret_array[2][3];?>)<br>
    <input class = "range_id" type  = "checkbox" name="price_range[]" value="48888" <?php if(isset($_GET['price_range'][4])) echo SHOWCHECKED; ?> >50000 and above  (<?php echo $ret_array[2][4];?>)<br>
    <input class = "range_id" type  = "checkbox" name="price_range[]" value="-5"    <?php if(isset($_GET['price_range'][5])) echo SHOWCHECKED; ?> >price not listed (<?php echo $ret_array[2][5];?>)<br>
    <input type  = "submit"   value = "submit" class="buttons"/>
    
</form>

</body>
</html>

</body>
</html>