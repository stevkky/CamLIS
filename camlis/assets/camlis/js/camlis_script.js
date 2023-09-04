var months = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];

/* DataTable Option */
var dataTableOption = {
	"language": {
		"lengthMenu"	: "បង្ហាញ&nbsp _MENU_ &nbsp;ជួរក្នុងមួយទំព័រ",
		"zeroRecords"	: "គ្មានទិន្នន័យ",
		"info"			: "បង្ហាញទំព័រទី _PAGE_ ក្នុងចំណោម _PAGES_ ទំព័រ",
		"search"		: "ស្វែងរក",
		"infoEmpty"		: "គ្មានទិន្នន័យ",
		"infoFiltered"	: "(ស្វែងរកក្នុងចំណោម _MAX_)",
		"paginate"		: {
			"previous"	: "ថយក្រោយ",
			"next"		: "ទៅមុខ"
		}
	}
};

//DateTimePicker option
var dateTimePickerOption = {
    widgetPositioning : {
        horizontal	: 'left',
        vertical	: 'bottom'
    },
    showClear		: true,
    format			: 'DD/MM/YYYY',
    useCurrent		: false,
    maxDate			: new Date(),
    locale			: APP_LANG === 'kh' ? 'km' : 'en'
};

$(function () {
    $.ajaxSetup({ cache: false });

	if (app_lang == undefined || app_lang == 'en') {
		dataTableOption.language = null;
		app_lang = 'en';
	}
	
	init_scrollEvent('#template-wrapper');

	$(window).on("click", function (evt) {
		var $target = $(evt.target);
		var currentTarget = false;
		if ($target.hasClass("dropdown-list")) currentTarget = $target;
		else if ($target.closest("li").hasClass("dropdown-list")) currentTarget = $target.closest("li");

		if ($target.parents("nav#main-menu").length > 0 && currentTarget && ($target.hasClass("dropdown-list") || $target.closest("li").hasClass("dropdown-list"))) {
			currentTarget.toggleClass('show-dropdown');
			currentTarget.find("a i.nav-indicator").toggleClass("fa-chevron-down fa-chevron-up");

			var siblings = currentTarget.siblings("li.dropdown-list.show-dropdown");
			if (siblings.length > 0) {
				siblings.find("a i.nav-indicator").removeClass("fa-chevron-up").addClass("fa-chevron-down");
				siblings.removeClass("show-dropdown");
			}
		} else {
			$("nav#main-menu > ul > li.dropdown-list.show-dropdown > a i.nav-indicator").removeClass("fa-chevron-up").addClass("fa-chevron-down");
			$("nav#main-menu > ul > li.dropdown-list.show-dropdown").removeClass("show-dropdown");
		}
	});
	
	$(".modal").on("hidden.bs.modal", function (evt) {
		$(".modal").css("overflow", "auto");
    });

	//Default DataTable Option
	if ($.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
            "pageLength": 20
        });
    }
});

//Custom Function
/**
 * Fixed Top Menu on scroll
 */
function init_scrollEvent(target) {
	$(target).off("scroll").on("scroll", function (evt) {
		var sTop = $(this).scrollTop();

		var top_menu = $("nav#main-menu");
		if (sTop >= 150 && !top_menu.hasClass("fixed-top")) {
			top_menu.addClass("fixed-top");
			top_menu.animate({
				"marginTop": "0"
			});
		} else if (top_menu.hasClass("fixed-top") && sTop < 100) {
			top_menu.removeAttr("style");
			top_menu.removeClass("fixed-top");
		}
	});
}

/**
 * Check if Input is number
 * @param {Object} evt Event Object
 */
function isNumber(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;

	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	return true;
}

//Allow Double in input
function allowDouble(evt, self) {
	evt 			= (evt) ? evt : window.event;
	var charCode	= (evt.which) ? evt.which : evt.keyCode;
	var val			= $(self).val();
	
	if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 46 || val.length == 0 || val.indexOf('.') > -1)) {
		return false;
	}
	return true;
}

/**
 * Calculate Age from dob
 * @param dob
 * @param current_date
 */
function calculateAge(dob, current_date) {
    dob          = moment(dob);
    current_date = current_date ? moment(current_date) : moment();

    return moment.preciseDiff(dob, current_date, true)
}

function getAgeYear(dateString) {
	// current
    var c_y = new Date().getFullYear();
    var c_m = new Date().getMonth();
    var c_d = new Date().getDate();

    var bdays = dateString.getDate();
    var bmonths = dateString.getMonth();
    var byear = dateString.getFullYear();

    //alert(bdays);
    var sdays = c_d;
    var smonths = c_m;
    var syear = c_y;

    if(sdays < bdays)
    {
        sdays = parseInt(sdays) + 30;
        smonths = parseInt(smonths) - 1;

        var fdays = (sdays - bdays)+1;
    }
    else{
        var fdays = (sdays - bdays)+1;
    }

    if(smonths < bmonths)
    {
        smonths = parseInt(smonths) + 12;
        syear = syear - 1;
        var fmonths = smonths - bmonths;
    }
    else
    {
        var fmonths = smonths - bmonths;
    }

    var fyear = syear - byear;

    /*$('#patient_age').val(fyear);
    $('#patient_agem').val(fmonths);
    $('#patient_aged').val(fdays);*/
    return {"days":fdays,"months":fmonths,"years":fyear};
}

function CustomDialog() {
	/**
	 * Show Message Dialog
	 * @param {String} state   Show or Hide Model {'show', 'hidden'}
	 * @param {Array} options Array of Object 
	 */
	this.showDialog = function (state, options) {
		var css = {
			background	: 'inherit',
			border		: 'inherit',
			borderRadius: '50%',
			height		: '35px',
			left		: '-15px',
			lineHeight	: '35px',
			position	: 'absolute',
			textAlign	: 'center',
			top			: '-15px',
			width		: '35px'
		};
		var icon = {
			success	: 'fa fa-check',
			warning	: 'fa fa-exclamation-triangle'
		}

		$dialog = $("<div class='modal fade'> \
						<div class='modal-dialog'> \
							<div class='alert alert-dismissible' role='alert'> \
								<i id='status-icon' class='fa fa-check'></i> \
								<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button> \
								<span id='dlg_status'></span> <span id='dlg_msg'></span> \
							</div> \
						</div> \
					</div>");

		if (options.style == 'success')
			$dialog.find('.alert').css('background', '#008d4c');
		else if (options.style == 'warning')
			$dialog.find('.alert').css('background', '#e08e0b');
		else
			$dialog.find('.alert').css('background', '#d73925');
		
		$dialog.css({
			color		: 'white',
			fontWeight	: '600',
			textShadow	: '0 0 5px -2px'
		});
		
		$dialog.find('#dlg_status').css('font-weight', '700');

		$dialog.find('#dlg_msg').html(options.text);
		$dialog.find('#dlg_status').html(options.status);
		$dialog.find("#status-icon").css(css);

		if (icon[options.style] != undefined)
			$dialog.find("#status-icon").attr('class', icon[options.style]);
		
		if (options.icon != undefined)
			$dialog.find("#status-icon").attr('class', options.icon);

		$dialog.modal(state);
		
		$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (evt) {
			$("body, .modal").css("overflow", "auto");
			if (options.onHidden != undefined) {
				var func = options.onHidden;
				func();
			}
		});

		//Auto Hide Dialog
		if (options.autClose == undefined || options.autoClose == true) {
			setTimeout(function () {
				$dialog.modal('hide');
			}, 2000);
		}
	}

	/**
	 * Show Progress Dialog
	 * @param {String} state   Show or Hide Model {'show', 'hide'}
	 * @param {Array} options Array of Object 
	 */
	this.showProgress = function (state, options) {
		//Size range [1x, 2x, 3x]
		var fontSize = { '1x':'12pt', '2x':'18px', '3x':'22px' };
		var more_css = {};
		var position = 'absolute';
		
		var options = $.extend({
			text		: 'Loading...',
			appendTo	: 'viewport',
			size		: '2x'
		}, options);
		
		if (options.appendTo === 'viewport') {
			options.appendTo =  $("body");
			position = 'fixed';
		}

		if (options.display == 'inline') {
			more_css = { marginTop : '40px' };
			position = 'relative';
		}
		
		$dialog = $("<div class='text-center' id='progress_box'> \
						<div style='position:relative; top:40%'> \
							<div class='la-ball-clip-rotate la-dark la-" + options.size + "' style='margin:0 auto;'> \
								<div></div> \
							</div> <br> \
							<h4 id='progress_text'></h4> \
						</div> \
					</div>");
		
		$dialog.css($.extend(more_css, {
			background	: 'rgba(255, 255, 255, 0.7)',
			width		: '100%',
			height		: '100%',
			position	: position,
			zIndex		: '2000',
			top			: '0',
			left		: '0'
		}));

		if (state == 'show') {
			$('#progress_box').remove();
			options.appendTo.css("overflow", "hidden");
			$dialog.find('#progress_text').html("<b>" + options.text + "</b>").css("font-size", fontSize[options.size]);
			options.appendTo.append($dialog);
		}
		else if (state == 'hide') {
			$('#progress_box').fadeOut(200, function () {
				$('#progress_box').remove();
				if (options != undefined && options.onHidden != undefined) {
					var func = options.onHidden;
					func();
				}
			});
			
			$("body").css('overflow', 'auto');
		}
	}
}
// added 23 Jan 2021
function isInteger(x) { return typeof x === "number" && isFinite(x) && Math.floor(x) === x; }
function isFloat(x) { return !!(x % 1); }