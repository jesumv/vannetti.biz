<?php

?>
<!DOCTYPE html>
<html>
	<head>
		<script>
		function FechaValida(s) {
    // format D(D)/M(M)/(YY)YY
    var dateFormat = /^\d{1,4}[\.|\/|-]\d{1,2}[\.|\/|-]\d{1,4}$/;

    if (dateFormat.test(s)) {
    	s = s.replace(/0*(\d*)/gi,"$1");
    	var dateArray = s.split(/[\.|\/|-]/);
    	// correct month value
        //dateArray[1] = dateArray[1]-1;
            return dateArray[1];

    } else {
        return false;
    }
}
			var resul=FechaValida("2016-01-02")
			if(resul){console.log(resul)}else{console.log("mal")}	
		</script>
	</head>
	    
</html>