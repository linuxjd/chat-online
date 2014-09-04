<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();

if (login_check($mysqli) == false) {
    echo "<script>";
    echo "window.location.href='login.php'";
    echo "</script>";

}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>user lists</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <style>
    li {
        display:block;
        list-style:none;
    }
  </style>
</head>
<body>
<?php
    $sql = "SELECT id,username FROM members";
    if($result = $mysqli->query($sql)) {
        echo "<di style='margin-top:200px'>"; 
        while($row = $result->fetch_object()) {
            if(htmlentities($_SESSION['user_id']) == $row->id)
                continue;
            echo '<div class="row">';
            echo "<div class='span2 offset2'>".$row->username."</div>";
            echo '<div class="span2">';
            echo '<a class="btn follow" href="javascript:void(0);" action-data="'.$row->id.'" action-type="follow">';
            echo '<span>关注</span></div></a></div>';
        }
        echo "</div>";
    }
    $result->close();
?>

<script>
$(document).ready(function(){
    $(".follow").click(function(){
        var follow_btn = $(this).children("span");
        var uid = <?php echo htmlentities($_SESSION['user_id'])?>;
        var link = {
                "uid":uid,
                "followid":$(this).attr("action-data"),
                "type":1
        };
        var url = "includes/follow.php";
        console.log(link);
        if("关注" == follow_btn.text()) {
            //action=0:关注
            link.action=0;
            $.ajax({
                type:'POST',
                url:url,
                data:link,
                success: function (data,textStatus) {
                    if("true" == data)
                        follow_btn.text("取消关注"); 
                    else
                        console.log(data);
                },
                error: function(XMLHttpRequest,textStatus,errorThrown){
                    console.log(XMLHttpRequest,textStatus,errorThrown);
                }
            });
        } else {
            //取消关注
            link.action=1;
            $.ajax({
                type:'POST',
                url:url,
                data:link,
                success:function(data){
                    if("true" == data)
                        follow_btn.text("关注"); 
                    else
                        console.log(data);
                },
                error: function(XMLHttpRequest,textStatus,errorThrown){
                    console.log(XMLHttpRequest,textStatus,errorThrown);
                }
            });
        }
    });
});
</script>
</body>
</html>
