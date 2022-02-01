<?php
    require("config.php");
    include_once("russia_general_data.php");
?>
<div>
<p>
<table>
    <tr>
        <td><div class="header_parameters"><?php echo $content_region?></div></td><td>&nbsp;</td>
        <td>
            <select id="regions" name="regions" class="listbox_reference">
            <?php
                $arrRegions = GetRegions();

                foreach ($arrRegions as $region)
                {
                    echo "<option value='" . $region[0] . "'>" . $region[1] . "</option>";
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
<div id="report_info" class="report_info"><?php echo "* " . $content_source . "&nbsp;<a href='http://www.gks.ru' target='_blank'>www.gks.ru</a>"?></div>

<script language="javascript">
$(function()
{
    $('#dv_year_slider').slider({
        slide: function(event, ui)
        {
            $('#years').val(ui.value);
            $('#img_age_statistics_graph').attr('src', 'age_statistics_graph.php?type=russia&region=' + $('#regions').val() + '&year=' + ui.value  + '&report_type=' + $('#report_type').val());
            $('#dv_age_statistics_graph_legend').load('russia_age_statistics_graph_legend.php?region=' + $('#regions').val() + '&year=' + ui.value  + '&report_type=' + $('#report_type').val());
        }
    });
});

// загрузить список стран
function LoadRegionsList()
{
    $.getJSON('data_region_list.php',
    function(data)
    {
        var select = $('#regions');
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
function LoadYearList(regions)
{
    $.getJSON('data_russia_year_list.php', {
        regions: regions
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

        LoadReport();
    });
}

// загрузить отчет
function LoadReport() {
    $('#img_age_statistics_graph').attr('src', 'age_statistics_graph.php?type=russia&region=' + $('#regions').val() + '&year=' + $('#years').val() + '&report_type=' + $('#report_type').val());
    $('#dv_age_statistics_graph_legend').load('russia_age_statistics_graph_legend.php?region=' + $('#regions').val() + '&year=' + $('#years').val()  + '&report_type=' + $('#report_type').val());

    // ссылка на экспорт данных
    $('#export_data').html('<a href="russia_export_data.php?data_type=age&regions=' + $('#regions').val() +
        '&start_year=' + $('#years').val() + '&end_year=' + $('#years').val() +
        '&start_age=0&end_age=0&sex=both&report_type=' + $('#report_type').val() + '"><?php echo $content_export?></a>');
}

$(document).ready(function() {
    //LoadCountryList();
    LoadYearList($('#regions').val());
});

$('#regions').change(function() {
    LoadYearList($('#regions').val());
});

$('#regions').keyup(function() {
    $('#regions').change();
});

$('#years').change(function() {
    $('#dv_year_slider').slider('option', 'value', $('#years').val());
    LoadReport();
});

$('#years').keyup(function() {
    $('#years').change();
});

$('#report_type').change(function() {
	LoadReport();
});

$('#report_type').keyup(function() {
    $('#report_type').change();
});
</script>