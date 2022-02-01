<?php
if(!session_id()) session_start();

if (isset($_SESSION['lang']))
    $lang = $_SESSION['lang'];
else
    $lang = 'ru';

// получить массив регионов
function GetRegions()
{
    $arrRegions = array();

    require_once("database.php");

    global $lang;

    switch($lang)
    {
        case 'ru':
            $select_regions = "select id, name from regions order by name";
            break;
        case 'en':
            $select_regions = "select id, name_en from regions order by name";
            break;
    }


    $db = database::getInstance();
    $result = $db->query($select_regions);

    $i = 0;

    while ($type = mysqli_fetch_row($result))
    {
        $arrRegions[$i][0] = $type[0];
        $arrRegions[$i][1] = $type[1];

        $i++;
    }

    return $arrRegions;
}

// получить массив стран по указанным идентификаторам
function GetRegionName($region)
{
    require_once("database.php");

    global $lang;

    switch($lang)
    {
        case 'ru':
            $select_regions = "select name from regions where id in (" . $region . ")";
            break;
        case 'en':
            $select_regions = "select name_en from regions where id in (" . $region . ")";
            break;
    }

    $db = database::getInstance();
    $result = $db->query($select_regions);

    while ($type = mysqli_fetch_row($result))
    {
        $res = $type[0];
    }

    return $res;
}

// получить массив регионов по указанным идентификаторам
function GetRegionsNames($region)
{
    $arrRegions = array();

    require_once("database.php");

    global $lang;

    switch($lang)
    {
        case 'ru':
            $select_regions = "select name from regions where id in (" . $region . ") order by name";
            break;
        case 'en':
            $select_regions = "select name_en from regions where id in (" . $region . ") order by name";
            break;
    }

    $db = database::getInstance();
    $result = $db->query($select_regions);

    $i = 0;

    while ($type = mysqli_fetch_row($result))
    {
        $arrRegions[$i] = $type[0];
        
        $i++;
    }

    return $arrRegions;
}

// получить глубину данных по всей таблице смертности
function GetMortalityMinYear()
{
    require_once("database.php");

    // запрос к базе
    $select_min_year = "select min(year) min_year from mortality_russia";

    $db = database::getInstance();
    $result = $db->query($select_min_year);

    while ($type = mysqli_fetch_row($result))
    {
        $res = $type[0];
    }

    return $res;
}

// получить глубину данных по всей таблице смертности для выбраных регионов
function GetMortalityMinYearRegion($region)
{
    require_once("database.php");

    // запрос к базе
    $select_min_year = "select min(year) min_year from mortality_russia where region in (" . $region . ")";

    $db = database::getInstance();
    $result = $db->query($select_min_year);

    while ($type = mysqli_fetch_row($result))
    {
        $res = $type[0];
    }

    return $res;
}

// Получить основные данные для отчета
function GetGeneralReportData($report_type, $regions, $start_year, $end_year, $start_age, $end_age, $sex)
{
    $arrData = array();

    require_once("database.php");

    // формируем запрос к базе
    $select_data = "select name, ";

    // вариант запроса без функций
    for ($year = $start_year; $year <= $end_year; $year++)
    {
        if ($report_type == "population")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(count_man + count_woman) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(count_man) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(count_woman) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "population_urban")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(count_man_urban + count_woman_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(count_man_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(count_woman_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "population_rural")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(count_man + count_woman) - sum(count_man_urban + count_woman_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(count_man) - sum(count_man_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(count_woman) - sum(count_woman_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "part")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(count_man + count_woman) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality_russia t_m where region = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(count_man) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality_russia t_m where region = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(count_woman) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality_russia t_m where region = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "olders")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(count_man + count_woman) from mortality_russia t_m where region = id and year = " . $year . " and age between 60 and 110)/(select sum(count_man + count_woman) from mortality_russia t_m where region = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(count_man) from mortality_russia t_m where region = id and year = " . $year . " and age between 60 and 110)/(select sum(count_man) from mortality_russia t_m where region = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(count_woman) from mortality_russia t_m where region = id and year = " . $year . " and age between 60 and 110)/(select sum(count_woman) from mortality_russia t_m where region = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "life")
            switch ($sex)
            {
                case 'both':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum((lx_man + lx_woman)/2)/(select (lx_man + lx_woman)/2 from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
                case 'man':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum(lx_man)/(select lx_man from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum(lx_woman)/(select lx_woman from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "life_urban")
            switch ($sex)
            {
                case 'both':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum((lx_man_urban + lx_woman_urban)/2)/(select (lx_man_urban + lx_woman_urban)/2 from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
                case 'man':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum(lx_man_urban)/(select lx_man_urban from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum(lx_woman_urban)/(select lx_woman_urban from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
            }

        if ($year != $end_year) $select_data .= ", ";
    }

    // условие выборки
    $select_data .= "from regions where id in (" . $regions . ") order by name";

    $db = database::getInstance();
    $result = $db->query($select_data);

    $c_region = 0;

    while ($type = mysqli_fetch_row($result))
    {
        for ($i = 0; $i <= $end_year - $start_year + 1; $i++)
        {
            $arrData[$c_region][$i] = $type[$i];
        }

        $c_region++;
    }

    return $arrData;
}

// Получить основные данные для отчета
function GetGeneralReportDataDetailed($report_type, $regionsDetailed, $start_year, $end_year)
{
    $arrData = array();

    require_once("database.php");

    global $lang;

    $select_data = '';
    $i = 0;

    foreach ($regionsDetailed as $regionData)
    {
        $region = $regionData[0];
        $start_age = $regionData[1];
        $end_age = $regionData[2];
        $sex = $regionData[3];

        // формируем запрос к базе
        switch($lang)
        {
            case 'ru':
                $select_data .= "select name, ";
                break;
            case 'en':
                $select_data .= "select name_en, ";
                break;
        }

        // вариант запроса без функций
        for ($year = $start_year; $year <= $end_year; $year++)
        {
            if ($report_type == "population")
                switch ($sex)
                {
                    case 'both':
                        $select_data .= "ifnull((select sum(count_man + count_woman) from mortality_russia where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(count_man) from mortality_russia where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(count_woman) from mortality_russia where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "population_urban")
                switch ($sex)
                {
                    case 'both':
                        $select_data .= "ifnull((select sum(count_man_urban + count_woman_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(count_man_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(count_woman_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "population_rural")
                switch ($sex)
                {
                    case 'both':
                        $select_data .= "ifnull((select sum(count_man + count_woman) - sum(count_man_urban + count_woman_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(count_man) - sum(count_man_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(count_woman) - sum(count_woman_urban) from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "part")
                switch ($sex)
                {
                    case 'both':
                        $select_data .= "ifnull((select sum(count_man + count_woman) from mortality_russia  where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality_russia where region = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(count_man) from mortality_russia  where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality_russia where region = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(count_woman) from mortality_russia where region = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality_russia where region = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "olders")
                switch ($sex)
                {
                    case 'both':
                        $select_data .= "ifnull((select sum(count_man + count_woman) from mortality_russia where region = id and year = " . $year . " and age between 60 and 110)/(select sum(count_man + count_woman) from mortality_russia where region = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(count_man) from mortality_russia where region = id and year = " . $year . " and age between 60 and 110)/(select sum(count_man) from mortality_russia where region = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(count_woman) from mortality_russia where region = id and year = " . $year . " and age between 60 and 110)/(select sum(count_woman) from mortality_russia where region = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "life")
                switch ($sex)
                {
                    case 'both':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum((lx_man + lx_woman)/2)/(select (lx_man + lx_woman)/2 from mortality_russia where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum(lx_man)/(select lx_man from mortality_russia where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum(lx_woman)/(select lx_woman from mortality_russia where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "life_urban")
                switch ($sex)
                {
                    case 'both':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum((lx_man_urban + lx_woman_urban)/2)/(select (lx_man_urban + lx_woman_urban)/2 from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum(lx_man_urban)/(select lx_man_urban from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum(lx_woman_urban)/(select lx_woman_urban from mortality_russia t_m where region = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality_russia t_m where region = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                }

            if ($year != $end_year) $select_data .= ", ";
        }

        $select_data .= ", " . $i . " as f_order ";

        $select_data .= "from regions where id in (" . $region . ")";
        $select_data .= " union ";

        $i++;
    }

    // отсекаем последний union
    $select_data = substr($select_data, 0, strlen($select_data) - 7);

    // сортировка
    $select_data = "select * from (" . $select_data . ") as result order by f_order";

    $db = database::getInstance();
    $result = $db->query($select_data);

    $c_region = 0;

    while ($type = mysqli_fetch_row($result))
    {
        for ($i = 0; $i <= $end_year - $start_year + 1; $i++)
        {
            $arrData[$c_region][$i] = $type[$i];
        }

        $c_region++;
    }

    return $arrData;
}
?>