$(function() {
    var $preview_modal = $("#print_preview_modal");
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
    var option = {
        'buttonWidth': '100%',
        'buttonClass': 'form-control text-left custom-multiselect',
        'includeSelectAllOption': true,
        'enableFiltering': true,
        'filterPlaceholder': '',
        'selectAllText': 'All',
        'nonSelectedText': 'Choose test',
        'nSelectedText': 'tests',
        'allSelectedText': 'All selected',
        'numberDisplayed': 1,
        'selectAllNumber': false,
        'templates': {
            ul: '<ul class="multiselect-container dropdown-menu custom-multiselect-container"></ul>',
            filter: '<li class="multiselect-item filter"><input class="form-control input-sm multiselect-search" type="text"></li>',
        }
    }
    $("#start-date, #end-date").datetimepicker(datePickerConfig);
    $("#testing-type").select2();
    $("#group-result").multiselect(option);
    /*button generate event*/
    $(document).on('click', '#btnGenerate', function(event) {
        event.preventDefault();
        
        var start_date = moment($("#start-date").data("DateTimePicker").date());
        var end_date   = moment($("#end-date").data("DateTimePicker").date());
        var testing_type = $("#testing-type").val();
        var group_result = {
            group_result: $("#group-result").val()
        };
        if (!start_date.isValid() || !end_date.isValid()) {
            myDialog.showDialog('show', { text  : msg_required_data, style : 'warning' });
            return false;
        }
        $preview_modal.find(".modal-dialog").empty();
        //Show Progress
        myDialog.showProgress('show', {text: globalMessage.loading});
        $.ajax({
            url: base_url + "report/generate_tat_report/" + start_date.format('YYYY-MM-DD') + "/" + end_date.format("YYYY-MM-DD") + "/" + testing_type,
            type: 'POST',
            dataType: 'json',
            data: group_result,
            success: function (resText) {
                myDialog.showProgress('hide');

                $preview_modal.find(".approve").remove();
                $preview_modal.find(".modal-dialog").removeClass("A4-portrait");
                $preview_modal.find(".modal-dialog").addClass("A4-landscape");
                $preview_modal.find(".modal-dialog").html(resText.template);

                $preview_modal.modal("show");

                $preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
                    evt.preventDefault();
                    console.log(base_url + "report/generate_tat_report/" + start_date.format('YYYY-MM-DD') + "/" + end_date.format("YYYY-MM-DD") + "/" + testing_type + "/print");
                    printpage(base_url + "report/generate_tat_report/" + start_date.format('YYYY-MM-DD') + "/" + end_date.format("YYYY-MM-DD") + "/" + testing_type + "/print?" + $.param(group_result));
                });
            },
            error: function () {
                myDialog.showProgress('hide');
                myDialog.showDialog("show", {text: globalMessage.error, style: "warning"});
            }
        });
    });
    /*button export raw data event*/
    $(document).on('click', '#btnExportRawdata', function(event) {
        event.preventDefault();

        if (!moment($("#start-date").data("DateTimePicker").date()).isValid() || !moment($("#end-date").data("DateTimePicker").date()).isValid()) {
            myDialog.showDialog('show', { text  : msg_required_data, style : 'warning' });
            return false;
        }
        var tat_report = $('#tat_report');    
        tat_report.attr('action', base_url + "tat/generate_to_excel");
        tat_report.attr('method','POST');
        tat_report.submit();
    });
});