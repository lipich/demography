<?php
$arrRegions = GetRegionList();

echo json_encode($arrRegions);

// получить список регионов
function GetRegionList()
{
    require_once("database.php");

    $select_regions = "select id, name from regions order by name";

    $db = database::getInstance();
    $result = $db->query($select_regions);

    $arrRegions = array();
    $i = 0;

    while ($type = mysqli_fetch_row($result))
    {
        $arrRegions[$i][0] = $type[0];
        $arrRegions[$i][1] = $type[1];

        $i++;
    }

    return $arrRegions;
}
?>