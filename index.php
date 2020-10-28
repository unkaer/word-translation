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
   
   $N=8;
   $list0=array("dict_a_b","dict_c","dict_d_f","dict_g_k","dict_l_o","dict_p_r","dict_s","dict_t_z");
   $list1=array("variant_a_b","variant_a_b","variant_c","variant_d_f","variant_g_k","variant_l_o","variant_p_r","variant_s","variant_t_z");
   
   if(array_key_exists("word", $_POST)|array_key_exists("word", $_GET)){
      if(isset($_POST["word"])){$word = $_POST["word"];}else{$word = $_GET["word"];}
      $word=trim($word);
      $word=strtolower($word);
      if($word==""){
         print_r("请输入要查询的单词");
      }
      else{    // 查询功能
         $fist=substr($word, 0 , 1);
         if(($fist>="a"&&$fist<="z")|($fist>="A"&&$fist<="Z")){   // 是字母，找单词
            for($i=0;$i<$N;$i++){   // 每一个都跑一遍
               $sql =<<<EOF
                  SELECT * from $list0[$i] WHERE word=="$word";
               EOF;
               $ret = $db->query($sql);
               while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                  echo "<br>单词：<a href=\"?word=".$row['word']."\">".$row['word']."</a>\n<br>";
                  echo "发音：". $row['accent'] . "\n<br>";
                  echo "中文意思：". $row['mean_cn'] . "\n<br>";
                  echo "数据库：". $list0[$i] . "\n<br>";
               }
            }

            //是否存在变体
            $j=0;
            for($i=0;$i<$N;$i++){  // 每一个都跑一遍
               $sql =<<<EOF
                  SELECT * from $list1[$i] WHERE variant=="$word" OR origin=="$word";
               EOF;
               $ret = $db->query($sql);
               while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                  $origin[$j]=$row["origin"];
                  $variant[$j]=$row["variant"];
                  $j++;
               }
            }
      
            if(isset($variant)){
               print_r("原型--->变体<br>");
               for($i=0;$i<sizeof($variant);$i++){
                  print_r('<a href="./?word='.$origin[$i].'">'.$origin[$i].'</a>--->');
                  print_r('<a href="./?word='.$variant[$i].'">'.$variant[$i].'</a><br>');
               }
            }
         }
         else{   // 非字母，查找中文
            for($i=0;$i<$N;$i++){   // 每一个都跑一遍
               $sql =<<<EOF
                  SELECT * from $list0[$i] WHERE mean_cn LIKE "%$word%";
               EOF;
               $ret = $db->query($sql);
               while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                  echo "单词：<a href=\"?word=".$row['word']."\">".$row['word']."</a>\n<br>";
                  echo "发音：". $row['accent'] . "\n<br>";
                  echo "中文意思：". $row['mean_cn'] . "\n<br>";
               }
            }
         }
      }
   }
   else{
      echo "随机展示: <br>";
      $i = rand(0,8);
      $sql =<<<EOF
         SELECT * FROM $list0[$i] ORDER BY RANDOM() limit 1
      EOF;
      $ret = $db->query($sql);
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
         $word=$row['word'];
         echo "单词：". $row['word'] . "\n<br>";
         echo "发音：". $row['accent'] . "\n<br>";
         echo "中文意思：". $row['mean_cn'] . "\n<br>";
      }
      //是否存在变体
      $j=0;
      for($i=0;$i<$N;$i++){  // 每一个都跑一遍
         $sql =<<<EOF
            SELECT * from $list1[$i] WHERE variant=="$word" OR origin=="$word";
         EOF;
         $ret = $db->query($sql);
         while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
            $origin[$j]=$row["origin"];
            $variant[$j]=$row["variant"];
            $j++;
         }
      }

      if(isset($variant)){
         print_r("原型--->变体<br>");
         for($i=0;$i<sizeof($variant);$i++){
            print_r('<a href="./?word='.$origin[$i].'">'.$origin[$i].'</a>--->');
            print_r('<a href="./?word='.$variant[$i].'">'.$variant[$i].'</a><br>');
         }
      }
   }
   $db->close();

?>