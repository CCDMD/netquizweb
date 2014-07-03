var QuestionRanking = Class.create({
    sQuestionType: 'RANKING',
    
    TEXT: 0,
    IMAGE: 1,

    NONE: 0,
    LETTERS: 1,
    NUMBERS: 2,
    
    //System settings
    sListIdPrefix: 'ranking',
    sIdDelimiter: '_',
    sLabelSuffix: ')&nbsp;&nbsp;',
    iListHeightAdjust: -17, //17,//10
    iListItemPadding: 10, //10,//5
    
    
    //User settings
    iChoiceWidth: 110,
    iChoiceMaxHeight: 145,
    labelType: 0,
    
    //Variables
    questionNb: 0,
    oList: null,
    someSortableId: '',
    widthSortableFeedback: 0,
    tableWidthSortableFeedback: 0,
    tableHeightSortableFeedback: 0,
    
    currentScore: 0,
    ponderation: 0,
    triesCount: 0,
    status:'',
    statusMenuPages: -1,
    
    reBalanced: false,
    nbImages: 0,
    
    //Mandatory functions
    initialize: function(quiz, page, questionNb){
        this.quiz = quiz;
        this.page = page;
        this.questionNb = questionNb;

        this.sChoices = new Array();
        this.iChoicesPos = new Array();
        this.choicesInitPos = new Array();
        this.sGoodAnswerFeedback = new Array();
        this.sWrongAnswerFeedback = new Array();
        this.arrSrcMediaCategory = new Array();
        
        this.status = this.quiz.statusToDo;
    },
    
    display: function(){
        //Lists
        this.reBalanced = false;
        
        var oListsWrapper = nq4_buildHTMLElement('table',{cellpadding:'0',cellspacing:'0',border:'0'});
        var oCurrRow = oListsWrapper.insertRow(0);
        var oLabelsWrapper = oCurrRow.insertCell(0);
        var oChoicesWrapper = oCurrRow.insertCell(1);
        
        oLabelsWrapper.vAlign = "top";

        this.oList = this.buildRankingSortable();
        this.oList.id = this.sListIdPrefix + this.sIdDelimiter + this.questionNb
        oChoicesWrapper.appendChild(this.oList);
        
        if(this.labelType != this.NONE){
            var oLabelsList = this.buildRankingLabels();
            oLabelsList.id = this.sListIdPrefix  + this.sIdDelimiter + 'labels' + this.sIdDelimiter + this.questionNb
            oLabelsWrapper.appendChild(oLabelsList);
        }
        $('question').update(oListsWrapper);

        Position.includeScrollOffsets = true;
        Sortable.create(this.oList.id, {scroll:window});
        
        if(this.labelType != this.NONE){
            this.balanceLists(this.oList,oLabelsList);
        }
    },
    
    save: function(){
        this.sSaveValue = '';
        var iIndex = null;
        var sId = null;
        var iPos = 0;
        
        for(var i = 0;i < this.oList.childNodes.length;i++){
            if(this.oList.childNodes[i].tagName == 'LI'){
                sId = this.oList.childNodes[i].id;
                iIndex = sId.substring(sId.lastIndexOf(this.sIdDelimiter) + 1);
                this.iChoicesPos[iIndex] = iPos;
                
                iPos++;
            }
        }
    },
    validate: function(){
        this.save();
        this.triesCount++;
        var feedbackHTML = '';
        var goodAnswerCount = 0;
        var wrongAnswerCount = 0;
        var heightToPut;
        var widthToPut;
        
        var label = '';
        var choice = '';
        var bulletImage = '';
        var feedback = '';
        
        var maxW = this.widthSortableFeedback + 2;
        
        for(var i = 0;i < this.sChoices.length; i++){
            var theCols = '<td width="20">&nbsp;</td>';

            switch(this.labelType){
                case this.LETTERS:
                    label = getLetterLabel(i + 1).toUpperCase() + this.sLabelSuffix;
                    break;
                case this.NUMBERS:
                    label = (i + 1) + this.sLabelSuffix;
                    break;
            }
            
            currentChoiceId = this.iChoicesPos.indexOf(i);
            choice = this.sChoices[currentChoiceId];
            
            if(this.iChoicesPos[i] == i){
                goodAnswerCount++;
                bulletImage = 'bullet_green.png';
                feedback = this.sGoodAnswerFeedback[currentChoiceId];
            }else{
                wrongAnswerCount++;
                bulletImage = 'bullet_red.png';
                feedback = this.sWrongAnswerFeedback[currentChoiceId]
            }
            

            heightToPut = document.getElementById(this.someSortableId).style.height;

            feedbackHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
            feedbackHTML += '<tr>';
            feedbackHTML += '<td width="20" style="padding-top: 8px;"><img src="images/' + bulletImage + '" /></td>';
            if (this.labelType > 0){
                feedbackHTML += '<td width="25"><ul style="list-style-type: none; margin-top: -3px; padding: 0pt; margin-left: 0pt; margin-bottom: 0px;"><li style="margin-bottom: 0px; padding-top: 10px; padding-bottom: 10px; padding-right: 6px; height:' + heightToPut + ';">' + label + '</li></ul></td>';
                theCols = theCols + '<td width="25">&nbsp;</td>';
            }

            if(choice[1] == this.TEXT){
                feedbackHTML += '<td height="' + heightToPut + '"><div style="display: table-row"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-3px; margin-bottom: 0px;"><li style="border:1px solid #CBC8C8; padding:10px; min-width:' + this.widthSortableFeedback + '; max-width:' + maxW + '; height:' + heightToPut + ';">' + choice[0] + '</li></ul></div></td>';
                feedbackHTML += '<tr>' + theCols + '<td class="feedbackLineTxt"><span class="small">' + feedback + '</span></td>';

            }else{
                var imgMediaFolder;
            
                if (this.arrSrcMediaCategory[this.iChoicesPos.indexOf(i)] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategory[this.iChoicesPos.indexOf(i)] == 2){
                    imgMediaFolder = '';
                }
                
            
                var temp = new Element('div');
                temp.appendChild(nq4_buildImageObject(choice[0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
                heightToPut = "100%";

                feedbackHTML  += '<td height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-3px; margin-bottom: 0px;"><li style="border:1px solid #CBC8C8; padding:10px; position:relative; width:' + this.widthSortableFeedback + '; height:' + heightToPut + ';"><table class="feedbackTable2" cellspacing="0" cellpadding="0" border="0"><tr><td width="' + this.tableWidthSortableFeedback + '" height="' + this.tableHeightSortableFeedback + '" align="center" valign="middle">' + temp.innerHTML + '</td></tr></table></li></ul></td>';
                feedbackHTML += '<tr>' + theCols + '<td class="feedbackLineImg"><span class="small">' + feedback + '</span></td>';
            }
            
            feedbackHTML += '</tr>';

            if (feedback != ''){
                feedbackHTML += '<tr>' + theCols + '<td class="feedbackLineSpacer">&nbsp;</td></tr>';
            }
            feedbackHTML += '</table>';
        }
        
        this.currentScore = goodAnswerCount / this.sChoices.length * this.ponderation;
        
        this.status = this.quiz.statusToRedo;
        if(this.currentScore == this.ponderation){
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
        var label = '';
        var solutionHTML =  this.quiz.solutionLabel + '<br /><br />';
        var heightToPut;
        
        var maxW = this.widthSortableFeedback + 2;
        
        for(var i = 0;i < this.sChoices.length;i++){
            switch(this.labelType){
                case this.LETTERS:
                    label = getLetterLabel(i + 1).toUpperCase() + this.sLabelSuffix;
                    break;
                case this.NUMBERS:
                    label = (i + 1) + this.sLabelSuffix;
                    break;
            }
            
            choice = this.sChoices[i];

            heightToPut = document.getElementById(this.someSortableId).style.height;

            solutionHTML += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
            solutionHTML += '<tr>';
            if (this.labelType > 0)
                solutionHTML += '<td width="25"><ul style="list-style-type: none; margin-top: -3px; padding: 0pt; margin-left: 0pt;"><li style="margin-bottom: 20px; padding-top: 10px; padding-bottom: 10px; padding-right: 6px; height:' + heightToPut + ';">' + label + '</li></ul></td>';
            
            if(choice[1] == this.TEXT){
                solutionHTML += '<td height="' + heightToPut + '"><div style="display: table-row"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-3px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:10px; min-width:' + this.widthSortableFeedback + '; max-width:' + maxW + '; height:' + heightToPut + ';">' + choice[0] + '</li></ul></div></td>';
            }else{
                var imgMediaFolder;
            
                if (this.arrSrcMediaCategory[i] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategory[i] == 2){
                    imgMediaFolder = '';
                }
            
            
                var temp = new Element('div');
                temp.appendChild(nq4_buildImageObject(choice[0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
                solutionHTML += '<td height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-3px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:10px; position:relative; width:' + this.widthSortableFeedback + '; height:' + heightToPut + ';"><table class="feedbackTable2" cellspacing="0" cellpadding="0" border="0"><tr><td width="' + this.tableWidthSortableFeedback + '" height="' + this.tableHeightSortableFeedback + '" align="center" valign="middle">' + temp.innerHTML + '</td></tr></table></li></ul></td>';
            }
            
            solutionHTML += '</tr>';
            solutionHTML += '</table>';
        }
        setFeedback(solutionHTML);
        openFeedback();
    },
    redo:function(whereFrom){
        this.iChoicesPos = this.choicesInitPos.clone();
        this.currentScore = 0;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;

        closeFeedback();
        this.display();
    },
    redoQuiz: function(){
        this.iChoicesPos = this.choicesInitPos.clone();
        this.currentScore = 0;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;
    },
    isAnswered: function() {
        return true;
    },
    
    setLabelType: function(i){
        this.labelType = i;
    },
    //Question specific functions
    addChoice: function(sChoice,iChoiceType,sGoodAnswerFeedback,sWrongAnswerFeedback,position,mediaCategory){
        var strToEvaluate;

        if (position == 0){
            this.sChoices[this.sChoices.length] = [sChoice, iChoiceType];
            if(iChoiceType == this.IMAGE){
                this.nbImages++;
           
                if (mediaCategory == 1){
                    sChoice = this.quiz.mediasFolder + '/' + sChoice;
                }
                this.quiz.imgPreloader.addImage(sChoice);
            }
            this.iChoicesPos[this.iChoicesPos.length] = this.iChoicesPos.length;
            this.choicesInitPos[this.choicesInitPos.length] = this.choicesInitPos.length;
            this.sGoodAnswerFeedback[this.sGoodAnswerFeedback.length] = sGoodAnswerFeedback;
            this.sWrongAnswerFeedback[this.sWrongAnswerFeedback.length] = sWrongAnswerFeedback;
            this.arrSrcMediaCategory[this.arrSrcMediaCategory.length] = mediaCategory;
        }
        else{
            position = position - 1;

            this.sChoices[this.sChoices.length] = [sChoice, iChoiceType];
            if(iChoiceType == this.IMAGE){
                this.nbImages++;
            
                if (mediaCategory == 1){
                    sChoice = this.quiz.mediasFolder + '/' + sChoice;
                }
                this.quiz.imgPreloader.addImage(sChoice);
            }
            this.iChoicesPos[this.iChoicesPos.length] = position;
            this.choicesInitPos[this.choicesInitPos.length] = position;
            this.sGoodAnswerFeedback[this.sGoodAnswerFeedback.length] = sGoodAnswerFeedback;
            this.sWrongAnswerFeedback[this.sWrongAnswerFeedback.length] = sWrongAnswerFeedback;
            this.arrSrcMediaCategory[this.arrSrcMediaCategory.length] = mediaCategory;
        }
        
        
        strToEvaluate = this.sGoodAnswerFeedback[this.sGoodAnswerFeedback.length - 1];
        this.sGoodAnswerFeedback[this.sGoodAnswerFeedback.length - 1] = evalStringForLexique(strToEvaluate);

        strToEvaluate = this.sWrongAnswerFeedback[this.sWrongAnswerFeedback.length - 1];
        this.sWrongAnswerFeedback[this.sWrongAnswerFeedback.length - 1] = evalStringForLexique(strToEvaluate);
    },
    setChoicePos: function(iID,iPos){
        this.iChoicesPos[iID] = iPos;
    },
    buildRankingSortable: function(){
        var oListItem = null;
        var iCurrIndex = null;
        var oList = nq4_buildHTMLElement('ul',{});
        oList.style.listStyleType = 'none';
        oList.style.padding = '0';
        oList.style.marginLeft = '0';
        oList.style.marginTop = '-3px'; //MONTE DE 10px POUR ALIGNEMENT LUCIE.
        
        for(var i = 0;i < this.sChoices.length;i++){
            oListItem = nq4_buildHTMLElement('li',{});
            
            oListItem.style.marginBottom = '15px'; //20px
            oListItem.style.border = 'solid 1px #CBC8C8';
            oListItem.style.padding = this.iListItemPadding + 'px';
            oListItem.style.cursor = 'move';
            
            for(var j = 0;j < this.iChoicesPos.length;j++){
                if(this.iChoicesPos[j] == i){
                    iCurrIndex = j;
                    break;
                }
            }
           
            if(this.sChoices[iCurrIndex][1] == this.TEXT){
                oListItem.innerHTML = this.sChoices[iCurrIndex][0];
            }else{
                var imgMediaFolder;
            
                if (this.arrSrcMediaCategory[iCurrIndex] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategory[iCurrIndex] == 2){
                    imgMediaFolder = '';
                }
                
            
                var oContentLI = nq4_buildHTMLElement('table',{cellpadding:'0',cellspacing:'0',border:'0'});
                var oCurrRowLI = oContentLI.insertRow(0);
                var oCellLI = oCurrRowLI.insertCell(0);

                oCellLI.id = "tdLI" + i;

                oListItem.appendChild(oContentLI);
                oCellLI.appendChild(nq4_buildImageObject(this.sChoices[iCurrIndex][0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
            }
            
            oListItem.id = this.sListIdPrefix + this.sIdDelimiter + this.questionNb + this.sIdDelimiter + iCurrIndex;
            this.someSortableId = this.sListIdPrefix + this.sIdDelimiter + this.questionNb + this.sIdDelimiter + iCurrIndex;
            oList.appendChild(oListItem);
        }
        
        return oList;
    },
    buildRankingLabels: function(){
        var sLabel = '';
        var oListItem = null;
        var iCurrIndex = null;
        var oList = nq4_buildHTMLElement('ul',{});
        oList.style.listStyleType = 'none';
        oList.style.padding = '0';
        oList.style.marginLeft = '0';
        oList.style.marginTop = '-3px'; //MONTE DE 10px POUR ALIGNEMENT LUCIE.
        
        for(var i = 0;i < this.sChoices.length;i++){
            oListItem = nq4_buildHTMLElement('li',{});
            
            oListItem.style.marginBottom = '15px';
            oListItem.style.paddingTop = this.iListItemPadding + 'px';

            oListItem.style.paddingBottom = this.iListItemPadding + 'px';
            oListItem.style.paddingRight = '6px';
            
            if(this.labelType == this.LETTERS){
                sLabel = getLetterLabel(i + 1).toUpperCase() + this.sLabelSuffix;
            }else if(this.labelType == this.NUMBERS){
                sLabel = (i + 1) + this.sLabelSuffix;
            }
            oListItem.innerHTML = sLabel;
            
            oList.appendChild(oListItem);
        }
        
        return oList;
    },
    balanceLists: function(oListA,oListB){
        var iMaxWidth = 0;
        var iMaxHeight = 0;
        var reBalanceList = false;
        var listStyle = this.sChoices[0][1]; // 0 = TEXT, 1 = IMAGE. Prend n'importe lequel (ici le premier) car c'est soit tous texte ou tous image.
        var assignImgHeight = false;
        var iMinImgHeight = 0;
        
        for(var i = 0;i < oListA.childNodes.length;i++){
            if(oListA.childNodes[i].tagName == 'LI'){
                iMaxHeight = Math.max(iMaxHeight,getElementPos(oListA.childNodes[i]).height);
                iMaxWidth = Math.max(iMaxWidth,getElementPos(oListA.childNodes[i]).width);
            }
        }
        
        for(var i = 0;i < oListB.childNodes.length;i++){
            if(oListB.childNodes[i].tagName == 'LI'){
                iMaxHeight = Math.max(iMaxHeight,getElementPos(oListB.childNodes[i]).height);
            }
        }
        
        
        var allImgLoaded = true;
        
        jQuery("li img").each(function() {
            if (!jQuery(this).hasClass('img_status_menupages')) {
               var srcImg = jQuery(this).attr("src");

               var iw = jQuery(this).attr("width");
               var ih = jQuery(this).attr("height");
               
               if (listStyle == 0) { // 0 = TEXT, mais a une image à cause de code HTML...
                   if (ih == undefined) {
                      assignImgHeight = true;
                   }
                   else {
                        if (iMinImgHeight == 0) {
                            iMinImgHeight = ih;
                        }
                        else {
                            if (ih < iMinImgHeight) {
                                iMinImgHeight = ih;
                            }
                        }
                   }
               }
               
               if (iw == 0 && ih == 0){
                   allImgLoaded = false;
               }
            }
        });
        
        
        if (assignImgHeight == true) {
           jQuery("li img").each(function() {
               if (!jQuery(this).hasClass('img_status_menupages')) {
                  var ih = jQuery(this).attr("height");
                      
                  if (ih == undefined) {
                     if (iMinImgHeight == 0) {
                        iMinImgHeight = iChoiceMaxHeight; // Default
                     }

                     jQuery(this).attr("height", iMinImgHeight);
                  }
               }
           });
        }
        
        
        if (allImgLoaded == false) {
           reBalanceList = true;
        }
        
        if (reBalanceList == true) {
            top.ccdmd.nq4.recalculerDimensionsAssRank.push("ranking", oListA, oListB);
        }

        
        if (reBalanceList == false) {
           iMaxHeight += this.iListHeightAdjust;
           iMaxWidth = iMaxWidth - (this.iListItemPadding * 2) - 2;


           for(var i = 0;i < oListA.childNodes.length;i++){
               if(oListA.childNodes[i].tagName == 'LI'){
                   oListA.childNodes[i].style.height = iMaxHeight + 'px';
               }
           }
           
           for(var i = 0;i < oListB.childNodes.length;i++){
               if(oListB.childNodes[i].tagName == 'LI'){
                   oListB.childNodes[i].style.height = (iMaxHeight + 2) + 'px';
               }
           }

           if (reBalanceList == false) {
              for(var i = 0;i < oListA.childNodes.length;i++){
                  if(oListA.childNodes[i].tagName == 'LI'){
                      var iCurrIndex;

                      for(var j = 0;j < this.iChoicesPos.length;j++){
                          if(this.iChoicesPos[j] == i){
                              iCurrIndex = j;
                              break;
                          }
                      }

                      this.widthSortableFeedback = iMaxWidth + 'px';

                      if(this.sChoices[iCurrIndex][1] == this.IMAGE){
                          var theID = "tdLI" + i;
                          $(theID).width = iMaxWidth + 'px';
                          $(theID).height = iMaxHeight + 'px';

                          this.tableWidthSortableFeedback = iMaxWidth + 'px';
                          this.tableHeightSortableFeedback = iMaxHeight + 'px';

                          document.getElementById(theID).align = 'center';
                          document.getElementById(theID).vAlign = 'middle';
                      }
                  }
              }
           }
        }
        
        if (assignImgHeight == true) {
            // On doit recalculer la dimensions des items.
            
            for(var i = 0;i < oListA.childNodes.length;i++){
                if(oListA.childNodes[i].tagName == 'LI'){
                    var idLi = oListA.childNodes[i].id;
                    var iMaxH = jQuery("#" + idLi).outerHeight(); // Inclus padding
                    
                    iMaxHeight = Math.max(iMaxHeight,iMaxH);
                }
            }
            
           for(var i = 0;i < oListA.childNodes.length;i++){
               if(oListA.childNodes[i].tagName == 'LI'){
                   oListA.childNodes[i].style.height = iMaxHeight + 'px';
               }
           }
           
           for(var i = 0;i < oListB.childNodes.length;i++){
               if(oListB.childNodes[i].tagName == 'LI'){
                   oListB.childNodes[i].style.height = (iMaxHeight + 2) + 'px';
               }
           }
        }
        
    },
    reBalanceLists: function(oListA,oListB,iNewWidth,iNewHeight,src) {
        var nbImagesLoaded = 0;
        
        jQuery("li img").each(function() {
            if (!jQuery(this).hasClass('img_status_menupages')) {
               var srcImg = jQuery(this).attr("src");

               if (srcImg == src) {            
                  jQuery(this).attr("width", iNewWidth);
                  jQuery(this).attr("height", iNewHeight);
               }
               
               
               var iw = jQuery(this).attr("width");
               var ih = jQuery(this).attr("height");
               
               if (iw != 0 && ih != 0){
                   nbImagesLoaded++;
               }
            }
        });
    
    
        if (nbImagesLoaded == this.nbImages && this.reBalanced == false) {
            var iMaxWidth = 0;
            var iMaxHeight = 0;
            var reBalanceList = false;
            
            for(var i = 0;i < oListA.childNodes.length;i++){
                if(oListA.childNodes[i].tagName == 'LI'){
                    iMaxHeight = Math.max(iMaxHeight,getElementPos(oListA.childNodes[i]).height);
                    iMaxWidth = Math.max(iMaxWidth,getElementPos(oListA.childNodes[i]).width);
                }
            }
            
            for(var i = 0;i < oListB.childNodes.length;i++){
                if(oListB.childNodes[i].tagName == 'LI'){
                    iMaxHeight = Math.max(iMaxHeight,getElementPos(oListB.childNodes[i]).height);
                }
            }   
            
            
           iMaxHeight += this.iListHeightAdjust;
           iMaxWidth = iMaxWidth - (this.iListItemPadding * 2) - 2;


           for(var i = 0;i < oListA.childNodes.length;i++){
               if(oListA.childNodes[i].tagName == 'LI'){
                   oListA.childNodes[i].style.height = iMaxHeight + 'px';
               }
           }
           
           for(var i = 0;i < oListB.childNodes.length;i++){
               if(oListB.childNodes[i].tagName == 'LI'){
                   oListB.childNodes[i].style.height = (iMaxHeight + 2) + 'px';
               }
           }

           if (reBalanceList == false) {
              for(var i = 0;i < oListA.childNodes.length;i++){
                  if(oListA.childNodes[i].tagName == 'LI'){
                      var iCurrIndex;

                      for(var j = 0;j < this.iChoicesPos.length;j++){
                          if(this.iChoicesPos[j] == i){
                              iCurrIndex = j;
                              break;
                          }
                      }

                      this.widthSortableFeedback = iMaxWidth + 'px';

                      if(this.sChoices[iCurrIndex][1] == this.IMAGE){
                          var theID = "tdLI" + i;
                          $(theID).width = iMaxWidth + 'px';
                          $(theID).height = iMaxHeight + 'px';

                          this.tableWidthSortableFeedback = iMaxWidth + 'px';
                          this.tableHeightSortableFeedback = iMaxHeight + 'px';

                          document.getElementById(theID).align = 'center';
                          document.getElementById(theID).vAlign = 'middle';
                      }
                  }
              }
              
              this.reBalanced = true;
               
              Position.includeScrollOffsets = true;
              Sortable.create(this.oList.id, {scroll:window});
           }
        }
    },
    shuffle: function(){
        this.iChoicesPos = getShuffledOrder(this.sChoices.length);
        this.choicesInitPos = this.iChoicesPos.clone();
    },
    getConsigne: function(){
        return this.quiz.consigneRanking;
    }
});