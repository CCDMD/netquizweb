var QuestionImagePart = Class.create({
    sQuestionType: 'ZONE A IDENTIFIER',

    NONE: 0,
    ALPHA: 1,
    NUMERIC: 2,

    TEXT: 0,
    IMAGE: 1,

    //settings
    labelType: 1,
    mustGiveAllGoodAnswers: false, //true = tous les éléments doivent êtres aux bons endroits pour avoir les points

    //User settings
    iChoiceWidth: 100, //200
    iChoiceMaxHeight: 150,  //250

    otherAnswersFeedback: null,
    currentSequence: null,
    currentChoices: null,
    currentTopChoice: null,
    currentScore: 0,
    ponderation: 0,
    triesCount: 0,
    status:'',
    statusMenuPages: -1,
    selectCount: 0,
    srcMainImage: null,
    srcMediaCategory: 1,
    theDropZoneBorder: 'FFFFFF',
    choiceSize: 19,

    oPreload: new Image(),
    oImagePart: getNewImagePart(),

    oPreloadSolution: new Image(),
    oImagePartSolution: getNewImagePart(),
    
    initialize: function(quiz, page, questionNb){
        this.quiz = quiz;
        this.page = page;
        this.questionNb = questionNb;

        this.choices = new Array();
        this.choicesType = new Array();
        this.displayChoices = new Array();
        this.goodFeedback = new Array();
        this.goodFeedbackDisplay = new Array();
        this.badFeedback = new Array();
        this.badFeedbackDisplay = new Array();
        this.goodAnswers = new Array();
        this.goodPosition = new Array();
        this.goodPositionDisplay = new Array();
        this.nowPositionDisplay = new Array();
        this.yxChoices = new Array();
        this.arrSrcMediaCategory = new Array();

        this.status = this.quiz.statusToDo;

        this.userAnswers = '';
    },

    addImage: function(mainImage, mediaCategory){
        this.srcMainImage = mainImage; 
    
        if (mediaCategory == 1) {
            this.srcMainImage = this.quiz.mediasFolder + '/' + this.srcMainImage;
        }
        
        this.srcMediaCategory = mediaCategory;
        this.quiz.imgPreloader.addImage(this.srcMainImage);
    },

    addImagePart: function(y, x){
        this.yxChoices[this.yxChoices.length] = y + "," + x;
    },

    addImagePartSolution: function(y, x){
        this.oImagePartSolution.addChoice(y, x);
    },

    addChoice: function(choice, iChoiceType, goodFeedback, badFeedback, goodPosition, mediaCategory){
        var theLength = this.choices.length;
        var strToEvaluate;

        if(iChoiceType == this.IMAGE) {
            this.quiz.imgPreloader.addImage(choice);
        }

        this.choices[this.choices.length] = choice;
        this.choicesType[theLength] = iChoiceType;
        this.goodFeedback[this.choices.length] = goodFeedback;
        this.badFeedback[this.choices.length] = badFeedback;
        this.goodPosition[this.choices.length] = goodPosition;
        this.goodAnswers[this.goodAnswers.length] = this.choices.length - 1;
        this.displayChoices[theLength] = this.choices.length - 1;

        this.goodPositionDisplay[theLength + 1] = this.goodPosition[this.displayChoices[theLength] + 1];
        this.goodFeedbackDisplay[theLength + 1] = this.goodFeedback[this.displayChoices[theLength] + 1];
        this.badFeedbackDisplay[theLength + 1] = this.badFeedback[this.displayChoices[theLength] + 1];
        
        this.arrSrcMediaCategory[this.arrSrcMediaCategory.length] = mediaCategory;
        
        
        if(iChoiceType == this.TEXT) {
            strToEvaluate = this.choices[this.choices.length - 1];
            this.choices[this.choices.length - 1] = evalStringForLexique(strToEvaluate);
        }

        strToEvaluate = this.goodFeedback[this.choices.length];
        this.goodFeedback[this.choices.length] = evalStringForLexique(strToEvaluate);

        strToEvaluate =  this.badFeedback[this.choices.length ];
        this.badFeedback[this.choices.length] = evalStringForLexique(strToEvaluate);
    },
    
    shuffle: function(){
        this.shuffleChoices(this.displayChoices);

        for(i = 1;i <= this.choices.length;i++){
            this.goodPositionDisplay[i] = this.goodPosition[this.displayChoices[i - 1] + 1];
            this.goodFeedbackDisplay[i] = this.goodFeedback[this.displayChoices[i - 1] + 1];
            this.badFeedbackDisplay[i] = this.badFeedback[this.displayChoices[i - 1] + 1];
        }
    },
    
    display: function(){
        var divMainImage = null;
        var divStrChoices = null;
        var topDivStrChoices = null;
        var inputUser = null;
        var dragLabelDrop;
        var strDisplayChoices = "";
        var mediaMediaFolder;

        this.oImagePart.oChoices = new Array();
        this.oImagePart.oDropZones = new Array();
        
        this.oImagePart.sImage = this.srcMainImage;

        if (this.srcMediaCategory == 1){
            mediaMediaFolder = this.quiz.mediasFolder;
        }
        else if(this.srcImageCategory == 2){
            mediaMediaFolder = '';
        }
        
        this.oImagePart.iChoiceSize = this.choiceSize - 2; //a cause du border

        var varDropZoneBorder = 'solid 1px #' + this.theDropZoneBorder;
        this.oImagePart.sDropZoneBorder = varDropZoneBorder;

        for(i = 0;i < this.yxChoices.length;i++){
            var stryxChoices = this.yxChoices[i].split(",");
            var yChoice = stryxChoices[0];
            var xChoice = stryxChoices[1];

            this.oImagePart.addChoice(yChoice, xChoice);
        }

        var divLastChoice = this.choices.length - 1;
        divLastChoice = 'drag_' + divLastChoice;
        
        divMainImage = document.createElement('div');
        divMainImage.id = 'idMainImage';
        divMainImage.style.position = 'relative';

        divStrChoices = document.createElement('div');
        divStrChoices.id = 'idStrChoices';

        $('question').appendChild(divMainImage);
        this.oImagePart.iLabelType = this.labelType;

        this.oImagePart.display(this.fo('idMainImage'));

        $('idMainImage').appendChild(divStrChoices);
        $('idStrChoices').style.position = 'absolute';


        var lastChoiceTop = $(divLastChoice).style.top;
        lastChoiceTop = lastChoiceTop.replace('px','');

        var lastChoiceHeight = $(divLastChoice).style.height;
        lastChoiceHeight = lastChoiceHeight.replace('px','');


        var oTable = null;
        var iColIndex = null;
        var oCurrRow = null;
        var oCurrCell = null;

        var iRowHeight = 22;
        var iTextCellPaddingLeft = 10;
        var iChoiceRowPaddingTop = 10;

        oTable = document.createElement('table');
        oTable.width = '100%';
        oTable.cellPadding = '0';
        oTable.cellSpacing = '0';

        for(i = 0;i < this.choices.length;i++){
            dragLabelDrop = this.oImagePart.innerHTMLTag[i];
            var inputIndexDrop = "<input type=\"hidden\" id=\"idDrop" + i + "\">"
            var charsLabel = ')';

            iColIndex = 0;
            oCurrRow = oTable.insertRow(i);
            oCurrCell = oCurrRow.insertCell(iColIndex);

            oCurrCell.height = iRowHeight;
            oCurrCell.style.paddingTop = iChoiceRowPaddingTop + 'px';
            oCurrCell.align = 'left';

            oCurrCell.innerHTML = dragLabelDrop + charsLabel;
            iColIndex++;

            oCurrCell = oCurrRow.insertCell(iColIndex);


            strDisplayChoices = "" ;
            oCurrCell.innerHTML = strDisplayChoices;


            oCurrCell.height = iRowHeight;
            oCurrCell.style.paddingTop = iChoiceRowPaddingTop + 'px';
            oCurrCell.align = 'left';
            oCurrCell.style.paddingBottom = '3px';
            oCurrCell.style.paddingLeft = '13px';

            iColIndex++;

            oCurrCell = oCurrRow.insertCell(iColIndex);

            oCurrCell.width = '100%';
            oCurrCell.height = iRowHeight;
            oCurrCell.style.paddingTop = iChoiceRowPaddingTop + 'px';
            oCurrCell.align = 'left';


            if(this.choicesType[this.displayChoices[i]] == this.TEXT){
                strDisplayChoices = this.choices[this.displayChoices[i]] + inputIndexDrop;
            }else{
                var mediaMediaFolder;

                if (this.arrSrcMediaCategory[this.displayChoices[i]] == 1){
                    mediaMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategory[this.displayChoices[i]] == 2){
                    mediaMediaFolder = '';
                }
            
                var temp = new Element('div');
                temp.appendChild(nq4_buildImageObject(this.choices[this.displayChoices[i]],this.iChoiceWidth,this.iChoiceMaxHeight,mediaMediaFolder));
                strDisplayChoices = temp.innerHTML + inputIndexDrop;
            }

            oCurrCell.innerHTML = strDisplayChoices;
        }

        inputUser = document.createElement('input');
        inputUser.id = 'userAnswers';
        inputUser.type = 'hidden';

        if (this.currentSequence != null && this.currentSequence != ""){
            topDivStrChoices = this.currentTopChoice;
        }
        else{
            topDivStrChoices = parseInt(lastChoiceTop) + parseInt(lastChoiceHeight) + 20;
            topDivStrChoices = topDivStrChoices + 'px';
            this.currentTopChoice = topDivStrChoices;
        }


        $('idStrChoices').style.top = topDivStrChoices;
        $('idStrChoices').appendChild(oTable);
        $('idStrChoices').appendChild(inputUser);
        

        if (this.currentSequence != null && this.currentSequence != ""){
            document.getElementById('userAnswers').value = this.currentSequence;

            var splitUserAnswer = this.currentSequence.split("x");

            for(var i = 0;i < this.choices.length;i++){
                var posSplit;
                var ySplit;
                var xSplit;
                var idTxtDropIndex = "idDrop" + i;
                dragLabelDrop = 'drag_' + i;

                if (splitUserAnswer[i] == "-1")
                    document.getElementById(idTxtDropIndex).value = "";
                else
                    document.getElementById(idTxtDropIndex).value = splitUserAnswer[i];


                posSplit = this.nowPositionDisplay[i].split(",");
                ySplit = posSplit[0];
                xSplit = posSplit[1];

                $(dragLabelDrop).style.top = ySplit;
                $(dragLabelDrop).style.left = xSplit;
            }
       }
       
       //AJOUT BOB, TEST MOBILE
       var topOfChoices = parseInt($('idStrChoices').style.top, 10); 
       var newQuizHeight = (topOfChoices + $('idStrChoices').getHeight() + 120); //120 = Partie du haut: Description question, indice, nombre de points, margin-top, padding-top, et on rajoute 20 pour que le dernier élément ai un peu d'espace en-dessous
       
       if ($('scrollwrapper').getHeight() < newQuizHeight){
           $('quizpage').style.height = newQuizHeight + 'px';
       }
    },

    fo: function(theObj, theDoc)
    {
        var p, i, foundObj;

        if(!theDoc) theDoc = document;
        if( (p = theObj.indexOf('?')) > 0 && parent.frames.length)
        {
            theDoc = parent.frames[theObj.substring(p+1)].document;
            theObj = theObj.substring(0,p);
        }
        if(!(foundObj = theDoc[theObj]) && theDoc.all) foundObj = theDoc.all[theObj];
                
        for (i=0; !foundObj && i < theDoc.forms.length; i++)
            foundObj = theDoc.forms[i][theObj];
                
        for(i=0; !foundObj && theDoc.layers && i < theDoc.layers.length; i++)
            foundObj = fo(theObj,theDoc.layers[i].document);
                
        if(!foundObj && document.getElementById) foundObj = document.getElementById(theObj);

        return foundObj;
    },

    save: function(){
        this.currentChoices = new Array();
        this.nowPositionDisplay = new Array();
        this.currentSequence = document.getElementById('userAnswers').value;

        for(var i = 0;i < this.choices.length;i++){
            var dragLabelDrop = 'drag_' + i;
            this.nowPositionDisplay[i] = $(dragLabelDrop).style.top + "," + $(dragLabelDrop).style.left;
        }
    },

    validate: function(){
        this.save();
        this.triesCount++;
        var feedbackHTML = '';
        var answerCount = 0;
        var goodAnswerCount = 0;
        var wrongAnswerCount = 0;
        var splitUserAnswer;
        var blankAnswer = false;

        var disBulletImage = new Array();
        var disChoice = new Array();
        var disChoiceType = new Array();
        var disFeedback = new Array();
        var counterLabel = 0;
        
        
        /* On doit refaire ce calcul car si sous firefox et ie et l'image est très grosse, problème d'affichage */
        var mainImgHeight;
                       
        if (jQuery("#idMainImage img").attr("datamh") == "9999") {
           mainImgHeight = parseInt(jQuery("#idMainImage img").attr("height"));
                           
           if (mainImgHeight == 0) {
              // Problème... Alors on y va par élémination
              mainImgHeight = jQuery(document).height() - jQuery("#header").height() - jQuery("#headersep").height() - jQuery("#saq").height() - jQuery("#idStrChoices").height() - 120;
           }
        }
        else {
             mainImgHeight = parseInt(jQuery("#idMainImage img").attr("datamh"));
        }
        
        var newQuizHeight = jQuery("#statement").height() + mainImgHeight + jQuery("#saq").height() + jQuery("#idStrChoices").height() + 120 + 20; //120 = Partie du haut: Description question, indice, nombre de points, margin-top, padding-top, et on rajoute 20 pour que le dernier élément ai un peu d'espace en-dessous
                       
        $('quizpage').style.height = newQuizHeight + 'px';
        /*************/
        
        
           this.userAnswers = document.getElementById('userAnswers').value;
           splitUserAnswer = this.userAnswers.split("x");

           if (this.userAnswers != ''){
              for(var i = 0;i < this.choices.length;i++){
                  var bulletImage = 'bullet_red.png';
                  var choice;
                  var choiceType;
                  var feedback;
                  var displayFeedback = false;
                  var whereToChoice;

                  for(var j = 0;j < this.choices.length;j++){
                       if (i == this.goodPositionDisplay[j + 1] - 1){
                          whereToChoice = j;
                      }
                  }

                  if (splitUserAnswer[i] == whereToChoice){
                     answerCount++;
                     goodAnswerCount++;
                     bulletImage = 'bullet_green.png';
                     displayFeedback = true;
                     feedback = this.goodFeedbackDisplay[parseInt(splitUserAnswer[i]) + 1];
                     choice = this.choices[this.displayChoices[splitUserAnswer[i]]];
                     choiceType = this.choicesType[this.displayChoices[splitUserAnswer[i]]];
                  }
                  else if (splitUserAnswer[i] != -1){
                     answerCount++;
                     wrongAnswerCount++;
                     displayFeedback = true;
                     feedback = this.badFeedbackDisplay[parseInt(splitUserAnswer[i]) + 1];
                     choice = this.choices[this.displayChoices[splitUserAnswer[i]]];
                     choiceType = this.choicesType[this.displayChoices[splitUserAnswer[i]]];
                  }
                  else{
                     choice = "";
                     blankAnswer = true;
                     // AVANT MODIF UNIFORMISATION 2014-01-23
                     // wrongAnswerCount++;
                  }

                  if (displayFeedback == true){
                     for(var x = 0;x < this.choices.length;x++){
                         if (this.displayChoices[splitUserAnswer[i]] == this.displayChoices[x]){
                             disBulletImage[x] = bulletImage;
                             disChoice[x] = choice;
                             disChoiceType[x] = choiceType;
                             disFeedback[x] = feedback;
                         }
                     }
                  }
              }
              
              
              var goodAnswerCountRequired = this.goodAnswers.length;

              if(this.mustGiveAllGoodAnswers == false){
                  var goodAnswerPonderation = this.ponderation / goodAnswerCountRequired;
                  
                  this.currentScore = (goodAnswerCount * goodAnswerPonderation);
                  
                  if (this.currentScore < 0){
                      this.currentScore = 0;
                  }
              }else{
                  this.currentScore = (Math.min(Math.max((goodAnswerCount - wrongAnswerCount),0),goodAnswerCountRequired) / goodAnswerCountRequired) * this.ponderation;
              }

              // AVANT MODIF UNIFORMISATION 2014-01-23
              /*
              if(blankAnswer == false){
                  var goodAnswerCountRequired = this.goodAnswers.length;

                  if(this.mustGiveAllGoodAnswers == false){
                      var wrongAnswerCountPossible = this.choices.length;
                      
                      var goodAnswerPonderation = ((goodAnswerCount > 0) ? this.ponderation : 0);
                      var wrongAnswerPonderation = this.ponderation / wrongAnswerCountPossible;
                          
                      this.currentScore = Math.max((goodAnswerPonderation - (wrongAnswerPonderation * wrongAnswerCount)),0);

                  }else{
                      this.currentScore = (Math.min(Math.max((goodAnswerCount - wrongAnswerCount),0),goodAnswerCountRequired) / goodAnswerCountRequired) * this.ponderation;
                  }
               }
               */
           }
           
           
           // AVANT MODIF UNIFORMISATION 2014-01-23
           //if(blankAnswer == false){
               for(var i = 0;i < this.choices.length;i++){
                   if (disBulletImage[i] != null){

                          counterLabel = counterLabel + 1;

                          var label = '';

                          if(this.labelType == this.ALPHA){
                              label = getLetterLabel(i + 1).toUpperCase() + ')';
                          }else if(this.labelType == this.NUMERIC){
                              label = (parseInt(i + 1)) + ')';
                          }


                          feedbackHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
                          feedbackHTML += '<tr>';
                          feedbackHTML += '<td width="20"><img src="images/' + disBulletImage[i] + '" /></td>';
                          feedbackHTML += '<td width="25">' + label + '</td>';


                          if(disChoiceType[i] == this.IMAGE){
                              var mediaMediaFolder;

                              if (this.arrSrcMediaCategory[i] == 1){
                                  mediaMediaFolder = this.quiz.mediasFolder;
                              }
                              else if(this.arrSrcMediaCategory[i] == 2){
                                  mediaMediaFolder = '';
                              }
                              
                          
                              var temp = new Element('div');
                              temp.appendChild(nq4_buildImageObject(disChoice[i],this.iChoiceWidth,this.iChoiceMaxHeight,mediaMediaFolder));
                              feedbackHTML += '<td>' + temp.innerHTML + '</td></tr>';

                              if (disFeedback[i] != ''){
                                  feedbackHTML += '<tr><td width="20">&nbsp;</td><td width="25">&nbsp;</td><td class="feedbackLineImg"><span class="small">' + disFeedback[i] + '</span></td></tr>';
                              }
                          }
                          else{
                              feedbackHTML += '<td>' + disChoice[i] + '</td></tr>';

                              if (disFeedback[i] != ''){
                                  feedbackHTML += '<tr><td width="20">&nbsp;</td><td width="25">&nbsp;</td><td class="feedbackLineTxt"><span class="small">' + disFeedback[i] + '</span></td></tr>';
                              }
                          }

                          feedbackHTML += '<tr><td width="20">&nbsp;</td><td class="feedbackLineSpacer">&nbsp;</td></tr>';
                          feedbackHTML += '</table>';
                   }
               }
           //}


        this.status = this.quiz.statusToRedo;
        
        if(this.currentScore == this.ponderation){
            feedbackHTML = '<span class="Green">' + this.page.goodAnswerLabel + '</span><br /><br />' + feedbackHTML;
            this.status = this.quiz.statusCompleted;
            this.statusMenuPages = 1;
        }
        else if(wrongAnswerCount > 0){
             feedbackHTML = '<span class="Red">' + this.page.wrongAnswerLabel + '</span><br /><br />' + feedbackHTML;
             this.statusMenuPages = 0;
        }
        else {
             feedbackHTML = '<span class="Yellow">' + this.page.incompleteAnswerLabel + '</span><br /><br />' + feedbackHTML;
             this.statusMenuPages = -1;
        }
        

        // AVANT MODIF UNIFORMISATION 2014-01-23
        /*
        if(blankAnswer == true){
            feedbackHTML = '<span class="Yellow">' + this.page.incompleteAnswerLabel + '</span><br /><br />' + feedbackHTML;
        }else if(this.currentScore == this.ponderation){
            feedbackHTML = '<span class="Green">' + this.page.goodAnswerLabel + '</span><br /><br />' + feedbackHTML;
            this.status = this.quiz.statusCompleted;
        }else{
            if(wrongAnswerCount > 0)
                feedbackHTML = '<span class="Red">' + this.page.wrongAnswerLabel + '</span><br /><br />' + feedbackHTML;
            else
                feedbackHTML = '<span class="Yellow">' + this.page.incompleteAnswerLabel + '</span><br /><br />' + feedbackHTML;
        }
        */

        setFeedback(feedbackHTML);
        openFeedback();
        
        return this.currentScore;
    },
    
    showSolution: function(){
        var solutionHTML =  this.quiz.solutionLabel + '<br /><br />';
        var divSolutionImage = null;
        
        top.ccdmd.nq4.recalculerDimensionsImagepart = true;

        this.oImagePartSolution.oChoices = new Array();
        this.oImagePartSolution.oDropZones = new Array();
        this.oImagePartSolution.iChoiceSize = this.choiceSize - 1;

        solutionHTML += '<div id="divSolution"></div>';
        setFeedback(solutionHTML);

        divSolutionImage = document.createElement('div');
        divSolutionImage.id = 'idSolutionImage';
        divSolutionImage.style.position = 'relative';

        $('divSolution').appendChild(divSolutionImage);
        this.oPreloadSolution.src = this.oPreload.src;
        this.oImagePartSolution.sImage = this.oImagePart.sImage;

        this.oImagePartSolution.sChoiceIDPrefix = 'solution';
        this.oImagePartSolution.sDropZoneIDPrefix = 'dropzoneSolution';

        for(var i = 0;i < this.choices.length;i++){
            var ySol;
            var xSol;

            var ySplit;
            var xSplit;

            ySol = this.oImagePart.oDropZones[i].oDiv.style.top;
            xSol = this.oImagePart.oDropZones[i].oDiv.style.left;

            ySplit = ySol.split("px");
            xSplit = xSol.split("px");

            this.addImagePartSolution(ySplit[0], xSplit[0]);
        }

        this.oImagePartSolution.iLabelType = this.labelType;
        this.oImagePartSolution.display(this.fo('idSolutionImage'));


        var theChoiceSize = this.choiceSize - 1;

        for(var i = 0;i < this.choices.length;i++){
            var indexPosSolution = this.goodPositionDisplay[i + 1] - 1;
            var solutionId = "solution_" + i;

            if (this.choiceSize > 15){
                var construtTable = "<table width=\"" + theChoiceSize + "\" height=\"" + theChoiceSize + "\" align=\"left\" valign=\"top\" border=\"0\">";
                construtTable+= "<tr><td align=\"center\" valign=\"middle\" class=\"divDragChoiceCls\">" + this.oImagePart.innerHTMLTag[i] + "</td></tr>";
                construtTable+= "</table>";
                document.getElementById(solutionId).innerHTML = construtTable;
            }

            $(solutionId).style.left = this.oImagePart.oDropZones[indexPosSolution].oDiv.style.left;
            $(solutionId).style.top = this.oImagePart.oDropZones[indexPosSolution].oDiv.style.top;
        }

        
        /* On doit refaire ce calcul car si sous firefox et ie et l'image est très grosse, problème d'affichage */
        var mainImgHeight;
                       
        if (jQuery("#idMainImage img").attr("datamh") == "9999") {
           mainImgHeight = parseInt(jQuery("#idMainImage img").attr("height"));
                           
           if (mainImgHeight == 0) {
              // Problème... Alors on y va par élémination
              mainImgHeight = jQuery(document).height() - jQuery("#header").height() - jQuery("#headersep").height() - jQuery("#saq").height() - jQuery("#idStrChoices").height() - 120;
           }
        }
        else {
             mainImgHeight = parseInt(jQuery("#idMainImage img").attr("datamh"));
        }
        
        var newQuizHeight = jQuery("#statement").height() + mainImgHeight + jQuery("#saq").height() + jQuery("#idStrChoices").height() + 120 + 20; //120 = Partie du haut: Description question, indice, nombre de points, margin-top, padding-top, et on rajoute 20 pour que le dernier élément ai un peu d'espace en-dessous
                       
        $('quizpage').style.height = newQuizHeight + 'px';
        /*************/


        openFeedback();
    },
    
    redo: function(){
       this.currentSequence = null;
       this.currentChoices = null;
       this.currentScore = 0;
       this.status = this.quiz.statusToRedo;
       this.statusMenuPages = -1;

       document.getElementById('userAnswers').value = "";
       
       for(var i = 0;i < this.choices.length;i++){
           var dragId = "drag_" + i;
           var idTxtDropIndex = "idDrop" + i;
           
           $(dragId).style.left = this.oImagePart.oChoices[i].iOriginLeft;
           $(dragId).style.top = this.oImagePart.oChoices[i].iOriginTop;

           this.oImagePart.oChoices[i].oCurrDropZone = null;

           document.getElementById(idTxtDropIndex).value = "";
        }

        closeFeedback();

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
        return this.quiz.consigneImagePart;
    }
});