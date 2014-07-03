<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_LANGUES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript">
	
	function changerPage(page) {

		// Vérifier si on peut changer la page
		pageCour = "<?php echo $langue->get("page_courante") ?>";

		if (page != pageCour) {
			document.frm.pagination_page_dest.value=page;
			document.frm.demande.value="langue_sauvegarder";
			document.frm.submit();
		}
	}

	// Annuler
	function annuler() {

		desactiverSuiviModifications();
		
		if (confirm(TXT_AVERTISSEMENT_ANNULER)) {

			// Obtenir l'URL
			url = "bibliotheque.php?demande=langue_modifier&langue_id_langue=<?php echo $langue->get("id_langue") ?>";

			// Rediriger vers la page
			document.location.href = url;
			
		} else {
			activerSuiviModifications(1);
		}
	}	
	
	// Démarrage
	$(document).ready(function() {

		// Resize des fenêtres inital pour affichage plus rapide de l'interface
		resizePanels();

	}); 
	
	</script>	
	
</head>

<body id="bBLangues" onload="resizePanels();">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete.php' ?>
		
		<div id="corps">

			<div id="jqxSplitter">
				<div id="colG">
					<?php include '../ressources/includes/ss-menu-biblio1.php' ?>
				</div>  <!-- /colG -->
				
				<div id="colD">
					<div id="zoneContenu">
						<?php include '../ressources/includes/barre-nav-langues.php' ?>
						<div id="contenu">
							<div id="contenuPrincipal">
								<form id="frm" name="frm" action="bibliotheque.php" method="post">
								<input type="hidden" name="demande" value="langue_sauvegarder" />
								<input type="hidden" name="langue_id_langue" value="<?php echo $langue->get("id_langue") ?>" />
								<input type="hidden" name="pagination_page" value="<?php echo $pagination->getPageCour()?>" />
								<input type="hidden" name="pagination_page_dest" value="" />
								
								<input type="hidden" name="verrou_id_projet" value="<?php echo $projetActif->get("id_projet")?>" />
								<input type="hidden" name="verrou_id_element1" value="<?php echo TXT_PREFIX_LANGUE . $langue->get("id_langue") ?>" />
								<input type="hidden" name="verrou_id_element2" value="" />
								
								
									<div class="filAriane"><h2><img src="../images/ic-langues.png" alt="<?php echo TXT_MES_LANGUES ?>" /><a href="bibliotheque.php?demande=langue_liste"><?php echo TXT_MES_LANGUES ?></a><span class="sep">&gt;</span><?php echo $langue->get("titre")?> <span class="id">(<?php echo TXT_PREFIX_LANGUE . $langue->get("id_langue")?>)</span></h2></div>
									<div class="flDr menuContexteGa">
										<img src="../images/ic-tools.png" alt="" />
										<?php include '../ressources/includes/menu-contexte-langues.php' ?>
									</div>
	
									<div class="detail">
										<div class="detailTop"><div>
											<input class="btnReset" name="btnReset" id="btnReset1" onclick="annuler()" type="button" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_ENREGISTRER ?>" /></div>
										</div>
	
										<div id="section1" class="detailContenant">
											<div class="detailContenu">
	
												<!--  Messages -->
												<?php include '../ressources/includes/message_onglet1.php' ?>
												<!--  /Messages -->
	
												<div class="flDr">
													<p><label for="langue_delimiteur"><?php echo TXT_DELIMITEUR_DE_NOMBRE ?></label>
														<select id="langue_delimiteur" name="langue_delimiteur" class="w150">
															<option value="0" <?php echo $langue->get("delimiteur_0") ?>><?php echo TXT_VIRGULE ?></option>
															<option value="1" <?php echo $langue->get("delimiteur_1") ?>><?php echo TXT_DELIMITEUR_POINT ?></option>
														</select></p>
												</div>
												
												<p><label for="langue_titre"><?php echo TXT_TITRE_DE_LA_LANGUE ?></label>
													<input class="w250 suiviModif" type="text" id="langue_titre" name="langue_titre" value="<?php echo $langue->get("titre") ?>" /></p>
												
												<hr />
												
												<p><label><?php echo TXT_BOUTONS ?></label></p>
												<p><label class="niv2" for="langue_boutons_annuler"><?php echo TXT_ANNULER ?></label>
													<input class="w250 suiviModif" type="text" id="langue_boutons_annuler" name="langue_boutons_annuler" value="<?php echo $langue->get("boutons_annuler") ?>"  placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>" /></p>
												<p><label class="niv2" for="langue_boutons_ok"><?php echo TXT_OK ?></label>
													<input class="w250 suiviModif" type="text" id="langue_boutons_ok" name="langue_boutons_ok" value="<?php echo $langue->get("boutons_ok") ?>"  placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>" /></p>
	
												<hr />
												
												<p><label><?php echo TXT_CONSIGNES ?></label></p>
												<p><label class="niv2" for="langue_consignes_association"><?php echo TXT_QUESTION_TYPE_ASSOCIATION ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_association" name="langue_consignes_association" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_association") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_choixmultiples"><?php echo TXT_QUESTION_TYPE_CHOIX_MULTIPLES ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_choixmultiples" name="langue_consignes_choixmultiples" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_choixmultiples") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_classement"><?php echo TXT_QUESTION_TYPE_CLASSEMENT ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_classement" name="langue_consignes_classement" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_classement") ?></textarea></p>
	
												<p><label class="niv2" for="langue_consignes_damier_masquees"><?php echo TXT_QUESTION_TYPE_DAMIER_CASES_MASQUEES ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_damier_masquees" name="langue_consignes_damier_masquees" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_damier_masquees") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_damier_nonmasquees"><?php echo TXT_QUESTION_TYPE_DAMIER_CASES_NON_MASQUEES ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_damier_nonmasquees" name="langue_consignes_damier_nonmasquees" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_damier_nonmasquees") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_developpement"><?php echo TXT_QUESTION_TYPE_DEVELOPPEMENT ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_developpement" name="langue_consignes_developpement" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_developpement") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_dictee_debut"><?php echo TXT_QUESTION_TYPE_DICTEE_CONSIGNE ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_dictee_debut" name="langue_consignes_dictee_debut" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_dictee_debut") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_dictee_majuscules"><?php echo TXT_QUESTION_TYPE_DICTEE_MAJUSCULES ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_dictee_majuscules" name="langue_consignes_dictee_majuscules" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_dictee_majuscules") ?></textarea></p>
																								
												<p><label class="niv2" for="langue_consignes_dictee_ponctuation"><?php echo TXT_QUESTION_TYPE_DICTEE_PONCTUATION ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_dictee_ponctuation" name="langue_consignes_dictee_ponctuation" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_dictee_ponctuation") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_marquage"><?php echo TXT_QUESTION_TYPE_MARQUAGE ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_marquage" name="langue_consignes_marquage" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_marquage") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_ordre"><?php echo TXT_QUESTION_TYPE_MISE_EN_ORDRE ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_ordre" name="langue_consignes_ordre" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_ordre") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_reponsebreve_debut"><?php echo TXT_QUESTION_TYPE_REPONSE_BREVE_CONSIGNE ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_reponsebreve_debut" name="langue_consignes_reponsebreve_debut" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_reponsebreve_debut") ?></textarea></p>
	
												<p><label class="niv2" for="langue_consignes_reponsebreve_majuscules"><?php echo TXT_QUESTION_TYPE_REPONSE_BREVE_MAJUSCULES ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_reponsebreve_majuscules" name="langue_consignes_reponsebreve_majuscules" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_reponsebreve_majuscules") ?></textarea></p>
	
												<p><label class="niv2" for="langue_consignes_reponsebreve_ponctuation"><?php echo TXT_QUESTION_TYPE_REPONSE_BREVE_PONCTUATION ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_reponsebreve_ponctuation" name="langue_consignes_reponsebreve_ponctuation" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_reponsebreve_ponctuation") ?></textarea></p>
												
												<p><label class="niv2" for="langue_consignes_reponsesmultiples_unereponse"><?php echo TXT_QUESTION_TYPE_REPONSES_MULTIPLES_UNE_BONNE_REPONSE ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_reponsesmultiples_unereponse" name="langue_consignes_reponsesmultiples_unereponse" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_reponsesmultiples_unereponse") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_reponsesmultiples_toutes"><?php echo TXT_QUESTION_TYPE_REPONSES_MULTIPLES_TOUTES_BONNES_REPONSES ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_reponsesmultiples_toutes" name="langue_consignes_reponsesmultiples_toutes" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_reponsesmultiples_toutes") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_lacunaire_menu"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_MENU ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_lacunaire_menu" name="langue_consignes_lacunaire_menu" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_lacunaire_menu") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_lacunaire_glisser"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_GLISSER ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_lacunaire_glisser" name="langue_consignes_lacunaire_glisser" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_lacunaire_glisser") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_lacunaire_reponsebreve_debut"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_CONSIGNE ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_lacunaire_reponsebreve_debut" name="langue_consignes_lacunaire_reponsebreve_debut" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_lacunaire_reponsebreve_debut") ?></textarea></p>
	
												<p><label class="niv2" for="langue_consignes_lacunaire_reponsebreve_majuscules"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_MAJUSCULES ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_lacunaire_reponsebreve_majuscules" name="langue_consignes_lacunaire_reponsebreve_majuscules" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_lacunaire_reponsebreve_majuscules") ?></textarea></p>
	
												<p><label class="niv2" for="langue_consignes_lacunaire_reponsebreve_ponctuation"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_PONCTUATION ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_lacunaire_reponsebreve_ponctuation" name="langue_consignes_lacunaire_reponsebreve_ponctuation" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_lacunaire_reponsebreve_ponctuation") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_vraifaux"><?php echo TXT_QUESTION_TYPE_VRAI_OU_FAUX ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_vraifaux" name="langue_consignes_vraifaux" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_vraifaux") ?></textarea></p>
													
												<p><label class="niv2" for="langue_consignes_zones"><?php echo TXT_QUESTION_TYPE_ZONES_A_IDENTIFIER ?></label>
													<textarea class="wmax suiviModif" id="langue_consignes_zones" name="langue_consignes_zones" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_CONSIGNE ?>"><?php echo $langue->get("consignes_zones") ?></textarea></p>
													
												<hr />
												
												<p><label><?php echo TXT_FENETRES ?></label></p>
												<p><label class="niv2" for="langue_fenetre_renseignements"><?php echo TXT_RENSEIGNEMENT_SUR_LE_REPONDANT ?></label>
													<textarea class="wmax suiviModif" id="langue_fenetre_renseignements" name="langue_fenetre_renseignements" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_renseignements") ?></textarea></p>
													
												<p><label class="niv2" for="langue_fenetre_nom"><?php echo TXT_NOM ?></label>
													<textarea class="wmax suiviModif" id="langue_fenetre_nom" name="langue_fenetre_nom" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_nom") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fenetre_prenom"><?php echo TXT_PRENOM ?></label>
													<textarea class="wmax suiviModif" id="langue_fenetre_prenom"  name="langue_fenetre_prenom" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_prenom") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fenetre_matricule"><?php echo TXT_MATRICULE ?></label>
													<textarea class="wmax suiviModif" id="langue_fenetre_matricule" name="langue_fenetre_matricule" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_matricule") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fenetre_groupe"><?php echo TXT_GROUPE ?></label>
													<textarea class="wmax suiviModif" id="langue_fenetre_groupe" name="langue_fenetre_groupe" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_groupe") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fenetre_courriel"><?php echo TXT_COURRIEL ?> </label>
													<textarea class="wmax suiviModif" id="langue_fenetre_courriel" name="langue_fenetre_courriel" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_courriel") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fenetre_autre"><?php echo TXT_AUTRE ?></label>
													<textarea class="wmax suiviModif" id="langue_fenetre_autre" name="langue_fenetre_autre" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_autre") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fenetre_envoi"><?php echo TXT_ENVOI_DES_RESULTATS_PAR_COURRIEL ?></label>
													<textarea class="wmax suiviModif" id="langue_fenetre_envoi" name="langue_fenetre_envoi" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_envoi") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fenetre_courriel_destinataire"><?php echo TXT_COURRIEL_DU_DESTINATAIRE ?></label>
													<textarea class="wmax suiviModif" id="langue_fenetre_courriel_destinataire" name="langue_fenetre_courriel_destinataire" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fenetre_courriel_destinataire") ?></textarea></p>
													
												<hr />												
	
												<p><label><?php echo TXT_FONCTIONNALITES ?></label></p>
												
												<p><label class="niv2" for="langue_fonctionnalites_commencer"><?php echo TXT_COMMENCER ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_commencer" name="langue_fonctionnalites_commencer" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_commencer") ?></textarea></p>
													
												<p><label class="niv2" for="langue_fonctionnalites_effacer"><?php echo TXT_EFFACER_LES_MARQUES ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_effacer" name="langue_fonctionnalites_effacer" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_effacer") ?></textarea></p>												
	
												<p><label class="niv2" for="langue_fonctionnalites_courriel"><?php echo TXT_ENVOYER_PAR_COURRIEL ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_courriel" name="langue_fonctionnalites_courriel" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_courriel") ?></textarea></p>												
	
												<p><label class="niv2" for="langue_fonctionnalites_imprimer"><?php echo TXT_IMPRIMER ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_imprimer" name="langue_fonctionnalites_imprimer" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_imprimer") ?></textarea></p>												
	
												<p><label class="niv2" for="langue_fonctionnalites_recommencer"><?php echo TXT_RECOMMENCER ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_recommencer" name="langue_fonctionnalites_recommencer" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_recommencer") ?></textarea></p>												
	
												<p><label class="niv2" for="langue_fonctionnalites_reprendre"><?php echo TXT_REPRENDRE ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_reprendre" name="langue_fonctionnalites_reprendre" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_reprendre") ?></textarea></p>												
	
												<p><label class="niv2" for="langue_fonctionnalites_resultats"><?php echo TXT_RESULTATS ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_resultats" name="langue_fonctionnalites_resultats" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_resultats") ?></textarea></p>												
	
												<p><label class="niv2" for="langue_fonctionnalites_lexique"><?php echo TXT_LEXIQUE ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_lexique" name="langue_fonctionnalites_lexique" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_lexique") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fonctionnalites_questionnaire"><?php echo TXT_QUESTIONNAIRE ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_questionnaire" name="langue_fonctionnalites_questionnaire" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_questionnaire") ?></textarea></p>
	
												<p><label class="niv2" for="langue_fonctionnalites_solution"><?php echo TXT_SOLUTION ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_solution" name="langue_fonctionnalites_solution" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_solution") ?></textarea></p>												
	
												<p><label class="niv2" for="langue_fonctionnalites_valider"><?php echo TXT_VALIDER ?></label>
													<textarea class="wmax suiviModif" id="langue_fonctionnalites_valider" name="langue_fonctionnalites_valider" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("fonctionnalites_valider") ?></textarea></p>												
	
												<hr />												
	
												<p><label><?php echo TXT_MENU_DE_NAVIGATION ?></label></p>
	
												<p><label class="niv2" for="langue_navigation_page"><?php echo TXT_PAGE ?></label>
													<textarea class="wmax suiviModif" id="langue_navigation_page" name="langue_navigation_page" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("navigation_page") ?></textarea></p>												
	
												<p><label class="niv2" for="langue_navigation_de"><?php echo TXT_DE_2 ?><span class="aide"><?php echo TXT_EX_2_DE_30 ?></span></label>
													<textarea class="wmax suiviModif" id="langue_navigation_de" name="langue_navigation_de" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("navigation_de") ?></textarea></p>												
	
												<hr />												
	
												<p><label><?php echo TXT_MESSAGE_GENERAUX ?></label></p>
	
												<p><label class="niv2" for="langue_message_bonnereponse"><?php echo TXT_MESSAGE_POUR_BONNE_REPONSE ?></label>
													<textarea class="wmax suiviModif" id="langue_message_bonnereponse" name="langue_message_bonnereponse" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $langue->get("message_bonnereponse") ?></textarea></p>
	
													
												<!--  Ajouter un média pour bonne réponse -->
												<div>
													<div class="menuContexte displayInline">
														<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_MEDIA?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
														<div class="menuDeroul">
															<ul class="sansTitre">
																<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre" onclick="ouvrirSelectionMediaLien('langue_media_bonnereponse')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																<li><a class="fenetreStd" href="media.php?demande=media_importer" onclick="ouvrirImportMediaLien('langue_media_bonnereponse')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
															</ul>
														</div>
													</div>
													<input type="hidden" id="langue_media_bonnereponse" name="langue_media_bonnereponse" value="<?php echo $langue->get("media_bonnereponse") ?>" />
													<p id="langue_media_bonnereponse_lien">
														<?php if ($langue->get("media_bonnereponse") == 0) { 
															echo TXT_AUCUN_FICHIER_SELECTIONNE;  
														} else { 
															echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
															<a href="media.php?demande=media_presenter&media_id_media=<?php echo $langue->get("media_bonnereponse") ?>" target="media_<?php echo $langue->get("media_bonnereponse") ?>"><?php echo $langue->get("media_bonnereponse_txt") ?></a>
														<?php }?>
														<span id="langue_media_bonnereponse_supp" <?php if ($langue->get("media_bonnereponse") == 0) { ?> style="display: none;" <?php } ?>>
															<a onclick="viderChampMedia('langue_media_bonnereponse','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
													
												</div>
	
	
	
												<p><label class="niv2" for="langue_message_mauvaisereponse"><?php echo TXT_MESSAGE_POUR_MAUVAISE_REPONSE ?></label>
													<textarea class="wmax suiviModif" id="langue_message_mauvaisereponse" name="langue_message_mauvaisereponse" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $langue->get("message_mauvaisereponse") ?></textarea></p>
													
													
												<!--  Ajouter un média pour mauvaise réponse -->
												<div>
													<div class="menuContexte displayInline">
														<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_MEDIA?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
														<div class="menuDeroul">
															<ul class="sansTitre">
																<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre" onclick="ouvrirSelectionMediaLien('langue_media_mauvaisereponse')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																<li><a class="fenetreStd" href="media.php?demande=media_importer" onclick="ouvrirImportMediaLien('langue_media_mauvaisereponse')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
															</ul>
														</div>
													</div>
													<input type="hidden" id="langue_media_mauvaisereponse" name="langue_media_mauvaisereponse" value="<?php echo $langue->get("media_mauvaisereponse") ?>" />
													<p id="langue_media_mauvaisereponse_lien">
														<?php if ($langue->get("media_mauvaisereponse") == 0) { 
															echo TXT_AUCUN_FICHIER_SELECTIONNE;  
														} else { 
															echo TXT_FICHIER_ACTUEL . "&nbsp;:&nbsp;"; ?>
															<a href="media.php?demande=media_presenter&media_id_media=<?php echo $langue->get("media_mauvaisereponse") ?>" target="media_<?php echo $langue->get("media_mauvaisereponse") ?>"><?php echo $langue->get("media_mauvaisereponse_txt") ?></a>
														<?php }?>
														<span id="langue_media_mauvaisereponse_supp" <?php if ($langue->get("media_mauvaisereponse") == 0) { ?> style="display: none;" <?php } ?>>
															<a onclick="viderChampMedia('langue_media_mauvaisereponse','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
													
												</div>												
	
												<p><label class="niv2" for="langue_message_reponseincomplete"><?php echo TXT_MESSAGE_POUR_REPONSE_INCOMPLETE ?></label>
													<textarea class="wmax suiviModif" id="langue_message_reponseincomplete" name="langue_message_reponseincomplete" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $langue->get("message_reponseincomplete") ?></textarea></p>
	
												<!--  Ajouter un média pour réponse incomplète -->
												<div>
													<div>
														<div class="menuContexte displayInline">
															<a class="tools" href="#"><?php echo TXT_AJOUTER_UN_MEDIA?>&nbsp;<img src="../images/ic-tools-2.png" alt="" /></a>
															<div class="menuDeroul">
																<ul class="sansTitre">
																	<li><a class="fenetreStd" href="media.php?demande=media_selectionner&mode=fenetre" onclick="ouvrirSelectionMediaLien('langue_media_reponseincomplete')"><?php echo TXT_CHOISIR_DE_LA_BIBLIOTHEQUE ?></a></li>
																	<li><a class="fenetreStd" href="media.php?demande=media_importer" onclick="ouvrirImportMediaLien('langue_media_reponseincomplete')"><?php echo TXT_IMPORTER_UN_NOUVEAU_FICHIER ?></a></li>
																</ul>
															</div>
														</div>
													</div>
													<input type="hidden" id="langue_media_reponseincomplete" name="langue_media_reponseincomplete" value="<?php echo $langue->get("media_reponseincomplete") ?>" />
													<p id="langue_media_reponseincomplete_lien">
														
															<?php if ($langue->get("media_reponseincomplete") == 0) { 
																echo TXT_AUCUN_FICHIER_SELECTIONNE;  
															} else { 
																echo TXT_FICHIER_ACTUEL; echo "&nbsp;:&nbsp;"; ?>
																<a href="media.php?demande=media_presenter&media_id_media=<?php echo $langue->get("media_reponseincomplete") ?>" target="media_<?php echo $langue->get("media_reponseincomplete") ?>"><?php echo $langue->get("media_reponseincomplete_txt") ?></a>
															<?php }?>
														
														<span id="langue_media_reponseincomplete_supp" <?php if ($langue->get("media_reponseincomplete") == 0) { ?> style="display: none;" <?php } ?>>
															<a onclick="viderChampMedia('langue_media_reponseincomplete','<?php echo TXT_AUCUNE_SELECTION ?>')"><img class="icDelete" src="../images/ic-delete.png" alt="<?php echo TXT_SUPPRIMER ?>" /></a>
														</span>
													</p>
													
												</div>
	
												<p><label class="niv2" for="langue_message_libelle_solution"><?php echo TXT_MESSAGE_LIBELLE_SOLUTION ?></label>
													<textarea class="wmax suiviModif" id="langue_message_libelle_solution" name="langue_message_libelle_solution" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("message_libelle_solution") ?></textarea></p>	
	
												<p><label class="niv2" for="langue_message_point"><?php echo TXT_POINT ?></label>
													<textarea class="wmax suiviModif" id="langue_message_point" name="langue_message_point" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("message_point") ?></textarea></p>	
	
												<p><label class="niv2" for="langue_message_points"><?php echo TXT_POINTS ?></label>
													<textarea class="wmax suiviModif" id="langue_message_points" name="langue_message_points" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("message_points") ?></textarea></p>	
	
												<p><label class="niv2" for="langue_message_sanstitre"><?php echo TXT_SANS_TITRE ?></label>
													<textarea class="wmax suiviModif" id="langue_message_sanstitre" name="langue_message_sanstitre" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("message_sanstitre") ?></textarea></p>	
	
												<hr />
												
												<p><label><?php echo TXT_MESSAGES_PARTICULIERS ?></label></p>
	
												<p><label class="niv2" for="langue_conjonction_et"><?php echo TXT_CONJONCTION_ET ?></label>
													<textarea class="wmax suiviModif" id="langue_conjonction_et" name="langue_conjonction_et" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("conjonction_et") ?></textarea></p>
	
												<p><label class="niv2" for="langue_message_dictee_motsentrop"><?php echo TXT_MESSAGE_POUR_MOTS_EN_TROP ?></label>
													<textarea class="wmax suiviModif" id="langue_message_dictee_motsentrop" name="langue_message_dictee_motsentrop" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("message_dictee_motsentrop") ?></textarea></p>
	
												<p><label class="niv2" for="langue_message_dictee_orthographe"><?php echo TXT_MESSAGE_POUR_MOTS_MAL_ORTHOGRAPHIES ?></label>
													<textarea class="wmax suiviModif" id="langue_message_dictee_orthographe" name="langue_message_dictee_orthographe" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("message_dictee_orthographe") ?></textarea></p>
													
												<p><label class="niv2" for="langue_message_dictee_motsmanquants"><?php echo TXT_MESSAGE_POUR_MOTS_MANQUANTS ?></label>
													<textarea class="wmax suiviModif" id="langue_message_dictee_motsmanquants" name="langue_message_dictee_motsmanquants" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("message_dictee_motsmanquants") ?></textarea></p>
	
												<p><label class="niv2" for="langue_message_reponsesuggeree"><?php echo TXT_MESSAGE_POUR_REPONSE_SUGGEREE ?></label>
													<textarea class="wmax suiviModif" id="langue_message_reponsesuggeree" name="langue_message_reponsesuggeree" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("message_reponsesuggeree") ?></textarea></p>
													
												<hr />
												
												<p><label><?php echo TXT_TABLEAU_DE_RESULTATS ?></label></p>
	
												<p><label class="niv2" for="langue_resultats_afaire"><?php echo TXT_A_FAIRE ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_afaire" name="langue_resultats_afaire" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_afaire") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_areprendre"><?php echo TXT_A_REPRENDRE ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_areprendre" name="langue_resultats_areprendre" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_areprendre") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_confirmation"><?php echo TXT_MESSAGE_DE_CONFIRMATION_REPRISE_QUESTIONNAIRE ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_confirmation" name="langue_resultats_confirmation" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_confirmation") ?></textarea></p>
													
												<p><label class="niv2" for="langue_resultats_accueil"><?php echo TXT_MESSAGE_DE_CONFIRMATION_RETOUR_ACCUEIL ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_accueil" name="langue_resultats_accueil" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_accueil") ?></textarea></p>
													
												<p><label class="niv2" for="langue_resultats_objet_courriel"><?php echo TXT_RESULTATS_OBJET_COURRIEL ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_objet_courriel" name="langue_resultats_objet_courriel" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_objet_courriel") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_message_courriel_erreur"><?php echo TXT_RESULTATS_MESSAGE_COURRIEL_ERREUR ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_message_courriel_erreur" name="langue_resultats_message_courriel_erreur" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_message_courriel_erreur") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_message_courriel_succes"><?php echo TXT_RESULTATS_MESSAGE_COURRIEL_SUCCES ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_message_courriel_succes" name="langue_resultats_message_courriel_succes" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_message_courriel_succes") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_nbessais"><?php echo TXT_NOMBRE_ESSAIS ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_nbessais" name="langue_resultats_nbessais" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_nbessais") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_points"><?php echo TXT_POINTS ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_points" name="langue_resultats_points" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_points") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_reussi"><?php echo TXT_REUSSI ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_reussi" name="langue_resultats_reussi" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_reussi") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_sansobjet"><?php echo TXT_SANS_OBJET ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_sansobjet" name="langue_resultats_sansobjet" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_sansobjet") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_statut"><?php echo TXT_STATUT ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_statut" name="langue_resultats_statut" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_statut") ?></textarea></p>
	
												<p><label class="niv2" for="langue_resultats_tempsdereponse"><?php echo TXT_TEMPS_DE_REPONSE ?></label>
													<textarea class="wmax suiviModif" id="langue_resultats_tempsdereponse" name="langue_resultats_tempsdereponse" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("resultats_tempsdereponse") ?></textarea></p>
	
												<hr />
												
												<p><label><?php echo TXT_TYPES_ITEMS ?></label></p>
	
												<p><label class="niv2" for="langue_item_association"><?php echo TXT_ASSOCIATIONS ?></label>
													<textarea class="wmax suiviModif" id="langue_item_association" name="langue_item_association" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_association") ?></textarea></p>
												
												<p><label class="niv2" for="langue_item_choixmultiples"><?php echo TXT_CHOIX_MULTIPLES ?></label>
													<textarea class="wmax suiviModif" id="langue_item_choixmultiples" name="langue_item_choixmultiples" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_choixmultiples") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_classement"><?php echo TXT_CLASSEMENT ?></label>
													<textarea class="wmax suiviModif" id="langue_item_classement" name="langue_item_classement" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_classement") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_damier"><?php echo TXT_DAMIER ?></label>
													<textarea class="wmax suiviModif" id="langue_item_damier" name="langue_item_damier" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_damier") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_developpement"><?php echo TXT_DEVELOPPEMENT ?></label>
													<textarea class="wmax suiviModif" id="langue_item_developpement" name="langue_item_developpement" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_developpement") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_dictee"><?php echo TXT_DICTEE ?></label>
													<textarea class="wmax suiviModif" id="langue_item_dictee" name="langue_item_dictee" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_dictee") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_marquage"><?php echo TXT_MARQUAGE ?></label>
													<textarea class="wmax suiviModif" id="langue_item_marquage" name="langue_item_marquage" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_marquage") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_miseenordre"><?php echo TXT_MISE_EN_ORDRE ?></label>
													<textarea class="wmax suiviModif" id="langue_item_miseenordre" name="langue_item_miseenordre" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_miseenordre") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_reponsebreve"><?php echo TXT_REPONSE_BREVE ?></label>
													<textarea class="wmax suiviModif" id="langue_item_reponsebreve" name="langue_item_reponsebreve" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_reponsebreve") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_reponsesmultiples"><?php echo TXT_REPONSES_MULTIPLES ?></label>
													<textarea class="wmax suiviModif" id="langue_item_reponsesmultiples" name="langue_item_reponsesmultiples" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_reponsesmultiples") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_textelacunaire"><?php echo TXT_TEXTE_LACUNAIRE ?></label>
													<textarea class="wmax suiviModif" id="langue_item_textelacunaire" name="langue_item_textelacunaire" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_textelacunaire") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_vraioufaux"><?php echo TXT_VRAI_OU_FAUX ?></label>
													<textarea class="wmax suiviModif" id="langue_item_vraioufaux" name="langue_item_vraioufaux" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_vraioufaux") ?></textarea></p>
	
												<p><label class="niv2" for="langue_item_zonesaidentifier"><?php echo TXT_ZONES_A_IDENTIFIER ?></label>
													<textarea class="wmax suiviModif" id="langue_item_zonesaidentifier" name="langue_item_zonesaidentifier" rows="2" cols="200" placeholder="<?php echo TXT_INSCRIRE_VOTRE_LIBELLE ?>"><?php echo $langue->get("item_zonesaidentifier") ?></textarea></p>
												
												<hr />
												
												<p><label for="langue_remarque"><?php echo TXT_REMARQUE ?></label>
													<textarea name="langue_remarque" id="langue_remarque" rows="5" cols="200" class="wmax suiviModif" placeholder="<?php echo TXT_INSCRIRE_VOTRE_TEXTE ?>"><?php echo $langue->get("remarque")?></textarea>
												</p>
												
												
											</div>						
										</div>						
	
										<div class="detailBot"><div>
											<input class="btnReset" name="btnReset" id="btnReset2" onclick="annuler()" type="button" value="<?php echo TXT_ANNULER ?>"  />
											<input class="btnSubmit btnEnregistrer" name="btnSubmit" id="btnSubmit2" type="submit" value="<?php echo TXT_ENREGISTRER ?>" /></div>
										</div>		
									</div>					
								</form>
							</div> <!-- /contenuPrincipal -->
						</div> <!-- /contenu -->
					</div> <!-- /zoneContenu -->
				</div> <!-- /colD -->
			</div> <!-- /jqxSplitter -->
		</div> <!-- /corps -->
	
		<?php include '../ressources/includes/piedpage.php' ?>
	</div> <!-- /bodyContenu -->
</body>
</html>
