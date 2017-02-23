function invokeRegisterJob() {
	var vbudget_upper_bound = document.getElementById("budget_upper_bound").value;
	var vjob_projected_end_date = document.getElementById("job_projected_end_date").value;
	var vjob_start_date = document.getElementById("job_start_date").value;
	var vdescription = document.getElementById("description").value;
	var vbudget_lower_bound = document.getElementById("budget_lower_bound").value;
	var vname = document.getElementById("name").value;
	var vcurrency_type = document.getElementById("currency_type").value;
	$.post("RegisterJob.php",{budget_upper_bound : vbudget_upper_bound,job_projected_end_date : vjob_projected_end_date,job_start_date : vjob_start_date,description : vdescription,budget_lower_bound : vbudget_lower_bound,name : vname,currency_type : vcurrency_type}).done(function(data) {}).fail(function () {});
}
