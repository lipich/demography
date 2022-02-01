<?php
// Получить данные по смертности для теплокарты
function GetMortalityMapData($country, $start_year, $end_year, $start_age, $end_age, $sex)
{
    $arrMortality = array();

    require_once("database.php");

    $select_data = "select ";

    switch ($sex)
    {
        case 'man':
            for ($year = $start_year; $year <= $end_year; $year++)
            {
                $select_data .= "((1 - (select tq1_1.lx_man from mortality tq1_1 where tq1_1.country = " . $country . " and tq1_1.year = " . $year . " and tq1_1.age = t_m.age + 1)/
                                       (select tq1_2.lx_man from mortality tq1_2 where tq1_2.country = " . $country . " and tq1_2.year = " . $year . " and tq1_2.age = t_m.age))
                                       /
                                       (1 - (select tq2_1.lx_man from mortality tq2_1 where tq2_1.country = " . $country . " and tq2_1.year = " . $year . " - 1 and tq2_1.age = t_m.age + 1)/
                                       (select tq2_2.lx_man from mortality tq2_2 where tq2_2.country = " . $country . " and tq2_2.year = " . $year . " - 1 and tq2_2.age = t_m.age)) - 1) * 100 as '" . $year . "' ";

                if ($year != $end_year) $select_data .= ", ";
            }
            break;
        case 'woman':
            for ($year = $start_year; $year <= $end_year; $year++)
            {
                $select_data .= "((1 - (select tq1_1.lx_woman from mortality tq1_1 where tq1_1.country = " . $country . " and tq1_1.year = " . $year . " and tq1_1.age = t_m.age + 1)/
                                       (select tq1_2.lx_woman from mortality tq1_2 where tq1_2.country = " . $country . " and tq1_2.year = " . $year . " and tq1_2.age = t_m.age))
                                       /
                                       (1 - (select tq2_1.lx_woman from mortality tq2_1 where tq2_1.country = " . $country . " and tq2_1.year = " . $year . " - 1 and tq2_1.age = t_m.age + 1)/
                                       (select tq2_2.lx_woman from mortality tq2_2 where tq2_2.country = " . $country . " and tq2_2.year = " . $year . " - 1 and tq2_2.age = t_m.age)) - 1) * 100 as '" . $year . "' ";

                if ($year != $end_year) $select_data .= ", ";
            }
            break;
    }

    $select_data .= "from mortality t_m
                     where t_m.country = " . $country . " and t_m.year = " . $start_year . " and t_m.age between " . $start_age . " and " . $end_age . "
                     order by t_m.age desc";

    $db = database::getInstance();
    $result = $db->query($select_data);

    $c_age = 0;

    while ($type = mysqli_fetch_row($result))
    {
        for ($i = 0; $i <= $end_year - $start_year; $i++)
        {
            $arrMortality[$c_age][$i] = $type[$i];
        }

        $c_age++;
    }

    return $arrMortality;
}
?>