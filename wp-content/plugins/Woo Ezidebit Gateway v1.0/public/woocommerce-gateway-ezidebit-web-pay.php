<style type="text/css">
	.blockUI:before {
		margin-top: -20px !important;
		content: "" !important;
	}
</style>

<form id="ezidebit-form" method="post" action="<?php echo $data['ezidebit_url']; ?>">

	<!-- customer info -->
	<input type="hidden" name="FirstName" value="<?php echo $data['firstname']; ?>">
	<input type="hidden" name="LastName" value="<?php echo $data['lastname']; ?>">
	<input type="hidden" name="EmailAddress" value="<?php echo $data['email']; ?>">
	<input type="hidden" name="CompanyName" value="<?php echo $data['company']; ?>">
	<input type="hidden" name="Type" value="<?php echo $data['type']; ?>">
	
	<!-- payment info -->
	<input type="hidden" name="PaymentAmount" value="<?php echo $data['amount']; ?>">
	<input type="hidden" name="PaymentReference" value="<?php echo $data['payment_ref']; ?>">

	<!-- ezidebit payment page option -->
	<input type="hidden" name="RedirectMethod" value="POST">
	<input type="hidden" name="RedirectURL" value="<?php echo $data['return_url']; ?>">
	<input type="hidden" name="ShowDisabledInputs" value="0">

</form>