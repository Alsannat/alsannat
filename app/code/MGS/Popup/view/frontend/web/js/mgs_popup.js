var montharray = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

function countdown(yr, m, d, hr, min) {
	require(
		[ 'jquery' ],
	function($){
		theyear = yr;
		themonth = m;
		theday = d;
		thehour = hr;
		theminute = min;
		var today = new Date();
		var todayy = today.getYear();
		if (todayy < 1000)
			todayy += 1900;
		var todaym = today.getMonth();
		var todayd = today.getDate();
		var todayh = today.getHours();
		var todaymin = today.getMinutes();
		var todaysec = today.getSeconds();
		var todaystring = montharray[todaym] + " " + todayd + ", " + todayy + " " + todayh + ":" + todaymin + ":" + todaysec;
		var futurestring = montharray[m - 1] + " " + d + ", " + yr + " " + hr + ":" + min + ":" + "00";
		var dd = Date.parse(futurestring) - Date.parse(todaystring);
		var dday = Math.floor(dd / (60 * 60 * 1000 * 24) * 1);
		var dhour = Math.floor((dd % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
		var dmin = Math.floor(((dd % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
		var dsec = Math.floor((((dd % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
		if (dday <= 0 && dhour <= 0 && dmin <= 0 && dsec <= 0) {
			$("#timer-text").hide();
			$("#timer-table").hide();
		}
		if (dday == 0 && dhour == 0 && dmin == 0 && dsec == 0) {
			
		} else {
			$("#mgs_popup_count").hide();
			$("#dday").html(dday);
			$("#dhour").html(dhour);
			$("#dmin").html(dmin);
			$("#dsec").html(dsec);
			
			setTimeout("countdown(theyear,themonth,theday,thehour,theminute)", 1000);
		}
	});
}

function getCookieMgsPopup(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


function dontShowPopup(el){
	require(
		[ 'jquery' ],
	function($){
		if($('#'+el).prop('checked')) {
            var d = new Date();
            var cname = "mgspopup";
            var cvalue = "nevershow";
            d.setTime(d.getTime() + (1*24*60*60*1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		} else {
			document.cookie = cname + "= ''; -1";
		}
	});
}
