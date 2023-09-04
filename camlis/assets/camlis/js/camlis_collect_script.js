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

    var $sample_forms = $(".sample-form-wrapper");

    $sample_forms.find('input[name=collected_date]').datetimepicker(dtPickerOption);
    $sample_forms.find('input[name=collected_time]').timepicker({minuteStep: 1, showMeridian: false, defaultTime: '00:00'});
    //Scroll to bottom
    $('#template-wrapper').animate({
        scrollTop: $("#template-content").height()
    }, 1000);

    $(document).on('click', '#collect', function(event) {
        event.preventDefault();
        
        var patient_sample = {
            patient_sample_id   : $sample_forms.find("#patient_sample_id").val(),
            collected_date      : moment($sample_forms.find("input[name=collected_date]").data("DateTimePicker").date()).format('YYYY-MM-DD'),
            collected_time      : moment($sample_forms.find("input[name=collected_time]").val().trim(), 'HH:mm').format('HH:mm'),
        }

        $.ajax({
            url: base_url + "collect/save",
            type: 'POST',
            dataType: 'json',
            data: {patient_sample: patient_sample},
            success: function(response){
                if (response.status) {
                    window.location.replace(base_url + "request");
                }
            },
            error: function(){}
        });
    });
});