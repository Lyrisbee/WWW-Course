<?php
header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set("Asia/Taipei");
/*if(isset($_POST['Buffersize'])){
    include('result.php');
}*/
?>
    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <title>CodeGeo - OL3</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="css/ol.css" media="all" type="text/css" rel="stylesheet">
        <link href="css/main.css" media="all" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">


        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="js/ol-debug.js" defer="defer"></script>
        <script src="js/answer.js" defer="defer"></script>

    </head>

    <body>
     <!--  <div id="mapa" ></div>  -->
         <h1>Train Station Query System</h1>
         <div class="form-group">
           <form id="myform" action="" method="post">
             <span>範圍設定，請介於 1 至 10 公里</span>
             <input type="number" id ="Buffersize" name="Buffersize" value="<?php if(isset($_POST['Buffersize']))echo $_POST['Buffersize'] ?>" maxlength="2" min="1" max="10" >
             <button type="submit" class="btn btn-info">提交</button>
           </form>
         </div>
          <div class="main">
           <div class ="map" id='mapa'></div>
           <div class="table">
             <table class="station table table-striped">
                 <tr>
                   <td>車站名稱</td>
                   <td>車站地址</td>
                   <td>距離(Km)</td>
                 </tr>
             </table>
           </div>

         </div>


    </body>









    </html>
