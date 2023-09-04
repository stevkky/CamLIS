// DateTimePicker options
var dtPickerOption = {
    widgetPositioning : {
        horizontal  : 'left',
        vertical    : 'bottom'
    },
    showClear       : true,
    format          : 'DD/MM/YYYY',
    useCurrent      : false,
    maxDate         : new Date(),
    locale          : app_lang == 'kh' ? 'km' : 'en'
};

$(function() {
    /*=====================Initialize Control==============*/
    $("#province, #district, #commune, #village").select2();
    // init checkbox and radio button
    $("#patient_entry_form_wrapper input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
    // init auto number
    $("#test_modal").find(".total-test-payment").autoNumeric({vMin: 0, aPad: 0});
    // init and generate test list
    (new sampleTest()).generate();
    /*========================All Functions================*/
    /*
    * generating sample test
    */
    function sampleTest() {
        this.renderList = function(data, parent, level){
            parent = (parent === undefined) ? 0 : parent;
            level = level === undefined ? 0 : level;
            var html = "";
            if (data) {
                html = "<ul class='list-unstyled'>";
                for (var i in data) {
                    if (parseInt(data[i].child_count) > 0) {
                        html += "<li is_heading='" + data[i].is_heading + "' parent='"+ data[i].testPID +"'><label style='cursor:pointer;'><input type='checkbox' class='sample-test' id='st-" + data[i].sample_test_id + "' is_heading='" + data[i].is_heading + "' parent='" + parent + "' testID='" + data[i].test_id + "' value='" + data[i].sample_test_id + "' test-name='" + data[i].test_name + "' data-group-result='"+ data[i].group_result +"'>&nbsp;&nbsp;<span class='t-name'>" + data[i].test_name + "</span></label>";
                        html += this.renderList(data[i].childs, data[i].sample_test_id, level + 1);
                        html += "</li>";
                    } else {
                        html += "<li is_heading='" + data[i].is_heading + "'><label style='font-weight:100; cursor:pointer;'><input type='checkbox' class='sample-test' id='st-" + data[i].sample_test_id + "' name='sample_tests[]' is_heading='" + data[i].is_heading + "' parent='" + parent + "' value='" + data[i].sample_test_id + "' test-name='" + data[i].test_name + "' data-group-result='"+ data[i].group_result + "'>&nbsp;&nbsp;<span class='t-name'>" + data[i].test_name + "</span></label></li>";
                    }
                }
                html += "</ul>";
            }
            return html;
        };
        this.generate = function(){
            var test_modal = $("#test_modal");
            var tree_list  = test_modal.find(".tree-list");
            var _this = this;
            $.ajax({
                url: base_url + "test/get_std_sample_test",
                type: 'POST',
                dataType: 'json',
                data: {group_by: 'sample'},
                success: function(resText){
                    test_modal.find("#test-form div.department-test").each(function() {
                        var department_id = $(this).data("value");
                        var test_list = $(this).find("div.tree-list");
                        test_list.empty();
                        var samples = resText[department_id].samples;
                        var html    = "";
                        if (samples) {
                            for (var i in samples) {
                                html += "<div class='sample-type-wrapper' id='dsample-" + samples[i].department_sample_id + "' data-department-sample='" + samples[i].department_sample_id + "'>";
                                html += "<div class='sample-type-header-wrapper'>";
                                html += "<div class='sample-type-header'><i class='fa fa-hand-o-right'></i> " + samples[i].sample_name;
                                html += "<button type='button' class='pull-right btn btn-default btn-toggle-sample-info'><i class='fa fa-chevron-up'></i></button>";
                                html += "</div>"; //End Sample Type header
                                html += "<div class='sample-info-wrapper form-vertical'>";
                                html += "<label class='control-label'>"+ label_sample_desription +"</label>";
                                html += "<select name='sample_desc' class='form-control input-sm sample-desc' id='dsample-description-" + samples[i].department_sample_id + "'>";
                                html += "<option value='-1'></option>";
                                if (sample_descriptions !== undefined && Array.isArray(sample_descriptions[samples[i].sample_id])) {
                                    var sample_description = sample_descriptions[samples[i].sample_id];
                                    for (var j in sample_description) {
                                        html += "<option value='" + sample_description[j].ID + "'>" + sample_description[j].description + "</option>";
                                    }
                                }
                                html += "</select>";
                                if (samples[i].show_weight) {
                                    html += "<div class='row' style='margin: 7px 0 0;'>";
                                    html += "<div class='col-sm-6' style='padding-left: 0;'><label class='control-label'>" +label_weight1+"</label>";
                                    html += "<div class=\"input-group input-group-sm\">";
                                    html += "<input type=\"text\" class=\"form-control\" name=\"first_weight\" placeholder=\"Weight 1\" id='dsample-first-weight-" + samples[i].department_sample_id + "'>";
                                    html += "<span class=\"input-group-addon\">gm</span>";
                                    html += "</div>";
                                    html += "</div>";
                                    html += "<div class='col-sm-6' style='padding-right: 0;'><label class='control-label'>" + label_weight2 + "</label>";
                                    html += "<div class=\"input-group input-group-sm\">";
                                    html += "<input type=\"text\" class=\"form-control\" name=\"second_weight\" placeholder=\"Weight 2\" id='dsample-second-weight-" + samples[i].department_sample_id + "'>";
                                    html += "<span class=\"input-group-addon\">gm</span>";
                                    html += "</div>";
                                    html += "</div>";
                                    html += "</div>";
                                }
                                html += "</div>"; //End Sample Info Wrapper
                                html += "</div>"; //End Sample Type header wrapper
                                html += _this.renderList(samples[i].tests);
                                html += "</div>"; //End Sample Type Wrapper
                            }
                        }
                        test_list.html(html);
                    });
                    //Init iCheck
                    tree_list.find("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal', indeterminateClass: 'indeterminate-line' });
                    //Set Treeview
                    tree_list.treeview({ collapsed : false, animated : true });
                    //test_modal.modal({backdrop : 'static'});
                }
            });
        };
    }
    /*
    * patient sample processing
    * @param form
    */
    function patientSample(form) {
        this.form = form;
        /*
        * @return patient and sample  information 
        */
        this.get_patient_sample = function(){
            var data = {};
            data.sample_number = this.form.find("input[type=hidden][name=patient_sample_id]").data('value');
            data.sample_source_id = Number(this.form.find("select[name=sample_source]").val());
            data.sample_source_title = this.form.find("select[name=sample_source] option:selected").text();
            data.requester_id = Number(this.form.find("select[name=requester]").val());
            data.is_urgent = this.form.find("input[type=checkbox][name=is_urgent]").is(":checked") ? 1 : 0;
            //data.for_research = this.form.find("input[type=checkbox][name=for_research]").is(":checked") ? 1 : 0;
            data.for_research		= this.form.find("select[name=for_research]").val();
            data.clinical_history = this.form.find("[name=clinical_history]").val();
            data.payment_type_id = this.form.find("select[name=payment_type]").val();
            var admission_date = this.form.find("input[name=admission_date]").data("DateTimePicker").date();
            var admission_time = this.form.find("input[name=admission_time]").val().trim();
            data.admission_date = moment(admission_date);
            data.admission_time = moment(admission_time, "HH:mm");
            // patient information
            data.patient_id = ($("#patient_entry_form_wrapper").find("input[type=hidden]#patient_id") != undefined) ? $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_id").data("value") : "";
            data.patient_age = ($("#patient_entry_form_wrapper").find("input[type=hidden]#patient_age") != undefined) ? $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_age").data("value") : "";
            data.patient_sex = ($("#patient_entry_form_wrapper").find("input[type=hidden]#patient_sex") != undefined) ? $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_sex").data("value") : "";
            data.patient_sample_id  = (this.form.find("input[type=hidden][name=patient_sample_id]") != undefined) ? Number(this.form.find("input[type=hidden][name=patient_sample_id]").data("value")) : "";
            return data;
        };
        /*
        * require field
        * @return boolean
        */
        this.require = function(){
            var is_valid = true;
            if(this.form.find('select[name=sample_source]').val() <= 0) is_valid = false;
            if(this.form.find('select[name=requester]').val() <= 0) is_valid = false;
            if(this.form.find('select[name=payment_type]').val() <= 0) is_valid = false;
            if(this.form.find('textarea[name="clinical_history"]').val().toString().trim().length == 0) is_valid = false;
            return is_valid;
        };
        /*
        * save patient sample
        */
        this.save = function(data, onSuccess, onMsgClosed){
            data = $.extend({}, data, this.get_patient_sample());
            
            // format admission_date
            data.admission_date = data.admission_date.isValid() ? data.admission_date.format("YYYY-MM-DD") : undefined;
            if (data.admission_date) {
                // combine admission date and admission time together
                data.admission_date += " " + (data.admission_time.isValid() ? data.admission_time.format("HH:mm:ss") : "");
            }
            // remove some fields from data object
            data = _.omit(data, ['admission_time', 'collected_date', 'received_date', 'collected_time', 'received_time']);
            // set url for process data
            var url = base_url + 'request/save';
            if (data.patient_sample_id > 0) {
                url = base_url + 'request/update';
            }
               
            // show waitting progress
            myDialog.showProgress('show', { text : msg_loading });
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(respose){
                    if (respose.status === true) {
                        form.find("input[type=hidden][name=patient_sample_id]").data("value", respose.data.patient_sample_id);
                        // patient sample user information
                        // call back
                        if (onSuccess && {}.toString.call(onSuccess) === '[object Function]') {
                            onSuccess(respose);
                        }
                    }
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', {
                        text: respose.msg,
                        style: respose.status === true ? 'success' : 'warning',
                        onHidden: function(){
                            if (respose.status === true) {
                                // call back
                                if (onMsgClosed && {}.toString.call(onMsgClosed) === '[object Function]') {
                                    onMsgClosed(respose);
                                }
                            }
                        }
                    });
                },
                error: function(){
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', { text  : msg_save_fail, style : 'warning' });
                }
            });
        };
        /*
        * validate field to enable assign test and save buttion
        */
        this.validate = function(){
            var data = this.get_patient_sample();
            var is_valid = true;
            //if (data.sample_number.length === 0) is_valid = false;
            if (isNaN(data.sample_source_id) || data.sample_source_id <= 0) is_valid = false;
            if (isNaN(data.requester_id) || data.requester_id <= 0) is_valid = false;
            if (isNaN(data.payment_type_id) || data.payment_type_id < 0) is_valid = false;
            //if (data.patient_id == undefined || data.patient_id.toString().trim().length == 0) is_valid = false;
            if (data.clinical_history == undefined || data.clinical_history.toString().trim().length == 0) is_valid = false;
            // enable save sample and sssign test button
            this.form.find('.btn-show-test-modal').prop('disabled', !is_valid);
            this.form.find('.btn-save-sample').prop('disabled', !is_valid);
            return is_valid;
        };
        // show title
        this.show_title = function(){
            var data = this.get_patient_sample();
            if (data.sample_number.length > 0 || data.collected_date_time != null || data.received_date_time != null) {
                this.form.find(".sample-title").show();
            } else {
                this.form.find(".sample-title").hide();
            }
            if (data.sample_number.length > 0) {
                this.form.find(".sample-number-title").show();
                this.form.find(".sample-number-title b.value").text(data.sample_number).show();
            } else {
                this.form.find(".sample-number-title").hide();
                this.form.find(".sample-number-title b.value").empty().hide();
            }
            if (data.collected_date_time != null) {
                this.form.find(".collected-date-title").show();
                this.form.find(".collected-date-title b.value").text(data.collected_date_time.format("DD/MM/YYYY hh:mm A")).show();
            } else {
                this.form.find(".collected-date-title").hide();
                this.form.find(".collected-date-title b.value").empty().hide();
            }
            if (data.received_date_time != null) {
                this.form.find(".received-date-title").show();
                this.form.find(".received-date-title b.value").text(data.received_date_time.format("DD/MM/YYYY hh:mm A")).show();
            } else {
                this.form.find(".received-date-title").hide();
                this.form.find(".received-date-title b.value").empty().hide();
            }
        };
    }
    /*
    * generate patient sample form
    * @param init_value
    * @param patient_sample_form
    * @param patient_sample_form_data
    */
    function createSampleForm(init_value, patient_sample_form, previous_sample_data) {
        var sample_form = $("#patient_sample_form");
        var count   = sample_form.find("div.sample-form").length;
        var item   = null;
        // check patient sample form
        if (patient_sample_form && patient_sample_form.length === 1) {
            item = patient_sample_form;
        } else {
            var content = $("#sample_form_template").html();
            item = $(content);
            item.find("span.sample-order").text(count + 1);
            // Append Form
            $('#button_add_more_sample').before(item);
        }
        // init controll and event
        if (item) {
            var patient_sample = new patientSample(item.find('form.form-sample-entry'));
            // init select2
            item.find("select[name=sample_source], select[name=requester], select[name=payment_type]").select2();
            // init check
            item.find('input').iCheck('destroy');
            item.find('input').iCheck({checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal'});
            // init date picker
            item.find("input.dtpicker, input.admission-date").datetimepicker(dtPickerOption);
            // init time picker
            item.find("input.admission-time").timepicker({minuteStep: 1, showMeridian: false, defaultTime: '00:00'});
            // autocomplete clinical history
            item.find('textarea[name="clinical_history"]').autocomplete({
                minLength: 1,
                source: function(request, response){
                    $.ajax({
                        url: base_url+'sample/clinical_history',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            filter_val: item.find('textarea[name="clinical_history"]').val(),
                        },
                        success: function(data){
                            if (data != null) {
                                response(data);
                            }
                            // enable and disable button assign test
                            patient_sample.validate();
                        },
                    });
                },
            }).focus(function(event) {
                $(this).autocomplete("search");
            }).blur(function(event) {
                // enable and disable button assign test
                patient_sample.validate();
            });
            // set previous samples
            if (previous_sample_data) {
                item.find(".sample-title").show();
                item.find(".sample-number-title").show();
                item.find(".sample-number-title b.value").text(previous_sample_data.sample_number).show();
                item.find("input[type=hidden][name=patient_sample_id]").data("value", previous_sample_data.patient_sample_id);
                // item.find("input[name=sample_number]").val(previous_sample_data.sample_number);
                // item.find("input[name=sample_number]").prop("disabled", true);
                item.find("select[name=sample_source]").val(previous_sample_data.sample_source_id).trigger("change", [previous_sample_data.requester_id]);
                item.find("select[name=payment_type]").val(previous_sample_data.payment_type_id).trigger("change");
                var admission_date = moment(previous_sample_data.admission_date, 'YYYY-MM-DD HH:mm:ss');
                item.find("input[name=admission_date]").data("DateTimePicker").date(admission_date.isValid() ? admission_date.toDate() : null);
                item.find("input[name=admission_time]").timepicker('setTime', admission_date.isValid() ? admission_date.format("HH:mm") : null);
                item.find(":checkbox[name=is_urgent]").iCheck(previous_sample_data.is_urgent == 1 ? "check" : "uncheck");
                //item.find(":checkbox[name=for_research]").iCheck(previous_sample_data.for_research == 1 ? "check" : "uncheck");
                item.find("select[name=for_research]").val(previous_sample_data.for_research).trigger("change");
                item.find("textarea[name=clinical_history]").val(previous_sample_data.clinical_history);
                item.find("form.form-sample-entry").data("test-payment", previous_sample_data.test_payments);
                patient_sample.validate();
            }
        }
        // scroll to bottom
        $('#template-wrapper').animate({
            scrollTop: $("#template-content").height()
        }, 1000);
    }
    /*=================All Events====================*/
    /*
    * init edit patient sample form
    * it's happen when user click on edit button from the requested sample list
    */
    if ($("#patient_sample_form").find("div.panel.sample-form.edit").length > 0) {
        myDialog.showProgress("show");
        $.ajax({
            url: base_url + 'patient/search/' + patient.patient_code,
            type: 'POST',
            dataType: 'json',
            data: {param1: 'value1'},
            success: function(resText){
                myDialog.showProgress("hide");
                patient = resText.patient;
                $("#button_add_more_sample").trigger("click", [null, $("#patient_sample_form").find("div.panel.sample-form.edit:first-child"), PATIENT_SAMPLE]);
            }
        });
    }
    /*
    * button assign test
    * @desc: Save or update assign test
    */
    $("#test_modal").on('click', '.btn-assign-test', function(event) {
        event.preventDefault();
        // sample check list test
        var sample_tests = [];
        // sample check list test
        var sample_tests = [];
        // sample detail
        var sample_details = [];
        // Type of payments
        var test_payments  = {};
        // find the department test
        $("#test_modal").find(".modal-body .department-test").each(function() {
            // find the sample department
            var department_samples = $(this).find("div.sample-type-wrapper");
            // check department
            if (department_samples.length > 0) {
                // get value of the checkbox that are check in each department
                department_samples.each(function() {
                    // department sample
                    var department_sample_id = Number($(this).data('department-sample'));
                    // selected test
                    var selected_tests = $(this).find(":checkbox.sample-test:checked");
                    // sample description
                    var sample_description = $(this).find("select.sample-desc");
                    // store value of first weight
                    var first_weight = $(this).find("input[name=first_weight]").val();
                    // store value of second weight
                    var second_weight = $(this).find("input[name=second_weight]").val();
                    $(this).find(":checkbox.sample-test").each(function() {
                        var checkbox = $(this).get(0);
                        var id = $(this).val();
                        if (checkbox && checkbox.indeterminate && id > 0) {
                            sample_tests.push(id);
                        }
                    });
                    if (selected_tests.length > 0) {
                        if (!isNaN(department_sample_id) && department_sample_id > 0 && sample_description.length > 0) {
                            sample_details.push({
                                'department_sample_id': department_sample_id,
                                'sample_description': sample_description.val(),
                                'first_weight': (first_weight === undefined) ? null : first_weight,
                                'second_weight': (second_weight === undefined) ? null : second_weight
                            });
                        }
                        // selected test
                        selected_tests.each(function() {
                            var val = Number($(this).val());
                            if (!isNaN(val) && val > 0) sample_tests.push(val);
                            var group_result = $(this).data("group-result");
                            var price = $(this).data("test-price") || 0;
                            if (group_result && price > 0) {
                                test_payments[Math.floor((Math.random() * 1000) + 1)] = {
                                    group_result: group_result,
                                    price: parseFloat(price)
                                };
                            }
                        });
                    }
                });
            }
        });
        // if do not check any sample test
        if (sample_tests.length === 0) {
            myDialog.showDialog('show', { text  : msg_must_select_test, style   : 'warning' });
            return false;
        }
        var patient_sample = $("#test_modal").data('patient_sample');
        if (patient_sample instanceof patientSample) {
            patient_sample.save({
                // variable patient is declare in add_request.php 
                patient: patient,
                sample_tests: sample_tests,
                sample_details: sample_details,
                test_payments: test_payments,
                is_assign_test: 200
            }, function(data){
                patient_sample.form.data("test-payment", test_payments);
                $("#test_modal").removeData("patient_sample");
                $("#test_modal").modal('hide');
            }, function(data){
                // console.log('add result');
            });
        }
    });
    /*
    * check or uncheck test
    */
    $("#test_modal").find(".tree-list").on('ifChanged', 'input:checkbox.sample-test', function(event) {
        event.preventDefault();
        // check parent
        var parent_id = $(this).attr('parent');
        var tree = $(this).parents(".tree-list");
        var parent = tree.find('input:checkbox[value=' + parent_id + '].sample-test');
        var sub_list = tree.find('input:checkbox[parent=' + parent_id + '].sample-test');
        var selected_sub_list = tree.find('input:checkbox[parent=' + parent_id + '].sample-test:checked');
        if (selected_sub_list.length > 0 && selected_sub_list.length < sub_list.length) {
            parent.iCheck('indeterminate');
        }
        else if (selected_sub_list.length === sub_list.length) {
            parent.iCheck('check');
        }
        if (selected_sub_list.length === 0) {
            parent.iCheck('determinate');
            parent.iCheck('uncheck');
        }
        // check or uncheck all sub list
        var sample_test_id = $(this).attr('value');
        sub_list = tree.find('input[parent=' + sample_test_id +'].sample-test');
        selected_sub_list = tree.find('input[parent=' + sample_test_id +'].sample-test:checked');
        if ($(this).is(":checked")) {
            tree.find("input[parent="+ sample_test_id + "].sample-test").iCheck("check");
        } else if (sub_list.length === selected_sub_list.length) {
            //uncheck sublist when parent is uncheck and all child are checked
            tree.find("input[parent="+ sample_test_id + "].sample-test").iCheck("uncheck");
        }
        // calculate total test payment
        var test_price = {};
        var total = 0;
        $("#test_modal").find(".tree-list").find(":checkbox:checked").each(function() {
            var group_result = $(this).data("group-result");
            var price = $(this).data("test-price") || 0;
            if (group_result && !test_price[group_result]) {
                test_price[group_result] = parseFloat(price);
                total += parseFloat(price);
            }
        });
        $("#test_modal").find(".total-test-payment").autoNumeric("set", total);
    });
    /*
    * search test tree
    */
    $("#test_modal").on('keyup', '.tree-filter', function(event) {
        event.preventDefault();
        var search_text = $(this).val();
        var regular_expression = new RegExp(search_text, "i");
        var tree = $(this).parents("div.tree-list-wrapper").find("div.tree-list");
        tree.find("ul li[is_heading=0] label").each(function() {
            var test_name = $(this).find("span.t-name").text().trim();
            if (regular_expression.test(test_name)) {
                $(this).parent("li").show();
            } else {
                $(this).parent("li").hide();
            }
        });
    });
    /*
    * button assign test
    */
    $(document).on('click', '.btn-show-test-modal', function(event) {
        event.preventDefault();
        // load waiting process
        myDialog.showProgress('show', { text : msg_loading });
        
        // clear text fields value
        $("#test_modal").find("input[type=text]").val('');
        // reset total test payment
        $("#test_modal").find(".total-test-payment").val(0);
        // reset default select
        $("#test_modal").find("select").val(-1);
        // 
        $("#test_modal").find(".tree-filter").trigger("keyup");
        // reset all checkboxs
        $("#test_modal").find('.tree-list input[type=checkbox].sample-test').iCheck('uncheck');
        var form = $(this).parents("form.form-sample-entry");
        var patient_sample = new patientSample(form);
        var data = patient_sample.get_patient_sample();
        $("#test_modal").data("patient_sample", patient_sample);
        // set test price
        $("#test_modal").find(":checkbox.sample-test").each(function() {
            var group_result = $(this).data("group-result");
            if (group_result) {
                var payment = _.chain(TEST_PAYMENTS).filter(function(d) { return d.group_result == group_result && d.payment_type_id == data.payment_type_id; }).first().value();
                if (payment) {
                    $(this).data("test-price", payment.price);
                }
            }
        });
        
        $.ajax({
            url: base_url + 'patient_sample/get_patient_sample_test',
            type: 'POST',
            dataType: 'json',
            data: {
                patient_sample_id : data.patient_sample_id, 
                patient_id : data.patient_id
            },
            success: function (resText){
                myDialog.showProgress('hide');
                if (resText.sample_tests && Array.isArray(resText.sample_tests) && resText.sample_tests.length > 0) {
                    for (var i in resText.sample_tests) {
                        if(resText.sample_tests[i].is_heading==0) {
                            $("#test_modal").find("input#st-" + resText.sample_tests[i].sample_test_id).iCheck('check');
                        }
                    }
                }
                // set to header patient
                if (resText.patient) {
                    $("#test_modal").find("span[id=sp-header_pid]").text(resText.patient.pid);
                    $("#test_modal").find("span[id=sp-header_name]").text(resText.patient.name);
                    $("#test_modal").find("span[id=sp-sample_number]").text(resText.patient_sample[0].sample_number);
                    $("#test_modal").find("span[id=sp-sample_source_name]").text(resText.patient_sample[0].sample_source_name);
                }
                // Set Sample Details
                if (resText.sample_details && Array.isArray(resText.sample_details) && resText.sample_details.length > 0) {
                    for (var i in resText.sample_details) {
                        var $department_sample  = $("#test_modal").find("#dsample-" + resText.sample_details[i].department_sample_id);
                        if ($department_sample != undefined) {
                            var $sample_desc    = $department_sample.find("select[name=sample_desc]");
                            var $first_weight   = $department_sample.find("input[name=first_weight]");
                            var $second_weight  = $department_sample.find("input[name=second_weight]");
                            if ($sample_desc   != undefined) $sample_desc.val(resText.sample_details[i].sample_description_id);
                            if ($first_weight  != undefined) $first_weight.val(resText.sample_details[i].sample_volume1);
                            if ($second_weight != undefined) $second_weight.val(resText.sample_details[i].sample_volume2);
                        }
                    }
                }
                // show test modal
                $("#test_modal").modal({backdrop : 'static'});
            }
        });
    });
    /*
    * Minimized the Sample panel
    */
    $(document).on('click', '.panel-heading', function(event) {
        event.preventDefault();
        $(this).siblings(".panel-body").slideToggle();
        $(this).find(".button-minimized").find("i.fa").toggleClass("fa-plus fa-minus");
    });
    /*
    * button delete sample
    */
    $(document).on('click', '.btn-remove', function(event) {
        event.preventDefault();
        if (confirm(q_delete_patient_sample)) {
            var form = $(this).parents("form.form-sample-entry");
            var patient_sample = new patientSample(form);
            var patient_sample_id = patient_sample.get_patient_sample().patient_sample_id;
            var panel = $(this).parents(".panel.sample-form");
            if (patient_sample_id > 0 && panel) {
                // show waiting progress
                myDialog.showProgress('show', { text : msg_loading });
                $.ajax({
                    url: base_url + 'patient_sample/delete',
                    type: 'POST',
                    dataType: 'json',
                    data: {patient_sample_id: patient_sample_id},
                    success: function(response){
                        // hide waiting progress
                        myDialog.showProgress('hide');
                        if (response.status === true) {
                            panel.slideUp('fast', function(){
                                var next_all = panel.nextAll("div.sample-form");
                                if (next_all.length > 0) {
                                    next_all.each(function() {
                                        var order = parseInt($(this).find("span.sample-order").text());
                                        $(this).find("span.sample-order").text(order - 1);
                                    });
                                }
                                panel.remove();
                            });
                            myDialog.showDialog('show', { text : response.msg, style : 'success' });
                        } else {
                            myDialog.showDialog('show', { text : response.text, style : 'warning' });
                        }
                    },
                    error: function(){
                        myDialog.showProgress('hide');
                        myDialog.showDialog('show', { text : msg_delete_fail, style : 'warning' });
                    }
                });
            } else {
                panel.slideUp('fast', function(){
                    var next_all = panel.nextAll("div.sample-form");
                    if (next_all > 0) {
                        next_all.each(function() {
                            var order = parseInt($(this).find("span.sample-order").text());
                            $(this).find("span.sample-order").text(order - 1);
                        });
                    }
                    panel.remove();
                });
            }
        }
    });
    /*
    * button save sample
    */
    $(document).on('click', '.btn-save-sample', function(event) {
        event.preventDefault();
        var form = $(this).parents("form.form-sample-entry");
        var patient_sample = new patientSample(form);
        var test_payments = _.map(patient_sample.form.data("test-payment"), function(d) {
            return _.omit(d, 'id');
        });
        // variable patient come from add_request.php
        patient_sample.save({ patient : patient, test_payments: test_payments });
    });
    /*
    * patient date of birth
    */
    $("#patient_entry_form").find('#patient_dob').datetimepicker(dtPickerOption).on('dp.change', function(event) {
        event.preventDefault();
        var dob = $(this).data("DateTimePicker").date();
        if (dob) {
            var age = calculateAge(dob);
            $("#patient_entry_form").find("#patient-age-year").val(age.years);
            $("#patient_entry_form").find("#patient-age-month").val(age.months);
            var days = age.days === 0 && age.months === 0 && age.years === 0 ? 1 : age.days;
            $("#patient_entry_form").find("#patient-age-day").val(days);
        }
    });
    /*
    * patient's age in year, month and day
    */
    $("#patient_entry_form").on("keyup change", "#patient-age-year, #patient-age-month, #patient-age-day", function(event) {
        event.preventDefault();
        var years  = $("#patient_entry_form").find("#patient-age-year").val() || 0;
        var months = $("#patient_entry_form").find("#patient-age-month").val() || 0;
        var days   = $("#patient_entry_form").find("#patient-age-day").val() || 0;
        var dob = moment();
        dob.subtract(days, 'days');
        dob.subtract(months, 'months');
        dob.subtract(years, 'years');
        $("#patient_entry_form").find("#patient_dob").data("DateTimePicker").setDate(dob.toDate());
    });
    /*
    * Get Requester Base on sample source
    */
    $("#patient_sample_form").on('change', 'select[name=sample_source]', function(event, requester_id) {
        event.preventDefault();
        // select form sample entry
        var form = $(this).parents("form.form-sample-entry");
        // find requester
        var requester = form.find("select[name=requester]");
        // get value from sample source
        var sample_source_id = $(this).val();
        // clear value of the requester and change to default select
        form.find("select[name=requester] option[value!=-1]").remove();
        // trigger change event of the requester
        form.find("select[name=requester]").val(-1).trigger("change");
        // exit the event function
        if (sample_source_id <= 0) return false;
        $.ajax({
            url: base_url + 'requester/get_lab_requester',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: {sample_source_id: sample_source_id},
            success: function(resText){
                if (resText.requesters.length > 0) {
                    for (var i in resText.requesters) {
                        var selected = '';
                        if (requester_id == resText.requesters[i].requester_id) selected = 'selected';
                        var opt = "<option value='" + resText.requesters[i].requester_id + "' "+ selected +">" + resText.requesters[i].requester_name + "</option>";
                        requester.append(opt);
                    }
                }
            }
        });
        
    });
    /*
    * patient's address
    */
    $("select#province, select#district, select#commune").on('change', function(event, data) {
        event.preventDefault();
        var get = $(this).attr("data-get");
        var val = data != undefined ? data.cur : $(this).val();
        $.ajax({
            url: base_url + 'gazetteer/get_' + get,
            type: 'POST',
            dataType: 'json',
            data: {code: val},
            success: function(resText){
                $target = $("#" + get);
                $target.find("option").not(":eq(0)").remove();
                for (var i in resText) {
                    var selected = "";
                    if (data != undefined && resText[i].code == data.next) {
                        selected = "selected";
                    }
                    var name = 'name_' + app_lang;
                    $opt = $("<option value='" + resText[i].code + "' " + selected + ">" + resText[i][name] + "</option>");
                    $target.append($opt);
                }
            }
        });
    });
    /*
    * search existing patient
    * use existed patient
    */ 
    $("#modal-existed-patient").on('click', 'button.use-existed-patient', function(event) {
        event.preventDefault();
        // get patient information from modal existed patient
        var exist_patient = $("#modal-existed-patient").data("patient");
        // set patient code into search patient id 
        $("#search_patient_id").val(exist_patient.patient_code);
        // click button search patient
        $("#search_patient").submit();
        // hide the modal existed patient
        $("#modal-existed-patient").modal("hide");
    });
    /*
    * save only outside patient information
    */
    $(document).on('click', '#button_save_patient', function(event) {
        event.preventDefault();
        var outside_patient = {};
        outside_patient.patient_manual_code = $("#patient_entry_form").find("#patient_manual_code").val().trim() || undefined;
        outside_patient.patient_name = $("#patient_entry_form").find("#patient_name").val().trim();
        outside_patient.sex = $("#patient_entry_form").find("input[name=patient_sex]:checked").val();
        outside_patient.dob = $("#patient_entry_form").find("#patient_dob").data("DateTimePicker").date();
        outside_patient.phone = $("#patient_entry_form").find("#patient_phone").val().trim() || undefined;
        outside_patient.province = $("#patient_entry_form").find("#province").val();
        outside_patient.commune = $("#patient_entry_form").find("#commune").val();
        outside_patient.district = $("#patient_entry_form").find("#district").val();
        outside_patient.village = $("#patient_entry_form").find("#village").val();
        // validation
        var is_valid = true;
        if (outside_patient.patient_name.length == 0) {
            is_valid = false;
        }
        if (outside_patient.sex <= 0 || outside_patient.sex > 2 || !outside_patient.sex) {
            is_valid = false;
        }
        if (outside_patient.dob == null) {
            is_valid = false;
        }
        if (outside_patient.dob && outside_patient.dob.isAfter(moment())) {
            $("#patient_entry_form").find("label[for=patient_dob]").attr("data-hint", msg_dob_not_after_now);
            return false;
        } else {
            $("#patient_entry_form").find("label[for=patient_dob]").removeAttr("data-hint");
        }
        if (!is_valid) {
            myDialog.showDialog('show', {text: msg_required_data, style: 'warning'});
        }
        // format date
        outside_patient.dob = outside_patient.dob.format('YYYY-MM-DD');
        var url  = "patient/save_outside_patient";
        if (patient && patient.pid > 0) {
            url = "patient/update_outside_patient/" + patient.pid;
        }
        // pass data to server
        $.ajax({
            url: base_url + url,
            type: 'POST',
            dataType: 'json',
            data: {patient: outside_patient},
            success: function(resText){
                // not exist patient
                if (resText.status == true && resText.patient && !resText.id_exist) {
                    // set data patient id to button save patient for add sample and assign test
                    $("#button_save_patient").data("pid", resText.patient.pid);
                    // hide patient entry form
                    $("#patient_entry_form").hide();
                    // show patient Information
                    $("#display_patient_information").find(".patient-code").text(resText.patient.patient_code);
                    $("#display_patient_information").find(".patient-name").text(resText.patient.name);
                    // patient's sex
                    var sex = '';
                    if (app_lang == 'kh') sex = resText.patient.sex == 'M' ? 'ប្រុស' : 'ស្រី';
                    else if (app_lang == 'en') sex = resText.patient.sex == 'M' ? 'Male' : 'Female';
                    $("#display_patient_information").find(".patient-gender").text(sex);
                    // patient's date of birth and age
                    var dob = moment(resText.patient.dob, 'YYYY-MM-DD');
                    var age = calculateAge(dob);
                    var days = age.days === 0 && age.months === 0 && age.years === 0 ? 1 : age.days;
                    $("#display_patient_information").find(".patient-age-year").text(age.years);
                    $("#display_patient_information").find(".patient-age-month").text(age.months);
                    $("#display_patient_information").find(".patient-age-day").text(days);
                    // patient's phone
                    $("#display_patient_information").find(".patient-phone").text(resText.patient.phone);
                    // patient's address
                    $("#display_patient_information").find(".patient-address-village").text(resText.patient['village_' + app_lang]);
                    $("#display_patient_information").find(".patient-address-commune").text(resText.patient['commune_' + app_lang]);
                    $("#display_patient_information").find(".patient-address-district").text(resText.patient['district_' + app_lang]);
                    $("#display_patient_information").find(".patient-address-province").text(resText.patient['province_' + app_lang]);
                    // patient hidden field patient id, sex and age
                    $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_id").attr("data-value", resText.patient.pid);
                    $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_age").attr("data-value", moment().diff(moment(resText.patient.dob, 'YYYY-MM-DD'), 'days'));
                    $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_sex").attr("data-value", resText.patient.sex);
                    // show patient information view
                    $("#display_patient_information").fadeIn(700);
                    // 2. show edit patient
                    if (!patient) {
                        // remove sample form entry
                        $("#patient_sample_entry_form_wrapper").find("div.sample-form").remove();
                        // add one patient sample entry form
                        $("#button_add_more_sample").trigger('click');
                        // show patient sample entry form
                        $("#patient_sample_entry_form_wrapper").fadeIn(700);
                    }
                    // add patient info to global patient variable in add_request.php
                    patient = $.extend({}, resText.patient);
                }
                // exist patient
                if (resText.id_exist) {
                    // add class duplicate
                    $("#patient_entry_form").find("#patient_manual_code").addClass("duplicate");
                    // remove patient data property from modal existed patient
                    $("#modal-existed-patient").removeData("patient");
                    // empty the prevous data
                    $("#modal-existed-patient").find("#tbl-existed-patient").find("tbody").empty();
                    // patient's date of birth
                    var dob = moment(resText.patient.dob, 'YYYY-MM-DD');
                    // view patient information in table form in modal existed patient
                    var tr = "<tr>";
                    tr += "<td>"+ resText.patient.patient_code +"</td>";
                    tr += "<td>"+ resText.patient.name +"</td>";
                    tr += "<td>"+ resText.patient.sex +"</td>";
                    tr += "<td>"+ (dob ? dob.format('DD/MM/YYYY') : '') +"</td>";
                    tr += "<td>"+ (resText.patient.phone || '') +"</td>";
                    tr += "<td>";
                    tr += (resText.patient['village_' + app_lang] || '?') + ' - ';
                    tr += (resText.patient['commune_' + app_lang] || '?') + ' - ';
                    tr += (resText.patient['district_' + app_lang] || '?') + ' - ';
                    tr += (resText.patient['province_' + app_lang] || '?');
                    tr += "</td>";
                    tr += "<tr>";
                    $("#modal-existed-patient").find("#tbl-existed-patient").find("tbody").html(tr);
                    // set value for patient data property in modal existed patient
                    $("#modal-existed-patient").data("patient", resText.patient);
                    // show modal existed patient
                    $("#modal-existed-patient").modal({backdrop : 'static'});
                }
                myDialog.showDialog('show', { text  : resText.msg, style : resText.status == true ? 'success' : 'warning' });
            },
            error: function(){
                myDialog.showDialog('show', { text  : msg_save_fail, style  : 'warning' });
            }
        });
    });
    /*
    * Add new patient & sample by manual
    */
    $(document).on('click', '#button_new_patient', function(event) {
        event.preventDefault();
        // hide patient not found
        $("#search_patient_not_found").hide();
        // hide display patient information
        $("#display_patient_information").hide();
        // hide patient entry form
        $("#patient_sample_entry_form_wrapper").hide();
        // remove patient sample entry form
        $("#patient_sample_entry_form_wrapper").find('div.sample-form').remove();
        // show patient entry form
        $("#patient_entry_form_wrapper").fadeIn(600);
        // add one patient sample entry form
        //$("#button_add_more_sample").trigger('click');
        // show patient sample entry form
        //$("#patient_sample_entry_form_wrapper").fadeIn(700);
    });
    /*
    * cancel the patient entry
    */
    $(document).on('click', '#button_cancel_patient', function(event) {
        event.preventDefault();
        // clear patient's date of birth
        $("#patient_entry_form").find("#patient_dob").data("DateTimePicker").clear();
        // clear value
        $("#patient_entry_form").find("input:not(:radio)").val("");
        // change to the default selected province
        $("#patient_entry_form").find("#province").val(-1).trigger('change');
        // clear all districts
        $("#patient_entry_form").find("#district").find("option[value!=-1]").remove();
        // clear all communes
        $("#patient_entry_form").find("#commune").find("option[value!=-1]").remove();
        // clear all villages
        $("#patient_entry_form").find("#village").find("option[value!=-1]").remove();
        // hide the patient entry form
        $("#patient_entry_form_wrapper").fadeOut(300);
    });
    /*
    * Add new patient sample form
    */
    $(document).on('click', '#button_add_more_sample', function(event, init_value, patient_sample_form, previous_sample_data) {
        event.preventDefault();
        var admission_dates = $.map((patient) ? moment(patient.admission_date, 'YYYY-MM-DD HH:mm:ss') : [], function(item, index) { return moment(item, 'YYYY-MM-DD HH:mm:ss'); });
        init_value = $.extend(true, init_value, {admission_date: moment.max(admission_dates).toDate()});
        // call function patient sample form
        createSampleForm(init_value, patient_sample_form, previous_sample_data);
    });
    /*
    * @Search patient by id
    */
    $("#search_patient").on('submit', function(event) {
        event.preventDefault();
        // Initial variable
        var patient_id = $(this).find("#search_patient_id").val();
        var english_digits = {'០':'0','១':'1','២':'2','៣':'3','៤':'4','៥':'5','៦':'6','៧':'7','៨':'8','៩':'9','ឥ':'-'};
        // exit function
        if (!patient_id) return false;
        // Convert khmer unicode to english
        patient_id = patient_id.replace(/[០១២៣៤៥៦៧៨៩ឥ]/g, function(s){
            return english_digits[s];
        });
        // change value to english
        $(this).find("#search_patient_id").val(patient_id);
        // hide search patient not found
        $("#search_patient_not_found").hide();
        // hide display patient information
        $("#display_patient_information").hide();
        // hide patient entry form
        $("#patient_entry_form_wrapper").hide();
        // show progressing dialog
        myDialog.showProgress('show', {text: '', appendTo: $("section.content-body")});
        // remove value of patient id field
        $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_id").removeData("value");
        // remove data value of patient id field
        $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_id").removeAttr("data-value");
        // search patient from database
        $.ajax({
            url: base_url + 'patient/search/' + patient_id,
            type: 'POST',
            dataType: 'json',
            data: {pid: patient_id},
            success: function(response){
                myDialog.showProgress('hide', {
                    onHidden: function(){
                        var patient_found = response.patient;
                        // patient found
                        if (patient_found) {
                            patient = $.extend({}, patient_found);
                            // display patient information
                            $("#display_patient_information").find('.patient-code').text(patient_found.patient_code);
                            $("#display_patient_information").find('.patient-name').text(patient_found.name);
                            var sex = '';
                            if (app_lang == 'kh') sex = patient.sex == 'M' ? 'ប្រុស' : 'ស្រី';
                            else if (app_lang == 'en') sex = patient.sex == 'M' ? 'Male' : 'Female';
                            $("#display_patient_information").find('.patient-gender').text(sex);
                            var date_of_birth = moment(patient_found.dob, 'YYYY-MM-DD');
                            var age = calculateAge(date_of_birth);
                            var days = age.days === 0 && age.months === 0 && age.years === 0 ? 1 : age.days;
                            $("#display_patient_information").find('.patient-age-year').text(age.years);
                            $("#display_patient_information").find('.patient-age-month').text(age.months);
                            $("#display_patient_information").find('.patient-age-day').text(days);
                            $("#display_patient_information").find('.patient-phone').text(patient_found.phone);
                            $("#display_patient_information").find('.patient-address-village').text(patient_found['village_' + app_lang]);
                            $("#display_patient_information").find('.patient-address-commune').text(patient_found['commune_' + app_lang]);
                            $("#display_patient_information").find('.patient-address-district').text(patient_found['district_' + app_lang]);
                            $("#display_patient_information").find('.patient-address-province').text(patient_found['province_' + app_lang]);
                            // set require value for saving sample
                            $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_id").attr('data-value', patient_found.pid);
                            $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_age").attr('data-value', moment().diff(date_of_birth, 'days'));
                            $("#patient_entry_form_wrapper").find("input[type=hidden]#patient_sex").attr('data-value', patient_found.sex);
                            // show patient information
                            $("#display_patient_information").fadeIn(700);
                            // show sample entry form wrapper
                            $("#patient_sample_entry_form_wrapper").fadeIn(700);
                            // search for existing patient sample 
                            $.ajax({
                                url: base_url + "patient_sample/get_patient_sample",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    patient_id: patient_found.pid,
                                    laboratory_id: LABORATORY_SESSION.labID
                                },
                                success: function(response){
                                    // samples were found
                                    if (response.patient_samples && response.patient_samples.length > 0) {
                                        for (var i in response.patient_samples) {
                                            // click button add more sample
                                            $("#button_add_more_sample").trigger('click', [null, null, response.patient_samples[i]]);
                                        }
                                    }
                                    // add new patient sample
                                    $("#button_add_more_sample").trigger('click');
                                },
                                error: function(){
                                    // click button add more sample
                                    $("#button_add_more_sample").trigger('click');
                                }
                            });
                            
                        } else {
                            $(event.target).focus();
                            // show search patient not found
                            $("#search_patient_not_found").fadeIn(500);
                        }
                    }
                });
            },
            error: function(){
                myDialog.showProgress('hide', {
                    onHidden: function(){
                        $(event.target).focus();
                        // show search patient not found
                        $("#search_patient_not_found").fadeIn(500);
                    }
                });
            }
        });
    });

});