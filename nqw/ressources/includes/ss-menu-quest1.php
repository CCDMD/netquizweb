	<div id="ssMenu1">
		<h1><?php echo TXT_QUESTIONNAIRES ?></h1>
		<ul id="ssMenuItems">
			<li class="ssm-questionnaires">
				<div class="tools menuContexte">
					<img src="../images/ic-tools.png" alt="" />
					<?php include '../ressources/includes/menu-contexte-quest-ajouter.php' ?>
				</div>
				<a href="questionnaires.php?demande=liste"><span><?php echo TXT_MES_QUESTIONNAIRES ?></span></a>
			</li>
			<li class="ssm-corbeille"><a href="questionnaires.php?demande=corbeille"><span id="ssm1"><?php echo TXT_CORBEILLE ?></span></a></li>
		</ul>
	</div>