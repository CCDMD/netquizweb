<div class="zoneMsg">
	<?php if (isset($messages)) { ?>

	<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1"><p><?php echo $messages->getMessages(); ?> </p></div>
	
	<script type="text/javascript">
		setTimeout(function() {
			$('#message1').animate({
			opacity: 0.0
		  }, 20000, function() {
			// Animation complete.
			$('#message1').hide(); // pour IE7 et IE8, après le fadeout du texte, on ferme la boite
		  });
			
		}, 21000);
	</script> 
			
	<?php } ?>
</div>
