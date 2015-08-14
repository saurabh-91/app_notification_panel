<?php  include ("class_query.php");?>
<?php  include ("constants.php");?>

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
<form form id = "brand-fliter" action ="test1.php" method="get">

<?php while (current($ret_array[1]))
    { ?>
    <input type="checkbox" name="brand_name[]" id="brand_id" value="<?php echo key($ret_array[1]); ?>"><?php echo key($ret_array[1])." = ".current($ret_array[1]); ?><br>
       
        <?php next($ret_array[1]);
    }?>

    <input type="hidden"   name="selected[b]" value="<?php echo $s_brand?>">
    <input type="hidden"   name="selected[r]" value="<?php echo $s_range?>"> 
    <!--<?php foreach($s_brand as $value)
{
  echo '<input type="hidden" name="selected_brand[]" value="'. $value. '">';
}
?>
<?php foreach($s_range as $value)
{
  echo '<input type="hidden" name="selected_range[]" value="'. $value. '">';
}
?>-->
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

</body>
</html>