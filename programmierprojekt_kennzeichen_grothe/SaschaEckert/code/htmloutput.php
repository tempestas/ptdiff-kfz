<?php

# implement some routine to render HTML from the data
class HTMLOutput
{

    function suggestions($locationIDs)
    {
        $suggestions = "";
        
        $suggestions = "<table>\n";
        $suggestions = $suggestions."<tr><th>Kennzeichen</th><th>Landkreis</th><th>Kreisstadt</th><th>Bundesland</th></tr>\n";
        foreach($locationIDs as $l)
        {
            $suggestions = $suggestions."<tr>";
            $suggestions = $suggestions."<td>".$l["kreis_kurz"]."</td>"."<td>".$l["kreis_name"]."</td>"."<td>".$l["kreis_stadt"]."</td>"."<td>".$l["bundesland"]."</td>";
            $suggestions = $suggestions."</tr>\n";
        }
        $suggestions = $suggestions."</table>\n";
        
        return $suggestions;
    }

    function gmapsEmbedding($locationID)
    {
        $html = "<iframe width='425' height='350' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='https://maps.google.com/maps?&amp;q=$locationID&amp;output=embed'></iframe>";
        return $html;
    }
    
    function wikiEmbedding($locationID)
    {
        $html = "<iframe width='425' height='350' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='http://de.wikipedia.org/w/api.php?action=query&prop=extracts&titles=$locationID&exintro=1'></iframe>";
        return $html;
    }
}

?>