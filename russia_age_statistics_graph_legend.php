<?php
if(!session_id()) session_start();

require("config.php");
include_once("russia_age_data.php");

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

if (isset($_GET['region']) && isset($_GET['year']))
{
    // читаем параметры графика
    $region = $_GET['region'];
    $year = $_GET['year'];
    $report_type = $_GET['report_type'];

    $arrAgeLegendData = GetAgeLegendData($report_type, $region, $year);

    $res = "<table><tr>
    <td style='background-color: " . rgb2html(0, 71, 127) . "'>&nbsp;&nbsp;&nbsp;</td>
    <td>" . $content_sex_males . ": " . number_format($arrAgeLegendData[0], 0, ',', ' ') . "</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td style='background-color: " . rgb2html(76, 136, 190) . "'>&nbsp;&nbsp;&nbsp;</td>
    <td>" . $content_sex_females . ": " . number_format($arrAgeLegendData[1], 0, ',', ' ') . "</td></tr></table>";

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