function invokeSendPM() {
	var vmessage = document.getElementById("message").value;
	var vto_uid = document.getElementById("to_uid").value;
	var vsubject = document.getElementById("subject").value;
	$.post("SendPM.php",{message : vmessage,to_uid : vto_uid,subject : vsubject}).done(function(data) {}).fail(function () {});
}
