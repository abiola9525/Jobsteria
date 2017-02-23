function invokeGetBid() {
	var vjid = document.getElementById("jid").value;
	$.post("GetBid.php",{jid : vjid}).done(function(data) {}).fail(function () {});
}
