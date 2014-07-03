 // Si pas de lexique, mettre seulement ceci dans le fichier.
 var lexiqueObj;
 
 /* Sinon, composer le lexique de la manière suivante (en répétant autant d'expressions que nécessaire.
 var lexiqueObj = 

 {
 	elements: 
 	[
 	
   	 {
         "expression": "<texte de l'expression>", // Expression à détecter dans les textes, non sensible à la case (Québec = québec = QUÉBEC)
         "variantes" : "<variante1>||<variante2>||<variante3>...", //Expressions à détecter aussi, mais le contenu qui s'affichée est celui de "expression"
         "type": "<valeur>", // Type de contenu (valeurs possibles: texte, image, son, video, lien)
         "contenu": "<contenu>", // Selon le type, il peut s'agir d'un texte (avec ou sans style), du nom du fichier image, son ou vidéo, ou d'une adresse URL http ou https.
         "localisation": 1 // Pour contenu de type vidéo, images et sons, 1 = local, 2 = distant. Si le contenu est de type "texte", mettre 1; s'il est de type "lien", mettre 2.
        }, // Pour la dernière expression, enlever la virgule
   ]
 };
*/
