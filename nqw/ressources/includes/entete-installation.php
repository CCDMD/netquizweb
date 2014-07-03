	<div id="entete">
		<a name="haut" id="haut"></a><a name="top" id="top"></a>
		<div id="banniere">
			<!--[if lt IE 7]><a id="logo-ccdmd" href="http://www.ccdmd.qc.ca/" target="_blank"><img src="../images/logo-ccdmd-ie6.png" alt="Accueil CCDMD"  /></a><![endif]-->
			<!--[if gte IE 7]><a id="logo-ccdmd" href="http://www.ccdmd.qc.ca/" target="_blank"><img src="../images/logo-ccdmd.png" alt="Accueil CCDMD"  /></a><![endif]--> 	
			<!--[if !IE]><-->
			<a id="logo-ccdmd" href="http://www.ccdmd.qc.ca/" target="_blank"><img src="../images/logo-ccdmd.png" alt="<?php echo TXT_ACCUEIL_CCDMD ?>"  /></a>
			<!--><![endif]--> 	
			
			<?php
				$nbLang = 0; 
				if (isset($listeLanguesInterface)) {
	 				foreach ($listeLanguesInterface as $codeLangue => $titreLangue) { 
	
	 					// Separateur						
						if ($nbLang > 0) {
							print "&nbsp;|&nbsp;";
						}
							
						$nbLang++;
				?>
	                   		
	            		<a class="langue" href="install.php?lang=<?php echo $codeLangue ?>"><?php echo $titreLangue ?></a>
	                    		
	    	    <?php 
						}
					} 
	    	    ?>	    			
    	    
    	    <br />
			
			<!--[if lt IE 7]><a id="logo-netquiz" href="<?php echo URL_ACCUEIL ?>"><img src="../images/logo-netquiz-index-ie6.png" alt="<?php echo TXT_ACCUEIL_NETQUIZWEB ?>"  /></a><![endif]-->
			<!--[if gte IE 7]><a id="logo-netquiz" href="<?php echo URL_ACCUEIL ?>"><img src="../images/logo-netquiz-index.png" alt="<?php echo TXT_ACCUEIL_NETQUIZWEB ?>"  /></a><![endif]--> 	
			<!--[if !IE]><-->
			<a id="logo-netquiz" href="<?php echo URL_ACCUEIL ?>"><img src="../images/logo-netquiz-index.png" alt="<?php echo TXT_ACCUEIL_NETQUIZWEB ?>"  /></a>
			<!--><![endif]--> 	
		</div>
	</div>