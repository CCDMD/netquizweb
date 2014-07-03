// Nom du dossier contenant les médias du questionnaire
// ----------------------------------------------------------------------------------------------------------
var mediasFolder = 'medias'; //devrait rester invariable

// Médias de la page d'accueil (onglet Complément de l'item Page d'accueil)
// ----------------------------------------------------------------------------------------------------------
// Texte (champ Ajouter un texte -> titre + texte)
var textGuidelineWelcome = '<?php echo $quest->getJS("media_titre_texte") ?>'

// Image (champ Ajouter une image)
<?php if ($quest->getJS("media_image_fichier") != "") { ?>
var imagePath = '<?php echo $quest->getJS("media_image_fichier")?>';
var imageCategory = '<?php echo $quest->getJS("media_image_fichier_source")?>';
<?php } else { ?>
var imagePath;
<?php } ?>

// Son (champ Ajouter un son)
<?php if ($quest->getJS("media_son_fichier") != "") { ?>
var soundPath = '<?php echo $quest->getJS("media_son_fichier")?>';
var soundCategory = '<?php echo $quest->getJS("media_son_fichier_source")?>';
var autoplaySound = false;
var showSoundController = true;
<?php } else { ?>
var soundPath;
<?php } ?>

// Vidéo (champ Ajouter une vidéo)
<?php if ($quest->getJS("media_video_fichier") != "") { ?>
var videoPath = '<?php echo $quest->getJS("media_video_fichier")?>';
var videoCategory = '<?php echo $quest->getJS("media_video_fichier_source")?>';
var autoplayVideo = false;
var showVideoController = true;
<?php } else { ?>
var videoPath;
<?php } ?>


// Préparation des pages index.html (Page d'accueil), main.html (page des items) et printable.html (impression du tableau de résultats)
// (onglet Contenu de la page d'accueil du questionnaire)
// ----------------------------------------------------------------------------------------------------------
// Champ Titre du questionnaire de la page d'accueil 
var WM_QUIZ_TITLE = '<?php echo $quest->getJS("titre_long") ?>';

// Champ Mot de bienvenue
var W_INTRO = '<?php echo $quest->getJS("mot_bienvenue") ?>';

// Champ Note
var W_AVERTISSEMENT = '<?php echo $quest->getJS("note") ?>';

// Champ Générique
var W_CREDITS = '<?php echo $quest->getJS("generique") ?>';

// Champ Fonctionnalités/Commencer de la langue du questionnaire
var W_CONTINUE = '<?php echo $langue->getJS("fonctionnalites_commencer") ?>';

if (loadedWelcome == false) {
 jQuery(document).ready(function() {   
   load_welcome();
   loadedWelcome = true;
  });
}
else if (loadedWelcome == true) {
jQuery(document).ready(function() {
 	jQuery("#quiztitle").html('<a href="#" class="quiz_title_link" onclick="top.ccdmd.nq4.restartQuizFromWelcome();">' + WM_QUIZ_TITLE + '</a>');
});

document.title = WM_QUIZ_TITLE;


// Initialisation du questionnaire
// ----------------------------------------------------------------------------------------------------------
var gNQ4 = null;
window.onload = function(){
initLexiqueVariantes();	
var page = null;
indiceWindow = new DraggableWindow($('indice'),$('indicehandle'));
identFormWindow = new DraggableWindow($('resultIdentForm'),$('resulthandle'));
sendtoFormWindow = new DraggableWindow($('sendtoForm'),$('sendtohandle'));
gNQ4 = getNewNetquiz();
gNQ4.showNavTextBox = true; //Aucun paramètre ne permet de changer cette valeur
gNQ4.quizTitle = WM_QUIZ_TITLE; //Défini plus haut pour la page d'accueil


// Paramètres du questionnaire
// ----------------------------------------------------------------------------------------------------------
// Toujours 0 pour phase 1
// Pour phase 2: 0 = formatif; 1 = formatif avec soumission au serveur; 2 = sommatif
gNQ4.mode = 0;

// Champ Temps de réponse
gNQ4.answerTimerEnabled = <?php echo $quest->getJS("temps_reponse") ?>;

// Nombre de pages total du questionnaire
gNQ4.numberPagesQuiz = '<?php echo $quest->getJS("nb_items_total") ?>';

<?php if ($quest->get("generation_question_type") == "aleatoire" && $quest->get("generation_question_nb") != 0) { ?>
// Nombre de questions aléatoire - Inclure X items de Y
gNQ4.numberOfQuestions = '<?php echo $quest->get("generation_question_nb") ?>';
<?php } ?>


// Libellés généraux propres à la langue du questionnaire (ou de l'aperçu)
// ----------------------------------------------------------------------------------------------------------
// Champ Délimiteur de nombre
gNQ4.decimalSymbol = <?php echo $langue->getJS("delimiteur") ?>;

// BOUTONS
// ----------------------------------------------------------------------------------------------------------
// Champ Annuler
gNQ4.resultButtonCancel = '<?php echo $langue->getJS("boutons_annuler") ?>';

// Champ OK
gNQ4.resultButtonOK = '<?php echo $langue->getJS("boutons_ok") ?>';

// FENÊTRES (renseignements sur le répondant pour Imprimer et Envoyer par courriel)
// ----------------------------------------------------------------------------------------------------------
// Champ Renseignements sur le répondant
gNQ4.resultIdentTitle = '<?php echo $langue->getJS("fenetre_renseignements") ?>';

// Champ Nom
gNQ4.rifLblLastName = '<?php echo $langue->getJS("fenetre_nom") ?>';

// Champ Prénom
gNQ4.rifLblName = '<?php echo $langue->getJS("fenetre_prenom") ?>';

// Champ Matricule
gNQ4.rifLblCode = '<?php echo $langue->getJS("fenetre_matricule") ?>';

// Champ Groupe
gNQ4.rifLblGroup = '<?php echo $langue->getJS("fenetre_groupe") ?>';

// Champ Courriel
gNQ4.rifLblEmail = '<?php echo $langue->getJS("fenetre_courriel") ?>';

// Champ Autre
gNQ4.rifLblOther = '<?php echo $langue->getJS("fenetre_autre") ?>';

// Champ Envoi des résultats par courriel
gNQ4. rifLblTitleSendTo = '<?php echo $langue->getJS("fenetre_envoi") ?>';

// Champ Courriel du destinataire
gNQ4.rifLblSendTo = '<?php echo $langue->getJS("fenetre_courriel_destinataire") ?>';

// FONCTIONNALITÉ
// ----------------------------------------------------------------------------------------------------------
// Champ Effacer les marques
gNQ4.removeHiliteLabel = '<?php echo $langue->getJS("fonctionnalites_effacer") ?>';

// Champ Envoyer par courriel
gNQ4.resultButtonSendTo = '<?php echo $langue->getJS("fonctionnalites_courriel") ?>';

// Champ Imprimer
gNQ4.resultButtonPrint = '<?php echo $langue->getJS("fonctionnalites_imprimer") ?>';

// Champ Recommencer
gNQ4.resultButtonRedo = '<?php echo $langue->getJS("fonctionnalites_recommencer") ?>';

// Champ Reprendre
gNQ4.navbRedo = '<?php echo $langue->getJS("fonctionnalites_reprendre") ?>';

// Champ Résultats
gNQ4.navbResult = '<?php echo $langue->getJS("fonctionnalites_resultats") ?>';

// Champ Lexique
gNQ4.navbLexique = '<?php echo $langue->getJS("fonctionnalites_lexique") ?>';

// Champ Questionnaire
gNQ4.navbQuestionnaire = '<?php echo $langue->getJS("fonctionnalites_questionnaire") ?>';

// Champ Solution
gNQ4.navbSolution = '<?php echo $langue->getJS("fonctionnalites_solution") ?>';

// Champ Valider
gNQ4.navbValidate = '<?php echo $langue->getJS("fonctionnalites_valider") ?>';

// MENU DE NAVIGATION
// ----------------------------------------------------------------------------------------------------------
// Champ Page
gNQ4.pageLabel = '<?php echo $langue->getJS("navigation_page") ?>';

// Champ De (ex. 2 de 30)
gNQ4.navBarOf = '<?php echo $langue->getJS("navigation_de") ?>';

// MESSAGES GÉNÉRAUX (TOUT TYPE D'ITEM)
// ----------------------------------------------------------------------------------------------------------
// Champ Point
gNQ4.point = '<?php echo $langue->getJS("message_point") ?>';

// Champ Points
gNQ4.points = '<?php echo $langue->getJS("message_points") ?>';

// Champ Libellé Solution
gNQ4.solutionLabel = '<?php echo $langue->getJS("message_libelle_solution") ?>';

// MESSAGE PARTICULIERS (PROPRES À CERTAINS TYPES D'ITEMS)
// ----------------------------------------------------------------------------------------------------------
// Champ Message pour mots en trop (dictée)
gNQ4.sMsgMotsTrop = '<?php echo $langue->getJS("message_dictee_motsentrop") ?>';

// Champ Message pour mots mal orthographiés (dictée)
gNQ4.sMsgMotsMOrtho = '<?php echo $langue->getJS("message_dictee_orthographe") ?>';

// Champ Message pour mots manquants (dictée)
gNQ4.sMsgMotsManq = '<?php echo $langue->getJS("message_dictee_motsmanquants") ?>';

// Champ Message pour réponse suggérée (développement)
gNQ4.suggestionLabel = '<?php echo $langue->getJS("message_reponsesuggeree") ?>';

// TABLEAU DES RÉSULTATS
// ----------------------------------------------------------------------------------------------------------
// Première colonne du tableau = même libellé que pageLabel
gNQ4.resultHeaderCol0 = gNQ4.pageLabel;

// Champ Nombre d’essais
gNQ4.resultHeaderCol1 = '<?php echo $langue->getJS("resultats_nbessais") ?>';

// Champ Temps de réponse
gNQ4.resultHeaderCol2 = '<?php echo $langue->getJS("resultats_tempsdereponse") ?>';

// Champ Points
gNQ4.resultHeaderCol3 = '<?php echo $langue->getJS("resultats_points") ?>';

// Champ Statut
gNQ4.resultHeaderCol4 = '<?php echo $langue->getJS("resultats_statut") ?>';

// Champ À faire
gNQ4.statusToDo = '<?php echo $langue->getJS("resultats_afaire") ?>';

// Champ À reprendre
gNQ4.statusToRedo = '<?php echo $langue->getJS("resultats_areprendre") ?>';

// Champ Réussi
gNQ4.statusCompleted = '<?php echo $langue->getJS("resultats_reussi") ?>';

// Champ Sans objet
gNQ4.resultNoValue = '<?php echo $langue->getJS("resultats_sansobjet") ?>';

// Champ Message de confirmation pour la reprise du questionnaire
gNQ4.msgRestartQuiz = '<?php echo $langue->getJS("resultats_confirmation") ?>';

// Champ Message de confirmation pour le retour à la page d'accueil
gNQ4.msgRestartQuizWelcome = '<?php echo $langue->getJS("resultats_accueil") ?>';

// Champ Objet du courriel (concaténé avec nom questionnaire + nom répondant dans l'objet du courriel)
gNQ4.strObjet = '<?php echo $langue->getJS("resultats_objet_courriel") ?>';

// Champ Courriel envoyé avec succès
gNQ4.msgEmailOk = '<?php echo $langue->getJS("resultats_message_courriel_succes") ?>';

// Champ Erreur lors de l'envoi du courriel
gNQ4.msgEmailError = '<?php echo $langue->getJS("resultats_message_courriel_erreur") ?>';


// Fin du questionnaire
// ----------------------------------------------------------------------------------------------------------
// Champ Texte de Fin du questionnaire
gNQ4.resultOtherContent = '<?php echo $quest->getJS("texte_fin") ?>';



