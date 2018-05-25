jQuery(document).ready(function(){
	jQuery(document).off('click', '.not-a-member-handle');
	jQuery(document).on('click', '.not-a-member-handle',function(){
		location.href = "/sign-up";
	});
	jQuery('a.not-a-member-handle').attr('href','/sign-up'); 
	
	jQuery(document).off('click', '.already-registered-handle');
	jQuery(document).on('click', '.already-registered-handle',function(){
		location.href = "/sign-in";
	});
	jQuery('a.already-registered-handle').attr('href','/sign-in'); 
});