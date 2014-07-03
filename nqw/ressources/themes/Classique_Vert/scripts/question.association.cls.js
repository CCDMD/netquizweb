var QuestionAssociation = Class.create({
    sQuestionType: 'ASSOCIATION',
    
    TEXT: 0,
    IMAGE : 1,
    
    NONE: 0,
    LETTERS: 1,
    NUMBERS: 2,
    
    //System settings
    sListIdPrefix: 'association',
    sIdDelimiter: '_',
    sLabelSuffix: ')&nbsp;&nbsp;',
    iListHeightAdjust: -17,
    iListItemPadding: 5,
    paddingTopCell: 0,
    
    
    //User settings
    iChoiceWidth: 110, //200
    iChoiceMaxHeight: 145, //250
    labelType: 0,

    
    //Variables
    //sChoicesA: new Array(),
    //sChoicesB: new Array(),
    //iChoicesPos: new Array(),
    //choicesInitPos: new Array(),
    //sGoodAnswerFeedback: new Array(),
    //sWrongAnswerFeedback: new Array(),
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
    statusMenuPages: -1, // -1 = incomplet / à faire, 0 = mauvaise réponse, 1 = bonne réponse
    shuffledPositions: 0,
    
    reBalanced: false,
    nbImages: 0,
    
    //Mandatory functions
    initialize: function(quiz, page, questionNb){
        this.quiz = quiz;
        this.page = page;
        this.questionNb = questionNb;

        this.sChoicesA = new Array();
        this.iChoicesLabelPos = new Array();
        this.choicesInitLabelPos = new Array();

        this.sChoicesB = new Array();
        this.iChoicesPos = new Array();
        this.choicesInitPos = new Array();

        this.sGoodAnswerFeedback = new Array();
        this.sWrongAnswerFeedback = new Array();
        
        this.arrSrcMediaCategoryFixed = new Array();
        this.arrSrcMediaCategoryChoice = new Array();
        
        this.status = this.quiz.statusToDo;

        this.typeA = '';
        this.typeB = '';
    },
    
    display: function(){
        //Lists
        this.reBalanced = false;
        
        var oListsWrapper = nq4_buildHTMLElement('table',{cellpadding:'0',cellspacing:'0',border:'0'});

        var oCurrRow = oListsWrapper.insertRow(0);
        var oLabelsTag = oCurrRow.insertCell(0);
        var oLabelsWrapper = oCurrRow.insertCell(1);
        var oChoicesWrapper = oCurrRow.insertCell(2);

        oChoicesWrapper.align = 'left';
        oLabelsWrapper.margin = '0';

        this.oList = this.buildAssociationSortable();
        this.oList.id = this.sListIdPrefix + this.sIdDelimiter + this.questionNb;
        oChoicesWrapper.appendChild(this.oList);

        //TAGS
        var oLabelsListTag = this.buildAssociationLabelsTag();

        oLabelsListTag.id = 'listLabelTag';
        oLabelsTag.appendChild(oLabelsListTag);
       
        //"CONTENT"
        oLabelsList = this.buildAssociationLabels();

        oLabelsList.id = this.sListIdPrefix  + this.sIdDelimiter + 'labels' + this.sIdDelimiter + this.questionNb; //NEW
        oLabelsWrapper.appendChild(oLabelsList);

        $('question').update(oListsWrapper);

        if (this.labelType == 0)
            $('listLabelTag').hide();

        var idListSort = this.sListIdPrefix + this.sIdDelimiter + this.questionNb;
        var idListAss = this.sListIdPrefix  + this.sIdDelimiter + 'labels' + this.sIdDelimiter + this.questionNb;

        if (this.typeA == this.TEXT && this.typeB == this.TEXT){
           var ow = document.getElementById(this.someSortableId).offsetWidth;

           if (ow > 250){
              var ow2 = document.getElementById('labelItem_0').offsetWidth;

              if (ow2 > 270){
                  document.getElementById(idListSort).style.width = '250px';
              }
              else{
                  document.getElementById('labelItem_0').style.width = (ow2 + 10) + 'px';
                  document.getElementById(idListSort).style.width = (ow - 10) + 'px';
              }
           }

           ow = document.getElementById('labelItem_0').offsetWidth;

           if (ow > 270){
               document.getElementById(idListAss).style.width = '250px';
           }
        }

        this.balanceLists(this.oList,oLabelsList,oLabelsTag);

        Position.includeScrollOffsets = true;
        Sortable.create(this.oList.id, {scroll:window});
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
                
                this.sSaveValue += iIndex + ',';
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
        var iCurrIndexLabels = null;
        var heightToPut;
        var widthToPut;
        
        var label = '';
        var choice = '';
        var fixed = '';
        var bulletImage = '';
        var feedback = '';
        var imgMediaFolder;

        for(var i = 0;i < this.sChoicesB.length; i++){
            var classFeedBackLine = 'feedbackLineTxt';
            var theCols = '<td width="20">&nbsp;</td>';

            switch(this.labelType){
                case this.LETTERS:
                    label = getLetterLabel(i + 1).toUpperCase() + ')';
                    break;
                case this.NUMBERS:
                    label = (i + 1) + ')';
                    break;
            }

            iCurrIndexLabels = i;

            fixed = this.sChoicesA[iCurrIndexLabels];
            currentChoiceId = this.iChoicesPos.indexOf(i);
            choice = this.sChoicesB[currentChoiceId];

            for(var j = 0;j < this.sChoicesB.length;j++){
                if (this.choicesInitPos[j] == i){
                    if(this.iChoicesPos[iCurrIndexLabels] == i){
                        goodAnswerCount++;
                        bulletImage = 'bullet_green.png';
                        feedback = this.sGoodAnswerFeedback[iCurrIndexLabels];
                    }else{
                        wrongAnswerCount++;
                        bulletImage = 'bullet_red.png';
                        feedback = this.sWrongAnswerFeedback[iCurrIndexLabels]
                    }
                }
            }

            widthToPut = document.getElementById("labelItem_0").offsetWidth;
            heightToPut = document.getElementById(this.someSortableId).style.height;

            feedbackHTML += '<table cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
            feedbackHTML += '<tr>';
            feedbackHTML += '<td width="20" style="padding-top: 4px;"><img src="images/' + bulletImage + '" /></td>';
            if (this.labelType > 0){
                feedbackHTML += '<td width="25"><ul style="list-style-type: none; margin-top: -2px;  margin-bottom:0px; padding: 0pt; margin-left: 0pt;"><li style="margin-bottom: 0px; padding-top: 5px; padding-bottom: 5px; padding-right: 3px; height:' + heightToPut + ';">' + label + '</li></ul></td>';
                theCols = theCols + '<td width="25">&nbsp;</td>';
            }

            if(fixed[1] == this.TEXT){
                heightToPut = "100%";
                feedbackHTML += '<td width="' + widthToPut + '" height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li class="liAssLabel" style="margin-bottom: 0px; padding: 5px 20px 5px 5px; height:' + heightToPut + ';">' + fixed[0] + '</li></ul></td>';
            }else{
                if (this.arrSrcMediaCategoryFixed[i] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategoryFixed[i] == 2){
                    imgMediaFolder = '';
                }
            
                heightToPut = "100%";

                var temp = new Element('div');
                temp.appendChild(nq4_buildImageObject(fixed[0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
                feedbackHTML += '<td width="' + widthToPut + '" height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li class="liAssLabel" style="margin-bottom: 0px; padding: 5px 20px 5px 5px; height:' + heightToPut + ';">' + temp.innerHTML + '</li></ul></td>';

                classFeedBackLine = 'feedbackLineImgAssRank';
            }

            heightToPut = document.getElementById(this.someSortableId).style.height;

            if(choice[1] == this.TEXT){
                 if(fixed[1] == this.TEXT){
                    feedbackHTML += '<td height="' + heightToPut + '"><div style="display: table-row"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:5px; min-width:' + this.widthSortableFeedback + 'px; max-width:' + this.widthSortableFeedback + 'px; height:' + heightToPut + ';">' + choice[0] + '</li></ul></div></td>';
                }
                else{
                    feedbackHTML += '<td height="' + heightToPut + '"><div style="display: table-row"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:3px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:5px; min-width:' + this.widthSortableFeedback + 'px; max-width:' + this.widthSortableFeedback + 'px; height:' + heightToPut + ';">' + choice[0] + '</li></ul></div></td>';
                }
            }else{
                 if(fixed[1] == this.TEXT){
                    if (this.arrSrcMediaCategoryChoice[currentChoiceId] == 1){
                        imgMediaFolder = this.quiz.mediasFolder;
                    }
                    else if(this.arrSrcMediaCategoryChoice[currentChoiceId] == 2){
                        imgMediaFolder = '';
                    } 
                 
                    var temp = new Element('div');
                    temp.appendChild(nq4_buildImageObject(choice[0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
                    feedbackHTML += '<td height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:5px; position:relative; width:' + this.widthSortableFeedback + 'px;  height:' + heightToPut + ';"><table class="feedbackTable2" cellspacing="0" cellpadding="0" border="0"><tr><td width="' + this.tableWidthSortableFeedback + '" height="' + this.tableHeightSortableFeedback + '" align="center" valign="middle">' + temp.innerHTML + '</td></tr></table></li></ul></td>';

                    classFeedBackLine = 'feedbackLineImgAssRank';
                }
                else{
                    if (this.arrSrcMediaCategoryChoice[currentChoiceId] == 1){
                        imgMediaFolder = this.quiz.mediasFolder;
                    }
                    else if(this.arrSrcMediaCategoryChoice[currentChoiceId] == 2){
                        imgMediaFolder = '';
                    }
                
                    var temp = new Element('div');
                    temp.appendChild(nq4_buildImageObject(choice[0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
                    feedbackHTML += '<td height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:5px; position:relative; width:' + this.widthSortableFeedback + 'px;  height:' + heightToPut + ';"><table class="feedbackTable2" cellspacing="0" cellpadding="0" border="0"><tr><td width="' + this.tableWidthSortableFeedback + '" height="' + this.tableHeightSortableFeedback + '" align="center" valign="middle">' + temp.innerHTML + '</td></tr></table></li></ul></td>';

                    classFeedBackLine = 'feedbackLineImgAssRank';
                }
            }

            feedbackHTML += '</tr>';

            if (feedback != ''){
                feedbackHTML += '<tr><td height="5"></td></tr><tr>' + theCols + '<td colspan="2" style="padding-left:5px;"><span class="small">' + feedback + '</span></td></tr>';
            }
            feedbackHTML += '<tr>' + theCols + '<td class="feedbackLineSpacer">&nbsp;</td></tr>';
            feedbackHTML += '</table>';
        }
        
        this.currentScore = goodAnswerCount / this.sChoicesB.length * this.ponderation;
        
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
        var iCurrIndexLabels = null; 
        var heightToPut;
        var widthToPut;
        var imgMediaFolder;
        var widthSortableFeedbackOrig = this.widthSortableFeedback;

        for(var i = 0;i < this.sChoicesB.length;i++){
            switch(this.labelType){
                case this.LETTERS:
                    label = getLetterLabel(i + 1).toUpperCase() + ')';
                    break;
                case this.NUMBERS:
                    label = (i + 1) + ')';
                    break;
            }

            iCurrIndexLabels = i;

            fixed = this.sChoicesA[iCurrIndexLabels];
            

            for(var j = 0;j < this.sChoicesB.length;j++){
                if (this.choicesInitPos[j] == i){
                    choice = this.sChoicesB[iCurrIndexLabels];
                }
            }

            widthToPut = document.getElementById("labelItem_0").offsetWidth;
            heightToPut = document.getElementById("labelItemTag_0").style.height;
            
            solutionHTML += '<table cellpadding="0" cellspacing="0" border="0" class="feedbackTable">';
            solutionHTML += '<tr>';

            if (this.labelType > 0)
                solutionHTML += '<td width="25"><ul style="list-style-type: none; margin-top: -2px; padding: 0pt; margin-left: 0pt;"><li style="margin-bottom: 15px; padding-top: 5px; padding-bottom: 5px; padding-right: 3px; height:' + heightToPut + ';">' + label + '</li></ul></td>';
            
            if(fixed[1] == this.TEXT){
                solutionHTML += '<td width="' + widthToPut + '" height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li class="liAssLabel" style="margin-bottom: 15px; padding: 5px 20px 5px 5px; height:' + heightToPut + ';">' + fixed[0] + '</li></ul></td>';
            }else{
                if (this.arrSrcMediaCategoryFixed[iCurrIndexLabels] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategoryFixed[iCurrIndexLabels] == 2){
                    imgMediaFolder = '';
                }
                    
            
                var temp = new Element('div');
                temp.appendChild(nq4_buildImageObject(fixed[0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
                solutionHTML += '<td width="' + widthToPut + '" height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li class="liAssLabel" style="margin-bottom: 15px; padding: 5px 20px 5px 5px; height:' + heightToPut + ';">' + temp.innerHTML + '</li></ul></td>';
            }

            heightToPut = document.getElementById(this.someSortableId).style.height;

            if(choice[1] == this.TEXT){
                if(fixed[1] == this.TEXT){
                    solutionHTML += '<td height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:5px; min-width:' + this.widthSortableFeedback + 'px; max-width:' + this.widthSortableFeedback + 'px; height:' + heightToPut + ';">' + choice[0] + '</li></ul></td>';
                }
                else{
                    solutionHTML += '<td height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:3px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:5px; min-width:' + this.widthSortableFeedback + 'px; max-width:' + this.widthSortableFeedback + 'px; height:' + heightToPut + ';">' + choice[0] + '</li></ul></td>';
                }
            }else{
                if(fixed[1] == this.TEXT){
                    if (this.arrSrcMediaCategoryChoice[iCurrIndexLabels] == 1){
                        imgMediaFolder = this.quiz.mediasFolder;
                    }
                    else if(this.arrSrcMediaCategoryChoice[iCurrIndexLabels] == 2){
                        imgMediaFolder = '';
                    }
                
                    var temp = new Element('div');
                    temp.appendChild(nq4_buildImageObject(choice[0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
                    solutionHTML += '<td height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:5px; position:relative; width:' + this.widthSortableFeedback + 'px;  height:' + heightToPut + ';"><table class="feedbackTable2" cellspacing="0" cellpadding="0" border="0"><tr><td width="' + this.tableWidthSortableFeedback + '" height="' + this.tableHeightSortableFeedback + '" align="center" valign="middle">' + temp.innerHTML + '</td></tr></table></li></ul></td>';
                }
                else{
                    if (this.arrSrcMediaCategoryChoice[iCurrIndexLabels] == 1){
                        imgMediaFolder = this.quiz.mediasFolder;
                    }
                    else if(this.arrSrcMediaCategoryChoice[iCurrIndexLabels] == 2){
                        imgMediaFolder = '';
                    }
                
                    var temp = new Element('div');
                    temp.appendChild(nq4_buildImageObject(choice[0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
                    solutionHTML += '<td height="' + heightToPut + '"><ul style="list-style-type:none; padding:0pt; margin-left:0pt; margin-top:-2px; margin-bottom:0px;"><li style="border:1px solid #CBC8C8; padding:5px; position:relative; width:' + this.widthSortableFeedback + 'px;  height:' + heightToPut + ';"><table class="feedbackTable2" cellspacing="0" cellpadding="0" border="0"><tr><td width="' + this.tableWidthSortableFeedback + '" height="' + this.tableHeightSortableFeedback + '" align="center" valign="middle">' + temp.innerHTML + '</td></tr></table></li></ul></td>';
                }
            }
            
            solutionHTML += '</tr>';
            solutionHTML += '</table>';
        }
        
        setFeedback(solutionHTML);
        openFeedback();
    },
    redo:function(){
        top.ccdmd.nq4.recalculerDimensionsAssRank = [];
        
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
    addChoice: function(sChoiceA,iChoiceTypeA,sChoiceB,iChoiceTypeB,sGoodAnswerFeedback,sWrongAnswerFeedback,position,mediaCategory1,mediaCategory2){
        var strToEvaluate;
        
        this.sChoicesA[this.sChoicesA.length] = [sChoiceA, iChoiceTypeA];
        if(iChoiceTypeA == this.IMAGE){
            this.nbImages++;
        
            if (mediaCategory1 == 1){
                sChoiceA = this.quiz.mediasFolder + '/' + sChoiceA;
            }
            this.quiz.imgPreloader.addImage(sChoiceA);
        }
        
        
        this.sChoicesB[this.sChoicesB.length] = [sChoiceB, iChoiceTypeB];
        if(iChoiceTypeB == this.IMAGE){
            this.nbImages++;
            
            if (mediaCategory2 == 1){
                sChoiceB = this.quiz.mediasFolder + '/' + sChoiceB;
            }
            this.quiz.imgPreloader.addImage(sChoiceB);
        }
        
        if (position == 0){
            this.iChoicesLabelPos = getShuffledOrder(this.sChoicesA.length);
            this.choicesInitLabelPos = this.iChoicesLabelPos.clone();

            this.iChoicesPos = this.iChoicesLabelPos.clone();
            this.choicesInitPos = this.iChoicesLabelPos.clone();
        }
        else{
            this.iChoicesPos[this.iChoicesPos.length] = position - 1;
            this.choicesInitPos[this.choicesInitPos.length] = position - 1;

            this.iChoicesLabelPos[this.iChoicesLabelPos.length] = this.iChoicesPos[this.iChoicesPos.length - 1];
            this.choicesInitLabelPos[this.choicesInitLabelPos.length] = this.iChoicesPos[this.iChoicesPos.length - 1];
        }


        this.sGoodAnswerFeedback[this.sGoodAnswerFeedback.length] = sGoodAnswerFeedback;
        this.sWrongAnswerFeedback[this.sWrongAnswerFeedback.length] = sWrongAnswerFeedback;
        
        
        strToEvaluate = this.sGoodAnswerFeedback[this.sGoodAnswerFeedback.length - 1];
        this.sGoodAnswerFeedback[this.sGoodAnswerFeedback.length - 1] = evalStringForLexique(strToEvaluate);

        strToEvaluate = this.sWrongAnswerFeedback[this.sWrongAnswerFeedback.length - 1];
        this.sWrongAnswerFeedback[this.sWrongAnswerFeedback.length - 1] = evalStringForLexique(strToEvaluate);

        
        this.arrSrcMediaCategoryFixed[this.arrSrcMediaCategoryFixed.length] = mediaCategory1;
        this.arrSrcMediaCategoryChoice[this.arrSrcMediaCategoryChoice.length] = mediaCategory2;
    },
    setChoicePos: function(iID,iPos){
        this.iChoicesPos[iID] = iPos;
    },
    buildAssociationSortable: function(){
        var oListItem = null;
        var iCurrIndex = null;
        var oList = nq4_buildHTMLElement('ul',{});
        oList.style.listStyleType = 'none';
        oList.style.padding = '0';
        oList.style.marginLeft = '0';
        oList.style.marginTop = '-2px'; //MONTE DE 10px POUR ALIGNEMENT LUCIE.
        
        for(var i = 0;i < this.sChoicesB.length;i++){
            oListItem = nq4_buildHTMLElement('li',{});
            
            oListItem.style.border = 'solid 1px #CBC8C8';
            oListItem.style.padding = this.iListItemPadding + 'px';
            oListItem.style.cursor = 'move';

            for(var j = 0;j < this.iChoicesPos.length;j++){
                if(this.iChoicesPos[j] == i){
                    iCurrIndex = j;
                    break;
                }
            }
            
            if(this.sChoicesB[iCurrIndex][1] == this.TEXT){
                oListItem.innerHTML = this.sChoicesB[iCurrIndex][0];
                this.typeB = this.TEXT;
            }else{
                var imgMediaFolder;
            
                if (this.arrSrcMediaCategoryChoice[iCurrIndex] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategoryChoice[iCurrIndex] == 2){
                    imgMediaFolder = '';
                }
            
            
                var oContentLI = nq4_buildHTMLElement('table',{cellpadding:'0',cellspacing:'0',border:'0'});
                var oCurrRowLI = oContentLI.insertRow(0);
                var oCellLI = oCurrRowLI.insertCell(0);

                oCellLI.id = "tdLI" + i;
                oListItem.appendChild(oContentLI);
                oCellLI.appendChild(nq4_buildImageObject(this.sChoicesB[iCurrIndex][0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder));
            }
            
            oListItem.id = i + this.sIdDelimiter + this.sListIdPrefix + this.sIdDelimiter + iCurrIndex; //NEW
            this.someSortableId = i + this.sIdDelimiter + this.sListIdPrefix + this.sIdDelimiter + iCurrIndex;
            oList.appendChild(oListItem);
        }
        
        return oList;
    },
    buildAssociationLabelsTag: function(){
        var oListItem = null;
        var iCurrIndex = null;
        var iCurrIndexLabels = null; 
        var oList = nq4_buildHTMLElement('ul',{});
        var sLabel = '';
        var oImg = null;
        oList.style.listStyleType = 'none';
        oList.style.marginTop = '-2px'; //MONTE POUR ALIGNEMENT LUCIE.
        
        oList.style.padding = '0';
        oList.style.marginLeft = '0';
        
        for(var i = 0;i < this.sChoicesA.length;i++){
            oListItem = nq4_buildHTMLElement('li',{});
            
            oListItem.style.marginBottom = '15px'; //15px
            oListItem.style.paddingTop = this.iListItemPadding + 'px';
            oListItem.style.paddingBottom = this.iListItemPadding + 'px';
            oListItem.style.paddingRight = '3px';
            
            if(this.labelType == this.LETTERS){
                sLabel = getLetterLabel(i + 1).toUpperCase() + this.sLabelSuffix;
            }else if(this.labelType == this.NUMBERS){
                sLabel = (i + 1) + this.sLabelSuffix;
            }

            if (this.sChoicesB[i][1] == this.TEXT)
                oListItem.innerHTML = '<div style="width:5px;height:5px"></div>' + sLabel;
            else
                oListItem.innerHTML = sLabel;

            oListItem.id = "labelItemTag" + this.sIdDelimiter + i;
            oList.appendChild(oListItem);
        }
        
        return oList;
    },
    buildAssociationLabels: function(){
        var oListItem = null;
        var iCurrIndex = null;
        var oList = nq4_buildHTMLElement('ul',{});
        var sLabel = '';
        var oImg = null;
        oList.style.listStyleType = 'none';
        oList.style.marginTop = '-2px'; //MONTE POUR ALIGNEMENT LUCIE.
        
        oList.style.padding = '0';
        oList.style.marginLeft = '0';
        
        for(var i = 0;i < this.sChoicesA.length;i++){
            oListItem = nq4_buildHTMLElement('li',{});
            
            oListItem.style.marginBottom = '15px';
            oListItem.style.paddingTop = this.iListItemPadding + 'px';
            oListItem.style.paddingBottom = this.iListItemPadding + 'px';
            oListItem.style.paddingLeft = this.iListItemPadding + 'px';
            oListItem.style.paddingRight = 20 + 'px';
            oListItem.className = 'liAssLabel';

            iCurrIndexLabels = i;

            if(this.sChoicesA[iCurrIndexLabels][1] == this.TEXT){
                oListItem.innerHTML += this.sChoicesA[iCurrIndexLabels][0];
                this.typeA = this.TEXT;
            }else{
                var imgMediaFolder;
            
                if (this.arrSrcMediaCategoryFixed[iCurrIndexLabels] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategoryFixed[iCurrIndexLabels] == 2){
                    imgMediaFolder = '';
                }
            
            
                oImg = nq4_buildImageObject(this.sChoicesA[iCurrIndexLabels][0],this.iChoiceWidth,this.iChoiceMaxHeight,imgMediaFolder);
                oImg.align = 'absmiddle';
                oListItem.appendChild(oImg);
            }

            if (this.sChoicesB[iCurrIndexLabels][1] == this.TEXT)
                oListItem.innerHTML = '<div style="width:5px;height:5px"></div>' + oListItem.innerHTML;
            
            oListItem.id = "labelItem" + this.sIdDelimiter + i;
            oList.appendChild(oListItem);
        }
        
        return oList;
    },
    balanceLists: function(oListA,oListB,oListC){
        //oListA = SORTABLES
        //oListB = LABELS CONTENT
        //oListC = LABELS TAG
        
        var iMaxWidth = 0;
        var iMaxHeight = 0;
        var addedHeight = 0;
        var theLabelHeight = 0;
        var onlyLabelHeight = 0;
        var theSortableHeight = 0;
        var theHeightToPut = 0;
        var iMaxLabel = 0;
        var arrMaxHeightLabel = new Array();
        var arrMaxHeightSortable = new Array();
        var idLeftList = this.sListIdPrefix + this.sIdDelimiter + this.questionNb;
        var theMarginBottom;
        var reBalanceList = false;
        

        for(var i = 0;i < oListA.childNodes.length;i++){
            if(oListA.childNodes[i].tagName == 'LI'){
                iMaxHeight = Math.max(iMaxHeight,getElementPos(oListA.childNodes[i]).height);
                theSortableHeight = iMaxHeight - (this.iListItemPadding * 2);

                iMaxWidth = Math.max(iMaxWidth,getElementPos(oListA.childNodes[i]).width);
            }
        }
        
        for(var i = 0;i < oListB.childNodes.length;i++){
            if(oListB.childNodes[i].tagName == 'LI'){
                iMaxHeight = Math.max(iMaxHeight,getElementPos(oListB.childNodes[i]).height);
                iMaxLabel = Math.max(iMaxLabel,getElementPos(oListB.childNodes[i]).height);
            }
        }

        
        var allImgLoaded = true;
        
        jQuery("li img").each(function() {
            if (!jQuery(this).hasClass('img_status_menupages')) {
               var srcImg = jQuery(this).attr("src");

               var iw = jQuery(this).attr("width");
               var ih = jQuery(this).attr("height");
               
               if (iw == 0 && ih == 0){
                   allImgLoaded = false;
               }
            }
        });
        
        if (allImgLoaded == false) {
           reBalanceList = true;
        }
        
        if (reBalanceList == true) {
            top.ccdmd.nq4.recalculerDimensionsAssRank.push("association", oListA, oListB, oListC);
        }

        if (reBalanceList == false) {
            iMaxHeight += this.iListHeightAdjust;
        
           for(var i = 0;i < oListA.childNodes.length;i++){
               if(oListA.childNodes[i].tagName == 'LI'){

                  if (theSortableHeight > 0)
                      oListA.childNodes[i].style.height = theSortableHeight + 'px';
               }
           }

           for(var i = 0;i < oListB.childNodes.length;i++){
               var idlabelItemTag = 'labelItemTag_' + i;

               if(oListB.childNodes[i].tagName == 'LI'){
                   if (theSortableHeight > iMaxHeight){
                       oListB.childNodes[i].style.height = (theSortableHeight) + 15 + 'px';
                       $(idlabelItemTag).style.height = (theSortableHeight) + 15 + 'px';

                       addedHeight = addedHeight + (theSortableHeight) + (this.iListItemPadding * 2) + 15 + 15;

                       onlyLabelHeight = (theSortableHeight);
                       theLabelHeight = (theSortableHeight) + 15 + 15;
                   }
                   else{
                       oListB.childNodes[i].style.height = (iMaxHeight + 2) + 'px';
                       $(idlabelItemTag).style.height = (iMaxHeight + 2) + 'px';

                       addedHeight = addedHeight + (iMaxHeight + 2) + (this.iListItemPadding * 2) + 15;

                       onlyLabelHeight = (iMaxHeight + 2);
                       theLabelHeight = (iMaxHeight + 2) + (this.iListItemPadding * 2) + 15;
                   }
               }
           }

           addedHeight = addedHeight + this.iListHeightAdjust;
           document.getElementById(idLeftList).style.height = addedHeight + 'px';

           for(var i = 0;i < oListB.childNodes.length;i++){
              var iplus1 = i + 1;
              var iCurrIndex = null;

              var missingToNextTop;
              var missingToNextBottom;

              for(var j = 0;j < this.iChoicesPos.length;j++){
                   if(this.iChoicesPos[j] == i){
                       iCurrIndex = j;
                       break;
                   }
               }

              if (theSortableHeight <= iMaxHeight){
                  if(this.sChoicesA[iCurrIndex][1] == this.TEXT){
                      missingToNextTop = (onlyLabelHeight / 2) - (theSortableHeight / 2) - 4;
                  }
                  else{
                      missingToNextTop = (onlyLabelHeight / 2) - (theSortableHeight);
                  }

                  missingToNextBottom = theLabelHeight - theSortableHeight - 10;
              }
              else{
                  missingToNextTop = (theLabelHeight / 2) - (theSortableHeight / 2) - 4;
                  missingToNextBottom = theLabelHeight - theSortableHeight;
              }

              this.paddingTopCell = missingToNextTop;

              var sortableId = i + this.sIdDelimiter + this.sListIdPrefix + this.sIdDelimiter + iCurrIndex; //NEW


              var newMarginBottom;

              if (i == 0){
                  theMarginBottom = missingToNextBottom - 2;
                  newMarginBottom = theMarginBottom;
              }
              else{
                  newMarginBottom = theMarginBottom;
              }

              document.getElementById(sortableId).style.marginBottom = newMarginBottom + 'px';

           }


           var innerHTMLUL;

           if(this.sChoicesB[iCurrIndex][1] == this.TEXT){
               innerHTMLUL = document.getElementById(idLeftList).innerHTML;

               if (this.sChoicesA[iCurrIndex][1] == this.TEXT){
                   innerHTMLUL = "<div id=\"divMarginTop\" style=\"height:3px\">&nbsp;</div>" + innerHTMLUL;
               }
               else{
                   innerHTMLUL = "<div id=\"divMarginTop\" style=\"height:8px\">&nbsp;</div>" + innerHTMLUL;
               }

               document.getElementById(idLeftList).innerHTML = innerHTMLUL;
           }

           for(var i = 0;i < oListA.childNodes.length;i++){
               if(oListA.childNodes[i].tagName == 'LI'){
                   var iCurrIndex;

                   for(var j = 0;j < this.iChoicesPos.length;j++){
                       if(this.iChoicesPos[j] == i){
                           iCurrIndex = j;
                           break;
                       }
                   }

                   var theSortableIdTD = i + this.sIdDelimiter + this.sListIdPrefix + this.sIdDelimiter + iCurrIndex; //NEW
                   theHeightToPut = '';
                   iMaxWidth = '';
                   theHeightToPut = document.getElementById(sortableId).style.height;
                   iMaxWidth = document.getElementById(sortableId).offsetWidth;
                   iMaxWidth = iMaxWidth - 10 - 2;
                   this.widthSortableFeedback = iMaxWidth;

                   if(this.sChoicesB[iCurrIndex][1] == this.IMAGE){
                       var theID = "tdLI" + i;
                       $(theID).width = iMaxWidth + 'px';
                       $(theID).height = theHeightToPut;

                       this.tableWidthSortableFeedback = iMaxWidth + 'px';
                       this.tableHeightSortableFeedback = theHeightToPut;

                       document.getElementById(theID).align = 'center';
                       document.getElementById(theID).vAlign = 'middle';
                   }
               }
           }
        }
    },
    reBalanceLists: function(oListA,oListB,oListC,iNewWidth,iNewHeight,src){
        //oListA = SORTABLES
        //oListB = LABELS CONTENT
        //oListC = LABELS TAG
        
        var nbImagesLoaded = 0;
        
        jQuery("#question li img").each(function() {
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
        });
        
        if (nbImagesLoaded == this.nbImages && this.reBalanced == false) {
            var iMaxWidth = 0;
            var iMaxHeight = 0;
            var addedHeight = 0;
            var theLabelHeight = 0;
            var onlyLabelHeight = 0;
            var theSortableHeight = 0;
            var theHeightToPut = 0;
            var iMaxLabel = 0;
            var arrMaxHeightLabel = new Array();
            var arrMaxHeightSortable = new Array();
            var idLeftList = this.sListIdPrefix + this.sIdDelimiter + this.questionNb;
            var theMarginBottom;
            var reBalanceList = false;
            
            for(var i = 0;i < oListA.childNodes.length;i++){
                if(oListA.childNodes[i].tagName == 'LI'){
                    iMaxHeight = Math.max(iMaxHeight,getElementPos(oListA.childNodes[i]).height);
                    theSortableHeight = iMaxHeight - (this.iListItemPadding * 2);

                    iMaxWidth = Math.max(iMaxWidth,getElementPos(oListA.childNodes[i]).width);
                }
            }
            
            for(var i = 0;i < oListB.childNodes.length;i++){
                if(oListB.childNodes[i].tagName == 'LI'){
                    iMaxHeight = Math.max(iMaxHeight,getElementPos(oListB.childNodes[i]).height);
                    iMaxLabel = Math.max(iMaxLabel,getElementPos(oListB.childNodes[i]).height);
                }
            }
            
            iMaxHeight += this.iListHeightAdjust;

            if (reBalanceList == false) {
               for(var i = 0;i < oListA.childNodes.length;i++){
                   if(oListA.childNodes[i].tagName == 'LI'){

                      if (theSortableHeight > 0)
                          oListA.childNodes[i].style.height = theSortableHeight + 'px';
                   }
               }

               for(var i = 0;i < oListB.childNodes.length;i++){
                   var idlabelItemTag = 'labelItemTag_' + i;

                   if(oListB.childNodes[i].tagName == 'LI'){
                       if (theSortableHeight > iMaxHeight){
                           oListB.childNodes[i].style.height = (theSortableHeight) + 15 + 'px';
                           $(idlabelItemTag).style.height = (theSortableHeight) + 15 + 'px';

                           addedHeight = addedHeight + (theSortableHeight) + (this.iListItemPadding * 2) + 15 + 15;

                           onlyLabelHeight = (theSortableHeight);
                           theLabelHeight = (theSortableHeight) + 15 + 15;
                           
                       }
                       else{
                           oListB.childNodes[i].style.height = (iMaxHeight + 2) + 'px';
                           $(idlabelItemTag).style.height = (iMaxHeight + 2) + 'px';

                           addedHeight = addedHeight + (iMaxHeight + 2) + (this.iListItemPadding * 2) + 15;

                           onlyLabelHeight = (iMaxHeight + 2);
                           theLabelHeight = (iMaxHeight + 2) + (this.iListItemPadding * 2) + 15;
                       }
                   }
               }

               addedHeight = addedHeight + this.iListHeightAdjust;
               document.getElementById(idLeftList).style.height = addedHeight + 'px';

               for(var i = 0;i < oListB.childNodes.length;i++){
                  var iplus1 = i + 1;
                  var iCurrIndex = null;

                  var missingToNextTop;
                  var missingToNextBottom;

                  for(var j = 0;j < this.iChoicesPos.length;j++){
                       if(this.iChoicesPos[j] == i){
                           iCurrIndex = j;
                           break;
                       }
                   }

                  if (theSortableHeight <= iMaxHeight){
                      if(this.sChoicesA[iCurrIndex][1] == this.TEXT){
                          missingToNextTop = (onlyLabelHeight / 2) - (theSortableHeight / 2) - 4;
                      }
                      else{
                          missingToNextTop = (onlyLabelHeight / 2) - (theSortableHeight);
                      }

                      missingToNextBottom = theLabelHeight - theSortableHeight - 10;
                  }
                  else{
                      missingToNextTop = (theLabelHeight / 2) - (theSortableHeight / 2) - 4;
                      missingToNextBottom = theLabelHeight - theSortableHeight;
                  }

                  this.paddingTopCell = missingToNextTop;

                  var sortableId = i + this.sIdDelimiter + this.sListIdPrefix + this.sIdDelimiter + iCurrIndex; //NEW


                  var newMarginBottom;

                  if (i == 0){
                      theMarginBottom = missingToNextBottom - 2;
                      newMarginBottom = theMarginBottom;
                  }
                  else{
                      newMarginBottom = theMarginBottom;
                  }

                  document.getElementById(sortableId).style.marginBottom = newMarginBottom + 'px';

               }


               var innerHTMLUL;

               if(this.sChoicesB[iCurrIndex][1] == this.TEXT){
                   innerHTMLUL = document.getElementById(idLeftList).innerHTML;

                   if (this.sChoicesA[iCurrIndex][1] == this.TEXT){
                       innerHTMLUL = "<div id=\"divMarginTop\" style=\"height:3px\">&nbsp;</div>" + innerHTMLUL;
                   }
                   else{
                       innerHTMLUL = "<div id=\"divMarginTop\" style=\"height:8px\">&nbsp;</div>" + innerHTMLUL;
                   }

                   document.getElementById(idLeftList).innerHTML = innerHTMLUL;
               }

               for(var i = 0;i < oListA.childNodes.length;i++){
                   if(oListA.childNodes[i].tagName == 'LI'){
                       var iCurrIndex;

                       for(var j = 0;j < this.iChoicesPos.length;j++){
                           if(this.iChoicesPos[j] == i){
                               iCurrIndex = j;
                               break;
                           }
                       }

                       var theSortableIdTD = i + this.sIdDelimiter + this.sListIdPrefix + this.sIdDelimiter + iCurrIndex; //NEW
                       theHeightToPut = '';
                       iMaxWidth = '';
                       theHeightToPut = document.getElementById(sortableId).style.height;
                       iMaxWidth = document.getElementById(sortableId).offsetWidth;
                       iMaxWidth = iMaxWidth - 10 - 2;
                       this.widthSortableFeedback = iMaxWidth;

                       if(this.sChoicesB[iCurrIndex][1] == this.IMAGE){
                           var theID = "tdLI" + i;
                           $(theID).width = iMaxWidth + 'px';
                           $(theID).height = theHeightToPut;

                           this.tableWidthSortableFeedback = iMaxWidth + 'px';
                           this.tableHeightSortableFeedback = theHeightToPut;

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
        this.iChoicesPos = getShuffledOrder(this.sChoicesA.length);
        this.choicesInitPos = this.iChoicesPos.clone();
    },
    getConsigne: function(){
        return this.quiz.consigneAssociation;
    }
});