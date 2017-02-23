function invokeSimpleJobList() {
	var vbase = document.getElementById("base").value;
	var vrc = document.getElementById("rc").value;
	$.post("SimpleJobList.php",{base : vbase,rc : vrc}).done(function(data) {}).fail(function () {});
}
