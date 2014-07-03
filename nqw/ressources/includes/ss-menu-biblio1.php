	<div id="ssMenu1">
		<h1><?php echo TXT_BIBLIOTHEQUE ?></h1>
		<ul id="ssMenuItems">
			<li class="ssm-items">
				<div class="tools menuContexte">
					<img src="../images/ic-tools.png" alt="" />
					<?php include '../ressources/includes/menu-contexte-items-ajouter-biblio2.php' ?>
				</div>
				<a href="bibliotheque.php"><span><?php echo TXT_MES_ITEMS ?></span></a>
			</li>
			<li class="ssm-medias"><a href="media.php?demande=media_liste"><span id="ssm1"><?php echo TXT_MES_MEDIAS ?></span></a></li>
			<li class="ssm-termes"><a href="bibliotheque.php?demande=terme_liste"><span id="ssm3"><?php echo TXT_MES_TERMES ?></span></a></li>
			<li class="ssm-langues"><a href="bibliotheque.php?demande=langue_liste"><span id="ssm2"><?php echo TXT_MES_LANGUES ?></span></a></li>
			<li class="ssm-collections"><a href="bibliotheque.php?demande=collection_liste"><span id="ssm4"><?php echo TXT_MES_COLLECTIONS ?></span></a></li>
			<li class="ssm-categories"><a href="bibliotheque.php?demande=categorie_liste"><span id="ssm5"><?php echo TXT_MES_CATEGORIES ?></span></a></li>
			<li class="ssm-corbeille"><a href="bibliotheque.php?demande=corbeille"><span id="ssm6"><?php echo TXT_CORBEILLE ?></span></a></li>
		</ul>
	</div>