/* * 
 * Librairie Javascript : netquiz.js
 * 
 * Fonctions Javascript pour Netquiz Web
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca> 
 * @version 1.0
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */	


var debug = false;
var prefixMedia = prefixMedia;

var ItemsMenu = new Array();

// Flag global pour déterminer si le contenu d'un ou de plusieurs champs ont été modifiés
var flagModifications = false; 

// Cible pour chargement de média
champSelectionMedia = "";

// Action après sélection du média
actionSelectionMedia = "";

// Objet sélectionné actuellement
objSel = "";

// Éditeur actuellement utilisé
edSel = "";

// Nombre total de marque
nbMarques = 0;

// Id Marque ouverte en édition
marqueOuverteDansEditeur = 0;

// Nombre total de lacunes
nbLacunes = 0;

//Id Lacune ouverte en édition
lacuneOuverteDansEditeur = 0;

// Tableau des données pour les marques
retroMarques = new Array();

// Conserver la dernière zone déplacée
var idZoneCourante = null;

// Gestion de la session
var verifierSession;
var statutSession;

// Gestion des verrous
var verrouDernierMessage = "-";

//------------------------------------------------------------------------------
// Fonction 	: journaliser()
// Description	: Envoi du texte à la console si elle est disponible 
//------------------------------------------------------------------------------		
function journaliser(txt) {
	
	if (debug && (typeof console == "object") ) {
		console.log(txt);
	}
}

//------------------------------------------------------------------------------
//Fonction 	: resizePanels()
//Description	: Si la fenêtre est redimensionnée, revoir la taille des panneaux
//------------------------------------------------------------------------------
function resizePanels() {
	
	// Obtenir la taille de la fenêtre
	var windowWidth = $(window).width();
	var windowHeight = $(window).height(); 
	
	// Obtenir les largeurs du splitter et des zones
	var splitterWidth = Math.ceil(windowWidth * .95);
	var menuWidth = Math.floor(splitterWidth * .20);
	var textWidth = Math.floor(splitterWidth * .80);

	splitterWidth += "px";
	
	// Réviser la largeur du splitter
	$('#jqxSplitter').width(splitterWidth);
	$('#jqxSplitter').jqxSplitter({ width: splitterWidth});

	// Hauteur selon la hauteur du contenu
	var hauteurBarreNav = $('#barreNav').height();
	var hauteurContenuPrincipal = $('#contenuPrincipal').height();
	var hauteurDetailBot = $('.detailBot').height();
	var hauteurZoneContenu = hauteurBarreNav + hauteurContenuPrincipal + hauteurDetailBot + 60;
		
	var hauteurMenu = $('#ssMenu2').height()+2;
	var hauteurDetail = $('.detail').height();	
	
	var hauteurMax = 0;
	
	if (hauteurZoneContenu > (hauteurMenu + 130) || hauteurDetail > (hauteurMenu + 130)) {
		hauteurMax = hauteurZoneContenu;
	} else {
		hauteurMax = hauteurMenu + 167;
	}
	
	// Vérifier le minimum
	if (hauteurMax < 600) {
		hauteurMax = 600;
	}
	
	// Régler la hauteur de la zone de contenu
	$('#zoneContenu').height(hauteurMax);
	$('#contenu').height(hauteurMax);
		
	// Calculer la hauteur du splitter
	var splitterHeight = hauteurMax + 2;
	splitterHeight += "px";

	// Régler la hauteur du splitter
	$('#jqxSplitter').height(splitterHeight);
	$('#colG').height(splitterHeight);
	$('#colD').height(splitterHeight);
	$('#jqxSplitter').jqxSplitter({ height: splitterHeight});
}



// ------------------------------------------------------------------------------
// Fonction 	: dump()
// Description	: Obtenir les informations sur le menu
// ------------------------------------------------------------------------------			 
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	// Padding au début de la ligne
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";

	if(typeof(arr) == 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];

			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Strings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}


// ------------------------------------------------------------------------------
// Fonction 	  : changerOnglet()
// Description	  : Changer d'onglet dans la page
// ------------------------------------------------------------------------------
function changerOnglet(id) {
	showSection('onglet','section',id);
	document.frm.onglet.value = id;
}

// ------------------------------------------------------------------------------
// Fonction 	  : selectionnerOnglet()
// Description	  : Selectionner un onglet au chargement de la page
// ------------------------------------------------------------------------------
function selectionnerOnglet(onglet) {
	if ( onglet != "") {
		showSection('onglet','section', onglet);
	}
}	

// ------------------------------------------------------------------------------
// Fonction 	   : document.ready()
// Description	   : Au chargement d'une page activer les fonctions dynamiques
// ------------------------------------------------------------------------------
$(document).ready(function () {
	$("#jqxSplitter").jqxSplitter({ splitBarSize:10, orientation:'horizontal', cookies: true, panels: [{ min: 110, size: 275, resizable: true }, { min: 705, size: 800, resizable: true}] });
	$('#jqxSplitter').bind('resize', function () { resizePanels(); }); 
	$('#jqxSplitter').bind('expanded', function () { resizePanels(); });
	
	// Restreindre le nombre d'évènements lors de la redimension de l'écran
	var doit; 
	$(window).resize(function(){ 
		clearTimeout(doit); 
		doit = setTimeout(function(){resizePanels();}, 100); 
	}); 
	
	// Activer le menu
	$('ul.sortable').nestedSortable({
			disableNesting: 'no-nest',
			forcePlaceholderSize: true,
			handle: 'div',
			helper:	'clone',
			items: "li:not(.ui-state-disabled)",
			listType: 'ul',
			maxLevels: 2,
			update: function(serialized) {
				
				// Obtenir le contenu du menu lorsque des éléments sont déplacés
				arraied = $('ul.sortable').nestedSortable('toArray', {startDepthCount: 0});
				contenuMenu = dump(arraied);
				
				// Obtenir l'id du questionnaire
				idQuest = document.frm.questionnaire_id_questionnaire.value;
				
				// Transmettre en Ajax au serveur
		        $.post("questionnaires.php", 
		    	        { demande: 'menu_modifier', questionnaire_id_questionnaire : idQuest, menu : contenuMenu },  
		    	        function(result){  
			                if(result != 1){
			                	alert("Impossible de transmettre l'information au serveur.  Veuillez essayer de nouveau.\nSi le probl\350me persiste, v\351rifier que votre session est toujours active.");
			                }  
		    	        });  
				
			}, 
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div'
		});
	
	// Détection des modifications aux champs des formulaires identifiés par la classe "suiviModif"
	$('.suiviModif').keyup(function() {
        flagModifications = true;
	});
	$('.suiviModif').change(function() {
        flagModifications = true;
	});
	
	// Détecter la situation ou l'utilisateur quitte sans sauvegarder
	$(window).bind('beforeunload', function() {
	    if (flagModifications) {
	        return "Assurez-vous de sauvegarder vos modifications!"; 
	    } 
	});
	
	// Désactiver le flag de modification si l'utilisateur soumet le formulaire
	$('input:submit').click(function(e) {
	    flagModifications = false;
	});
	
	// Fenêtre jaillissante pour modifier le profil et le mdp
	$(".fenetreProfil").fancybox({
		'width' : 500,
		'height' : 398,
		'autoScale' : false,
		'hideOnContentClick': false,
		'hideOnOverlayClick' : false,
		'transitionIn'	: 'fade',
		'transitionOut'	: 'fade',
		'scrolling' : 'no',
		'padding' : 0,
		'overlayOpacity': 0.2,
		'showCloseButton': false,
		'type' : 'iframe'
		 
 	});			
	
	// Ajuster les panneaux
	resizePanels();
	
	// Ajuster les panneaux pour IE7
	setTimeout(function(){
		resizePanels();
	}, 300);
	

	// Vérifier la session seulement dans la fenêtre principale
	var isInIframe = self != top;
	
	// Vérifier que la session est active
	if (isInIframe == false) {
		verifierSession();
		verifierSession = setInterval(verifierSession, 10000);
	}
	
	// Message d'avertissement sur le bouton annuler
	$('.annuler').click(function(e) {

			// Intervenir si le flag de modifications est activé
			if (flagModifications == true) {
				if (!confirm(TXT_AVERTISSEMENT_ANNULER)) {
					e.preventDefault();
				} else {
					desactiverSuiviModifications();
				}
			}
			
	});
	
});


//------------------------------------------------------------------------------
// Fonction 	   : verifierSession()
// Description	   : Vérifier que la session est active
//------------------------------------------------------------------------------
function verifierSession() {
	
	// Obtenir l'id de l'élément en édition
	verrouIdProjet = "";
	verrouIdElement1 = "";
	verrouIdElement2 = "";
	
	if (typeof document.frm != 'undefined') {
	
		if ((typeof document.frm.verrou_id_projet != 'undefined') && (typeof document.frm.verrou_id_element1 != 'undefined')) {
			verrouIdProjet = document.frm.verrou_id_projet.value;
			verrouIdElement1 = document.frm.verrou_id_element1.value;
		}
		
		if ((typeof document.frm.verrou_id_projet != 'undefined') && (typeof document.frm.verrou_id_element2 != 'undefined')) {
			verrouIdElement2 = document.frm.verrou_id_element2.value;
		}
	}
		
	var str="";
	str += "demande=session_verifier";
	str += "&chksession=true";
	str += "&verrou_id_projet=" + verrouIdProjet;
	str += "&verrou_id_element1=" + verrouIdElement1;
	str += "&verrou_id_element2=" + verrouIdElement2;
	
	jQuery.ajax({
		type: "POST",
		url: "questionnaires.php",
		data: str,
		cache: false,
		success: function(rep){
			
			// Obtenir les paramètres de la réponse
			var reponse = jQuery.parseJSON(rep);
						
			// Traiter les statuts de la session
			if(reponse.sessionActive == "1") {
				
				if (statutSession != "1") {
					journaliser("afficher popup pre timeout");
					$(".fenetreGestionSession").trigger('click');
				}
				statutSession = "1";

			} else if (reponse.sessionActive == "2") {
				
				if (statutSession != "2") {
				
					// Ouvrir la fenêtre 
					$(".fenetreGestionSession").trigger('click');
					
				}
				// Cesser la vérification, c'est trop tard :)
				clearInterval(verifierSession);
				statutSession = "2"
				
			} else {
				journaliser("verifierSession() Validation complétée");
			}
			
			// Vérifier si le message a déjà été envoyé pour cette page sinon l'envoyer et en prendre note
			// Effectuer seulement si la session n'est pas expirée
			if (reponse.verrouListe != verrouDernierMessage && reponse.sessionActive != "2") {
			
				if (verrouDernierMessage != "-" && reponse.verrouListe != "") {
					alert(TXT_AVERTISSEMENT_NOUVEAU_VERROU + " " + reponse.verrouListe );
				}
				verrouDernierMessage = reponse.verrouListe;
			}
			
		}
	});
}

//------------------------------------------------------------------------------
// Fonction 	   : window.load()
// Description	   : Au chargement de la fenêtre redimensionner les panneaux
//------------------------------------------------------------------------------
$(window).load(function() {
	// Resize des fenêtres inital pour affichage plus rapide de l'interface
	resizePanels();
});

//------------------------------------------------------------------------------
// Fonction 	: envoiSuiviItem()
// Description	: Activer ou désactiver le suivi (étoile)
//------------------------------------------------------------------------------
function envoiSuiviItem(aiguilleur, demande, idQuest, idItem){  
  
    // Envoi en AJAX  
    $.post(aiguilleur, 
	        { demande: demande, questionnaire_id_questionnaire : idQuest, item_id_item : idItem },  
        function(result){  
            if(result == 1){
            	$("#icone-etoile").attr("src","../images/ic-star-jaune.png");
            } else{  
            	$("#icone-etoile").attr("src", "../images/ic-star-gris.png");  
            }  
    });  
}

//------------------------------------------------------------------------------
// Fonction 	: envoiSuiviQuestionnaire()
// Description	: Activer ou désactiver le suivi (étoile)
//------------------------------------------------------------------------------
function envoiSuiviQuestionnaire(aiguilleur, demande, idQuest){  

 // Envoi en AJAX  
 $.post(aiguilleur, 
	        { demande: demande, questionnaire_id_questionnaire : idQuest},  
     function(result){  
         if(result == 1){
         	$("#icone-etoile").attr("src","../images/ic-star-jaune.png");
         } else{  
         	$("#icone-etoile").attr("src", "../images/ic-star-gris.png");  
         }  
 });  
}

//------------------------------------------------------------------------------
// Fonction 	: envoiSuiviMedia()
// Description	: Activer ou désactiver le suivi (étoile)
//------------------------------------------------------------------------------
function envoiSuiviMedia(aiguilleur, demande, idMedia){  

 // Envoi en AJAX  
 $.post(aiguilleur, 
	        { demande: demande, media_id_media : idMedia },  
     function(result){  
         if(result == 1){
         	$("#icone-etoile").attr("src","../images/ic-star-jaune.png");
         } else{  
         	$("#icone-etoile").attr("src", "../images/ic-star-gris.png");  
         }  
 });  
}

//------------------------------------------------------------------------------
// Fonction 	: NewWindow()
// Description	: Ouvrir une nouvelle fenêtre
//------------------------------------------------------------------------------
var win= null;
function NewWindow(mypage,myname,w,h,scroll,menu,tool){
	var winl = (screen.width-w)/2;
	var wint = (screen.height-h)/2;
	var settings  ='height='+h+',';
	settings +='width='+w+',';
	settings +='top='+wint+',';
	settings +='left='+winl+',';
	settings +='scrollbars='+scroll+',';
	settings +='menubar='+menu+',';
	settings +='toolbar='+tool+',';
	settings +='resizable=yes';
	win=window.open(mypage,myname,settings);
	if(parseInt(navigator.appVersion) >= 4){win.window.focus();}
}

//------------------------------------------------------------------------------
// Fonction 	: afficherApercu()
// Description	: Afficher l'aperçu avec l'url passé en paramètre 
//------------------------------------------------------------------------------
function afficherApercu(url){  
	
		if (url != "") {
			NewWindow(url,'apercu','1024','768','yes','yes','yes'); 
		}
}

//------------------------------------------------------------------------------
// Fonction 	: changerTri()
// Description	: Changer le tri 
//------------------------------------------------------------------------------
function changerTri(tri) {
	document.frm.demande.value="liste";
	document.frm.tri.value = tri;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: changerTriUsager()
// Description	: Changer le tri pour la liste des utilisateurs
//------------------------------------------------------------------------------
function changerTriUsager(tri) {
	document.frm.demande.value="utilisateurs_liste";
	document.frm.tri.value = tri;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: changerTriTermes()
// Description	: Changer le tri pour la liste des termes
//------------------------------------------------------------------------------
function changerTriTermes(tri) {
	document.frm.demande.value="terme_liste";
	document.frm.tri.value = tri;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: changerTriCorbeille()
// Description	: Changer le tri 
//------------------------------------------------------------------------------
function changerTriCorbeille(tri) {
	document.frm.demande.value="corbeille";
	document.frm.tri.value = tri;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: changerTriMediaSel()
// Description	: Changer le tri pour les médias à sélectionner
//------------------------------------------------------------------------------
function changerTriMediaSel(tri) {
	document.frm.demande.value="media_selectionner";
	document.frm.tri.value = tri;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: changerTriItemSel()
// Description	: Changer le tri pour les items à sélectionner
//------------------------------------------------------------------------------
function changerTriItemSel(tri) {
	document.frm.demande.value="items_selectionner";
	document.frm.tri.value = tri;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: changerTriSelQuest()
// Description	: Changer le tri 
//------------------------------------------------------------------------------
function changerTriSelQuest(tri) {
	document.frm.demande.value="questionnaire_selectionner";
	document.frm.tri.value = tri;
	document.frm.submit();
}


//------------------------------------------------------------------------------
// Fonction 	: soumettreDemande(demande)
// Description	: soumettre une demande dans la page courante
//------------------------------------------------------------------------------
function soumettreDemande(demande) {
	document.frm.demande.value = demande;
	document.frm.submit();
}

//------------------------------------------------------------------------------
//Fonction 	: soumettreDemandeApercu(demande)
//Description	: soumettre une demande dans la page courante
//------------------------------------------------------------------------------
function soumettreDemandeApercu(demande) {
	
	// Cas spécial pour marquage
	if (document.frm.item_type_item && document.frm.item_type_item.value == "7") {
		preparerMarqueRetroPourEnregistrement();
	}
	
	afficherApercu('vide.html');
	document.frm.target="apercu";
	document.frm.demande.value = demande;
	document.frm.submit();
	document.frm.target="";
}

//------------------------------------------------------------------------------
// Fonction 	: soumettre()
// Description	: soumettre la page courante
//------------------------------------------------------------------------------
function soumettre() {
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: soumettreDemandeListe()
// Description	: soumettre une demande de liste
//------------------------------------------------------------------------------
function soumettreDemandeListe() {
	document.frm.demande.value = "liste";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: soumettreDemandeCourante()
// Description	: Soumettre une demande pour changer certains paramètres
//------------------------------------------------------------------------------
function soumettreDemandeCourante() {
	document.frm.submit();
}


//------------------------------------------------------------------------------
// Fonction 	: modifierQuestionnaire(id)
// Description	: Modifier un questionnaire
//------------------------------------------------------------------------------
function modifierQuestionnaire(id) {
	document.location = "questionnaires.php?demande=questionnaire_modifier&questionnaire_id_questionnaire=" + id ;
}

//------------------------------------------------------------------------------
// Fonction 	: modifierQuestionnaireSelectionne()
// Description	: Modifier un questionnaire
//------------------------------------------------------------------------------
function modifierQuestionnaireSelectionne() {
	
	// Déterminer le questionnaire sélectionné
	var val = $('input:checkbox:checked.selectionQuest').map(function () { 
		  return this.value; 
	}).get();

	document.location = "questionnaires.php?demande=questionnaire_modifier&questionnaire_id_questionnaire=" + val ;
}

//------------------------------------------------------------------------------
// Fonction 	: apercuQuestionnaireSelectionne()
// Description	: Aperçu d'un questionnaire
//------------------------------------------------------------------------------
function apercuQuestionnaireSelectionne() {
	
	// Déterminer le questionnaire sélectionné
	var val = $('input:checkbox:checked.selectionQuest').map(function () { 
		  return this.value; 
	}).get();

	document.location = "questionnaires.php?demande=questionnaire_apercu&questionnaire_id_questionnaire=" + val + "&demandeRetour=liste";
}

//------------------------------------------------------------------------------
// Fonction 	: exporterQuestionnaireSelectionne()
// Description	: Exporter un questionnaire
//------------------------------------------------------------------------------
function exporterQuestionnaireSelectionne() {
	
	// Déterminer le questionnaire sélectionné
	var val = $('input:checkbox:checked.selectionQuest').map(function () { 
		  return this.value; 
	}).get();

	document.location = "questionnaires.php?demande=questionnaire_exporter&questionnaire_id_questionnaire=" + val + "&demandeRetour=liste";
}

//------------------------------------------------------------------------------
// Fonction 	: telechargerQuestionnaireSelectionne()
// Description	: Télécharger un questionnaire
//------------------------------------------------------------------------------
function telechargerQuestionnaireSelectionne() {
	
	// Déterminer le questionnaire sélectionné
	var val = $('input:checkbox:checked.selectionQuest').map(function () { 
		  return this.value; 
	}).get();

	document.location = "questionnaires.php?demande=questionnaire_telecharger&questionnaire_id_questionnaire=" + val + "&demandeRetour=liste";
}

//------------------------------------------------------------------------------
// Fonction 	: imprimerQuestionnaireSelection()
// Description	: Imprimer un questionnaire
//------------------------------------------------------------------------------
function imprimerQuestionnaireSelectionne(id) {
	
	// Déterminer le questionnaire sélectionné
	var val = $('input:checkbox:checked.selectionQuest').map(function () { 
		  return this.value; 
	}).get();

	afficherApercu("questionnaires.php?demande=questionnaire_imprimer&questionnaire_id_questionnaire=" + val);
}

//------------------------------------------------------------------------------
// Fonction 	: envoiDemandeQuestionnaire()
// Description	: Envoi d'une demande
//------------------------------------------------------------------------------
function envoiDemandeQuestionnaire(demande) {
	document.location = "questionnaires.php?demande=" + demande;
}

//------------------------------------------------------------------------------
// Fonction 	: envoiDemandeIdQuestionnaire()
// Description	: Envoi d'une demande
//------------------------------------------------------------------------------
function envoiDemandeIdQuestionnaire(demande, idQuest) {
	document.location = "questionnaires.php?demande=" + demande + "&questionnaire_id_questionnaire=" + idQuest;
}

//------------------------------------------------------------------------------
// Fonction 	: envoiDemandeBiblio()
// Description	: Envoi d'une demande
//------------------------------------------------------------------------------
function envoiDemandeBiblio(demande) {
	document.location = "bibliotheque.php?demande=" + demande;
}

//------------------------------------------------------------------------------
// Fonction 	: envoiDemandeAdmin()
// Description	: Envoi d'une demande Admin
//------------------------------------------------------------------------------
function envoiDemandeAdmin(demande) {
	document.location = "admin.php?demande=" + demande;
}

//------------------------------------------------------------------------------
// Fonction 	: envoiDemandeMedia()
// Description	: Envoi d'une demande
//------------------------------------------------------------------------------
function envoiDemandeMedia(demande) {
	document.location = "media.php?demande=" + demande;
}

//------------------------------------------------------------------------------
// Fonction 	: envoiDemandeProjet()
// Description	: Envoi d'une demande
//------------------------------------------------------------------------------
function envoiDemandeProjets(demande) {
	document.location = "projets.php?demande=" + demande;
}

//------------------------------------------------------------------------------
// Fonction 	: envoiDemandeCompte()
// Description	: Envoi d'une demande
//------------------------------------------------------------------------------
function envoiDemandeCompte(demande) {
	document.location = "compte.php?demande=" + demande;
}

//------------------------------------------------------------------------------
// Fonction 	: modifierItemSelectionne()
// Description	: Modifier un item sélectionné
//------------------------------------------------------------------------------
function modifierItemSelectionne() {
	
	// Déterminer l'item sélectionné
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();
	
	document.location = "bibliotheque.php?demande=item_modifier&item_id_item=" + val ;
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerItem()
// Description	: Supprimer un item
//------------------------------------------------------------------------------
function supprimerItem(message, idQuest, idItem) {
	
	if (confirm(message)) {
		document.location = "questionnaires.php?demande=item_supprimer&questionnaire_id_questionnaire=" + idQuest + "&item_id_item=" + idItem ;
	}
}

//------------------------------------------------------------------------------
// Fonction 	: apercuWebItemListe()
// Description	: Apercu web d'un item dans la liste des items
//------------------------------------------------------------------------------
function apercuWebItemListe() {

	// Déterminer l'item sélectionné
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();
	
	document.location = "bibliotheque.php?demande=item_apercu_liste&item_id_item=" + val;
}

//------------------------------------------------------------------------------
// Fonction 	: imprimerItemSelection()
// Description	: Imprimer un item
//------------------------------------------------------------------------------
function imprimerItemSelectionne(id) {
	
	// Déterminer l'élment sélectionné
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();

	afficherApercu("bibliotheque.php?demande=item_imprimer_liste&item_id_item=" + val);
}

//------------------------------------------------------------------------------
// Fonction 	: mettreQuestionnaireCorbeille()
// Description	: Mettre le questionnaire à la corbeille
//------------------------------------------------------------------------------
function mettreQuestionnaireCorbeille(message, idQuest) {
	
	if (confirm(message)) {
		document.location = "questionnaires.php?demande=questionnaire_corbeille&questionnaire_id_questionnaire=" + idQuest;
	}
}

//------------------------------------------------------------------------------
// Fonction 	: modifierCollectionSelectionnee()
// Description	: Modifier une collection sélectionnée
//------------------------------------------------------------------------------
function modifierCollectionSelectionnee() {
	
	// Déterminer la collection sélectionnée
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();
	
	document.location = "bibliotheque.php?demande=collection_modifier&collection_id_collection=" + val ;
}

//------------------------------------------------------------------------------
// Fonction 	: modifierCategorieSelectionne()
// Description	: Modifier une collection sélectionnée
//------------------------------------------------------------------------------
function modifierCategorieSelectionnee() {
	
	// Déterminer la collection sélectionnée
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();
	
	document.location = "bibliotheque.php?demande=categorie_modifier&categorie_id_categorie=" + val ;
}

//------------------------------------------------------------------------------
// Fonction 	: modifierLangueSelectionne()
// Description	: Modifier une langue sélectionnée
//------------------------------------------------------------------------------
function modifierLangueSelectionnee() {
	
	// Déterminer la langue sélectionnée
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();
	
	document.location = "bibliotheque.php?demande=langue_modifier&langue_id_langue=" + val ;
}

//------------------------------------------------------------------------------
// Fonction 	: modifierMediaSelectionne()
// Description	: Modifier un média sélectionné
//------------------------------------------------------------------------------
function modifierMediaSelectionne() {
	
	// Déterminer le média sélectionnée
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();
	
	document.location = "media.php?demande=media_modifier&media_id_media=" + val ;
}

//------------------------------------------------------------------------------
// Fonction 	: modifierProjetSelectionne()
// Description	: Modifier un projet
//------------------------------------------------------------------------------
function modifierProjetSelectionne() {
	
	// Déterminer le projet sélectionné
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();

	document.frm.demande.value = "projet_modifier";
	document.frm.projet_id_projet.value = val;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: modifierChampMedia()
// Description	: Modifier la valeur d'un champ media
//------------------------------------------------------------------------------
function modifierChampMedia(val) {
	
	// Obtenir l'id et le titre
	idx = val.indexOf("-");
	id = val.substring(0,idx);
	titre = val.substring(idx+2);
	
	// Mettre à jour le champs caché
	$(champSelectionMedia).val(id);

	// Déterminer si on créé un lien ou on soumet la modification (image)
	typeChamp = "texte";
	
	// Déterminer le type d'action à effectuer - Mettre à jour la page 
	if (actionSelectionMedia == "maj") {
		
		// Soumettre la page pour obtenir l'aperçu de l'image
		flagModifications = false;
		document.frm.demande.value="item_modifier_media";
		document.frm.submit();
	}
	
	// Déterminer le type d'action à effectuer - Créer un lien
	if (actionSelectionMedia == "lien") {

		// Créer un lien dans le div visé
		url = "media.php?demande=media_presenter&media_id_media=" + id;
		lienHtml = '<a href="' + url + '" target="media_' + id + '">M' + id + " - " + titre + '</a>';
		champSelectionLien = champSelectionMedia + "_lien";
		$(champSelectionLien).html(lienHtml);

		// Rendre visible le bouton supprimer
		champSelectionSupp = champSelectionMedia + "_supp";
		$(champSelectionSupp).show();
		
		// Activer flag modification
		flagModifications = true;
		
		// Vérifier la longueur des panneaux
		resizePanels();
	}
	
	// Déterminer le type d'action à effectuer - Ajouter le média dans l'éditeur
	if (actionSelectionMedia == "editeur") {

		journaliser("Ajouter le média '" + id + "' à l'éditeur" );
		
		// Créer un lien dans le div visé
		id = id.replace(/\s+/g, '');
		mediaInfo = " [M" + id + "] ";

		// Ajouter le contenu dans l'éditeur
		contenu = edSel.selection.getContent({format : 'html '});
		
		// Traiter d'un champ avec un placeholder
		contenuChamp = edSel.getContent();
		if (contenuChamp.indexOf('PHSTART') > 0) {
			edSel.setContent(mediaInfo);
		} else {
		 
			// Si du contenu existe, ajouter un espace après
			if (contenu != "") {
				contenu = contenu + " ";
			}
			contenu = contenu + mediaInfo;
			edSel.selection.setContent(contenu);
		}

		// Activer flag modification
		flagModifications = true;
		
		// Vérifier la longueur des panneaux
		resizePanels();
	}	
	
	// Mettre à jour le radio button pour le type de définition (Mes termes seulement)
	champDefinition = champSelectionMedia.substr(7);
	journaliser("Champ Définition : '" + champDefinition + "'");
	selectionnerTypeDefinition(champDefinition);
}

//------------------------------------------------------------------------------
//Fonction 	: modifierChampMediaFermer()
//Description	: Modifier la valeur d'un champ media
//------------------------------------------------------------------------------
function modifierChampMediaFermer(val) {

	// Appel normal
	modifierChampMedia(val);
	
	// Fermer la fenêtre fancybox
	$.fancybox.close();
}

//------------------------------------------------------------------------------
// Fonction 	: viderChampImage()
// Description	: vider un champ image
//------------------------------------------------------------------------------
function viderChampImage(id) {

	// Vidé le champ caché
	idChamp = "#" + id;
	$(idChamp).val("");
	
	// Soumettre la page pour obtenir l'aperçu de l'image
	flagModifications = false;
	document.frm.demande.value="item_modifier_media";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: viderChampMedia()
// Description	: vider un champ média
//------------------------------------------------------------------------------
function viderChampMedia(id, valeur) {

	// Vidé le champ caché
	idChamp = "#" + id;
	$(idChamp).val("");
	
	// Enlever le lien
	idLien = idChamp + "_lien";
	$(idLien).html(valeur);
	
	// Rendre invisible le bouton supprimer
	idSupp = "#" + id + "_supp";
	$(idSupp).hide();

	// Vérifier la longueur des panneaux
	resizePanels();
}

//------------------------------------------------------------------------------
// Fonction 	: ouvrirSelectionMedia(champ)
// Description	: Ouvrir la fenêtre de sélection des médias
//------------------------------------------------------------------------------
function ouvrirSelectionMedia(champ) {

	// Régler le champ cible
	champSelectionMedia = "#" + champ;
	
	// Régler l'action
	actionSelectionMedia = "maj";
}

//------------------------------------------------------------------------------
// Fonction 	: ouvrirImportMedia(champ)
// Description	: Ouvrir la fenêtre de sélection des médias
//------------------------------------------------------------------------------
function ouvrirImportMedia(champ) {

	// Régler le champ cible
	champSelectionMedia = "#" + champ;
	
	// Régler l'action
	actionSelectionMedia = "maj";	
}

//------------------------------------------------------------------------------
// Fonction 	: ouvrirSelectionMediaLien(champ)
// Description	: Ouvrir la fenêtre de sélection des médias
//------------------------------------------------------------------------------
function ouvrirSelectionMediaLien(champ) {

	// Régler le champ cible
	champSelectionMedia = "#" + champ;
	
	// Régler l'action
	actionSelectionMedia = "lien";
}

//------------------------------------------------------------------------------
// Fonction 	: ouvrirSelectionMediaEditeur(champ)
// Description	: Ouvrir la fenêtre de sélection des médias
//------------------------------------------------------------------------------
function ouvrirSelectionMediaEditeur(champ) {

	// Régler le champ cible
	champSelectionMedia = "#" + champ;
	
	// Régler l'action
	actionSelectionMedia = "editeur";
}

//------------------------------------------------------------------------------
// Fonction 	: ouvrirImportMediaLien(champ)
// Description	: Ouvrir la fenêtre de sélection des médias
//------------------------------------------------------------------------------
function ouvrirImportMediaLien(champ) {

	// Régler le champ cible
	champSelectionMedia = "#" + champ;
	
	// Régler l'action
	actionSelectionMedia = "lien";	
}

//------------------------------------------------------------------------------
// Fonction 	: selectionnerItems()
// Description	: Ouvrir une fenêtre jaillissante pour sélectioner des items
//------------------------------------------------------------------------------
function selectionnerItems() {
	document.frm.items_selectionner.value="1";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: selectionnerQuestionnaire()
// Description	: Ouvrir une fenêtre jaillissante pour sélectioner un questionnaire
//------------------------------------------------------------------------------
function selectionnerQuestionnaire() {
	$(".fenetreSelQuest").trigger('click');
}

//------------------------------------------------------------------------------
// Fonction 	: enregistrer(demande)
// Description	: Soumettre une demande pour enregistrer
//------------------------------------------------------------------------------
function enregistrer(demande) {

	// Placer la page de destination comme la page courante
	document.frm.pagination_page_dest.value = "";
	
	// Désactiver flag modification
	flagModifications = false; 
	
	// Soumettre la demande d'enregistrement
	document.frm.demande.value = demande;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: remplacerIdentificationUtilisateur(identification)
// Description	: Remplacer le prénom et le nom de l'utilisateur dans l'entête
//------------------------------------------------------------------------------
function remplacerIdentificationUtilisateur(identification) {
	$("#identificationUtilisateur").text(identification);
}

//------------------------------------------------------------------------------
// Fonction 	: remplacerStatut(statut, statutTxt)
// Description	: Remplacer le statut du questionnaire
//------------------------------------------------------------------------------
function remplacerStatut(statut, statutTxt) {

	$("#statutQuestionnaire").text(statutTxt);

	// Activer l'action "désactiver"
	$("#questionnaireVoir").removeClass("inactif");
	$("#questionnaireDesactiver").removeClass("inactif");
}
 
//------------------------------------------------------------------------------
// Fonction 	: ajouterElement(element)
// Description	: Ajouter un élément de réponse
//------------------------------------------------------------------------------
function ajouterElement(element) {
	flagModifications = false;
	document.frm.element.value=element;
	document.frm.demande.value="item_modifier_ajouter_element";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerElement(element)
// Description	: Supprimer un élément de réponse
//------------------------------------------------------------------------------
function supprimerElement(element) {
	flagModifications = false;
	document.frm.element.value=element;
	document.frm.demande.value="item_modifier_supprimer_element";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: ajouterCouleur(idxCouleur)
// Description	: Ajouter une couleur
//------------------------------------------------------------------------------
function ajouterCouleur(idxCouleur) {
	flagModifications = false;
	document.frm.couleur.value=idxCouleur;
	document.frm.demande.value="item_modifier_ajouter_couleur";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerCouleur(idxCouleur)
// Description	: Ajouter une couleur
//------------------------------------------------------------------------------
function supprimerCouleur(idxCouleur) {
		flagModifications = false;
		document.frm.couleur.value=idxCouleur;
		document.frm.demande.value="item_modifier_supprimer_couleur";
		document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: ajouterClasseur(idxClasseur)
// Description	: Ajouter un classeur
//------------------------------------------------------------------------------
function ajouterClasseur(idxClasseur) {
	flagModifications = false;
	document.frm.classeur.value=idxClasseur;
	document.frm.demande.value="item_modifier_ajouter_classeur";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerClasseur(idxClasseur)
// Description	: Ajouter un classeur
//------------------------------------------------------------------------------
function supprimerClasseur(idxClasseur) {
		flagModifications = false;
		document.frm.classeur.value=idxClasseur;
		document.frm.demande.value="item_modifier_supprimer_classeur";
		document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: redimensionner()
// Description	: Ajuste la présentation de l'image media
//------------------------------------------------------------------------------
(function($){
    // Redimensionner l'image dans la fiche média
    $.fn.redimensionner = function() {   
 
        $(document).ready(function() {
            $('#image_media').redimensionnerImage(300,200); 
        });

        $(window).bind("resize", function() {
            $('#image_media').redimensionnerImage(300,200); 
        });
        
    };
    
    // Ajuster la taille
    $.fn.redimensionnerImage = function(largeur, longueur) {

    	// Obtenir la taille de l'image originale
        var startWidth = $(this).width();
        var startHeight = $(this).height();
        
        journaliser("redimensionnerImage() startWidth :" + startWidth);
        journaliser("redimensionnerImage() startHeight :" + startHeight);
        
        // Obtenir le ratio de l'image
        var ratio = startHeight/startWidth;

        // Obtenir la taille de l'espace disponible
        var browserWidth = largeur;
        var browserHeight = longueur;
        
        // Resize image to proper ratio
        var browserRatio = browserHeight/browserWidth;

		var imageWidth;
		var imageHeight;				              
				               
        if ( browserRatio > ratio) {
        	
        	// Ne pas zoomer l'image
        	if (largeur < startWidth) {
	      		imageWidth = browserWidth;
	       		imageHeight = browserWidth * ratio;
	        
	            // Respecter la taille maximale de l'image
			    if (browserWidth > startWidth) {
			      	imageWidth = startWidth;
			       	imageHeight = startHeight;
			    }
	    
	        	$(this).width(Math.round(imageWidth));
	            $(this).height(Math.round(imageHeight));
	            $(this).children().width(Math.round(imageWidth));
	            $(this).children().height(Math.round(imageHeight));
        	}

        } else {
        	
        	// Ne pas zoomer l'image
        	if (longueur < startHeight ) {
	        	imageHeight = browserHeight;
	        	imageWidth = browserHeight / ratio;
	        
	            // Respecter la taille maximale de l'image
			    if (browserHeight > startHeight) {
			      	imageHeight = startHeight;
			      	imageWidth = startWidth;
			    }
			    
	            $(this).height(Math.round(imageHeight));
	            $(this).width(Math.round(imageWidth));
        	}
        }
        
    };
})(jQuery);


//------------------------------------------------------------------------------
// Fonction 	: activerSuiviModifications(flag)
// Description	: Activer le suivi des modifications au besoin
//------------------------------------------------------------------------------
function activerSuiviModifications(flag) {
	if (flag == '1') {
		flagModifications = true;
		document.frm.flagModifications.value = "1";
	}
}

//------------------------------------------------------------------------------
// Fonction 	: desactiverSuiviModifications(flag)
// Description	: Désactiver le suivi des modifications au besoin
//------------------------------------------------------------------------------
function desactiverSuiviModifications() {
	flagModifications = false;
	if (document.frm.flagModifications) {
		document.frm.flagModifications.value = "0";
	}
}

//------------------------------------------------------------------------------
// Fonction 	: ouvrirPanneauParametres(flag)
// Description	: Ouvrir le panneau des paramètres au besoin
//------------------------------------------------------------------------------
function ouvrirPanneauParametres(flag) {
	if (flag == "1") {
		toggleCadre('lnkCadre1','cadre1');
		document.frm.vider_panneau_parametres.value = "";
	} else {
		document.frm.vider_panneau_parametres.value = "1";
	}
}

//------------------------------------------------------------------------------
// Fonction 	: ouvrirPanneauMessages(flag)
// Description	: Ouvrir le panneau des messages au besoin
//------------------------------------------------------------------------------
function ouvrirPanneauMessages(flag) {
	if (flag == "1") {
		toggleCadre('lnkCadre2','cadre2');
		document.frm.vider_panneau_messages.value = "";
	} else {
		document.frm.vider_panneau_messages.value = "1";
	}
}

//------------------------------------------------------------------------------
// Fonction 	: activerModifierValeursParametres()
// Description	: Ouvrir le panneau des paramètres au besoin
//------------------------------------------------------------------------------
function activerModifierValeursParametres() {
	toggleCadre('lnkCadre1','cadre1');
	document.frm.vider_panneau_parametres.value = "";
}

//------------------------------------------------------------------------------
// Fonction 	: desactiverModifierValeursParametres()
// Description	: Ouvrir le panneau des paramètres au besoin
//------------------------------------------------------------------------------
function desactiverModifierValeursParametres() {
	toggleCadre('lnkCadre1','cadre1');
	document.frm.vider_panneau_parametres.value="1";
}

//------------------------------------------------------------------------------
// Fonction 	: activerModifierValeursMessages()
// Description	: Ouvrir le panneau des paramètres au besoin
//------------------------------------------------------------------------------
function activerModifierValeursMessages() {
	document.frm.vider_panneau_messages.value="";
	toggleCadre('lnkCadre2','cadre2');
}

//------------------------------------------------------------------------------
// Fonction 	: desactiverModifierValeursMessages()
// Description	: Ouvrir le panneau des paramètres au besoin
//------------------------------------------------------------------------------
function desactiverModifierValeursMessages() {
	toggleCadre('lnkCadre2','cadre2');
	document.frm.vider_panneau_messages.value="1";
}

//------------------------------------------------------------------------------
// Fonction 	: changerTypeElement1()
// Description	: Changer le type d'élément de réponse (image, texte, etc.)
//------------------------------------------------------------------------------
function changerTypeElement1(type) {
	flagModifications = false;
	document.frm.item_type_elements1.value=type;
	document.frm.demande.value="item_modifier_type_element";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: changerTypeElement2()
// Description	: Changer le type d'élément de réponse (image, texte, etc.)
//------------------------------------------------------------------------------
function changerTypeElement2(type) {
	flagModifications = false;
	document.frm.item_type_elements2.value=type;
	document.frm.demande.value="item_modifier_type_element";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: changerTypeClasseur()
// Description	: Changer le type de classeur
//------------------------------------------------------------------------------
function changerTypeClasseur(type1, type2) {
	flagModifications = false;
	document.frm.item_type_elements1.value=type1;
	document.frm.item_type_elements2.value=type2;
	document.frm.demande.value="item_modifier_type_element_classeur";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: changerTypeLacune()
// Description	: Changer le type de lacune
//------------------------------------------------------------------------------
function changerTypeLacune(typeLacune) {
	flagModifications = false;
	document.frm.item_type_lacune.value = typeLacune;
	document.frm.demande.value="item_modifier_type_lacune";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: changerLangueApercu()
// Description	: Changer la langue de l'aperçu
//------------------------------------------------------------------------------
function changerLangueApercu() {

	// Conserver le flag des modifications
	if (flagModifications) {
		document.frm.flagModifications.value = "1";
	} else {
		document.frm.flagModifications.value = "0";
	}
	flagModifications = false;
	
	// Soumettre la demande
	document.frm.demande.value="item_modifier_langue_apercu";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: changerThemeApercu()
// Description	: Changer le thème de l'aperçu
//------------------------------------------------------------------------------
function changerThemeApercu() {

	// Conserver le flag des modifications
	if (flagModifications) {
		document.frm.flagModifications.value = "1";
	} else {
		document.frm.flagModifications.value = "0";
	}
	flagModifications = false;
	
	// Soumettre la demande
	document.frm.demande.value="item_modifier_theme_apercu";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: genererApercuItem()
// Description	: Générer un aperçu
//------------------------------------------------------------------------------
function genererApercuItem() {

	// Conserver le flag des modifications
	if (flagModifications) {
		document.frm.flagModifications.value = "1";
	} else {
		document.frm.flagModifications.value = "0";
	}
	flagModifications = false;
	
	// Soumettre la demande
	document.frm.demande.value="item_apercu";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: apercuQuestionnaire()
// Description	: Lancer l'aperçu d'un questionnaire
//------------------------------------------------------------------------------
function apercuQuestionnaire(demande, demandeRetour) {

	// Conserver le flag des modifications
	if (flagModifications) {
		document.frm.flagModifications.value = "1";
	} else {
		document.frm.flagModifications.value = "0";
	}
	flagModifications = false;
	
	// Soumettre la demande
	document.frm.demandeRetour.value = demandeRetour;
	document.frm.demande.value = demande;
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: annulerQuestionnaire()
// Description	: Bouton annuler du questionnaire, recharge des données
//------------------------------------------------------------------------------
function annulerQuestionnaire(demande) {

	if (flagModifications == true) {
		console.log("demande de confirmation x");
		flagModifications = false;
		if (confirm(TXT_AVERTISSEMENT_ANNULER)) {
			// Soumettre la demande
			document.frm.demande.value = demande;
			document.frm.submit();
		} else {
			flagModifications = true;
		}
	}
	
}	

//------------------------------------------------------------------------------
// Fonction 	: changerTypeItem()
// Description	: Changer l'item pour un nouveau type d'item
//------------------------------------------------------------------------------
function changerTypeItem(type) {

	// Vérifier que l'utilisateur demande un changement vers un autre type
	if (type != document.frm.item_type_item.value) {
	
		// Désactiver flag modifications
		flagModifications = false;
		
		// Soumettre la demande
		document.frm.item_type_item.value=type;
		document.frm.demande.value = "item_modifier_type";
		document.frm.submit();
	}
}	

//------------------------------------------------------------------------------
// Fonction 	: choisirCouleur(champ, couleur)
// Description	: Choisir une couleur
//------------------------------------------------------------------------------
function choisirCouleur(champ, couleur) {
	
	idChampCache = "#item_couleur_" + champ;
	idChampCouleur = "#couleur_" + champ;
	carreCouleur = "carre" + couleur;
	
	// Régler le champ caché
	$(idChampCache).val(couleur);
	
	// Mettre à jour le nom de la couleur
	modifierMarqueTitreCouleurSel(couleur);
	
	// Changer la classe de couleur
	nouvelleClasse = "zoneBordure" + couleur;
	$('.zonesZone').removeClass('zoneBordureFFFFFF zoneBordure0000FF zoneBordure000000 zoneBordureFFFF00 zoneBordureFF0000 zoneBordure00CC00').addClass(nouvelleClasse);
		
	// Changer la couleur du carré
	$(idChampCouleur).css('background-color', '#'+couleur);

}

//------------------------------------------------------------------------------
// Fonction 	: marqueChoisirCouleur()
// Description	: Choisir et appliquer une couleur à la marque
//------------------------------------------------------------------------------
function marqueChoisirCouleur(couleur) {

	journaliser("marqueChoisirCouleur() Contenu avant : '" + $('#item_texte').html() + "'");
	
	node = null;
	
	// Déterminer l'éditeur
	ed = $('#item_texte').tinymce();

	// Obtenir le html de la marque
	marque = ed.selection.getContent({format : 'text'});
	journaliser("marqueChoisirCouleur() sélection : '" + marque + "'");

	// Déterminer la marque en cours de modification
	journaliser("marqueChoisirCouleur() marqueOuverteDansEditeur : '" + marqueOuverteDansEditeur + "'");
	
	// Obtenir l'id de la marque, au besoin créer un nouvel id
	if (marqueOuverteDansEditeur == "" || marqueOuverteDansEditeur == "0") {
		
		// Obtenir le prochain id
		noMarque = nbMarques + 1;
		idMarque = "marque_" + noMarque; 
		journaliser("marqueChoisirCouleur() Générer un nouveau id marque : '" + idMarque + "'");
		
	} else {
		idMarque = marqueOuverteDansEditeur;
		journaliser("marqueChoisirCouleur() Utiliser la marque ouverte dans l'éditeur : '" + idMarque + "'");
	}
	
	// Noter comme la marque ouverte dans l'édieur
	marqueOuverteDansEditeur = idMarque;
	
	// Créer la marque
	journaliser("marqueChoisirCouleur() Utiliser la marque suivante pour créer le span : '" + idMarque + "'");
	marqueHTML = "<span id='" + idMarque + "' class='marque' style='background-color: #" + couleur + "'>" + marque + "</span>"; 
	
    // Récupérer le node
	if (marque != "") {
		// 1. Via sélection si possible
		journaliser("marqueChoisirCouleur() Obtenir le node via selection");
		node = ed.selection.getNode();	
	} else {
		// 2. Sinon via le DOM
		node = obtenirNodeEditeur(idMarque);
		journaliser("marqueChoisirCouleur() Obtenir le node via DOM");
	}
		
	journaliser("marqueChoisirCouleur() id du node '" + node.id + "'");

	// Déterminer si on est déjà dans une marque
	if ( node.nodeName == 'SPAN' && ed.dom.hasClass(node, 'marque') ) {	
		journaliser("marqueChoisirCouleur() marque détectée");
		journaliser("marqueChoisirCouleur() id : '" + node.id + "'");
		journaliser("marqueChoisirCouleur() couleur : '" + couleur + "'");
		remplacerCouleurMarque(node.id, couleur);
   } else {
	   journaliser("marqueChoisirCouleur() hors marque");
	   ed.selection.setContent(marqueHTML);
   }
   
   // Analyser les marques
   analyserMarques();
   
   // Mettre à jour les placeholders des rétros
   preparerAffichageRetroPlaceHolder(couleur);
	
   journaliser("marqueChoisirCouleur() Contenu après : '" + $('#item_texte').html() + "'");
}


//------------------------------------------------------------------------------
// Fonction 	: remplacerCouleurMarque()
// Description	: Remplacer la couleur d'une marque existante
//------------------------------------------------------------------------------
function remplacerCouleurMarque(idMarque, couleur) {
	
	idMarque = "#" + idMarque;
	couleur = "#" + couleur;
	
	ed = $('#item_texte').tinymce();
	
	journaliser("remplacerCouleurMarque() Régler la couleur à '" + couleur + "' pour la marque '" + idMarque + "'");
	ed.dom.setStyle(ed.dom.select(idMarque), 'background-color', couleur);
}


//------------------------------------------------------------------------------
// Fonction 	: ajouterMarque()
// Description	: Ajouter une marque
//------------------------------------------------------------------------------
function ajouterMarque() {

	// Obtenir le contenu sélectionné
	marqueTexte = $('#item_texte').tinymce().selection.getContent({format : 'text'});
	
	// Si du texte est sélectionné et que l'éditeur de marque n'est pas ouvert, action... sinon on ne fait rien
	
	journaliser("ajouterMarque() marqueTexte : '" + marqueTexte + "' marqueOuverteDansEditeur : '" + marqueOuverteDansEditeur + "'");
	journaliser("ajouterMarque() Vérifier si marqueTexte n'est pas vide et que l'éditeur n'est pas vide");
	if (marqueTexte != "" && marqueOuverteDansEditeur == 0) {
		
		journaliser("ajouterMarque() Validation complétée");
		
		// Parcourir la liste des rétros et assigner des placeholders par défaut
		$(".marqueRetro").each(function() {
			
			// Obtenir l'id
			idRetro = "#" + this.id;
			
			// Mettre un placholder pour retro positive
			$(idRetro).attr("placeholder", TXT_RETRO_NEGATIVE);
			$(idRetro).tinymce().setContent(getPlaceHolder(TXT_RETRO_NEGATIVE));
			
		});
		
		// Modifier le titre de la marque dans le panneau d'édition
		journaliser("ajouterMarque() texte de la marque : '" + supprimerFinLigne(marqueTexte) + "'");
		$("#marque_titre").text(supprimerFinLigne(marqueTexte));
		
		// Fermer les barres d'outil des éditeurs
		journaliser("ajouterMarque() Fermer les éditeurs");
		fermerEditeurs();
		
		// Afficher le cadre
		$("#cadreEditeurMarques").show();
		
		// Cacher les titres des couleurs
		$('.couleurTitre').hide();
		
		// Ajuster la fenêtre
		resizePanels();

		// Appliquer la couleur à la marque
		marqueChoisirCouleur(MARQUAGE_COULEUR_DEFAUT);
		
		// Mettre la couleur jaune par défaut
		choisirCouleur('marque_couleur', MARQUAGE_COULEUR_DEFAUT);
		
		// Cacher les titres de couleurs
		$('.titreCouleur').hide();
		
		
		
	} else {
		journaliser("ajouterMarque() Validation en erreur");
	}
	
}


//------------------------------------------------------------------------------
// Fonction 	: modifierMarque()
// Description	: Modifier une marque
//------------------------------------------------------------------------------
function modifierMarque(node) {

	// Obtenir le span courant
	marqueTexte = node.innerHTML;
	marqueHTML = node.outerHTML;
	marqueId = node.id;

	// Identifier la marque en édition
	marqueOuverteDansEditeur = marqueId;
	
	// Obtenir la couleur
	var Styles = new tinymce.html.Styles();
	marqueCouleur = Styles.toHex(node.style.backgroundColor).toUpperCase();
	journaliser("modifierMarque() marqueTxt: '" + marqueTexte + "'");
	journaliser("modifierMarque() marqueHTML : '" + marqueHTML + "'");
	journaliser("modifierMarque() marqueCouleur : '" + marqueCouleur + "'");	
	journaliser("modifierMarque() marqueId : '" + marqueId + "'");
	
	// Modifier le texte dans le panneau d'édition
	$("#marque_titre").text(supprimerFinLigne(marqueTexte));
	
	// Modifier la couleur
	idChampCouleur = "#couleur_marque_couleur";
	$(idChampCouleur).css('background-color', marqueCouleur);
	
	// Modifier le titre de la couleur
	modifierMarqueTitreCouleurSel(marqueCouleur.substring(1));

	// Préparer l'affichage des rétros
	preparerAffichageRetro(marqueCouleur.substring(1));

	// Fermer les barres d'outil des éditeurs
	journaliser("modifierMarque() Fermer les éditeurs");
	fermerEditeurs();
	
	// Ouvrir l'éditeur de marque
	$("#cadreEditeurMarques").show();
	
	// Ajuster la fenêtre
	resizePanels();
}


//------------------------------------------------------------------------------
// Fonction 	: modifierMarqueTitreCouleurSel()
// Description	: Modifier le titre de la couleur sélectionnée
//------------------------------------------------------------------------------
function modifierMarqueTitreCouleurSel(couleur) {

	idCouleurTitre = "#titre" + couleur.toUpperCase();
	journaliser("modifierMarqueTitreCouleurSel() idCouleurTitre : '" + idCouleurTitre + "'");
	$('.couleurTitre').hide();
	$(idCouleurTitre).show();
	
}


//------------------------------------------------------------------------------
// Fonction 	: preparerAffichageRetro()
// Description	: Préparer l'affichage des rétroactions
//------------------------------------------------------------------------------
function preparerAffichageRetro(couleurSel) {

	journaliser("preparerAffichageRetro()");
	
	// Mettre à jour les placeholders des rétros
	journaliser("preparerAffichageRetro() couleurSel : '" + couleurSel + "'");
	preparerAffichageRetroPlaceHolder(couleurSel);

	// Obtenir l'id de la marque en édition
	idMarque = marqueOuverteDansEditeur;
	journaliser("preparerAffichageRetro() Marque en édition : '" + idMarque + "'");
	
	// Récupérer les valeurs
	if (retroMarques[idMarque] != 'undefined') {
		for (var key in retroMarques[idMarque] ) {
			journaliser("key : '" + key + "'");
			if (retroMarques[idMarque][key] != "") {
				contenu = retroMarques[idMarque][key];
				journaliser("preparerAffichageRetro() Régler le champ '" + key + "' avec le contenu : '" + contenu + "'" );
				if($(key).length != 0) {
					$(key).tinymce().setContent(contenu);
				}
			}
		}
	}
}


//------------------------------------------------------------------------------
// Fonction 	: preparerAffichageRetroPlaceHolder()
// Description	: Préparer l'affichage des placeholder des rétroactions 
//------------------------------------------------------------------------------
function preparerAffichageRetroPlaceHolder(couleurSel) {

	journaliser("preparerAffichageRetroPlaceHolder() couleurSel : '" + couleurSel + "'");
	
	// Obtenir l'id du champ dont la couleur est sélectionnée
	idRetroSel = "#retro_" + couleurSel;
	journaliser("preparerAffichageRetroPlaceHolder() ID Retro sel : '" + idRetroSel + "'");
	
	// Parcourir la liste des rétros
	$(".marqueRetro").each(function() {
		
		// Obtenir l'id
		idRetro = "#" + this.id;
		
		// Raccourci pour l'éditeur
		ed = $(idRetro).tinymce();
		
		// Vérifier si c'est la couleur sélectionnée
		journaliser("preparerAffichageRetroPlaceHolder() idRetro :'" + idRetro + "' idRetroSel : '" + idRetroSel + "'");

		// Déterminer si on doit modifier le contenu pour y placer un PH... 
		// Si l'utilisateur a saisi du texte, le conserver
        var contenu = decodeEntities(ed.getContent());
        var attribut = decodeEntities(getPlaceHolder($(idRetro).attr("placeholder")));
    	is_default = (contenu == attribut || contenu == '');

    	journaliser("preparerAffichageRetroPlaceHolder() contenu : '" + contenu + "'");
    	journaliser("preparerAffichageRetroPlaceHolder() attribut : '" + attribut + "'");
    	journaliser("preparerAffichageRetroPlaceHolder() is_default : '" + is_default + "'");
    	
		if (idRetro.toLowerCase() == idRetroSel.toLowerCase()) {
			// Mettre un placholder pour retro positive
			$(idRetro).attr("placeholder", TXT_RETRO_POSITIVE);
			if (is_default) {
				journaliser("preparerAffichageRetroPlaceHolder() Retro positive changer dans éditeur");
				ed.setContent(getPlaceHolder(TXT_RETRO_POSITIVE));
			}
		} else {
			// Mettre un placholder pour retro négative
			$(idRetro).attr("placeholder", TXT_RETRO_NEGATIVE);
			if (is_default) {
				journaliser("preparerAffichageRetroPlaceHolder() Retro négative changer dans éditeur");
				ed.setContent(getPlaceHolder(TXT_RETRO_NEGATIVE));
			}
		}
		
	});
}


//------------------------------------------------------------------------------
// Fonction 	: viderMarqueRetro()
// Description	: Vider les champs retro 
//------------------------------------------------------------------------------
function viderMarqueRetro() {

	journaliser("viderMarqueRetro()");
	
	// Parcourir la liste des rétros
	$(".marqueRetro").each(function() {
		
		// Obtenir l'id
		idRetro = "#" + this.id;
		
		// Raccourci pour l'éditeur
		$(idRetro).tinymce().setContent("");
	
	});
}

//------------------------------------------------------------------------------
// Fonction 	: fermerMarque()
// Description	: Fermer une marque
//------------------------------------------------------------------------------
function fermerMarque() {

	// Sauvegarder les rétroactions
	enregistrerMarqueRetro();
	
   	// Cacher le cadre pour l'édition des marques
	$("#cadreEditeurMarques").hide();
	
	// Marque en édition à 0
	marqueOuverteDansEditeur = 0;
	
	// Ajuster la fenêtre
	resizePanels();
}


//------------------------------------------------------------------------------
// Fonction 	: enregistrerMarqueRetro()
// Description	: Sauvegarder les informations sur les rétroactions
//------------------------------------------------------------------------------
function enregistrerMarqueRetro() {

	journaliser("enregistrerMarqueRetro()");
	
	// Obtenir l'id de la marque en édition
	journaliser("enregistrerMarqueRetro() Marque en édition : '" + marqueOuverteDansEditeur + "'");
	idMarque = marqueOuverteDansEditeur;
	
	if (idMarque != "" && idMarque != "0") {
	
		$(".marqueRetro").each(function() {
			
			// Obtenir l'id de la rétro
			idRetro = "#" + this.id;
			
			// Obtenir le contenu de TinyMCE
			contenu = $(idRetro).tinymce().getContent();
			
			// Ne pas enregistrer les placeholder
			if (contenu.indexOf('PHSTART') > 0) {
				contenu = "";
			}
			
			// Sauvegarder
			journaliser("enregistrerMarqueRetro() Sauvegarde en mémoire idMarque : '" + idMarque + "' idRetro : '" + idRetro + "' Contenu : '" + contenu + "'");
			if (retroMarques[idMarque] == undefined) {
				retroMarques[idMarque] = new Array();
			}
			retroMarques[idMarque][idRetro] = contenu;
			
		});
	}
}


//------------------------------------------------------------------------------
// Fonction 	: preparerMarqueRetroPourEnregistrement()
// Description	: Préparer les champs rétro pour l'enregistrement sur le serveur
//------------------------------------------------------------------------------
function preparerMarqueRetroPourEnregistrement() {

	journaliser("preparerMarqueRetroPourEnregistrement()");
	
	// Déterminer l'éditeur
	ed = $('#item_texte').tinymce();
	
	// Enregistrer les retros de la marque courante au besoin
	enregistrerMarqueRetro();

	// Sélectionner tous les éléments de type span
	var spanElems = ed.dom.select('span');

	// Parcourir la liste et préparer les données
	tinymce.each(spanElems, function(spanElem) {
		
		idMarque = spanElem.id;
	    journaliser("preparerMarqueRetroPourEnregistrement() Marque localisée : '" + idMarque + "'");

	    // Générer un champ hidden
		if (retroMarques[idMarque] != 'undefined') {
			for (var key in retroMarques[idMarque] ) {
				journaliser("preparerMarqueRetroPourEnregistrement() key : '" + key + "'");
				if (retroMarques[idMarque][key] != "") {
					contenu = retroMarques[idMarque][key];
					
					// Enlever le # et créer un champ hidden pour la rétro
					key = key.replace("#", "");
					journaliser("preparerMarqueRetroPourEnregistrement() Régler le champ '" + key + "' avec le contenu : '" + contenu + "'" )
					$('#frm').append('<input type="hidden" name="item_' + idMarque + '_' + key + '" value="' + contenu +'" />');
				}
			}
		}

	});
	
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerMarques()
// Description	: Supprimer les marques dans la sélection
//------------------------------------------------------------------------------
function supprimerMarques() {

	// Déterminer l'éditeur
	ed = $('#item_texte').tinymce();
	
	// Obtenir le contenu sélectionné
	var contenuSel = ed.selection.getContent();
	journaliser("supprimerMarques() contenu :'" + contenuSel + "'");

	// Sélectionner tous les éléments de type span
	var spanElems = ed.dom.select('span');

	// Parcourir la liste et enlever les spans
	tinymce.each(spanElems, function(spanElem) {
		journaliser("supprimerMarques() found outter:'" + spanElem.outerHTML + "'");
	    journaliser("supprimerMarques() found inner:'" + spanElem.innerHTML + "'");
	    journaliser("supprimerMarques() found id : '" + spanElem.id + "'");
	    
	    // Vérifier si le span est dans la sélection, si oui, supprimer
	    spanRech = 'id="' + spanElem.id + '"';
	    if (contenuSel.indexOf(spanRech) > 0) {
	    	journaliser("supprimerMarques() suppression du span id : '" + spanElem.id + "'");
		    spanElem.outerHTML = spanElem.innerHTML;
		    
			// Supprimer les données pour cette marque
			delete(retroMarques[spanElem.id]);
	    }

	});
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerMarque()
// Description	: Supprimer la marque courante
//------------------------------------------------------------------------------
function supprimerMarque() {
	
	// Analyser les marques
	analyserMarques();
	
	// Obtenir la marque courante
	idMarque = marqueOuverteDansEditeur;
	journaliser("supprimerMarque() Supprimer la marque : '" + idMarque + "'");
	
	// Obtenir le noeud pour la marque
	node = obtenirNodeEditeur(idMarque)
	
	// Parcourir la liste et enlever le spans
   	journaliser("supprimerMarque() suppression du span id : '" + node.id + "'");
	node.outerHTML = node.innerHTML;
	
	// Supprimer les données pour cette marque
	delete(retroMarques[idMarque]);
	
	// Cacher le cadre pour l'édition des marques
	$("#cadreEditeurMarques").hide();
	
	// Ajuster la fenêtre
	resizePanels();
	
	// Supprimer l'id de la marque en édition
	marqueOuverteDansEditeur = "0";	
}

//------------------------------------------------------------------------------
// Fonction 	: analyserMarques()
// Description	: Analyser les marques
//------------------------------------------------------------------------------
function analyserMarques() {
	
	// Déterminer l'éditeur
	ed = $('#item_texte').tinymce();
	
	// Sélectionner tous les éléments de type span
	var spanElems = ed.dom.select('span');

	// Déterminer la position de la marque en cours d'édition dans la liste
	positionMarque = 0;
	
	// Déterminer la marque précédente et la marque suivante
	idMarquePrec = '';
	idMarqueSuiv = '';
	idMarqueMemoire = "";
	idMarqueDebut = '';
	idMarqueFin = '';
	
	// Parcourir la liste
	i = 0;
	tinymce.each(spanElems, function(spanElem) {
		i++;
	    journaliser("analyserMarques() found outter:'" + spanElem.outerHTML + "'");
	    journaliser("analyserMarques() found inner:'" + spanElem.innerHTML + "'");
	    
	    // Obtenir l'id de la marque
	    idMarque = spanElem.id;
	    
	    // Marque début
	    if (idMarqueDebut == '') {
	    	idMarqueDebut = idMarque;
	    }
	    
	    // Marque suivante
	    if (positionMarque != "" && idMarqueSuiv == "") {
	    	idMarqueSuiv = idMarque;
	    } 
	    idMarqueMemoire = idMarque;
	    
	    // Marque précédente
	    if (positionMarque == '' && idMarque != marqueOuverteDansEditeur) {
	    	idMarquePrec = idMarque;
	    }
	    
	    // Marque Fin
	    idMarqueFin = idMarque;
	    
	    // Vérifier si l'id de la marque courante est celle en cours d'édition
	    journaliser("analyserMarques() idMarque :'" + idMarque + "' marqueOuverteDansEditeur : '" + marqueOuverteDansEditeur + "'");
	    if (idMarque == marqueOuverteDansEditeur) {
	    	positionMarque = i;
	    }
	    
	    // Supprimer les marques vides
	    if (spanElem.innerHTML == "") {
	    	journaliser("analyserMarques()  supprimer marque vide : '" + spanElem.outerHTML + "'");
	    	
	    	spanElem.outerHTML = "";
	    	journaliser("analyserMarques()  supprimer marque: '" + idMarque + "'");
	    }
	    
	    journaliser("analyserMarques() marqueOuverteDansEditeur : '" + marqueOuverteDansEditeur + "' idMarque : '" + idMarque + "' idMarquePrec = '" + idMarquePrec + "' idMarqueSuiv = '" + idMarqueSuiv + "' idMarqueDebut : '" + idMarqueDebut + "' idMarqueFin : '" + idMarqueFin + "'");
	});
			
	journaliser("analyserMarques() FIN marqueOuverteDansEditeur : '" + marqueOuverteDansEditeur + "' idMarque : '" + idMarque + "' idMarquePrec = '" + idMarquePrec + "' idMarqueSuiv = '" + idMarqueSuiv + "' idMarqueDebut : '" + idMarqueDebut + "' idMarqueFin : '" + idMarqueFin + "'");
	
	// Assigner le nombre total de marques
	nbMarques = i;
	journaliser("analyserMarques() Nb total de marques : '" + nbMarques + "'");
	journaliser("analyserMarques() Contenu après : '" + $('#item_texte').html() + "'");
	
	// Mettre à jour le nombre total de marques
	$('#nav_total').text(nbMarques);
	$('#nav_position').text(positionMarque);
	$('#nav_position2').text(positionMarque);
	
}


//------------------------------------------------------------------------------
// Fonction 	: selectionnerMarque()
// Description	: Sélectionner une marque
//------------------------------------------------------------------------------
function selectionnerMarque(idMarque) {
	
	// Déterminer l'éditeur
	ed = $('#item_texte').tinymce();
	
	// Obtenir le node
	node = obtenirNodeEditeur(idMarque);
	journaliser("selectionnerMarque id node trouvé : '" + node.id + "'");
}


//------------------------------------------------------------------------------
// Fonction 	: modifierMarqueSuiv()
// Description	: Passer à la marque suivante et l'ouvrir pour modification
//------------------------------------------------------------------------------
function modifierMarqueSuiv() {

	journaliser("marqueSuiv() idMarqueSuiv : '" + idMarqueSuiv + "'");
	
	// Déterminer l'éditeur
	ed = $('#item_texte').tinymce();
	
	if (idMarqueSuiv != "" && idMarqueSuiv != "0") {

		// Sauvegarder le contenu actuel
		enregistrerMarqueRetro()
		
		// Vider les champs retro
		viderMarqueRetro()
		
		// Sélectionner la marque
		selectionnerMarque(idMarqueSuiv);
	
		// Ouvrir la marque pour modification
		modifierMarque(node);
		
		// Analyser
		analyserMarques();
	}
}


//------------------------------------------------------------------------------
// Fonction 	: modifierMarquePrec()
// Description	: Passer à la marque précédente et l'ouvrir pour modification
//------------------------------------------------------------------------------
function modifierMarquePrec() {
	
	journaliser("marquePrec() idMarquePrec : '" + idMarquePrec + "'");
	
	// Déterminer l'éditeur
	ed = $('#item_texte').tinymce();
	
	// Obtenir l'id de la marque précédente
	
	
	if (idMarquePrec != "" && idMarquePrec != "0") {

		// Sauvegarder le contenu actuel
		enregistrerMarqueRetro()
		
		// Vider les champs retro
		viderMarqueRetro()
		
		// Sélectionner la marque
		selectionnerMarque(idMarquePrec);

		// Ouvrir la marque pour modification
		modifierMarque(node);
	
		// Analyser
		analyserMarques();
	}
}


//------------------------------------------------------------------------------
//Fonction 	: obtenirNodeEditeur()
//Description	: Obtenir un node de l'éditeur par id
//------------------------------------------------------------------------------
function obtenirNodeEditeur(idMarque) {

	journaliser("obtenirNodeEditeur() Obtenir le node pour la marque :'" + idMarque + "'");
	
	// Sélectionner tous les éléments de type span
	var spanElems = ed.dom.select('span');
	node = null;
	
	// Parcourir la liste
	i = 0;
	tinymce.each(spanElems, function(spanElem) {
		i++;
	    journaliser("obtenirNodeEditeur() Élément outter:'" + spanElem.outerHTML + "'");
	    journaliser("obtenirNodeEditeur() Élément inner:'" + spanElem.innerHTML + "'");
	
	    // Vérifier si on a un match sur l'id
	    journaliser("obtenirNodeEditeur() Vérifier node courant : '" + spanElem.id + "' versus node recherché : '" + idMarque + "'");
	    if (spanElem.id == idMarque) {
	    	journaliser("obtenirNodeEditeur() MATCH!");
	    	node = spanElem;
	    }
	});
	
	return node;
}


//------------------------------------------------------------------------------
// Fonction 	: afficherSection(id)
// Description	: Afficher la section
//------------------------------------------------------------------------------			 
function afficherSection(id) {
	
	// Obtenir les id
	idSection = "#" + id;
	idSectionOuvert = idSection + "Ouvert";
	idSectionFerme = idSection + "Ferme";
	
	// Afficher la bonne section
	$(idSectionOuvert).show();
	$(idSectionFerme).hide();
	
	// Prendre note de la section actuellement ouverte
	document.frm.section.value = id;
	
	resizePanels();
}


//------------------------------------------------------------------------------
// Fonction 	: fermerSection(id)
// Description	: Fermer la section
//------------------------------------------------------------------------------			 
function fermerSection(id) {
	
	idSection = "#" + id;
	idSectionOuvert = idSection + "Ouvert";
	idSectionFerme = idSection + "Ferme";
	
	$(idSectionOuvert).hide();
	$(idSectionFerme).show();

	resizePanels();
}


//------------------------------------------------------------------------------
// Fonction 	: changerSection(id)
// Description	: Sauvegarder les données et changer la section
//------------------------------------------------------------------------------			 
function changerSection(id) {
	
	// Désactiver le flag des modifications
	flagModifications = false;
	
	// Transmettre la section demandée et rafraîchir la page
	document.frm.section.value = id;
	document.frm.demande.value = "item_changer_section";
	document.frm.submit();
}


//------------------------------------------------------------------------------
// Fonction 	: supprimerHTML(html)
// Description	: Supprimer les tags HTML
//------------------------------------------------------------------------------			 
function supprimerHTML(html) {
   var tmp = document.createElement("DIV");
   tmp.innerHTML = html;
   return tmp.textContent||tmp.innerText;
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerFinLigne(html)
// Description	: Supprimer les tags <br>
//------------------------------------------------------------------------------			 
function supprimerFinLigne(html) {
	journaliser("HTML:'"+ html + "'");
	return html.replace(/<br>/g," ");
}

// ------------------------------------------------------------------------------
// Fonction 	: checkedAll(champs)
// Description	: Cocher/Decocher toutes les cases a cocher passees en parametres (name="")
// ------------------------------------------------------------------------------			 
checked=false;
function checkedAll (champs) {
	//var frm= document.getElementById(frm1);
	if (checked == false) { checked = true; }
	else { checked = false }
	for (var i =0; i < champs.length; i++)  {
		champs[i].checked = checked;
	}
}

// ------------------------------------------------------------------------------
// Fonction 	: showSection(nom-lien, nom-section, id)
// Description	: Montrer la section requise et Cacher les autres
// ------------------------------------------------------------------------------			 
function showSection(lien, section, id) {
	if (lien != "") { 
		for (var i = 1; i<=3; i++) {
			if (document.getElementById(lien+i)) {
				document.getElementById(lien+i).className = "ongletInactif";
			}
			if (document.getElementById(section+i)) {
				document.getElementById(section+i).style.display = "none";
			}
		}
		document.getElementById(lien+id).className = "ongletActif";
		document.getElementById(section+id).style.display = "block";
	}
	else { /* Une seule section - pas d'onglets */
		document.getElementById(section+id).style.display = "block";
	}
	resizePanels();
}

// ------------------------------------------------------------------------------
// Fonction 	: toggleCadre(id-lien, id-cadre)
// Description	: Montrer/Cacher le lien et son cadre correspondant 
// ------------------------------------------------------------------------------			 
function toggleCadre(idlien, idcadre) {
	if (document.getElementById(idcadre)) {
		if (document.getElementById(idcadre).style.display == "none") {
			document.getElementById(idlien).style.display = "none";
			document.getElementById(idcadre).style.display = "inline-block";
		} else {
			document.getElementById(idcadre).style.display = "none";
			document.getElementById(idlien).style.display = "inline-block";
		}
		resizePanels();
	} else {
		journaliser("Un cadre n'existe pas (idlien = '" + idlien + "' idcadre = '" + idcadre + "')");
	}
}


//------------------------------------------------------------------------------
// Fonction 	: changeFancybox(urlData)
// Description	:  Fonction pour changer dynamiquement le contenu de la fenêtre 
//                 Fancybox - Utilisé pour la section Aide 
//------------------------------------------------------------------------------	
function changeFancybox(urlData) {
	$.ajax({
		url		: urlData,
		data	: $(this).serializeArray(),
		success: function(data) { 
			$.fancybox(data,{
				'hideOnContentClick': false,
				'hideOnOverlayClick' : false,
				'transitionIn'	: 'fade',
				'transitionOut'	: 'fade',
				'scrolling' : 'auto',
				'padding' : 0,
				'overlayOpacity': 0
			});
		}
	});
}

//------------------------------------------------------------------------------
// Fonction 	: document.ready
// Description	:  Définition des fenêtres Fancybox 
//------------------------------------------------------------------------------
$(document).ready(function() {
	// Fenetre jaillissante pour du contenu texte uniquement
	$(".lnk-fancybox").fancybox({
		'hideOnContentClick': false,
		'hideOnOverlayClick' : false,
		'transitionIn'	: 'fade',
		'transitionOut'	: 'fade',
		'scrolling' : 'auto',
		'padding' : 0,
		'overlayOpacity': 0
	});

	// Fenetre jaillissante standard pour ajouter des médias
	$(".fenetreStd").fancybox({
		'width' : 900,
		'height' : 528,
		'autoScale' : false,
		'hideOnContentClick': false,
		'hideOnOverlayClick' : false,
		'transitionIn'	: 'fade',
		'transitionOut'	: 'fade',
		'scrolling' : 'no',
		'padding' : 0,
		'overlayOpacity': 0.2,
		'showCloseButton': false,
		'type' : 'iframe'
 	});	

	// Fenetre jaillissante standard pour ajouter des médias dans l'éditeur
	$(".fenetreEditeurMedia").fancybox({
		'width' : 900,
		'height' : 528,
		'autoScale' : false,
		'hideOnContentClick': false,
		'hideOnOverlayClick' : false,
		'transitionIn'	: 'fade',
		'transitionOut'	: 'fade',
		'scrolling' : 'no',
		'padding' : 0,
		'overlayOpacity': 0.2,
		'showCloseButton': false,
		'type' : 'iframe'
 	});	
	
	
	// Fenetre jaillissante pour importer des items de la biblio
	$(".fenetreSelItems").fancybox({
		'width' : 900,
		'height' : 528,
		'autoScale' : false,
		'hideOnContentClick': false,
		'hideOnOverlayClick' : false,
		'transitionIn'	: 'fade',
		'transitionOut'	: 'fade',
		'scrolling' : 'no',
		'padding' : 0,
		'overlayOpacity': 0.2,
		'showCloseButton': false,
		'type' : 'iframe'
	 });
	
	// Fenetre jaillissante pour selectionner un questionnaire
	$(".fenetreSelQuest").fancybox({
		'width' : 900,
		'height' : 528,
		'autoScale' : false,
		'hideOnContentClick': false,
		'hideOnOverlayClick' : false,
		'transitionIn'	: 'fade',
		'transitionOut'	: 'fade',
		'scrolling' : 'no',
		'padding' : 0,
		'overlayOpacity': 0.2,
		'showCloseButton': false,
		'type' : 'iframe'
	 });
	
	// Fenetre jaillissante pour gestion de la session
	$(".fenetreGestionSession").fancybox({
		'width' : 500,
		'height' : 208,
		'autoScale' : false,
		'hideOnContentClick': false,
		'hideOnOverlayClick' : false,
		'transitionIn'	: 'fade',
		'transitionOut'	: 'fade',
		'scrolling' : 'no',
		'padding' : 0,
		'overlayOpacity': 0.2,
		'showCloseButton': false,
		'type' : 'iframe',
	 });	

});


// ------------------------------------------------------------------------------
// Fonction 	: Image MouseOver / MouseOut
// Description	: Modifier l'image sur le mouseOver et le mouseOut
// ------------------------------------------------------------------------------			 

$(document).ready(function(){
	$(".icDelete").hover(function() {
		$(this).attr("src","../images/ic-delete-over.png");
	}, function() {
		$(this).attr("src","../images/ic-delete.png");
	});
	$(".icAdd").hover(function() {
		$(this).attr("src","../images/ic-add-over.png");
	}, function() {
		$(this).attr("src","../images/ic-add.png");
	});
});


//------------------------------------------------------------------------------
// Fonction 	: ajouterClasseurElement()
// Description	: Ajouter un élément au classeur
//------------------------------------------------------------------------------
function ajouterClasseurElement(classeur) {

	// Prendre note du classeur
	document.frm.classeur.value = classeur;

	// Soumettre la page
	flagModifications = false;
	document.frm.demande.value="item_ajouter_classeur_element";
	document.frm.submit();	

}

//------------------------------------------------------------------------------
// Fonction 	: supprimerClasseurElements()
// Description	: Supprimer un ou plusieurs éléments du classeur
//------------------------------------------------------------------------------
function supprimerClasseurElements(classeur) {

	// Prendre note du classeur
	document.frm.classeur.value = classeur;

	// Soumettre la page
	flagModifications = false;
	document.frm.demande.value="item_supprimer_classeur_elements";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerClasseurElement()
// Description	: Supprimer un élément du classeur
//------------------------------------------------------------------------------
function supprimerClasseurElement(idClasseur, idElement) {

	// Prendre note du classeur
	document.frm.classeur.value = idClasseur;
	document.frm.element.value = idElement;

	// Soumettre la page
	flagModifications = false;
	document.frm.demande.value="item_supprimer_classeur_element";
	document.frm.submit();	
}

//------------------------------------------------------------------------------
// Fonction 	: afficherEditeurElement()
// Description	: Afficher un éditeur en particulier
//------------------------------------------------------------------------------
function afficherEditeurElement(id) {
		
	
	idEditeur = "#editeur_" + id;
	idLigne = "#ligne_" + id;
	
	// Enlever le highlight précédent
	idLignePrec = "#ligne_" + document.frm.elementEditeur.value;
	$(idLignePrec).removeClass("actif");
	
	// Highlight de la ligne sélectionné
	$(idLigne).addClass("actif");
		
	// Mettre à jour le texte de l'élément (titre de l'éditeur)
	majTexteElement(id, false);
	
	// Conserver la valeur pour ouvrir l'éditeur après sauvegarde
	document.frm.elementEditeur.value = id;
	
	// Cacher tous les éditeurs
	$('.cadre').hide();
	
	// Montrer l'éditeur sélectionné
	$(idEditeur).show();
	
	resizePanels();
}

//------------------------------------------------------------------------------
// Fonction 	: majTexteElement(id)
// Description	: Mettre à jour le texte de l'élément
//------------------------------------------------------------------------------
function majTexteElement(id, majLien) {
	
	// Obtenir le texte
	cleTexte = "#" + id + "_texte";
	txt = $(cleTexte).val();

	if (txt != undefined) {
		// Enlever les fins de ligne
		txt = txt.replace(/(\r\n|\n|\r)/gm," ");
		
		// Mettre à jour le texte du lien
		if (majLien) {
			cleLien = "#" + "lien_" + id;
			$(cleLien).text(txt);
		}
		
		// Mettre à jour le titre de l'élément en haut de l'éditeur
		cleTitre = "#" + "titre_" + id;
		$(cleTitre).text(txt);
	}
}


//------------------------------------------------------------------------------
// Fonction 	: ajouterLacune()
// Description	: Ajouter une lacune
//------------------------------------------------------------------------------
function ajouterLacune() {

	// Déterminer l'éditeur
	ed = $('#item_solution').tinymce();
	
	// Obtenir le contenu sélectionné
	lacuneTexte = ed.selection.getContent({format : 'text'});
	
	// Si du texte est sélectionné, action... sinon on ne fait rien
	if (lacuneTexte != "") {

		// Obtenir le html de la lacune
		lacune = ed.selection.getContent({format : 'text'});
		journaliser("ajouterLacune() sélection : '" + lacune + "'");

		// Déterminer la lacune en cours de modification
		journaliser("ajouterLacune() lacuneOuverteDansEditeur : '" + lacuneOuverteDansEditeur + "'");
		
		// Obtenir l'id de la lacune, au besoin créer un nouvel id
		if (lacuneOuverteDansEditeur == "" || lacuneOuverteDansEditeur == "0") {
			
			// Obtenir le prochain id
			noLacune = nbLacunes + 1;
			idLacune = "lacune_" + noLacune; 
			journaliser("ajouterLacune() Générer un nouveau id lacune : '" + idLacune + "'");
			
		} else {
			idLacune = lacuneOuverteDansEditeur;
			journaliser("ajouterLacune() Utiliser la lacune ouverte dans l'éditeur : '" + idLacune + "'");
		}
		
		// Noter comme la lacune ouverte dans l'éditeur
		lacuneOuverteDansEditeur = idLacune;
		
		// Créer la lacune
		journaliser("ajouterLacune() Utiliser la lacune suivante pour créer le span : '" + idLacune + "'");
		lacuneHTML = "<span id='lacune_9999' class='lacune-glisser-deposer'>" + lacune + "</span>"; 
		
	    // Récupérer le node
		if (lacune != "") {
			// 1. Via sélection si possible
			journaliser("ajouterLacune() Obtenir le node via selection");
			node = ed.selection.getNode();	
		} else {
			// 2. Sinon via le DOM
			node = obtenirNodeEditeur(idLacune);
			journaliser("ajouterLacune() Obtenir le node via DOM");
		}

		// Ajouter le HTML
		ed.selection.setContent(lacuneHTML);
		
		// Envoi du formulaire au serveur
		flagModifications = false;
		document.frm.lacune_texte.value = lacune;
		document.frm.lacune.value = idLacune
		document.frm.demande.value = "item_ajouter_lacune";
		document.frm.submit();
	}
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerLacunes()
// Description	: Supprimer les lacunes dans la sélection
//------------------------------------------------------------------------------
function supprimerLacunes() {

	// Déterminer l'éditeur
	ed = $('#item_solution').tinymce();
	
	// Obtenir le contenu sélectionné
	var contenuSel = ed.selection.getContent();
	journaliser("supprimerLacunes() contenu :'" + contenuSel + "'");

	// Sélectionner tous les éléments de type span
	var spanElems = ed.dom.select('span');

	// Parcourir la liste et enlever les spans
	tinymce.each(spanElems, function(spanElem) {
		journaliser("supprimerLacunes() found outter:'" + spanElem.outerHTML + "'");
	    journaliser("supprimerLacunes() found inner:'" + spanElem.innerHTML + "'");
	    journaliser("supprimerLacunes() found id : '" + spanElem.id + "'");
	    
	    // Vérifier que le span est pour une lacune
	    spanLong = spanElem.id.length;
	    spanPrefix = spanElem.id.substring(0,6);
	    journaliser("supprimerLacunes() spanLong : '" + spanLong + "'  spanPrefix : '" + spanPrefix + "'");
	    
	    if (spanLong > 6 && spanPrefix == "lacune") {
	    
		    // Vérifier si le span est dans la sélection, si oui, supprimer
		    spanRech = 'id="' + spanElem.id + '"';
		    if (contenuSel.indexOf(spanRech) > 0) {
		    	journaliser("supprimerLacunes() suppression du span id : '" + spanElem.id + "'");
			    spanElem.outerHTML = "";
		    }
	    }
	});
	
	// Mettre à jour les informations au serveur
	flagModifications = false;
	document.frm.demande.value = "item_supprimer_lacunes";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: afficherEditeurLacune()
// Description	: Afficher un éditeur de lacune en particulier
//------------------------------------------------------------------------------
function afficherEditeurLacune(id) {
	
	if (id != "") {
		idEditeur = "#editeur_" + id;
		
		journaliser("afficherEditeurLacune() id : '" + idEditeur + "'");
		
		// Cacher tous les éditeurs
		$('.cadreEditeur').hide();
		
		// Montrer l'éditeur sélectionné
		$(idEditeur).show();
		
		resizePanels();
		
		// Déplacer la page pour édition du contenu
		if ($(idEditeur).length > 0) {
		    $(document).scrollTop( $(idEditeur).offset().top );
		}
	}
}


//------------------------------------------------------------------------------
// Fonction 	: modifierLacune()
// Description	: Modifier une lacune
//------------------------------------------------------------------------------
function modifierLacune(node) {
        
	// Fermer tous les éditeurs
	$('.cadreEditeur').hide();

	// Obtenir le span courant
	lacuneTexte = node.innerHTML;
	lacuneHTML = node.outerHTML;
	lacuneId = node.id;

	// Identifier la lacune en édition
	lacuneOuverteDansEditeur = lacuneId;
	journaliser("modifierLacune() lacuneId : '" + lacuneId + "'");
	
	// Ouvrir l'éditeur de lacune
	idEditeur = "#editeur_" + lacuneId;
	journaliser("modifierLacune() ouvrir éditeur : '" + idEditeur + "'");
	$(idEditeur).show();

	// Déplacer la page pour édition du contenu
	if ($(idEditeur).length > 0) {
	    $(document).scrollTop( $(idEditeur).offset().top );
	}

	// Ajuster la fenêtre
	resizePanels();
	
	//ed = $('#item_solution').tinymce();
	//try { ed.selection.select(node); } catch(e) {};	

}


//------------------------------------------------------------------------------
// Fonction 	: supprimerLacune()
// Description	: Supprimer une lacune
//------------------------------------------------------------------------------
function supprimerLacune(idLacune) {

	journaliser("supprimerLacune() idLacune : '" + idLacune + "'");
	
	// Déterminer l'éditeur
	ed = $('#item_solution').tinymce();
	
	// Sélectionner tous les éléments de type span
	var spanElems = ed.dom.select('span');

	// Parcourir la liste et enlever la lacune sélectionnée
	tinymce.each(spanElems, function(spanElem) {
		journaliser("supprimerLacune() found outter:'" + spanElem.outerHTML + "'");
	    journaliser("supprimerLacune() found inner:'" + spanElem.innerHTML + "'");
	    journaliser("supprimerLacune() found id : '" + spanElem.id + "'");
	    
	    // Vérifier si le span est dans la sélection, si oui, supprimer
	    spanRech = 'id="' + spanElem.id + '"';
	    if (spanElem.id == idLacune) {
	    	journaliser("supprimerLacune() suppression du span id : '" + spanElem.id + "'");
		    spanElem.outerHTML = "";
	    }
	});
	
	// Mettre à jour les informations au serveur
	flagModifications = false;
	document.frm.demande.value = "item_supprimer_lacunes";
	document.frm.submit();	
}


//------------------------------------------------------------------------------
// Fonction 	: ajouterLacuneReponse(element)
// Description	: Ajouter un élément de réponse
//------------------------------------------------------------------------------
function ajouterLacuneReponse(element) {
	journaliser("ajouterLacuneReponse() element = '" + element + "'");
	flagModifications = false;
	document.frm.element.value=element;
	document.frm.demande.value="item_modifier_ajouter_lacune_reponse";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerLacuneReponse(element)
// Description	: Supprimer un élément de réponse
//------------------------------------------------------------------------------
function supprimerLacuneReponse(element) {
	journaliser("supprimerLacuneReponse() element = '" + element + "'");
	flagModifications = false;
	document.frm.element.value=element;
	document.frm.demande.value="item_modifier_supprimer_lacune_reponse";
	document.frm.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: reinitialiserRecherche()
// Description	: Réinitialiser la recherche pour un ou des médias
//------------------------------------------------------------------------------
function reinitialiserRecherche() {
	document.frmRech.demande.value = 'media_selectionner_recherche_initialiser';
	document.frmRech.submit();
}

//------------------------------------------------------------------------------
// Fonction 	: calculerPositionZone()
// Description	: Calculer l'emplacement d'une zone 
//------------------------------------------------------------------------------
function calculerPositionZone(idZone) {

	// Position de l'image
	var imgCoord = $("#image_zone").offset();
	var imgLeft = imgCoord.left;
	var imgTop = imgCoord.top; 
	
	// Afficher la position
	var posCoord = $(idZone).offset();
	var posLeft = posCoord.left;
	var posTop = posCoord.top;

	// Position calculée
	var posX = Math.round(posLeft - imgLeft);
	var posY = Math.round(posTop - imgTop);
	
	journaliser("IMG: \nLeft: "+ imgLeft + "\nTop: " + imgTop + "\nPOS: \nLeft: "+ posLeft + "\nTop: " + posTop + "\n");
	journaliser("CALC: \nX: "+ posX + "\nY: " + posY);
	journaliser("ID : '" + idElem + "'\n");

	// Assigner les valeurs
	var idElemX = idZone + "_coordonnee_x";
	var idElemY = idZone + "_coordonnee_y";
	journaliser("elem x : '" + idElemX + "'\n");
	journaliser("elem y : '" + idElemY + "'\n");
	$(idElemX).val(posX);
	$(idElemY).val(posY);

}

//------------------------------------------------------------------------------
//Fonction 	: ajouterCollaborateur()
//Description	: Ajouter un collaborateur 
//------------------------------------------------------------------------------
function ajouterCollaborateur(pos) {
	
	journaliser("ajouterCollaborateur : " + pos);
	
	flagModifications = false;
	
	document.frm.demande.value = "projet_modifier_ajouter_collaborateur";
	document.frm.projet_collaborateur.value = pos;
	document.frm.submit();
	
}

//------------------------------------------------------------------------------
// Fonction 	: supprimerCollaborateur()
// Description	: Supprimer un collaborateur 
//------------------------------------------------------------------------------
function supprimerCollaborateur(pos) {
	
	journaliser("supprimerCollaborateur : " + pos);
	
	flagModifications = false;
	
	document.frm.demande.value = "projet_modifier_supprimer_collaborateur";
	document.frm.projet_collaborateur.value = pos;
	document.frm.submit();

}

//------------------------------------------------------------------------------
// Fonction 	: verifierLienActif(element)
// Description	: Vérifier si le lien est actif 
//------------------------------------------------------------------------------
function isLienActif(element) {
	
	if ($(element).parent().hasClass("inactif")) {
		return false;
	} else {
		return true;
	}
}

//------------------------------------------------------------------------------
// Fonction 	: changerProjet(urlProjet)
// Description	: Changer le projet actif 
//------------------------------------------------------------------------------
function changerProjet(urlProjet) {

	document.location = urlProjet;
	
}

//------------------------------------------------------------------------------
// Fonction 	: selectionnerTypeDefinition(type)
// Description	: Séléctionner le type de définition si le champ est disponible
//------------------------------------------------------------------------------
function selectionnerTypeDefinition(type) {

	// Vérifier que le champ existe avant d'essayer de le modifier
	if ($('input[type=radio][name=terme_type_definition]', '#frm').length) {
	   $('input[type=radio][name=terme_type_definition][value=' + type + ']', '#frm').prop('checked', true);
	}
	
}
		
//------------------------------------------------------------------------------
// Fonction 	: supprimerTerme()
// Description	: Supprimer un terme
//------------------------------------------------------------------------------
function supprimerTerme(message, idTerme) {
	
	if (confirm(message)) {
		document.location = "bibliotheque.php?demande=terme_supprimer&terme_id_terme=" + idTerme;
	}
}


//------------------------------------------------------------------------------
// Fonction 	: modifierTermeSelectionne()
// Description	: Modifier un terme sélectionné
//------------------------------------------------------------------------------
function modifierTermeSelectionne() {
	
	// Déterminer le terme sélectionné
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();
	
	document.location = "bibliotheque.php?demande=terme_modifier&terme_id_terme=" + val ;
}


//------------------------------------------------------------------------------
// Fonction 	: desactiverPublication()
// Description	: Désactiver la publication d'un questionnaire
//------------------------------------------------------------------------------
function desactiverPublication(idQuest, idItem, demandeRetour, message) {
	if (confirm(message)) {
		url = "questionnaires.php?demande=questionnaire_desactiver&questionnaire_id_questionnaire=" + idQuest + "&item_id_item=" + idItem +"&demandeRetour=" + demandeRetour;
		document.location = url;
	}
}


//------------------------------------------------------------------------------
// Fonction 	: modifierUtilisateurSelectionne()
// Description	: Modifier un utilisateur sélectionné
//------------------------------------------------------------------------------
function modifierUtilisateurSelectionne() {
	
	// Déterminer l'utilisateur sélectionné
	var val = $('input:checkbox:checked.selectionElement').map(function () { 
		  return this.value; 
	}).get();
	
	document.location = "admin.php?demande=utilisateur_modifier&usager_id_usager=" + val ;
}

