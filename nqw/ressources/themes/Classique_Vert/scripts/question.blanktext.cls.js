var QuestionBlankText = Class.create({
    sQuestionType: 'TEXTE LACUNAIRE',
    
    NONE: 0,
    ALPHA: 1,
    NUMERIC: 2,

    //settings
    questionType: 1, //1 = drag and drop, 2 = blank text, 3 = combo box
    labelType: 0,
    mustGiveAllGoodAnswers: false,

    currentSequence: null,
    currentChoices: null,
    currentSpanChoices: null,
    currentScore: 0,
    ponderation: 0,
    triesCount: 0,
    status: '',
    statusMenuPages: -1,
    blankChars: '____________',
    inputSize: 100,
    selectCount: 0,
    blankCount: 0,
    bPoncCompte: false,
    bCaseSens: false, 
    
    initialize: function(quiz, page, questionNb){
        this.quiz = quiz;
        this.page = page;
        this.questionNb = questionNb;
           
        this.choices = new Array();
        this.displayChoices = new Array();
        this.goodFeedback = new Array();
        this.badFeedback = new Array();
        this.goodAnswers = new Array();
        this.wrongAnswers = new Array();
        this.goodPosition = new Array();
        this.badPosition = new Array();
        this.otherAnswersFeedback = new Array();
        this.selectElement = new Array();
        this.selectFeedBack = new Array();
        this.selectAnswer = new Array();
        this.selectPosition = new Array(); //Position = lequel des combo box dans le main text(premier, deuxième...)
        this.shuffleList = new Array();

        this.status = this.quiz.statusToDo;

        this.userAnswers = '';
    },

    addMainText: function(mainText){
        var strToEvaluate;
        var blankSplit;
        var htmlSelect = '';

        this.mainText = mainText;
        this.initialMainText = mainText;

        blankSplit = this.mainText.split("%blank");
        this.blankCount = blankSplit.length - 1;

        for(i = 0;i < blankSplit.length - 1;i++){
            var indexBlank = i + 1;

            if (this.questionType == 1)
               this.mainText = this.mainText.replace("%blank" + indexBlank, "<span id=\"idspan" + indexBlank + "\"><span id=\"drop" + indexBlank + "\" style=\"border:1px solid transparent;\">" + this.blankChars + "</span></span>");
            else if(this.questionType == 2)
               this.mainText = this.mainText.replace("%blank" + indexBlank, "<input type=\"text\" id=\"idinput" + indexBlank + "\" style=\"width:" + this.inputSize + "px\" />");
            else if(this.questionType == 3){

               this.shuffleListDisplay(true,indexBlank);


               htmlSelect = '<select id="select' + indexBlank + '">';
               htmlSelect += '<option value="0"> </option>';

               if (this.shuffleList[i] == true){
                   var shuffledArray = new Array();
                   var indexReplace = new Array();
                   var changeSelectElement = new Array();
                   var changeSelectFeedBack = new Array();
                   var changeSelectAnswer = new Array();
                   var changeSelectPosition = new Array();

                   for(j = 0;j <= this.selectElement.length - 1;j++){
                       if (indexBlank == this.selectPosition[j]){
                           shuffledArray[shuffledArray.length] = j;
                           indexReplace[indexReplace.length] = j;
                       }
                   }

                   this.shuffleChoices(shuffledArray);

                   for(x = 0;x < shuffledArray.length;x++){
                        changeSelectElement[changeSelectElement.length] = this.selectElement[shuffledArray[x]];
                        changeSelectFeedBack[changeSelectFeedBack.length] = this.selectFeedBack[shuffledArray[x]];
                        changeSelectAnswer[changeSelectAnswer.length] = this.selectAnswer[shuffledArray[x]];
                        changeSelectPosition[changeSelectPosition.length] = this.selectPosition[shuffledArray[x]];
                   }

                   for(x = 0;x < shuffledArray.length;x++){
                        this.selectElement[indexReplace[x]] = changeSelectElement[x];
                        this.selectFeedBack[indexReplace[x]] = changeSelectFeedBack[x];
                        this.selectAnswer[indexReplace[x]] = changeSelectAnswer[x];
                        this.selectPosition[indexReplace[x]] = changeSelectPosition[x];
                   }

               }

               for(j = 0;j <= this.selectElement.length - 1;j++){
                   var optionValue = j + 1;
                   if (indexBlank == this.selectPosition[j])
                       htmlSelect += '<option value="' + optionValue + '">' + this.selectElement[j] + '</option>';
               }

               htmlSelect += '</select>';

               this.selectCount = this.selectCount + 1;
               this.mainText = this.mainText.replace("%blank" + indexBlank, htmlSelect);
            }
        }

        this.mainText = this.mainText + "<br />";
        
        strToEvaluate = this.mainText;
        this.mainText = evalStringForLexique(strToEvaluate);  
    },

    addGoodAnswer: function(goodAnswer, feedback, position){
        if (this.questionType == 1){
            var theLength = this.choices.length;

            this.choices[this.choices.length] = goodAnswer;
            this.goodFeedback[this.goodFeedback.length] = feedback;
            this.goodPosition[this.goodPosition.length] = position;
            this.goodAnswers[this.goodAnswers.length] = goodAnswer;
            this.displayChoices[theLength] = this.choices.length - 1;
        }
        else if (this.questionType == 2){
            this.goodAnswers[this.goodAnswers.length] = goodAnswer;
            this.goodFeedback[this.goodFeedback.length] = feedback;
            this.goodPosition[this.goodPosition.length] = position;
        }
    },
    
    addWrongAnswer: function(wrongAnswer, feedback, position){
        if (this.questionType == 1){
            var theLength = this.choices.length;

            this.choices[this.choices.length] = wrongAnswer;
            this.badFeedback[this.badFeedback.length] = feedback;
            this.badPosition[this.badPosition.length] = position;
            this.wrongAnswers[this.wrongAnswers.length] = wrongAnswer;
            this.displayChoices[theLength] = this.choices.length - 1;
        }
        else if (this.questionType == 2){
            this.wrongAnswers[this.wrongAnswers.length] = wrongAnswer;
            this.badFeedback[this.badFeedback.length] = feedback;
            this.badPosition[this.badPosition.length] = position;
        }
    },
    
    setOtherAnswersFeedback: function(feedback, position){
        this.otherAnswersFeedback[position] = feedback;
    },

    createSelect: function(element, feedback, goodAnswer, position){
        this.selectElement[this.selectElement.length] = element;
        this.selectFeedBack[this.selectFeedBack.length] = feedback;
        this.selectAnswer[this.selectAnswer.length] = goodAnswer;
        this.selectPosition[this.selectPosition.length] = position;
    },

    shuffle: function(){
        this.shuffleChoices(this.displayChoices);
    },

    shuffleListDisplay: function(randomDisplay, indexList){
        this.shuffleList[indexList - 1] = randomDisplay;
    },
    
    display : function(){
        var divMainText = null;
        var spanChoice = null;
        var inputUser = null;
        var idDrag = "";
        var idDrop = "";
        var idDragInput = "";
        var idDragInputStr = "";
        var indexValue;
        var strUserAnswers = "";
        var lengthChoices;
        var varBlankCount;

        lengthChoices = this.choices.length;
        varBlankCount = this.blankCount;

        $('question').update('');

        divMainText = document.createElement('div');
        divMainText.id = 'idMainText';

        if (this.questionType == 1){
           if (document.all) { Droppables.drops = [] }

           inputUser = document.createElement('input');
           inputUser.id = 'userAnswers';
           inputUser.type = 'hidden';

           $('question').appendChild(divMainText);
           $('idMainText').appendChild(inputUser);


           var divAnswerChoices = document.createElement('div');
           divAnswerChoices.id = 'divAnswerChoices';
           $('idMainText').appendChild(divAnswerChoices);

           for(i = 0;i < this.choices.length;i++){
               indexValue = this.displayChoices[i] + 1;
               idDrag = "drag" + indexValue;
               idDragInput = "dragInput" + indexValue;
               idDragInputStr = "str" + idDragInput;
               
               spanChoice = document.createElement('span');
               spanChoice.innerHTML = "<b>" + this.choices[this.displayChoices[i]] + "</b><input type=\"hidden\" style=\"width:20px;z-index:5\" id=\"" + idDragInput + "\" value=\"" + indexValue + "\">" + "<input type=\"hidden\" style=\"width:20px;z-index:5\" id=\"" + idDragInputStr + "\" value=\"" + this.choices[this.displayChoices[i]] + "\">";
               spanChoice.id = idDrag;
               spanChoice.style.cursor = 'move';

               $('divAnswerChoices').appendChild(spanChoice);

               //A CAUSE DE CA, DOIT REPETER LE LOOP APRES CAR SINON, CA INIT PAS LES DROP
               if ((i + 1) < this.choices.length)
                   $('divAnswerChoices').innerHTML = $('divAnswerChoices').innerHTML + "<img src=\"images/pagechoicesep.png\" border=\"0\">";
           }

           $('idMainText').innerHTML = $('idMainText').innerHTML + "<br />";
           $('idMainText').innerHTML = $('idMainText').innerHTML + this.mainText;

           for(i = 0;i < this.choices.length;i++){

               idDrag = this.displayChoices[i] + 1;
               idDrag = "drag" + idDrag;

               new Draggable(idDrag, {revert:true, scroll:window,onStart:function(){closeFeedback();}});
            }

            if (this.currentSequence != null){
               document.getElementById('userAnswers').value = this.currentSequence;

               for(i = 1;i <= this.blankCount;i++){
                   var idDropHTML;
                   var idSpan = i;

                   idSpan = "idspan" + idSpan;
                   $(idSpan).update(this.currentSpanChoices[i - 1]);
               }

               for(i = 1;i <= this.blankCount;i++){
                     idDrop = i;
                     idDrop = "drop" + idDrop;

                     idDropHTML = $(idDrop).innerHTML;

                     if (idDropHTML.charAt(0) != "_"){

                        new Draggable(idDrop, {revert:true, scroll:window,onStart:function(){closeFeedback();}});

                        var idSpan = i;
                        idSpan = "idspan" + idSpan;

                        var oldSplitResultDrag;
                        var oldStrSpan;
                        var oldIdDrag;
                        var oldIdDragInput;

                        oldStrSpan = $(idSpan).innerHTML;
                        oldSplitResultDrag = oldStrSpan.split("dragInput");

                        if (oldSplitResultDrag[1] != null){
                            oldIdDragInput = trim(oldSplitResultDrag[1].substring(0,2));
                            oldIdDragInput = oldIdDragInput.replace("\"","");
                            oldIdDrag  = "drag" + oldIdDragInput;
                            $(oldIdDrag).hide();
                        }
                     }
               }
            }

            for(i = 1;i <= this.blankCount;i++){
                   idDrop = i;
                   idDrop = "drop" + idDrop;

                   Droppables.add(
                         idDrop,
                         {
                            scrollingParent: window,
                            onDrop: function(drag, drop) {
                                //POUR AVOIR LE ID DU DRAG ET LE ID DU DROP
                                //alert("dragid = " + drag.id);
                                //alert("dropid = " + drop.id);
                                //IL SEMBLE QUE JE PEUX PAS METTRE DES VARIABLES D'AILLEURS ICI...(this.userAnswers au début)
                                //D'OU LE POURQUOI D'UN INPUT HIDDEN PLUS BAS

                                var idDragged = drag.id;
                                var splitidDragged;

                                var idDropped = drop.id;
                                var splitidDropped;

                                var alreadyTaken = false;

                                var innerHTMLDragged;
                                var innerHTMLDropped;

                                var idSpan;
                                var strSpan;
                                var indexSpan;
                                var splitResultDrag;
                                var idDragInput;

                                strUserAnswers = "";


                                if (idDragged.indexOf("drag") > -1)
                                   alreadyTaken = false
                                else if(idDragged.indexOf("drop") > -1)
                                   alreadyTaken = true;


                                innerHTMLDragged = $(idDragged).innerHTML;
                                innerHTMLDropped = $(idDropped).innerHTML;

                                $(idDragged).update(innerHTMLDropped);
                                $(idDropped).update(innerHTMLDragged);


                                if (innerHTMLDropped.charAt(0) == "_"){
                                   new Draggable(idDropped, {revert:true, scroll:window,onStart:function(){closeFeedback();}});

                                   $(idDropped).style.cursor = 'move';

                                   if (alreadyTaken == false)
                                      $(idDragged).hide();
                                }

                                for(j = 1;j <= varBlankCount;j++){
                                      idDragInput = "";
                                      indexSpan = j;
                                      idSpan = "idspan" + indexSpan;
                                      strSpan = $(idSpan).innerHTML;

                                      splitResultDrag = strSpan.split("dragInput");

                                      if (splitResultDrag[1] != null){
                                         idDragInput = trim(splitResultDrag[1].substring(0,2));

                                         idDragInput = idDragInput.replace("\"","");
                                         idDragInput = trim(idDragInput);
                                         idDragInput = "dragInput" + idDragInput;
                                      }

                                      if (idDragInput != "")
                                          strUserAnswers = strUserAnswers + document.getElementById(idDragInput).value + "x";
                                      else
                                          strUserAnswers = strUserAnswers + "-1x";
                                }
                                document.getElementById('userAnswers').value = strUserAnswers;

                                //FACON LA PLUS SIMPLE DE REGLER LE GLICTH SOUS SAFARI (SI LONG TEXTE LAISSE TRAIL A CAUSE LE DRAG EST UN SPAN)
                                document.getElementById('quizpage').style.backgroundColor = 'white';
                                document.getElementById('scrollwrapper').style.backgroundColor = 'white';
                            }
                         }
                      );
              }
        }
        else if (this.questionType == 2){
            $('question').appendChild(divMainText);
            $('idMainText').update(this.mainText);

            if (this.currentChoices != null){
                for(i = 0;i < this.currentChoices.length;i++){
                    var inputId = i + 1;

                    inputId = "idinput" + inputId;
                    document.getElementById(inputId).value = this.currentChoices[i];
                }
            }
        }
        else if (this.questionType == 3){
            $('question').appendChild(divMainText);
            $('idMainText').update(this.mainText);

            if (this.currentChoices != null){
                for(i = 1;i <= this.selectCount;i++){
                    var selectId = i;

                    selectId = "select" + selectId;
                    document.getElementById(selectId).value = this.currentChoices[i - 1];
                }
            }
        }

    },

    save: function(){
        this.currentChoices = new Array();  
        this.currentSpanChoices = new Array();

        if (this.questionType == 1){
            this.currentSequence = document.getElementById('userAnswers').value;

            for(i = 1;i <= this.blankCount;i++){
                var spanSave = i;
                 
                spanSave = "idspan" + spanSave;
                this.currentSpanChoices[this.currentSpanChoices.length] = $(spanSave).innerHTML;
            }
        }
        else if (this.questionType == 2){
            for(i = 1;i <= this.blankCount;i++){
                var inputSave = i;

                inputSave = "idinput" + inputSave;
                this.currentChoices[this.currentChoices.length] = document.getElementById(inputSave).value;
            }
        }
        else if (this.questionType == 3){
            for(i = 1;i <= this.selectCount;i++){
                var selectSave = i;

                selectSave = "select" + selectSave;
                this.currentChoices[this.currentChoices.length] = document.getElementById(selectSave).value;
            }
        }
    },

    validate: function(){
        this.save();
        this.triesCount++;
        var feedbackHTML = '';
        var answerCount = 0;
        var goodAnswerCount = 0;
        var wrongAnswerCount = 0;
        var foundRealWrongAnswer = false;
        var splitUserAnswer;
        var blankAnswer = false;
        var strToEvaluate;

        if (this.questionType == 1){
           this.userAnswers = document.getElementById('userAnswers').value;
           splitUserAnswer = this.userAnswers.split("x");

           if (this.userAnswers != ''){
               for(i = 1;i <= this.blankCount;i++){
                  if(splitUserAnswer[i - 1] != -1){
                     var bulletImage = 'bullet_red.png';  
                     var idDragInputStrValue = splitUserAnswer[i - 1];
                     idDragInputStrValue = 'strdragInput' + idDragInputStrValue;
                     
                     var choice = document.getElementById(idDragInputStrValue).value;
                     var acceptedChoices = new Array();
                     var acceptedChoicesFeedback = new Array();
                     var acceptedChoicesPosition = new Array();
                     var rejectedChoices = new Array();
                     var rejectedChoicesFeedback = new Array();
                     var rejectedChoicesPosition = new Array();
                     var answerIsGood = false;
                     var answerIsBad = false;
                     var feedback = '';
                     var displayFeedback = false;
                     var indexGoodFeedback = -1;
                     var indexBadFeedback = -1;

                     for(j = 0;j < this.goodAnswers.length;j++){
                         if (this.goodPosition[j] == i){
                             acceptedChoices[acceptedChoices.length] = this.goodAnswers[j];
                             acceptedChoicesFeedback[acceptedChoicesFeedback.length] = this.goodFeedback[j];
                             acceptedChoicesPosition[acceptedChoicesPosition.length] = this.goodPosition[j];
                         }
                     }

                     for(j = 0;j < acceptedChoices.length;j++){
                         if (choice == acceptedChoices[j]){
                             if (answerIsGood == true){
                                 if (acceptedChoicesPosition[indexGoodFeedback] != i){
                                     if (acceptedChoicesPosition[j] == i){
                                         indexGoodFeedback = j;
                                     }
                                 }
                             }
                             else{
                                 answerIsGood = true;
                                 indexGoodFeedback = j;
                             }
                         }
                     }

                     if (answerIsGood == true){
                         answerCount++;
                         goodAnswerCount++;
                         bulletImage = 'bullet_green.png';
                         displayFeedback = true;
                         feedback = acceptedChoicesFeedback[indexGoodFeedback];
                     }
                     else{
                         if (choice != ''){
                            answerCount++;
                            wrongAnswerCount++;
                            displayFeedback = true;
                            var foundWrongChoice = false;

                            for(j = 0;j < this.wrongAnswers.length;j++){
                                if (this.badPosition[j] == i){
                                    foundWrongChoice = true;
                                    rejectedChoices[rejectedChoices.length] = this.wrongAnswers[j];
                                    rejectedChoicesFeedback[rejectedChoicesFeedback.length] = this.badFeedback[j];
                                    rejectedChoicesPosition[rejectedChoicesPosition.length] = this.badPosition[j];
                                }
                            }

                            if (foundWrongChoice == true){
                               for(j = 0;j < rejectedChoices.length;j++){
                                   if (choice == rejectedChoices[j]){
                                       if (answerIsBad == true){
                                           if (rejectedChoicesPosition[indexBadFeedback] != i){
                                               if (rejectedChoicesPosition[j] == i){
                                                   indexBadFeedback = j;
                                               }
                                           }
                                       }
                                       else{
                                           answerIsBad = true;
                                           indexBadFeedback = j;
                                       }
                                   }
                               }

                               if (answerIsBad == true) {
                                   feedback = rejectedChoicesFeedback[indexBadFeedback];
                               }
                               else {
                                   feedback = this.otherAnswersFeedback[i];
                               }

                            }
                            else{
                               feedback = this.otherAnswersFeedback[i];
                               foundRealWrongAnswer = true;
                            }
                         }
                         else{
                             wrongAnswerCount++;
                             blankAnswer = true;
                         }
                         
                         if (foundRealWrongAnswer == false) {
                            if (foundWrongChoice == true) {
                               foundRealWrongAnswer = true;
                            }
                         }
                     }

                     if (displayFeedback == true){
                         var label = '';
                         label = '';
                         if(this.labelType == this.ALPHA){
                             label = getLetterLabel(answerCount).toUpperCase() + ')';
                         }else if(this.labelType == this.NUMERIC){
                             label = (answerCount) + ')';
                         }

                         feedbackHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
                         feedbackHTML += '<tr>';
                         feedbackHTML += '<td width="20"><img src="images/' + bulletImage + '" /></td>';

                         if (label != '')
                            solutionHTML += '<td width="25">' + label + '</td>';

 
                         strToEvaluate = choice;
                         choice = evalStringForLexique(strToEvaluate);
                         feedbackHTML += '<td>' + choice + '</td></tr>';

                         if (feedback != ''){
                             strToEvaluate = feedback;
                             feedback = evalStringForLexique(strToEvaluate);
                             feedbackHTML += '<tr><td width="20">&nbsp;</td><td class="feedbackLineTxt"><span class="small">' + feedback + '</span></td></tr>';
                         }
                         feedbackHTML += '<tr><td width="20">&nbsp;</td><td class="feedbackLineSpacer">&nbsp;</td></tr>';
                         feedbackHTML += '</table>';
                     }
                   }
                 }


                 var goodAnswerCountRequired = this.blankCount;

                 if(this.mustGiveAllGoodAnswers == false){
                     var wrongAnswerCountPossible = this.blankCount;
                     wrongAnswerCount = this.blankCount - goodAnswerCount;

                     var goodAnswerPonderation = ((goodAnswerCount > 0) ? this.ponderation : 0);
                     var wrongAnswerPonderation = this.ponderation / wrongAnswerCountPossible;

                     this.currentScore = Math.max((goodAnswerPonderation - (wrongAnswerPonderation * wrongAnswerCount)),0);

                 }else{
                     this.currentScore = (Math.min(Math.max((goodAnswerCount - wrongAnswerCount),0),goodAnswerCountRequired) / goodAnswerCountRequired) * this.ponderation;
                 }
                 
                 
                 if (this.blankCount != answerCount && foundRealWrongAnswer == false) {
                     blankAnswer = true;
                 }
           }
        }
        else if (this.questionType == 2){
            for(i = 1;i <= this.blankCount;i++){
                var bulletImage = 'bullet_red.png';  
                var answerIsGood = false;
                var feedback = '';
                var displayFeedback = false;
                var indexGoodFeedback = -1;
                var choice = cleanForValid(document.getElementById('idinput' + i).value);
                
                
                if (this.bPoncCompte == false){
                   choice = choice.replace(/\,/g, '');
                   choice = choice.replace(/\./g, '');
                   choice = choice.replace(/\;/g, '');
                   choice = choice.replace(/\:/g, '');
                   choice = choice.replace(/\!/g, '');
                   choice = choice.replace(/\?/g, '');
                   choice = choice.replace(/\«/g, '');
                   choice = choice.replace(/\»/g, '');
                }
                

                for(j = 0;j < this.goodAnswers.length;j++){
                    if (this.bCaseSens == false){
                        if (choice.toLowerCase() == this.goodAnswers[j].toLowerCase()){
                            if (i == this.goodPosition[j]){
                                answerIsGood = true;
                                indexGoodFeedback = j;
                            }
                        }
                    }  
                    else {
                        if (choice == this.goodAnswers[j]){
                            if (i == this.goodPosition[j]){
                                answerIsGood = true;
                                indexGoodFeedback = j;
                            }
                        }
                    }
                }

                if (answerIsGood == true){
                    answerCount++;
                    goodAnswerCount++;
                    bulletImage = 'bullet_green.png';
                    displayFeedback = true;
                    feedback = this.goodFeedback[indexGoodFeedback];
                }
                else{
                    if (choice != ''){
                       answerCount++;
                       wrongAnswerCount++;
                       displayFeedback = true;

                       for(k = 0;k < this.wrongAnswers.length;k++){
                           if (choice == this.wrongAnswers[k]){
                              if (i == this.badPosition[k])
                                  feedback = this.badFeedback[k];
                           }
                       }

                       if (feedback == '') {
                          feedback = this.otherAnswersFeedback[i];
                       }
                       
                       foundRealWrongAnswer = true;
                    }
                    else{
                        wrongAnswerCount++;
                        blankAnswer = true;
                    }
                }

                if (displayFeedback == true){
                    var label = '';
                    label = '';
                    if(this.labelType == this.ALPHA){
                        label = getLetterLabel(answerCount).toUpperCase() + ')';
                    }else if(this.labelType == this.NUMERIC){
                        label = (answerCount) + ')';
                    }

                    feedbackHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
                    feedbackHTML += '<tr>';
                    feedbackHTML += '<td width="20"><img src="images/' + bulletImage + '" /></td>';

                    if (label != '')
                        solutionHTML += '<td width="25">' + label + '</td>';

                    strToEvaluate = choice;
                    choice = evalStringForLexique(strToEvaluate);
                    feedbackHTML += '<td>' + choice + '</td></tr>';

                    if (feedback != ''){
                        strToEvaluate = feedback;
                        feedback = evalStringForLexique(strToEvaluate);
                        feedbackHTML += '<tr><td width="20">&nbsp;</td><td class="feedbackLineTxt"><span class="small">' + feedback + '</span></td></tr>';
                    }

                    feedbackHTML += '<tr><td width="20">&nbsp;</td><td class="feedbackLineSpacer">&nbsp;</td></tr>';
                    feedbackHTML += '</table>';
                }
            }

            var goodAnswerCountRequired = this.blankCount;

            if(this.mustGiveAllGoodAnswers == false){
                var wrongAnswerCountPossible = this.blankCount;
              
                var goodAnswerPonderation = ((goodAnswerCount > 0) ? this.ponderation : 0);
                var wrongAnswerPonderation = this.ponderation / wrongAnswerCountPossible;

                this.currentScore = Math.max((goodAnswerPonderation - (wrongAnswerPonderation * wrongAnswerCount)),0);

            }else{
                this.currentScore = (Math.min(Math.max((goodAnswerCount - wrongAnswerCount),0),goodAnswerCountRequired) / goodAnswerCountRequired) * this.ponderation;
            }
            
            
            if (this.blankCount != answerCount && foundRealWrongAnswer == false) {
               blankAnswer = true;
            }
            else if (foundRealWrongAnswer == true) {
               blankAnswer = false;
            }
        }
        else if (this.questionType == 3){
            for(i = 1;i <= this.selectCount;i++){
                var bulletImage = 'bullet_red.png';  
                var choice = document.getElementById('select' + i).value;
                var feedback = '';
                var displayFeedback = false;

                if (choice == 0){
                    wrongAnswerCount++;
                    blankAnswer = true;

                    choice = "";
                }
                else if (this.selectAnswer[choice - 1] == true){
                    answerCount++;
                    goodAnswerCount++;
                    bulletImage = 'bullet_green.png';
                    displayFeedback = true;
                    feedback = this.selectFeedBack[choice - 1];

                    choice = this.selectElement[choice - 1];
                }
                else{
                    answerCount++;
                    wrongAnswerCount++;
                    displayFeedback = true;
                    feedback = this.selectFeedBack[choice - 1];

                    choice = this.selectElement[choice - 1];
                }

                if (displayFeedback == true){
                    var label = '';
                    label = '';
                    if(this.labelType == this.ALPHA){
                        label = getLetterLabel(answerCount).toUpperCase() + ')';
                    }else if(this.labelType == this.NUMERIC){
                        label = (answerCount) + ')';
                    }

                    feedbackHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
                    feedbackHTML += '<tr>';
                    feedbackHTML += '<td width="20"><img src="images/' + bulletImage + '" /></td>';

                    if (label != '')
                        solutionHTML += '<td width="25">' + label + '</td>';

                    strToEvaluate = choice;
                    choice = evalStringForLexique(strToEvaluate);
                    feedbackHTML += '<td>' + choice + '</td></tr>';

                    if (feedback != ''){
                        strToEvaluate = feedback;
                        feedback = evalStringForLexique(strToEvaluate);
                        feedbackHTML += '<tr><td width="20">&nbsp;</td><td class="feedbackLineTxt"><span class="small">' + feedback + '</span></td></tr>';
                    }
                    feedbackHTML += '<tr><td width="20">&nbsp;</td><td class="feedbackLineSpacer">&nbsp;</td></tr>';
                    feedbackHTML += '</table>';
                }
            }

            var goodAnswerCountRequired = this.selectCount;

            if(this.mustGiveAllGoodAnswers == false){
                var wrongAnswerCountPossible = this.selectCount;
              
                var goodAnswerPonderation = ((goodAnswerCount > 0) ? this.ponderation : 0);
                var wrongAnswerPonderation = this.ponderation / wrongAnswerCountPossible;

                this.currentScore = Math.max((goodAnswerPonderation - (wrongAnswerPonderation * wrongAnswerCount)),0);

            }else{
                this.currentScore = (Math.min(Math.max((goodAnswerCount - wrongAnswerCount),0),goodAnswerCountRequired) / goodAnswerCountRequired) * this.ponderation;
            }
        }

        this.status = this.quiz.statusToRedo;
        
        
        
        if(blankAnswer == true){
            feedbackHTML = '<span class="Yellow">' + this.page.incompleteAnswerLabel + '</span><br /><br />' + feedbackHTML;
            this.statusMenuPages = -1;
        }else if(this.currentScore == this.ponderation){
            feedbackHTML = '<span class="Green">' + this.page.goodAnswerLabel + '</span><br /><br />' + feedbackHTML;
            this.status = this.quiz.statusCompleted;
            this.statusMenuPages = 1;
        }else{
            if(wrongAnswerCount > 0) {
                feedbackHTML = '<span class="Red">' + this.page.wrongAnswerLabel + '</span><br /><br />' + feedbackHTML;
                this.statusMenuPages = 0;
            }
            else {
                feedbackHTML = '<span class="Yellow">' + this.page.incompleteAnswerLabel + '</span><br /><br />' + feedbackHTML;
                this.statusMenuPages = -1;
            }
        }

        setFeedback(feedbackHTML);
        openFeedback();
        
        return this.currentScore;
    },
    
    showSolution: function(){
        var solutionHTML =  this.quiz.solutionLabel + '<br /><br />';
        var whereToChoice;
        var choice;
        var label = '';
        var theText;
        var blankReplace;
        var strToEvaluate;

        if (this.questionType == 1){

            theText = this.initialMainText;

            for(i = 1;i <= this.blankCount;i++){
                choice = '';

                for(j = 0;j < this.goodAnswers.length;j++){
                    if (i == this.goodPosition[j]){
                        if (choice == '')
                            choice += this.goodAnswers[j];
                        else
                            choice += " / " + this.goodAnswers[j];
                    }
                }

                blankReplace = "%blank" + i;
                theText = theText.replace(blankReplace,"<b>" + choice + "</b>");
            }

             solutionHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
             solutionHTML += '<tr>';

             if (label != '')
                 solutionHTML += '<td width="25">' + label + '</td>';

             strToEvaluate = theText;    
             theText = evalStringForLexique(strToEvaluate);
             
             solutionHTML += '<td>' + theText + '</td>';
             solutionHTML += '</tr>';
             solutionHTML += '</table><br />';
        }
        else if (this.questionType == 2){
            //suite à divers modifs, on retrouve le même code que questionType 1, mais attendre de finir avant d'enlever au cas ou changement.
            theText = this.initialMainText;

            for(i = 1;i <= this.blankCount;i++){
                choice = '';

                for(j = 0;j < this.goodAnswers.length;j++){
                    if (i == this.goodPosition[j]){
                        if (choice == '')
                            choice += this.goodAnswers[j];
                        else
                            choice += " / " + this.goodAnswers[j];
                    }
                }

                blankReplace = "%blank" + i;
                theText = theText.replace(blankReplace,"<b>" + choice + "</b>");
            }

            solutionHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
            solutionHTML += '<tr>';

            if (label != '')
                solutionHTML += '<td width="25">' + label + '</td>';

            strToEvaluate = theText;    
            theText = evalStringForLexique(strToEvaluate);
 
            solutionHTML += '<td>' + theText + '</td>';
            solutionHTML += '</tr>';
            solutionHTML += '</table><br />';
        }
        else if (this.questionType == 3){

            var theText = this.initialMainText;

            for(i = 1;i <= this.selectCount;i++){
                label = '';
                choice = '';

                for(j = 0;j <= this.selectElement.length - 1;j++){
                    if (this.selectAnswer[j] == true){
                        if (this.selectPosition[j] == i){
                            if (choice == '')
                                choice += this.selectElement[j];
                            else
                                choice += " / " + this.selectElement[j];
                        }
                    }
                }

                blankReplace = "%blank" + i;
                theText = theText.replace(blankReplace,"<b>" + choice + "</b>");
            }

            solutionHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
            solutionHTML += '<tr>';

            if (label != '')
                solutionHTML += '<td width="25">' + label + '</td>';
                
            strToEvaluate = theText;    
            theText = evalStringForLexique(strToEvaluate);    

            solutionHTML += '<td>' + theText + '</td>';
            solutionHTML += '</tr>';
            solutionHTML += '</table><br />';
        }

        setFeedback(solutionHTML);
        openFeedback();
    },
    
    redo: function(){
        this.currentSequence = null;
        this.currentChoices = null;
        this.currentScore = 0;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;

        closeFeedback();

        if (this.questionType == 1){
            $('question').update('');
        }

        this.display();
    },

    redoQuiz: function(){
        this.currentSequence = null;
        this.currentChoices = null;
        this.currentScore = 0;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;
    },

    shuffleChoices: function(o){
        for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
        return o;
    },

    trim : function(myString){
         return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
    },
    getConsigne: function(){
        return this.quiz.consigneBlankText;
    }
});

