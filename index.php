
   <form action="" method='POST'>
      <p>单词查询：<input name="word" style="" autofocus value="">
      <input type="submit" value="搜索"></p>
   </form>
<?php
   
if(array_key_exists("word", $_POST)|array_key_exists("word", $_GET)){
   if(isset($_POST["word"])){$word = $_POST["word"];}else{$word = $_GET["word"];}
   class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('lookup.db');
      }
   }
   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      echo "开始检索\n<br>";
   }

   $fist=substr($word, 0 , 1);
   $list0=array("a","b","c","def","ghijk","lmno","pqr","s","tuvwxyz");
   $list1=array("dict_a_b","dict_bcz","dict_c","dict_d_f","dict_g_k","dict_l_o","dict_p_r","dict_s","dict_t_z");

   for($i=0;$i<9;$i++){
      if(strpos($list0[$i],$fist) !== false){ 
         $sql =<<<EOF
         SELECT * from $list1[$i] WHERE word=="$word";
      EOF;
      }
   }
   // SELECT * from $list1[$i] WHERE word LIKE "%$word%";

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      echo "topic_id = ". $row['topic_id'] . "\n<br>";
      echo "word = ". $row['word'] . "\n<br>";
      echo "accent = ". $row['accent'] . "\n<br>";
      echo "mean_cn = ". $row['mean_cn'] . "\n<br>";
      echo "freq = ". $row['freq'] . "\n<br><br>";
      // print_r($row);
      
   }
   echo "检索完成\n";
   $db->close();
}
?>