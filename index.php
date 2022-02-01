<?php session_start();?>
<?php ob_start();?>
<?php
// выбранный язык
if (isset($_GET['lang']))
    $lang = $_GET['lang'];
else
    $lang = "ru";

$_SESSION['lang'] = $lang;

// подключаем словарь в соответствии с языком
switch($lang)
{
   case "ru":
       include("content_ru.php");
       break;
   case "en":
       include("content_en.php");
       break;
}

// выбраное меню 1-го уровня (регион)
if (isset($_GET['type']))
    $type = $_GET['type'];
else
    $type = "world";  

$_SESSION['type'] = $type;

// выбранное меню второго уровня (тип отчета)
if (isset($_GET['action']))
    $action = $_GET['action'];
else
    $action = "general";

if (($action == "heatmap") && ($type == "russia")) $action = "general";

$_SESSION['action'] = $action;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $content_header?></title>
    <link rel="stylesheet" type="text/css" href="styles/site.css" />
    <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script language="javascript" type="text/javascript" src="scripts/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
</head>
<body>    
    <div class="page">
        <div class="header">
            <div class="header_text"><?php echo $content_header?></div>
            <div class="menu_head">
            <?php
                if ($type == "world")
                    echo "<div class='menu_head_item_selected'>" . $content_menu_head_world . "</div>";
                else
                    echo "<div class='menu_head_item'><a href='index.php?type=world&action=" . $action . "&lang=" . $lang . "'>" . $content_menu_head_world . "</a></div>";

                if ($type == "russia")
                    echo "<div class='menu_head_item_selected'>" . $content_menu_head_russia . "</div>";
                else
                    echo "<div class='menu_head_item'><a href='index.php?type=russia&action=" . $action . "&lang=" . $lang . "'>" . $content_menu_head_russia . "</a></div>";

                echo "<div class='menu_language'><table><tr>";
                switch($lang)
                {
                   case "ru":
                       echo "<td><div class='menu_language_item_selected'>ru</div></td><td><div class='menu_language_item'><a href='index.php?type=" . $type . "&action=" . $action . "&lang=en'>en</a></div></td>";
                       break;
                   case "en":
                       echo "<td><div class='menu_language_item'><a href='index.php?type=" . $type . "&action=" . $action . "&lang=ru'>ru</a></div></td><td><div class='menu_language_item_selected'>en</div></td>";
                       break;
                }
                echo "</tr></table></div>";
            ?>
            </div>
        </div>
        <div class="header_under">&nbsp;</div>
        <div class="menu_head_under">&nbsp;</div>
        <div style="clear: both"></div>
        <div class="menu">
        <?php
            if ($action == "general")
                echo "<div class='menu_item_selected'>" . $content_menu_general . "</div>";
            else
                echo "<div class='menu_item'><a href='index.php?type=" . $type . "&action=general&lang=" . $lang . "'>" . $content_menu_general . "</a></div>";

            if ($action == "age")
                echo "<div class='menu_item_selected'>" . $content_menu_age . "</div>";
            else
                echo "<div class='menu_item'><a href='index.php?type=" . $type . "&action=age&lang=" . $lang . "'>" . $content_menu_age . "</a></div>";

            if ($action == "heatmap")
            	echo "<div class='menu_item_selected'>" . $content_menu_heatmap	. "</div>";
			else
				if ($type == "world") echo "<div class='menu_item'><a href='index.php?type=" . $type . "&action=heatmap&lang=" . $lang . "'>" . $content_menu_heatmap . "</a></div>";
        ?>
        </div>
        <div style="clear: both"></div>
        <div class="main">
        <?php
            if ($type == "world")
            {
                if ($action == "general") include("general_statistics.php");
                if ($action == "age") include("age_statistics.php");
                if ($action == "heatmap") include("heat_map.php");
            }

            if ($type == "russia")
            {
                if ($action == "general") include("russia_general_statistics.php");
                if ($action == "age") include("russia_age_statistics.php");
            }
        ?>
        </div>
    </div>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(87055809, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/87055809" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>
<?php ob_end_flush();?>