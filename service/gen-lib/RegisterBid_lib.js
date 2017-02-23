function invokeRegisterBid() {
	var vmilestone = document.getElementById("milestone").value;
	var vmessage = document.getElementById("message").value;
	var vamount = document.getElementById("amount").value;
	var vuid = document.getElementById("uid").value;
	var vstartDate = document.getElementById("startDate").value;
	var vcharge = document.getElementById("charge").value;
	var vendDate = document.getElementById("endDate").value;
	var vpid = document.getElementById("pid").value;
	$.post("RegisterBid.php",{milestone : vmilestone,message : vmessage,amount : vamount,uid : vuid,startDate : vstartDate,charge : vcharge,endDate : vendDate,pid : vpid}).done(function(data) {}).fail(function () {});
}
