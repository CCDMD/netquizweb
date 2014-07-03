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
       
		<div id="menuSec">
			<ul>
				<li><a id="identificationUtilisateur" class="drop" href="#" onClick="return false"><?php echo $usager->get("prenom") . " " . $usager->get("nom") ?></a>
																					
					<div class="ssMenuDeroul">
						<ul>
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
	</div>