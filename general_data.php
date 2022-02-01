<?php
if(!session_id()) session_start();

// настройки языка
if (isset($_SESSION['lang']))
    $lang = $_SESSION['lang'];
else
    $lang = 'ru';

// получить массив стран
function GetCountries($filter)
{
    $arrCountries = array();

    require_once("database.php");

    global $lang;

    switch($lang)
    {
        case 'ru':
            $select_countries = "select id, name from countries";
            break;
        case 'en':
            $select_countries = "select id, name_en from countries";
            break;
    }

    if ($filter == "g20") $select_countries .= " where g20 = 1";

    switch($lang)
    {
        case 'ru':
            $select_countries .= " order by name";
            break;
        case 'en':
            $select_countries .= " order by name_en";
            break;
    }

    $db = database::getInstance();
    $result = $db->query($select_countries);

    $i = 0;

    while ($type = mysqli_fetch_row($result))
    {
        $arrCountries[$i][0] = $type[0];
        $arrCountries[$i][1] = $type[1];

        $i++;
    }

    return $arrCountries;
}

// получить имя страны по укзанному идентификатору
function GetCountryName($country)
{
    require_once("database.php");

    global $lang;

    switch($lang)
    {
        case 'ru':
            $select_countries = "select name from countries where id in (" . $country . ")";
            break;
        case 'en':
            $select_countries = "select name_en from countries where id in (" . $country . ")";
            break;
    }

    $db = database::getInstance();
    $result = $db->query($select_countries);

    while ($type = mysqli_fetch_row($result))
    {
        $res = $type[0];
    }

    return $res;
}

// получить глубину данных по всей таблице смертности
function GetMortalityMinYear()
{
    require_once("database.php");

    // запрос к базе
    $select_min_year = "select min(year) min_year from mortality";

    $db = database::getInstance();
    $result = $db->query($select_min_year);

    while ($type = mysqli_fetch_row($result))
    {
        $res = $type[0];
    }
   
    return $res;
}

// получить глубину данных по всей таблице смертности для выбраных стран
function GetMortalityMinYearCountry($country)
{
    require_once("database.php");

    // запрос к базе
    $select_min_year = "select min(year) min_year from mortality where country in (" . $country . ")";

    $db = database::getInstance();
    $result = $db->query($select_min_year);

    while ($type = mysqli_fetch_row($result))
    {
        $res = $type[0];
    }

    return $res;
}

// Получить основные данные для отчета
function GetGeneralReportData($report_type, $countries, $start_year, $end_year, $start_age, $end_age, $sex)
{
    $arrData = array();

    require_once("database.php");

    global $lang;

    // формируем запрос к базе
    switch($lang)
    {
        case 'ru':
            $select_data = "select t_c.name, ";
            break;
        case 'en':
            $select_data = "select t_c.name_en, ";
            break;
    }

    // вариант запроса без функций
    for ($year = $start_year; $year <= $end_year; $year++)
    {
        if ($report_type == "population")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(t_m.count_man + t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(t_m.count_man) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "part")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(t_m.count_man + t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/(select sum(t_m.count_man + t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . ")*100, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(t_m.count_man) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/(select sum(t_m.count_man + t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . ")*100, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/(select sum(t_m.count_man + t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . ")*100, -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "olders")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(t_m.count_man + t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between 60 and 110)/(select sum(t_m.count_man + t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between 20 and 59)*100, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(t_m.count_man) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between 60 and 110)/(select sum(t_m.count_man) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between 20 and 59)*100, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between 60 and 110)/(select sum(t_m.count_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between 20 and 59)*100, -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "life")
            switch ($sex)
            {
                case 'both':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum((t_m.lx_man + t_m.lx_woman)/2)/(select (t_m.lx_man + t_m.lx_woman)/2 from mortality t_m where t_m.country = t_c.id and t_m.year = " .$year . " and t_m.age = " . $start_age . ") + 0.5 from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
                case 'man':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum(t_m.lx_man)/(select t_m.lx_man from mortality t_m where t_m.country = t_c.id and t_m.year = " .$year . " and t_m.age = " . $start_age . ") + 0.5 from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $start_age_l = $start_age + 1;
                    $select_data .= "ifnull((select sum(t_m.lx_woman)/(select t_m.lx_woman from mortality t_m where t_m.country = t_c.id and t_m.year = " .$year . " and t_m.age = " . $start_age . ") + 0.5 from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                    break;
            }

        if ($report_type == "dead")
            switch ($sex)
            {
                case 'both':
                    $select_data .= "ifnull((select sum(t_m.dead_man + t_m.dead_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'man':
                    $select_data .= "ifnull((select sum(t_m.dead_man) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
                case 'woman':
                    $select_data .= "ifnull((select sum(t_m.dead_woman) from mortality t_m where t_m.country = t_c.id and t_m.year = " . $year . " and t_m.age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                    break;
            }

        if ($year != $end_year) $select_data .= ", ";
    }

    // условие выборки
    $select_data .= "from countries t_c where t_c.id in (" . $countries . ")";

    // условие сортировки
    switch($lang)
    {
        case 'ru':
            $select_data .= " order by t_c.name";
            break;
        case 'en':
            $select_data .= "order by t_c.name_en";
            break;
    }

    // выполняем запрос
    $db = database::getInstance();
    $result = $db->query($select_data);

    $c_country = 0;

    while ($type = mysqli_fetch_row($result))
    {
        for ($i = 0; $i <= $end_year - $start_year + 1; $i++)
        {
            $arrData[$c_country][$i] = $type[$i];
        }

        $c_country++;
    }

    return $arrData;
}

// Получить основные данные для отчета
function GetGeneralReportDataDetailed($report_type, $countriesDetailed, $start_year, $end_year)
{
    $arrData = array();

    require_once("database.php");

    global $lang;

    $select_data = '';
    $i = 0;

    foreach ($countriesDetailed as $countryData)
    {
        $country = $countryData[0];
        $start_age = $countryData[1];
        $end_age = $countryData[2];
        $sex = $countryData[3];

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
                        $select_data .= "ifnull((select sum(count_man + count_woman) from mortality where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(count_man) from mortality where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(count_woman) from mortality where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "part")
                switch ($sex)
                {
                    case 'both':
                        $select_data .= "ifnull((select sum(count_man + count_woman) from mortality  where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality where country = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(count_man) from mortality  where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality where country = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(count_woman) from mortality where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/(select sum(count_man + count_woman) from mortality where country = id and year = " . $year . ")*100, -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "olders")
                switch ($sex)
                {
                    case 'both':
                        $select_data .= "ifnull((select sum(count_man + count_woman) from mortality where country = id and year = " . $year . " and age between 60 and 110)/(select sum(count_man + count_woman) from mortality where country = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(count_man) from mortality where country = id and year = " . $year . " and age between 60 and 110)/(select sum(count_man) from mortality where country = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(count_woman) from mortality where country = id and year = " . $year . " and age between 60 and 110)/(select sum(count_woman) from mortality where country = id and year = " . $year . " and age between 20 and 59)*100, -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "life")
                switch ($sex)
                {
                    case 'both':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum((lx_man + lx_woman)/2)/(select (lx_man + lx_woman)/2 from mortality where country = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality where country = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum(lx_man)/(select lx_man from mortality where country = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality where country = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $start_age_l = $start_age + 1;
                        $select_data .= "ifnull((select sum(lx_woman)/(select lx_woman from mortality where country = id and year = " .$year . " and age = " . $start_age . ") + 0.5 from mortality where country = id and year = " . $year . " and age between " . $start_age_l . " and " . $end_age . "), -1) '" . $year . "' ";
                        break;
                }

            if ($report_type == "dead")
                switch ($sex)
                {
                    case 'both':
                        $select_data .= "ifnull((select sum(dead_man + dead_woman) from mortality where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'man':
                        $select_data .= "ifnull((select sum(dead_man) from mortality where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                    case 'woman':
                        $select_data .= "ifnull((select sum(dead_woman) from mortality where country = id and year = " . $year . " and age between " . $start_age . " and " . $end_age . ")/1000, -1) '" . $year . "' ";
                        break;
                }

            if ($year != $end_year) $select_data .= ", ";
        }

        $select_data .= ", " . $i . " as f_order ";

        $select_data .= "from countries where id in (" . $country . ")";
        $select_data .= " union ";

        $i++;
    }

    // отсекаем последний union
    $select_data = substr($select_data, 0, strlen($select_data) - 7);

    // сортировка
    $select_data = "select * from (" . $select_data . ") as result order by f_order";

    // выполняем запрос
    $db = database::getInstance();
    $result = $db->query($select_data);

    $c_country = 0;

    while ($type = mysqli_fetch_row($result))
    {
        for ($i = 0; $i <= $end_year - $start_year + 1; $i++)
        {
            $arrData[$c_country][$i] = $type[$i];
        }

        $c_country++;
    }

    return $arrData;
}
?>