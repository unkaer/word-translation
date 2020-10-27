
   <form action="" method='POST'>
      <p>单词查询：<input name="word" style="" autofocus value="">
      <input type="submit" value="查询"></p>
   </form>
   <form action="./api.php" method='POST'>
      <p>API查询：<input name="word" style="" autofocus value="">
      <input type="submit" value="查询"></p>
   </form>
<?php
   class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('lookup.db');
      }
   }
   $db = new MyDB();
   
   $list0=array("a","b","c","def","ghijk","lmno","pqr","s","tuvwxyz");  // 0-8
   $list1=array("dict_a_b","dict_bcz","dict_c","dict_d_f","dict_g_k","dict_l_o","dict_p_r","dict_s","dict_t_z");
   $list2=array("variant_a_b","variant_a_b","variant_c","variant_d_f","variant_g_k","variant_l_o","variant_p_r","variant_s","variant_t_z");
   
   if(array_key_exists("word", $_POST)|array_key_exists("word", $_GET)){
      if(isset($_POST["word"])){$word = $_POST["word"];}else{$word = $_GET["word"];}
      if($word==""){
         print_r("请输入要查询的单词");
      }else{
         $fist=substr($word, 0 , 1);
         for($i=0;$i<9;$i++){   // 判断第一个字母，确定数据库
            if(strpos($list0[$i],$fist) !== false){ 
               $sql =<<<EOF
               SELECT * from $list1[$i] WHERE word=="$word";
            EOF;
            break;
            }
         }
      
         $ret = $db->query($sql);
         while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
            echo "单词：". $row['word'] . "\n<br>";
            echo "发音：". $row['accent'] . "\n<br>";
            echo "中文意思：". $row['mean_cn'] . "\n<br>";
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
      
         print_r('原型为:<a href="./?word='.$origin.'">'.$origin.'</a>');
         print_r("<br>变型有: ");
         for($i=0;$i<sizeof($variant);$i++){
            print_r('<a href="./?word='.$variant[$i].'">'.$variant[$i].'</a>;');
         }
      }
   }else{
      echo "随机展示: <br>";
      $i = rand(0,8);
      $sql =<<<EOF
         SELECT * FROM $list1[$i] ORDER BY RANDOM() limit 1
      EOF;
      $ret = $db->query($sql);
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
         echo "单词：". $row['word'] . "\n<br>";
         echo "发音：". $row['accent'] . "\n<br>";
         echo "中文意思：". $row['mean_cn'] . "\n<br>";
         }
   }
   $db->close();

?>