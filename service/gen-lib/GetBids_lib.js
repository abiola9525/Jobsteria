function invokeGetBids() {
	var vpid = document.getElementById("pid").value;
	$.post("GetBids.php",{pid : vpid}).done(function(data) {}).fail(function () {});
}
