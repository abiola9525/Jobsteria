
function getCurrency(ccode) {
	if(ccode == "US") {
		return "$";
	}
	else if(ccode == "EURO") {
		return "&#163;";
	}
	else if(ccode == "IR") {
		return "&#8377;";
	}
	
}

function log(msg) {
	if ( window.console && window.console.log ) {
		console.log(msg);
	}
}


function getStatus(status) {
	if(status == "BID") {
		return "Open";
	}
	else if(status == "PRE_HIRE") {
		return "Hiring";
	}
	else if(status == "IN_PROG") {
		return "In Progress";
	}
	else if(status == "BID_CLS") {
		return "Bidding Time is Over.  Employer must Hire";
	}
	else if(status == "FIN_CLS") {
		return "Finished and Closed";
	}
	else {
		return "Closed";
	}
}

function getTransactionType(type) {
	if(type == "SERVICE_CHARGE") {
		return "Service Charge";
	}
	else if(type == "FINAL") {
		return "Final Employee Payment";
	}
	else if(type == "MILE_STONE") {
		return "Milestone Request";
	}
	
}