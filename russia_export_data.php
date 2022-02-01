<?php
require("russia_general_data.php");
require("russia_age_data.php");

if (isset($_GET['regions']))
{
    // читаем параметры данных
    $data_type = $_GET['data_type'];
    $regions = $_GET['regions'];
    $start_year = $_GET['start_year'];
    $end_year = $_GET['end_year'];
    $start_age = $_GET['start_age'];
    $end_age = $_GET['end_age'];
    $sex = $_GET['sex'];
    $report_type = $_GET['report_type'];

    $FileName = $data_type . "_demography_" . $report_type . "_regions(" . $regions . ")_years(" . $start_year . "_" . $end_year . ")" . '.csv';
    $Content = "";

    // экспорт общих данных
    if ($data_type == "general")
    {
        // корректируем год начала
        $min_year = GetMortalityMinYearRegion($regions);
        if ($start_year < $min_year) $start_year = $min_year;

        // получаем данные для отчета
        $arrData = GetGeneralReportData($report_type, $regions, $start_year, $end_year, $start_age, $end_age, $sex);

        # заголовок файла CSV
        $Content = "Region";
        for ($year = $start_year; $year <= $end_year; $year++)
        {
            $Content .= ";" . $year;
        }

        $Content .= " \n";

        foreach ($arrData as $region)
        {
            $Content .= "\"" . iconv('utf-8', 'windows-1251', $region[0]) . "\"";

            for ($i = 1; $i <= $end_year - $start_year + 1; $i++)
            {
                if ($report_type == "population")
                    $Content .= ";" . str_replace('-1', '', number_format($region[$i], 0, ',', ' '));
                if ($report_type == "population_urban")
                    $Content .= ";" . str_replace('-1', '', number_format($region[$i], 0, ',', ' '));
                if ($report_type == "part")
                    $Content .= ";" . str_replace('-1,00', '', number_format($region[$i], 2, ',', ' '));
                if ($report_type == "olders")
                    $Content .= ";" . str_replace('-1,00', '', number_format($region[$i], 2, ',', ' '));
                if ($report_type == "life")
                    $Content .= ";" . str_replace('-1,00', '', number_format($region[$i], 2, ',', ' '));
                if ($report_type == "life_urban")
                    $Content .= ";" . str_replace('-1,00', '', number_format($region[$i], 2, ',', ' '));
            }

            $Content .= " \n";
        }
    }

    // экспорт данных возрастной структуры
    if ($data_type == "age")
    {
        // получаем данные для отчета
        $arrData = GetAgeReportData($report_type, $regions, $start_year);

        # заголовок файла CSV
        $Content = "age;male;female \n";

        foreach ($arrData as $data)
        {
            $Content .= $data[0] . ";" . $data[1] . ";" . $data[2] . " \n";
        }
    }

    header('Content-type: application/octet-stream');
    //header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename="' . $FileName . '"');

    echo $Content;
    exit();
}
?>