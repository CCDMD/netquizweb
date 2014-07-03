	<div id="entete">
		<a name="haut" id="haut"></a><a name="top" id="top"></a>
		<div id="entColG">
        	<div>
 				
 				<?php
 					$nbLang = 0; 
 					foreach ($listeLanguesInterface as $codeLangue => $titreLangue) { 

 						// Séparateur						
						if ($nbLang > 0) {
							print "&nbsp;|&nbsp;";
						}
						
						$nbLang++;
				?>
                   		
               			<a class="langue" href="questionnaires.php?lang=<?php echo $codeLangue ?>"><?php echo $titreLangue ?></a>
                    		
    	        <?php 
					} 
    	        ?>	       	
        		
        	</div>
			<a id="logo-netquiz" href="questionnaires.php"><img style="padding-top:20px;" src="../images/logo-netquiz.png" alt="<?php echo TXT_ACCUEIL_NETQUIZWEB ?>"  /></a>
		</div>
		
        <div id="logo-ccdmd">
			<a href="http://www.ccdmd.qc.ca/" target="_blank"><img src="../images/logo-ccdmd.png" alt="<?php echo TXT_ACCUEIL_CCDMD ?>" /></a>
		</div>
       
        <div id="projetActif">
            <label class="displayInline"><?php echo TXT_PROJET_COURANT ?> : </label>
            <div class="menuContexte displayInline">
				<a class="tools" href="#"><?php echo $projetActif->get("titre") . " (" . TXT_PREFIX_PROJET . $projetActif->get("id_projet") . ")" ?>&nbsp;<img src="../images/ic-fleche-bleu-bas.png" alt="" /></a>

                <div class="menuDeroul">
                    <ul class="sansTitre">
                    	<?php 
                    		foreach ($listeProjetsActifs as $proj) {
                    	?>

                    		<li <?php if ($idProjetActif == $proj->get("id_projet")) { echo "class=\"inactif\""; } ?> ><a href="#" onclick="if (isLienActif(this)) { changerProjet('identification.php?demande=projet_selectionner&id_projet=<?php echo $proj->get("id_projet") ?>') } else { return false; }" ><?php echo $proj->get("titre") . " (" . TXT_PREFIX_PROJET . $proj->get("id_projet") . ")" ?></a></li>
                        
                        <?php 
                        	}
                        ?>
                    </ul>
                </div>
			</div>
		</div>
        
		<div id="menuSec">
			<ul>
				<li><a id="identificationUtilisateur" class="drop" href="#" onClick="return false"><?php echo $usager->get("prenom") . " " . $usager->get("nom") ?></a>
																					
					<div class="ssMenuDeroul">
						<ul>
							<li><a href="compte.php?demande=compte_profil"><?php echo TXT_MON_PROFIL ?></a></li>
							<li><a href="compte.php?demande=compte_mdp"><?php echo TXT_MON_MOT_DE_PASSE ?></a></li>
							<li><a href="identification.php?demande=deconnexion"><?php echo TXT_DECONNEXION ?></a></li>
						</ul>
					</div>
				</li>
				<li><a class="drop lnk-fancybox" href="questionnaires.php?demande=aide_apropos_intro"><?php echo TXT_A_PROPOS ?>...</a>
					<div class="ssMenuDeroul">
						<ul>
							<li><a class="lnk-fancybox" href="questionnaires.php?demande=aide_apropos_intro"><?php echo TXT_INTRODUCTION ?></a></li>
							<li><a class="lnk-fancybox" href="questionnaires.php?demande=aide_apropos_droits"><?php echo TXT_DROITS_UTILISATION ?></a></li>
							<li><a class="lnk-fancybox" href="questionnaires.php?demande=aide_apropos_generique"><?php echo TXT_GENERIQUE ?></a></li>
							<li><a class="lnk-fancybox" href="questionnaires.php?demande=aide_apropos_commentaires"><?php echo TXT_COMMENTAIRES ?></a></li>
						</ul>
					</div>
				</li>
				<li class="last"><a href="#" onClick="NewWindow('http://aide.ccdmd.qc.ca/nqw/','aide','1024','800','yes','yes','yes')"><?php echo TXT_AIDE?></a></li>
			</ul>
		</div>
		
        <div id="menu">
			<ul>
				<li id="menu-questionnaires"><a href="questionnaires.php"><?php echo TXT_QUESTIONNAIRES ?></a></li>
				<li id="menu-bibliotheque"><a href="bibliotheque.php"><?php echo TXT_BIBLIOTHEQUE ?></a></li>
				<li id="menu-compte" class="sepVertical"><a href="compte.php?demande=compte_profil"><?php echo TXT_COMPTE ?></a></li>
				
				<?php if ($usager->isAdmin()) { ?>
					<li id="menu-admin" class="last"><a href="admin.php"><?php echo TXT_ADMINISTRATION ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	
	<!--  Lien pour fenêtre jaillissante servant à la gestion de la session -->
	<a class="fenetreGestionSession" href="questionnaires.php?demande=session_message"></a>
	
	