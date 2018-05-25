<?php
/*
Plugin Name: Lead Form 
Description: Custom lead form
Version: 1.0
Author: Matthew Barry
Author URI: http://matthewbarry.me
*/


/****************************************************************************************************************************************************************
	// SHORTCODES
****************************************************************************************************************************************************************/

// Lead form
function mjb_lead_form() {

	global $add_my_script;

	$add_my_script = true;

	global $mjb_load_css;

	// set this to true so the CSS is loaded
	$mjb_load_css = true;

	$output = mjb_lead_form_fields();
	
	return $output;
}
add_shortcode('lead_form', 'mjb_lead_form');


// Lead form
function mjb_single_lead_form() {

	global $add_my_script;

	$add_my_script = true;

	global $mjb_load_css;

	// set this to true so the CSS is loaded
	$mjb_load_css = true;

	$output = mjb_single_lead_form_fields();
	
	return $output;
}
add_shortcode('single_lead_form', 'mjb_single_lead_form');


/****************************************************************************************************************************************************************
	// FORM HTML
****************************************************************************************************************************************************************/

// Registration form fields
function mjb_lead_form_fields() {

	ob_start(); ?>	

	<?php 
		// show any error messages after form submission
	mjb_show_error_messages(); ?>


	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">

				<div class="debt-progress-wrapper">
					<ul class="debt-progress-bar">
						<li class="stage-indicator active form-stage-1 first"></li>
						<li class="stage-indicator form-stage-2"></li>
						<li class="stage-indicator form-stage-3"></li>
						<li class="stage-indicator form-stage-4"></li>
						<li class="stage-indicator form-stage-5"></li>
						<li class="stage-indicator form-stage-6"></li>
					</ul>
				</div>

				<form id="mjb_lead_form" class="MJB_formSend mjb_form form-horizontal text-center" action="" method="POST">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active" id="form-stage-1">
							<div class="wrapper panel bg-white-90">
								<h3>Do you have unsecured debts of $8,000 or greater?</h3>
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg tab-nav tab-set" value="has unsecured debts of $8000 or greater" value-target="#debt_value" data-target="#form-stage-2" aria-controls="form-stage-2" role="tab" data-toggle="tab">Yes</a>
										</div>
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg show-message">No</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade " id="form-stage-2">
							<div class="wrapper panel bg-white-90">
								<h3>Do you receive a regular income?</h3>
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg tab-nav tab-set" value="receives a regular income" value-target="#regular_income" data-target="#form-stage-3" aria-controls="form-stage-3" role="tab" data-toggle="tab">Yes</a>
										</div>
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg show-message">No</a>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4 col-sm-offset-4">
											<a class="tab-nav tab-back" data-target="#form-stage-1" aria-controls="form-stage-1" role="tab" data-toggle="tab">Go Back</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="form-stage-3">
							<div class="wrapper panel bg-white-90">
								<h3>Have you been bankrupt in the last 10 years?</h3>
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg show-message">Yes</a>
										</div>
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg tab-nav tab-set" value="has not been bankrupt in the last 10 years" value-target="#bankrupt" data-target="#form-stage-4" aria-controls="form-stage-4" role="tab" data-toggle="tab">No</a>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4 col-sm-offset-4">
											<a class="tab-nav tab-back" data-target="#form-stage-2" aria-controls="form-stage-2" role="tab" data-toggle="tab">Go Back</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade " id="form-stage-4">
							<div class="wrapper panel bg-white-90">
								<h3>Are you behind on payments?</h3>
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg tab-nav tab-set" value="is not behind on payments" value-target="#payments_behind" data-target="#form-stage-5" aria-controls="form-stage-5" role="tab" data-toggle="tab">Yes</a>
										</div>
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg tab-nav tab-set" value="is behind on payments" value-target="#payments_behind" data-target="#form-stage-5" aria-controls="form-stage-5" role="tab" data-toggle="tab">No</a>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4 col-sm-offset-4">
											<a class="tab-nav tab-back" data-target="#form-stage-3" aria-controls="form-stage-3" role="tab" data-toggle="tab">Go Back</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade " id="form-stage-5">
							<div class="wrapper panel bg-white-90">
								<h3>Are you a home owner?</h3>
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg tab-nav tab-set" value="Is a home owner" value-target="#home_owner" data-target="#form-stage-6" aria-controls="form-stage-6" role="tab" data-toggle="tab">Yes</a>
										</div>
										<div class="col-sm-6 m-b">
											<a class="btn btn-block btn-primary btn-lg tab-nav tab-set" value="isn't a home owner" value-target="#home_owner" data-target="#form-stage-6" aria-controls="form-stage-6" role="tab" data-toggle="tab">No</a>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4 col-sm-offset-4">
											<a class="tab-nav tab-back" data-target="#form-stage-4" aria-controls="form-stage-4" role="tab" data-toggle="tab">Go Back</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="form-stage-6">
							<div class="panel panel-default b-a bg-white-90">
								<div class="panel-heading bg-primary">
									<h2 class="text-white">You Qualify For Assistance!</h2>
								</div>
								<div class="panel-body">
									<p>Please provide your contact details below. A debt specialist will be in touch to provide you with <strong>debt relief options and a free savings estimate</strong>.</p>
									
									<div class="m-b-sm">
										<input id="first_name" class="form-control" name="firstName" type="text" placeholder="First name*" required>
									</div>
									<div class="m-b-sm">
										<input id="last_name" class="form-control" name="lastName" type="text" placeholder="Last name*" required>
									</div>
									<div class="m-b-sm">
										<input id="your_phone" class="form-control" name="number" type="tel" maxlength="10" placeholder="Best contact number*" required>
									</div>
									<div class="m-b-sm">
										<input id="your_email" class="form-control" name="email" type="email" placeholder="Email address*" required>
									</div>
									<div class="m-b-sm">
										<input id="your_postcode" class="form-control" name="postcode" type="tel" maxlength="4" placeholder="Postcode*" required>
									</div>

									<div class="m-b-sm">
										<textarea name="debt_description" id="debt_description" class="form-control" placeholder="A description of your debt"></textarea>
									</div>
									
									<div class="text-left m-b">
										<span class="checkbox">
											<label class="i-checks text-sm">
												<input type="checkbox" id="mjb_terms" name="mjb_terms" value="terms" required><i></i> I agree to be contacted by a debt specialist by phone, email or SMS.
											</label>
										</span>
									</div>

									<input type="hidden" class="form_answer" name="debt_value" id="debt_value">
									<input type="hidden" class="form_answer" name="regular_income" id="regular_income">
									<input type="hidden" class="form_answer" name="bankrupt" id="bankrupt">
									<input type="hidden" class="form_answer" name="payments_behind" id="payments_behind">
									<input type="hidden" class="form_answer" name="home_owner" id="home_owner">

									<input type="submit" class="btn btn-block btn-success btn-lg m-b" value="<?php _e('Submit'); ?>"/>

									<small>See our <a target="_blank" href="../privacy-policy">privacy policy and terms of use</a> for more information.</small>
								</div>
							</div>
						</div>
					</div>
					<div class="failure_message"></div>
				</form>
				<span class="loadingResults"></span>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}







// Registration single form fields
function mjb_single_lead_form_fields() {

	ob_start(); ?>	

	<?php 
		// show any error messages after form submission
	mjb_show_error_messages(); ?>


	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">

				<form name="single_lead_form" id="mjb_single_lead_form" class="MJB_formSend mjb_form mjb_single_form form-horizontal text-center" action="" method="POST">
					<div class="panel panel-default b-a bg-white-90">
						<div class="panel-heading bg-primary">
							<h2 class="text-white">15 Second Eligibility Analysis</h2>
						</div>
						<div class="panel-body">
							
								<div class=" wrapper">
										<div class="b-b">
								<div class="row m-b-lg text-left">
									<div class="col-xs-12 col-sm-9">
										Do you have unsecured debt of $8,000 or greater?
									</div>
									<div class="col-xs-12 col-sm-3 text-right">
										<div class="btn-group" data-toggle="buttons">
											<div class="btn btn-sm">
												<input type="radio" class="radio_check" name="debt_value" value="has unsecured debts of $8000 or greater">
												<span>Yes</span>
											</div>

											<div class="btn btn-sm ">
												<input type="radio" class="radio_check radio_check_error" name="debt_value" value="does not have secured debts of $8000 or greater">
												<span>No</span>
											</div>
										</div>

									</div>
									<div class="col-xs-12 text-center radio_error_display debt_value_error text-danger text-sm">Our service partners require $8000 in unsecured debt to apply.<br>Consider speaking to a <a href="https://www.financialcounsellingaustralia.org.au">free Financial Counsellor</a> for other financial assistance options.</div>
								</div>
								<div class="row m-b-lg text-left">
									<div class="col-xs-12 col-sm-9">
										Do you receive a regular income?
									</div>
									<div class="col-xs-12 col-sm-3 text-right">
										<div class="btn-group" data-toggle="buttons">
											<div class="btn btn-sm ">
												<input type="radio" class="radio_check" name="regular_income" value="receives a regular income">
												<span>Yes</span>
											</div>

											<div class="btn  btn-sm ">
												<input type="radio" class="radio_check radio_check_error" name="regular_income" value="does not receive a regular income">
												<span>No</span>
											</div>
										</div>
									</div>
									<div class="col-xs-12 text-center radio_error_display regular_income_error text-danger text-sm">Our service parnters require a regular income source to apply.<br>Consider speaking to a <a href="https://www.financialcounsellingaustralia.org.au">free Financial Counsellor</a> for other financial assistance options.</div>
								</div>
								
								<div class="row m-b-lg text-left">
									<div class="col-xs-12 col-sm-9">
										Have you been bankrupt in the last 10 years?
									</div>
									<div class="col-xs-12 col-sm-3 text-right">
										<div class="btn-group" data-toggle="buttons">
											<div class="btn btn-sm ">
												<input class="radio_check radio_check_error" type="radio" name="bankrupt" value="has been bankrupt in the last 10 years">
												<span>Yes</span>
											</div>

											<div class="btn  btn-sm">
												<input class="radio_check" type="radio" name="bankrupt" value="has not been bankrupt in the last 10 years">
												<span>No</span>
											</div>
										</div>
									</div>
									<div class="col-xs-12 text-center radio_error_display bankrupt_error text-danger text-sm">Unfortunately we will be unable to assist you.<br>Consider speaking to a <a href="https://www.financialcounsellingaustralia.org.au">free Financial Counsellor</a> for other financial assistance options.</div>
								</div>
								
								<div class="row m-b-lg text-left">
									<div class="col-xs-12 col-sm-9">
										Are you behind on repayments?
									</div>
									<div class="col-xs-12 col-sm-3 text-right">
										<div class="btn-group" data-toggle="buttons">
											<div class="btn btn-sm ">
												<input type="radio" name="payments_behind" value="is behind on payments">
												<span>Yes</span>
											</div>

											<div class="btn  btn-sm ">
												<input type="radio" name="payments_behind" value="is not behind on payments">
												<span>No</span>
											</div>
										</div>
									</div>
									
								</div>
								<div class="row m-b-lg text-left">
									<div class="col-xs-12 col-sm-9">
										Are you a home owner?
									</div>
									<div class="col-xs-12 col-sm-3 text-right">
										<div class="btn-group" data-toggle="buttons">
											<div class="btn btn-sm ">
												<input type="radio" name="home_owner" value="is a home owner">
												<span>Yes</span>
											</div>
											<div class="btn  btn-sm ">
												<input type="radio" name="home_owner" value="is not a home owner">
												<span>No</span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row m-t-xl m-b-sm">
								<label for="first_name" class="col-sm-4 text-right">First Name:<em>*</em> </label>
								<div class="col-sm-8">
									<div class=" input-group addon-right">
										<input id="first_name" class="form-control personal-setting" name="firstName" type="text" required>
										<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
									</div>
								</div>
							</div>
							<div class="row m-b-sm">
								<label for="last_name" class="col-sm-4 text-right">Last Name:<em>*</em> </label>
								<div class="col-sm-8">
									<div class="input-group addon-right">
										<input id="last_name" class="form-control personal-setting" name="lastName" type="text" required>
										<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
									</div>
								</div>
							</div>
							<div class="row m-b-sm">
								<label for="dob" class="col-sm-4 text-right">Date of Birth:<em>*</em> </label>

								<div class="col-sm-8">
									<div class="input-group addon-right">
										<input id="dob" class="form-control personal-setting date" name="dob" type="tel" max="" placeholder="dd/mm/yyyy">
										<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
									</div>
								</div>

							</div>
							<div class="row m-b-sm">
								<label for="your_email" class="col-sm-4 text-right">Email:<em>*</em> </label>
								<div class="col-sm-8">
									<div class=" input-group addon-right">
										<input id="your_email" class="form-control personal-setting" name="email" type="email" required>
										<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
									</div>
								</div>
							</div>
							<div class="row m-b-sm">
								<label for="your_phone" class="col-sm-4 text-right">Phone:<em>*</em> </label>
								<div class="col-sm-8">
									<div class=" input-group addon-right">
										<input id="your_phone" class="form-control personal-setting" maxlength="10" name="number" type="tel" required>
										<span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
									</div>
								</div>
							</div>
							<div class="row m-b-sm">
								<label for="your_postcode" class="col-sm-4 text-right">Postcode:<em>*</em> </label>
								<div class="col-sm-8">
									<div class=" input-group addon-right">
										<input id="your_postcode" class="form-control personal-setting" maxlength="4" name="postcode" type="tel" required>
										<span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
									</div>
								</div>
							</div>

							<div class="text-left m-b">
								<span class="checkbox">
									<label class="i-checks text-sm">
										<input type="checkbox" id="mjb_terms" name="mjb_terms" value="terms" required><i></i> I agree to be contacted by phone, email or sms to receive a free debt savings estimate
									</label>
								</span>
							</div>

							<input type="submit" class="btn btn-block btn-success btn-lg m-b" value="See Results" >

							<small>See our <a target="_blank" href="../privacy-policy">privacy policy and terms of use</a> for more information.</small>
						
						
						</div>
						</div>
					</div>

					<div class="failure_message"></div>
				</form>
				<span class="loadingResults"></span>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}









/****************************************************************************************************************************************************************
	// PROCESS FORMS
****************************************************************************************************************************************************************/

// Register a new user
function wis_add_new_member() {
	if ( isset( $_POST["mjb_name"] ) &&  isset( $_POST["mjb_phone"] ) && isset( $_POST["mjb_email"] ) ) {
		
		$lead_name 				= $_POST["mjb_name"];
		$lead_phone				= $_POST["mjb_phone"];
		$lead_email		 		= $_POST["mjb_email"];
		$lead_post_code		 	= $_POST["mjb_post_code"];
		$lead_debt_description	= $_POST["mjb_debt_description"];
		

		if($lead_email == '') {
			// empty email
			mjb_errors()->add('email_empty', __('Please enter an email'));
		}
		if(!is_email($lead_email)) {
			//invalid email
			mjb_errors()->add('email_invalid', __('Invalid email'));
		}
		

		$errors = mjb_errors()->get_error_messages();

		// only create the user in if there are no errors
		if(empty($errors)) {

			
			
			if($new_user_id) {
				// send an email to the admin alerting them of the registration
				wp_new_user_notification($new_user_id);
				$from_name = 'LICG';
				$from_address = 'noreply@rex.finance';
				$headers = array("From: " . $from_name . " <" . $from_address . ">", "Content-Type: text/html");
				$h = implode("\r\n",$headers) . "\r\n";
				$notify_email = array(
					'matthew.barry@wisecure.it',
					'brad.powar@wisecure.it'
					);
				$notify_subject = 'New Sign Up for LICG';

				$body =
				'First Name: '.$user_first.'<br>
				Last Name: '.$user_last.'<br>
				Phone Number: '.$user_phone.'<br>
				Email: '.$user_email.'<br>
				Company: '.$user_company.'<br>
				Clients: '.$user_num_clients.'<br>
				Interested in: '.$user_wis_referee.' '.$user_wis_referrer.'
				';

				wp_mail( $notify_email, $notify_subject, $body, $h );


				// send the newly created user to the home page after logging them in
				wp_redirect( '/thank-you'); exit;
			}			
		}
	}
}
add_action('init', 'wis_add_new_member');

/****************************************************************************************************************************************************************
	// Error Handling
****************************************************************************************************************************************************************/

// Tracks error messages
function mjb_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// Displays error messages from form submissions
function mjb_show_error_messages() {
	if($codes = mjb_errors()->get_error_codes()) {
		echo '<div class="wis_errors">';
		    // Loop error codes and display errors
		foreach($codes as $code){
			$message = mjb_errors()->get_error_message($code);
			echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		}
		echo '</div>';
	}	
}


/****************************************************************************************************************************************************************
	// Load CSS
****************************************************************************************************************************************************************/


// Register form css
function mjb_register_css() {
	wp_register_style('mjb-form-css', plugin_dir_url( __FILE__ ) . '/css/lead-form-style.css');
}
add_action('init', 'mjb_form_css');


// Load form css
function mjb_print_css() {
	global $mjb_load_css;

	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $mjb_load_css )
		return; // this means that neither short code is present, so we get out of here

	wp_print_styles('mjb-form-css');
}
add_action('wp_footer', 'mjb_print_css');



/****************************************************************************************************************************************************************
	// Display new company fields in user profile
****************************************************************************************************************************************************************/


add_action('init', 'register_my_script');
add_action('wp_footer', 'print_my_script');

function register_my_script() {
	wp_register_script('form-script', plugins_url('lead_form.js', __FILE__), array('jquery'), '1.0', true);
	wp_register_script('validate-script', plugins_url('jquery.validate.min.js', __FILE__), array('jquery'), '1.0', true);
}

function print_my_script() {
	global $add_my_script;

	if ( ! $add_my_script )
		return;

	wp_enqueue_script('validate-script');
	wp_enqueue_script('form-script');
	
}