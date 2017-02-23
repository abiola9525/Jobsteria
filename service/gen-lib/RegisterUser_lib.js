function invokeRegisterUser() {
	var vuid = document.getElementById("uid").value;
	var vphone = document.getElementById("phone").value;
	var vbirth_date = document.getElementById("birth_date").value;
	var vzipcode = document.getElementById("zipcode").value;
	var vupdate_ts = document.getElementById("update_ts").value;
	var vresume_file_loc = document.getElementById("resume_file_loc").value;
	var vpassword = document.getElementById("password").value;
	var vcountry = document.getElementById("country").value;
	var vcity = document.getElementById("city").value;
	var vavitar = document.getElementById("avitar").value;
	var vstate_prov = document.getElementById("state_prov").value;
	var vusername = document.getElementById("username").value;
	var vaim = document.getElementById("aim").value;
	var vaddress = document.getElementById("address").value;
	var vemail = document.getElementById("email").value;
	var vname = document.getElementById("name").value;
	var vwork_phone = document.getElementById("work_phone").value;
	var vadd_ts = document.getElementById("add_ts").value;
	var vskype = document.getElementById("skype").value;
	$.post("RegisterUser.php",{uid : vuid,phone : vphone,birth_date : vbirth_date,zipcode : vzipcode,update_ts : vupdate_ts,resume_file_loc : vresume_file_loc,password : vpassword,country : vcountry,city : vcity,avitar : vavitar,state_prov : vstate_prov,username : vusername,aim : vaim,address : vaddress,email : vemail,name : vname,work_phone : vwork_phone,add_ts : vadd_ts,skype : vskype}).done(function(data) {}).fail(function () {});
}
