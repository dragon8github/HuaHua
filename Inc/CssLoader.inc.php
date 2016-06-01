<?php

class CssLoader
{
    private $CSS_ROOT = ""; //SRC目录的路径

    public static function Jqm()
    {
        //...
        echo sprintf("<link rel='stylesheet' href='%s'  />","https://cdn.bootcss.com/jquery-mobile/1.4.4/jquery.mobile.min.css");
    }
    
    public static function LoadCss($folder,$name)
    {
        echo sprintf("<link rel='stylesheet' href='%s'  />",$_SESSION["STATIC_ROOT"].'/Css/'.$folder.'/'.$name);
    }

}


?>