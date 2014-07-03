<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_ITEMS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies-avec-editeur.php' ?>

	<script type="text/javascript">

	// Changer de page
	function changerPage(page) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $item->get("page_courante") ?>";

		if (page != pageCour) {
			document.frm.demande.value="item_sauvegarder";
			document.frm.pagination_page_dest.value=page;
			document.frm.submit();
		}
	}

	// Annuler
	function annuler() {

		desactiverSuiviModifications();
		
		if (confirm(TXT_AVERTISSEMENT_ANNULER)) {
			// Obtenir l'onglet
			onglet = document.frm.onglet.value;
			
			// Obtenir l'URL
			url = "bibliotheque.php?demande=item_modifier&item_id_item=<?php echo $item->get("id_item") ?>&onglet=" + onglet;

			// Rediriger vers la page
			document.location.href = url;
			
		} else {
			activerSuiviModifications(1);
		}
	}

	// Activer suivi
	function activerSuivi() {
		envoiSuiviItem('bibliotheque.php', 'item_suivi','', '<?php echo $item->get("id_item") ?>');
	}

	// Vérifier la catégorie choisie
	function validerCategorie() {

		sel = $("#categorieSelect").val();
		if (sel != "") {
			$("#categorieText").val("");
		} 
	}

	// Enregistrer la sélection du questionnaire et soumettre
	function ajouterItemsAuQuestionnaire(idQuest) {
		console.log("idQuest : " + idQuest);
		document.frm.questionnaire_dest.value = idQuest;
		document.frm.demande.value="item_ajouter_questionnaire_item";
		document.frm.submit();
	}	

	// Traitement spécifique au changement de certains onglets pour zones à identifier
	function traitementChangementOnglet(onglet) {
		<?php if ($item->get("type_item") == '13') { ?>

		journaliser("traitementChangementOnglet() Début");
		
		// Initialiser la zone à identifier si l'onglet 1 est visible
		if (onglet == '1') {
			initialiserZonesAIdentifier();
		}
		
		<?php } ?>
	}
	
	<?php if ($item->get("type_item") == '13') { ?>
	// Traitement particulier pour Zones à identifier

	var initialisationZAIComplete = false;

	function initialiserZonesAIdentifier() {

		if (initialisationZAIComplete == false) {

			journaliser("initialiserZonesAIdentifier() initialisation ZAI");	

			if ( "<?php echo $item->get("image") ?>" != "" && "<?php echo $item->get("image") ?>" != "0") {
			
			    // Redimensionner l'image
				//$("image_media").redimensionnerZoneIdentifier();
				$('#image_zone').redimensionnerImage(550,1000); 
				
		    	// Obtenir les positions X / Y de départ de l'image (terrain de jeu pour les zones)
		    	var imgCoord = $("#image_zone").offset();
		    	var imgLeft = imgCoord.left;
		    	var imgRight = $("#image_zone").width();
		    	var imgTop = imgCoord.top;
		    	var imgBottom = $("#image_zone").height();
		    	journaliser("initialiserZonesAIdentifier() image_zone  imgLeft : " + imgLeft + " imgTrop : '" + imgTop + "'\n" ); 
	
			    // Lors du chargement de la page, positionner les zones
				<?php for ($i = 1; $i < NB_MAX_CHOIX_REPONSES; $i++) { 
					if ($item->get("reponse_" . $i . "_statut") == "1" ) {	?>
	
					idElem = "#item_reponse_<?php echo $i ?>";
					posX = Math.round(imgLeft + <?php echo (int)$item->get("reponse_" . $i . "_coordonnee_x") ?>);
					posY = Math.round(imgTop + <?php echo (int)$item->get("reponse_" . $i . "_coordonnee_y") ?>);
					
					journaliser("initialiserZonesAIdentifier() Ajouter une zone à identifier : " + idElem + " X : '" + posX + "' Y : '" + posY + "'\n" );
					$(idElem).offset({ top: posY, left: posX });
	
					// Activer le clic sur chaque zone
					$(idElem).click(function() {
						idZoneCourante = "#" + $(this).attr('id');
						journaliser("initialiserZonesAIdentifier() ZoneCourante : '" + idZoneCourante + "'\n");
					});

					// Détection des modifications via les coordonnées
					idElemCoordX = "#item_reponse_<?php echo $i ?>_coordonnee_x";
					idElemCoordY = "#item_reponse_<?php echo $i ?>_coordonnee_y";

					// Écouter pour un changement de position horizontal (x)
					$(idElemCoordX).bind("change paste", function(event) {

						// Obtenir l'id
						idEvt = event.target.id;
						idEvt = "#" + idEvt.replace("_coordonnee_x","");
						
						// Obtenir la nouvelle position
						coordX = parseInt($(this).val());

						// Vérifier les extrêmes
						if (coordX < 0)  {
							coordX = 0;
						}

						if (coordX > (imgRight - 17) )  {
							coordX = imgRight - 17;
						}
						
						posX = imgLeft + coordX;

						// Déplacer la zone
						$(idEvt).offset({ left: posX }); 
					});

					// Écouter pour un changement de position vertical (y)
					$(idElemCoordY).bind("change paste", function(event) {

						// Obtenir l'id
						idEvt = event.target.id;
						idEvt = "#" + idEvt.replace("_coordonnee_y","");
						
						// Obtenir la nouvelle position
						coordY = parseInt($(this).val());

						// Vérifier les extrêmes
						if (coordY < 0)  {
							coordY = 0;
						}

						if (coordY > (imgBottom - 17) )  {
							coordY = imgBottom - 17;
						}
																		
						posY = imgTop + coordY;

						// Déplacer la zone
						$(idEvt).offset({ top: posY }); 
					});	
					
				<?php }
					
				}?>
			}

			// Resize panels
			resizePanels();
			
			initialisationZAIComplete = true;
		} else {
			journaliser("initialiserZonesAIdentifier() Ne pas effectuer l'initialisation");
		}
	}	
	
	<?php } ?>
	
	// Démarrage sans attendre les images
	$(document).ready(function() {

		// Resize des fenêtres inital pour affichage plus rapide de l'interface
		resizePanels();

		// Sélectionner l'onglet actif
		selectionnerOnglet("<?php echo $onglet ?>");

		// Activer le clic sur l'étoile pour le suivi
		$("#icone-etoile").click(function() {
			// RODO RD FIX
			envoiSuiviItem('bibliotheque.php', 'item_suivi','', '<?php echo $item->get("id_item") ?>');
		});

		// Afficher aperçu au besoin
		afficherApercu('<?php echo $item->get("apercu")?>');

		// Ouvrir le panneau des messages au besoin
		ouvrirPanneauMessages('<?php echo $item->get("ouvrirPanneauMessages") ?>');		

		// Activer le flag des modifications au besoin
		activerSuiviModifications('<?php echo $pageInfos["flagModifications"] ?>');		


		<?php if ($item->get("type_item") == '3') { ?>
			// Traitement particulier pour Classement

			// Fermer les cadres d'éditions
			$('.cadreEditeur').hide();
			
			// Afficher la section "contenu" si : 
			// 1. Cette section est sélectionné
			// 2. Il y a des éléments dans classeurs
			if ('<?php echo $section ?>' == 'contenu' || ('<?php echo $section ?>' == '' && '<?php echo $item->get("classeur_1_statut") ?>' == "1")   ) {
				afficherSection('contenu');
				fermerSection('classeurs');

				elementEditeur = '<?php echo $elementEditeur ?>';
				
				if ( elementEditeur != "") {

					idElementEditeur = "#editeur_" + elementEditeur;	

					// Afficher l'éditeur
					afficherEditeurElement(elementEditeur);

					// Déplacer la page à l'éditeur
					$(document).scrollTop( $(idElementEditeur).offset().top );
					
				} else {
					// Déplacer la page pour édition du contenu
			    	$(document).scrollTop( $("#contenuOuvert").offset().top );
				}  

			} else {
				afficherSection('classeurs');
				fermerSection('contenu');

				// Déplacer la page pour édition du contenu
			    $(document).scrollTop( $("#classeursOuvert").offset().top );  
			}

			// Désactiver certaines fonctions selon le type d'éléments
			$("#changerTypeClasseur<?php echo $item->getTypeElements1Txt() . $item->getTypeElements2Txt()?>").addClass("inactif");
				
		<?php } ?>

		
		<?php if ($item->get("type_item") == '7') { ?>
			// Traitement particulier pour Marquage
			MARQUAGE_COULEUR_DEFAUT = '<?php echo $item->get("couleur_1_couleur") ?>';
			
			// Masquer le panneau pour l'édition des marques
			$('.cadre').hide();

			// Afficher la section texte si : 
			// 1. Cette section est sélectionné
			// 2. Aucune section sélectionnée mais du texte est présent
			
			if ('<?php echo $item->get("doublons_couleurs") ?>' == '1') {

				// Erreur doublons dans les couleurs - ouvrir la fenêtre des couleurs
				afficherSection('couleurs');
				fermerSection('texte');

				// Afficher un message d'erreur
				<?php 
				if ($item->get("doublons_couleurs") == 1) { 
					$messages = new Messages(ERR_151, Messages::ERREUR);
				} 
				?>
				
			} else if ( ('<?php echo $section ?>' == 'texte') || ('<?php echo $section ?>' == '' && '<?php echo $item->getJS("solution")?>' != "") ) {
				afficherSection('texte');
				fermerSection('couleurs');
			} else {
				afficherSection('couleurs');
				fermerSection('texte');
			}

			// Définir les chaînes de rétro
			TXT_RETRO_POSITIVE = "<?php echo TXT_INSCRIRE_VOTRE_RETROACTION_POSITIVE ?>";
			TXT_RETRO_NEGATIVE = "<?php echo TXT_INSCRIRE_VOTRE_RETROACTION_NEGATIVE?>";

			// Charger les rétros
			<?php	$listeRetros = $item->getListeRetros();

					foreach ($listeRetros as $key => $contenu) { 

						// Obtenir l'id de la marque et l'id de la retro
						preg_match('/(marque_\d+?)_(retro_.+)/', $key, $matches);
						$idMarque = $matches[1];
						$idRetro = "#" . $matches[2];
			?>
			
						idMarque = '<?php echo $idMarque ?>';
						idRetro = '<?php echo $idRetro ?>';
						contenu = '<?php echo addslashes($contenu) ?>';
						journaliser("enregistrerMarqueRetro() Sauvegarde en mémoire idMarque : '" + idMarque + "' idRetro : '" + idRetro + "' Contenu : '" + contenu + "'");
						if (retroMarques[idMarque] == undefined) {
							retroMarques[idMarque] = new Array();
						}
						retroMarques[idMarque][idRetro] = contenu;
						
			<?php	}	
			}
			?>

		<?php if ($item->get("type_item") == '11') { ?>
			// Traitement particulier pour Texte lacunaire
	
			// Désactiver certaines fonctions selon le type de lacune
			$("#changerTypeLacune_<?php echo $item->get("type_lacune") ?>").addClass("inactif");

			// Cacher tous les éditeurs
			$('.cadreEditeur').hide();
			
			// Afficher l'éditeur pour la lacune active
			afficherEditeurLacune('<?php echo $idLacune ?>');
			
			// Définir les chaînes de rétro
			TXT_LACUNE = "<?php echo TXT_LACUNE ?>";
		
		<?php } ?>

	}); 


	// Démarrage avec les images
	$(window).load(function() {
		
		<?php if ($item->get("type_item") == '13') { ?>
			// Traitement particulier pour Zones à identifier
			
			// Initialiser la zone à identifier si l'onglet 1 est visible
			if ('<?php echo $onglet ?>' == '1' || '<?php echo $onglet ?>' == '') {
				initialiserZonesAIdentifier();
			}			
			
			// Activer les flèches pour le déplacement des zones
			$(document).keydown(function(e) {
				journaliser("Key down, zoneCourante : '" + idZoneCourante + "'\n");
			    switch (e.which) {
			    case 37:
			        $(idZoneCourante).stop().animate({
			        	// Gauche
			            left: '-=1'
			        }, 200, function() {
					 	// Recalculer la position de la zone
					 	calculerPositionZone(idZoneCourante);
			        }); 
			        break;
			    case 38:
			        $(idZoneCourante).stop().animate({
				        // Haut
			            top: '-=1'
			        }, 200, function() {
					 	// Recalculer la position de la zone
					 	calculerPositionZone(idZoneCourante);
			        }); 
			        break;
			    case 39:
			        $(idZoneCourante).stop().animate({
				        // Droite
			            left: '+=1'
			        }, 200, function() {
					 	// Recalculer la position de la zone
					 	calculerPositionZone(idZoneCourante);
			        }); 
			        break;
			    case 40:
			        $(idZoneCourante).stop().animate({
				        // Bas
			            top: '+=1'
			        }, 200, function() {
					 	// Recalculer la position de la zone
					 	calculerPositionZone(idZoneCourante);
			        }); 
			        break;
			    }

			})			    
			
			// Désactiver le scroll de la page avec les flèches
			var ar=new Array(33,34,35,36,37,38,39,40);
			$(document).keydown(function(e) {
			     var key = e.which;
			      if($.inArray(key,ar) > -1) {
			          e.preventDefault();
			          return false;
			      }
			      return true;
			});
			
			// Traiter les déplacements des zones
		    $('.zonesZone').draggable({
		        scroll:true,
		        revert: false,
		        containment: $('.containerDraggable'),
		        
		        // Find position where image is dropped.
			    stop: function(event, ui) {

					// Obtenir l'id
					var idElem = $(this).attr('id');

					// Obtenir les positions X / Y de départ de l'image (terrain de jeu pour les zones)
			    	var imgCoord = $("#image_zone").offset();
			    	var imgLeft = imgCoord.left;
			    	var imgTop = imgCoord.top;
			    	journaliser("image_zone  imgLeft : " + imgLeft + " imgTrop : '" + imgTop + "'\n" ); 
			    	
			    	// Obtenir la position
			    	var posCoord = $(this).offset();
			    	var posLeft = posCoord.left;
			    	var posTop = posCoord.top;

			    	// Obtenir le scroll
			    	//var scroll = $(window).scrollTop();
			    	//console.log("scroll : " + scroll);
			    	
			    	// Position calculée
			    	var posX = Math.round(posLeft - imgLeft);
			    	var posY = Math.round(posTop - imgTop);
			    	//var posY = Math.round(posTop - imgTop + scroll);
			    	
			    	journaliser("IMG: \nLeft: "+ imgLeft + "\nTop: " + imgTop + "\nPOS: \nLeft: "+ posLeft + "\nTop: " + posTop + "\n");
			    	journaliser("CALC: \nX: "+ posX + "\nY: " + posY);
			    	journaliser("ID : '" + idElem + "'\n");

			    	// Assigner les valeurs
			    	var idElemX = "#" + idElem + "_coordonnee_x";
			    	var idElemY = "#" + idElem + "_coordonnee_y";
			    	journaliser("elem x : '" + idElemX + "'\n");
			    	journaliser("elem y : '" + idElemY + "'\n");
			    	$(idElemX).val(posX);
			    	$(idElemY).val(posY);

			    	// Conserver en mémoire le dernier id
			    	idZoneCourante = "#" + idElem;
			    }			        
		        
		    });

			// En cas de redimension de la fenêtre revoir l'image
		    $(window).bind("resize", function() {
		        $('#image_zone').redimensionnerImage(550,1000);
		    });
			
		    var currentParent;
		    
		    $('#image_zone, .listeZones').droppable({
		        accept:'.zonesZone',
		        drop: function(event,ui){

	                $.ui.ddmanager.current.cancelHelperRemoval = true;

	                journaliser("Évaluer");
		        }
	        
		    });
			    			
		<?php } ?>
			
	}); 

	
</script>
	
</head>

<body id="bBItems" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-items.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
								<div class="filAriane"><h2><img src="../images/ic-items.png" alt="<?php echo TXT_MES_ITEMS ?>" /><a href="bibliotheque.php"><?php echo TXT_MES_ITEMS ?></a><span class="sep">&gt;</span><?php echo $item->get("titre") ?>  <span class="id">(I<?php echo $item->get("id_item")?>)</span></h2></div>
								
								<form id="frm" name="frm" action="bibliotheque.php" method="post">
								<input type="hidden" name="demande" value="item_sauvegarder" />
								<input type="hidden" name="item_id_item" value="<?php echo $item->get("id_item") ?>" />
								<input type="hidden" name="item_type_item" value="<?php echo $item->get("type_item") ?>" />
								<input type="hidden" name="item_type_elements1_orig" value="<?php echo $item->get("type_elements1") ?>" />
								<input type="hidden" name="item_type_elements1" value="<?php echo $item->get("type_elements1") ?>" />
								<input type="hidden" name="item_type_elements2_orig" value="<?php echo $item->get("type_elements2") ?>" />
								<input type="hidden" name="item_type_elements2" value="<?php echo $item->get("type_elements2") ?>" />
								<input type="hidden" name="item_type_lacune" value="<?php echo $item->get("type_elements1") ?>" />
								<input type="hidden" name="item_type_lacune_orig" value="<?php echo $item->get("type_elements1") ?>" />														
								<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
								<input type="hidden" name="pagination_page_dest" value="" />
								<input type="hidden" name="onglet" value="<?php echo $onglet ?>" />
								<input type="hidden" name="section" value="<?php echo $section ?>" />
								<input type="hidden" name="element" value="" />
								<input type="hidden" name="couleur" value="" />
								<input type="hidden" name="classeur" value="" />
								<input type="hidden" name="lacune" value="" />
								<input type="hidden" name="lacune_texte" value="" />
								<input type="hidden" name="elementEditeur" value="" />
								<input type="hidden" name="flagModifications" value="" />
								<input type="hidden" name="vider_panneau_messages" value="<?php if ($item->get('ouvrirPanneauMessages') == '1') { echo '0'; } else { echo '1'; }?>" />
								<input type="hidden" name="questionnaire_dest" value="" />
								<input type="hidden" name="items_selection_1" value="<?php echo $item->get("id_item") ?>" />
								<input type="hidden" name="verrou_id_projet" value="<?php echo $projetActif->get("id_projet")?>" />
								<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_ITEM . $item->get("id_item") ?>" />								
								<input type="hidden" name="verrou_id_element2" value="" />
	
								<div class="onglets">
									<div id="onglet1" class="ongletActif"><div><a href="#" onclick="changerOnglet('1');traitementChangementOnglet('1');rafraichirEditeurs();return false;"><?php echo TXT_CONTENU ?></a></div></div>
									<div id="onglet2" class="ongletInactif"><div><a href="#" onclick="changerOnglet('2');traitementChangementOnglet('2');rafraichirEditeurs();return false;"><?php echo TXT_COMPLEMENTS ?></a></div></div>
									<div id="onglet3" class="ongletInactif"><div><a href="#" onclick="changerOnglet('3');traitementChangementOnglet('3');return false;"><?php echo TXT_PARAMETRES ?></a></div></div>
									<div class="tools menuContexteGa itemTools">
										<img src="../images/ic-tools.png" alt="" />
										<?php include '../ressources/includes/menu-contexte-items-biblio.php' ?>
									</div>
								</div>
								
								<?php
									$includeItem = '../ressources/includes/item-' . constant('ITEM_' . $item->get("type_item")) . '.php';
									include $includeItem;
								 ?>
								 							 
								</form>
								
							 </div> <!-- /zoneContenuPrincipal -->
						</div> <!-- /contenu -->
					</div> <!-- /zoneContenu -->
				</div> <!-- /colD -->
			</div> <!-- /jqxSplitter -->
		</div> <!-- /corps -->
	
		<?php include '../ressources/includes/piedpage.php' ?>
	</div> <!-- /bodyContenu -->
	<a class="fenetreEditeurMedia" href="media.php?demande=media_selectionner&mode=fenetre&filtre_type_media=image"></a>
	
	<!--  Lien pour fenêtre jaillissante servant à l'ajout d'items dans un questionnaire -->
	<a class="fenetreSelQuest" href="questionnaires.php?demande=questionnaire_selectionner"></a>
	
</body>
</html>