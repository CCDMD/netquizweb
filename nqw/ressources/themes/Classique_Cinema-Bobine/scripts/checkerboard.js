function CheckerBoard(iObjectID){
    this.iObjectID = iObjectID;

    //Constants
    this.TEXT = 0;
    this.IMAGE = 1;
    
    this.VISIBLE = 0;
    this.HIDDEN = 1;
    
    //System settings
    this.iTablePadding = 5;
    this.iTableSpacing = 5;
    this.iMaxNbCellByRow = 4;
    this.iWidthAvail = 560; //**POUR LE WIDTH DES CELLULES 608 = cellules de "132x132". Doit etre 120x120 pour cellule de 132 x 132 pcq padding + borders
    this.iFlipBackTime = 1000;
    this.sCellIDPrefix = 'CBCell';
    
    //Client settings
    this.iDisplayType = this.VISIBLE;
    this.sCellBorderStyleNorm = 'solid 1px #c0c0c0';
    this.sCellBorderStyleSel = 'solid 1px black';
    this.sCell1BkgColor = '#ffffff';
    this.sCell2BkgColor = '#cccccc';
    this.sCellClosedBkgColor = '#ffffff';
    this.owner = null;
    
    //Variables
    this.iCellSize = 126; //114 = ok
    this.bFirstDisplay = true;
    this.iTimer = null;
    this.oChoices = new Array();
    this.iChoicesPos = new Array();
    this.iCurrChoiceSel = -1;
    this.oCells = null;
    
    this.display = function(oContainerElement){
        if(this.bFirstDisplay){
            this.bFirstDisplay = false;
            
            this.shuffle();
        }
        
        var oTable = cb_buildHTMLElement('table',{cellpadding:0,cellspacing:this.iTableSpacing,border:'0'});
        var iIndex = 0;
        var iCellIndex = 0;
        var iRowIndex = 0;
        var oCurrRow = null;
        var oCurrCell = null;
        this.oCells = new Array(this.oChoices.length);


        var browser = navigator.appName;

        while(iIndex < this.oChoices.length){
            //Create new row if MaxNbCellByRow reached (or first row)
            if((iIndex % this.iMaxNbCellByRow) == 0){
                oCurrRow = oTable.insertRow(iRowIndex);
                iRowIndex++;
                iCellIndex = 0;
            }
            
            oCurrCell = oCurrRow.insertCell(iCellIndex);

            if (browser.indexOf('Microsoft') > -1){
                oCurrCell.width = this.iCellSize - ((this.iTablePadding * 2) + 2);
                oCurrCell.height = this.iCellSize - ((this.iTablePadding * 2) + 2);
            }
            else{
                oCurrCell.width = this.iCellSize - ((this.iTablePadding * 2) + 2);
                oCurrCell.height = this.iCellSize;
            }


            oCurrCell.style.paddingTop = this.iTablePadding + 'px';
            oCurrCell.style.paddingLeft = this.iTablePadding + 'px';
            oCurrCell.style.paddingRight = this.iTablePadding + 'px';
            oCurrCell.style.paddingBottom = this.iTablePadding + 'px';

            oCurrCell.valign = 'middle';
            oCurrCell.align = 'center';
            oCurrCell.style.border = this.sCellBorderStyleNorm;
            oCurrCell.style.backgroundColor = this.oChoices[this.iChoicesPos[iIndex]].sBkgColor;
            oCurrCell.style.cursor = 'pointer';
            oCurrCell.id = this.sCellIDPrefix + this.iChoicesPos[iIndex];
            oCurrCell.onclick = new Function("top.oCheckerBoards[" + this.iObjectID + "].onCellClicked(this);");
            this.oCells[this.iChoicesPos[iIndex]] = oCurrCell;
            
            if((this.iDisplayType == this.VISIBLE && !this.oChoices[this.iChoicesPos[iIndex]].bPairFound) || (this.iDisplayType == this.HIDDEN && this.oChoices[this.iChoicesPos[iIndex]].bPairFound)){
                this.flipOpen(this.iChoicesPos[iIndex]);
            }else{
                this.flipClosed(this.iChoicesPos[iIndex]);
            }
            
            iIndex++;
            iCellIndex++;
        }


        oContainerElement.appendChild(oTable);
        
    }
    
    this.addPair = function(sChoice1,iChoice1Type,sChoice2,iChoice2Type,iBrotherHoodID,sBackImage,mediaCategory1,mediaCategory2,backMediaCategory){
        this.oChoices[this.oChoices.length] = new cb_Choice(sChoice1,iChoice1Type,this.oChoices.length + 1,false,this.sCell1BkgColor,iBrotherHoodID,sBackImage);
        this.oChoices[this.oChoices.length] = new cb_Choice(sChoice2,iChoice2Type,this.oChoices.length - 1,false,this.sCell2BkgColor,iBrotherHoodID,sBackImage);
        
        this.owner.arrSrcMediaCategory[this.owner.arrSrcMediaCategory.length] = mediaCategory1;
        this.owner.arrSrcMediaCategory[this.owner.arrSrcMediaCategory.length] = mediaCategory2;
        this.owner.arrSrcMediaCategoryBack[this.owner.arrSrcMediaCategoryBack.length] = backMediaCategory; //need twice for pair combination
        this.owner.arrSrcMediaCategoryBack[this.owner.arrSrcMediaCategoryBack.length] = backMediaCategory;
    }
    
    this.shuffle = function(){
        this.iChoicesPos = cb_getShuffledOrder(this.oChoices.length);
    }
    
    this.redo = function(){
        for($i = 0;$i < this.oChoices.length;$i++){
            this.oChoices[$i].bPairFound = false;
        }
    }
    
    this.flipOpen = function(iChoiceID){
        this.oCells[iChoiceID].innerHTML = '';
        if(this.oChoices[iChoiceID].iType == this.TEXT){
            this.oCells[iChoiceID].innerHTML = this.oChoices[iChoiceID].sText;
        }else{
            var mediaMediaFolder = '';
            var theCellSizeO = this.iCellSize - ((this.iTablePadding * 2) + 2);
            this.oCells[iChoiceID].appendChild(cb_buildImageObject(this.oChoices[iChoiceID].sText,theCellSizeO,theCellSizeO,mediaMediaFolder));
        }
        this.oCells[iChoiceID].style.border = this.sCellBorderStyleNorm;
        this.oCells[iChoiceID].style.backgroundColor = this.oChoices[iChoiceID].sBkgColor;
    }
    
    this.flipClosed = function(iChoiceID){
        var mediaMediaFolder;

        if (this.owner.arrSrcMediaCategoryBack[iChoiceID] == 1){
            mediaMediaFolder = top.ccdmd.nq4.mediasFolder;
        }
        else if(this.owner.arrSrcMediaCategoryBack[iChoiceID] == 2){
            mediaMediaFolder = '';
        }
    
        var theCellSizeC = this.iCellSize - ((this.iTablePadding * 2) + 2);

        this.oCells[iChoiceID].innerHTML = '';
        this.oCells[iChoiceID].appendChild(cb_buildImageObject(this.oChoices[iChoiceID].sBackImage,theCellSizeC,theCellSizeC,mediaMediaFolder));
        this.oCells[iChoiceID].style.border = this.sCellBorderStyleNorm;
        this.oCells[iChoiceID].style.backgroundColor = this.sCellClosedBkgColor;
    }
    
    this.onCellClicked = function(oCell){
        iChoiceID = parseInt(oCell.id.substring(this.sCellIDPrefix.length));
        
        if(this.oChoices[iChoiceID].bPairFound || this.iTimer != null)return;
        
        if(this.iDisplayType == this.VISIBLE){
            if(this.iCurrChoiceSel > -1){
                if(this.oChoices[iChoiceID].iPairID == this.iCurrChoiceSel){
                    this.oChoices[iChoiceID].bPairFound = true;
                    this.oChoices[this.iCurrChoiceSel].bPairFound = true;
                    this.flipClosed(iChoiceID);
                    this.flipClosed(this.iCurrChoiceSel);
                    this.owner.setBrotherhoodFound(this.oChoices[this.iCurrChoiceSel].iBrotherHoodID);
                    
                    this.iCurrChoiceSel = -1;
                }else{
                    this.oCells[this.iCurrChoiceSel].style.border = this.sCellBorderStyleNorm;
                
                    this.iCurrChoiceSel = -1;
                }
            }else{
                this.iCurrChoiceSel = iChoiceID;
                oCell.style.border = this.sCellBorderStyleSel;
            }
        }else{
            if(this.iCurrChoiceSel > -1){
                if(this.oChoices[iChoiceID].iPairID == this.iCurrChoiceSel){
                    this.oChoices[iChoiceID].bPairFound = true;
                    this.oChoices[this.iCurrChoiceSel].bPairFound = true;
                    this.flipOpen(iChoiceID);
                    this.owner.setBrotherhoodFound(this.oChoices[this.iCurrChoiceSel].iBrotherHoodID);
                    
                    this.iCurrChoiceSel = -1;
                }else{
                    this.flipOpen(iChoiceID);
                
                    this.iTimer = setTimeout("top.oCheckerBoards[" + this.iObjectID + "].flipBack(" + iChoiceID + "," + this.iCurrChoiceSel + ");",this.iFlipBackTime);
                    this.iCurrChoiceSel = -1;
                }
            }else{
                this.iCurrChoiceSel = iChoiceID;
                this.flipOpen(iChoiceID);
            }
        }
    }
    
    this.flipBack = function(iChoiceID1, iChoiceID2){
        this.flipClosed(iChoiceID1);
        this.flipClosed(iChoiceID2);
        this.iCurrChoiceSel = -1;
        clearTimeout(this.iTimer);
        this.iTimer = null;
    }
    
    this.onPairFound = null;
    
}

function cb_Choice(sText,iType,iPairID,bPairFound,sBkgColor,iBrotherHoodID,sBackImage){
    this.sText = sText;
    this.iType = iType;
    this.iPairID = iPairID;
    this.bPairFound = bPairFound;
    this.sBkgColor = sBkgColor;
    this.iBrotherHoodID = iBrotherHoodID;
    this.sBackImage = sBackImage;
}

function getNewCheckerBoard(){
    if(!top.oCheckerBoards) top.oCheckerBoards = new Array();
    var iNewID = top.oCheckerBoards.length
    top.oCheckerBoards[iNewID] = new CheckerBoard(iNewID);
    return top.oCheckerBoards[iNewID];
}

function cb_buildHTMLElement(sTagName,aAtts){
    var oElement = null;
    var oElementDimensionsAreOk = true;
    
    var pic_real_width, pic_real_height;
    var iNewWidth, iNewHeight;
    
    try{
        var sElementHTML = '<' + sTagName;
        for(var sAtt in aAtts){
            if(aAtts[sAtt] || typeof aAtts[sAtt] != 'object'){
                if (sTagName == 'img') {
                   if (sAtt == 'width' || sAtt == 'height') {
                      if (aAtts[sAtt] <= 0) {
                          oElementDimensionsAreOk = false;
                      }
                   }
                }
                else {
                    oElementDimensionsAreOk = true;
                }
                
                sElementHTML += ' ' + sAtt + '=\"' + aAtts[sAtt] + '\"';
            }
        }
        sElementHTML += ">";
        
        oElement = document.createElement(sElementHTML);
    }catch(e){
        oElement = document.createElement(sTagName.toUpperCase());
        
        for(var sAtt in aAtts){
            if(aAtts[sAtt] || typeof aAtts[sAtt] != 'object'){
                if (sTagName == 'img') {
                   if (sAtt == 'width' || sAtt == 'height') {
                      if (aAtts[sAtt] <= 0) {
                          oElementDimensionsAreOk = false;
                      }
                   }
                }
                else {
                    oElementDimensionsAreOk = true;
                }    

                oElement.setAttribute(sAtt,aAtts[sAtt]);   
            }
        }
    }
    
    if (oElementDimensionsAreOk == false) {
        // "Bogue" Chrome / Safari : parfois, on ne voit pas l'image car les dimensions (width et height) retournés sont 0.
        // Suspecte le cache qui ne peut pas lire adéquatement l'image dans nq4_buildImageObject...
        // Pour informations : http://stackoverflow.com/questions/318630/get-real-image-width-and-height-with-javascript-in-safari-chrome
    
        var img = oElement;
    
        jQuery("<img/>") // Make in memory copy of image to avoid css issues
            .attr("src", jQuery(img).attr("src"))
                .load(function() {
                    pic_real_width = this.width;   // Note: $(this).width() will not
                    pic_real_height = this.height; // work for in memory images.
                    
                    var datamw = jQuery(img).attr("datamw");
                    var datamh = jQuery(img).attr("datamh");
                    
                    if(datamw && pic_real_width > datamw){
                        iNewWidth = datamw;
                        iNewHeight = nq4_b2(pic_real_width,pic_real_height,datamw);
                    }else{
                        iNewWidth = pic_real_width;
                        iNewHeight = pic_real_height;
                    }
                
                    if(datamh && iNewHeight > datamh){
                        iNewWidth = nq4_b2(iNewHeight,iNewWidth,datamh);
                        iNewHeight = datamh;
                    }
                
                    iNewWidth = Math.round(iNewWidth);
                    iNewHeight = Math.round(iNewHeight);
                    
                    jQuery(img).attr("width", iNewWidth);
                    jQuery(img).attr("height", iNewHeight);
                });
    }
    
    return oElement;
}

function cb_getShuffledOrder(N) {
    var J, K, Q = new Array(N);
    for (J = 0; J < N; J++) {
        K = cb_Random(J + 1);
        Q[J] = Q[K];
        Q[K] = J;
    }
    return Q;
}
function cb_Random(N) {
    return Math.floor(N * (Math.random() % 1));
}
function cb_buildImageObject(sFileName,iMaxWidth,iMaxHeight,sImgFolder){
    var iNewWidth = 0;
    var iNewHeight = 0;
    var sInnerHTML = "";
    var sImgSrc;
    
    if (sImgFolder != '') {
        sImgSrc = sImgFolder + '/' + sFileName;
    }
    else {
        sImgSrc = sFileName;
    }
    
    var oImg = new Image();
    oImg.src = sImgSrc;

    
    if(oImg.width > iMaxWidth){
        iNewWidth = iMaxWidth;
        iNewHeight = cb_b2(oImg.width,oImg.height,iMaxWidth);
    }else{
        iNewWidth = oImg.width;
        iNewHeight = oImg.height;
    }
    
    if(iNewHeight > iMaxHeight){
        iNewWidth = cb_b2(iNewHeight,iNewWidth,iMaxHeight);
        iNewHeight = iMaxHeight;
    }
    
    iNewWidth = Math.round(iNewWidth);
    iNewHeight = Math.round(iNewHeight);

    //return cb_buildHTMLElement('img',{src:sImgSrc, width:iNewWidth, height:iNewHeight});
    return cb_buildHTMLElement('img',{src:sImgSrc, width:iNewWidth, height:iNewHeight, datamw:iMaxWidth, datamh:iMaxHeight});
}

function cb_b2(a1,a2,b1){
    return (a2 * b1) / a1;
}