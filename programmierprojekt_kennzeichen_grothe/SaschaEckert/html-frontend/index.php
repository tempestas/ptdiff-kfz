<?php
$list="";
$wiki="";
$google="";
        if(isset($_GET["kurz"])||isset($_GET["kreis"]))
        {
               $kurz="";
               if(isset($_GET["kurz"])){
                        $kurz=$_GET["kurz"];
               }
               $kreis="";
               if(isset($_GET["kreis"])){
                        $kreis=$_GET["kreis"];
               }
               if($kurz=="ERB"||$kreis=="Erbach"){
                     $list="<p>Erbach</p>" ;
                     $google=' <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.de/maps?q=Erbach&amp;z=11&amp;ll=49.659025,8.996182&amp;iwloc=near&amp;output=embed"></iframe><br /><small><a href="https://maps.google.de/maps?client=opera&amp;q=Erbach&amp;oe=utf-8&amp;channel=suggest&amp;ie=UTF8&amp;hq=&amp;hnear=Erbach,+Darmstadt,+Hessen&amp;t=m&amp;z=11&amp;ll=49.659025,8.996182&amp;source=embed" style="color:#0000FF;text-align:left">Größere Kartenansicht</a></small>';
                     $wiki='<p>Erbach ist die Kreisstadt des Odenwaldkreises in Hessen.</p><p><a href="http://de.wikipedia.org/wiki/Erbach_(Odenwald)" target="_blank">Erbach</a></p>';
               }
        }
?>

<html>
         <head>
                <link rel="stylesheet" type="text/css" href="CSS/style.css" />

         </head>
         <body>
                 <div id="body">
                          <div id="center">
                          <form action="index.php" method="get">
                                 <div class="kfzimage"><input id="kfz" name="kurz" type="text" placeholder="---" maxlength="3"/></div>
                                 <div class="others"><input id="kreis" name="kreis" type="text" placeholder="Kreisstadt"/><input id="rightbutton"  type="submit" value="Suchen"/></div>
                          </form>
                          <div class="list">
                          <?php
                            if($list!="")echo "<hr>".$list;
                          ?>
                          </div>
                          <div class="wikipedia">
                          <?php
                            if($wiki!="")echo "<hr>".$wiki;
                          ?>
                          </div>
                          <div class="googlemaps">
                         <?php
                            if($google!="")echo "<hr>".$google;
                          ?>
                          </div> </div>
                 </div>
         </body>
</html>
