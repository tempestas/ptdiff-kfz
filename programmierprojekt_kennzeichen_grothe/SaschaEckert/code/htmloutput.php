<?php

# implement some routine to render HTML from the data
class HTMLOutput
{

    function suggestions() #($locationIDs)
    {
        return "";
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