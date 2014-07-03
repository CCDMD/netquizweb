var QuestionMarquage = Class.create({
    sQuestionType: 'MARQUAGE',
    
    sRepAct: '', //Réponse actuelle
    mustGiveAllGoodAnswers: false,

    currentSequence: null,
    currentScore: 0,
    ponderation: 0,
    triesCount: 0,
    status: '',
    statusMenuPages: -1,
    iFautePond: 0,
    removeHiliteLabel: '',
    nomTypeQuestion: 'marquage',
    debutSelection: 0,
    finSelection: 0,
    idOfSelectionNow: 0,
    
    initialize: function(quiz, page, questionNb){

        this.quiz = quiz;
        this.page = page;
        this.questionNb = questionNb;

        this.colors = new Array();
        this.colorsTag = new Array();
        this.retroPos = new Array();
        this.retroNeg = new Array();
        this.retroInc = new Array();

        this.choicesStart = new Array();
        this.choicesEnd = new Array();
        this.choicesColor = new Array();
        
        this.retroChoice = new Array();

        this.status = this.quiz.statusToDo;
    },

    addColor: function(color, tag, retroPos, retroNeg, retroInc){
        var strToEvaluate;
        
        color = color.toUpperCase();
        
        this.colors[this.colors.length] = color;
        this.colorsTag[this.colorsTag.length] = tag;
        this.retroPos[this.retroPos.length] = retroPos;
        this.retroNeg[this.retroNeg.length] = retroNeg;
        this.retroInc[this.retroInc.length] = retroInc;
        
        
        strToEvaluate = this.retroPos[this.retroPos.length - 1];
        this.retroPos[this.retroPos.length - 1] = evalStringForLexique(strToEvaluate);
        
        strToEvaluate = this.retroNeg[this.retroNeg.length - 1];
        this.retroNeg[this.retroNeg.length - 1] = evalStringForLexique(strToEvaluate);
        
        strToEvaluate = this.retroInc[this.retroInc.length - 1];
        this.retroInc[this.retroInc.length - 1] = evalStringForLexique(strToEvaluate);
    },

    addMainText: function(mainText){
        //MODIF 2012-06-04 : QUAND ON PREND jQuery().html, LE <br /> RETOURNE <br>. DEMANDER À LUCIE DE CHANGER.
        mainText = mainText.replace(/<br \/>/gi, "<br>");
        this.initialMainText = mainText;
    },

    addChoice: function(posStart, posEnd, color){
        if (color != '') {
            color = color.toUpperCase();
        
            this.choicesStart[this.choicesStart.length] = posStart;
            this.choicesEnd[this.choicesEnd.length] = posEnd;
            this.choicesColor[this.choicesColor.length] = color;
        }
    },
    
    addRetro: function(posStart, posEnd, color, retro){
        color = color.toUpperCase();
        this.retroChoice[this.retroChoice.length] = [posStart, posEnd, color, retro];
    },

    display : function(){
        var htmlColors = '';
        var putText = this.initialMainText;
        var strToEvaluate;
        
        if (this.sRepAct != ''){
           putText = this.sRepAct;
        }
        
        if (isMobile.any()) {
           jQuery("#wrapperMarquage").css('margin-top','23px');
        }

        var divAnswerChoices = document.createElement('div');
        divAnswerChoices.id = 'divAnswerChoices';
        $('question').appendChild(divAnswerChoices);

        for (i = 0; i < this.colors.length; i++) {
            htmlColors = htmlColors + '<div class="colorButtonsOuter" title="' + this.colorsTag[i] + '" ontouchstart="top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex].question.selectionMouseDown(\'' + this.colors[i] + '\')" onclick="top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex].question.selectionMouseDown(\'' + this.colors[i] + '\')"><div class="colorButtonsInner" style="background-color:#' + this.colors[i] + '"><img src="images/spacer.gif" width="19" height="19" border="0" /></div></div>';
            htmlColors = htmlColors + '<div style="width:10px;height:21px;float:left;"></div>';
        }

        htmlColors = htmlColors + '<div style="height:23px;float:left;"><table height="23" cellpadding="0" cellspacing="0" border="0"><tr><td valign="bottom"><a href="#" class="aMarquage" ontouchstart="top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex].question.selectionMouseDown(\'000000\')" onclick="top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex].question.selectionMouseDown(\'000000\')">' + this.removeHiliteLabel + '</a></td></tr></table></div>';

        $('divAnswerChoices').update(htmlColors);
        divAnswerChoices.style.clear = 'both';

        var divSpacer = document.createElement('div');
        divSpacer.id = 'divSpacer1';
        $('question').appendChild(divSpacer);
        divSpacer.style.height = '1px'
        divSpacer.style.clear = 'both';
        
        
        // Attendre de parler avec Lucie.
        //strToEvaluate = putText;
        //putText = evalStringForLexique(strToEvaluate);
        
        
        $('saq').hide();
        $('question').show();
        
        $('wrapperMarquage').show();
        $('containerMarquage').update(putText);
        $('containerMarquage').show();
    },
    
    getSelectionCharOffsetsWithin : function(element) {
        var start = 0;
        var end = 0;

        var sel, range, priorRange;

        if (typeof window.getSelection != "undefined") {
           range = window.getSelection().getRangeAt(0);

           priorRange = range.cloneRange();
           priorRange.selectNodeContents(element);
           priorRange.setEnd(range.startContainer, range.startOffset);

           start = priorRange.toString().length;
           end = start + range.toString().length;
       }
       else if (typeof document.selection != "undefined" && (sel = document.selection).type != "Control") {
           range = sel.createRange();

           priorRange = document.body.createTextRange();
           priorRange.moveToElementText(element);
           priorRange.setEndPoint("EndToStart", range);

           start = priorRange.text.length;
           end = start + range.text.length;
       }

       return {
           start: start,
           end: end
       };

    },
    
    selectionMouseDown : function(codeCouleur){
        var mainDiv = document.getElementById("containerMarquage");
        var sel = this.getSelectionCharOffsetsWithin(mainDiv);
        
        if (window.top.intervalPosition){
           window.clearInterval(window.top.intervalPosition);
           window.top.intervalPosition = null;
        }
        else{
            this.debutSelection = sel.start;
            this.finSelection = sel.end;
        }
        
        var selection = window.getSelection().getRangeAt(0);
        var selectedText = selection.extractContents();
        
        this.idOfSelectionNow = this.idOfSelectionNow + 1;

        var jQidOfSelectionNow = "#" + 'spanSelection' + this.idOfSelectionNow;
        
        var span = document.createElement("span");
        span.id = "spanSelection" + this.idOfSelectionNow;
        span.className = "clsSelection";
        span.style.backgroundColor = '#' + codeCouleur;
        span.appendChild(selectedText);
        selection.insertNode(span);
        
        
        var bkgRGBColorInsertedNode = jQuery(jQidOfSelectionNow).css('background-color');

        var objParent = jQuery(jQidOfSelectionNow).parent('span');
        var objChildren = jQuery(jQidOfSelectionNow).find('span');
        var elementTxtOnly;
        var mergeToOne = false;
        
        if (objChildren.length > 0){
            var arrIdChildrenSpan = new Array();
            
            jQuery.each(objChildren, function(i, val) {
                jQuery(val).attr("id", "new_id" + i)                     
                arrIdChildrenSpan.push(jQuery(val).attr("id"));
            });
            
            for (i = arrIdChildrenSpan.length - 1; i >= 0; i--){
                elementTxtOnly = jQuery("#" + arrIdChildrenSpan[i]).html();
                jQuery("#" + arrIdChildrenSpan[i]).replaceWith(elementTxtOnly);
            }
        }
        
        if (objParent.length > 0){
            var nbInsideSpan = 0;
            var startNewSpan = '';
            var newSpanId = '';
            var strNewInParentSpan = '';
            var bkgColorParent = jQuery("#" + objParent.attr("id")).css('background-color');
            var charAtAddSpan = -1;
            var strHTMLSpanParent = jQuery("#" + objParent.attr("id")).html();
            
            if (bkgRGBColorInsertedNode == bkgColorParent){
               elementTxtOnly = jQuery(jQidOfSelectionNow).html();
               jQuery(jQidOfSelectionNow).replaceWith(elementTxtOnly);
            }
            else{
                for (i = 0; i < strHTMLSpanParent.length; i++){
                    if (strHTMLSpanParent.charAt(i + 1) == "<" && strHTMLSpanParent.charAt(i + 2) == "s" && strHTMLSpanParent.charAt(i + 3) == "p" && strHTMLSpanParent.charAt(i + 4) == "a" && strHTMLSpanParent.charAt(i + 5) == "n"){
                       this.idOfSelectionNow = this.idOfSelectionNow + 1;
                       newSpanId = "spanSelection" + this.idOfSelectionNow;
                       
                       startNewSpan = '<span id="' + newSpanId + '" style="background-color: ' + bkgColorParent + '">';

                       strNewInParentSpan += strHTMLSpanParent.charAt(i);
                       strNewInParentSpan += "</span>";       
                    }
                    else if (strHTMLSpanParent.charAt(i - 1) == ">" && strHTMLSpanParent.charAt(i - 2) == "n" && strHTMLSpanParent.charAt(i - 3) == "a" && strHTMLSpanParent.charAt(i - 4) == "p" && strHTMLSpanParent.charAt(i - 5) == "s" && strHTMLSpanParent.charAt(i - 6) == "/" && strHTMLSpanParent.charAt(i - 7) == "<") {
                         nbInsideSpan++;
                    
                         charAtAddSpan = i + 7;
                         strNewInParentSpan += strHTMLSpanParent.charAt(i)
                     }
                     else{
                         strNewInParentSpan += strHTMLSpanParent.charAt(i);
                     }
              }
              
              if (nbInsideSpan > 0){
                 this.idOfSelectionNow = this.idOfSelectionNow + 1;
                 newSpanId = "spanSelection" + this.idOfSelectionNow;
                 
                 strNewInParentSpan = startNewSpan + strNewInParentSpan.substr(0, charAtAddSpan) + '<span id="' + newSpanId + '" style="background-color: ' + bkgColorParent + '">' + strNewInParentSpan.substr(charAtAddSpan) + "</span>";
              }
              else{
                  this.idOfSelectionNow = this.idOfSelectionNow + 1;
                  newSpanId = "spanSelection" + this.idOfSelectionNow;
                 
                  strNewInParentSpan = startNewSpan + strNewInParentSpan;   
              }
              
              jQuery("#" + objParent.attr("id")).replaceWith(strNewInParentSpan);
            }   
        }
        
        
        /*FUSIONNER LES COULEURS PAREILLES, C-A-D UNE À COTÉ DE L'AUTRE DIRECTEMENT POUR FAIRE UN SEUL SPAN*/
        var userAnswer = jQuery("#containerMarquage").html();
        var idSpan1 = '';
        var startedSearchId = false;
        var startedGetId = false;
        
        for (i = 0; i < userAnswer.length; i++){
            if (userAnswer.charAt(i) == '<'){
                if (userAnswer.charAt(i + 1) == 's' && userAnswer.charAt(i + 2) == 'p'){
                    //found span -> start id search
                    startedSearchId = true;
                    
                    if (userAnswer.charAt(i - 1) == '>' && userAnswer.charAt(i - 2) == 'n' && userAnswer.charAt(i - 3) == 'a' && userAnswer.charAt(i - 4) == 'p' && userAnswer.charAt(i - 5) == 's' && userAnswer.charAt(i - 6) == '/' && userAnswer.charAt(i - 7) == '<'){
                       //SPAN NEXT TO SPAN
                       if (jQuery("#" + idSpan2).css('background-color') == jQuery("#" + jQuery("#" + idSpan2).next('span').attr("id")).css('background-color')) {
                          var nextSpanId = jQuery("#" + idSpan2).next('span').attr("id");
                          
                          jQuery("#" + idSpan2).html(jQuery("#" + idSpan2).html() + jQuery("#" + nextSpanId).html());
                          jQuery("#" + nextSpanId).remove();
                       }
                       
                       idSpan2 = '';
                    }
                }
                
            }
            
            if (startedSearchId == true){
               if (startedGetId == true){
                  idSpan1 += userAnswer.charAt(i);
               }
               
               if (userAnswer.charAt(i) == "="){
                  if (userAnswer.charAt(i - 1) == 'd' && userAnswer.charAt(i - 2) == 'i'){
                     startedGetId = true;
                  }
               }
               
               if (startedGetId == true){
                  if ((userAnswer.charAt(i + 1) == "\"" || userAnswer.charAt(i + 1) == "'") && idSpan1 != ""){
                     startedSearchId = false;
                     startedGetId = false;
                     
                     idSpan1 = idSpan1.replace(/"/gi, "");
                     idSpan1 = idSpan1.replace(/'/gi, "");
                     
                     idSpan2 = idSpan1;
                     idSpan1 = '';
                  }
               }
            }
        }
        /**********/
        
        
        /*SI ON A ENLEVER LES MARQUES*/
        var objMarquage = jQuery("#containerMarquage").find('span');
        
        if (codeCouleur == '000000'){
           jQuery.each(objMarquage, function(i, val) {
               if (rgb2hex(jQuery("#" + jQuery(val).attr("id")).css('background-color')) == "#" + codeCouleur) {
                  elementTxtOnly = jQuery("#" + jQuery(val).attr("id")).html();
                  jQuery("#" + jQuery(val).attr("id")).replaceWith(elementTxtOnly);
               }
            });
        }
        
        jQuery.each(objMarquage, function(i, val) {
            if (jQuery("#" + jQuery(val).attr("id")).html() == "") {
                jQuery("#" + jQuery(val).attr("id")).remove();
            }
         });
        /*****************************/
        
        if (!isMobile.any()) {
           document.getElementById('navBarTxtPageIndex').focus();
        }
        //document.selection.clear(); /*NE SEMBLE PAS FONCTIONNER CORRECTEMENT SURTOUT SOUS IE*/

    },

    save: function(){
        this.sRepAct = jQuery("#containerMarquage").html();

        return this.sRepAct;    
    },

    validate: function(){
        this.save();
        this.triesCount++;

        var spanFeedbackHTML = 0;
        var strSpanFeedbackHTML = '';
        var feedbackHTML = '';
        var goodAnswerCount = 0;
        var wrongAnswerCount = 0;
        var bulletImage = '';
        var feedback = '';

        var userAnswer = jQuery("#containerMarquage").html();

        var userChoicesStart = new Array();
        var userChoicesEnd = new Array();
        var userChoicesColor = new Array();

        var userAnswerHTML = new Array();
        var goodAnswersColorGroup = new Array();
        var wrongAnswersColorGroup = new Array();
        var retroTxt;
        
        var startGetEndColor = false;
        var startRemoveCount = false;
        var removeNbChars = 0;
        var foundHilite = false;
        
        var strToEvaluate;
        var filteredColosTag;
        
        // Si on a plusieurs espaces, le jQuery("#containerMarquage").html(); va nous donner des &nbsp; sauf pour le dernier espace.
        // Pour les bonnes réponses, les positions ne calculent pas '&nbsp;', mais vont donner X pour le nombre d'espaces.
        // Ex: 'allo     toi' = 5 espaces. Donne (0, 4) et (9, 12). Sauf que .html() donne allo&nbsp;nbsp;nbsp;nbsp; toi = (0, 4) et (26, 29)
        // D'où le pourquoi on replace les &nbsp; par ' ' pour l'évaluation de la string.
        userAnswer = userAnswer.replace(/&nbsp;/g, ' '); 

        for (i = 0; i < this.colors.length; i++){
            /*QUAND ON INSERT NODE, CA MET LES COULEURS EN RGB... LA COULEUR EST PASSÉ EN HEX. FAIRE LA CONVERSION POUR L'ÉVALUATION*/
            var rgbColor = hexToRgb(this.colors[i]);
            
            rgbColor = "rgb(" + rgbColor.r + ", " + rgbColor.g + ", " + rgbColor.b + ")";
            rgbColor = rgbColor.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
            
            var re = new RegExp(rgbColor,"gi");
            userAnswer = userAnswer.replace(re, "#" + this.colors[i]);

            userAnswerHTML[i] = '';
            retroTxt = '';
            goodAnswersColorGroup[i] = 0;
            wrongAnswersColorGroup[i] = 0;
        }
        

        for (i = 0; i < userAnswer.length; i++){
            if (userAnswer.charAt(i) == '<'){
                if (userAnswer.charAt(i + 1) == 's' && userAnswer.charAt(i + 2) == 'p'){
                    //found span -> start hilite
                    startRemoveCount = true;
                    foundHilite = true;
                }
                else{
                    //found end </span>
                    if (userAnswer.charAt(i + 1) == '/' && userAnswer.charAt(i + 2) == 's'){
                        userChoicesEnd[userChoicesEnd.length] = i - (removeNbChars + 1);

                        if (this.initialMainText.charAt(userChoicesEnd[userChoicesEnd.length - 1]) == ' '){
                            //SI A CHOISI AVEC ESPACE EN AVANT, C'EST OK. DONC, AJUSTER
                            userChoicesEnd[userChoicesEnd.length - 1] = userChoicesEnd[userChoicesEnd.length - 1] - 1;
                        }

                        removeNbChars = removeNbChars + 7;
                        startRemoveCount = false;
                    }
                }
            }
            else if (userAnswer.charAt(i) == '#'){
                //get color
                userChoicesColor[userChoicesColor.length] = userAnswer.charAt(i + 1) + userAnswer.charAt(i + 2) + userAnswer.charAt(i + 3) + userAnswer.charAt(i + 4) + userAnswer.charAt(i + 5) + userAnswer.charAt(i + 6);
            }
            else if (userAnswer.charAt(i) == ';' && foundHilite == true){
                startGetEndColor = true;
            }
            
            if (startGetEndColor == true){
               if ((userAnswer.charAt(i) == '"' || userAnswer.charAt(i) == '\'') && userAnswer.charAt(i + 1) == '>'){
                  userChoicesStart[userChoicesStart.length] = i - removeNbChars;
                  startGetEndColor = false;
                  
                  removeNbChars = removeNbChars + 2;
                  startRemoveCount = false;
               }
            }

            if (startRemoveCount == true){
                removeNbChars++;
            }
        }
        
        for (i = 0; i < userChoicesStart.length; i++){
            var mot = '';
            var strRetroactionChoice = '';
            var colorIndex;
            var foundGoodAnswer = false;

            for (j = 0; j < this.initialMainText.length; j++){
                if (j >= userChoicesStart[i] && j <= userChoicesEnd[i]){
                    mot += this.initialMainText.charAt(j);
                }
            }

            for (j = 0; j < this.colors.length; j++){
                if (userChoicesColor[i] == this.colors[j]){
                    colorIndex = j;
                }
            }

            for (x = 0; x < this.choicesStart.length; x++){
                if ((this.choicesStart[x] == userChoicesStart[i]) && (this.choicesEnd[x] == userChoicesEnd[i]) && (this.choicesColor[x] == userChoicesColor[i])){
                    foundGoodAnswer = true;
                }
            }
            
            for (y = 0; y < this.retroChoice.length; y++){
                if ((this.retroChoice[y][0] == userChoicesStart[i]) && (this.retroChoice[y][1] == userChoicesEnd[i]) && this.retroChoice[y][2] == userChoicesColor[i]) {
                    strRetroactionChoice = this.retroChoice[y][3];
                }
            }


            if (foundGoodAnswer == true){
                goodAnswerCount++;
                goodAnswersColorGroup[colorIndex]++;
                bulletImage = 'bullet_green.png';
            }
            else{
                wrongAnswerCount++;
                wrongAnswersColorGroup[colorIndex]++;
                bulletImage = 'bullet_red.png';
            }
            
            
            strToEvaluate = strRetroactionChoice;
            strRetroactionChoice = evalStringForLexique(strToEvaluate); 
            
            strToEvaluate = mot;
            mot = evalStringForLexique(strToEvaluate); 
            

            userAnswerHTML[colorIndex] += '<table cellpadding="0" cellspacing="0" border="0">';
              userAnswerHTML[colorIndex] += '<tr>';
                userAnswerHTML[colorIndex] += '<td width="17" valign="top">';
                  userAnswerHTML[colorIndex] += '<img src="images/' + bulletImage + '" />';
                userAnswerHTML[colorIndex] += '</td>';
                userAnswerHTML[colorIndex] += '<td valign="top">';
                  userAnswerHTML[colorIndex] += mot;
                userAnswerHTML[colorIndex] += '</td>';
              userAnswerHTML[colorIndex] += '</tr>';
              
              if (strRetroactionChoice != '') {
                  userAnswerHTML[colorIndex] += '<tr>';
                userAnswerHTML[colorIndex] += '<td colspan="2">';
                userAnswerHTML[colorIndex] += '<span class="retroContainerStyle3">' + strRetroactionChoice + '</span>';
                userAnswerHTML[colorIndex] += '</td>';
               userAnswerHTML[colorIndex] += '</tr>';
               userAnswerHTML[colorIndex] += '<tr>';
                userAnswerHTML[colorIndex] += '<td height="10">';
                userAnswerHTML[colorIndex] += '</td>';
               userAnswerHTML[colorIndex] += '</tr>';
              }
              else {
               userAnswerHTML[colorIndex] += '<tr>';
                userAnswerHTML[colorIndex] += '<td height="5">';
                userAnswerHTML[colorIndex] += '</td>';
               userAnswerHTML[colorIndex] += '</tr>';
              }
              
              
            userAnswerHTML[colorIndex] += '</table>';
        }


        var nbLigne = 0;
        var statusCompleted = true;

        for (i = 0; i < this.colors.length; i++) {
            var goodAnswersColorsTotal = 0;

            for (j = 0; j < this.choicesStart.length; j++){
                if (this.choicesColor[j] == this.colors[i]){
                    goodAnswersColorsTotal++;
                }
            }

            if (wrongAnswersColorGroup[i] > 0){
                retroTxt = this.retroNeg[i];

                this.status = this.quiz.statusToRedo;
                statusCompleted  = false;

                spanFeedbackHTML = 1;
            }
            else{
                if (goodAnswersColorGroup[i] == goodAnswersColorsTotal){
                    retroTxt = this.retroPos[i];
                }
                else{
                    retroTxt = this.retroInc[i];

                    this.status = this.quiz.statusToRedo;
                    statusCompleted  = false;

                    if (spanFeedbackHTML != 1){
                        spanFeedbackHTML = 2;
                    }
                }
            }

            if (statusCompleted == true){
                this.status = this.quiz.statusCompleted;
            }
            
            
            filteredColosTag = this.colorsTag[i];
            strToEvaluate = filteredColosTag;
            filteredColosTag = evalStringForLexique(strToEvaluate);


            if (this.colors.length == 1){
                feedbackHTML += '<table cellpadding="0" cellspacing="0" border="0">';
                  feedbackHTML += '<tr>';
                    feedbackHTML += '<td valign="top">';
                      feedbackHTML += '<table style="min-width:150px;max-width:700px; cellpadding="0" cellspacing="0" border="0">';
                        feedbackHTML += '<tr>';
                          feedbackHTML += '<td height="21" align="left" valign="middle">';
                            feedbackHTML += '<div class="colorButtonsOuter"><div class="colorButtonsInner" style="background-color:#' + this.colors[i] + '"></div></div>';
                            feedbackHTML += '<table height="21" cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle" style="padding-left:9px">' + filteredColosTag + '</td></td></table>';
                          feedbackHTML += '</td>';
                          feedbackHTML += '</tr>';
                          feedbackHTML += '<tr>'
                            feedbackHTML += '<td height="5" align="left">';
                          feedbackHTML += '</td>';
                          feedbackHTML += '</tr>';
                          feedbackHTML += '<tr>';
                            feedbackHTML += '<td height="120" align="left" valign="top" style="border:solid 1px gray;padding-left:10px;padding-top:8px;padding-right:10px;min-width:150px;max-width:700px;">';
                              feedbackHTML += userAnswerHTML[i];
                            feedbackHTML += '</td>';
                          feedbackHTML += '</tr>';
                          feedbackHTML += '<tr>';
                            feedbackHTML += '<td align="left" valign="top" class="tdRetro" style="padding-top:2px;">';
                              feedbackHTML += retroTxt;
                            feedbackHTML += '</td>';
                          feedbackHTML += '</tr>';
                        feedbackHTML += '</table>';
                      feedbackHTML += '</td>';
                    feedbackHTML += '</tr>';
                feedbackHTML += '</table>';
            }
            else{
                if (nbLigne == 0){
                    feedbackHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
                    feedbackHTML += '<tr>';
                }

                feedbackHTML += '<td width="330" valign="top">';
                  feedbackHTML += '<table width="330" cellpadding="0" cellspacing="0" border="0">';
                    feedbackHTML += '<tr>';
                      feedbackHTML += '<td width="30" height="21" align="left" valign="middle">';
                        feedbackHTML += '<div class="colorButtonsOuter"><div class="colorButtonsInner" style="background-color:#' + this.colors[i] + '"></div></div>';
                      feedbackHTML += '</td>';
                      feedbackHTML += '<td width="300" height="21" align="left" valign="middle">';
                        feedbackHTML += filteredColosTag;
                      feedbackHTML += '</td>';
                    feedbackHTML += '</tr>';
                    feedbackHTML += '<tr>'
                      feedbackHTML += '<td width="30" height="5" align="left">';
                      feedbackHTML += '</td>';
                      feedbackHTML += '<td width="300" height="5" align="left">';
                      feedbackHTML += '</td>';
                    feedbackHTML += '</tr>';
                    feedbackHTML += '<tr>';
                      feedbackHTML += '<td width="330" height="120" colspan="2" align="left" valign="top" style="border:solid 1px gray;padding-left:10px;padding-top:8px;padding-right:10px;">';
                        feedbackHTML += userAnswerHTML[i];
                      feedbackHTML += '</td>';
                    feedbackHTML += '</tr>';
                    feedbackHTML += '<tr>';
                      feedbackHTML += '<td width="330" colspan="2" align="left" valign="top" class="tdRetro" style="padding-top:2px;">';
                        feedbackHTML += retroTxt;
                      feedbackHTML += '</td>';
                    feedbackHTML += '</tr>';
                  feedbackHTML += '</table>';
                feedbackHTML += '</td>';
                feedbackHTML += '<td width="20" height="141">';
                feedbackHTML += '</td>';

                nbLigne++;

                if (nbLigne == 2){
                    feedbackHTML += '</tr>';

                    feedbackHTML += '<tr>';
                      feedbackHTML += '<td width="700" height="35" align="left" colspan="4">';
                      feedbackHTML += '</td>';
                    feedbackHTML += '</tr>';

                    feedbackHTML += '</table>';

                    nbLigne = 0;
                }
            }
        }

        if (this.colors.length%2 && this.colors.length > 1){
            //nb impair de classeur

            feedbackHTML += '</tr>';
            feedbackHTML += '<tr>';
              feedbackHTML += '<td width="700" height="35" align="left" colspan="4">';
              feedbackHTML += '</td>';
            feedbackHTML += '</tr>';

            feedbackHTML += '</table>';
        }


        var goodAnswerCountRequired = this.choicesStart.length;
        var wrongAnswerCountPossible = this.choicesStart.length;

        if (wrongAnswerCount > wrongAnswerCountPossible){
            wrongAnswerCount = wrongAnswerCountPossible;
        }

        if(this.mustGiveAllGoodAnswers == false){
            var goodAnswerPonderation = this.ponderation / goodAnswerCountRequired;
            this.currentScore = (goodAnswerCount * goodAnswerPonderation) - (wrongAnswerCount * this.iFautePond);
            
            if (this.currentScore < 0){
                this.currentScore = 0;
            }

        }else{
            if (statusCompleted == true){
                this.currentScore = this.ponderation;
            }
            else{
                this.currentScore = 0;
            }
        }
        

        switch (spanFeedbackHTML){
            case 0:
                strSpanFeedbackHTML = '<span class="Green">' + this.page.goodAnswerLabel + '</span><br /><br />';
                this.statusMenuPages = 1;
            break;

            case 1:
                strSpanFeedbackHTML = '<span class="Red">' + this.page.wrongAnswerLabel + '</span><br /><br />';
                this.statusMenuPages = 0;
            break;

            case 2:
                strSpanFeedbackHTML = '<span class="Yellow">' + this.page.incompleteAnswerLabel + '</span><br /><br />';
                this.statusMenuPages = -1;
            break;
        }
        
        

        if (foundHilite == true){
            foundHilite = false;
        }
        
        feedbackHTML = strSpanFeedbackHTML + feedbackHTML;
        feedbackHTML = feedbackHTML + '<br /><br />';


        setFeedback(feedbackHTML);
        openFeedback();
        

        return this.currentScore;
    },

    showSolution: function(){
        var solutionTxt = '';
        var putSpanStart = false;
        var putSpanEnd = false;
        var txtSpanToPut;
        var strToEvaluate;

        var solutionHTML =  this.quiz.solutionLabel + '<br /><br />';
    
        for (i = 0; i < this.initialMainText.length; i++){
            for (j = 0; j < this.choicesStart.length; j++){
                if (i == this.choicesStart[j]){
                    txtSpanToPut = '<span style="background-color: #' + this.choicesColor[j] + '" class="span_choice">';
                    putSpanStart = true;
                }
                if (i == this.choicesEnd[j]){
                    putSpanEnd = true;
                }
            }
            if ((putSpanStart == true) && (putSpanEnd == true)){
                solutionTxt = solutionTxt + txtSpanToPut + this.initialMainText.charAt(i) + '</span>';
                putSpanStart = false;
                putSpanEnd = false;
            }
            else if (putSpanStart == true){
                solutionTxt = solutionTxt + txtSpanToPut + this.initialMainText.charAt(i);
                putSpanStart = false;
            }
            else if (putSpanEnd == true){
                solutionTxt = solutionTxt + this.initialMainText.charAt(i) + '</span>';
                putSpanEnd = false;
            }
            else{
                solutionTxt = solutionTxt + this.initialMainText.charAt(i);
            }
        }
        
        
        putSpanStart = false;
        putSpanEnd = false;
        solutionHTML = solutionHTML + '<div class="solutionMarquage">' + solutionTxt + '</div>';
        
        setFeedback(solutionHTML);
        
        
        jQuery(".span_choice").each(function() {
            var spanChoiceHTML = jQuery(this).html();
            var filteredSpanChoice;
            
            strToEvaluate = spanChoiceHTML;
            filteredSpanChoice = evalStringForLexique(strToEvaluate);
            
            jQuery(this).html(filteredSpanChoice);
        });
        
        strToEvaluate = jQuery("#feedbackcontent .solutionMarquage").html();
        jQuery("#feedbackcontent .solutionMarquage").html(evalStringForLexique(strToEvaluate));
        
        
        openFeedback();
    },

    redo: function(){
        this.sRepAct = '';
        this.currentScore = 0;
        this.redoQuestion = true;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;

        closeFeedback();
        this.display();
    },

    redoQuiz: function(){
        this.currentScore = 0;
        this.redoQuestion = true;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;
    },

    trim : function(myString){
         return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
    },

    getConsigne: function(){
        return this.quiz.consigneMarquage;
    }

});

