<?php
require("config.php");
include_once("graph_lib.php");

if (isset($_GET['type']))
{
    // читаем параметры данных
    $r_type = $_GET['r_type'];
    $type = $_GET['type'];

    if (($type == 'world') || ($type == 'g20'))
    {
        include_once("general_data.php");
        $countries = $_GET['countries'];
    }
    else
    {
        include_once("russia_general_data.php");
        $countries = $_GET['regions'];
    }

    $start_year = $_GET['start_year'];
    $end_year = $_GET['end_year'];
    $start_age = $_GET['start_age'];
    $end_age = $_GET['end_age'];
    $sex = $_GET['sex'];
    $report_type = $_GET['report_type'];

    $image_width = $_GET['image_width'];

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

    // рисуем график
    // настройки графика
    // фильтруем ширину изображения на случай получения очень большого значения
    if ($image_width > $generalImageWidthMax)
    {
        $imageWidth = $generalImageWidth;
        $imageHeight = $generalImageHeight;
    }
    else if ($image_width < $generalImageWidthMin)
    {
        $imageWidth = $generalImageWidthMin;
        $imageHeight = round($generalImageWidthMin / 3);
    }
    else
    {
        $imageWidth = $image_width;
        $imageHeight = round($image_width / 3);
    }

    // отступ зоны данных от края изображения
    $indent = $global_indent;
    $indent_border = $global_indent_border;

    // максимальная высота координат в зоне данных
    $maxHeight = $imageHeight - ($indent + $indent_border) * 2;

    // создаем изображение
    $image = imagecreatetruecolor($imageWidth, $imageHeight);
    imageantialias($image, true);

    // настройки цвета (цветовые константы)
    $background = imagecolorallocate($image, 244, 244, 244);
    $border = imagecolorallocate($image, 205, 205, 205);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $gray = imagecolorallocate($image, 220, 220, 220);

    // подготавливаем фон графика
    imagefill($image, 0, 0, $background);

    // рисуем зону данных
    imagerectangle($image, $indent * 2, $indent, $imageWidth - $indent, $imageHeight - $indent, $border);

    // заполняем зону данных цветом
    imagefilltoborder($image, $indent * 2 + 5, $indent + 5, $border, $white);

    // если массив стран не пуст
    if ($countries != 'null')
    {
        // корректируем год начала
        if (($type == 'world') || ($type == 'g20'))
            $min_year = GetMortalityMinYearCountry($countries);
        else
            $min_year = GetMortalityMinYearRegion($countries);

        if ($start_year < $min_year) $start_year = $min_year;

        // получаем данные для отчета
        if ($r_type == 'detailed')
            $arrValues = GetGeneralReportDataDetailed($report_type, $countriesDetailed, $start_year, $end_year);
        else
            $arrValues = GetGeneralReportData($report_type, $countries, $start_year, $end_year, $start_age, $end_age, $sex);

        // копируем массив значений для дальнейшего преобразования в координаты
        $arrData = $arrValues;

        // массив стран
        $countryData = explode(',', $countries);
        //$countryNames = array();

        // размерность массива стран
        $countryDimension = count($countryData);

        // размерность массива лет
        $yearDimension = $end_year - $start_year;

        // формируем массив названий стран (не используется)
        /*$currCountry = 0;
        foreach ($arrData as $country)
        {
            $countryNames[$currCountry] = $country[0];
            $currCountry++;
        }*/

        // округляем и фильтруем данные
        for ($i = 0; $i < $countryDimension; $i++)
            for ($j = 1; $j <= $yearDimension + 1; $j++)
            {
                if ($arrData[$i][$j] == -1) $arrData[$i][$j] = null;
                if ($arrValues[$i][$j] == -1) $arrValues[$i][$j] = null;

                if (!is_null($arrData[$i][$j])) $arrData[$i][$j] = round($arrData[$i][$j]);
                //debug
                //imagestring($image, 2, $indent * 2, $indent + $j * 15, $j . ' = ' . $arrData[$i][$j], $black);
            }

        // масштабируем график
        $arrDataMax = $arrData[0][1];
        $arrDataMin = $arrData[0][1];

        // выбираем максимум и минимум
        for ($i = 0; $i < $countryDimension; $i++)
            for ($j = 1; $j <= $yearDimension + 1; $j++)
            {
                if ($arrData[$i][$j] > $arrDataMax) $arrDataMax = $arrData[$i][$j];
                if (($arrData[$i][$j] < $arrDataMin) & (!is_null($arrData[$i][$j]))) $arrDataMin = $arrData[$i][$j];
            }

        // округляем значения
        $arrDataMax = roundMax($arrDataMax);
        $arrDataMin = roundMin($arrDataMin);

        if ($arrDataMax == $arrDataMin) $arrDataMin = $arrDataMin - 1;
        //debug
        //imagestring($image, 2, 200, 100, 'arrDataMin=' . $arrDataMin . '  arrDataMax=' . $arrDataMax, $black);

        for ($i = 0; $i < $countryDimension; $i++)
            for ($j = 1; $j <= $yearDimension + 1; $j++)
                if (!is_null($arrData[$i][$j]))
                {
                    $arrData[$i][$j] = round(($arrValues[$i][$j] - $arrDataMin) * $maxHeight / ($arrDataMax - $arrDataMin), 2);
                    //debug
                    //imagestring($image, 2, $indent * 2 + 100, $indent + $j * 15, $j . ' = ' . $arrData[$i][$j], $black);
                }

        // количество интервалов на оси лет
        $yearInterval = $global_year_interval;
        if ($yearDimension < $yearInterval) $yearInterval = $yearDimension;

        // шаг в значении года
        $yearDataStep = $global_year_data_step;
        if ($yearDimension > $yearInterval) $yearDataStep = round($yearDimension / $yearInterval);

        // корректируем количество интервалов
        $yearInterval = round($yearDimension/$yearDataStep);

        // шаг по оси дат в зависимости от количества интервалов
        $yearGraphicStep = round(($imageWidth - $indent * 3 - $indent_border * 2) / $yearInterval);

        // отобразим значения на оси Х
        for ($i = 0; $i <= $yearInterval; $i++)
        {
            // небольшая хитрость с округлением
            $val = $start_year + $yearDataStep * $i;
            if ($val > $end_year) $val = $end_year;
            if (($i == $yearInterval) & ($val < $end_year)) $val = $end_year;

            $x = $indent * 2 + $indent_border + $yearGraphicStep * $i - 10;
            $y = $imageHeight - $indent - $indent_border + 25;

            //imagettftext($image, $font_size, 0, $x, $y, $black, $font_calibri, $val);
            imagestring($image, 2, $x + 2, $y - 12, $val, $black);

            if (($i > 0) & ($i < $yearInterval))
            {
                // линии значений
                //imageline($image, $indent * 2 + 1, $y - 3, $imageWidth - $indent - 1, $y - 3, $gray);
                // отметки значений на осях
                imageline($image, $x + 15, $imageHeight - $indent + 1, $x + 15, $imageHeight - $indent + 5, $black);
            }
        }

        // отобразим значения на оси Y
        // сетка значений
        $valueInterval = $global_value_interval;

        // корректируем кол-во интервалов
        if ($arrDataMax - $arrDataMin < $valueInterval) $valueInterval = $arrDataMax - $arrDataMin;

        // шаг в данных по оси значений
        $valueDataStep = round($arrDataMax - $arrDataMin, (strlen($arrDataMax - $arrDataMin) - 2) * -1) / $valueInterval;

        // шаг в координатах по оси значений
        $valueGraphicStep = round(($imageHeight - $indent * 2 - $indent_border * 2) / $valueInterval);

        for($i = 0; $i < $valueInterval; $i++)
        {
            $val = $valueDataStep * $i + $arrDataMin;

            $x = $indent * 2 - (strlen($val) * $font_width + (strlen(number_format($val, 0, ',', ' ')) - strlen($val)) * 3) - 10;
            $y = $imageHeight - $indent - $indent_border + 8 - $valueGraphicStep * $i;

            //imagettftext($image, $font_size, 0, $x, $y, $black, $font_calibri, number_format($val, 0, ',', ' '));
            imagestring($image, 2, $x, $y - 12, number_format($val, 0, ',', ' '), $black);
            if ($i > 0)
            {
                // линии значений
                imageline($image, $indent * 2 + 1, $y - 4, $imageWidth - $indent - 1, $y - 4, $gray);
                // отметки значений на осях
                imageline($image, $indent * 2 - 5, $y - 4, $indent * 2 - 1, $y - 4, $black);
            }
        }

        // максимальное значение
        $x = $indent * 2 - (strlen($arrDataMax) * $font_width + (strlen(number_format($arrDataMax, 0, ',', ' ')) - strlen($arrDataMax)) * 3) - 10;
        $y = $indent - $indent_border + 8;
        //imagettftext($image, $font_size, 0, $x, $y, $black, $font_calibri, number_format($arrDataMax, 0, ',', ' '));
        imagestring($image, 2, $x, $y - 12,  number_format($arrDataMax, 0, ',', ' '), $black);

        // отобразим данные на графике
        $xStep = ($imageWidth - $indent * 3 - $indent_border * 2) / $yearDimension;

        // рисуем графики
        for ($i = 0; $i < $countryDimension; $i++)
        {
            for ($j = 1; $j <= $yearDimension; $j++)
            {
                //debug
                //imagestring($image, 2, $indent * 2 + 20, $indent + $j * 15, $j . ' = ' . $arrValues[$i][$j], $black);
                //imagestring($image, 2, $indent * 2 + 20, $indent + $j * 15, 'x1 = ' . $x1 .';y1 = ' . $y1 . ';x2 = ' . $x2 . ';y2 = ' . $y2 , $black);

                $x1 = round($indent * 2 + $indent_border + $xStep * ($j - 1));
                $y1 = $imageHeight - $indent - $indent_border - $arrData[$i][$j];

                $x2 = round($indent * 2 + $indent_border + $xStep * $j);
                $y2 = $imageHeight - $indent - $indent_border - $arrData[$i][$j + 1];

                $rgb = GetColor($i);
                $color = imagecolorallocate($image,  $rgb["r"], $rgb["g"], $rgb["b"]);

                if ((!is_null($arrData[$i][$j])) & (!is_null($arrData[$i][$j + 1])))
                {
                    // отрезок графика
                    imageline($image, $x1, $y1, $x2, $y2, $color);
                    if ($yearDimension > 40)
                    {
                        // делаем линию толще
                        imageline($image, $x1, $y1 + 1, $x2, $y2 + 1, $color);
                    }

                    // значение в точке
                    //imagestring($image, 2, $x1, $y1, $arrValues[$i][$j], $black);
                    //imagestring($image, 2, $x1, $y1, $arrData[$i][$j], $black);

                    // маркер
                    if ($yearDimension <= 40)
                    {
                        imagearc($image, $x1, $y1, 6, 6,  0, 360, $color);
                        imagefilltoborder($image, $x1 + 1, $y1 + 1, $color, $color);
                        imagefilltoborder($image, $x1 - 1, $y1 - 1, $color, $color);
                    }

                    // последняя точка на графике (надо доработать)
                    if ($j == $yearDimension)
                    {
                        // маркер
                        if ($yearDimension <= 40)
                        {
                            imagearc($image, $x2, $y2, 6, 6,  0, 360, $color);
                            imagefilltoborder($image, $x2 + 1, $y2 + 1, $color, $color);
                            imagefilltoborder($image, $x2 - 1, $y2 - 1, $color, $color);
                        }
                        // значение в точке
                        //imagestring($image, 2, $x2, $y2, $arrValues[$i][$j + 1], $black);
                    }
                    else if (is_null($arrData[$i][$j + 2]))
                    {
                        // маркер
                        if ($yearDimension <= 40)
                        {
                            imagearc($image, $x2, $y2, 6, 6,  0, 360, $color);
                            imagefilltoborder($image, $x2 + 1, $y2 + 1, $color, $color);
                            imagefilltoborder($image, $x2 - 1, $y2 - 1, $color, $color);
                        }
                        // значение в точке
                        //imagestring($image, 2, $x2, $y2, $arrValues[$i][$j + 1], $black);
                    }
                }
            }
        }
    }

    // выводим изображение
    header("content-type: image/png");
    imagepng($image);
    imagedestroy($image);
}

// округление до ближайшей сотни вверх
function roundMax($p)
{
    $round = 0;

    if (strlen($p) > 2)
    {
        $p = $p + pow(10, (strlen($p) - 2))/2 - 1;
        $round = (strlen($p) - 2) * -1;
    }
    if (strlen($p) == 2)
    {
        $p = $p + 2;
        $round = 0;
    }
    if (strlen($p) == 1)
    {
        $p = $p + 1;
        $round = 0;
    }

    return round($p, $round);
}

// округление до ближайшей сотни вниз
function roundMin($p)
{
    $round = 0;

    if (strlen($p) > 2)
    {
        $p = $p - pow(10, (strlen($p) - 2))/2 - 1;
        $round = (strlen($p) - 2) * -1;
    }
    if (strlen($p) == 2)
    {
        $p = $p - 2;
        $round = 0;
    }
    if (strlen($p) == 1)
    {
        $p = $p - 1;
        if ($p < 0) $p = 0;
        $round = 0;
    }

    return round($p, $round);
}
?>