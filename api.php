<?php

class MyDB extends SQLite3
{
   function __construct()
   {
      $this->open('lookup.db');
   }
}
$db = new MyDB();
$N=8;
$list0=array("dict_a_b","dict_c","dict_d_f","dict_g_k","dict_l_o","dict_p_r","dict_s","dict_t_z");
$list1=array("variant_a_b","variant_c","variant_d_f","variant_g_k","variant_l_o","variant_p_r","variant_s","variant_t_z");
   
if(array_key_exists("word", $_POST)|array_key_exists("word", $_GET)){
   if(isset($_POST["word"])){$word = $_POST["word"];}else{$word = $_GET["word"];}
   $word=trim($word);
   $word=strtolower($word);
   if($word==""){
      print_r("请输入要查询的单词");
   }
   else{
      $fist=substr($word, 0 , 1);
      $j=0;
      for($i=0;$i<$N;$i++){
         $sql =<<<EOF
         SELECT * from $list0[$i] WHERE word=="$word" OR mean_cn LIKE "%$word%";
         EOF;
         $ret = $db->query($sql);
         while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
            $arr[$j] = array('word' => $row['word'], 'accent' => $row['accent'], 'mean_cn' => $row['mean_cn']);
            $j++;
         }
      }

      // $arr[1] = array('origin' => $origin, 'variant' => $variant);
      header("Content-type: text/json");
      echo json_encode($arr);
      $db->close();
   }
}
?>