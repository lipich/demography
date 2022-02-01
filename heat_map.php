<?php
require("config.php");
include_once("general_data.php");

$min_year = GetMortalityMinYear();

// type
if (isset($_SESSION['type']))
    $type = $_SESSION['type'];
else
    $type = "world";
?>
<div>
<p>
    <table>
    <tr>
        <td><?php echo $content_country?></td><td>&nbsp;</td>
        <td>
            <select id="country" name="country" class="listbox_reference">
            <?php
                $arrCountries = GetCountries($type);

                foreach ($arrCountries as $country)
                {
                    if ($heatmap_selected_country == $country[0])
                        echo "<option selected='true' value='" . $country[0] . "'>" . $country[1] . "</option>";
                    else
                        echo "<option value='" . $country[0] . "'>" . $country[1] . "</option>";
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo $content_period?></td><td>&nbsp;</td>
        <td>
            <table class="table_layout_left">
                <tr>
                    <td>
                        <select id="start_year" name="start_year" class="listbox">
                        <?php
                            for ($year = $min_year; $year <= $global_max_year; $year++)
                            {
                                if ($year == $heatmap_selected_start_year)
                                    echo "<option value='" . $year . "' selected='true'>" . $year . "</option>";
                                else
                                    echo "<option value='" . $year . "'>" . $year . "</option>";
                            }
                        ?>
                        </select>
                    </td>
                    <td>&nbsp;-&nbsp;</td>
                    <td>
                        <select id="end_year" name="end_year" class="listbox">
                        <?php
                            for ($year = $min_year; $year <= $global_max_year; $year++)
                            {
                                if ($year == $global_selected_end_year)
                                    echo "<option value='" . $year . "' selected='true'>" . $year . "</option>";
                                else
                                    echo "<option value='" . $year . "'>" . $year . "</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><?php echo $content_age?></td><td>&nbsp;</td>
        <td>
            <table class="table_layout_left">
                <tr>
                    <td>
                        <select id="start_age" name="start_age" class="listbox">
                        <?php
                            for ($age = $global_min_age; $age <= $heatmap_max_age; $age++)
                            {
                                if ($age == $global_min_age)
                                    echo "<option value='" . $age . "' selected='true'>" . $age . "&nbsp;&nbsp;</option>";
                                else
                                    echo "<option value='" . $age . "'>" . $age . "&nbsp;&nbsp;</option>";
                            }
                        ?>
                        </select>
                    </td>
                    <td>&nbsp;-&nbsp;</td>
                    <td>
                        <select id="end_age" name="end_age" class="listbox">
                        <?php
                            for ($age = $global_min_age; $age <= $heatmap_max_age; $age++)
                            {
                                if ($age == $heatmap_max_age)
                                    echo "<option value='" . $age . "' selected='true'>" . $age . "&nbsp;&nbsp;</option>";
                                else
                                    echo "<option value='" . $age . "'>" . $age . "&nbsp;&nbsp;</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><?php echo $content_sex?></td><td>&nbsp;</td>
        <td>
            <select id="sex" name="sex" class="listbox">
                <option value="man" selected="true"><?php echo $content_sex_males?></option>
                <option value="woman"><?php echo $content_sex_females?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td>NX/NY</td><td>&nbsp;</td>
        <td>
            <select id="nx" name="nx" class="listbox">
            <?php
                for ($nx = 1; $nx <= $global_nx_max; $nx++)
                {
                    if ($nx == $global_nx_default)
                        echo "<option value='" . $nx . "' selected='true'>" . $nx . "&nbsp;&nbsp;</option>";
                    else
                        echo "<option value='" . $nx . "'>" . $nx . "&nbsp;&nbsp;</option>";
                }
            ?>
            </select>
            &nbsp;/&nbsp;
            <select id="ny" name="ny" class="listbox">
            <?php
                for ($ny = 1; $ny <= $global_ny_max; $ny++)
                {
                    if ($ny == $global_ny_default)
                        echo "<option value='" . $ny . "' selected='true'>" . $ny . "&nbsp;&nbsp;</option>";
                    else
                        echo "<option value='" . $ny . "'>" . $ny . "&nbsp;&nbsp;</option>";
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo $content_noise?></td><td>&nbsp;</td>
        <td>
            <select id="noise" name="noise" class="listbox">
                <option value="0">0&nbsp;&nbsp;</option>
                <option value="5">5&nbsp;&nbsp;</option>
                <option value="10" selected="true">10&nbsp;&nbsp;</option>
                <option value="15">15&nbsp;&nbsp;</option>
                <option value="20">20&nbsp;&nbsp;</option>
            </select>
            <input type="checkbox" id="g_channel" name="g_channel">RGB G-channel
        </td>
    </tr>
    <tr>
        <td><?php echo $content_report?></td><td>&nbsp;</td>
        <td>
            <input type="radio" id="view_table" name="view_type" value="table" disabled="true"><?php echo $content_table?>
            <input type="radio" id="view_heatmap" name="view_type" value="heatmap" checked="true"><?php echo $content_heatmap?>
        </td>
    </tr>
    </table>
</p>
</div>
<div>&nbsp;<a href="#" id="report"><?php echo $content_calculate?></a>&nbsp;&nbsp;&nbsp;(*<a href="help/<?php echo $_SESSION['lang']?>/heat_map/index.htm" target="_blank"><i><?php echo $content_calculate_help?></i></a>)</div><br />
<div id="report_table" class="report"></div>
<div style="clear: both"></div>
<div style="position:relative; width: 1000px;">
	<div id="report_map" style="height: 250px;"><img id="img_report_map" /></div>
	<div style="display: none; position: relative; top: -50px; left: 300px;" id="progress">
	<table bgcolor="#ffffff"><tr><td><?php echo $content_loading?></td><td>&nbsp;</td><td><img src="images/loading.gif" /></td></tr></table>
	</div>
</div>

<script type="text/javascript" language="javascript">
// загрузить отчет
function loadReport() {
    if ($('input:radio[name=view_type]:checked').val() == "table")
    {
        $('#report_map').hide();
        $('#report_table').hide();

        $('#report_table').load('heat_map_graph.php?country=' + $('#country').val() +
               '&start_year=' + $('#start_year').val() + '&end_year=' + $('#end_year').val() +
               '&start_age=' + $('#start_age').val() + '&end_age=' + $('#end_age').val() +
               '&sex=' + $('#sex').val() + '&type=table');

        $('#report_table').show();
    }
    else
    {
        var g_channel = "false";
        if ($('#g_channel').prop('checked')) g_channel = "true";
		
		//$('#report_map').hide();
        $('#report_table').hide();
        $('#progress').show();

        $('#img_report_map').attr('src', 'heat_map_graph.php?country=' + $('#country').val() +
        	'&start_year=' + $('#start_year').val() + '&end_year=' + $('#end_year').val() +
            '&start_age=' + $('#start_age').val() + '&end_age=' + $('#end_age').val() +
            '&nx=' + $('#nx').val() + '&ny=' + $('#ny').val() + '&sex=' + $('#sex').val() +
            '&noise=' + $('#noise').val() + '&g_channel=' + g_channel + '&type=map').on('load', (function()
            {
            	$('#progress').hide();
            }));

        $('#report_map').show();
    }
}

$(document).ready(function() {
    loadReport();
});

$('#report').click(function() {
    loadReport();
});

$('#country').change(function() {
    loadReport();
});

$('#start_year').change(function() {
    loadReport();
});

$('#end_year').change(function() {
    loadReport();
});

$('#start_age').change(function() {
    loadReport();
});

$('#end_age').change(function() {
    loadReport();
});

$('#sex').change(function() {
    loadReport();
});

$('#nx').change(function() {
    loadReport();
});

$('#ny').change(function() {
    loadReport();
});

$('#noise').change(function() {
    loadReport();
});

$('#g_channel').click(function() {
    loadReport();
});
</script>