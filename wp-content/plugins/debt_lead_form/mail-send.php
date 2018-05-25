<?php

add_action('wp_ajax_lead_form_send_mail','lead_form_send_mail');
add_action('wp_ajax_nopriv_lead_form_send_mail','lead_form_send_mail');

function lead_form_send_mail(){

    $result = email_2_users_cb();

    echo $result;

    mail('matthewdotbarry@gmail.com', 'Inside mail-send', $result);
    
    wp_die();
}

function email_2_users_cb() { //settings subpage cb

	$args = array( 'role' => 'active_lead_recipient' , 'orderby' => 'ID' );


	$user_query = new WP_User_Query($args);
	$allusers = $user_query->get_results();

	if (isset($_POST['message'])) {

		$subject = "New Debt Site Lead";

        //Check current day
		date_default_timezone_set('Australia/Sydney');
		$today = date("dmY");

            //If its a new day, reset everything
		$is_day_set = get_user_meta($user_query->results[0]->ID, 'curr_day', true);

		if($is_day_set != $today){

                //Set current day and tallies
			foreach ( $user_query->results as $user ) {

                    //if current day is not today, set today
				$user_curr_day = get_user_meta($user->ID, 'curr_day', true);

				if($user_curr_day != $today){
					update_user_meta( $user->ID, 'curr_day', $today );
					update_user_meta( $user->ID, 'daily_tally', 0 );
				}
			}
                //Set first user to ricipient
			update_user_meta( $user_query->results[0]->ID, 'is_recipient',  true);
                //$user_query->results[0]->is_recipient = true;
		}

		$compacity_reached = 0;

		foreach ( $user_query->results as $user ) {
			$tally_check = get_user_meta($user->ID, 'daily_tally', true);
			$capacity_check = get_user_meta($user->ID, 'daily_capacity', true);

			if ( $tally_check === $capacity_check )
				$compacity_reached++;
		}

		if ( $compacity_reached == sizeof($user_query->results) ){
                //capacity reached
			$send_to = 'matthewdotbarry@gmail.com';
			$subject .= " - Capacity Reached";
		} else {

                //Find the recipient
			for( $i=0; $i < sizeof($user_query->results); $i++ ){

				$recipient = $user_query->results[$i];

                    //set next recipient
				if( $i+1 < sizeof($user_query->results) ){
					$next_recipient = $user_query->results[$i+1];
				} else{
					$next_recipient = $user_query->results[0];
				}

				$is_recipient_check = get_user_meta($recipient->ID, 'is_recipient', true);

                    //Check if this is the recipient 
				if( $is_recipient_check ){

					$tally_check = get_user_meta($recipient->ID, 'daily_tally', true);
					$capacity_check = get_user_meta($recipient->ID, 'daily_capacity', true);

                        //Check recipient tally
					if( $tally_check < $capacity_check ){
                            //Within capacity - exit
						$i = sizeof($user_query->results);
					} elseif ( $i+1 < sizeof($user_query->results) ) {
                            //If outsite capacity set the next 
						update_user_meta( $next_recipient->ID, 'is_recipient',  true);
					}
				}
			}

                //Increment Tally
			$tally = get_user_meta($recipient->ID, 'daily_tally', true);
			$tally++;
			update_user_meta( $recipient->ID, 'daily_tally',  $tally);

                //Set email
			$send_to = $recipient->data->user_email;


                //Set next recipient
			update_user_meta( $recipient->ID, 'is_recipient',  false);
			update_user_meta( $next_recipient->ID, 'is_recipient',  true);
		}

		$message = $_POST['message'];

		$send_to .=', matthewdotbarry@gmail.com';

		$headers = array('From: Dealing With Debt <noreply@dealingwithdebt.com.au>');

		$message_send_result = wp_mail($send_to, $subject, $message, $headers);

        
        echo "<h2>Emails have been sent successfully</h2>";
    }
}

	?>