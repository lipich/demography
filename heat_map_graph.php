<?php
require("config.php");
include_once("general_data.php");
include_once("heat_map_data.php");

// читаем параметры данных
$country = $_GET['country'];
$start_year = $_GET['start_year'];
$end_year = $_GET['end_year'];
$start_age = $_GET['start_age'];
$end_age = $_GET['end_age'];
$sex = $_GET['sex'];

// размерность интерполяции
$nx = $_GET['nx'];
$ny = $_GET['ny'];

// сглаживание
$noise = $_GET['noise'];

// отображение G канала в RGB
$g_channel = $_GET['g_channel'];

// тип отчета (данные/график)
$type = $_GET['type'];

// корректируем минимальный год в соответствии с глубиной данных в базе
$min_year = GetMortalityMinYearCountry($country);
if ($start_year < $min_year) $start_year = $min_year;

// получаем данные для отчета
$arrData = GetMortalityMapData($country, $start_year, $end_year, $start_age, $end_age, $sex);

// находим минимум и максимум
$arrDataMax = floatval($arrData[0][0]);
$arrDataMin = floatval($arrData[0][0]);
$arrDataMid = 0;

foreach ($arrData as $age)
{
    for ($i = 0; $i < $end_year - $start_year + 1; $i++)
    {
        if (floatval($age[$i]) > $arrDataMax) $arrDataMax = floatval($age[$i]);
        if (floatval($age[$i]) < $arrDataMin) $arrDataMin = floatval($age[$i]);
        $arrDataMid += $age[$i];
    }
}

// сглаживание данных
$arrDataMid = $arrDataMid / count($arrData, COUNT_RECURSIVE);
$dateDiff_left = $arrDataMid - $arrDataMin;
$dateDiff_right = $arrDataMax - $arrDataMid;

if ($dateDiff_left < $dateDiff_right) 
    $arrDataMax = $arrDataMid + $dateDiff_left;
else
    $arrDataMin = $arrDataMid - $dateDiff_right;

$arrDataMaxSmooth = $arrDataMax;
$arrDataMinSmooth = $arrDataMin;

for($i = 0; $i < $end_age - $start_age; $i++)
{
    for ($j = 0; $j < $end_year - $start_year + 1; $j++)
    {
        if ($arrData[$i][$j] > $arrDataMaxSmooth) $arrData[$i][$j] = $arrDataMaxSmooth;
        if ($arrData[$i][$j] < $arrDataMinSmooth) $arrData[$i][$j] = $arrDataMinSmooth;

        if (abs($arrData[$i][$j]) < $noise) $arrData[$i][$j] = 0;
    }
}

// разброс данных
$arrDim = $arrDataMax - $arrDataMin;

// размерность данных
$dataWidth = $end_year - $start_year + 1;
$dataHeight = $end_age - $start_age + 1;

// размер выводимого изображения
$nx_w = ($dataWidth - 1) * $nx + 1 + $global_indent * 2;
$nx_h = ($dataHeight - 1) * $ny + 1 + $global_indent * 2;

// интерполируем массив
$arrDataInt = BilinearInterpolation($arrData, $dataWidth, $dataHeight, $nx, $ny);

// выводим таблицу результатов
if ($type == "table")
{
    // вывод расчета
    $res = "<table class='report_table' border='1'>";
    $res = $res . "<tr>";
    $res = $res . "<td class='head_table' width='50px'>" . "Age" . "</td>";

    for ($year = $start_year; $year <= $end_year; $year++)
    {
        $res = $res . "<td class='head_table' align='right'>" . $year . "</td>";
    }

    $res = $res . "</tr>";

    $l_age = 0;
    $c_age = $end_age - $l_age;

    foreach ($arrData as $age)
    {

        $res = $res . "<tr>";
        $res = $res . "<td class='head_table' nowrap>" . $c_age . "</td>";

        for ($i = 0; $i < $end_year - $start_year + 1; $i++)
        {
            $res = $res . "<td class='td_numeric' align='right' nowrap>" . number_format($age[$i], 4, ',', ' ') . "</td>";
        }

        $res = $res . "</tr>";
        $l_age++;
        $c_age = $end_age - $l_age;
    }
    $res = $res . "</table>";

    // освобождаем массив с данными
    unset($arrData);
    // освобождаем интерполированный массив
    unset($arrDataInt);

    echo $res;
}
else // выводим теплокарту
{
    // освобождаем массив с данными
    unset($arrData);

    // размер легенды
    $legend = 70;

    // создаем изображение
    $image = imagecreatetruecolor($nx_w + $legend, $nx_h);

    // настройки цвета цветовые константы
    $background = imagecolorallocate($image, 244, 244, 244);
    $border = imagecolorallocate($image, 205, 205, 205);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $gray = imagecolorallocate($image, 220, 220, 220);

    // заливка фона
    imagefill($image, 0, 0, $background);

    // оси координат
    imageline($image, $global_indent, $nx_h - $global_indent, $nx_w - $global_indent / 2, $nx_h - $global_indent, $black);
    imageline($image, $global_indent, $nx_h - $global_indent, $global_indent, $global_indent / 2, $black);

    // значения оси времени
    for ($i = 0; $i <= (($end_year - $start_year) / 10); $i++)
    {
        $x = $global_indent + $i * (($nx_w - $global_indent * 2) / (($end_year - $start_year) / 10)) - 10;
        $y = $nx_h - $global_indent + 5;

        imagestring($image, 2, $x, $y, $start_year + $i * 10, $black);
    }

    // значения ося возраста
    for ($i = 1; $i <= (($end_age - $start_age) / 10); $i++)
    {
        $x = 10;
        $y = $nx_h - $global_indent - $i * (($nx_h - $global_indent * 2) / (($end_age - $start_age) / 10)) - 5;

        imagestring($image, 2, $x, $y, $i * 10, $black);
    }

    // теплокарта (по пикселам)
    $j = $global_indent;

    foreach ($arrDataInt as $data)
    {
        for ($i = 0; $i < $nx_w - $global_indent * 2; $i++)
        {
            // линейный расчет
            //$colorCorrector = 255 * ($arrDataMax - $data[$i]) / $arrDim;
            //$color = imagecolorallocate($image, round(255 - $colorCorrector), 0, round(0 + $colorCorrector));

            // более сложный расчет по градиенту
            $rgb = ColorInterpolation($arrDim, $data[$i] - $arrDataMin, $g_channel);
            $color = imagecolorallocate($image, $rgb["r"], $rgb["g"], $rgb["b"]);

            imagesetpixel($image, $i + $global_indent + 1, $j, $color);
        }
        $j++;
    }

    // освобождаем интерполированный массив
    unset($arrDataInt);

    // выводим легеду
    for ($i = 0; $i <= $nx_h - $global_indent * 2; $i++)
    {
        $rgb = ColorInterpolation($arrDim, $arrDim - $i * $arrDim / ($nx_h - $global_indent * 2), $g_channel);
        $color = imagecolorallocate($image, $rgb["r"], $rgb["g"], $rgb["b"]);

        imageline($image, $nx_w + 10, $global_indent + $i, $nx_w + 16, $global_indent + $i, $color);
        imagestring($image, 2, $nx_w + 22, $global_indent - 2, number_format($arrDataMaxSmooth, 2), $black);
        imagestring($image, 2, $nx_w + 22, $nx_h - $global_indent - 9, number_format($arrDataMinSmooth, 2), $black);
    }

    // debug - memory usage
    //$mem = number_format(memory_get_usage());
    //imagestring($image, 2, 50, 10, $mem, $black);
    //imagestring($image, 2, 50, 50, $arrDataMax, $white);
    //imagestring($image, 2, 50, 70, $arrDataMin, $white);

    // выводим изображение
    header("content-type: image/png");
    imagepng($image);
    imagedestroy($image);
}

// Билинейная итерполяция
function BilinearInterpolation($arrSource, $sourceWidth, $sourceHeight, $exp_w, $exp_h)
{
    $arrResult = array();

    $resWidth = ($sourceWidth - 1) * $exp_w + 1;
    $resHeight = ($sourceHeight - 1) * $exp_h + 1;

    for ($i = 0; $i < $resHeight; $i++)
    {
        for ($j = 0; $j < $resWidth; $j++)
        {
            $tmp = floatval($i / $exp_h);
            $l = intval(floor($tmp));
            if ($l < 0)
            {
                $l = 0;
            }
            else
            {
                if ($l >= $sourceHeight - 1)
                {
                    $l = $sourceHeight - 2;
                }
            }

            $u = $tmp - $l;

            $tmp = floatval(($j) / $exp_w);
            $c = intval(floor($tmp));
            if ($c < 0)
            {
                $c = 0;
            }
            else
            {
                if ($c >= $sourceWidth - 1)
                {
                    $c = $sourceWidth - 2;
                }
            }

            $t = $tmp - $c;

            /* Коэффициенты */
            $d1 = (1 - $t) * (1 - $u);
            $d2 = $t * (1 - $u);
            $d3 = $t * $u;
            $d4 = (1 - $t) * $u;

            /* Окрестные пиксели: source[i][j] */
            $p1 = $arrSource[$l][$c];
            $p2 = $arrSource[$l][$c + 1];
            $p3 = $arrSource[$l + 1][$c + 1];
            $p4 = $arrSource[$l + 1][$c];

            $arrResult[$i][$j] = $p1 * $d1 + $p2 * $d2 + $p3 * $d3 + $p4 * $d4;
        }
    }

    return $arrResult;
}

// цветовая интерполяция
function ColorInterpolation($dimension, $value, $g_channel)
{
    $rgb = array();

    // массив цветовых переходов
    $arrR = array(0, 0, 0, 128, 255, 255, 128);
    $arrG = array(0, 0, 255, 255, 255, 0, 0);
    $arrB = array(128, 255, 255, 128, 0, 0, 0);

    // количество интервалов в массиве
    $n = 6;

    // посчитаем в какой интервал массива цветовых переходов попадает значение
    $x = $value * $n / $dimension;

    $x_min = intval(floor($x));
    $x_max = intval(ceil($x));

    // коррекция на случай ошибок
    if ($x_min < 0) $x_min = 0; if ($x_min > 6) $x_min = 6;
    if ($x_max < 0) $x_max = 0; if ($x_max > 6) $x_max = 6;

    $rgb["r"] = intval($arrR[$x_max] * ($x - $x_min) + $arrR[$x_min] * ($x_max - $x));

    if ($g_channel == "true")
        $rgb["g"] = intval($arrG[$x_max] * ($x - $x_min) + $arrG[$x_min] * ($x_max - $x));
    else
        $rgb["g"] = 0;

    $rgb["b"] = intval($arrB[$x_max] * ($x - $x_min) + $arrB[$x_min] * ($x_max - $x));

    unset($arrR);
    unset($arrG);
    unset($arrB);

    return $rgb;
}
?>