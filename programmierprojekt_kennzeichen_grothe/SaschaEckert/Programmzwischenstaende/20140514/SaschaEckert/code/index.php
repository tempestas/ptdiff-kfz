<?php

# include and define some necessary functionality
include "htmloutput.php";
$htmloutput = new HTMLOutput();

function getData($par, $status)
{    
	if($par==null || $par=='')
    $json_data = @file_get_contents("http://www.sbeckert.de/ptdiff-kfz/index-db.php?status=$status&par=");
    else
	$json_data = @file_get_contents("http://www.sbeckert.de/ptdiff-kfz/index-db.php?status=$status&par=$par");
    
  # return null if, json_data is broken
    if($json_data == null)
    {
        return null;
    }
    
    # otherwise go on with further treatment
    $data = json_decode($json_data);

    if(sizeof($data) == 1)
    {
        $tmp[0] = @get_object_vars($data[0]);
        $data = $tmp;
    }
    
    if(sizeof($data) > 1)
    {
        $temp=array();
        for($i = 0; $i < sizeof($data); $i++)
        {
            $temp[$i]=@get_object_vars($data[$i]);
        }
        $data = $temp;
    }
    
    return $data;
}



# some logic to fill string variables with html code

$message="";
$list="";
$wiki="";
$info="";
$google="";

$status="";
$par="";
$country='<select id="middle" name="BDLpar"><option  value="" selected="selected">Bundesland</option>';
$countryList=getData('',"BDLshort");
foreach($countryList as $bundarr){
	foreach($bundarr as $key=>$bund){
		$country.='<option value="'.$bund.'">'.$bund.'</option>';
	}
}
$country.='</select>';
if(isset($_GET["KENpar"]) || isset($_GET["KREpar"]) || isset($_GET["BDLpar"]))
{    
    if($_GET["KENpar"] != "")
    {
        $status = "KEN";
        $par = $_GET["KENpar"];
    }
	else if($_GET["BDLpar"] != "")
    {
        $status = "BDL";
        $par = $_GET["BDLpar"];
    }
    else
    {
        $status = "KRE";
        $par = $_GET["KREpar"];
    }
    
    $data = getData($par, $status);
    $listdata = getData($par, $status."long");


    # when fetching json data doesnt work, then we'll get "null"
    if($data == null || $listdata == null)
    {
        $message="Couldn't fetch data.";
        $data=false;
        $listdata=false;
    }
    
    # wenn nicht genau ein treffer, dann liste
    if($listdata != false &&( sizeof($listdata) > 1 || $_GET["BDLpar"] != "") )
    {
		$flag=false;
		if($_GET["BDLpar"] != ""){
			$flag=true;
			$data=false;
		}
		$list =$htmloutput->renderSuggestionsSection($listdata,$flag);
		
    }
    
    # wenn treffer, dann gmaps und wiki
    if($data != false && $data[0] != null)
    {
        $google = $htmloutput->renderGmapsEmbedding($data[0]["kreis_stadt"]);
        $info = $htmloutput->renderInfoSection(array_slice($data[0], 1));
//        $wiki = $htmloutput->renderWikiEmbedding($data[0]["kreis_stadt"]); // ausgabe gibt eine liste bei nicht eindeudikeit durch leerzeichen (es wird der suchbegriff abgeschnitten)

		$kreisstadt = str_replace(" ","_",$data[0]["kreis_stadt"]);
		$wiki = $htmloutput->renderWikiEmbedding($kreisstadt);
    }
}
?>


<!-- embed those variables into the following template -->

<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="html-frontend/CSS/style.css" />
        <script type="text/javascript">
            function CheckInput ()
            {
                if(document.forms[0].elements["kfz"].value == "" && document.forms[0].elements["kreis"].value == "")
                {
                    return false;
                }
                
                if(document.forms[0].elements["kfz"].value != "" && document.forms[0].elements["kreis"].value != "")
                {
                    alert("Bitte genau ein Feld ausfüllen.");
                    document.forms[0].reset();
                    return false;
                }
                return true;
            }
				function SelectCountry(e){
				var elem, evt = e ? e:event;
 if (evt.srcElement)  elem = evt.srcElement;
 else if (evt.target) elem = evt.target;
 while (elem && elem.nodeName !== "TR") {
        elem = elem.parentNode;
    }
 if (elem) {
						var cells = elem.getElementsByTagName("td");
						for (var i = 0; i < cells.length; i++) {
							if(cells[i].id=="kurz")
							document.getElementById('kfz').value = cells[i].children[0].value;
						}
					}
 return true;
				};
			
        </script>
    </head>
    <body>
        <div id="body">
            <?php if($list!="")echo '<div id="suggestions" class="wikipedia">'.$list."</div>"; ?>
            <div id="center">
                <?php if($message!="")echo '<div class="others"> <hr>'.$message."</hr></div>"; ?>
                <form action="index.php" method="get" onsubmit="CheckInput();">
                    <div class="kfzimage">
                        <?php
                            if($status == "KEN")
                            {echo '<input id="kfz" name="KENpar" type="text" value="'.$par.'" placeholder="---" maxlength="3"/>';}
                            else
                            {echo '<input id="kfz" name="KENpar" type="text" placeholder="---" maxlength="3"/>';}
                        ?>
                        <!-- <input id="kfz flag" name="status" type="hidden" value="KEN"/> -->
                    </div>
                    <div class="others">
                        <input id="rightbutton"  type="submit" value="Suchen"/>
                    </div>
					
                    <div class="others">
                        <?php
                            if($status == "KRE")
                            {echo '<input id="kreis" name="KREpar" type="text" value="'.$par.'" placeholder="Kreisstadt"/>';}
                            else
                            {echo '<input id="kreis" name="KREpar" type="text" placeholder="Kreisstadt"/>';}
                        ?>
                        <?php   echo $country;?><input id="rightbutton"  type="submit" value="Suchen"/>
                        <!-- <input id="kreis flag" name="status" type="hidden" value="KRE"/> -->
                    </div>
                </form>
                <?php if($google!="")echo '<div class="googlemaps"> <hr>'.$google."</div>"; ?>
                <?php if($info!="")echo '<div class="wikipedia"> <hr>'.$info."</div>"; ?>
                <?php if($wiki!="")echo '<div class="wikipedia"> <hr>'.$wiki."</div>"; ?>
            </div>
        </div>
    </body>
</html>
