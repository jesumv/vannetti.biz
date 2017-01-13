/**
 * @author jmv
 */

function resulmal(resp){
	//inserta mensaje de error
	document.getElementById('avisor').innerHTML=resp;
}

function isValidDate(s) {
    // format (YY)YY/M(M)/D(D)
    var dateFormat = /^\d{1,4}[\.|\/|-]\d{1,2}[\.|\/|-]\d{1,4}$/;

    if (dateFormat.test(s)) {
        // remove any leading zeros from date values
        s = s.replace(/0*(\d*)/gi,"$1");
        var dateArray = s.split(/[\.|\/|-]/);
      
        // correct month value
        dateArray[1] = dateArray[1]-1;

        // correct year value
        if (dateArray[0].length<4) {
            // correct year value
            dateArray[0] = (parseInt(dateArray[0]) < 50) ? 2000 + parseInt(dateArray[0]) : 1900 + parseInt(dateArray[0]);
        }

        var testDate = new Date(dateArray[0], dateArray[1], dateArray[2]);
        if (testDate.getDate()!=dateArray[2] || testDate.getMonth()!=dateArray[1] || testDate.getFullYear()!=dateArray[0]) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}