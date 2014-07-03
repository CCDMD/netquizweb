tinyMCE.init({
	// Options générales
	mode : "specific_textareas",
	editor_selector : "editeur",
	theme : "advanced",
	width : "100%",
	min_height : 40,
	language : 'fr',
	content_css : "../css/editeur-nqw.css",
	skin : "o2k7",
	skin_variant : "silver-nqw",
	forced_root_block : false,
	convert_urls: false,
	plugins : "pagebreak,style,table,save,advhr,advimage,advlink,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,fullscreen",

	// Options pour le thème
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,nonbreaking,|,closeeditor,|,fullscreen,",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "none",
	theme_advanced_path : false,
	theme_advanced_resizing : false,
    theme_advanced_resizing_use_cookie : false,
	fullscreen_new_window : false,
	fullscreen_settings : {
		theme : "advanced",
		forced_root_block : false,
		plugins : "pagebreak,style,table,save,advhr,advimage,advlink,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,|,fullscreen,",
		theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,nqwmedia,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,charmap,nonbreaking,|,media,advhr,",

		setup : function(ed) {
			
			// Enregistrer les modifications
			ed.onKeyUp.add(function(ed, e) {
				flagModifications = true;
			});
			
			// Bouton pour ajouter une image de NQW
		     ed.addButton('nqwmedia', {
		        title : 'Ajouter un média',
		        image : '../images/btn-importer-media.gif',
		        onclick : function() {
		        	journaliser("Ajout d'un média. Texte sélectionné : '" + ed.selection.getContent({format : 'text'}) + "'");
		        	
		        	// Prendre note de l'éditeur
		        	edSel = ed;
		        	
		        	// Diriger la sélection vers l'éditeur
		        	ouvrirSelectionMediaEditeur("item_editeur_media");
		        	
		        	// Ouvrir la fenêtre de sélection
		        	$(".fenetreEditeurMedia").trigger('click');
		        	$(".fenetreEditeurMedia").css('z-index','5000000');
		        	
		        }
		     });
		}
	},
	onchange_callback : "myCustomOnChangeHandler",
	setup : function(ed) {

		// Préparer les placeholders
		var tinymce_placeholder = $('#' + ed.id);
		var attr = tinymce_placeholder.attr('placeholder');

		// Vérifier l'attribut pour les placeholders
		if (typeof attr !== 'undefined' && attr !== false) {
			var is_default = false;

			ed.onInit.add(function(ed) {

				// Obtenir le contenu de l'éditeur
				var cont = ed.getContent();

				// Si le contenu est vide, le remplacer par la
				// valeur par défaut
				longueur = cont.length;
				if (cont.length == 0) {
					ed.setContent(getPlaceHolder(tinymce_placeholder.attr("placeholder")));

					// Obtenir le conteu
					cont = tinymce_placeholder.attr("placeholder");
				}

				// Obtenir le contenu de l'éditeur et vérifier
				// si c'est le placeholder
				var contenu = decodeEntities(ed.getContent());
				var attribut = decodeEntities(getPlaceHolder(tinymce_placeholder
						.attr("placeholder")));
				is_default = (contenu == attribut);

				if (!is_default) {
					return;
				}
			});

			// Clic sur le champ --> Remplacer le contenu du
			// placeholder par vide
			ed.onMouseDown.add(function(ed, e) {
				
				// Ouvrir seulement la barre d'outil pour l'éditeur en cours d'utilisation
				fermerEditeurs();
				$("#" + ed.editorId + "_toolbargroup").show();
				resizePanels();				
				
				// Obtenir le contenu de l'éditeur et vérifier
				// si c'est le placeholder
				var contenu = decodeEntities(ed.getContent());
				var attribut = decodeEntities(getPlaceHolder(tinymce_placeholder.attr("placeholder")));
				is_default = (contenu == attribut);

				// Si c'est la valeur par défaut l'enlever
				if (is_default) {
					ed.setContent('');
				}
			});
			

			// Focus sur le champ --> Remplacer le contenu du
			// placeholder par vide
			ed.onActivate.add(function(ed) {
			
				// Ouvrir seulement la barre d'outil pour l'éditeur en cours d'utilisation
				fermerEditeurs();
				$("#" + ed.editorId + "_toolbargroup").show();
				resizePanels();				
				
				// Obtenir le contenu de l'éditeur et vérifier
				// si c'est le placeholder
				var contenu = decodeEntities(ed.getContent());
				var attribut = decodeEntities(getPlaceHolder(tinymce_placeholder.attr("placeholder")));
				is_default = (contenu == attribut);

				// Si c'est la valeur par défaut l'enlever
				if (is_default) {
					ed.setContent('');
				}
			});
	
			// On quitte le champ --> vérifier si on doit remettre le
			// placeholder
			ed.onDeactivate.add(function(ed, evt) {
				tinymce.dom.Event.add(ed.getWin(), 'blur', function(e) {
					var cont = ed.getContent();
					if (cont.length == 0) {
						ed.setContent(getPlaceHolder(tinymce_placeholder.attr("placeholder")));
					}
				});
			});

			// On quitte le champ --> vérifier si on doit remettre le
			// placeholder
			ed.onInit.add(function(ed, evt) {
				tinymce.dom.Event.add(ed.getWin(), 'blur', function(e) {
					var cont = ed.getContent();
					if (cont.length == 0) {
						ed.setContent(getPlaceHolder(tinymce_placeholder.attr("placeholder")));
					}
				});
			});
		}

		ed.onInit.add(function(ed, evt) {

			var dom = ed.dom;
			var s = ed.settings;

			// Ajouter des listeners d'évènements
			tinymce.dom.Event.add(ed.getDoc(), 'mousedown',
					function(e) {
						fermerEditeurs();
						$("#" + ed.editorId + "_toolbargroup").show();
						resizePanels();
					});

			// Cacher la barre d'outil au démarrage
			if (!isPleinEcran()) {
				TBG = "#" + ed.editorId + "_toolbargroup";
				// journaliser("Fermer initialement " + TBG);
				$(TBG).css('display', 'none');
			} 

		}); // Fin de ed.onInit.add()

		// Enregistrer les modifications
		ed.onKeyUp.add(function(ed, e) {
			flagModifications = true;
			
			// Pour la page modifier un terme
			selectionnerTypeDefinition("texte");
		});

		
		// Ajouter un bouton pour fermer la barre d'outil, sauf en mode
		// plein-écran
		if (!isPleinEcran()) {
			ed.addButton('closeeditor', {
				title : 'Fermer',
				image : '../images/fermer_editeur.gif',
				onclick : function() {
					TBG = "#" + ed.editorId + "_toolbargroup";
					// journaliser("Fermer initialement " + TBG);
					$(TBG).css('display', 'none');
					// Rafraîchir la taille des fenêtres
					// resizePanels();
				}
			});
		}
	}
});

tinyMCE.init({
	// Options générales pour un éditeur pour marquage
	mode : "specific_textareas",
	editor_selector : "editeur_marquage",
	theme : "advanced",
	width : "100%",
	min_height : 40,
	language : 'fr',
	content_css : "../css/editeur-nqw.css",
	skin : "o2k7",
	skin_variant : "silver-nqw",
	forced_root_block : false,
	invalid_elements : "p",
	plugins : "paste",
	oninit : "disableShortcuts",
	paste_auto_cleanup_on_paste : true,
	paste_remove_styles : true,
	paste_remove_styles_if_webkit : true,
	paste_strip_class_attributes : true,
	paste_preprocess : function(pl, o) {
		  // remove all tags => plain text
		  o.content = strip_tags( o.content,'' );
		},
	force_hex_style_colors : true,

	// Options du thème
	theme_advanced_toolbar_location : "none",
	theme_advanced_statusbar_location : "none",
	theme_advanced_path : false,
	theme_advanced_resizing : false,
	setup : function(ed) {

		// Conserver un lien sur l'éditeur courant
		edSel = ed;
		
		// Register dummy command to use it as place holder to stop shortcuts
        ed.addCommand('Dummy', function() {
	    });
        
		// Détecter les modifications et analyser les marques
		ed.onChange.add(function(ed, l) {
			journaliser('onChange() Modification détectée');
			analyserMarques();
		});

		// Détecter les touches pressées et analyser les marques
		ed.onKeyUp.add(function(ed, e) {
			journaliser('onKeyPress() Modification détectée');
			
			flagModifications = true;

			// Obtenir le node actuel
			node = ed.selection.getNode();

			if (node.nodeName == 'SPAN' && ed.dom.hasClass(node, 'marque')) {
				journaliser("onKeyPress() span détecté");

				// Sauvegarder les rétroactions
				enregistrerMarqueRetro();

				// Vider les champs rétro
				viderMarqueRetro()

				// Ouvrir ou mettre à jour l'éditeur
				modifierMarque(node);
			} else {
				// Fermer l'éditeur de marque
				fermerMarque();
			}

			analyserMarques();

		});

		// Détecter les clics
		ed.onMouseDown.add(function(ed, e) {
			if (e.target.nodeName == 'SPAN'	&& ed.dom.hasClass(e.target, 'marque')) {
				journaliser("onMouseDown() span détecté");

				// Sauvegarder les rétroactions
				enregistrerMarqueRetro();

				// Vider les champs rétro
				viderMarqueRetro()

				// Ouvrir le panneau d'édition
				modifierMarque(e.target);

			} else {

				// Aucun span détecté, fermer le cadre au besoin
				journaliser("onMouseDown aucun span");

				// Fermer le panneau d'édition d'une marque
				fermerMarque();
			}
			analyserMarques();
		});

	}
});

tinyMCE.init({

	// Options générales pour un éditeur de lacunes
	mode : "specific_textareas",
	editor_selector : "editeur_lacune",
	theme : "advanced",
	width : "100%",
	min_height : 40,
	language : 'fr',
	content_css : "../css/editeur-nqw.css",
	skin : "o2k7",
	skin_variant : "silver-nqw",
	forced_root_block : false,
	plugins : "paste,noneditable,pagebreak,style,table,save,advhr,advimage,advlink,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,fullscreen",
	force_hex_style_colors : true,

	// Options du thème
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,nonbreaking,|,forecolor,backcolor,|,closeeditor",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "none",
	theme_advanced_path : false,
	theme_advanced_resizing : false,
	fullscreen_new_window : false,
	fullscreen_settings : {
		theme : "advanced",
		forced_root_block : false,
		plugins : "pagebreak,style,table,save,advhr,advimage,advlink,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,|,fullscreen,",
		theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,charmap,nonbreaking,|,media,advhr"
	},
	paste_preprocess : function(pl, o) {
		// Enlever les spans
		o.content = o.content.replace(/<span.+?>.+<\/span>/ig, " ");
	},

	// Fonctions spécifiques
	setup : function(ed) {

		// Conserver un lien sur l'éditeur courant
		edSel = ed;

		// Détecter les clics
		ed.onMouseDown.add(function(ed, e) {
			if (e.target.nodeName == 'SPAN' && ed.dom.hasClass(e.target, 'lacune')) {
				journaliser("onMouseDown() span détecté");

				// Ouvrir le panneau d'édition
				journaliser("click sur : " + e.target);
				modifierLacune(e.target);

			} else {

				// Aucun span détecté, fermer les éditeurs
				journaliser("onMouseDown aucun span");

				// Fermer le panneau d'édition d'une marque
				$('.cadreEditeur').hide();
			}
		});

		ed.onInit.add(function(ed, evt) {

			var dom = ed.dom;
			var s = ed.settings;

			// Ajouter des listeners d'évènements
			tinymce.dom.Event.add(ed.getDoc(), 'mousedown',
					function(e) {
						fermerEditeurs();
						$("#" + ed.editorId + "_toolbargroup").show();
						resizePanels();
					});

			// Cacher la barre d'outil au démarrage
			if (!isPleinEcran()) {
				TBG = "#" + ed.editorId + "_toolbargroup";
				// journaliser("Fermer initialement " + TBG);
				$(TBG).css('display', 'none');
			}

		}); // Fin de ed.onInit.add()

		
		// Enregistrer les modifications
		ed.onKeyUp.add(function(ed, e) {
			flagModifications = true;
		});				
		
		// Ajouter un bouton pour fermer la barre d'outil, sauf en mode
		// plein-écran
		if (!isPleinEcran()) {
			ed.addButton('closeeditor', {
				title : 'Fermer',
				image : '../images/fermer_editeur.gif',
				onclick : function() {
					TBG = "#" + ed.editorId + "_toolbargroup";
					// journaliser("Fermer initialement " + TBG);
					$(TBG).css('display', 'none');
				}
			});
		}
	}
});


// Désactiver les raccourcis clavier
function disableShortcuts(){
	ed = $('#item_texte').tinymce();
	ed.addShortcut("ctrl+b","nix","Dummy");
    ed.addShortcut("ctrl+i","nix","Dummy");
    ed.addShortcut("ctrl+u","nix","Dummy");
}

// Détecter les modifications dans l'éditeur
function myCustomOnChangeHandler(inst) {
	//journaliser("change");
}

// Fermer tous les éditeurs dans le DOM
function fermerEditeurs() {
	// journaliser("fermerEditeurs() Début");
	$('textarea.editeur').each(function() {
		currentId = $(this).attr("id");
		fermerEditeur(currentId);
	});

	$('textarea.editeur_lacune').each(function() {
		currentId = $(this).attr("id");
		fermerEditeur(currentId);
	});

	// Rafraîchir la taille des fenêtres
	resizePanels();
}

// Rafraichir tous les éditeurs dans le DOM
function rafraichirEditeurs() {
		
//	$('textarea.editeur').each(function() {
//		currentId = $(this).attr("id");
//		tinymce.execCommand('mceRemoveControl', true, currentId);
//		tinymce.execCommand('mceAddControl', true, currentId);
//	});
	
//	$('textarea.editeur_lacune').each(function() {
//		currentId = $(this).attr("id");
//		tinymce.execCommand('mceRemoveControl', true, currentId);
//		tinymce.execCommand('mceAddControl', true, currentId);
//	});
	
//	$('textarea.editeur_marquage').each(function() {
//		currentId = $(this).attr("id");
//		tinymce.execCommand('mceRemoveControl', true, currentId);
//		tinymce.execCommand('mceAddControl', true, currentId);
//	});
	
	// Rafraîchir la taille des fenêtres
	resizePanels();
}



// Fermer l'éditeur passé en paramètre
function fermerEditeur(idElem) {
	idTB = "#" + idElem + "_toolbargroup";
	$(idTB).hide();
}

// Vérifier si on est en mode plein écran
function isPleinEcran() {

	var isPleinEcran = false;
	try {
		if (document.getElementById("mce_fullscreen_container")) {
			var rien = document.getElementById("mce_fullscreen_container").style.display;
			isPleinEcran = true;
		}
	} catch (e) {
	}

	return isPleinEcran;
}

// Retourner le text en format placeholder
function getPlaceHolder(txt) {

	$ph = "<!-- PHSTART --><span class=\"champPlaceholder\">" + txt
			+ "</span><!-- PHSTOP -->";
	return $ph;

}

var decodeEntities = (function() {
	// Créer un div temporaire pour décoder les entités HTML
	var element = document.createElement('div');

	function decodeHTMLEntities(str) {
		if (str && typeof str === 'string') {
			// strip script/html tags
			str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
			str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
			element.innerHTML = str;
			str = element.textContent;
			element.textContent = '';
		}

		return str;
	}

	return decodeHTMLEntities;
})();


// Enlève les tags HTML
// Fonction récupérée de : http://stackoverflow.com/questions/4122451/tinymce-paste-as-plain-text
function strip_tags (str, allowed_tags)
{

 var key = '', allowed = false;
 var matches = [];    var allowed_array = [];
 var allowed_tag = '';
 var i = 0;
 var k = '';
 var html = ''; 
 var replacer = function (search, replace, str) {
     return str.split(search).join(replace);
 };
 // Build allowes tags associative array
 if (allowed_tags) {
     allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
 }
 str += '';

 // Match tags
 matches = str.match(/(<\/?[\S][^>]*>)/gi);
 // Go through all HTML tags
 for (key in matches) {
     if (isNaN(key)) {
             // IE7 Hack
         continue;
     }

     // Save HTML tag
     html = matches[key].toString();
     // Is tag not in allowed list? Remove from str!
     allowed = false;

     // Go through all allowed tags
     for (k in allowed_array) {            // Init
         allowed_tag = allowed_array[k];
         i = -1;

         if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+'>');}
         if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+' ');}
         if (i != 0) { i = html.toLowerCase().indexOf('</'+allowed_tag)   ;}

         // Determine
         if (i == 0) {                allowed = true;
             break;
         }
     }
     if (!allowed) {
         str = replacer(html, "", str); // Custom replace. No regexing
     }
 }
 return str;
}
