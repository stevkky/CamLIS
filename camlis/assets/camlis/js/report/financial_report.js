$(function() {
    var $start_date = $("#start-date");
    var $end_date   = $("#end-date");
    var $start_time = $("#start-time");
    var $end_time   = $("#end-time");

    $start_date.datetimepicker(dateTimePickerOption);
    $end_date.datetimepicker(dateTimePickerOption);
    $start_time.timepicker({minuteStep: 1, showMeridian: false});
    $end_time.timepicker({minuteStep: 1, showMeridian: false});

    $("#btnGenerate").on("click", function(evt) {
        evt.preventDefault();
        var $preview_modal = $("#print_preview_modal");
        var start_date = $start_date.data("DateTimePicker").date();
        var end_date   = $end_date.data("DateTimePicker").date();
        var start_time = moment($start_time.val(), 'HH:mm');
        var end_time   = moment($end_time.val(), 'HH:mm');

        if (!start_date || !end_date || !start_time.isValid() || !end_time.isValid()) {
            myDialog.showDialog("show", { text: msg_required_data, style: 'warning'});
            return false;
        }

        $preview_modal.find(".modal-dialog").html("<div class='content-wrapper'></div>");
        $preview_modal.find(".modal-dialog").removeClass("A4-portrait").addClass('full-width');
        myDialog.showProgress("show");

        var url = BASE_URL + "report/get_financial_report/" +
                  start_date.format("YYYY-MM-DD") + "/" +
                  start_time.format("HH:mm") + "/" +
                  end_date.format("YYYY-MM-DD") + "/" +
                  end_time.format("HH:mm");
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            success: function(resText) {
                for(var i in resText) {
                    var $page = $("<div class='psample-result'></div>");
                    $page.attr("id", "presult-" + (parseInt(i) + 1));
                    $page.html(resText[i].template);
                    if (i == 0) $page.addClass("active");
                    else $page.hide();

                    $preview_modal.find(".modal-dialog .content-wrapper").append($page);
                }

                $preview_modal.find(".page-count").text(resText.length);
                $preview_modal.find(".page-number").val(1);
                $preview_modal.find(".page-number").autoNumeric({aPad: 0, vMin: 1, vMax: resText.length});

                setTimeout(function () {
                    myDialog.showProgress('hide');
                    $preview_modal.modal("show");
                }, 400);

                $preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
                    evt.preventDefault();
                    printpage(url + "/print");
                });

                $preview_modal.modal("show");
            },
            error: function () {
                myDialog.showProgress("hide");
            }
        });
    });

    $("#btnExportExcel").on("click", function(evt) {
        evt.preventDefault();
        var start_date = $start_date.data("DateTimePicker").date();
        var end_date   = $end_date.data("DateTimePicker").date();
        var start_time = moment($start_time.val(), 'HH:mm');
        var end_time   = moment($end_time.val(), 'HH:mm');

        if (!start_date || !end_date || !start_time.isValid() || !end_time.isValid()) {
            myDialog.showDialog("show", { text: msg_required_data, style: 'warning'});
            return false;
        }

        var url = BASE_URL + "report/get_financial_report/" +
                  start_date.format("YYYY-MM-DD") + "/" +
                  start_time.format("HH:mm") + "/" +
                  end_date.format("YYYY-MM-DD") + "/" +
                  end_time.format("HH:mm");
        window.location = url + "/excel";
    });
});