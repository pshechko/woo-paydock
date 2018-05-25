(function( $ ) {

  // Initialize form validation on the registration form.
  // It has the name attribute "registration"

  	
  

	$('.tab-nav').unbind('click');
	$('.tab-nav').bind('click', function (e) {
		e.preventDefault();
		$('.failure_message').slideUp();
		var stage = '';
		if( $(this).hasClass('tab-set') ){
			var target = $(this).attr('value-target');
			var val = $(this).attr('value');
			console.log('Target: '+target+' Value: '+val);
			$(target).val(val);
		}
		if( $(this).hasClass('tab-back') ){
			console.log('back');
			stage = '.'+$(this).closest( '.tab-pane' ).attr('id');
			console.log('back: '+stage);
			$(stage).removeClass('active');
		} else {
			stage = '.'+$(this).attr( 'aria-controls' );
			$(stage).addClass('active');
		}
	});	

	$('.show-message').unbind('click');
	$('.show-message').bind('click', function (e) {
		e.preventDefault();
		console.log( $(this).closest( '.tab-pane' ).attr('id') );
		print_message( $(this).closest( '.tab-pane' ).attr('id') );
	});

	function print_message(stage){
		console.log('inside');
		var message = '';
		switch(stage){

			case 'form-stage-1' :
				message = '<div class="alert alert-danger">Our service partners require $8000 in unsecured debt to apply.<br>Consider speaking to a <a href="https://www.financialcounsellingaustralia.org.au">free Financial Counsellor</a> for other financial assistance options.</div>';
				break;	
			case 'form-stage-2' :
				message = '<div class="alert alert-danger">Our service parnters require a regular income source to apply.<br>Consider speaking to a <a href="https://www.financialcounsellingaustralia.org.au">free Financial Counsellor</a> for other financial assistance options.</div>';
				break;
			case 'form-stage-3' :
				message = '<div class="alert alert-danger">Unfortunately we will be unable to assist you.<br>Consider speaking to a <a href="https://www.financialcounsellingaustralia.org.au">free Financial Counsellor</a> for other financial assistance options.</div>';
				break;
			default :
				message = '<div class="alert alert-danger">Unfortunately we will be unable to assist you.<br>Consider speaking to a <a href="https://www.financialcounsellingaustralia.org.au">free Financial Counsellor</a> for other financial assistance options.</div>';
		}
		console.log(message);
		$('.failure_message').html(message);
		$('.failure_message').slideDown();
	}

	$('.radio_check').change(function (e) {
		if( $(this).hasClass('radio_check') ){
			var display = '.'+$(this).attr('name')+'_error';
			if( $(this).hasClass('radio_check_error') ){
				$(display).show();
				$(display).addClass('error_active');
			} else {
				$(display).hide();
				$(display).removeClass('error_active');
			}
			var errors = 0;
			$('.radio_error_display').each(function(i) {
				if ( $(this).hasClass('error_active') )
					errors++;
			});
			if (errors){
				$('input[type=submit]').attr('disabled','disabled');
			} else{
				$('input[type=submit]').removeAttr('disabled');
			}
		}
	});


	$('.MJB_formSend').unbind('submit');
	$('.MJB_formSend').bind('submit', function(e){
		e.preventDefault();
		$("html, body").animate({ scrollTop: 0 }, "slow");
		//check date

		var dob = $('#dob').val().split("/");
		console.log("herer");
		console.log(dob+' '+dob[0]+' '+dob[1]);
		if ( (dob[0] < 13) || (dob[1] < 13)){
			$('#dob-error').hide();
			$("form[name='single_lead_form']").validate({
		    // Specify validation rules
		    rules: {
		      // The key name on the left side is the name attribute
		      // of an input field. Validation rules are defined
		      // on the right side
		      debt_value: "required",
		      regular_income: "required",
		      bankrupt: "required",
		      payments_behind: "required",
		      home_owner: "required",
		      
		      firstName: "required",
		      lastName: "required",
		      email: {
		        required: true,
		        // Specify that email should be validated
		        // by the built-in "email" rule
		        email: true
		      },
		      number: {
		        required: true,
		        maxlength: 10
		      },
		      postcode: {
		        required: true,
		        minlength: 4,
		        maxlength: 4
		      },
		      mjb_terms: "required"

		    },
		    // Specify validation error messages
		    messages: {
		      firstname: "Please enter your firstname",
		      lastname: "Please enter your lastname",
		      email: "Please enter a valid email address"
		    },
		    // Make sure the form is submitted to the destination defined
		    // in the "action" attribute of the form when valid
		    submitHandler: function(form) {
		      consolidateFields();
		    }
		  });   
		} else {
			console.log('FAIL');
			$('#dob').after('<label id="dob-error" class="error" for="dob">Please enter a valid date.</label>');
		}

	    //var data = $(this).serializeObject();
	    //serverComm('http://dealingwithdebt.com.au/wp-content/themes/layerswp/WiSecureAPI.php', data, showMessage, 'loadingResults')
	});

	function showMessage(data){
		$.ajax({
            type: "POST",
            url: "http://dealingwithdebt.com.au/wp-content/plugins/debt_lead_form/mail-send.php",
            data: data,
            success: function(){
            console.log('Done');
            }
        });
	    window.location.href ="http://dealingwithdebt.com.au/thank-you/";
	}

	function consolidateFields(){
		var firstName = $('[name="firstName"]').val();
		var lastName = $('[name="lastName"]').val();
		var number = $('[name="number"]').val();
		var email = $('[name="email"]').val();
		var postcode = $('[name="postcode"]').val();
		var borrowAmount = $('[name="dob"]').val();
		var depositAmount = $('[name="debt_value"]:checked').val()+', '+$('[name="regular_income"]:checked').val()+', '+$('[name="bankrupt"]:checked').val()+', '+$('[name="payments_behind"]:checked').val()+', '+$('[name="home_owner"]:checked').val();

		serverComm('http://dealingwithdebt.com.au/wp-content/themes/layerswp/WiSecureAPI.php', {firstName: firstName, lastName: lastName, number: number, email: email, postcode: postcode, borrowAmount: borrowAmount, depositAmount: depositAmount }, showMessage, 'loadingResults');
	};

	function serverComm(url, data, callbackFunction, statusClass){
		if (statusClass) $('.'+statusClass).fadeIn();
		$('.jQuery_connectionQuery').slideDown();
		var request = $.ajax({
			url:        url,
			type:       'POST',
			data:       data,
			dataType:   'json',
			error:      testErrorFunction
		});
		request.done(function(returnData){
			if (statusClass) $('.'+statusClass).fadeOut();
			if (typeof callbackFunction === "function") {
				callbackFunction(returnData);
			}
		});
	}

	$.fn.serializeObject = function(){
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } 
            else {
                o[this.name] = this.value || '';
            }
            if ($("input[name='"+this.name+"']").attr('readReset') == "true"){
                $("input[name='"+this.name+"']").val('');
            }

        });
        return o;
    };
    
    function testErrorFunction(e){
        alert('transferError');
    };

})( jQuery );