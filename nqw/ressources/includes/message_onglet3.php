<div class="zoneMsg">
	<?php if (isset($messages)) { ?>

	<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message3"><p><?php echo $messages->getMessages(); ?> </p></div>
	
	<script type="text/javascript">
		setTimeout(function() {
			$('#message3').animate({
			opacity: 0.0
		  }, 20000, function() {
			// Animation complete.
			$('#message3').hide(); // pour IE7 et IE8, apr�s le fadeout du texte, on �teint la boite
		  });
			
		}, 21000);
	</script> 
			
	<?php } ?>
</div>