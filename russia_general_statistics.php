<?php
    require("config.php");
    include_once("russia_general_data.php");

    $min_year = GetMortalityMinYear();
?>
<div>
    <table>
    <tr valign="top">
        <td>
            <table>
            <tr><td><div class="header_parameters"><?php echo $content_region?></div><div class="hint">&nbsp;&nbsp;&nbsp;<?php echo $content_multiselect_hint?></div></td></tr>
            <tr>
                <td>
                    <select id="regions" name="regions" class="listbox" multiple="true" style="width:300px" size="5">
                    <?php
                        $arrRegions = GetRegions();

                        foreach ($arrRegions as $region)
                        {
                            if (in_array($region[0], $general_russia_selected_regions))
                                echo "<option selected='true' value='" . $region[0] . "'>" . $region[1] . "</option>";
                            else
                                echo "<option value='" . $region[0] . "'>" . $region[1] . "</option>";
                        }
                    ?>
                    </select>
                </td>
            </tr>
            </table>
        </td>
        <td>&nbsp;&nbsp;&nbsp;</td>
        <td>
        <table>
        <tr><td>
        <table>
            <tr><td><div class="header_parameters"><?php echo $content_age?></div></td></tr>
            <tr>
                <td width="60px"><select id="start_age" name="start_age" class="listbox" style="width: 60px"></select></td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td width="200px"><div id="dv_age_slider"></div></td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td width="60px"><select id="end_age" name="end_age" class="listbox" style="width: 60px"></select></td>
            </tr>
        </table>
        </td></tr>
        <tr><td>
        <table>
            <tr><td><div class="header_parameters"><?php echo $content_sex?></div></td></tr>
            <tr>
                <td>
                    <input type="checkbox" id="sex_male" name="sex_male" class="checkbox" checked="true" /><?php echo $content_sex_males?>
                </td>
                <td>
                    <input type="checkbox" id="sex_female" name="sex_female" class="checkbox" checked="true" /><?php echo $content_sex_females?>
                </td>
            </tr>
        </table>
        </td></tr>
        </table>
        </td>
        <td>&nbsp;&nbsp;&nbsp;</td>
        <td align="center" style="display: none">
            <input type="checkbox" id="region_detailed" name="region_detailed" class="checkbox" /><?php echo $content_extended?>
            <br />
            <a href="#" id="add_region" style="display: none" class="add" title="<?php echo $content_add?>">+</a>
        </td>
        <td>&nbsp;&nbsp;&nbsp;</td>
        <td>
            <table id="table_regions_detailed" style="display: none">
            <tr><td><div class="header_parameters"><?php echo $content_region?></div></td></tr>
            <tr>
                <td>
                    <select id="regions_detailed" name="regions_detailed" class="listbox" multiple="true" style="width:400px" size="5">
                    </select>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    </table>
</div>
<br />
<div style="float: left;  padding-left: 3px;">
<select id="report_type" name="report_type" class="listbox_report_type">
    <option value="population" selected="true"><?php echo $content_report_population?></option>
    <option value="population_urban"><?php echo $content_report_population_urban?></option>
    <option value="population_rural"><?php echo $content_report_population_rural?></option>
    <option value="part"><?php echo $content_report_part?></option>
    <option value="olders"><?php echo $content_report_olders?></option>
    <option value="life"><?php echo $content_report_life?></option>
    <option value="life_urban"><?php echo $content_report_life_urban?></option>
</select>
</div>
<div style="float: left;">&nbsp;&nbsp;&nbsp;</div>
<div style="float: left;">
<table style="padding-top: 7px" cellpadding="0" cellspacing="0">
<tr>
    <td><input type="radio" id="view_table" name="view_type" value="table" class="radio" /></td><td><?php echo $content_table?></td>
    <td>&nbsp;&nbsp;</td>
    <td><input type="radio" id="view_graph" name="view_type" value="graph" class="radio" checked="true" /></td><td><?php echo $content_diagram?></td>
</tr>
</table>
</div><div style="clear: both"></div>
<br />
<div id="general_statistics_report" class="report"></div>
<div style="clear: both"></div>
<div id="general_statistics_graph">
<div id="general_statistics_graph_image" class="graph"><img id="img_general_statistics_graph" /></div>
<div id="general_statistics_graph_legend" class="graph"></div>
</div>
<div style="clear: both"></div>
<div style="padding-left: 2px">
<table>
    <tr>
        <td><div class="header_parameters"><?php echo $content_period?></div></td>
        <td>
        <table>
            <tr>
                <td><?php echo $content_from?></td>
                <td width="70px">
                    <select id="start_year" name="start_year" class="listbox"></select>
                </td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td width="400px"><div id="dv_year_slider"></div></td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td><?php echo $content_until?></td>
                <td width="70px">
                    <select id="end_year" name="end_year" class="listbox"></select>
                </td>
            </tr>
        </table>
        </td>
    </tr>
</table>
</div>
<div id="export_data" class="export_data"></div><div id="report_info" class="report_info"></div>
<input id="min_year" name="min_year" type="hidden" value="<?php echo $min_year?>" />
<input id="max_year" name="max_year" type="hidden" value="<?php echo $global_max_year?>" />

<script language="javascript">
// загрузить отчет
function loadReport()
{
    // считаем параметры отчета
    var r_type = 'general';
    var type = 'russia';
    var regions = $('#regions').val();
    var start_year = $('#start_year').val();
    var end_year = $('#end_year').val();
    var start_age = $('#start_age').val();
    var end_age = $('#end_age').val();
    var report_type = $('#report_type').val();

    if ($('#region_detailed').prop('checked'))
    {
        r_type = 'detailed';
        regions = $('#regions_detailed').val();
    }

    // получим пол
    var sex = getSex();

    // информация об отчете
    $('#report_info').html('<?php echo "&nbsp;&nbsp;&nbsp;*&nbsp;" . $content_source . "&nbsp;<a href=http://www.gks.ru target=_blank>www.gks.ru</a>"?>');

    // вывод таблицы результатов
    if ($('input:radio[name=view_type]:checked').val() == 'table')
    {
        $('#general_statistics_graph').hide();

        $('#general_statistics_report').load('russia_general_statistics_report.php?regions=' + regions +
            '&start_year=' + start_year + '&end_year=' + end_year +
            '&start_age=' + start_age + '&end_age=' + end_age +
            '&sex=' + sex + '&report_type=' + report_type);

        $('#general_statistics_report').show();
    }
    // вывод графика
    else
    {
        $('#general_statistics_report').hide();

        var image_width = parseInt($(window).width() - <?php echo $legendWidth?>)

        $('#img_general_statistics_graph').attr('src', 'general_statistics_graph.php?r_type=' + r_type + '&type=' + type + '&regions=' + regions +
            '&start_year=' + start_year + '&end_year=' + end_year +
            '&start_age=' + start_age + '&end_age=' + end_age +
            '&sex=' + sex + '&report_type=' + report_type + '&image_width=' + image_width);

        $('#general_statistics_graph_legend').load('russia_general_statistics_graph_legend.php?r_type=' + r_type + '&regions=' + regions);

        $('#general_statistics_graph').show();
    }

    // ссылка на экспорт данных
    $('#export_data').html('<a href="russia_export_data.php?data_type=general&regions=' + regions +
        '&start_year=' + start_year + '&end_year=' + end_year +
        '&start_age=' + start_age + '&end_age=' + end_age +
        '&sex=' + sex + '&report_type=' + report_type + '"><?php echo $content_export?></a>');
}

// инициализация страницы
$(function()
{
    $('#dv_year_slider').slider({
    	range: true,
    	min: 0,
        max: 0,
        values: [0, 0],
        slide: function(event, ui)
        {
            if (ui.values[0] == ui.values[1])
                if (ui.values[0] != $('#start_year').val())
                    ui.values[0] = ui.values[0] - 1;
                else
                    ui.values[1] = ui.values[1] + 1;

            $('#start_year').val(ui.values[0]);
            $('#end_year').val(ui.values[1]);
        },
        stop: function(event, ui)
        {
            loadReport();
        }
    });

    $('#dv_age_slider').slider({
    	range: true,
    	min: 0,
        max: 0,
        values: [0, 0],
        slide: function(event, ui)
        {
            $('#start_age').val(ui.values[0]);
            $('#end_age').val(ui.values[1]);
        },
        stop: function(event, ui)
        {
            loadReport();
        }
    });
});

// загрузить список дат для указанных стран
function loadYearList(regions, start_year, end_year)
{
    $.getJSON('data_russia_year_list.php',
    {
        regions: String(regions)
    },
    function(data)
    {
        var select = $('#start_year');
        $('option', select).remove();
        $.each(data, function(index, item)
        {
            if (item == start_year)
                select.append('<option value="' + item + '" selected="selected">' + item +  '</option>');
            else
                select.append('<option value="' + item + '">' + item +  '</option>');

            //select.append(new Option(item[0])); not working in IE
        });

        var select = $('#end_year');
        $('option', select).remove();
        $.each(data, function(index, item)
        {
            if (item == end_year)
                select.append('<option value="' + item + '" selected="selected">' + item +  '</option>');
            else
                select.append('<option value="' + item + '">' + item +  '</option>');

            //select.append(new Option(item[0])); not working in IE
        });

        var min_year = parseInt(data[0]);
        var max_year = parseInt(data[data.length - 1]);

        // скорректируем год начала и окончания
        if (min_year > start_year) start_year = min_year;
        if (max_year < end_year) end_year = max_year;

        $('#start_year').val(start_year);
        $('#end_year').val(end_year);

        // инициализируем слайдер
        $('#dv_year_slider').slider('option', 'min', min_year);
        $('#dv_year_slider').slider('option', 'max', max_year);
        $('#dv_year_slider').slider('option', 'values', [start_year, end_year]);
    });
}

// загрузить список возрастов
function loadAgeList()
{
    $.getJSON('data_age_list.php',
    function(data)
    {
        var min_age = parseInt(data[0]);
        var max_age = parseInt(data[data.length - 1]);

        var select = $('#start_age');
        $('option', select).remove();
        $.each(data, function(index, item)
        {
            if (item == min_age)
                select.append('<option value="' + item + '" selected="selected">' + item +  '</option>');
            else
                select.append('<option value="' + item + '">' + item +  '</option>');

            //select.append(new Option(item[0])); not working in IE
        });

        var select = $('#end_age');
        $('option', select).remove();
        $.each(data, function(index, item)
        {
            if (item == max_age)
                select.append('<option value="' + item + '" selected="selected">' + item +  '</option>');
            else
                select.append('<option value="' + item + '">' + item +  '</option>');

            //select.append(new Option(item[0])); not working in IE
        });

        // инициализируем слайдер
        $('#dv_age_slider').slider('option', 'min', min_age);
        $('#dv_age_slider').slider('option', 'max', max_age);
        $('#dv_age_slider').slider('option', 'values', [min_age, max_age]);
    });
}

// документ загружен
$(document).ready(function()
{
    $.ajaxSetup({'async': false});

	var start_year = <?php echo $global_selected_start_year?>;
	var end_year = <?php echo $global_selected_end_year?>;

    $('#general_statistics_graph').hide();

	loadYearList($('#regions').val(), start_year, end_year);
    loadAgeList();

    $.ajaxSetup({'async': true});

    loadReport();
});

// переключение региона
$('#regions').keyup(function()
{
    $('#regions').change();
});
$('#regions').change(function()
{
    var start_year = parseInt($('#start_year').val());
    var end_year = parseInt($('#end_year').val());

    loadYearList($('#regions').val(), start_year, end_year);
    loadReport();
});

// переключение года начала диапазона
$('#start_year').change(function()
{
    var currYear = parseInt($('#start_year').val()) + 1;
    var maxYear = $('#max_year').val();

    if (parseInt($('#start_year').val()) >= parseInt($('#end_year').val()))
        if (currYear < maxYear)
            $('#end_year').val(currYear + 1);
        else
        {
            $('#end_year').val(maxYear);
            $('#start_year').val(maxYear - 1);
        }

    $('#dv_year_slider').slider('option', 'values', [$('#start_year').val(), $('#end_year').val()]);

    loadReport();
});
$('#start_year').keyup(function()
{
    $('#start_year').change();
});

// переключение года окончания диапазона
$('#end_year').change(function()
{
    var currYear = parseInt($('#end_year').val()) - 1;
    var minYear = $('#min_year').val();

    if (parseInt($('#end_year').val()) <= parseInt($('#start_year').val()))
        if (currYear > minYear)
            $('#start_year').val(currYear);
        else
        {
            $('#start_year').val(minYear);
            $('#end_year').val(minYear + 1);
        }

    $('#dv_year_slider').slider('option', 'values', [$('#start_year').val(), $('#end_year').val()]);

    loadReport();
});
$('#end_year').keyup(function()
{
    $('#end_year').change();
});

// переключение нижней границы возраста
$('#start_age').change(function()
{
    if (parseInt($('#start_age').val()) > parseInt($('#end_age').val())) $('#end_age').val($('#start_age').val());
    $('#dv_age_slider').slider('option', 'values', [$('#start_age').val(), $('#end_age').val()]);

    loadReport();
});
$('#start_age').keyup(function()
{
    $('#start_age').change();
});

// переключение верхней границы возраста
$('#end_age').change(function()
{
    if (parseInt($('#end_age').val()) < parseInt($('#start_age').val())) $('#start_age').val($('#end_age').val());
    $('#dv_age_slider').slider('option', 'values', [$('#start_age').val(), $('#end_age').val()]);

    loadReport();
});
$('#end_age').keyup(function()
{
    $('#end_age').change();
});

// переключение пола
$('#sex_male').click(function()
{
    if (!$('#sex_male').prop('checked')) $('#sex_female').prop('checked', true);

    loadReport();
});
$('#sex_female').click(function()
{
    if (!$('#sex_female').prop('checked')) $('#sex_male').prop('checked', true);

    loadReport();
});


// переключение типа отчета
$('#report_type').change(function()
{
    loadReport();
});
$('#report_type').keyup(function()
{
    $('#report_type').change();
});

// вывод отчета
$('#report').click(function()
{
    loadReport();
});

// переключение в режим таблицы
$('#view_table').click(function()
{
    loadReport();
});

// переключение в режим графика
$('#view_graph').click(function()
{
    loadReport();
});

    // расширенное сравнение
$('#region_detailed').click(function()
{
    if ($('#region_detailed').prop('checked'))
    {
        $('#regions')[0].multiple = false;
        $('#add_region').show();
        $('#table_regions_detailed').show();
        //$('#multi_regions').enable();
    }
    else
    {
        $('#regions')[0].multiple = true;
        $('#add_region').hide();
        $('option', '#regions_detailed').remove();
        $('#table_regions_detailed').hide();
        //$('#multi_countries').disable();
    }

    loadReport();
});

// переключение страны
$('#regions_detailed').keyup(function()
{
    $('#regions_detailed').click();
});
$('#regions_detailed').change(function()
{
    loadReport();
});


$('#add_region').click(function()
{
    var option_val = $('#regions').val() + ';' + $('#start_age').val() + ';' + $('#end_age').val() + ';' + getSex();
    var option_text = $('#regions option:selected').text() + ': ' + '<?php echo $content_age?>' +' ' + $('#start_age').val() + ' - ' + $('#end_age').val() + ' ; ' + getSexText();

    $('#regions_detailed').append('<option value=' + option_val + ' selected="selected">' + option_text + '</option>');
    //var options = $('#regions_detailed').attr('options');
    //options[options.length] = new Option(option_text, option_val, true, true);

    loadReport();
});

// получить выбранный пол
function getSex()
{
    var sex = 'both';
    if (($('#sex_male').prop('checked')) && (!$('#sex_female').prop('checked'))) sex = 'man';
    if ((!$('#sex_male').prop('checked')) && ($('#sex_female').prop('checked'))) sex = 'woman';

    return sex;
}

// получить выбранный пол в текстовом отображении
function getSexText()
{
    var sex = '<?php echo $content_sex_unisex?>';
    if (($('#sex_male').prop('checked')) && (!$('#sex_female').prop('checked'))) sex = '<?php echo $content_sex_male?>';
    if ((!$('#sex_male').prop('checked')) && ($('#sex_female').prop('checked'))) sex = '<?php echo $content_sex_female?>';

    return sex;
}
</script>