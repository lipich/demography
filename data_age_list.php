<?php
$ages = GetAgeList();

echo json_encode($ages);

// получить список дозрастов за которые есть данные
function GetAgeList()
{
    require_once("database.php");

    // запрос к базе
    $select_age = "select distinct t.age from mortality t order by t.age";

    $db = database::getInstance();
    $result = $db->query($select_age);

    $ages = array();
    $i = 0;

    while ($row = mysqli_fetch_row($result))
    {
        foreach ($row as $item)
        {
            $ages[$i] = $item;
            $i++;
        }
    }

    return $ages;
}
?>