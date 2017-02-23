function invokeGetRegions() {
	var vcountry_id = document.getElementById("country_id").value;
	$.post("GetRegions.php",{country_id : vcountry_id}).done(function(data) {}).fail(function () {});
}
