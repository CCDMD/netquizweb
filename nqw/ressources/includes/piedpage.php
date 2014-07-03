<div id="piedpage">
	<ul id="ppmenu">
		<li><a href="questionnaires.php"><?php echo TXT_QUESTIONNAIRES ?></a></li>
		<li><a href="bibliotheque.php"><?php echo TXT_BIBLIOTHEQUE ?></a></li>
		
		<?php if ($usager->isAdmin()) { ?>
		
			<li><a href="compte.php?demande=compte_profil"><?php echo TXT_COMPTE ?></a></li>
			<li class="last"><a href="admin.php"><?php echo TXT_NETQUIZ_WEB_ADMIN ?></a></li>
			
		<?php } else { ?>
		
			<li class="last"><a href="compte.php?demande=compte_profil"><?php echo TXT_COMPTE ?></a></li>
		
		<?php } ?>
		
	</ul>
	<p id="copy"><?php echo TXT_CCDMD_DROITS ?> <img src="../images/copyright.png" alt="<?php echo TXT_CCDMD_DROITS ?>" /></p>
</div>