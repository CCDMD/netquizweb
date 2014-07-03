var QuestionCheckerBoard = Class.create({
    sQuestionType: 'DAMIER',

    TEXTE_TEXTE: 0,
    TEXTE_IMAGE: 1,
    IMAGE_TEXTE: 2,
    IMAGE_IMAGE: 3,
    
    NONE: 0,
    LETTERS: 1,
    NUMBERS: 2,
    
    iType: 0,
    iAffichage: 0,
    sBkgA: "FFFFFF",
    sBkgB: "FFFFFF",
    feedback: "",
    checkerBoard: null,
    
    //VARIABLE COMMUNE
    ponderation: 1,
    sDecDelimiter: ",",
    iLabelType: 0,
    currentScore: 0,
    status: 0,
    statusMenuPages: -1,
    triesCount: 0,
    questionNb: 0,
    
     //Mandatory functions
    initialize: function(quiz, page, questionNb){
        this.quiz = quiz;
        this.page = page;
        this.questionNb = questionNb;
        this.checkerBoard = getNewCheckerBoard();
        this.checkerBoard.owner = this;

        //REGLE BOGUE DOUBLONS SOLUTION
        this.sChoixA = new Array();
        this.sChoixB = new Array();
        this.bFound = new Array();
        this.iChoixOrder = new Array();
        this.sLogo = new Array();
        
        this.retroactionPos = new Array();

        this.arrSrcMediaCategory = new Array();
        this.arrSrcMediaCategoryA = new Array();
        this.arrSrcMediaCategoryB = new Array();
        this.arrSrcMediaCategoryBack = new Array();
        
        this.status = this.quiz.statusToDo;
        
        this.checkerBoard.onPairFound = this.setBrotherhoodFound;
    },
    
    save: function(){

    },
    
    display: function(){
        this.checkerBoard.display($('question'));
    },
    
    validate: function(){
        var feedbackHTML = "";
        var currentScore = 0;
        var goodAnswerCount = 0;
        var imgMediaFolder = 0;
        var strToEvaluate;
        var filteredChoice;
        
        top.ccdmd.nq4.recalculerDimensionsDamier = true;
        
        this.triesCount++;
        
        feedbackHTML += "<table class=\"feedbackTable\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
        for(var i = 0;i < this.bFound.length;i++){
            if(this.bFound[i]){
                goodAnswerCount++;
                
                feedbackHTML += "<tr>";  
                feedbackHTML += "<td style=\"width: 20px\"><img src=\"images/bullet_green.png\"></td>";
                
                if(this.iType == this.TEXTE_TEXTE || this.iType == this.TEXTE_IMAGE){
                    // feedbackHTML += "<td valign=\"top\">" + this.sChoixA[i] + "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
                    
                    filteredChoice = this.sChoixA[i];
                    strToEvaluate = filteredChoice;
                    filteredChoice = evalStringForLexique(strToEvaluate);
                    
                    feedbackHTML += "<td valign=\"top\">" + filteredChoice + "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
                }else{
                    if (this.arrSrcMediaCategoryA[i] == 1){
                        imgMediaFolder = this.quiz.mediasFolder;
                    }
                    else if(this.arrSrcMediaCategoryA[i] == 2){
                        imgMediaFolder = '';
                    }
                
                    feedbackHTML += "<td valign=\"top\">";
                    var temp = new Element('div');
                    temp.appendChild(nq4_buildImageObject(this.sChoixA[i],110,145,imgMediaFolder));
                    feedbackHTML += temp.innerHTML;
                        
                    feedbackHTML += "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
                }

                if(this.iType == this.TEXTE_TEXTE || this.iType == this.IMAGE_TEXTE){
                    // feedbackHTML += "<td valign=\"top\">" + this.sChoixB[i] + "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
                    
                    filteredChoice = this.sChoixB[i];
                    strToEvaluate = filteredChoice;
                    filteredChoice = evalStringForLexique(strToEvaluate);
                    
                    feedbackHTML += "<td valign=\"top\">" + filteredChoice + "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
                }else{
                    if (this.arrSrcMediaCategoryB[i] == 1){
                        imgMediaFolder = this.quiz.mediasFolder;
                    }
                    else if(this.arrSrcMediaCategoryB[i] == 2){
                        imgMediaFolder = '';
                    }
                
                    feedbackHTML += "<td valign=\"top\">";
                    var temp = new Element('div');
                    temp.appendChild(nq4_buildImageObject(this.sChoixB[i],110,145,imgMediaFolder));
                    feedbackHTML += temp.innerHTML;
                        
                    feedbackHTML += "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
                }

                if (this.retroactionPos[i] != '') {
                    feedbackHTML += "<tr><td style=\"width: 20px\"><td colspan=\"2\" class=\"sepRetro1\"><span class=\"small\">" + this.retroactionPos[i] + "</span></td></tr>";
                }
                
                feedbackHTML += "<tr><td class=\"feedbackLineSpacer\" colspan=\"3\">&nbsp;</td></tr>";
                feedbackHTML += "</tr>";
            }
        }
        
        
        feedbackHTML += "<tr><td class=\"feedbackLineSpacer\" colspan=\"2\">&nbsp;</td></tr>";
        feedbackHTML += "</table>";
        
        this.currentScore = (goodAnswerCount / this.bFound.length) * this.ponderation;
        
        this.status = this.quiz.statusToRedo;
        
        
        if (goodAnswerCount == this.bFound.length){
            if(this.currentScore == this.ponderation){
                feedbackHTML = '<span class="Green">' + this.page.goodAnswerLabel + '</span><br /><br />' + feedbackHTML;
                this.status = this.quiz.statusCompleted;
                this.statusMenuPages = 1;
            }
        }else{
            feedbackHTML = '<span class="Yellow">' + this.page.incompleteAnswerLabel + '</span><br /><br />' + feedbackHTML;
            this.statusMenuPages = -1;
        }
        
        
        setFeedback(feedbackHTML);
        openFeedback();
        
        return currentScore;
    },
    
    showSolution: function(){
        var feedbackHTML = "";
        var sEti = "";
        var iNbCol = 3;
        var imgMediaFolder;
        var strToEvaluate;
        var filteredChoice;
        
        top.ccdmd.nq4.recalculerDimensionsDamier = true;
        
        feedbackHTML =  this.quiz.solutionLabel + "<br /><br />";
        feedbackHTML += "<table class=\"feedbackTable\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>\n";

        for(var i = 0;i < this.sChoixA.length;i++){
            if(this.iType == this.TEXTE_TEXTE || this.iType == this.TEXTE_IMAGE){
                // feedbackHTML += "<td valign=\"top\">" + this.sChoixA[i] + "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
                
                filteredChoice = this.sChoixA[i];
                strToEvaluate = filteredChoice;
                filteredChoice = evalStringForLexique(strToEvaluate);
                
                feedbackHTML += "<td valign=\"top\">" + filteredChoice + "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
            }else{
                if (this.arrSrcMediaCategoryA[i] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategoryA[i] == 2){
                    imgMediaFolder = '';
                }
            
                feedbackHTML += "<td valign=\"top\">";
                var temp = new Element('div');
                temp.appendChild(nq4_buildImageObject(this.sChoixA[i],110,145,imgMediaFolder)); //115, 115
                feedbackHTML += temp.innerHTML;
                
                feedbackHTML += "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
            }

            if(this.iType == this.TEXTE_TEXTE || this.iType == this.IMAGE_TEXTE){
                // feedbackHTML += "<td valign=\"top\">" + this.sChoixB[i] + "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
                
                filteredChoice = this.sChoixB[i];
                strToEvaluate = filteredChoice;
                filteredChoice = evalStringForLexique(strToEvaluate);
                
                feedbackHTML += "<td valign=\"top\">" + filteredChoice + "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
            }else{
                if (this.arrSrcMediaCategoryB[i] == 1){
                    imgMediaFolder = this.quiz.mediasFolder;
                }
                else if(this.arrSrcMediaCategoryB[i] == 2){
                    imgMediaFolder = '';
                }
            
                feedbackHTML += "<td valign=\"top\">";
                var temp = new Element('div');
                temp.appendChild(nq4_buildImageObject(this.sChoixB[i],110,145,imgMediaFolder));
                feedbackHTML += temp.innerHTML;
                
                feedbackHTML += "<img src=\"images/spacer.gif\" width=\"25\" height=\"10\"></td>";
            }
            
            feedbackHTML += "</tr>";
            feedbackHTML += "<tr><td class=\"feedbackLineSpacer\">&nbsp;</td></tr>";
        }
        
        feedbackHTML += "</table>";
        
        setFeedback(feedbackHTML);
        openFeedback();
    },
    
    setWHImgSolution: function(iNewWidth,iNewHeight,src){
        jQuery("table.feedbackTable img").each(function() {
            var srcImg = jQuery(this).attr("src");

            if (srcImg == src) {            
               jQuery(this).attr("width", iNewWidth);
               jQuery(this).attr("height", iNewHeight);
            }
        });
    },
    
    redo: function(){
        for(var i = 0;i < this.bFound.length;i++){
            this.bFound[i] = false;
        }
        
        this.checkerBoard.redo();
        
        this.currentScore = 0;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;

        closeFeedback();
        $('question').update();
        this.display();
    },

    redoQuiz: function(){
        for(var i = 0;i < this.bFound.length;i++){
            this.bFound[i] = false;
        }
        
        this.checkerBoard.redo();
        
        this.currentScore = 0;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;

        closeFeedback();
        $('question').update();
        this.display();
    },
    
    addChoice: function(sCA, sCB, sLogo, retroactionPos, mediaCategoryA, mediaCategoryB, backMediaCategory){
        var iLastIndex = this.sChoixA.length;
        
        this.sChoixA[iLastIndex] = sCA;
        this.sChoixB[iLastIndex] = sCB;
        this.sLogo[iLastIndex] = sLogo;
        this.retroactionPos[iLastIndex] = retroactionPos;
        this.bFound[iLastIndex] = false;
        
        var aType = ((this.iType == this.TEXTE_TEXTE || this.iType == this.TEXTE_IMAGE ) ? this.checkerBoard.TEXT : this.checkerBoard.IMAGE);
        var bType = ((this.iType == this.TEXTE_TEXTE || this.iType == this.IMAGE_TEXTE ) ? this.checkerBoard.TEXT : this.checkerBoard.IMAGE);

        if (aType == 1){
            if (mediaCategoryA == 1){
                sCA = this.quiz.mediasFolder + '/' + sCA;
            }
            this.quiz.imgPreloader.addImage(sCA);
        }

        if (bType == 1){
            if (mediaCategoryB == 1){
                sCB = this.quiz.mediasFolder + '/' + sCB;
            }
            this.quiz.imgPreloader.addImage(sCB);
        }


        this.checkerBoard.addPair(sCA,aType,sCB,bType,iLastIndex,sLogo,mediaCategoryA,mediaCategoryB,backMediaCategory);
        
        
        if (backMediaCategory == 1) {
            sLogo = this.quiz.mediasFolder + '/' + sLogo;
        }
        this.quiz.imgPreloader.addImage(sLogo);
        
        this.arrSrcMediaCategoryA[this.arrSrcMediaCategoryA.length] = mediaCategoryA;
        this.arrSrcMediaCategoryB[this.arrSrcMediaCategoryB.length] = mediaCategoryB;
        
        
        strToEvaluate = this.retroactionPos[iLastIndex];
        this.retroactionPos[iLastIndex] = evalStringForLexique(strToEvaluate);
    },
    
    setBrotherhoodFound: function(i){
        this.bFound[i] = true;
    },
    
    setAffichage: function(i){
        this.iAffichage = i;
        
        this.checkerBoard.iDisplayType = (i == 0 ? this.checkerBoard.VISIBLE : this.checkerBoard.HIDDEN);//0 = NON-MASQUE
    },
    
    setType: function(i){
        this.iType = i;
    },
    
    setFeedback: function(s){
        this.feedback = s;
    },
    
    setBkgA: function(s){
        this.sBkgA = s;
        this.checkerBoard.sCell1BkgColor = '#' + s;
    },
    
    setBkgB: function(s){
        this.sBkgB = s;
        this.checkerBoard.sCell2BkgColor = '#' + s;
    },
    
    shuffle: function(){
        this.iChoixOrder = getShuffledOrder(this.sChoixA.length * 2);
    },
    
    getConsigne: function(){
        return this.quiz.consigneCheckerBoard;
    }
});