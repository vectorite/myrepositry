<?php 
$request=$_SERVER['REQUEST_URI'];

$arrMoved=array("/video.php"=>"/technical-information/installation-videos.html",
            "/old_path/oldname.php"=>"/new_path/newname.php");

if(array_key_exists($request,$arrMoved))
    {
    $newplace="http://".$_SERVER['HTTP_HOST'].$arrMoved[$request];
    header("HTTP/1.0 301 Moved Permanently");
    header("Location: $newplace");
    header("Connection: close");
    exit();
    }
else
    {
    header("HTTP/1.0 404 Not Found");
    }

?>

<!-- 
Your normal HTML code goes here since if a match is found the visitor will have been redirected. Only genuine 404 errors will see the HTML below.
 -->

<html>
  <head>
    <title>404 Error page</title>
  </head>
  <body>
    <p>Sorry but this page isn't here.</p>
  </body>
</html>