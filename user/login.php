<?php
header("Content-type: text/html; charset=utf-8"); 
include 'checkinput.php';

$sHttp = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['HTTP_HOST'];

// 连主库k
$db = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

if ($db) {
  // echo "连接数据库成功！";
    $select=mysql_select_db(SAE_MYSQL_DB, $db);

  if($select)
  {

    if(isset($_POST["subl"]))
    {
      $name=check_input($_POST["usernamel"]);
      $password=check_input($_POST["passwordl"]);
      if($name==""||$password=="")//判断是否为空
      {
        echo "<script>window.location='".$sHttp."/user/error-login.html'</script>";
       // echo"<script type="."\""."text/javascript"."\"".">"."window.alert"."("."\""."请填写正确的信息！"."\"".")".";"."</script>";
       // echo"<script type="."\""."text/javascript"."\"".">"."window.location="."\""."https://coderchain.cn/login.html"."\""."</script>";
      // echo "<p style='background:#FCC9C4;border-radius: 0.3em;padding:5px;color:#fff;''>请填写正确的信息！</p>";
        exit;
      }
      $str="select password from dengluyanzheng where usernamel="."'"."$name"."'";
       // $str="select password from register where username="."'"."$name"."'";
    $result=mysql_query($str);
      $pass=mysql_fetch_row($result);
      $pa=$pass[0];
      if($pa==$password)//判断密码与注册时密码是否一致
     {
      echo "<script>window.location='".$sHttp."/user/success-login.html'</script>";
          //echo"登录成功！";
          // echo "<script  type='text/javascript'>alert('登录成功!');</script>";
         // echo"<script type="."\""."text/javascript"."\"".">"."window.alert"."("."\""."登录成功！"."\"".")".";"."</script>";
        // echo"<script type="."\""."text/javascript"."\"".">"."window.location="."\""."https://coderchain.cn/message.html"."\""."</script>";
     }
      else{
      echo "<script>window.location='".$sHttp."/user/error-login.html'</script>";
       //echo "<script type='text/javascript'>alert('登录失败!');</script>";
      // echo "登录失败";
       //echo"<script type="."\""."text/javascript"."\"".">"."window.location="."\""."https://coderchain.cn/login.html"."\""."</script>";
     }
    }
     
  }

}  
  mysql_close($db);
?>