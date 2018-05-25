<?php
	
	//$args = array( 'role' => 'Active Lead Recipient' , 'orderby' => 'ID' );
	// Gett Users
	// The Query
/*
$blogusers = get_users( 'role=admin' );
// Array of WP_User objects.
foreach ( $blogusers as $user ) {
	echo '<span>' . esc_html( $user->user_email ) . '</span>';
}

	$args = array( 'role' => 'Active Lead Recipient' , 'orderby' => 'ID' );

        // Gett Users
        $user_query = new WP_User_Query( $args );
        
        // User Loop
        if ( ! empty( $user_query->results ) ) {

*/
        	echo 'test';
//send_email_to_all_users('text', 'subject'	);

        	 $args = [
        'number' => -1
    ];

    $user_query = new WP_User_Query($args);
    $allusers = $user_query->get_results();

function send_email_to_all_users($text, $subject, $user_query = false) { //this is function you'r looking for
    if (!is_a($user_query, 'WP_User_Query')) {
        $args = [
            'number' => -1
        ];
        $user_query = new WP_User_Query($args);
    }
    $allusers = $user_query->get_results();
    foreach ($allusers as $i => $user) {
    	echo $user->data->user_email;
        //$allusers[$i]->sent = wp_mail($user->data->user_email, $subject, $text);
    }

    return $allusers;
}
?>