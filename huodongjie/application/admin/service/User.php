<?php


namespace app\admin\service;


class User
{

    function alert_errors($msg=''){
        $str='<script type="text/javascript" src="/static/admin/js/jquery.min.js"></script> <script src="/static/layuiadmin/layui/layui.js"></script>';//加载jquery和layer
        $str.='<script>
        layui.use("layer", function(){
          var layer = layui.layer;
          layer.alert("'.$msg.'",{icon:6,btn:["重新登录"],offset:"c",area:"300px",yes:function() {
                parent.location.reload();
          }});
        }); 
        </script>';//主要方法
        return $str;
    }
}