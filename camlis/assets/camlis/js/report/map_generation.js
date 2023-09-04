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
        locale			: app_lang === 'kh' ? 'km' : 'en'
    };
    $(".start-date, .end-date").datetimepicker(datePickerConfig);
    $("select:not([multiple])").select2();
    var options = {
        'buttonWidth': '100%',
        'buttonClass': 'form-control text-left custom-multiselect',
        'includeSelectAllOption': true,
        'enableFiltering': true,
        'filterPlaceholder': '',
        'selectAllText': label_all,
        'nonSelectedText': label_choose,
        'allSelectedText': label_all,
        'numberDisplayed': 1,
        'selectAllNumber': false,
        'enableCaseInsensitiveFiltering': true,
        'templates': {
            ul: '<ul class="multiselect-container dropdown-menu custom-multiselect-container"></ul>',
            filter: '<li class="multiselect-item filter"><input class="form-control input-sm multiselect-search" type="text"></li>',
        }
    };
    $("select#laboratory").multiselect($.extend(options, {'nSelectedText': label_laboratory}));
    $("select#sample-test-result").multiselect($.extend(options, {'nSelectedText': label_possible_result}));

    generateHeatMap([]);

    /**
     * Get Sample test by department sample
     */
    var $sampleTest = $("#sample-test");
    function generateTestOption() {
        var selectedDepartmentSample = $("select#department-sample").val() || [];
        var sample_tests = getSampleTest(selectedDepartmentSample);

        $sampleTest.find("option").remove();
        for(var i in sample_tests) {
            $sampleTest.append("<option value='"+ sample_tests[i].sample_test_id +"'>"+ sample_tests[i].test_name +"</option>");
        }
    }

    $("select#department-sample").multiselect($.extend(options, {
        'nSelectedText': label_department_sammple,
        'onChange': function (option, checked, select) {
            generateTestOption();
            $("select#sample-test").multiselect('rebuild');
        },
        'onSelectAll': function () {
            generateTestOption()
            $("select#sample-test").multiselect('rebuild');
        },
        'onDeselectAll': function () {
            generateTestOption();
            $("select#sample-test").multiselect('rebuild');
        },
        'onInitialized': function(select, container) {
            generateTestOption();
        }
    }));
    /*$("#department-sample").on("change", function (evt) {
        var val = $(this).val() || '';
        var $sampleTest = $("#sample-test");

        $sampleTest.find("option[value!='']").remove();
        $("#sample-test-result").find("option[value!='']").remove();
        if (val.length === 0) return false;
        
        $.ajax({
            url: base_url + "test/get_std_sample_test",
            type: 'POST',
            data: {dep_sample_id: val},
            dataType: 'json',
            success: function (resTest) {
                for(var i in resTest) {
                    var option = $("<option value='"+ resTest[i].sample_test_id +"'>"+ resTest[i].test_name +"</option>");
                    option.data("field-type", resTest[i].field_type);
                    option.data("test-id", resTest[i].test_id);
                    $sampleTest.append(option);
                }
                $sampleTest.trigger("change");
            },
            error: function () {
                
            }
        });
    });*/

    /**
     * Get Sample test possible result
     */
    var $sampleTestResult = $("#sample-test-result");
    function generateResultOption() {
        var selectedTest = _.chain($("select#sample-test").val() || []).map(function(d) { return d.split(','); }).flatten().value();
        var possible_results = getPossibleResult(selectedTest);

        $sampleTestResult.find("option").remove();
        for(var i in possible_results) {
            $sampleTestResult.append("<option value='"+ possible_results[i].test_organism_id +"'>"+ possible_results[i].organism_name +"</option>");
        }
    }
    $("select#sample-test").multiselect($.extend(options, {
        'nSelectedText': label_test,
        'onChange': function (option, checked, select) {
            generateResultOption();
            $("select#sample-test-result").multiselect('rebuild');
        },
        'onSelectAll': function () {
            generateResultOption()
            $("select#sample-test-result").multiselect('rebuild');
        },
        'onDeselectAll': function () {
            generateResultOption();
            $("select#sample-test-result").multiselect('rebuild');
        },
        'onInitialized': function(select, container) {
            generateResultOption();
            $("select#sample-test-result").multiselect('rebuild');
        }
    }));
    /*$("#sample-test").on("change", function (evt) {
        var val = $(this).val() || '';
        var field_type = $(this).find("option:selected").data('field-type');
        var $sampleTestResult = $("#sample-test-result");

        $sampleTestResult.find("option[value!='']").remove();
        if (val.length === 0) {
            //$(".condition-wrapper.sample-test-result").hide();
            return false;
        }

        $.ajax({
            url: base_url + "organism/get_sample_test_organism",
            type: 'POST',
            data: {sample_test_id: val},
            dataType: 'json',
            success: function (resTest) {
                for(var i in resTest) {
                    var option = "<option value='"+ resTest[i].test_organism_id +"'>"+ resTest[i].organism_name +"</option>";
                    $sampleTestResult.append(option);
                }
                $sampleTestResult.trigger("change");
            },
            error: function () {

            }
        });

        if (field_type == 1 || field_type == 2) $(".condition-wrapper.sample-test-result").show();
    });*/

    $("#graph-type").on("change", function () {
        var type = $(this).val();
        if (type === 'NUMBER_TEST_BY_ADDRESS' || type === 'NUMBER_TEST_BY_LABORATORY') {
            $(".condition-wrapper.department-sample").show();
            $(".condition-wrapper.sample-test").show();
            $(".condition-wrapper.sample-test-result").show();
        }
        else {
            $(".condition-wrapper.department-sample").hide();
            $(".condition-wrapper.sample-test").hide();
            $(".condition-wrapper.sample-test-result").hide();
        }
    });

    var REPORT = {
        'NUMBER_PATIENT_BY_ADDRESS': {url: 'report/get_patient_by_address', generateMap: generateHeatMap},
        'NUMBER_TEST_BY_ADDRESS': {url: 'report/get_test_by_address', generateMap: generateHeatMap},
        'NUMBER_TEST_BY_LABORATORY': {url: 'report/get_test_by_laboratory', generateMap: generateBubbleMap},
    };

    $("#btn-generate").on("click", function (evt) {
        evt.preventDefault();
        var report_type = $("#graph-type").val();
        var start_date  = $("#start-date").data("DateTimePicker").date();
        var end_date    = $("#end-date").data("DateTimePicker").date();
        var dep_sample  = $("#department-sample").val() || [];
        var sample_test = _.chain($("#sample-test").val() || []).map(function(d) { return d.split(','); }).flatten().value();
        var test_result = _.chain($("#sample-test-result").val() || []).map(function(d) { return d.split(','); }).flatten().value();
        var laboratory  = $("#laboratory").val() || undefined;
        var data        = {
            start_date      : moment(start_date),
            end_date        : moment(end_date),
            laboratory_id   : laboratory,
            department_sample: dep_sample,
            sample_test_id  : sample_test,
            possible_result_id  : test_result
        };

        generateHeatMap([]);

        if (!data.start_date.isValid() || !data.end_date.isValid()) {
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

                    if (report_type === 'NUMBER_PATIENT_BY_ADDRESS' || report_type === 'NUMBER_TEST_BY_ADDRESS') {                       
                        REPORT[report_type].generateMap(result.data.province);
                    }
                    else if (report_type === 'NUMBER_TEST_BY_LABORATORY') {                        
                        REPORT[report_type].generateMap(result.data.laboratory, laboratoryLatLong);
                    }

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

function generateHeatMap(areaData) {
    console.log(areaData);
    var chart = AmCharts.makeChart( "mapdiv", {
        "type": "map",
        "colorSteps": 20,
        //"theme": "light",
        "dataProvider": {
            "mapVar": AmCharts.maps.cambodiaProvince,
            "areas": areaData
        },
        "areasSettings": {
            "autoZoom": true
        },
        "valueLegend": {
            "right": 10
        },
        "export": {
            "enabled": true
        },
    } );
    
    if(areaData.length > 0){
        setTimeout( function() {
            // iterate through areas and put a label over center of each
            //map.dataProvider.images = [];
            for ( x in chart.dataProvider.areas ) {
                var area = chart.dataProvider.areas[ x ];
                area.groupId = area.id;
                var image = new AmCharts.MapImage();
                image.title = area.title;
                image.linkToObject = area;
                image.groupId = area.id;
                
                image.latitude = chart.getAreaCenterLatitude( area );
                image.longitude = chart.getAreaCenterLongitude( area );
                image.label = area.title + "\n" + area.value;
                chart.dataProvider.images.push( image );
            }
            chart.validateData();
        }, 100 )
    }
    
}

function generateBubbleMap(mapData, latlong) {
    // get min and max values
    var minBulletSize = 3;
    var maxBulletSize = 70;
    var min = Infinity;
    var max = -Infinity;
    for ( var i = 0; i < mapData.length; i++ ) {
        var value = parseInt(mapData[ i ].value);
        if ( value < min ) {
            min = value;
        }
        if ( value > max ) {
            max = value;
        }
    }

    // it's better to use circle square to show difference between values, not a radius
    var maxSquare = maxBulletSize * maxBulletSize * 2 * Math.PI;
    var minSquare = minBulletSize * minBulletSize * 2 * Math.PI;

    // create circle for each country
    var images = [];
    for ( var i = 0; i < mapData.length; i++ ) {
        var dataItem = mapData[ i ];
        var value = dataItem.value;
        // calculate size of a bubble
        var square = ( value - min ) / ( max - min ) * ( maxSquare - minSquare ) + minSquare;
        if ( square < minSquare ) {
            square = minSquare;
        }
        var size = Math.sqrt( square / ( Math.PI * 2 ) );
        var id = dataItem.code;

        if (latlong[id]) {
            images.push({
                "type": "circle",
                "theme": "light",
                "width": size,
                "height": size,
                "color": "#eea638",
                "longitude": latlong[id].longitude,
                "latitude": latlong[id].latitude,
                "title": dataItem.name,
                "value": value,
                "label": dataItem.name+"\n"+value
            });
        }
    }

    // build map
    var map = AmCharts.makeChart( "mapdiv", {
        "type": "map",
        "areasSettings": {
            //"unlistedAreasColor": "#FFFFFF",
            //"unlistedAreasAlpha": 0.1
        },
        "imagesSettings": {
            "balloonText": "<b>[[title]]</b>: [[value]]",
            "alpha": 0.9
        },
        "dataProvider": {
            "mapVar": AmCharts.maps.cambodiaProvince,
            "images": images
        },
        "export": {
            "enabled": true
        }
    } );

}

function getSampleTest(department_sample_id) {
    department_sample_id = department_sample_id || [];
    var sample_tests     = SAMPLE_TESTS;
    if (department_sample_id.length > 0) {
        sample_tests = _.filter(SAMPLE_TESTS, function(d) {
            return department_sample_id.indexOf(d.dep_sample_id) > -1;
        });
    }

    return _.chain(sample_tests)
        .groupBy('test_id')
        .map(function(d) {
            var test = _.first(d);
            var sample_test_id = _.pluck(d, 'sample_test_id');
            return {test_id: test.test_id, test_name: test.test_name, sample_test_id: sample_test_id};
        })
        .sortBy('test_name')
        .value();
}

function getPossibleResult(sample_test_id) {
    sample_test_id = sample_test_id || [];
    var possible_results = POSSIBLE_RESULTS;
    if (sample_test_id.length > 0) {
        possible_results = _.filter(POSSIBLE_RESULTS, function (d) {
            return sample_test_id.indexOf(d.sample_test_id) > -1;
        });
    }

    return _.chain(possible_results)
        .groupBy('organism_id')
        .map(function (d) {
            var organism = _.first(d);
            var test_organism_id = _.pluck(d, 'test_organism_id');
            return {organism_id: organism.organism_id, organism_name: organism.organism_name, test_organism_id: test_organism_id};
        })
        .sortBy('organism_name')
        .value();
}