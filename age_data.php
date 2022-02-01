<?php
// Получить диапазон возможных лет для указанной страны
function GetYearRange($country)
{
    $arrYears = array();

    require_once("database.php");

    $select_years = "select min(year) min_year, max(year) max_year from mortality where country = " . $country;

    $db = database::getInstance();
    $result = $db->query($select_years);

    while ($type = mysqli_fetch_row($result))
    {
          $arrYears[0] = $type[0];
          $arrYears[1] = $type[1];
    }

    return $arrYears;
}

// Получить данные для графика половозрастной структуры
function GetAgeReportData($report_type, $country, $year)
{
    $arrAgeData = array();

    require_once("database.php");

    switch ($report_type)
    {
        case "population":
            $select_age_statistics = "select age, count_man, count_woman from mortality where country = " . $country . " and year = " . $year;
            break;
        case "dead":
            $select_age_statistics = "select age, dead_man, dead_woman from mortality where country = " . $country . " and year = " . $year;
            break;
    }

    $db = database::getInstance();
    $result = $db->query($select_age_statistics);

    $i = 0;

    while ($type = mysqli_fetch_row($result))
    {
        $arrAgeData[$i][0] = $type[0];
        $arrAgeData[$i][1] = $type[1];
        $arrAgeData[$i][2] = $type[2];

        $i++;
    }

    return $arrAgeData;
}

// Получить данные для легенды графика половозрастной структуры
function GetAgeLegendData($report_type, $country, $year)
{
    $arrAgeLegendData = array();

    require_once("database.php");

    switch ($report_type)
    {
        case "population":
            $select_age_statistics = "select sum(count_man) / 1000, sum(count_woman) / 1000 from mortality where country = " . $country . " and year = " . $year;
            break;
        case "dead":
            $select_age_statistics = "select sum(dead_man) / 1000, sum(dead_woman) / 1000 from mortality where country = " . $country . " and year = " . $year;
            break;
    }

    $db = database::getInstance();
    $result = $db->query($select_age_statistics);

    while ($type = mysqli_fetch_row($result))
    {
        $arrAgeLegendData[0] = $type[0];
        $arrAgeLegendData[1] = $type[1];
    }

    return $arrAgeLegendData;
}
?>