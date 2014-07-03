<div class="zoneMsg" id="zoneMsg" style="display:none; ">
	<?php if (isset($messages)) { ?>
	
	<script type="text/javascript">
		document.getElementById("zoneMsg").style.display = 'block';
	</script> 

	<div class="boxMsg<?php echo $messages->getTypeMessage() ?> boxMsgTableaux" id="message1"><p><?php echo $messages->getMessages(); ?> </p></div>
	
	<script type="text/javascript">
		setTimeout(function() {
			$('#message1').animate({
			opacity: 0.0
		  }, 20000, function() {
			// Animation complete.
			$('#message1').hide(); // pour IE7 et IE8, apr�s le fadeout du texte, on �teint la boite
			document.getElementById("zoneMsg").style.display = 'none';
		  });
			
		}, 21000);
	</script> 
			
	<?php } ?>
</div>
