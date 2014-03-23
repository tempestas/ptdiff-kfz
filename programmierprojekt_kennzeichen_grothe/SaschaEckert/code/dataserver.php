<?php

# provide data access
class DataServer
{
    function getData($par, $status)
    {
        $json_data = file_get_contents("http://www.sbeckert.de/ptdiff-kfz/index-db.php?status=$status&par=$par");
        $data = json_decode($json_data); 
        $data = get_object_vars($data[0]);
        return $data;
    }
    
#     function getKennzeichen($kreis)
#     {
#         return null;
#     }
#     
#     function getKreis($kennzeichen)
#     {
#         return null;
#     }
}

?>