function processDateFields(){
	var dateFields = new Array();
	var dateName;
	var dateCheckBox;
	var dateSelects;
	var dateSelects2;
	
	$(':checkbox').filter(function(){
		return /^use_/.test($(this).attr('id'));
	}).each(function(){
		dateFields[dateFields.length] = this.id.substr('use_'.length);
	});
	
	for (var i = 0; i < dateFields.length; ++i) {
		dateName     = dateFields[i];
		dateCheckBox = $('#use_'+dateName);
		dateSelects  = $('select[name='+dateName+'_year],select[name='+dateName+'_month],select[name='+dateName+'_day]');
		dateSelects2 = $('select[name='+dateName+'_hour],select[name='+dateName+'_minute],select[name='+dateName+'_second]');

		processDateInit(dateCheckBox, dateSelects, dateSelects2);
		processDateBoxes(dateCheckBox, dateSelects, dateSelects2);
	}	
}

function processDateInit(dateCheckBox, dateSelects, dateSelects2){
	if ($(dateCheckBox).attr('checked')) {
		dateEnable(dateSelects);
		dateEnable(dateSelects2);
	}
	else {
		dateDisable(dateSelects);
		dateDisable(dateSelects2);
	}
}

function processDateBoxes(dateCheckBox, dateSelects, dateSelects2){
	$(dateCheckBox).click(function(){
		processDateInit(dateCheckBox, dateSelects, dateSelects2);
	});
}

function dateEnable(sel){
	sel.attr('disabled', false);
}

function dateDisable(sel){
	sel.attr('disabled', 'disabled');
}
