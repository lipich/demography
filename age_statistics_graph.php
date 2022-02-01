<?php 
    require("config.php");

    if (isset($_GET['type']))
    {
	    // читаем параметры графика
        if ($_GET['type'] == 'world')
            include_once("age_data.php");
        else
            include_once("russia_age_data.php");

        if ($_GET['type'] == 'world')
            $country = $_GET['country'];
        else
            $region = $_GET['region'];

        $year = $_GET['year'];
	    $report_type = $_GET['report_type'];

        if ($_GET['type'] == 'world')
	        $arrAgeData = GetAgeReportData($report_type, $country, $year);
        else
            $arrAgeData = GetAgeReportData($report_type, $region, $year);

		$arrDataMan = array();
		$arrDataWoman = array();

        $sumDataMan = 0;
        $sumDataWoman = 0;

        foreach ($arrAgeData as $ageData)
        {
	        $arrDataMan[] = $ageData[1];
	        $arrDataWoman[] = $ageData[2];

            $sumDataMan = $sumDataMan + $ageData[1];
            $sumDataWoman = $sumDataWoman + $ageData[2];
	    }
	
		// рисуем график половозрастного состава
        // настройки графика
        if ($_GET['type'] == 'world')
 		    $matrixDimension = $global_max_age;
        else
            $matrixDimension = $global_russia_max_age;

        $imageWidth = $ageImageWidth;
        $imageHeight = $ageImageHeight;

        $indent = $global_indent;

        $colMaxWidth = $imageWidth / 2 - $indent;
        $colHeight = $ageColHeight;

        $image = imagecreate($imageWidth, $imageHeight);

        // настройки цвета
        $white = imagecolorallocate($image, 255, 255, 255);
        $manColor = imagecolorallocate($image, 0, 71, 127);
        $womanColor = imagecolorallocate($image, 76, 136, 190);
        $black = imagecolorallocate($image, 0, 0, 0);

        // масштабируем график
        $arrDataMax = $arrDataMan[0]; // есть функция max в php (может эффективней)
        $arrDataMaxMan = $arrDataMan[0];
        $arrDataMaxWoman = $arrDataWoman[0];
        
        for ($i = 0; $i <= $matrixDimension; $i++)
        {
            if ($arrDataMan[$i] > $arrDataMax) $arrDataMax = $arrDataMan[$i];
            if ($arrDataMan[$i] > $arrDataMaxMan) $arrDataMaxMan = $arrDataMan[$i];
            if ($arrDataWoman[$i] > $arrDataMax) $arrDataMax = $arrDataWoman[$i];
            if ($arrDataWoman[$i] > $arrDataMaxWoman) $arrDataMaxWoman = $arrDataWoman[$i];
        }

        if ($arrDataMax > $colMaxWidth)
        {
            for ($i = 0; $i <= $matrixDimension; $i++)
            {
                $arrDataMan[$i] = $arrDataMan[$i] * ($colMaxWidth - $indent) / $arrDataMax;
                $arrDataWoman[$i] = $arrDataWoman[$i] * ($colMaxWidth - $indent) / $arrDataMax;
            }
        }

        // отобразим год на графике
        imagestring($image, 4, 50, 0, $year, $black);
        //imagettftext($image, 14, 0, 50, 0, $black, $font_arial, $year);

        // отобразим кол-во мужчин и женщин
        //imagestring($image, 4, 52, 15, "male: " . number_format($sumDataMan / 1000, 0, ',', ' ') , $manColor);
        //imagestring($image, 4, 50, 30, "female: " . number_format($sumDataWoman / 1000, 0, ',', ' '), $womanColor);

        // отобразим оси координат
        imageline($image, $indent, $imageHeight - $indent, $imageWidth, $imageHeight - $indent, $black);
        imageline($image, $indent, $imageHeight - $indent, $indent, 0, $black);

        // отобразим значения на осях
        for ($i = 0; $i <= $matrixDimension; $i = $i + 10)
        {
            imagestring($image, 2, 5, $imageHeight - $indent - $i * ($imageHeight - $indent - 10) / $matrixDimension - 10, $i, $black);
        }

        //$arrDataMaxMan = roundMax($arrDataMaxMan) / 1000;
        //imagestring($image, 2, 50, $imageHeight - $indent + 5, number_format($arrDataMaxMan, 0, ',', ' '), $black);
        //imagestring($image, 2, $imageWidth / 2 - 3, $imageHeight - $indent + 5, "0", $black);
        //$arrDataMaxWoman = roundMax($arrDataMaxWoman) / 1000;
        //imagestring($image, 2, $imageWidth - 90, $imageHeight - $indent + 5, number_format($arrDataMaxWoman, 0, ',', ' '), $black);

        // отобразим данные на графике
        for ($i = 0; $i <= $matrixDimension; $i++)
        {
            $x1 = $imageWidth / 2 - $arrDataMan[$i];
            $y1 = $imageHeight - $indent - $i * ($imageHeight - $indent - $colHeight) / $matrixDimension - $colHeight;
            $x2 = $imageWidth / 2 - $arrDataMan[$i] + $arrDataMan[$i];
            $y2 = $imageHeight - $indent - $i * ($imageHeight - $indent - $colHeight) / $matrixDimension - $colHeight + $colHeight;

            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $manColor);

            $x1 = $imageWidth / 2;
            $y1 = $imageHeight - $indent - $i * ($imageHeight - $indent - $colHeight) / $matrixDimension - $colHeight;
            $x2 = $imageWidth / 2 + $arrDataWoman[$i];
            $y2 = $imageHeight - $indent - $i * ($imageHeight - $indent - $colHeight) / $matrixDimension - $colHeight + $colHeight;

            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $womanColor);
        }

        // выводим изображение
        header("content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }

    // округление до ближайшей сотни вверх
    function roundMax($p)
    {
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
?>