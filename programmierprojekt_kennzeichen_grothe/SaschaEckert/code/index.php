<?php

# include and define some necessary functionality
include "htmloutput.php";
$htmloutput = new HTMLOutput();


function getData($par, $status)
{
    $json_data = @file_get_contents("http://www.sbeckert.de/ptdiff-kfz/index-db.php?status=$status&par=$par");
    $data = json_decode($json_data);
    $data = @get_object_vars($data[0]);
    return $data;
}



# some logic to fill string variables with html code

$message="";
$list="";
$wiki="";
$google="";

if(isset($_GET["status"]) && isset($_GET["par"]))
{
    $data = getData($_GET["par"], $_GET["status"]);
    $listdata = getData($_GET["par"], $_GET["status"]."short");


    # when fetching json data doesnt work, then we'll get "null"
    if($data == null)
    {
        $message="Couldn't fetch data.";
        $data=false;
    }
    if($listdata == null)
    {
        $message="Couldn't fetch data.";
        $listdata=false;
    }
    

    if($data != false || $listdata != false)
    {
        # wenn nicht genau ein treffer, dann liste
        if(sizeof($listdata) > 1 )
        {
            $list = $htmloutput->suggestions();
        }
        
        # wenn treffer, dann gmaps und wiki
        if($data != false)
        {
            $google = $htmloutput->gmapsEmbedding(urlencode($data["kreis_stadt"]));
            #$wiki = $htmloutput->wikiEmbedding($data["kreis_stadt"]);
        }
    }
}
?>



<!-- embed those variables into the following template -->

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="html-frontend/CSS/style.css" />
    </head>
    <body>
        <div id="body">
            <div id="center">
                <?php if($message!="")echo '<div class="others"> <hr>'.$message."</hr></div>"; ?>
                <form action="index.php" method="get">
                    <div class="kfzimage">
                        <input id="kfz" name="par" type="text" placeholder="---" maxlength="3"/>
                        <input id="query type" name="status" type="hidden" value="KEN"/>
                    </div>
                    <div class="others">
                        <input id="rightbutton"  type="submit" value="Suchen"/>
                    </div>
                </form>
                <br>
                <br>
                <form action="index.php" method="get">
                    <div class="others">
                        <input id="kreis" name="par" type="text" placeholder="Kreisstadt"/>
                        <input id="rightbutton"  type="submit" value="Suchen"/>
                        <input id="query type" name="status" type="hidden" value="KRE"/>
                    </div>
                </form>
                <?php if($list!="")echo '<div class="list"> <hr>'.$list."</div>"; ?>
                <?php if($wiki!="")echo '<div class="wikipedia"> <hr>'.$wiki."</div>"; ?>
                <?php if($google!="")echo '<div class="googlemaps"> <hr>'.$google."</div>"; ?>
            </div>
        </div>
    </body>
</html>
