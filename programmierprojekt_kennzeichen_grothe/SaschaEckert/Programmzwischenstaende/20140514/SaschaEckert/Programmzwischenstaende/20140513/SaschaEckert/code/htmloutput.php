<?php

# implement some routine to render HTML from the data
class HTMLOutput
{

    function renderSuggestionsSection($locationIDs)
    {
        $html = "";
        
        $html = "<table id='list' onclick=\"SelectCountry()\">\n";
        $html = $html."<thead><tr><th>Kennzeichen</th><th>Landkreis</th><th>Kreisstadt</th><th>Bundesland</th></tr></thead><tbody>\n";
        foreach($locationIDs as $l)
        {
            $html = $html."<tr>";
            $html = $html."<td id='kurz'>".$l["kreis_kurz"]."</td>"."<td>".$l["kreis_name"]."</td>"."<td>".$l["kreis_stadt"]."</td>"."<td>".$l["bundesland"]."</td>";
            $html = $html."</tr>\n";
        }
        $html = $html."</tbody></table>\n";
        return $html;
    }
    
    function renderInfoSection($data)
    {
        $html = "";
        $html = "<table>\n";        
        
        $html = $html."<tr><td>Kennzeichen:</td>"."<td>".$data["kreis_kurz"]."</td></tr>\n";
        $html = $html."<tr><td>Landkreis:</td>"."<td>".$data["kreis_name"]."</td></tr>\n";
        $html = $html."<tr><td>Kreisstadt:</td>"."<td>".$data["kreis_stadt"]."</td></tr>\n";
        $html = $html."<tr><td>Bundesland:</td>"."<td>".$data["bundesland"]."</td></tr>\n";
        
        $html = $html."</table>\n";
        return $html;
    }

    function renderGmapsEmbedding($locationID)
    {
        $html = "<iframe width='425' height='350' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='https://maps.google.de/maps?q=$locationID&amp;output=embed'></iframe>";
        return $html;
    }
    
    function renderWikiEmbedding($locationID)
    {
        $data = array();
        $data = file_get_contents("http://de.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&titles=".$locationID."&exintro=1");
        $data = get_object_vars(json_decode($data));
        $data = get_object_vars($data[array_keys($data)[0]]);
        $data = get_object_vars($data[array_keys($data)[0]]);
        $data = get_object_vars($data[array_keys($data)[0]]);
        return $data["extract"];
    }
}

?>