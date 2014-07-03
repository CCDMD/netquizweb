<div class="zoneMsgListe">
	<?php if (isset($messages)) { ?>
    
    <div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1">
        <ul> 
            <?php echo $messages->getMessages() ?>
        </ul>
    </div>
        
    <?php } ?>
</div>
