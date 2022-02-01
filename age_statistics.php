<?php
require("config.php");
include_once("general_data.php");

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
        <td><div class="header_parameters"><?php echo $content_country?></div></td><td>&nbsp;</td>
        <td>
            <select id="countries" name="countries" class="listbox_reference">
            <?php
                $arrCountries = GetCountries($type);

                foreach ($arrCountries as $country)
                {
                    echo "<option value='" . $country[0] . "'>" . $country[1] . "</option>";
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><div class="header_parameters"><?php echo $content_year?></div></td><td>&nbsp;</td>
        <td>
            <table class="table_layout">
                <tr><td><select name="years" id="years" class="listbox"></select></td><td width="200px"><div id="dv_year_slider"></div></td></tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><div class="header_parameters"><?php echo $content_report?></div></td><td>&nbsp;</td>
        <td>
            <select id="report_type" name="report_type" class="listbox_reference">
                <option value="population" selected="true"><?php echo $content_report_population?></option>
                <option value="dead"><?php echo $content_report_dead?></option>
            </select>
        </td>
    </tr>
</table>
</p>
</div>
<br />
<div id="dv_age_statistics_graph"><img id="img_age_statistics_graph" /></div>
<div id="dv_age_statistics_graph_legend"></div>
<div style="padding-left: 5px; padding-top: 10px;" id="export_data"></div>
<br />
<div id="report_info" class="report_info"><?php echo "* " . $content_source . "&nbsp;<a href='http://www.mortality.org' target='_blank'>www.mortality.org</a>"?></div>

<script language="javascript">
$(function() {
    $('#dv_year_slider').slider({
        slide: function(event, ui)
        {
            $('#years').val(ui.value);

            loadReport();
            //$('#img_age_statistics_graph').attr('src', 'age_statistics_graph.php?type=world&country=' + $('#countries').val() + '&year=' + ui.value  + '&report_type=' + $('#report_type').val());
            //$('#dv_age_statistics_graph_legend').load('age_statistics_graph_legend.php?country=' + $('#countries').val() + '&year=' + ui.value  + '&report_type=' + $('#report_type').val());
        }
    });
});

// загрузить список стран
function loadCountryList()
{
    $.getJSON('data_country_list.php',
    function(data) {
        var select = $('#countries');
        $('option', select).remove();
        $.each(data, function(index, item)
        {
            if (index == 0)
                select.append('<option value="' + item[0] + '" selected="selected">' + item[1] +  '</option>');
            else
                select.append('<option value="' + item[0] + '">' + item[1] +  '</option>');

            //select.append(new Option(item[1], item[0])); not working in IE
        });
    });
}

// загрузить список дат для указанной страны
function loadYearList(countries) {
    $.getJSON('data_year_list.php', {
        countries: String(countries)
    },
    function(data) {
        var select = $('#years');
        $('option', select).remove();
        $.each(data, function(index, item)
        {
            if (index == 0)
                select.append('<option value="' + item + '" selected="selected">' + item +  '</option>');
            else
                select.append('<option value="' + item + '">' + item +  '</option>');

            //select.append(new Option(item[0])); not working in IE
        });

        var min_year = parseInt(data[0]);
        var max_year = parseInt(data[data.length - 1]);

        $('#dv_year_slider').slider('option', 'min', min_year);
        $('#dv_year_slider').slider('option', 'max', max_year);
        $('#dv_year_slider').slider('option', 'value', min_year);

        loadReport();
    });
}

// загрузить отчет
function loadReport() {
    $('#img_age_statistics_graph').attr('src', 'age_statistics_graph.php?type=world&country=' + $('#countries').val() + '&year=' + $('#years').val() + 
    	'&report_type=' + $('#report_type').val());
    $('#dv_age_statistics_graph_legend').load('age_statistics_graph_legend.php?country=' + $('#countries').val() + '&year=' + $('#years').val()  + 
    	'&report_type=' + $('#report_type').val());

    // ссылка на экспорт данных
    $('#export_data').html('<a href="general_export_data.php?data_type=age&countries=' + $('#countries').val() +
        '&start_year=' + $('#years').val() + '&end_year=' + $('#years').val() +
        '&start_age=0&end_age=0&sex=both&report_type=' + $('#report_type').val() + '"><?php echo $content_export?></a>');
}

$(document).ready(function() {
    //loadCountryList();
    loadYearList($('#countries').val());
});

$('#countries').change(function() {
    loadYearList($('#countries').val());
});

$('#countries').keyup(function() {
    $('#countries').change();
});

$('#years').change(function() {
    $('#dv_year_slider').slider('option', 'value', $('#years').val());
    loadReport();
});

$('#years').keyup(function() {
    $('#years').change();
});

$('#report_type').change(function() {
	loadReport();
});

$('#report_type').keyup(function() {
    $('#report_type').change();
});
</script>