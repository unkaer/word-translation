<?php
   
if(array_key_exists("word", $_POST)|array_key_exists("word", $_GET)){
   if(isset($_POST["word"])){$word = $_POST["word"];}else{$word = $_GET["word"];}
   if($word==""){
      print_r("请输入要查询的单词");
   }else{
   class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('lookup.db');
      }
   }
   $db = new MyDB();
   // if(!$db){
   //    echo $db->lastErrorMsg();
   // } else {
   //    echo "开始检索\n<br>";
   // }

   $fist=substr($word, 0 , 1);
   $list0=array("a","b","c","def","ghijk","lmno","pqr","s","tuvwxyz");
   $list1=array("dict_a_b","dict_bcz","dict_c","dict_d_f","dict_g_k","dict_l_o","dict_p_r","dict_s","dict_t_z");
   $list2=array("variant_a_b","variant_a_b","variant_c","variant_d_f","variant_g_k","variant_l_o","variant_p_r","variant_s","variant_t_z");

   for($i=0;$i<9;$i++){
      if(strpos($list0[$i],$fist) !== false){ 
         $sql =<<<EOF
         SELECT * from $list1[$i] WHERE word=="$word";
      EOF;
      break;
      }
   }
   // echo $i;
   // SELECT * from $list1[$i] WHERE word LIKE "%$word%";

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      // echo "word = ". $row['word'] . "\n<br>";
      // echo "accent = ". $row['accent'] . "\n<br>";
      // echo "mean_cn = ". $row['mean_cn'] . "\n<br>";
      $accent=$row['accent'];
      $mean_cn=$row['mean_cn'];
   }

   for($i=0;$i<9;$i++){
      if(strpos($list0[$i],$fist) !== false){ 
         $sql =<<<EOF
         SELECT * from $list2[$i] WHERE variant=="$word" OR origin=="$word";
      EOF;
      $j=$i;
      break;
      }
   }

   $i=0;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      $origin=$row["origin"];
      $variant[$i]=$row["variant"];
      $i++;
   }
   if($variant[0]==$word){   // 查找所有变型
      $sql =<<<EOF
         SELECT * from $list2[$j] WHERE origin=="$origin";
      EOF;
      $i=0;
      $ret = $db->query($sql);
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
         $variant[$i]=$row["variant"];
         $i++;
      }
   }

   $arr = array('word' => $word, 'accent' => $accent, 'mean_cn' => $mean_cn, 'origin' => $origin, 'variant' => $variant);
   echo json_encode($arr);
   // print_r($origin);
   // print_r($variant);
   $db->close();}
}
?>