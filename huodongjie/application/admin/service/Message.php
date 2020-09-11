<?php


namespace app\admin\service;


class Message
{
    function alert_errors($msg='',$time=3){
        $str='<script type="text/javascript" src="/static/admin/js/jquery.min.js"></script> <script src="/static/layuiadmin/layui/layui.js"></script>';//加载jquery和layer
        $str.='<script>
        layui.use("layer", function(){
          var layer = layui.layer;
          layer.msg("'.$msg.'",{icon:"5",time:'.($time*1000).',offset:"c",area:["280px","50px"]});
            setTimeout(function(){
                     exit();
            },2000)
        }); 
        </script>';//主要方法
        return $str;
    }
}