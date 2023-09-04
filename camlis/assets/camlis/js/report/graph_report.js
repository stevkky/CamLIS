$(function () {
    //DateTimePicker options
    var datePickerConfig = {
        widgetPositioning : {
            horizontal	: 'left',
            vertical	: 'bottom'
        },
        format			: 'DD/MM/YYYY',
        useCurrent		: false,
        maxDate			: new Date(),
        locale			: app_lang == 'kh' ? 'km' : 'en'
    };
    $(".start-date, .end-date").datetimepicker(datePickerConfig);
    $("select:not([multiple])").select2();
    $("select#laboratory").multiselect({
        'buttonWidth': '100%',
        'buttonClass': 'form-control text-left custom-multiselect',
        'includeSelectAllOption': true,
        'enableFiltering': true,
        'filterPlaceholder': '',
        'selectAllText': label_all,
        'nonSelectedText': label_choose,
        'nSelectedText': label_laboratory,
        'allSelectedText': label_all,
        'numberDisplayed': 1,
        'selectAllNumber': false,
        'templates': {
            ul: '<ul class="multiselect-container dropdown-menu custom-multiselect-container"></ul>',
            filter: '<li class="multiselect-item filter"><input class="form-control input-sm multiselect-search" type="text"></li>',
        }
    });

    $("#graph-type").on("change", function () {
        var val = $(this).val();
        if (val === 'SAMPLE_TYPE_BY_MONTH') $(".condition-wrapper.sample-type").show();
        else $(".condition-wrapper.sample-type").hide();

        if (val === 'TEST_BY_MONTH') $(".condition-wrapper.test").show();
        else $(".condition-wrapper.test").hide();
    });

    var REPORT = {
        'PATIENT_BY_AGE_GROUP': {url: 'report/get_patient_by_age_group', categoryField: 'age_group', generateChart: generatePatientChartReport},
        'PATIENT_BY_SAMPLE_SOURCE': {url: 'report/get_patient_by_sample_source', categoryField: 'source_name', generateChart: generatePatientChartReport},
        'PATIENT_BY_SAMPLE_TYPE': {url: 'report/get_patient_by_sample_type', categoryField: 'sample_name', generateChart: generatePatientChartReport},
        'PATIENT_BY_DEPARTMENT': {url: 'report/get_patient_by_department', categoryField: 'department_name', generateChart: generatePatientChartReport},
        'PATIENT_BY_MONTH': {url: 'report/get_patient_by_month', categoryField: 'month_year', generateChart: generatePatientChartReport},
        'SAMPLE_TYPE_BY_MONTH': {url: 'report/get_sample_type_by_month', categoryField: 'month_year', generateChart: generateChartReport},
        'TEST_BY_MONTH': {url: 'report/get_test_by_month', categoryField: 'month_year', generateChart: generateChartReport},
    };

    $("#btn-generate").on("click", function (evt) {
        evt.preventDefault();
        var report_type = $("#graph-type").val();
        var start_date  = $("#start-date").data("DateTimePicker").date();
        var end_date    = $("#end-date").data("DateTimePicker").date();
        var sample_type = $("#sample-type").val();
        var test_id     = $("#test").val();
        var laboratory  = $("#laboratory").val() || undefined;
        var data        = {
            start_date: moment(start_date),
            end_date: moment(end_date),
            laboratory_id: _.filter(laboratory, function(d) { return d > 0; }),
            sample_type_id: sample_type,
            test_id: test_id
        };

        if (!data.start_date.isValid() || !data.end_date.isValid() || data.laboratory_id.length === 0) {
            myDialog.showDialog('show', { text	: msg_required_data, style : 'warning' });
            return false;
        }

        data.start_date = data.start_date.format("YYYY-MM-DD");
        data.end_date = data.end_date.format("YYYY-MM-DD");

        if (REPORT[report_type]) {
            myDialog.showProgress("show", {text: globalMessage.loading});
            $.ajax({
                url: base_url + REPORT[report_type].url,
                type: "POST",
                data: data,
                dataType: "json",
                success: function (result) {
                    console.log(JSON.stringify(result));
                    REPORT[report_type].generateChart(result.data, REPORT[report_type].categoryField, 'chartdiv');
                    myDialog.showProgress("hide");
                },
                error: function () {
                    myDialog.showProgress("hide");
                }
            });
        }
    });

    $("#btn-reset").on("click", function (evt) {
        $(".start-date, .end-date").data("DateTimePicker").clear();
        $(".condition-list").find("select option:first-child").prop("selected", true).trigger("change");
    });
});

AmCharts.checkEmptyData = function(chart) {
    if (0 === chart.dataProvider.length) {
        // set min/max on the value axis
        chart.valueAxes[0].minimum = 0;
        chart.valueAxes[0].maximum = 100;

        // add dummy data point
        var dataPoint = {
            dummyValue: 0
        };
        dataPoint[chart.categoryField] = '';
        chart.dataProvider = [dataPoint];

        // add label
        chart.addLabel(0, '50%', 'The chart contains no data', 'center');

        // set opacity of the chart div
        chart.chartDiv.style.opacity = 0.5;

        // redraw it
        chart.validateNow();
    }
};

function generatePatientChartReport(data, categoryField, chartDestination) {
    var chart = AmCharts.makeChart( chartDestination, {
        "type": "serial",
        "theme": "light",
        "dataProvider": data,
        "valueAxes": [{
            "stackType": "regular",
            "axisAlpha": 0.3,
            "gridAlpha": 0
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "fillAlphas": 0.8,
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "Male",
            "type": "column",
            "color": "#000000",
            "valueField": "male"
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "fillAlphas": 0.8,
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "Female",
            "type": "column",
            "color": "#000000",
            "valueField": "female"
        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": categoryField,
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "gridAlpha": 0,
            "position": "left",
            "labelRotation": -45
        },
        "legend": {
            "useGraphSettings": true
        },
        "export": {
            "enabled": true
        }

    } );

    AmCharts.checkEmptyData(chart);
    return chart;
}

function generateChartReport(data, categoryField, chartDestination) {
    var chart = AmCharts.makeChart( chartDestination, {
        "type": "serial",
        "theme": "light",
        "dataProvider": data,
        "valueAxes": [ {
            "gridColor": "#e5e5e5",
            "gridAlpha": 0.2,
            "dashLength": 0
        } ],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [ {
            "balloonText": "[[category]]: <b>[[value]]</b>",
            "fillAlphas": 0.8,
            "lineAlpha": 0.2,
            "type": "column",
            "title": "Male",
            "valueField": "count"
        } ],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": categoryField,
        "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0,
            "tickPosition": "start",
            "tickLength": 20,
            "labelRotation": -45
        },
        "export": {
            "enabled": true
        }

    } );

    AmCharts.checkEmptyData(chart);
    return chart;
}