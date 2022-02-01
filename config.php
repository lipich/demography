<?php
    //$server_type = "local";
    $server_type = "remote"; // for publish

    // настройки базы
    $db_servertype = "mysql";

    if ($server_type == "local")
    {
        $db_host = "localhost";
        $db_name = "demography";
        $db_user = "demography";
        $db_password = "demography";
    }
    else
    {
        $db_host = "a15113.mysql.mchost.ru";
        $db_name = "a15113_demograph";
        $db_user = "a15113_demograph";
        $db_password = "a15113";
    }

    // общие настройки
    // страны выбранные по умолчанию для первой загрузки страницы
    $general_selected_countries = array(3,4);
    $general_russia_selected_regions = array(81,9,15);
    $heatmap_selected_country = 3;

    $global_max_year = 2020;
    $global_selected_start_year = 1990;
    $global_selected_end_year = 2020;

    $heatmap_selected_start_year = 1900;

    $global_min_age = 0;
    $global_max_age = 110;
    $global_russia_max_age = 100;
    $heatmap_max_age = 100;

    // настройки графика
    // отступ от края изображения
    $global_indent = 30;
    // отступ от оси координат
    $global_indent_border = 3;

    // общий отчет
    $generalImageWidth = 900;
    $generalImageHeight = 450;
    $generalImageWidthMin = 600;
    $generalImageWidthMax = 3000;

    $legendWidth = 300;

    // количество интервалов на оси дат
    $global_year_interval = 10;
    // шаг в данных по оси дат
    $global_year_data_step = 1;

    // количество интервалов на оси значений
    $global_value_interval = 5;

    // возрастной состав
    $ageImageWidth = 600;
    $ageImageHeight = 500;

    $ageColHeight = 2;

    // настройки интерполирования
    $global_nx_max = 10;
    $global_ny_max = 10;

    $global_nx_default = 8;
    $global_ny_default = 4;

    // настройки шрифта
    putenv('GDFONTPATH=' . realpath('.'));
    $font_calibri = "styles/fonts/calibri.ttf";
    $font_size = 11;
    $font_width = 7;
?>