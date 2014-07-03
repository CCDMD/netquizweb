var QuestionClassement = Class.create({
    sQuestionType: 'CLASSEMENT',

    ALPHA: 0,
    NUMERIC: 1,

    //settings
    elementType: 0, //0 = mots, 1 = images. Pour les choix de réponse.
    tagType: 0, //0 = mots, 1 = images. Pour les étiquettes.
    mustGiveAllGoodAnswers: false, 

    currentSequence: null,
    currentChoices: null,
    currentScore: 0,
    ponderation: 0,
    triesCount: 0,
    status:'',
    statusMenuPages: -1,
    selectCount: 0,
    orientation: 0,
    
    initialize: function(quiz, page, questionNb){
        this.quiz = quiz;
        this.page = page;
        this.questionNb = questionNb;
           
        this.choices = new Array();
        this.displayChoices = new Array();
        this.goodFeedback = new Array();
        this.badFeedback = new Array();
        this.container = new Array();
        this.containerid = new Array();
        this.containerHTML = new Array();
        this.evaluateContainer = new Array();

        this.goodAnswers = new Array();
        this.wrongAnswers = new Array();
        this.incompleteAnswers = new Array();
        this.numberGoodAnswersContainerNeeded = new Array();
        this.goodPosition = new Array();
        this.badPosition = new Array();
        this.selectElement = new Array();
        this.selectFeedBack = new Array();
        this.selectAnswer = new Array();
        
        this.imageCategory = new Array();
        this.retroactionChoice = new Array();
        this.arrSrcMediaCategoryContainer = new Array();

        this.status = this.quiz.statusToDo;

        this.userAnswers = '';
        this.emptyHTMLContainers = '';
        this.emptyLineId = 1;
    },

    addContainer: function(tag, id, mediaCategory){
        var strToEvaluate;
    
        this.container[this.container.length] = tag;
        this.containerid[this.containerid.length] = id;
        this.containerHTML[this.containerHTML.length] = "";
        this.arrSrcMediaCategoryContainer[this.arrSrcMediaCategoryContainer.length] = mediaCategory
        
        strToEvaluate = this.container[this.container.length - 1];
        this.container[this.container.length - 1] = evalStringForLexique(strToEvaluate);
    },

    addChoice: function(choice, goodPosition, imageCategory, retroactionChoice){
        var theLength = this.choices.length;

        this.choices[this.choices.length] = choice;
        this.goodPosition[this.choices.length] = goodPosition;
        this.displayChoices[theLength] = this.choices.length - 1;
        
        this.imageCategory[this.imageCategory.length] = imageCategory;
        
        this.retroactionChoice[this.retroactionChoice.length] = retroactionChoice;
        
        this.numberGoodAnswersContainerNeeded.push(goodPosition);
    },

    setGoodAnswer: function(goodAnswer, position){
        var strToEvaluate;           
    
        this.goodAnswers[position] = goodAnswer;
        
        strToEvaluate = this.goodAnswers[position];
        this.goodAnswers[position] = evalStringForLexique(strToEvaluate);
    },

    setWrongAnswer: function(wrongAnswer, position){
        var strToEvaluate;
        
        this.wrongAnswers[position] = wrongAnswer;
        
        strToEvaluate = this.wrongAnswers[position];
        this.wrongAnswers[position] = evalStringForLexique(strToEvaluate);
    },
    
    setIncompleteAnswer: function(incompleteAnswer, position){
        var strToEvaluate;  
    
        this.incompleteAnswers[position] = incompleteAnswer;
        
        strToEvaluate = this.incompleteAnswers[position];
        this.incompleteAnswers[position] = evalStringForLexique(strToEvaluate);
    },

    shuffle: function(){
        this.shuffleChoices(this.displayChoices);
    },
    
    getRetroactionByContainerChoice: function(choiceId, containerId){
        var strRetroaction = this.retroactionChoice[choiceId - 1][containerId - 1];
        var strToEvaluate;

        strToEvaluate = strRetroaction;
        strRetroaction = evalStringForLexique(strToEvaluate); 
        
        return strRetroaction;
    },

    display : function(){
        var divChoices = null;
        var spanChoice = null;
        var inputUser = null;
        var mainContainer = null;
        var indexContainer = null;
        var divTag = null;
        var divTagSpacer = null;
        var divContainers = null;
        var divContainerID = null;
        var divContainerSpacer = null;
        var divEmptyLine = null;
        var divSpacerLine = null;
        var divClearBoth = null;
        var inputContainer = null;
        var nbOnLine = 0;
        var nbOnLine2 = 0;
        var idDrag = "";
        var idDrop = "";
        var idDragInput = "";
        var idImgInDragInput = "";
        var idDragContentOnly  = "";
        var indexValue;
        var goodPositionContainer = "";
        var strUserAnswers = "";
        var lengthChoices;
        var startIndex = 0;
        var finishIndex = 0;
        var arrayDraggable = new Array();

        this.emptyLineId = 1;

        lengthChoices = this.choices.length;

        divChoices = document.createElement('div');
        divChoices.id = 'idChoices';

        $('question').appendChild(divChoices);

        for(i = 0;i < this.choices.length;i++){
            indexValue = this.displayChoices[i] + 1;
            goodPositionContainer = this.goodPosition[this.displayChoices[i] + 1];
            idDrag = "drag" + indexValue;
            idDragInput = "dragInput" + indexValue;
            idImgInDragInput = "imgDragInput" + indexValue;
            idDragContentOnly = "dragContentOnly" + indexValue;

            spanChoice = document.createElement('span');

            if (this.elementType == 0){
               var escapedString = this.choices[this.displayChoices[i]].replace(/"/g, '\\"');
               escapedString = escapedString.replace(/'/g, "&#39;");
            
               spanChoice.style.cursor = 'move';
               spanChoice.innerHTML = "<b>" + this.choices[this.displayChoices[i]] + "</b><input type=\"hidden\" style=\"width:20px;\" id=\"" + idDragInput + "\" value=\"" + goodPositionContainer + "\">" + "<input type=\"hidden\" style=\"width:50px;\" id=\"" + idDragContentOnly + "\" value='" + escapedString + "'>";
               spanChoice.id = idDrag;
            }
            else if (this.elementType == 1){
               if (this.imageCategory[this.displayChoices[i]] == 1) {
                   spanChoice.innerHTML = "<img id=\"" + idDrag + "\" class=\"userImgChoice\" src=\"" + this.quiz.mediasFolder + "/" + this.choices[this.displayChoices[i]] + "\"><input type=\"hidden\" style=\"width:20px;\" id=\"" + idDragInput + "\" value=\"" + goodPositionContainer + "\">" + "<input type=\"hidden\" style=\"width:50px;\" id=\"" + idDragContentOnly + "\" value=\"" + this.quiz.mediasFolder + "/" + this.choices[this.displayChoices[i]] + "\">";
               }
               else if (this.imageCategory[this.displayChoices[i]] == 2) {
                   spanChoice.innerHTML = "<img id=\"" + idDrag + "\" class=\"userImgChoice\" src=\"" + this.choices[this.displayChoices[i]] + "\"><input type=\"hidden\" style=\"width:20px;\" id=\"" + idDragInput + "\" value=\"" + goodPositionContainer + "\">" + "<input type=\"hidden\" style=\"width:50px;\" id=\"" + idDragContentOnly + "\" value=\"" + this.choices[this.displayChoices[i]] + "\">";
               }
               
               spanChoice.id = 'spanContent' + idDrag;
            }

            $('idChoices').appendChild(spanChoice);

            if (this.orientation == 0){
                if ((i + 1) < this.choices.length){
                    if (this.elementType == 0)
                        $('idChoices').innerHTML = $('idChoices').innerHTML + "<img src=\"images/pagechoicesep.png\" border=\"0\">";
                    else if (this.elementType == 1)
                        $('idChoices').innerHTML = $('idChoices').innerHTML + "<img src=\"images/pagechoicesep.png\" border=\"0\" style=\"margin-bottom:10px;\">";
                }
            }
            else if (this.orientation == 1){
                if (this.elementType == 0)
                    $('idChoices').innerHTML = $('idChoices').innerHTML + "<img src=\"images/spacer.gif\" border=\"0\"><div style=\"height:10px;\"><br />"; //doit avoir spacer.gif + <br /> sinon, bogue weird IE8: le drag se met "en-dessous" du classeur...
                else if (this.elementType == 1){
                    $('idChoices').innerHTML = $('idChoices').innerHTML + "<img src=\"images/spacer.gif\" border=\"0\"><br />";
                }
            }
        }

        $('idChoices').innerHTML = $('idChoices').innerHTML + "<div style=\"height:35px;\"></div>";

        divContainers = document.createElement('div');
        divContainers.id = 'idContainers';
        $('question').appendChild(divContainers);

        /*others*/
        var typeChoiceDrag = document.createElement('input');
        typeChoiceDrag.id = 'elementTypeDrag';
        typeChoiceDrag.type = 'hidden';

        $('idContainers').appendChild(typeChoiceDrag);
        document.getElementById('elementTypeDrag').value = this.elementType;


        var counterDrag = document.createElement('input');
        counterDrag.id = 'counterDrag';
        counterDrag.type = 'hidden';

        $('idContainers').appendChild(counterDrag);
        document.getElementById('counterDrag').value = 0;

        /*********/

        for(i = 0;i < this.container.length;i++){
            indexContainer = i + 1;

            mainContainer = "main" + indexContainer;

            divContainers = document.createElement('div');
            divContainers.id = mainContainer;

            if (this.container.length%2){
                //nb impair de classeur
                divContainers.className = 'mainContainers2';

                $('idContainers').appendChild(divContainers);

                finishIndex = finishIndex + 1;

                if (nbOnLine < 3 && finishIndex < this.container.length){

                   nbOnLine = nbOnLine + 1;

                   if (nbOnLine == 3 && finishIndex < this.container.length){
                       divEmptyLine = document.createElement('div');
                       divEmptyLine.className = 'emptyLine';
                       divEmptyLine.id = 'emptyLine' + this.emptyLineId;

                       $('idContainers').appendChild(divEmptyLine);

                       this.emptyLineId = this.emptyLineId + 1;
                       nbOnLine = 0;
                   }
                   else{
                       divTagSpacer = document.createElement('div');
                       divTagSpacer.className = 'mainSpaceStyle2';
                       $('idContainers').appendChild(divTagSpacer);
                   }
                }
                else{
                    if (finishIndex == this.container.length){
                        divEmptyLine = document.createElement('div');
                        divEmptyLine.className = 'emptyLine';
                        divEmptyLine.id = 'emptyLine' + this.emptyLineId;

                        $('idContainers').appendChild(divEmptyLine);

                        this.emptyLineId = this.emptyLineId + 1;
                    }
                }
            }
            else{
                //pair
                divContainers.className = 'mainContainers1';

                $('idContainers').appendChild(divContainers);

                finishIndex = finishIndex + 1;

                if (nbOnLine < 1 && finishIndex < this.container.length){
                   divTagSpacer = document.createElement('div');
                   divTagSpacer.className = 'mainSpaceStyle1';
                   $('idContainers').appendChild(divTagSpacer);

                   nbOnLine = nbOnLine + 1;
                }
                else{
                   divEmptyLine = document.createElement('div');
                   divEmptyLine.className = 'emptyLine';
                   divEmptyLine.id = 'emptyLine' + this.emptyLineId;

                   $('idContainers').appendChild(divEmptyLine);

                   this.emptyLineId = this.emptyLineId + 1
                   nbOnLine = 0;
                }
            }
        }

        var divDumpDrags = document.createElement('div');
        divDumpDrags.id = 'idDumpDrags';
        $('question').appendChild(divDumpDrags);
        $(divDumpDrags).hide();

        var inputArrayDraggable = document.createElement('input');
        inputArrayDraggable.id = 'inputArrayDraggable';
        inputArrayDraggable.type = 'hidden';
        $('question').appendChild(inputArrayDraggable);

        var inputRedoQuestion = document.createElement('input');
        inputRedoQuestion.id = 'inputRedoQuestion';
        inputRedoQuestion.type = 'hidden';
        $('question').appendChild(inputRedoQuestion);

        nbOnLine = 0;
        startIndex = 0;
        finishIndex = 0;

        var tagIdSplit;
        var indexMaxTag;
        var leHeight = 0;
        var tagId1;
        var tagId2;
        var tagId3;
        var tagH1 = 0;
        var tagH2 = 0;
        var tagH2 = 0;
        var divTagId;
        var tagCont;
        var inputContainerID;

        var tallestFound = false;

        for(i = 0;i < this.container.length;i++){
            var divContainerRetro = document.createElement('div');
            var theRetroId = i + 1;
            theRetroId = "retroContainer" + theRetroId

            var theTagId = i + 1;
            theTagId = "tag" + theTagId;

            indexContainer = i + 1;
            mainContainer = "main" + indexContainer;

            divTag = document.createElement('div');
            divTag.id = theTagId;

            divContainerID = document.createElement('div');

            idDrop = i + 1;
            idDrop = "drop" + idDrop;

            divContainerID.id = idDrop;

            if (this.container.length%2){
                //nb impair de classeur
                
                /****SANS BORDURE BLANCHE ****/
                /*divTag.className = 'tagStyle2';
                $(mainContainer).appendChild(divTag);
                $(theTagId).update(this.container[i]);*/
                
                if (this.tagType == 0)
                   divTag.className = 'tagStyle1_2';
                else if (this.tagType == 1)
                   divTag.className = 'tagImgStyle2';

                $(mainContainer).appendChild(divTag);
                divTagId = "divIn" + theTagId;

                if (this.tagType == 0){
                   tagCont = "<div id=\"" + divTagId + "\" class=\"divInTag\">" + this.container[i] + "</div>";
                }
                else if(this.tagType == 1){
                   if (this.arrSrcMediaCategoryContainer[i] == 1){
                       tagCont = "<div id=\"" + divTagId + "\"><img class=\"imgTag\" src=\"" + this.quiz.mediasFolder + "/" + this.container[i] + "\"></div>";
                   }
                   else {
                       tagCont = "<div id=\"" + divTagId + "\"><img class=\"imgTag\" src=\"" + this.container[i] + "\"></div>";
                   }
                }

                $(theTagId).update(tagCont);

                if (this.tagType == 0){
                   //REMOVED... Weird bogue 3 containers text...
                   //document.getElementById(divTagId).style.width = (document.getElementById(theTagId).offsetWidth - 2) + 'px';
                }

                tagIdSplit = theTagId.split("tag");
                indexMaxTag = tagIdSplit[1];

                nbOnLine = nbOnLine + 1;

                if (nbOnLine == 3){
                    tagId1 = indexMaxTag - 2;
                    tagId1 = "tag" + tagId1;

                    tagId2 = indexMaxTag - 1;
                    tagId2 = "tag" + tagId2;

                    tagId3 = theTagId;

                    tagH1 = document.getElementById(tagId1).offsetHeight;
                    tagH2 = document.getElementById(tagId2).offsetHeight;
                    tagH3 = document.getElementById(tagId3).offsetHeight;

                    if (this.tagType == 0){

                       while (tallestFound == false)
                       {
                           if (tagH1 >= tagH2){
                               tagH2 = tagH1;
                           }

                           if (tagH2 >= tagH3){
                               tagH3 = tagH2;
                           }

                           if (tagH3 >= tagH1){
                               tagH1 = tagH3;
                           }

                           if (tagH1 == tagH2 && tagH1 == tagH3){
                               document.getElementById(tagId1).style.height = (tagH1 - 2) + 'px';
                               document.getElementById(tagId2).style.height = (tagH2 - 2) + 'px';
                               document.getElementById(tagId3).style.height = (tagH3 - 2) + 'px';

                               divTagId = "divIn" + tagId1;
                               document.getElementById(divTagId).style.height = (document.getElementById(tagId1).offsetHeight - 9) + 'px';

                               divTagId = "divIn" + tagId2;
                               document.getElementById(divTagId).style.height = (document.getElementById(tagId2).offsetHeight - 9) + 'px';

                               divTagId = "divIn" + tagId3;
                               document.getElementById(divTagId).style.height = (document.getElementById(tagId3).offsetHeight - 9) + 'px';

                               tallestFound = true;
                           }
                        }
                     }
                     else if (this.tagType == 1){

                     }

                     nbOnLine = 0;
                }
                else if (finishIndex == this.container.length){
                    var tagId1 = "tag" + (indexContainer - 1);
                    var tagId2 = theTagId;

                    if (document.getElementById(tagId1).offsetHeight > document.getElementById(tagId2).offsetHeight){
                        document.getElementById(tagId2).style.height = (document.getElementById(tagId1).offsetHeight - 2) + 'px';

                        divTagId = "divIn" + tagId2;
                        document.getElementById(divTagId).style.height = (document.getElementById(tagId1).offsetHeight - 9) + 'px';

                    }
                    else if(document.getElementById(tagId2).offsetHeight > document.getElementById(tagId1).offsetHeight){
                        document.getElementById(tagId1).style.height = (document.getElementById(tagId2).offsetHeight - 2)  + 'px';

                        divTagId = "divIn" + tagId1;
                        document.getElementById(divTagId).style.height = (document.getElementById(tagId2).offsetHeight - 9) + 'px';
                    }
                }

                divContainerID.className = 'containerStyle2';
                $(mainContainer).appendChild(divContainerID);

                divContainerRetro.id = theRetroId;
                divContainerRetro.className = 'retroContainerStyle2';
                $(mainContainer).appendChild(divContainerRetro);
            }
            else{
                //pair
                if (this.tagType == 0)
                   divTag.className = 'tagStyle1';
                else if (this.tagType == 1)
                   divTag.className = 'tagImgStyle1';

                $(mainContainer).appendChild(divTag);
                divTagId = "divIn" + theTagId;

                if (this.tagType == 0){
                    tagCont = "<div id=\"" + divTagId + "\" class=\"divInTag\">" + this.container[i] + "</div>";
                }
                else if(this.tagType == 1){
                    if (this.arrSrcMediaCategoryContainer[i] == 1){
                       tagCont = "<div id=\"" + divTagId + "\"><img class=\"imgTag\" src=\"" + this.quiz.mediasFolder + "/" + this.container[i] + "\"></div>";
                    }
                    else {
                       tagCont = "<div id=\"" + divTagId + "\"><img class=\"imgTag\" src=\"" + this.container[i] + "\"></div>";
                    }
                }

                $(theTagId).update(tagCont);

                nbOnLine = nbOnLine + 1;

                if (nbOnLine == 2){
                    var tagId1 = "tag" + (indexContainer - 1);
                    var tagId2 = theTagId;

                    if (document.getElementById(tagId1).offsetHeight > document.getElementById(tagId2).offsetHeight){
                        document.getElementById(tagId2).style.height = (document.getElementById(tagId1).offsetHeight - 2) + 'px';

                        divTagId = "divIn" + tagId2;
                        document.getElementById(divTagId).style.height = (document.getElementById(tagId2).offsetHeight - 9) + 'px';
                    }
                    else if(document.getElementById(tagId2).offsetHeight > document.getElementById(tagId1).offsetHeight){
                        document.getElementById(tagId1).style.height = (document.getElementById(tagId2).offsetHeight - 2) + 'px';

                        divTagId = "divIn" + tagId1;
                        document.getElementById(divTagId).style.height = (document.getElementById(tagId1).offsetHeight - 9) + 'px';
                    }

                    nbOnLine = 0;
                }

                divContainerID.className = 'containerStyle1';
                $(mainContainer).appendChild(divContainerID);

                divContainerRetro.id = theRetroId;
                divContainerRetro.className = 'retroContainerStyle1';
                $(mainContainer).appendChild(divContainerRetro);
            }

            finishIndex = finishIndex + 1;

            inputContainerID = 'inputContainer' + idDrop;

            inputContainer = document.createElement('input');
            inputContainer.id = inputContainerID;
            inputContainer.type = 'hidden';
            inputContainer.style.width = '250px';
            inputContainer.style.height = '150px';
            $('question').appendChild(inputContainer);
        }

        if (this.userChoicesHTML != null){
            $('idChoices').innerHTML = this.userChoicesHTML;
            $('idDumpDrags').innerHTML = this.idDumpDragsHTML;
            document.getElementById('inputArrayDraggable').value = this.saveArrayDraggable;
            document.getElementById('counterDrag').value = this.counterDragged;

            for(i = 0;i < this.container.length;i++){
                var innerHTMLContainer = "";  
                indexContainer = i + 1;
                inputContainerID = "inputContainerdrop" + indexContainer;

                document.getElementById(inputContainerID).value = this.containersHTML[i];

                var dropContainer = "drop" + indexContainer;
                var splitContainer = this.containersHTML[i].split("|");

                for(k = 0;k < splitContainer.length;k++){
                    innerHTMLContainer = innerHTMLContainer + splitContainer[k];
                }

                $(dropContainer).update(innerHTMLContainer);
            }

            for(i = 0;i < this.saveArrayDraggable.length - 1;i++){
                new Draggable(this.saveArrayDraggable[i], {revert:true} );
            }

            if (document.getElementById('inputArrayDraggable').value != ""){
                var strArrDraggable = document.getElementById('inputArrayDraggable').value;
                var splitArrDraggable = strArrDraggable.split(",");

                for(i = 0;i < splitArrDraggable.length - 1;i++){
                    arrayDraggable[arrayDraggable.length] = splitArrDraggable[i];
                }
            }
        }

        this.emptyHTMLContainers = $('idContainers').innerHTML;

        for(i = 0;i < this.choices.length;i++){
            idDrag = this.displayChoices[i] + 1;
            idDrag = "drag" + idDrag;

            new Draggable(idDrag, {revert:true, scroll:window,onStart:function(){closeFeedback();}});
        }

                for(i = 0;i < this.container.length;i++){
                    var theContainerLength = this.container.length;
                      
                    idDrop = i + 1;
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

                                if (drag.id != 'indice'){
                                   var idDragged = drag.id;
                                   var splitidDragged = idDragged.split("drag");

                                   var idDropped = drop.id;
                                   var splitidDropped = idDropped.split("drop");

                                   var splitidDraggedIndex;

                                   if (idDragged.indexOf("_") == -1){
                                       splitidDraggedIndex = -1;

                                       var counterDragged = parseInt(document.getElementById('counterDrag').value);
                                       counterDragged = counterDragged + 1;
                                       document.getElementById('counterDrag').value = counterDragged;
                                   }
                                   else{
                                       var checkDragLastIndex = idDragged.split("_");
                                       splitidDraggedIndex = checkDragLastIndex[checkDragLastIndex.length - 1];
                                   }

                                   var splitidDroppedIndex = trim(splitidDropped[1].substring(0,1));

                                   var idNewDrag = idDragged + "_" + splitidDroppedIndex;
                                   idNewDrag = idDragged + "_" + splitidDroppedIndex;
                                   
                                   var innerHTMLDragged;
                                   var innerHTMLDropped = $(idDropped).innerHTML;

                                   var theElementType = document.getElementById('elementTypeDrag').value;

                                   if (theElementType == 0){
                                       innerHTMLDragged = "<span id=\"" + idNewDrag + "\" style=\"cursor: move;\">" + $(idDragged).innerHTML + "</span>";
                                   }
                                   else if (theElementType == 1){
                                       var splitidDragged2;
                                       var splitidDraggedIndex2;

                                       if (splitidDraggedIndex == -1){
                                           splitidDraggedIndex2 = splitidDragged[1];
                                       }
                                       else{
                                           var splitidDragged2 = splitidDragged[1].split("_");
                                           splitidDraggedIndex2 = splitidDragged2[0];
                                       }

                                       var srcDrag = "dragContentOnly" + splitidDraggedIndex2;
                                       srcDrag = document.getElementById(srcDrag).value;

                                       var idDragInput2 = "dragInput" + splitidDraggedIndex2;
                                       var goodPositionContainer2 = document.getElementById(idDragInput2).value;
                                       var idDragContentOnly2 = "dragContentOnly" + splitidDraggedIndex2

                                       innerHTMLDragged  = "<img id=\"" + idNewDrag + "\" class=\"userImgChoice\" src=\"" + srcDrag + "\"><input type=\"hidden\" style=\"width:20px;\" id=\"" + idDragInput2 + "\" value=\"" + goodPositionContainer2 + "\">" + "<input type=\"hidden\" style=\"width:50px;\" id=\"" + idDragContentOnly2 + "\" value=\"" + srcDrag + "\">";
                                   }

                                   if (theElementType == 1)
                                       innerHTMLDragged = innerHTMLDragged.replace("userImgChoice", "userImgContainer");

                                   var innerHTMLContainer = "";
                                   var innerHTMLInputContainer = "";

                                   var theInputContainerID = 'inputContainerdrop' + splitidDroppedIndex;
                                   var innerHTMLTxt;

                                   if (theElementType == 0)
                                       innerHTMLTxt = document.getElementById(theInputContainerID).value + innerHTMLDragged + "<br /><div class=\"emptyLineElements\"></div>";
                                   else if (theElementType == 1)
                                       innerHTMLTxt = document.getElementById(theInputContainerID).value + innerHTMLDragged + "&nbsp;&nbsp;";

                                   if (splitidDraggedIndex != splitidDroppedIndex){

                                      document.getElementById(theInputContainerID).value = innerHTMLTxt + "|";

                                      if (theElementType == 0)
                                         $(idDragged).update('');

                                      $('idDumpDrags').appendChild(drag);
                                      $(idDragged).hide();

                                      for(j = 0;j < theContainerLength;j++){
                                          theInputContainerID = j + 1;
                                          theInputContainerID = 'inputContainerdrop' + theInputContainerID;
                                          innerHTMLTxt = document.getElementById(theInputContainerID).value;
                                          innerHTMLContainer = "";
                                          innerHTMLInputContainer = "";

                                          var splitContainer = innerHTMLTxt.split("|");

                                          for(k = 0;k < splitContainer.length;k++){
                                              if (splitContainer[k] != ''){
                                                  var checkDrag = splitContainer[k].indexOf(idDragged);

                                                  if (checkDrag == -1){
                                                      innerHTMLContainer = innerHTMLContainer + splitContainer[k];
                                                      innerHTMLInputContainer = innerHTMLInputContainer + splitContainer[k] + "|";
                                                  }
                                                  else{
                                                      var leIDCheckDrag = idDragged;
                                                      var checkDrag2 = leIDCheckDrag.indexOf("_");

                                                      if (checkDrag2 == -1){
                                                          innerHTMLContainer = innerHTMLContainer + splitContainer[k];
                                                          innerHTMLInputContainer = innerHTMLInputContainer + splitContainer[k] + "|";
                                                      }
                                                      else{
                                                          var checkWhereToDrop = idDragged.indexOf("_");

                                                          if (checkWhereToDrop != -1){
                                                              var dragFrom = idDragged.split("_");
                                                              var index1 = dragFrom[dragFrom.length - 1];

                                                              if (index1 != (j + 1)){
                                                                  var j1 = j + 1;
                                                                  innerHTMLContainer = innerHTMLContainer + splitContainer[k];
                                                                  innerHTMLInputContainer = innerHTMLInputContainer + splitContainer[k] + "|";
                                                              }
                                                          }
                                                      }
                                                  }
                                              }
                                          }

                                          var idcontainerNow = j + 1;
                                          idcontainerNow = "drop" + idcontainerNow;

                                          var idcontainerNowDrop = idcontainerNow;

                                          $(idcontainerNow).update(innerHTMLContainer);

                                          idcontainerNow = j + 1;
                                          idcontainerNow = "inputContainerdrop" + idcontainerNow;
                                          document.getElementById(idcontainerNow).value = innerHTMLInputContainer;
                                      }

                                      if (document.getElementById('inputRedoQuestion').value == 'true'){
                                          arrayDraggable = new Array();
                                          document.getElementById('inputRedoQuestion').value = "";
                                      }

                                      arrayDraggable[arrayDraggable.length] = idNewDrag;
                                      document.getElementById('inputArrayDraggable').value = document.getElementById('inputArrayDraggable').value + idNewDrag + ",";

                                      for(k = 0;k < arrayDraggable.length;k++){
                                          new Draggable(arrayDraggable[k], {revert:true, scroll:window,onStart:function(){closeFeedback();}});
                                      }

                                      //FACON LA PLUS SIMPLE DE REGLER LE GLICTH SOUS SAFARI (SI LONG TEXTE LAISSE TRAIL A CAUSE LE DRAG EST UN SPAN)
                                      //document.getElementById('scrollwrapper').style.height = (document.getElementById('scrollwrapper').offsetHeight - 1) + 'px';
                                      //document.getElementById('scrollwrapper').style.height = (document.getElementById('scrollwrapper').offsetHeight + 1) + 'px';
                                      
                                      //RAJOUTE BOB MOBILE. P-E PLUS DE GLITCH...
                                      //document.getElementById('scrollwrapper').style.height = '';

                                      document.getElementById('quizpage').style.backgroundColor = 'white';
                                      document.getElementById('scrollwrapper').style.backgroundColor = 'white';
                                  }
                               }
                            }
                         }
                      );
                }
    },

    displayRedo: function(){
        var idDrag;
        $('idChoices').innerHTML = '';
        $('idDumpDrags').innerHTML = '';

        document.getElementById('inputRedoQuestion').value = 'true';
        document.getElementById('inputArrayDraggable').value = '';
        document.getElementById('counterDrag').value = 0;

        for(i = 0;i < this.choices.length;i++){
            indexValue = this.displayChoices[i] + 1;
            goodPositionContainer = this.goodPosition[this.displayChoices[i] + 1];
            idDrag = "drag" + indexValue;
            idDragInput = "dragInput" + indexValue;
            idImgInDragInput = "imgDragInput" + indexValue;
            idDragContentOnly = "dragContentOnly" + indexValue;

            spanChoice = document.createElement('span');
            spanChoice.style.cursor = 'move';

            if (this.elementType == 0){
               var escapedString = this.choices[this.displayChoices[i]].replace(/"/g, '\\"');
               escapedString = escapedString.replace(/'/g, "&#39;");
            
               spanChoice.innerHTML = "<b>" + this.choices[this.displayChoices[i]] + "</b><input type=\"hidden\" style=\"width:20px;\" id=\"" + idDragInput + "\" value=\"" + goodPositionContainer + "\">" + "<input type=\"hidden\" style=\"width:50px;\" id=\"" + idDragContentOnly + "\" value='" + escapedString + "'>";
               spanChoice.id = idDrag;
            }
            else if (this.elementType == 1){
               if (this.imageCategory[this.displayChoices[i]] == 1) {  
                   spanChoice.innerHTML = "<img id=\"" + idDrag + "\" class=\"userImgChoice\" src=\"" + this.quiz.mediasFolder + "/" + this.choices[this.displayChoices[i]] + "\"><input type=\"hidden\" style=\"width:20px;\" id=\"" + idDragInput + "\" value=\"" + goodPositionContainer + "\">" + "<input type=\"hidden\" style=\"width:50px;\" id=\"" + idDragContentOnly + "\" value=\"" + this.quiz.mediasFolder + "/" + this.choices[this.displayChoices[i]] + "\">";
               }
               else if (this.imageCategory[this.displayChoices[i]] == 2) {
                   spanChoice.innerHTML = "<img id=\"" + idDrag + "\" class=\"userImgChoice\" src=\"" + this.choices[this.displayChoices[i]] + "\"><input type=\"hidden\" style=\"width:20px;\" id=\"" + idDragInput + "\" value=\"" + goodPositionContainer + "\">" + "<input type=\"hidden\" style=\"width:50px;\" id=\"" + idDragContentOnly + "\" value=\"" + this.choices[this.displayChoices[i]] + "\">";
               }
               
               spanChoice.id = 'spanContent' + idDrag;
            }

            $('idChoices').appendChild(spanChoice);

            if (this.orientation == 0){
                if ((i + 1) < this.choices.length){
                    if (this.elementType == 0)
                        $('idChoices').innerHTML = $('idChoices').innerHTML + "<img src=\"images/pagechoicesep.png\" border=\"0\">";
                    else if (this.elementType == 1)
                        $('idChoices').innerHTML = $('idChoices').innerHTML + "<img src=\"images/pagechoicesep.png\" border=\"0\" style=\"margin-bottom:10px;\">";
                }
            }
            else if (this.orientation == 1){
                if (this.elementType == 0)
                    $('idChoices').innerHTML = $('idChoices').innerHTML + "<img src=\"images/spacer.gif\" border=\"0\"><div style=\"height:10px;\"><br />"; //doit avoir spacer.gif + <br /> sinon, bogue weird IE8: le drag se met "en-dessous" du classeur...
                else if (this.elementType == 1){
                    $('idChoices').innerHTML = $('idChoices').innerHTML + "<img src=\"images/spacer.gif\" border=\"0\"><br />";
                }
            }
        }

        $('idChoices').innerHTML = $('idChoices').innerHTML + "<div style=\"height:35px;\"></div>";


        for(i = 0;i < this.choices.length;i++){
            idDrag = this.displayChoices[i] + 1;
            idDrag = "drag" + idDrag;

            new Draggable(idDrag, {revert:true, scroll:window,onStart:function(){closeFeedback();}});
        }

        for(i = 0;i < this.container.length;i++){
            var indexContainer = i + 1;
            var inputContainerID = "inputContainerdrop" + indexContainer;

            document.getElementById(inputContainerID).value = '';

            var dropContainer = "drop" + indexContainer;

            $(dropContainer).innerHTML = '';
        }

    },

    save: function(){
        this.counterDragged = document.getElementById('counterDrag').value;
        this.userChoicesHTML = $('idChoices').innerHTML;
        this.idDumpDragsHTML = $('idDumpDrags').innerHTML;
        this.containersHTML = new Array();
        this.saveArrayDraggable = new Array();

        for(i = 0;i < this.container.length;i++){
            var indexContainer = i + 1;
            var inputContainerID = "inputContainerdrop" + indexContainer;

            this.containersHTML[this.containersHTML.length] = document.getElementById(inputContainerID).value;
        }

        var strDraggableArray = document.getElementById('inputArrayDraggable').value;
        var splitStrDraggableArray = strDraggableArray.split(",");

        for(i = 0;i < splitStrDraggableArray.length;i++){
            this.saveArrayDraggable[this.saveArrayDraggable.length] = splitStrDraggableArray[i];
        }
    },

    validate: function(){
        this.save();
        this.triesCount++;
        var feedbackHTML = '';
        var answerCount = 0;
        var goodAnswerCount = 0;
        var goodAnswerContainerCount = 0;
        var wrongAnswerCount = 0;
        var theInputContainerID;
        var innerHTMLTxt;
        var choice;
        var blankAnswer = false;

        var choiceEvaluated = new Array();
        var validateInnerHTML = new Array();
        var strEmptyHTMLValidate = this.createFeedbackContainers();
        var counterDragged = parseInt(document.getElementById('counterDrag').value);

        for(i = 0;i < this.container.length;i++) {
              goodAnswerContainerCount = 0;
        
              indexValue = i + 1;
              var innerHTMLTxt = '';
              var idContent = 'inputContainerdrop' + indexValue;
              var containerContent = document.getElementById(idContent).value;
              var splitContainer = containerContent.split("|");
              validateInnerHTML[indexValue] = '';
              this.evaluateContainer[i] = null;

              for(j = 0;j < splitContainer.length;j++) {
                  if (splitContainer[j] != '') {
                      var splitDragInput = splitContainer[j].split("dragInput");
                      var splitidIndexes = trim(splitDragInput[1].substring(0,2));
                      splitidIndexes = splitidIndexes.replace("\"",'');
                      
                      var idDragInput = "dragInput" + splitidIndexes;
                      var idDragContentOnly = "dragContentOnly" + splitidIndexes;
                      var goodAnswer = document.getElementById(idDragInput).value;
                      var styleFeedback;

                      answerCount++;

                      if (goodAnswer == i + 1){
                          goodAnswerCount++;
                          goodAnswerContainerCount++;
                          bulletImage = 'bullet_green.png';
                          displayFeedback = true;
                          styleFeedback = "Good";

                          if (this.evaluateContainer[i] != false)
                              this.evaluateContainer[i] = true;
                      }
                      else{
                          wrongAnswerCount++;
                          displayFeedback = true;
                          styleFeedback = "Bad";
                          this.evaluateContainer[i] = false;
                      }

                      if (this.elementType == 0){
                          choice = document.getElementById(idDragContentOnly).value;
                          choice = choice.replace(/\\"/g, '"');
                          choice = choice.replace(/&#39;/g, "'"); 
                      
                          styleFeedback = "str" + styleFeedback;
                          validateInnerHTML[indexValue] = validateInnerHTML[indexValue] + "<span class=\"" + styleFeedback + "\">" + choice + "</span>" + "<br />";
                      }
                      else if (this.elementType == 1){
                          styleFeedback = "img" + styleFeedback;
                          validateInnerHTML[indexValue] = validateInnerHTML[indexValue] + "<img src=\"" + document.getElementById(idDragContentOnly).value + "\" class=\"" + styleFeedback + "\">" + "<br />";
                      }
                      
                      if (this.getRetroactionByContainerChoice(splitidIndexes ,indexValue) != '') {
                         validateInnerHTML[indexValue] += "<span class=\"retroContainerStyle3\">" + this.getRetroactionByContainerChoice(splitidIndexes, indexValue) + "</span>";
                      }
                      
                      validateInnerHTML[indexValue] += "<div class=\"emptyLineElements\"></div>";
                  }
              }
              
              
              var nbAnswerContainerRequired = 0;
              
              for(j = 0;j < this.numberGoodAnswersContainerNeeded.length;j++) {
                  if (this.numberGoodAnswersContainerNeeded[j] == indexValue) {
                      nbAnswerContainerRequired++;
                  }
              }
              
              if (this.evaluateContainer[i] == true) {
                  if (goodAnswerContainerCount < nbAnswerContainerRequired) {
                      this.evaluateContainer[i] = null;
                  }
              }
              
        }

        var goodAnswerCountRequired = this.choices.length;

        if(this.mustGiveAllGoodAnswers == false){
            var goodAnswerPonderation = this.ponderation / goodAnswerCountRequired;
            
            this.currentScore = (goodAnswerCount * goodAnswerPonderation);
            
            if (this.currentScore < 0){
                this.currentScore = 0;
            }
        }else{
            this.currentScore = (Math.min(Math.max((goodAnswerCount - wrongAnswerCount),0),goodAnswerCountRequired) / goodAnswerCountRequired) * this.ponderation;
        }

        this.status = this.quiz.statusToRedo;
        
        
        if(this.currentScore == this.ponderation){
            feedbackHTML = '<span class="Green">' + this.page.goodAnswerLabel + '</span><br /><br />' + feedbackHTML;
            this.status = this.quiz.statusCompleted;
            this.statusMenuPages = 1;
        }
        else if (wrongAnswerCount > 0) {
            feedbackHTML = '<span class="Red">' + this.page.wrongAnswerLabel + '</span><br /><br />' + feedbackHTML;
            this.statusMenuPages = 0;
        }
        else {
            feedbackHTML = '<span class="Yellow">' + this.page.incompleteAnswerLabel + '</span><br /><br />' + feedbackHTML;
            this.statusMenuPages = -1;
        }
        
        
        feedbackHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
        feedbackHTML += '<tr>';
        feedbackHTML += '<td>' + strEmptyHTMLValidate + '</td>';
        feedbackHTML += '</tr>';
        feedbackHTML += '</table><br />';

        setFeedback(feedbackHTML);

        for(i = 0;i < this.container.length;i++){
            indexValue = i + 1;
            var sMainContainer = 'Sdrop' + indexValue;

            $(sMainContainer).innerHTML = validateInnerHTML[indexValue];
        }

        var feedbackContainerIdA;
        var feedbackContainerIdB;
        var feedbackContainerIdC;
        var insertFeedback;

        for(i = 0;i < this.container.length;i++){
            var indexContainer = i + 1;
            var feedbackContainerId;

            feedbackContainerId = 'SretroContainer' + indexContainer;
               
            if (this.evaluateContainer[i] == false) {
                $(feedbackContainerId).innerHTML = this.wrongAnswers[indexContainer];
            }
            else if(this.evaluateContainer[i] == true) {
                $(feedbackContainerId).innerHTML = this.goodAnswers[indexContainer];
            }
            else {
                // this.evaluateContainer[i] est égal à null 
            
                if (this.numberGoodAnswersContainerNeeded[indexContainer] == undefined) {
                    $(feedbackContainerId).innerHTML = this.goodAnswers[indexContainer];
                }
                else {
                    $(feedbackContainerId).innerHTML = this.incompleteAnswers[indexContainer];
                }
            }
        }
      
        openFeedback();

        return this.currentScore;
    },

    showSolution: function(){
        var solutionHTML =  this.quiz.solutionLabel + '<br /><br />';
        var solInnerHTML = new Array();
        var choice;
        var whereToChoice;
        var theInputContainerID;
        var indexValue;

        var strEmptyHTMLSolution = this.createFeedbackContainers();

        for(i = 0;i < this.container.length;i++){
              indexValue = i + 1;
              var innerHTMLTxt = '';

              for(j = 0;j < this.choices.length;j++){
                  var indexValueJ = this.displayChoices[j] + 1;
                  var idDragInput = "dragInput" + indexValueJ;
                  var idDragContentOnly = "dragContentOnly" + indexValueJ;

                  choice = document.getElementById(idDragContentOnly).value;
                  choice = choice.replace(/\\"/g, '"');
                  choice = choice.replace(/&#39;/g, "'");
                  
                  whereToChoice = document.getElementById(idDragContentOnly).value;

                  if (document.getElementById(idDragInput).value == indexValue){
                      if (this.elementType == 0)
                          innerHTMLTxt += "<b>" + choice + "</b><br /><div class=\"emptyLineElements\"></div>";
                      else if (this.elementType == 1)
                          innerHTMLTxt += "<img src=\"" + document.getElementById(idDragContentOnly).value + "\" class=\"userImgContainer\">&nbsp;&nbsp;";
                  }
              }

              solInnerHTML[indexValue] = innerHTMLTxt;
        }

        solutionHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
        solutionHTML += '<tr>';
        solutionHTML += '<td>' + strEmptyHTMLSolution + '</td>';
        solutionHTML += '</tr>';
        solutionHTML += '</table><br />';

        setFeedback(solutionHTML);

        for(i = 0;i < this.container.length;i++){
            indexValue = i + 1;
            var sMainContainer = 'Sdrop' + indexValue;

            $(sMainContainer).innerHTML = solInnerHTML[indexValue];
        }

        openFeedback();
    },
    
    createFeedbackContainers: function(){
        var indexValue;
        var toReplace = '';
        var replaceBy = '';

        var strEmptyHTMLContainers = this.emptyHTMLContainers;

        for(i = 1;i <= this.emptyLineId - 1;i++){
            var emptyLineReplace = 'emptyLine' + i;

            toReplace = emptyLineReplace;
            replaceBy = 'S' + emptyLineReplace;

            strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);


            toReplace = 'class="emptyLine"';
            replaceBy = 'class="emptyLineRetro"';

            strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);
        }

        toReplace = 'elementTypeDrag';
        replaceBy = 'SelementTypeDrag';

        strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);


        toReplace = 'counterDrag';
        replaceBy = 'ScounterDrag';

        strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);

        for(i = 0;i < this.container.length;i++){
            indexValue = i + 1;

            toReplace = 'main' + indexValue;
            replaceBy = 'Smain' + indexValue;

            strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);

            toReplace = 'tag' + indexValue;
            replaceBy = 'Stag' + indexValue;

            strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);

            toReplace = 'divIntag' + indexValue;
            replaceBy = 'SdivIntag' + indexValue;

            strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);

            toReplace = 'drop' + indexValue;
            replaceBy = 'Sdrop' + indexValue;

            strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);

            toReplace = 'retroContainer' + indexValue;
            replaceBy = 'SretroContainer' + indexValue;

            strEmptyHTMLContainers = strEmptyHTMLContainers.replace(toReplace,replaceBy);
        }

        return strEmptyHTMLContainers;

    },
    
    redo: function(){
        this.userChoicesHTML = null;
        this.currentScore = 0;
        this.redoQuestion = true;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;

        closeFeedback();
        this.displayRedo();
    },

    redoQuiz: function(){
        this.userChoicesHTML = null;
        this.currentScore = 0;
        this.redoQuestion = true;
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
        return this.quiz.consigneClassement;
    }

});

