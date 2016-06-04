<?php 

$a1=array("0","1","2","3");
$a2=array("1","2","3");


$result=array_diff($a1,$a2);
print_r($result);


$a3 = array("2","3");
    //生成一个数组
    $a4 = array("0","1","2","3");
    
      
    //差集
    $result2 = array_diff($a4,$a3);
    
    print_r($result2);
    
    exit();



 ?>