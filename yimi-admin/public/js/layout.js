$(document).ready(function() {
	$('.sidebar-list').hover(function() {
		$(this).addClass('on');
	}, function() {
		$(this).removeClass('on');
	})

	// datepicker
	if ($.isFunction($.fn.datepicker)) {
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			onSelect: function(dateText, inst) {
				$(this).trigger('input');
			}
		});
	}

	// bootstrapSwitch
	if ($.isFunction($.fn.bootstrapSwitch)) {
		$("[name='my-checkbox']").bootstrapSwitch();
	}

	// Confirm deleting resources
	$("form[data-confirm]").submit(function() {
		if (!confirm($(this).attr("data-confirm"))) {
			return false;
		}
	})

	setTimeout(function() {
		$('.alert').remove();
	}, 3000);
})