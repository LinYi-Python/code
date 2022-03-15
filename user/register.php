<?php
header("Content-type: text/html; charset=utf-8"); 
include 'checkinput.php';

$sHttp = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['HTTP_HOST'];

if(isset($_POST["sublogin"]))
{
      echo"<script type="."\""."text/javascript"."\"".">"."window.location="."\""."{$sHttp}/login.html"."\""."</script>";
}
$db = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

if ($db) {
  // echo "连接数据库成功！";
    $select=mysql_select_db(SAE_MYSQL_DB, $db);
	
  if($select)
  {
      //echo"选择数据库成功！";
      if(isset($_POST["sub"]))
      {
        $name=check_input($_POST["username"]);
        //  $name=check_input($name);
  // echo"<script type="."\""."text/javascript"."\"".">"."window.alert"."("."\""."$name"."\"".")".";"."</script>";
        $password1=check_input($_POST["password"]);//获取表单数据
        $password2=check_input($_POST["password2"]);
        if($name==""||$password1==""||$password2=="")//判断是否填写
        {
          // echo"<script type="."\""."text/javascript"."\"".">"."window.alert"."("."\""."请填写完成！"."\"".")".";"."</script>";
          // echo"<script type="."\""."text/javascript"."\"".">"."window.location="."\""."https://coderchain.cn/register.html"."\""."</script>";
          echo "<script>window.location='{$sHttp}/user/error-register.html#msg=empty'</script>";
          exit;
        }
       if($password1==$password2)//确认密码是否正确
      {
       $str="select usernamel from dengluyanzheng  where usernamel="."'"."$name"."'";
       $result=mysql_query($str);
       $pass=mysql_fetch_row($result);
        
        if($pass)//判断数据库表中是否已存在该用户名
        {
         
        // echo"<script type="."\""."text/javascript"."\"".">"."window.alert"."("."\""."该用户名已被注册"."\"".")".";"."</script>";
        // echo"<script type="."\""."text/javascript"."\"".">"."window.location="."\""."https://coderchain.cn/register.html"."\""."</script>";
        echo "<script>window.location='{$sHttp}/user/error-register.html#msg=exists'</script>";

        exit; 
        } 
         
         
        $sql="insert into dengluyanzheng(usernamel,password) values("."\""."$name"."\"".","."\""."$password1"."\"".")";//将注册信息插入数据库表中
        //echo"$sql";
         $res_insert =mysql_query($sql);
      //  mysql_query('SET NAMES UTF8');
      // mysql_close($link);
        if($res_insert)
        {
          //echo"数据库关闭";
          //echo"注册成功！";
           //  echo"<script type="."\""."text/javascript"."\"".">"."window.alert"."("."\""."注册成功"."\"".")".";"."</script>";
            // echo"<script type="."\""."text/javascript"."\"".">"."window.location="."\""."https://coderchain.cn/success.html?register=1"."\""."</script>";
             echo "<script>window.location='https://coderchain.cn/user/success-register.html'</script>";
             mysql_close($link);
        }
       }
        else
        {
          // echo"<script type="."\""."text/javascript"."\"".">"."window.alert"."("."\""."密码不一致！"."\"".")".";"."</script>";
          // echo"<script type="."\""."text/javascript"."\"".">"."window.location="."\""."https://coderchain.cn/register.html"."\""."</script>";
          echo "<script>window.location='https://coderchain.cn/user/error-register.html#msg=notequal'</script>";
        }
      }
    }
  }
?>
