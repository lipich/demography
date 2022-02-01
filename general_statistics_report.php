<?php
session_start();

require("config.php");
include_once("general_data.php");

if (isset($_SESSION['lang']))
    $lang = $_SESSION['lang'];
else
    $lang = 'ru';

switch($lang)
{
    case 'ru':
        include("content_ru.php");
        break;
    case 'en':
        include("content_en.php");
        break;
}

if (isset($_GET['countries']))
{
    // читаем параметры данных
    $countries = $_GET['countries'];
    $start_year = $_GET['start_year'];
    $end_year = $_GET['end_year'];
    $start_age = $_GET['start_age'];
    $end_age = $_GET['end_age'];
    $sex = $_GET['sex'];
    $report_type = $_GET['report_type'];

    if ($countries == "null")
    {
        echo "<br /><b>" . $content_no_data_message. "</b>";
        exit;
    }

    // корректируем год начала
    $min_year = GetMortalityMinYearCountry($countries);
    if ($start_year < $min_year) $start_year = $min_year;

    // получаем данные для отчета
    $arrData = GetGeneralReportData($report_type, $countries, $start_year, $end_year, $start_age, $end_age, $sex);

    // формируем таблицу результата
    $res = "<table class='report_table'>";
    $res = $res . "<tr>";
    $res = $res . "<td class='head_table' width='200px'>" . $content_country . "</td>";

    for ($year = $start_year; $year <= $end_year; $year++)
    {
        $res = $res . "<td class='head_table' align='right'>" . $year . "</td>";
    }

    $res = $res . "</tr>";

    foreach ($arrData as $country)
    {

        if (($country[0] == "Россия") || ($country[0] == "Russia"))
        {
            $res = $res . "<tr style='font-weight: bold;'>";
        }
        else
        {
            $res = $res . "<tr>";
        }

        $res = $res . "<td class='head_table' nowrap>" . $country[0] . "</td>";

        for ($i = 1; $i <= $end_year - $start_year + 1; $i++)
        {
            if ($report_type == "population")
                $res = $res . "<td class='td_numeric' align='right' nowrap>" . str_replace('-1', '&nbsp;', number_format($country[$i], 0, ',', ' ')) . "</td>";
            if ($report_type == "part")
                $res = $res . "<td class='td_numeric' align='right' nowrap>" . str_replace('-1,00', '&nbsp;', number_format($country[$i], 2, ',', ' ')) . "</td>";
            if ($report_type == "olders")
                $res = $res . "<td class='td_numeric' align='right' nowrap>" . str_replace('-1,00', '&nbsp;', number_format($country[$i], 2, ',', ' ')) . "</td>";
            if ($report_type == "life")
                $res = $res . "<td class='td_numeric' align='right' nowrap>" . str_replace('-1,00', '&nbsp;', number_format($country[$i], 2, ',', ' ')) . "</td>";
            if ($report_type == "dead")
                $res = $res . "<td class='td_numeric' align='right' nowrap>" . str_replace('-1', '&nbsp;', number_format($country[$i], 0, ',', ' ')) . "</td>";
        }
        $res = $res . "</tr>";
    }

    $res = $res . "</table><br />";

    echo $res;
}
?>