<?php
	require("config.php");
	include_once("general_data.php");
	include_once("graph_lib.php");

    if(!session_id()) session_start();

    if (isset($_SESSION['lang']))
        switch($_SESSION['lang'])
        {
           case 'ru':
               include_once("content_ru.php");
               break;
           case 'en':
               include_once("content_en.php");
               break;
        }
    else
        include_once("content_ru.php");

    if (isset($_GET['countries']) && $_GET['countries'] != 'null')
    {
        // читаем параметры данных
        $r_type = $_GET['r_type'];
        $countries = $_GET['countries'];

        // в зависимости от типа отчета обрабатываем массив стран или регионов
        if ($r_type == 'detailed')
        {
            // массив стран
            $countryDataDetailed = explode(',', $countries);
            $countries = '';
            $countriesDetailed = array();
            $currCountry = 0;

            foreach ($countryDataDetailed as $countryDataDetailedElement)
            {
                $detailedData = explode(';', $countryDataDetailedElement);
                $countries .= $detailedData[0] . ',';
                $col = 0;

                foreach ($detailedData as $detailedDataElement)
                {
                    $countriesDetailed[$currCountry][$col] = $detailedDataElement;
                    $col++;
                }
                $currCountry++;
            }

            $countries = rtrim($countries, ',');
        }

        //$countryNames = GetCountriesNames($countries);
        $arrCountries = explode(',', $countries);

        // вывод результата
        $res = "";

        $i = 0;
        foreach ($arrCountries as $country)
        {
            $countryName = GetCountryName($country);

            if ($r_type == 'detailed')
            {
                $strSex = '';

                switch ($countriesDetailed[$i][3])
                {
                    case "both":
                        $strSex = $content_sex_unisex;
                        break;
                    case "man":
                        $strSex = $content_sex_males;
                        break;
                    case "woman":
                        $strSex = $content_sex_females;
                        break;
                }

                $countryName .= ': ' . $countriesDetailed[$i][1] . ' - ' . $countriesDetailed[$i][2] . ' ; ' . $strSex;
            }

            $rgb = GetColor($i);
            $res .= "<table><tr><td style='background-color: " . rgb2html($rgb["r"], $rgb["g"], $rgb["b"]) . "'>&nbsp;&nbsp;&nbsp;</td><td>" . $countryName . "</td></tr></table>";
            $i++;
        }

        echo $res;
    }

    // конвертировать цвет из RGB в hex
    function rgb2html($r, $g=-1, $b=-1)
    {
        if (is_array($r) && sizeof($r) == 3) list($r, $g, $b) = $r;

        $r = intval($r); $g = intval($g);
        $b = intval($b);

        $r = dechex($r<0?0:($r>255?255:$r));
        $g = dechex($g<0?0:($g>255?255:$g));
        $b = dechex($b<0?0:($b>255?255:$b));

        $color = (strlen($r) < 2?'0':'').$r;
        $color .= (strlen($g) < 2?'0':'').$g;
        $color .= (strlen($b) < 2?'0':'').$b;

        return '#'.$color;
    }
?>
