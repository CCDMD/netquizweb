<h1><?php echo TXT_PREFIX_LANGUE . $element->get("id_langue")?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo TXT_LANGUE ?> - <?php echo $element->get("titre") ?></h1>

<p><span class="champTitre"><?php echo TXT_DATE_DE_CREATION ?></span>
	<span class="champValeur"><?php echo $element->getImpression("date_creation",1) ?></span>
	<span class="champTitre padGa25"><?php echo TXT_DATE_DE_MODIFICATION ?></span>
	<span class="champValeur"><?php echo $element->getImpression("date_modification",1) ?></span></p>


<p><span class="champTitre"><?php echo TXT_DELIMITEUR_DE_NOMBRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("delimiteur",1) ?></span></p>

<h3><?php echo TXT_BOUTONS ?></h3>
<p><span class="champTitre"><?php echo TXT_ANNULER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("boutons_annuler", 1) ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_OK ?></span>
	<span class="champValeur"><?php echo $element->getImpression("boutons_ok", 1) ?></span></p>


<h3><?php echo TXT_CONSIGNES ?></h3>
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_ASSOCIATION ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_association") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_CHOIX_MULTIPLES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_choixmultiples") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_CLASSEMENT ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_classement") ?></span></p>

<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_DAMIER_CASES_MASQUEES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_damier_masquees") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_DAMIER_CASES_NON_MASQUEES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_damier_nonmasquees") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_DEVELOPPEMENT ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_developpement") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_DICTEE_CONSIGNE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_dictee_debut") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_DICTEE_MAJUSCULES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_dictee_majuscules") ?></span></p>
												
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_DICTEE_PONCTUATION ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_dictee_ponctuation") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_MARQUAGE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_marquage") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_MISE_EN_ORDRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_ordre") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_REPONSE_BREVE_CONSIGNE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_reponsebreve_debut") ?></span></p>

<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_REPONSE_BREVE_MAJUSCULES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_reponsebreve_majuscules") ?></span></p>

<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_REPONSE_BREVE_PONCTUATION ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_reponsebreve_ponctuation") ?></span></p>

<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_REPONSES_MULTIPLES_UNE_BONNE_REPONSE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_reponsesmultiples_unereponse") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_REPONSES_MULTIPLES_TOUTES_BONNES_REPONSES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_reponsesmultiples_toutes") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_MENU ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_lacunaire_menu") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_GLISSER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_lacunaire_glisser") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_CONSIGNE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_lacunaire_reponsebreve_debut") ?></span></p>

<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_MAJUSCULES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_lacunaire_reponsebreve_majuscules") ?></span></p>

<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_TEXTE_LACUNAIRE_PONCTUATION ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_lacunaire_reponsebreve_ponctuation") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_QUESTION_TYPE_VRAI_OU_FAUX ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_vraifaux") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_ZONES_A_IDENTIFIER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("consignes_zones") ?></span></p>
	

<h3><?php echo TXT_FENETRES ?></h3>
<p><span class="champTitre"><?php echo TXT_RENSEIGNEMENT_SUR_LE_REPONDANT ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_renseignements", 1) ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_NOM ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_nom", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_PRENOM ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_prenom", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_MATRICULE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_matricule", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_GROUPE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_groupe", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_COURRIEL ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_courriel", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_AUTRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_autre", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_ENVOI_DES_RESULTATS_PAR_COURRIEL ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_envoi", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_COURRIEL_DU_DESTINATAIRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fenetre_courriel_destinataire", 1) ?></span></p>
	

<h3><?php echo TXT_FONCTIONNALITES ?></h3>

<p><span class="champTitre"><?php echo TXT_COMMENCER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_commencer", 1) ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_EFFACER_LES_MARQUES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_effacer", 1) ?></span></p>												

<p><span class="champTitre"><?php echo TXT_ENVOYER_PAR_COURRIEL ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_courriel", 1) ?></span></p>												

<p><span class="champTitre"><?php echo TXT_IMPRIMER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_imprimer", 1) ?></span></p>												

<p><span class="champTitre"><?php echo TXT_RECOMMENCER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_recommencer", 1) ?></span></p>												

<p><span class="champTitre"><?php echo TXT_REPRENDRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_reprendre", 1) ?></span></p>												

<p><span class="champTitre"><?php echo TXT_RESULTATS ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_resultats", 1) ?></span></p>												

<p><span class="champTitre"><?php echo TXT_SOLUTION ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_solution", 1) ?></span></p>												

<p><span class="champTitre"><?php echo TXT_VALIDER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("fonctionnalites_valider", 1) ?></span></p>												


<h3><?php echo TXT_MENU_DE_NAVIGATION ?></h3>

<p><span class="champTitre"><?php echo TXT_PAGE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("navigation_page", 1) ?></span></p>												

<p><span class="champTitre"><?php echo TXT_DE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("navigation_de", 1) ?></span></p>												


<h3><?php echo TXT_MESSAGE_GENERAUX ?></h3>

<p><span class="champTitre"><?php echo TXT_MESSAGE_POUR_BONNE_REPONSE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("message_bonnereponse",1) ?></span></p>

<p><span><?php echo TXT_MEDIA ?>
	<?php if ($element->get("media_bonnereponse") == 0) { 
		echo IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . IMPRESSION_HTML_AUCUNE_VALEUR . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE;  
	} else { ?>
		<?php echo $element->get("media_bonnereponse_txt") ?>
	<?php }?>
	</span>
</p>

<p><span class="champTitre"><?php echo TXT_MESSAGE_POUR_MAUVAISE_REPONSE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("message_mauvaisereponse",1) ?></span></p>
	
<p><span><?php echo TXT_MEDIA ?>
	<?php if ($element->get("media_mauvaisereponse") == 0) { 
		echo IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . IMPRESSION_HTML_AUCUNE_VALEUR . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE;  
	} else { ?>
		<?php echo IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . $element->get("media_mauvaisereponse_txt") . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE ?>
	<?php }?>
	</span>
</p>
	
<p><span class="champTitre"><?php echo TXT_MESSAGE_POUR_REPONSE_INCOMPLETE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("message_reponseincomplete",1) ?></span></p>

<p><span><?php echo TXT_MEDIA ?>
	<?php if ($element->get("media_reponseincomplete") == 0) { 
		echo IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . IMPRESSION_HTML_AUCUNE_VALEUR . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE;  
	} else { ?>
		<?php echo IMPRESSION_HTML_PREFIX_VALEUR_UNE_LIGNE . $element->get("media_reponseincomplete_txt") . IMPRESSION_HTML_SUFFIXE_VALEUR_UNE_LIGNE ?>
	<?php }?>
	</span>
</p>


<h3><?php echo TXT_MESSAGES_PARTICULIERS ?></h3>

<p><span class="champTitre"><?php echo TXT_MESSAGE_POUR_MOTS_EN_TROP ?></span>
	<span class="champValeur"><?php echo $element->getImpression("message_dictee_motsentrop") ?></span></p>

<p><span class="champTitre"><?php echo TXT_MESSAGE_POUR_MOTS_MAL_ORTHOGRAPHIES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("message_dictee_orthographe") ?></span></p>
	
<p><span class="champTitre"><?php echo TXT_MESSAGE_POUR_MOTS_MANQUANTS ?></span>
	<span class="champValeur"><?php echo $element->getImpression("message_dictee_motsmanquants") ?></span></p>

<p><span class="champTitre"><?php echo TXT_MESSAGE_POUR_REPONSE_SUGGEREE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("message_reponsesuggeree") ?></span></p>
	

<h3><?php echo TXT_TABLEAU_DE_RESULTATS ?></h3>

<p><span class="champTitre"><?php echo TXT_A_FAIRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_afaire", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_A_REPRENDRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_areprendre", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_MESSAGE_DE_CONFIRMATION_REPRISE_QUESTIONNAIRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_confirmation") ?></span></p>

<p><span class="champTitre"><?php echo TXT_NOMBRE_ESSAIS ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_nbessais", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_POINTS ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_points", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_REUSSI ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_reussi", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_SANS_OBJET ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_sansobjet", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_STATUT ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_statut", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_TEMPS_DE_REPONSE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("resultats_tempsdereponse", 1) ?></span></p>


<h3><?php echo TXT_TYPES_ITEMS ?></h3>

<p><span class="champTitre"><?php echo TXT_ASSOCIATIONS ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_association", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_CHOIX_MULTIPLES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_choixmultiples", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_CLASSEMENT ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_classement", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_DAMIER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_damier", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_DEVELOPPEMENT ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_developpement", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_DICTEE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_dictee", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_MARQUAGE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_marquage", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_MISE_EN_ORDRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_miseenordre", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_REPONSE_BREVE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_reponsebreve", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_REPONSES_MULTIPLES ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_reponsesmultiples", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_TEXTE_LACUNAIRE ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_textelacunaire", 1) ?></span></p>

<p><span class="champTitre"><?php echo TXT_ZONES_A_IDENTIFIER ?></span>
	<span class="champValeur"><?php echo $element->getImpression("item_zonesaidentifier", 1) ?></span></p>

<h3><?php echo TXT_REMARQUE ?>

	<br /><span class="champValeur" style="font-weight:normal; "><?php echo $element->getImpression("remarque")?></span></h3>


