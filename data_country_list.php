<?php
$arrCountries = GetCountryList();

echo json_encode($arrCountries);

// получить список стран
function GetCountryList()
{
    require_once("database.php");

    /*if (isset($_SESSION['type']))
        $type = $_SESSION['type'];
    else
        $type = 'world';*/

    $select_countries = "select id, name from countries";
    /*global $type;
    if ($type == "g20") $select_countries .= " where g20 = 1";*/
    $select_countries .= " order by name";

    $db = database::getInstance();
    $result = $db->query($select_countries);
    
    $arrCountries = array();
    $i = 0;

    while ($type = mysqli_fetch_row($result))
    {
        $arrCountries[$i][0] = $type[0];
        $arrCountries[$i][1] = $type[1];

        $i++;
    }

    return $arrCountries;
}
?>
 
