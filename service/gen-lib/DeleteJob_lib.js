function invokeDeleteJob() {
	var vjid = document.getElementById("jid").value;
	$.post("DeleteJob.php",{pid : vjid}).done(function(data) {}).fail(function () {});
}
