var Netquiz = Class.create({
    currentPageIndex:0,
    participantId: -1,
    showingResult: false,
    resultNbPagesBFScroll:7,
    imgPreloader: null,
    startingQuiz: true,
    firstLoad: false,
    numberPagesQuiz: 0,
    initializedSequence: false,
    isPageSection: false,
    questionSequence: new Array(),
    indexShuffleStart: new Array(),
    indexShuffleEnd: new Array(),
    pageAdded: new Array(),
    pageInitVal: new Array(),
    numberOfQuestions: null,
    
    //Dictee
    sMsgMotsMOrtho: "",
    sMsgMotsManq: "",
    sMsgMotsTrop: "",
  
    //msgs
    msgQuizNotExist: null,
    msgQuizInactive: null,
    msgKeyNotExist: null,
    msgKeyUsed: null,
    msgGeneralError: null,
    msgUnonload: null,
    msgRepeatNumEtudiant: null,
    msgModuleError: null,
    msgAddressModuleError: null,
    msgEmailOk: null, 
    msgEmailError: null, 
    
    //Labels
    pageLabel: null,
    solutionLabel: null,
    suggestionLabel: null,
    msgRestartQuiz: null,
    msgRestartQuizWelcome: null,
    removeHiliteLabel: null,
    point: null,
    points: null,
    strObjet: null, 
    
    //Status
    statusToDo: '',
    statusToRedo: '',
    statusCompleted: '',
    
    //Nav Bar
    navbResult: null,
    navbLexique: null,
    navbRedo: null,
    navbSolution: null,
    navbValidate: null,
    navBarOf: 'de',
    
    //Result
    resultHeaderCol0: '',
    resultHeaderCol1: '',
    resultHeaderCol2: '',
    resultHeaderCol3: '',
    resultHeaderCol4: '',
    
    resultIdentTitle: '',
    resultButtonRedo: '',
    resultButtonSendTo: '',
    resultButtonPrint: '',
    resultButtonOK: '',
    resultButtonCancel: '',
    resultNoValue: '',
    
    rifLblLastName: '',
    rifLblName: '',
    rifLblCode: '',
    rifLblGroup: '',
    rifLblEmail: '',
    rifLblOther: '',
    rifLblTitleSendTo: '',
    
    resultHTMLVersion: '',
    sendtoHTMLVersion: '',
    
    subjectMail: '',
    resultMailTop1: '',
    resultMailTop2: '',
    resultMail: '',
    
    //Settings
    mode:0, /*0 = Preview/Formatif, 1= Formatif w/ submit, 2= Sommatif */
    urlServer: null,
    quizId: null,    
    showNavTextBox: true,
    resultPageEnabled: true,    
    decimalSymbol: 0,
    mediasFolder: 'medias',    
    quizTitle: '',

    //Timer (each page)
    answerTimerEnabled: false,
    pageTimer: new Array(),
    secs: null,
    timerID: null,
    timerRunning: false,
    delay: 1000,
    
    //user info
    userLastName: '',
    userName: '',
    userCode: '',
    userGroup: '',
    userEmail: '',
    userSendTo: '',
    userOther: '',
    
    //other
    strNoObject: '',
    strSendEmail: '',
    recalculerDimensionsAssRank: new Array(),
    recalculerDimensionsDamier: false, 

    //pour zoom image
    imagePathHS: '',
    imgMaxWidth: 550,
    imgFeedbackMaxWidth: 150,
    
    //pour jwplayer
    jwp_width: 480,         
    jwp_audiowidth: 400,
    jwp_maxwidth: 480,      
    jwp_height: 270,        
    jwp_audioheight: 30,
    
    //pour lexique
    lexiqueImgMaxWidth: 400,
    lexiqueImgMaxHeight: 300,
    lexiquePageImg: new Array(), 

    
    //Construct
    initialize: function(){                      		    						    	
                //Hide pour pas avoir de glitch au load du quiz si video dans premiere page
                $('contentwrapper').hide();
                $('btnBackDisabled').hide();
                $('btnNextDisabled').hide();
                $('indice').hide();
                $('resultIdentForm').hide();
                $('sendtoForm').hide();
                $('scrollwrapper').hide();
    
                this.pages = new Array();
                this.imgPreloader = getImgPreloader();        
                                                                               
                if (self == top) {
                	// Le quiz n'est pas dans un iframe.                
                }
                else {
                	// Le quiz est dans un iframe.
                	jQuery("body").css('background', '#FFFFFF');
                	jQuery("body").css('overflow-x', 'hidden');
                	jQuery("#pagewrapper").removeClass('pagewrapper');
                	jQuery("#wrapperallcontentwrapper").css('box-shadow', 'none');
                	jQuery("#wrapperallcontentwrapper").css('margin-left', '0');
									jQuery("#wrapperallcontentwrapper").css('margin-right', '0');
                	jQuery("#allcontentwrapper").css('margin-left', '0');
                	jQuery("#allcontentwrapper").css('margin-right', '0');
                }
    },
    
    //Init
    init: function(){    				    	
            if (this.numberPagesQuiz != '0') {      
                    if (isMobile.Android() == true){
                         jQuery("#pagewrapper").removeClass("pagewrapper");
                    }
    
                    //Show apres les hide dans initialize
                    $('contentwrapper').show();
    
                    this.solutionLabel = '<b>' + this.solutionLabel + '</b>';
                    this.suggestionLabel = '<b>' + this.suggestionLabel + '</b>';
    
                    var newPageSequence = new Array();
    
                    for(i = 0;i < this.questionSequence.length;i++){    
                            this.pageInitVal[i] = this.pages[i];
    
                            for(j = 0;j < this.questionSequence.length;j++){
                                    if (j == this.questionSequence[i]){
                                            newPageSequence[newPageSequence.length] = this.pages[j];
                                    }
                            }
                    }
    
                    for(i = 0;i < newPageSequence.length;i++){
                            this.pages[i] = newPageSequence[i];
                    }
    
                    for(i = 0;i <= this.numberPagesQuiz - 1;i++){
                            this.pageTimer[i] = 0;
                    }
    
                    if (this.numberOfQuestions){
                            for(i = 0;i < this.questionSequence.length;i++){
                                    if (i >= this.numberOfQuestions){
                                            this.pages[i] = null;
                                    }
                            }
                            this.pages.length = this.numberOfQuestions;
                    }
    
                    //Result
                    $('resultHeaderCol0WOT').update(this.resultHeaderCol0);
                    $('resultHeaderCol1WOT').update(this.resultHeaderCol1);
                    $('resultHeaderCol2WOT').update(this.resultHeaderCol3);
                    $('resultHeaderCol3WOT').update(this.resultHeaderCol4);
                    
                    $('resultHeaderCol0WT').update('<div id="resultHeaderCol0WTContent" class="resultHeaderContent">' + this.resultHeaderCol0 + '</div>');
                    $('resultHeaderCol1WT').update('<div id="resultHeaderCol1WTContent" class="resultHeaderContent">' + this.resultHeaderCol1 + '</div>');
                    $('resultHeaderCol2WT').update('<div id="resultHeaderCol2WTContent" class="resultHeaderContent">' + this.resultHeaderCol2 + '</div>');
                    $('resultHeaderCol3WT').update('<div id="resultHeaderCol3WTContent" class="resultHeaderContent">' + this.resultHeaderCol3 + '</div>');
                    $('resultHeaderCol4WT').update('<div id="resultHeaderCol4WTContent" class="resultHeaderContent">' + this.resultHeaderCol4 + '</div>');
                    
                    $('resultIdentFormHeader').update(this.resultIdentTitle);
                    $('resultButtonRedo').update(this.resultButtonRedo);
                    $('resultButtonSendTo').update(this.resultButtonSendTo);
                    $('resultButtonPrint').update(this.resultButtonPrint);
                    $('resultButtonOK').update(this.resultButtonOK);
                    $('resultButtonCancel').update(this.resultButtonCancel);
                    
                    $('rifLblLastName').update(this.rifLblLastName);
                    $('rifLblName').update(this.rifLblName);
                    $('rifLblCode').update(this.rifLblCode);
                    $('rifLblGroup').update(this.rifLblGroup);
                    $('rifLblEmail').update(this.rifLblEmail);
                    $('rifLblOther').update(this.rifLblOther);
                    
                    
                    $('sendtoFormHeader').update(this.rifLblTitleSendTo);
                    $('sendtoButtonOK').update(this.resultButtonOK);
                    $('sendtoButtonCancel').update(this.resultButtonCancel);
                    
                    $('rifLblLastNameST').update(this.rifLblLastName);
                    $('rifLblNameST').update(this.rifLblName);
                    $('rifLblCodeST').update(this.rifLblCode);
                    $('rifLblGroupST').update(this.rifLblGroup);
                    $('rifLblEmailST').update(this.rifLblEmail);
                    $('rifLblEmailDestST').update(this.rifLblSendTo);
                    $('rifLblOtherST').update(this.rifLblOther);
                                                                                
                    //Netquiz Nav bar
                    if(this.navbResult)
                            $$('#navbResult a')[0].update(this.navbResult);
                    
                     if(this.navbLexique)
                            $$('#navbLexique')[0].update('<a href="javascript:top.ccdmd.nq4.pageGotoLexique();">' + this.navbLexique + '</a>');
                    
                    if(this.navbRedo)
                            $$('#navbRedo')[0].update('<a href="javascript:top.ccdmd.nq4.redo();">' + this.navbRedo + '</a>');
                    
                    if(this.navbSolution)
                            $$('#navbSolution')[0].update('<a href="javascript:top.ccdmd.nq4.showSolution();">' + this.navbSolution + '</a>');
                    
                    if(this.navbValidate)
                            $$('#navbValidate')[0].update('<a href="javascript:top.ccdmd.nq4.validate();">' + this.navbValidate + '</a>');
                    
                    $$('#pagenavbIndice a')[0].update(this.pages[this.currentPageIndex].indiceTag);
                    $$('#pagenavbSource a')[0].update(this.pages[this.currentPageIndex].sourceTag);
    
                    $('pageLabel').update(this.pageLabel);
    
                    var navBarOfStr = '&nbsp;' + this.navBarOf + '&nbsp;';
                    $('navBarOf').update(navBarOfStr);
                    
                    if(this.showNavTextBox)
                            $('navBarPageIndex').hide();
                    else
                            $('navBarTxtPageIndex').hide();
                    
                    if(this.resultPageEnabled) {                                                         
                            if (lexiqueObj == undefined) {		
                            	 // Pas de lexique
                            	 $('navBarPageCount').update(this.pages.length + 1);
                            }      
                            else {
                            		$('navBarPageCount').update(this.pages.length + 2);                            		
                            }
                    }
                    else {
                    				if (lexiqueObj == undefined) {		
                            	// Pas de lexique
                            	$('navBarPageCount').update(this.pages.length);
                            }      
                            else {
                            		$('navBarPageCount').update(this.pages.length + 1);                            		
                            }                    	                            
                    }
                    
                    
                    jQuery(window).scroll(function() {  
											var scrollTop = jQuery(window).scrollTop();  
											
											if (scrollTop > 15) {
												jQuery('#contentpageup').fadeIn();				
											}
											else {
												jQuery('#contentpageup').fadeOut();						
											}
											
											ajusterWrapperAll();
										});
																
                    
                    $('username').update('&nbsp;');
                    updateWrappersSize();
                    
                    this.imgPreloader.onFinish = nq4_onImgsPreloadFinish;
                    this.imgPreloader.preload();
                    
                    if (isMobile.any() == true) {
												jQuery(".sticky_header").css('position', 'absolute');                     	 
                     	 /*window.onscroll = function() {           
                     	 	 // http://stackoverflow.com/questions/4889601/css-position-fixed-into-ipad-iphone
                     	 	 mobileScroll();
                     	 } */                    	                    	                      	
										}
										                                     
        }        
        else {
            $('contentwrapper').show();                   
            jQuery("#wrapperallcontentwrapper").css('height', (jQuery(window).height()) + 'px');
            jQuery("#allcontentwrapper").css('height', (jQuery(window).height()) + 'px');   
        }
    },
    
    begin: function(){
        this.pageGoto(0);
        $('scrollwrapper').show();

        //Ass-rank temp "fix"
        if (this.pages[0].question){
            if (this.firstLoad == false){
                this.firstLoad = true;
                this.pageGoto(0);
                this.redo();
                if (this.pages[0].question.nbImages > 0) {                   
                   addHighSlideToOtherImages('page');
                   
                   var currentPage = top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex];
                   if (currentPage.question.sQuestionType == "ASSOCIATION") {
                      this.pageGoto(1);
                      this.pageGoto(0);
                   }
                }
            }
        }
        
        this.initPagesMenu();       
    },

    initShuffleQuestion: function(indexStart,indexEnd){
        if (this.initializedSequence == false){
            for(i = 0;i <= this.numberPagesQuiz - 1;i++){
                this.questionSequence[i] = i;
            }

            this.indexShuffleStart[this.indexShuffleStart.length] = indexStart;
            this.indexShuffleEnd[this.indexShuffleEnd.length] = indexEnd;

            this.initializedSequence = true;
        }

        var o = new Array();
        var arrayToSort = new Array();

        for(i = indexStart;i <= indexEnd;i++){
            arrayToSort[arrayToSort.length] = this.questionSequence[i];
        }

        o = arrayToSort;

        for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);

        arrayToSort = o;

        for(i = 0;i < arrayToSort.length;i++){
            this.questionSequence[indexStart + i] = arrayToSort[i];
        }
    },
    
    initShuffleSpecific: function(arrIndex){
        if (this.initializedSequence == false){
            for(i = 0;i <= this.numberPagesQuiz - 1;i++){
                this.questionSequence[i] = i;
            }
            this.initializedSequence = true;
        }
        
        var o = new Array();
        var questionArray = new Array();
        var indexQuestionArray = new Array();
        
        for(i = 0;i < this.questionSequence.length;i++){
            for(j = 0;j < arrIndex.length;j++){
                if (this.questionSequence[i] == arrIndex[j]) {
                    questionArray.push(this.questionSequence[i]);
                    indexQuestionArray.push(i);
                }
            }
        }
        
        o = questionArray;
        
        for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
        
        for(i = 0;i < o.length;i++){
            this.questionSequence[indexQuestionArray[i]] = o[i];
        }
    },
    
    initPagesMenu: function(){
    	  var currentSection = '';
    	  var menuPagesTypeQuestion = '';
    	  var htmlMenuPages = '';
    	  var openUlSection = false;
    	  var openedUl = false;
    	  
    	  var element = jQuery("#pageLabel");
        var position = element.position();
                
        var leftMenuPages; 
        var topMenuPages;
                                                                                        
        for(i = 0;i < this.pages.length;i++) {            	        	
            if (this.pages[i].question) {            	            	
            	switch (this.pages[i].question.sQuestionType)
            	{
            		case "ASSOCIATION":
            			menuPagesTypeQuestion = 'bg_ic_association';
            		break;
            		
            		case "CHOIX MULTIPLES":
            			menuPagesTypeQuestion = 'bg_ic_choix_multiple';
            		break;
            		
            		case "CLASSEMENT":
            			menuPagesTypeQuestion = 'bg_ic_classement';
            		break;
            		
            		case "DAMIER":
            			menuPagesTypeQuestion = 'bg_ic_damier';
            		break;
            		
            		case "DEVELOPPEMENT":
            			menuPagesTypeQuestion = 'bg_ic_developpement';
            		break;
            		
            		case "DICTEE":
            			menuPagesTypeQuestion = 'bg_ic_dictee';
            		break;
            		
            		case "MARQUAGE":
            			menuPagesTypeQuestion = 'bg_ic_marquage';
            		break;
            		
            		case "RANKING": // Mise en ordre, mais on a été utilisé le terme RANKING ailleurs...
            			menuPagesTypeQuestion = 'bg_ic_mise_ordre';
            		break;
            		
            		case "REPONSE BREVE":
            			menuPagesTypeQuestion = 'bg_ic_reponse_breve';
            		break;
            		
            		case "REPONSES MULTIPLES":
            			menuPagesTypeQuestion = 'bg_ic_reponses_multiples';
            		break;
            		
            		case "TEXTE LACUNAIRE":
            			menuPagesTypeQuestion = 'bg_ic_texte_lacunaire';
            		break;
            		
            		case "ZONE A IDENTIFIER":
            			menuPagesTypeQuestion = 'bg_ic_zone_identifier';
            		break;
            	}
            	
            	// Question
            	if (openUlSection == true) {
            		htmlMenuPages += '		<div id="div-list-menupages-' + i + '" class="div_in_list_menupages" data-page="' + i + '"><a href="#" onclick="top.ccdmd.nq4.pageGoto(' + i + '); return false;" class="link_to_page"><li class="li_menu_pages_question ' + menuPagesTypeQuestion + '">' + this.pages[i].itemTitle + '<img id="img-status-list-menupages-' + i + '" class="img_status_menupages" src="images/spacer.gif"></li></a></div>';
            	}
            	else {
            		menuPagesTypeQuestion = menuPagesTypeQuestion + "_alone";
            		
            		htmlMenuPages += '<ul class="ul_menu_pages_autres">';
            		htmlMenuPages += '		<div id="div-list-menupages-' + i + '" class="div_in_list_menupages" data-page="' + i + '"><a href="#" onclick="top.ccdmd.nq4.pageGoto(' + i + '); return false;" class="link_to_page"><li class="li_menu_pages_autres ' + menuPagesTypeQuestion + '">' + this.pages[i].itemTitle + '<img id="img-status-list-menupages-' + i + '" class="img_status_menupages" src="images/spacer.gif"></li></a></div>';
            		
            		openedUl = true;
            	}
            }
            else {            	            	
            	// Section            	
            	if (openUlSection == true) {
            		openUlSection = false;
            	}
            	
            	
            	if (currentSection != this.pages[i].title) {            		
            		if (currentSection == '') {            			            		            			
            			htmlMenuPages += '<ul class="ul_menu_pages_section">';
            			htmlMenuPages += '		<div class="div_in_list_menupages"><li class="li_menu_pages_titre_section bg_ic_section">' + this.pages[i].title + '</li></div>';
            			htmlMenuPages += '		<div id="div-list-menupages-' + i + '" class="div_in_list_menupages" data-page="' + i + '"><a href="#" onclick="top.ccdmd.nq4.pageGoto(' + i + '); return false;" class="link_to_section"><li class="li_menu_pages_question bg_ic_page">' + this.pages[i].itemTitle + '</li></a></div>';
            			
            			openUlSection = true;
            			openedUl = true;
            		}
            		else {
            			htmlMenuPages += '</ul>';
            			            			            			            			
            			htmlMenuPages += '<ul class="ul_menu_pages_section">';
            			htmlMenuPages += '		<div class="div_in_list_menupages"><li class="li_menu_pages_titre_section bg_ic_section">' + this.pages[i].title + '</li></div>';
            			htmlMenuPages += '		<div id="div-list-menupages-' + i + '" class="div_in_list_menupages" data-page="' + i + '"><a href="#" onclick="top.ccdmd.nq4.pageGoto(' + i + '); return false;" class="link_to_section"><li class="li_menu_pages_question bg_ic_page">' + this.pages[i].itemTitle + '</li></a></div>';

            			openUlSection = true;           
            			openedUl = true;
            		}
            		
            		currentSection = this.pages[i].title;            		
            	}            
            	else if (currentSection == '' && this.pages[i].title == '') {                      		            		
            		if (openedUl == true) {
            			htmlMenuPages += '</ul>';
            			
            			openedUl = false;
            		}
            		            		            		
           			htmlMenuPages += '<ul class="ul_menu_pages_autres">';           			
           			htmlMenuPages += '		<div id="div-list-menupages-' + i + '" class="div_in_list_menupages" data-page="' + i + '"><a href="#" onclick="top.ccdmd.nq4.pageGoto(' + i + '); return false;" class="link_to_autres"><li class="li_menu_pages_autres bg_ic_page_autre">' + this.pages[i].itemTitle + '</li></a></div>';
           			htmlMenuPages += '</ul>';	            			            		            		            		            		
            	}
            
            	
							if ((i + 1) == this.pages.length) {            	            	
								// Fin du questionnaire
								if (openUlSection == false) {
									htmlMenuPages += '</ul>';
								}
								else if (currentSection != '') {
									htmlMenuPages += '</ul>';
								}
							}            					        	
						}
        }
                
        if (this.resultPageEnabled) {                                                         
					htmlMenuPages += '<ul class="ul_menu_pages_autres">';
          htmlMenuPages += '		<div id="div-list-menupages-' + this.pages.length + '" class="div_in_list_menupages" data-page="' + i + '"><a href="#" onclick="top.ccdmd.nq4.pageGotoResult(); return false;" class="link_to_autres"><li class="li_menu_pages_autres bg_ic_page_autre">' + this.navbResult + '</li></a></div>';
          htmlMenuPages += '</ul>';
				}
			
				if (lexiqueObj == undefined) {		
					// Pas de lexique					
				}      
				else {
					htmlMenuPages += '<ul class="ul_menu_pages_autres">';
          htmlMenuPages += '		<div id="div-list-menupages-' + parseInt(this.pages.length + 1) + '" class="div_in_list_menupages" data-page="' + i + '"><a href="#" onclick="top.ccdmd.nq4.pageGotoLexique(); return false;" class="link_to_autres"><li class="li_menu_pages_autres bg_ic_page_autre">' + this.navbLexique + '</li></a></div>';
          htmlMenuPages += '</ul>';                  		
				}                   
        
				
        jQuery("#menupages").html(htmlMenuPages);
        
        jQuery(".div_in_list_menupages").removeClass('select_menupage');
        jQuery("#div-list-menupages-0").addClass('select_menupage');
                
                                                
        calculerDimensionsMenuPages();                        
                                                        
        new Draggable('menupageswrapper',{handle:'menupageshandle', scroll:window, zindex:1000, starteffect:effectFunction('menupageswrapper'), endeffect:effectFunction('menupageswrapper'), onEnd:menuPageEndDrag});       
    },

    restartQuiz: function(){
        var input_box = confirm(this.msgRestartQuiz);

        if (input_box == true){
           window.location.reload();
        }
    },
    
    restartQuizFromWelcome: function(){
        var input_box = confirm(this.msgRestartQuizWelcome);

        if (input_box == true){
           window.location.assign('index.html');
        }
    },
    
    connect: function(){
        
    },
    
    connectSuccess: function(transport){
      
    },
    
    auth: function(){
        
    },
    
    authSuccess: function(transport){
        var response = transport.responseJSON;
       
        var serverMsg = response.msg;
        switch(serverMsg){
            case 0:
                $('pagewrapperauth').hide();
                $('pagewrapperquiz').show();
                this.participantId = response.participantId;
                $('username').update(response.username);
                
                window.onbeforeunload = tcals_onunload;
                
                if(response.questionNb == -1)
                    this.pageGoto(0);
                else
                    this.resume(response.questionNb);
                break;
            case 1:
                $('msgErreur').update(this.msgKeyNotExist);
                break;
            case 2:
                $('msgErreur').update(this.msgKeyUsed);
                break;
            case 3:
                $('msgErreur').update(this.msgGeneralError);
                break;
        }
    },
    
    //Navbar
    newPage: function(){
        this.pages[this.pages.length] = new Page(this);
        return this.pages[this.pages.length - 1];
    },
    
    pageNext: function(){
    	 var pagesLength = this.pages.length;
    	
        if(this.resultPageEnabled){
        	if (lexiqueObj == undefined) {		
						 // Pas de lexique						 
					}      
					else {
						pagesLength = pagesLength + 1;
					}
        	
        	
        	if(this.currentPageIndex < pagesLength) {
                this.pageGoto(this.currentPageIndex + 1);
          }
        }else{
        	if (lexiqueObj == undefined) {		
						 // Pas de lexique		
						 pagesLength = pagesLength - 1;
					}      
															        	        	        	
        	if(this.currentPageIndex < pagesLength) {
                this.pageGoto(this.currentPageIndex + 1);
          }
        }
    },
    pageBack: function(){
        if(this.currentPageIndex > 0)
            this.pageGoto(this.currentPageIndex - 1);
    },
    pageGoto: function(pageIndex){    	  
        hs.close();            
        
        var pagesLength = this.pages.length;        
        
        if (lexiqueObj == undefined) {		
        	// Pas de lexique						 
				}      
				else {
					pagesLength = pagesLength + 1;					
				}
        
        if(pageIndex > -1 && pageIndex < this.pages.length){
            $('quizpage').style.height = '';
            $('feedbackcontent').innerHTML = '';     
                        
            if(!this.showingResult) {
            	if (this.startingQuiz == false) {
            		   if(this.currentPageIndex < this.pages.length){            		   	             		   	 
            		   	 this.savePage();
            		   }
              }  
              else {
                   this.startingQuiz = false;
              }
            }
             
            this.recalculerDimensionsAssRank = []; 
            this.recalculerDimensionsDamier = false;
            this.hideResultIdentForm();
            this.hideSendToForm();
            indiceWindow.close();
            showFeedback();
            closeFeedback();
            
            jQuery(".div_in_list_menupages").removeClass('select_menupage');
            jQuery("#div-list-menupages-" + pageIndex).addClass('select_menupage');
          
            this.displayPage(this.pages[pageIndex]);
            this.currentPageIndex = pageIndex;
            
            addHighSlideToOtherImages('page');                                           
        }else{        	
        	$('quizpage').style.height = '';
					$('feedbackcontent').innerHTML = ''; 
																																	
					this.recalculerDimensionsAssRank = [];
					this.recalculerDimensionsDamier = false;
					this.hideResultIdentForm();
					this.hideSendToForm();
					indiceWindow.close();
					closeFeedback();
					
					jQuery(".div_in_list_menupages").removeClass('select_menupage');
          jQuery("#div-list-menupages-" + pageIndex).addClass('select_menupage');
				  
					if(!this.showingResult && this.currentPageIndex < this.pages.length) {													
							this.savePage();						
					}
					
        	if (lexiqueObj == undefined) {		
        		// Pas de lexique					
        		if (pageIndex == pagesLength && this.resultPageEnabled){        			        			
        			this.displayResultPage();	
        		}        		
        	}
        	else {        		        		
        		if ((pageIndex == (pagesLength - 1)) && this.resultPageEnabled){        									
        			this.displayResultPage();	
        		}        		
        		else {
        			this.displayLexiquePage();
        		}
        	}
        	        	                                
          this.currentPageIndex = pageIndex;                    
        }
    },
    pageGotoResult: function(){    	      	     	
        if (this.resultPageEnabled){
            if(!this.showingResult && this.currentPageIndex < this.pages.length)
                    this.savePage();
                
            indiceWindow.close();
            closeFeedback();
            
            jQuery(".div_in_list_menupages").removeClass('select_menupage');
            jQuery("#div-list-menupages-" + this.pages.length).addClass('select_menupage');
            
            this.displayResultPage();
            this.currentPageIndex = this.pages.length;
            this.recalculerDimensionsAssRank = [];
            this.recalculerDimensionsDamier = false;                        
        }
    },
    pageGotoLexique: function(){    	    	     	        
				if(!this.showingResult && this.currentPageIndex < this.pages.length)
								this.savePage();
						
				indiceWindow.close();
				closeFeedback();
				
				jQuery(".div_in_list_menupages").removeClass('select_menupage');
        jQuery("#div-list-menupages-" + parseInt(this.pages.length + 1)).addClass('select_menupage');
            
				this.displayLexiquePage();
				this.currentPageIndex = this.pages.length + 1;
				this.recalculerDimensionsAssRank = [];
				this.recalculerDimensionsDamier = false;								        
    },
    resume: function(questionNb){
        var questionAt = 0;
        var thePage;

        for(i = 0;i < this.pages.length;i++){

              thePage = this.pages[i];

              if (thePage.question){
                 if (questionAt == questionNb){
                    this.pageGoto(i);
                    return;
                 }
                 else{
                     questionAt = questionAt + 1;
                 }
              }
        }
    },
    redo: function(){
        var currentPage = this.pages[this.currentPageIndex];

        jQuery(window).scrollTop(0);
        
        currentPage.redo();
        
        this.updateNavBar(currentPage);         
        
        jQuery("#img-status-list-menupages-" + this.currentPageIndex).attr('src', 'images/spacer.gif');
    },
    validate: function(){
        var currentPage = this.pages[this.currentPageIndex];
        
        currentPage.validate();
        this.updateNavBar(currentPage);        
        this.includeMediaFeedback();
                        
        resizeUnprocessedImagesFeedback();
        
        
        switch (currentPage.question.statusMenuPages)
        {
        	case -1:
        		// Incomplet ou à refaire        		
        		jQuery("#img-status-list-menupages-" + this.currentPageIndex).attr('src', 'images/spacer.gif');
        	break;
        	
        	case 0:
        		// Mauvaise réponse        		
        		jQuery("#img-status-list-menupages-" + this.currentPageIndex).attr('src', 'images/bullet_red.png');
        	break;
        	
        	case 1:
        		// Bonne réponse        		
        		jQuery("#img-status-list-menupages-" + this.currentPageIndex).attr('src', 'images/bullet_green.png');
        	break;
        }                
        
        var element = jQuery("#feedback");
        var position = element.position();
                                
        jQuery(window).scrollTop(position.top - jQuery("#header").height());        
        
    },
    showSolution: function(){
        var currentPage = this.pages[this.currentPageIndex];
        
        currentPage.showSolution();
                                
        resizeUnprocessedImagesFeedback();
        
        
        var element = jQuery("#feedback");
        var position = element.position();
                                
        jQuery(window).scrollTop(position.top - jQuery("#header").height());
    },
    
    showIndice: function(){
    	  var strToEvaluate;
    	  var lexiqueStr = this.pages[this.currentPageIndex].indice;
    	  
    	  // strToEvaluate = lexiqueStr;
    	  // lexiqueStr = evalStringForLexique(strToEvaluate);
    	  
    	  
        var indiceBR = "<div style='width:200px;height:15px;background-image:none'></div>" + lexiqueStr + "<div style='width:200px;height:40px;background-image:none'></div>";

        $$('#indiceheader span')[0].update(this.pages[this.currentPageIndex].indiceTag);
        $$('#indicewrapper div')[0].update(indiceBR);

        jQuery("#indicewrapper div:first").removeClass('indicelexiquecontent');

        indiceWindow.open('indice');
                
        jQuery("#indicewrapper div:first").scrollTop(0);
    },

    showSource: function(){
    	  var strToEvaluate;
    	  var lexiqueStr = this.pages[this.currentPageIndex].source;
    	  
    	  // strToEvaluate = lexiqueStr;
    	  // lexiqueStr = evalStringForLexique(strToEvaluate);
    	  
    	
        var sourceBR = "<div style='width:200px;height:15px;background-image:none'></div>" + lexiqueStr + "<div style='width:200px;height:40px;background-image:none'></div>";

        $$('#indiceheader span')[0].update(this.pages[this.currentPageIndex].sourceTag);
        $$('#indicewrapper div')[0].update(sourceBR);

        jQuery("#indicewrapper div:first").removeClass('indicelexiquecontent');

        indiceWindow.open('indice');
        
        jQuery("#indicewrapper div:first").scrollTop(0);
    },

    showConsigne: function(){
    	  var strToEvaluate;
    	  var lexiqueStr = this.pages[this.currentPageIndex].consigne;
    	  
    	  // strToEvaluate = lexiqueStr;
    	  // lexiqueStr = evalStringForLexique(strToEvaluate);
    	  
    	  
        var consigneBR = "<div style='width:200px;height:15px;background-image:none'></div>" + lexiqueStr + "<div style='width:200px;height:40px;background-image:none'></div>";

        $$('#indiceheader span')[0].update(this.pages[this.currentPageIndex].readableType);
        $$('#indicewrapper div')[0].update(consigneBR);

        jQuery("#indicewrapper div:first").removeClass('indicelexiquecontent');

        indiceWindow.open('indice');
        
        jQuery("#indicewrapper div:first").scrollTop(0);
    },
    
    showLexique: function(expression, type, localisation){		
    	  var contenu = '';
    	  var bonContenu = false;
    	
				jQuery.each(lexiqueObj.elements, function(j) {		
					bonContenu = false;	
					
					jQuery.each(lexiqueObj.elements[j], function(key, val) {
							if (key == "expression") {
								if (expression.toLowerCase() == val.toLowerCase()) {
									bonContenu = true;
								}
							}
							
							if (key == "contenu") {
								if (bonContenu == true) {
									contenu = val;
								}
							}
					 });
				 });
					
    	
    		expression = capitaliseFirstLetter(expression);
			
			  $$('#indicewrapper div')[0].update('');    	
    		jQuery("#indicewrapper div:first").removeClass('indicelexiquecontent');
    		
				switch (type) {
					case "texte":
						$$('#indicewrapper div')[0].update("<div style='width:200px;height:15px;background-image:none'></div>" + contenu + "<div style='width:200px;height:40px;background-image:none'></div>");
					break;
					
					case "image":			
						var srcImage;
												
						if (localisation == 1) {						
							sImgFolder = this.mediasFolder;
							srcImage = this.mediasFolder + "/" + contenu;
						}
						else {
							srcImage = contenu;
						}
																		
						this.imagePathHS = srcImage;
						
						var myImage = new Image(); 
            myImage.name = srcImage;
            myImage.onload = findHHandWWLexique;
            myImage.src = srcImage;												
					break;
					
					case "son":					
						jQuery("#indicewrapper div:first").addClass('indicelexiquecontent');
												
            var theSoundId = 'soundLexiqueInPage' + parseInt(this.currentPageIndex); //START AT 0
            var theSoundPath;
						
            var subCategory = 1;
						
            if (localisation == 1) {
                theSoundPath = this.mediasFolder + "/" + contenu;
            }
            else if (localisation == 2) {
                theSoundPath = contenu;
                
                if (theSoundPath.charAt(0) == '<' && theSoundPath.charAt(theSoundPath.length - 1) == '>') {
                    subCategory = 2;
                }
            }
            
                                    
            if (subCategory == 1){            	               
            	$$('#indicewrapper div')[0].update("<div style='width:200px;height:15px;background-image:none'></div><div id='soundLexiqueContainer'><div id='soundLexiqueContainerWrapper'><div id='soundLexiqueContainer2'><div id='" + theSoundId + "'></div></div></div></div><div style='width:200px;height:40px;background-image:none'></div>");
            	            	
            	var theScreenColor = '000000';
							
							jQuery("#soundLexiqueContainer2").html('<div id="' + theSoundId + '"></div>');							
																								
							jwplayer(theSoundId).setup({
								file: theSoundPath, 			
								width: this.jwp_audiowidth,
								height: this.jwp_audioheight,						
								primary: 'flash'     
							});					        	                    	            	            	                                                           							 							 							 									 							 
            }
            else if (subCategory == 2) {
            	$$('#indicewrapper div')[0].update("<div style='width:200px;height:15px;background-image:none'></div><div id='soundLexiqueContainer'><div id='soundLexiqueContainerWrapper'><div id='soundLexiqueContainer2'>" + theSoundPath + "</div></div></div></div><div style='width:200px;height:40px;background-image:none'></div>");            	               
            }																													
					break;
					
					case "video":												
						jQuery("#indicewrapper div:first").addClass('indicelexiquecontent');
						
						var videoWidth = this.jwp_width;
            var videoHeight = this.jwp_height;
            
            var theVideoId = 'videoLexiqueInPage' + parseInt(this.currentPageIndex); //START AT 0
            var theVideoPath;
						
            var subCategory = 1;
						
            if (localisation == 1) {
                theVideoPath = this.mediasFolder + "/" + contenu;
            }
            else if (localisation == 2) {
                theVideoPath = contenu;
                
                if (theVideoPath.charAt(0) == '<' && theVideoPath.charAt(theVideoPath.length - 1) == '>') {
                    subCategory = 2;
                }
            }
            
                                    
            if (subCategory == 1){            	             	 
            	$$('#indicewrapper div')[0].update("<div style='width:200px;height:15px;background-image:none'></div><div id='videoLexiqueContainer'><div id='videoLexiqueContainerWrapper'><div id='videoLexiqueContainer2'><div id='" + theVideoId + "'></div></div></div></div><div style='width:200px;height:40px;background-image:none'></div>");
            	                                                           
							 jwplayer(theVideoId).setup({
								 file: theVideoPath, 			
								 width: videoWidth,
								 height: videoHeight,             
								 primary: 'flash',
								 autostart: false
								 //aspectratio: "16:9"			
							 });
							 
							 
							 jwplayer(theVideoId).onReady(function() {							 								 		 
								 if (!isMobile.any()) {									 
									 jQuery("#videoLexiqueContainer2").css('border', '1px solid black');
								 }
							 });	 							 							 
            }
            else if (subCategory == 2) {
            	$$('#indicewrapper div')[0].update("<div style='width:200px;height:15px;background-image:none'></div><div id='videoLexiqueContainer'><div id='videoLexiqueContainerWrapper'><div id='videoLexiqueContainer2'>" + theVideoPath + "</div></div></div></div><div style='width:200px;height:40px;background-image:none'></div>");            	               
            }
        					
					break;
				}
    	    	        
        $$('#indiceheader span')[0].update(expression);     
        
        indiceWindow.open('indice');
        
        jQuery("#indicewrapper").scrollTop(0);        
    },
    
    //Private
    savePage: function(){
        var currentPage = this.pages[this.currentPageIndex];

        if (currentPage.question){
            var answer = currentPage.question.save();
            
            if (!this.previewMode){
                var url = this.urlServer + '/' + this.servercmdpage + '?cmd=save';
                var params = {
                    participantId: this.participantId,
                    questionNb: currentPage.question.questionNb,
                    value: answer
                }
            }
        }
    },

    displayPage: function(page){    	  
    	  jQuery(window).scrollTop(0);    	  
    	
        var theBrowser = detectBrowser();                

        $('videoContainer').style.paddingBottom = '0px';
        $('soundContainer').style.paddingBottom = '0px';
        $('imageContainer').style.paddingBottom = '0px';

        if (this.resultPageEnabled == true) {
        	jQuery("#navbSep3_1").css('visibility', '');
        	jQuery("#navbSep3_2").css('visibility', '');
        	jQuery("#navbSep3_3").css('visibility', '');
        }
        else {
        	if (lexiqueObj == undefined) {		
					 // Pas de lexique
					}
					else {
						jQuery("#navbSep3_1").css('visibility', '');
						jQuery("#navbSep3_2").css('visibility', '');
						jQuery("#navbSep3_3").css('visibility', '');
					}
        }
    	  

        $('quizpage').show();
        $('resultpage').hide();        
        $('lexiquepage').hide();        
        $('lexiquecontenu').update('');

        this.showingResult = false;
    
        this.resetLayout();

        if (page.title){
            $('pageTitle').update(page.title);
            $('pageTitleContainer').show();
            $('pagenavbar').style.marginTop = '0px';

            if (page.question){
                if(page.imagePath || page.videoPath){
                    $('pagecontent').style.marginTop = '48px';
                }
                else{
                    $('pagecontent').style.marginTop = '38px';
                }
            }
            else{
                if (theBrowser.indexOf("Safari") != -1 || theBrowser.indexOf("safari") != -1){
                     $('pagecontent').style.marginTop = '75px';
                }
                else{
                     $('pagecontent').style.marginTop = '78px';
                }   
            }
        }
        else{
            $('pageTitle').update('&nbsp;');
            $('pageTitleContainer').hide();
            $('pagenavbar').style.marginTop = '-2px';

            if (page.question){
                if (theBrowser.indexOf("Safari") != -1 || theBrowser.indexOf("safari") != -1){
                     $('pagecontent').style.marginTop = '62px';
                }
                else{
                     $('pagecontent').style.marginTop = '64px';
                }   
            }
            else{
                $('pagecontent').style.marginTop = '80px';
            }
        }

        //statement
        if(page.statement){
            $('statement').show();
            $('statement').update(page.statement);
        }

        //guideline
        if(page.textGuideline){
            $('textGuidelinequiz').show();
            $('textGuidelinequizSep').show();
            $('textGuidelinequiz').update(page.textGuideline);
        }

        //image
        if(page.imagePath){
            var srcImage;
            
            if (page.imageCategory == 1){
                srcImage = this.mediasFolder + "/" + page.imagePath;
            }
            else if (page.imageCategory == 2){
                srcImage = page.imagePath;
            }
            
            this.imagePathHS = srcImage;

            var myImage = new Image(); 
            myImage.name = srcImage;
            myImage.onload = findHHandWW;
            myImage.src = srcImage;
        }

        //sound
        if(page.soundPath){
            var theSoundId = 'soundInPage' + parseInt(this.currentPageIndex); //START AT 0
            var theSoundPath;
                                    
            var subCategory = 1;
            
            if (page.soundCategory == 1) {
                theSoundPath = this.mediasFolder + "/" + page.soundPath;
            }
            else if (page.soundCategory == 2) {
                 theSoundPath = page.soundPath;
        
                 if (theSoundPath.charAt(0) == '<' && theSoundPath.charAt(theSoundPath.length - 1) == '>') {
                    subCategory = 2;
                 }
            }
            
            
            $('soundContainer').show();    
            
            
            if (subCategory == 1) {                             
                var theScreenColor = '000000';
                            
                jQuery("#soundContainer2").html('<div id="' + theSoundId + '"></div>');
                            
                if (page.autoplaySound == true) {
                    theAutostart = true;           
                }
                            
                if (page.showSoundController == false) {
                    theScreenColor = 'FFFFFF';
                                
                    this.jwp_audiowidth = 1;
                    this.jwp_audioheight = 1;               
                }
                                                                    
                jwplayer(theSoundId).setup({
                    file: theSoundPath,             
                    width: this.jwp_audiowidth,
                    height: this.jwp_audioheight,                       
                    primary: 'flash',
                    screencolor: theScreenColor,
                    autostart: theAutostart            
                 });                                     
             }
             else if (subCategory == 2) {
                 $('soundContainer2').update(theSoundPath);            
              }
                                                
						 // On ne peut cacher et jouer un son jwPlayer 6
						 // Garder ce code quand même si on veut tester en exemple
						 // On fait une passe-passe en mettant le width et height à 1
						 /*
						 if (showSoundController == false) {
								 jwplayer(theSoundId).onPlay(function() {   
												jwplayer(theSoundId).setControls(showSoundController);
												jQuery("#" + theSoundId).hide();
								 });
								 
								 jwplayer(theSoundId).onComplete(function() {   
												jwplayer(theSoundId).setControls(true);
												jQuery("#" + theSoundId).show();
								 });
						 }
						 */                                                                      
        }
        
        //video
        if(page.videoPath){
            var videoWidth = this.jwp_width;
            var videoHeight = this.jwp_height;
            
            var theVideoId = 'videoInPage' + parseInt(this.currentPageIndex); //START AT 0
            var theVideoPath;

            var theAutostart = false;
            var theControlBar = "bottom";
            
            var subCategory = 1;
            
                                    
            if (page.autoplayVideo == true){
               theAutostart = true;               
            }
            
            
            if (page.videoCategory == 1) {
                theVideoPath = this.mediasFolder + "/" + page.videoPath;
            }
            else if (page.videoCategory == 2) {
                theVideoPath = page.videoPath;
                
                if (theVideoPath.charAt(0) == '<' && theVideoPath.charAt(theVideoPath.length - 1) == '>') {
                    subCategory = 2;
                }
            }
        
        
            if (subCategory == 1){
                 jQuery("#videoContainer2").html('<div id="' + theVideoId + '"></div>');
                
               $('videoContainer').show();
                                            
                             jwplayer(theVideoId).setup({
                                 file: theVideoPath,            
                                 width: videoWidth,
                                 height: videoHeight,             
                                 primary: 'flash',
                                 autostart: theAutostart
                                 //aspectratio: "16:9"          
                             });
                             
                             
                             if (page.showVideoController == false) {
                                 jwplayer(theVideoId).onPlay(function() {   
                                        jwplayer(theVideoId).setControls(showVideoController);
                                 });
                                 
                                 jwplayer(theVideoId).onComplete(function() {   
                                        jwplayer(theVideoId).setControls(true);
                                 });
                             }
                                                    
                             jwplayer(theVideoId).onReady(function() {              
                                 if (!isMobile.any()) {                                  
                                     jQuery("#videoContainer2").css('border', '1px solid black');
                                 }
                             });
                             
                             
                             // Plus besoin. De toute façon, fonctionne mal. On a pas width et height on play, mais "au 2e play"
                             // On dirait qu'il doit avoir été chargé "au moins une fois"
                             // Ex: Play - pause - play
                             // Garder le code quand même à titre d'exemple...
                             /*
                             jwplayer(theVideoId).onPlay(function() {
                                     videoWidth = jwplayer(theVideoId).getMeta()['width'];
                                     videoHeight = jwplayer(theVideoId).getMeta()['height'];
                                     
                                     var maxWidth;
                                     var maxVideoWidth = 640;
                                     
                                     if (videoWidth > maxVideoWidth) {
                                             maxWidth = maxVideoWidth;
                                     }
                                     else {
                                             maxWidth = videoWidth;
                                     }
                                     
                                     var w = videoWidth;
                                     var h = videoHeight;
                                     var r = gcd (w, h);
                                     var arrVideoDimensions = new Array();
                                            
                                     arrVideoDimensions = adjustVideoSize(maxWidth, w/r, h/r);
                                     videoWidth = arrVideoDimensions[0];
                                     videoHeight = arrVideoDimensions[1];

                                     jwplayer(theVideoId).resize(videoWidth, videoHeight);
               });
                             */
                             
                             //jwplayer(theVideoId).onPlay(function() {  alert('Play Pressed');   for (var detail in jwplayer(theVideoId).getMeta()){    alert('Detail: ' + detail );    for (var innerdetail in detail){      alert('InnerDetail: ' + innerdetail );    }  }});
            }
            else if (subCategory == 2) {
                $('videoContainer').show();
                $('videoContainer2').update(theVideoPath);
            }
        }
       
        //ADD PADDING TO LAST MEDIA
        if (page.videoPath){
            jQuery("#videoContainer").css('margin-bottom', '40px');
        }
        else if (page.soundPath){
            jQuery("#soundContainer").css('margin-bottom', '40px');
        }
        else if (page.imagePath){
            jQuery("#imageContainer").css('margin-bottom', '40px');
        }

        if(page.question)
            page.question.display();

        this.updateNavBar(page);

        //Remove if not a question
        if(!page.question){
            this.isPageSection = true;

            if(this.navbRedo)
                $$('#navbRedo')[0].update('<font class="navBarDisabled">' + this.navbRedo + '</font>');
        
            if(this.navbSolution)
                $$('#navbSolution')[0].update('<font class="navBarDisabled">' + this.navbSolution + '</font>');
        
            if(this.navbValidate)
                $$('#navbValidate')[0].update('<font class="navBarDisabled">' + this.navbValidate + '</font>');
            
                                   
            $('feedback').style.backgroundImage = 'none';
            $('feedback').hide();
            $('feedbackcontent').innerHTML = '';
            $('btnCloseFeedback').hide();

            $('pagenavbar').hide();
            
            $('containerMarquage').update('');
            $('containerMarquage').hide();
        }
        else{
            this.isPageSection = false;

            if(this.navbRedo)
                $$('#navbRedo')[0].update('<a href="javascript:top.ccdmd.nq4.redo();">' + this.navbRedo + '</a>');
        
            if(this.navbSolution)
                $$('#navbSolution')[0].update('<a href="javascript:top.ccdmd.nq4.showSolution();">' + this.navbSolution + '</a>');
        
            if(this.navbValidate)
                $$('#navbValidate')[0].update('<a href="javascript:top.ccdmd.nq4.validate();">' + this.navbValidate + '</a>');


            if (page.showNavBSolution)
                $('navbSolution').show();
            else
                $('navbSolution').hide();

            $('navbValidate').show();

            $('pagenavbar').show();
        }

        updateWrappersSize();


        if (this.answerTimerEnabled)
            InitializeTimer();
        
        //CSS visibility:hidden... On met visibility par défaut par la suite. Pour régler loading "glitch"...
        if (jQuery("#pageTitleContainer").hasClass("clsVisibilityHidden")) {
           jQuery("#navbar").removeClass("clsVisibilityHidden");
           jQuery("#pageTitleContainer").removeClass("clsVisibilityHidden");
           jQuery("#pagenavbar").removeClass("clsVisibilityHidden");
           jQuery("#statement").removeClass("clsVisibilityHidden");
           jQuery("#wrapperMarquage").removeClass("clsVisibilityHidden");
           jQuery("#textGuidelinequizSep").removeClass("clsVisibilityHidden");
           jQuery("#feedback").removeClass("clsVisibilityHidden");
           jQuery("#resultpage").removeClass("clsVisibilityHidden");
           jQuery("#lexiquepage").removeClass("clsVisibilityHidden");
        }
                                
        ajusterWrapperAll();
        initLexiqueGeneral();        
    },
    
    updateNavBar: function(page){
        var presentBar = false;
        
        jQuery('#navbRedo').css('visibility', '');
        jQuery('#navbSolution').css('visibility', '');
        jQuery('#navbValidate').css('visibility', '');
        
    
        //***Quiz navbar***        
        if (this.resultPageEnabled == false) {
          jQuery("#navbResult").hide();
          jQuery(".navbSep3").css('display', 'none');
        }
        else {
        	jQuery("#navbResult").css('visibility', '');
        	// $('navbResult').show();
        }
        
        
        if (lexiqueObj == undefined) {		
					 // Pas de lexique
					 $('navbLexique').hide();
				}      
				else {						
					  if (this.resultPageEnabled == true) {
					  	jQuery("#navbLexique").addClass('navbSepOnly');
					  }
					  
						$('navbLexique').show();
				}
        
        
        $('navbRedo').show();
        $('navbValidate').show();

        if(page.lastPage){
            $('navbar').update('');
            return;
        }   
        
                        
        //back
        if(page.pageBackEnabled){
            $('btnBackEnabled').show();
            $('btnBackDisabled').hide();
        }else{
            $('btnBackEnabled').hide();
            $('btnBackDisabled').show();
        }
        
        //index
        pageIndex = this.pages.indexOf(page);
        $('navBarPageIndex').update(pageIndex + 1);
        $('navBarTxtPageIndex').value = (pageIndex + 1);
        
        //next
        if(page.pageNextEnabled){
            $('btnNextEnabled').show();
            $('btnNextDisabled').hide();
        }else{
            $('btnNextEnabled').hide();
            $('btnNextDisabled').show();
        }

        //solution
        if (page.showNavBSolution)
            $('navbSolution').show();
        else
            $('navbSolution').hide();
        
        //***Page navbar***
        //type

        if(page.readableType){
            presentBar = true;
            $('pagenavbScore').style.background = 'url(./images/pagechoicesep.png) no-repeat left';
            $('pagenavbScore').style.paddingLeft = '21px';
            $('pagenavbType').show();
            $$('#pagenavbType a')[0].update(page.readableType);
        }
        else{
            $('pagenavbScore').style.background = '';
            $('pagenavbScore').style.paddingLeft = '0px';
            $('pagenavbType').hide();
        }
            
        //current score
        if(page.question){
            var strPoints;
            var roundedScore = Math.round(page.question.currentScore * 100) / 100;
            var formatPonderation = page.question.ponderation;

            if (this.decimalSymbol == 0){
                roundedScore = changeDecimalSymbol(roundedScore, ",");
                formatPonderation = changeDecimalSymbol(formatPonderation, ",");
            }

            if (page.question.ponderation > 1)
                strPoints = this.points;
            else
                strPoints = this.point;

           $('pagenavbScore').update(roundedScore + '&nbsp;/&nbsp;' + formatPonderation + '&nbsp;' + strPoints);
        }

        if(page.indiceTag){
            presentBar = true;
            $('pagenavbIndice').show();
            $$('#pagenavbIndice a')[0].update(page.indiceTag);
        }
        else{
            $('pagenavbIndice').hide();
        }
        
        if(page.sourceTag){
            presentBar = true;
            $('pagenavbSource').show();
            $$('#pagenavbSource a')[0].update(page.sourceTag);
        }
        else{
            $('pagenavbSource').hide();
        }

        if (presentBar == false){
            $('pagenavbar').style.paddingTop = '2px';
        }
        else{
            $('pagenavbar').style.paddingTop = '0px';
        }                                
    },
    
    resetLayout: function(){
        $('statement').hide();
        $('textGuidelinequiz').hide();
        $('textGuidelinequizSep').hide();

        $('question').update('');

        $('imageContainer').update('');
        $('imageContainer').hide();

        $('videoContainer').update('');
        $('videoContainer').update('<div id="videoContainer2"></div>');        
        $('videoContainer').hide();

        $('soundContainer').update('');
        $('soundContainer').update('<div id="soundContainer2"></div>');
        $('soundContainer').hide();
        
        $('containerMarquage').update('');
        $('wrapperMarquage').hide();
    },
    
    displayResultPage: function(){
    		var strToEvaluate;
    	
    	  jQuery(window).scrollTop(0);
    	
        if (this.answerTimerEnabled){
            InitializeTimer();
            StopTheClock();
        }
        
        this.resetLayout();
        	 
				if(this.navbRedo)
						$$('#navbRedo')[0].update('<font class="navBarDisabled">' + this.navbRedo + '</font>');
		
				if(this.navbSolution)
						$$('#navbSolution')[0].update('<font class="navBarDisabled">' + this.navbSolution + '</font>');
		
				if(this.navbValidate)
						$$('#navbValidate')[0].update('<font class="navBarDisabled">' + this.navbValidate + '</font>');
        
					
				// OLD 2014-04-07	
        /*jQuery("#navbRedo").css('visibility', 'hidden');
    	  jQuery("#navbSolution").css('visibility', 'hidden');
    	  jQuery("#navbValidate").css('visibility', 'hidden');
    	  jQuery("#navbResult").css('visibility', 'hidden');
    	  
    	  jQuery("#navbSep3_1").css('visibility', 'hidden');
    	  jQuery("#navbSep3_2").css('visibility', 'hidden');
    	  jQuery("#navbSep3_3").css('visibility', 'hidden');
    	  
    	  jQuery("#navbLexique").css('visibility', 'visible');			    	  
    	  jQuery("#navbLexique").removeClass('navbSep');*/
    	  
    	  
    	  for(var i = 0;i <= 4;i++){
        	jQuery("#resultHeaderCol" + i + "WT").css('height', '');   	     
        	jQuery("#resultHeaderCol" + i + "WTContent").css('height', '');  	
        }
    	  
        $('feedback').style.backgroundImage = 'none';
        $('btnCloseFeedback').hide();
        
        $('quizpage').hide();
        $('lexiquepage').hide();        
        $('lexiquecontenu').update('');
        
        $('containerMarquage').update('');
        $('containerMarquage').hide();
                    
        $('resultpage').show();
        
        this.showingResult = true;
        $('navBarPageIndex').update(this.pages.length + 1);
        $('navBarTxtPageIndex').value = this.pages.length + 1;
        
        $('resulttitle').update('');
        
        if (this.resultOtherContent != '') {
        		strToEvaluate = this.resultOtherContent;
        		this.resultOtherContent = evalStringForLexique(strToEvaluate);
        		        		        	
            $('resultother').update(this.resultOtherContent);
        }
        
        var currRow = null;
        var currCell = null;
        
        var resultMailHeader = new Array();
        var resultMailBody = new Array();

        var lastRowWidth = (this.pages.length > this.resultNbPagesBFScroll? 211 : 208);
        
        this.subjectMail = '';
        this.resultMailTop1 = '';
        this.resultMailTop2 = '';
        this.resultMail = '';
        
        if(this.answerTimerEnabled){
            lastRowWidth -= 99;
            $('resultWtimer').update();
            $('resultWOtimer').hide();
            $('resultheaderWOtimer').hide();
            $('resultcontentWOtimer').hide();
            
            resultMailHeader.push(this.resultHeaderCol0);
            resultMailHeader.push(this.resultHeaderCol1);
            resultMailHeader.push(this.resultHeaderCol2);
            resultMailHeader.push(this.resultHeaderCol3);
            resultMailHeader.push(this.resultHeaderCol4);
        }else{
            $('resultWOtimer').update();
            $('resultWtimer').hide();
            $('resultheaderWtimer').hide();
            $('resultcontentWtimer').hide();
            
            resultMailHeader.push(this.resultHeaderCol0);
            resultMailHeader.push(this.resultHeaderCol1);
            resultMailHeader.push(this.resultHeaderCol3);
            resultMailHeader.push(this.resultHeaderCol4);
        }
        
        var totalPond = 0;
        var totalScore = 0;

        if (this.pages.length > 8){
            if(this.answerTimerEnabled)
                $('resultcontentWtimer').style.width = 591 + 'px';
            else
                $('resultcontentWOtimer').style.width = 584 + 'px';
        }

        for($i = 0;$i < this.pages.length;$i++){
            var currPage = this.pages[$i];
        
            var pageNumber = $i + 1;
            var triesCount = this.resultNoValue;
            var answerTime = this.resultNoValue;
            var score = this.resultNoValue;
            var status = this.resultNoValue;
            
            var indexResultMail = 0;
            
            if(currPage.question){
                if (currPage.question.triesCount > 0){
                    var nbSeconds = Math.round(this.pageTimer[$i] / 100);  //Data in seconds
                    var minVar = Math.floor(nbSeconds / 60);  // The minutes
                    var secVar = nbSeconds % 60;  // The balance of seconds

                    if (secVar >= 10)
                        answerTime = minVar + ":" + secVar;
                    else
                        answerTime = minVar + ":0" + secVar;
                }

                triesCount = currPage.question.triesCount;


                var roundedCurrentScore = Math.round(currPage.question.currentScore * 100) / 100;

                if (this.decimalSymbol == 0)
                    score = changeDecimalSymbol(roundedCurrentScore,",") + ' / ' + changeDecimalSymbol(currPage.question.ponderation,",");
                else
                    score = roundedCurrentScore + ' / ' + currPage.question.ponderation;

                if (triesCount == 0)
                    status = this.statusToDo;
                else
                    status = currPage.question.status;
                
                totalPond += currPage.question.ponderation;
                totalScore += currPage.question.currentScore;
            }
            
            //Populate Table
            if(this.answerTimerEnabled)
                currRow = $('resultWtimer').insertRow($i);
            else
                currRow = $('resultWOtimer').insertRow($i);
            
            //pageNumber col
            currCell = currRow.insertCell(0);
            currCell.style.width = 99 + 'px';

            currCell.innerHTML = '<a href="javascript:top.ccdmd.nq4.pageGoto(' + (pageNumber-1) + ')">' + pageNumber + '</a>';
            resultMailBody += '<b>' + resultMailHeader[indexResultMail] + " " + pageNumber + "</b><br>";
            indexResultMail++;
            
            //triesCount col
            currCell = currRow.insertCell(1);
            currCell.style.width = 138 + 'px';
            currCell.innerHTML = triesCount;
            resultMailBody += resultMailHeader[indexResultMail] + " : " + triesCount + "<br>";
            
            var nextCellId = 2;
            if(this.answerTimerEnabled){
                currCell = currRow.insertCell(nextCellId);
                currCell.style.width = 119 + 'px';
                currCell.innerHTML = answerTime;
                nextCellId++;
                
                indexResultMail++;
                resultMailBody += resultMailHeader[indexResultMail] + " : " + answerTime + "<br>";
            }
            
            //score col
            currCell = currRow.insertCell(nextCellId);
            currCell.style.width = 99 + 'px';
            currCell.innerHTML = score;
            nextCellId++;
            indexResultMail++;
            resultMailBody += resultMailHeader[indexResultMail] + " : " + score + "<br>";
            
            //status col
            currCell = currRow.insertCell(nextCellId);

            if(this.answerTimerEnabled){
                currCell.style.width = 115 + 'px';
            }
            else{
                currCell.style.width = 228 + 'px';
            }

            currCell.style.textAlign = 'left';
            currCell.style.paddingLeft = '15px';
            currCell.innerHTML = status;
            
            indexResultMail++;
            resultMailBody += resultMailHeader[indexResultMail] + " : " + status + "<br>";
            
            resultMailBody += "<br><br>";
        }

        totalScore =  Math.round(totalScore * 100) / 100;
        var totalScorePC = Math.round(totalScore / totalPond * 100,2);

        if (this.decimalSymbol == 0){
            totalScore = changeDecimalSymbol(totalScore, ",");
            totalPond = changeDecimalSymbol(totalPond, ",");
            totalScorePC = changeDecimalSymbol(totalScorePC, ",");
        }
        
        
        // Uniformiser colonnes suite aux grossisements des fonts                        
        var maxH = jQuery("#resultHeaderCol" + i + "WT").parent().height();
        for(var i = 0;i <= 4;i++){
        	var thisH = jQuery("#resultHeaderCol" + i + "WT").parent().height();
        	
        	if (thisH > maxH) {
        		maxH = thisH;        		
        	}        	   
        	
        	jQuery("#resultHeaderCol" + i + "WTContent").width(jQuery("#resultHeaderCol" + i + "WTContent").parent().width());
        }
        
        for(var i = 0;i <= 4;i++){
        	jQuery("#resultHeaderCol" + i + "WT").height(maxH + 'px');        	     
        	jQuery("#resultHeaderCol" + i + "WTContent").height(maxH + 'px');        	     
        }
        
                        
        var resultTitle;
        
        if (totalPond == 0) {
            resultTitle = this.navbResult;
        }
        else {
            resultTitle = this.navbResult + "&nbsp;&nbsp;" + "<span id='totalScore'>" + totalScore + "&nbsp;/&nbsp;" + totalPond + "&nbsp;&nbsp;(" + totalScorePC + "&nbsp;%)</span>";
        }
        
        this.resultMailTop1 = this.navbResult;
        this.resultMailTop2 = '<br>' + totalScore + ' / ' + totalPond + ' (' + totalScorePC + '%)<br><br>';
        
        $('resulttitle').update(resultTitle);

        hideFeedback();
        
        this.resultMail = resultMailBody;
      
        this.resultMailTop1 = replaceCharsHTML(this.resultMailTop1);
        this.resultMailTop2 = replaceCharsHTML(this.resultMailTop2);
        this.resultMail = replaceCharsHTML(this.resultMail);
        //this.resultMail = this.resultMail.replace(/'/g, "\\\'");
        this.subjectMail = this.strObjet;
        this.subjectMail = replaceCharsHTML(this.subjectMail);
        
        if (isMobile.any()) {
            $('resultButtonPrint').hide();            
            $('nothing2').update('');
            $('nothing2').hide();
            
            jQuery('#resultButtons').css('width', '606px');
        }
        
        // CACHER BOUTON ENVOYER PAR COURRIEL POUR LE MOMENT 2013-03-20
        $('resultButtonSendTo').hide();       
        $('nothing2').hide();
        
        
        ajusterWrapperAll();
    },
    
    displayLexiquePage: function(){    	  
    	  jQuery(window).scrollTop(0);
    	
    	  var arrSortedExpressionsLexique = new Array();
    	  var arrAnchorsLexique = new Array();
    	  var htmlLexiqueExpression = '';
    	  var htmlLexiqueVariante = '';
    	  var htmlLexiqueContenu = '';
    	  var lexiqueTitle = this.navbLexique;
    	  
    	  var lexiqueExpressionOnlyTop = '';
    	  var lexiqueExpression = '';							
				var lexiqueContenu = '';
				var lexiqueLocalisation = '';
    	  var lexiqueType = '';
    	  var lexiqueTypeMedia = '';
    	  var lexiqueContenuMedia = '';
    	  var mediaId = '';				
				var subCategory = -1;					
				var showExpression = true;
				
				
				for (i = 1; i <= 26; i++) {
					arrAnchorsLexique[i] = ""; 
				}
    	      	
    	  this.resetLayout();
    	  
    	   if(this.navbRedo)
						$$('#navbRedo')[0].update('<font class="navBarDisabled">' + this.navbRedo + '</font>');
		
				if(this.navbSolution)
						$$('#navbSolution')[0].update('<font class="navBarDisabled">' + this.navbSolution + '</font>');
		
				if(this.navbValidate)
						$$('#navbValidate')[0].update('<font class="navBarDisabled">' + this.navbValidate + '</font>');
					
    	  
    	  // OLD 2014-04-07
    	  /*jQuery("#navbRedo").css('visibility', 'hidden');
    	  jQuery("#navbSolution").css('visibility', 'hidden');
    	  jQuery("#navbValidate").css('visibility', 'hidden');
    	  
    	  jQuery("#navbSep3_1").css('visibility', 'hidden');
    	  jQuery("#navbSep3_2").css('visibility', 'hidden');
    	  jQuery("#navbSep3_3").css('visibility', 'hidden');
        
    	  jQuery("#navbLexique").css('visibility', 'hidden');
    	  jQuery("#navbLexique").removeClass('navbSep');*/
    	  
    	  
        $('feedback').style.backgroundImage = 'none';
        $('btnCloseFeedback').hide();        
        
        $('quizpage').hide();
        $('resultpage').hide();
        $('lexiquepage').show();
        $('lexiquecontenu').update('');
        
        $('containerMarquage').update('');
        $('containerMarquage').hide();                
                      
				 if(this.resultPageEnabled) {   
				 	  jQuery("#navbResult").css('visibility', 'visible');				 	  
				 	 
						$('navBarPageIndex').update(this.pages.length + 2);
						$('navBarTxtPageIndex').value = this.pages.length + 2;
				}
				else {
						$('navBarPageIndex').update(this.pages.length + 1);
						$('navBarTxtPageIndex').value = this.pages.length + 1;
				}
				
								
				$('lexiquetitle').update(lexiqueTitle);					
												
				
				arrSortedExpressionsLexique = sortLexique();
				
				for (var i = 0; i < arrSortedExpressionsLexique.length; i++) {
					htmlLexiqueExpression = '';					
					htmlLexiqueContenu = '';
					
					lexiqueExpressionOnlyTop = '';
					lexiqueTypeMedia = '';
					lexiqueContenuMedia = '';
					mediaId = '';					
					subCategory = -1;
					showExpression = true;
					
					jQuery.each(lexiqueObj.elements, function(j) {
						lexiqueType = '';
						lexiqueExpression = '';							
						lexiqueContenu = '';
						lexiqueLocalisation = '';						
						htmlLexiqueVariante = '';
													
						jQuery.each(lexiqueObj.elements[j], function(key, val) {
							switch (key)
							{
								case "expression":
									if (arrSortedExpressionsLexique[i].toLowerCase() == val.toLowerCase()) {
										lexiqueExpression = capitaliseFirstLetter(val);
									}
								break;
								
								case "variantes":																		
									var arrVariantes = val.split("||");										
									
									if (val != "") {	
										htmlLexiqueVariante += '<span class="variantes_lexique">(';
										
										for (var x = 0; x < arrVariantes.length; x++) {																						
											if (x + 1 == arrVariantes.length) {
												htmlLexiqueVariante += capitaliseFirstLetter(arrVariantes[x]);
											}
											else {
												htmlLexiqueVariante += capitaliseFirstLetter(arrVariantes[x]) + '<img src=\"images/pagechoicesep.png\" border=\"0\">'; 		
											}																						
										}																	
									
										htmlLexiqueVariante += ')</span>';
									}
								break;
								
								case "type":
									lexiqueType = val;
								break;
								
								case "contenu":
									lexiqueContenu = val;																		
								break;
								
								case "localisation":
									lexiqueLocalisation = val;
								break;
								
								case "is_variante":
									showExpression = false;
								break;
							}																																
						});
						
						if (lexiqueExpression != '' && showExpression == true) {
							lexiqueExpressionOnlyTop = lexiqueExpression;
							htmlLexiqueExpression = '<div class="expression_lexique">' + lexiqueExpression;
							
							if (htmlLexiqueVariante != '') {
								htmlLexiqueExpression += ' ' + htmlLexiqueVariante;
							}
							
							htmlLexiqueExpression += '</div>';
							
							switch (lexiqueType)
							{
								case "texte":
									htmlLexiqueContenu = '<div class="container_contenu_lexique">' + lexiqueContenu + '</div>';									
								break;
								
								case "image":
									var sImgFolder;
									var srcImage;
									var idImage = "page-lexique-img-" + i;
																					
									if (lexiqueLocalisation == 1) {						
										sImgFolder = gNQ4.mediasFolder;
										srcImage = gNQ4.mediasFolder + "/" + lexiqueContenu;
									}
									else {
										srcImage = lexiqueContenu;
									}
																					
																											
									var myImage = new Image(); 
									myImage.name = srcImage;
									myImage.onload = findHHandWWLexiquePage;
									myImage.src = srcImage;
									myImage.dataimagename = lexiqueContenu;
									myImage.localisation = lexiqueLocalisation;
																											
									htmlLexiqueContenu = '<div class="container_contenu_lexique_img"><a href="' + srcImage + '" class="highslide" onclick="return hs.expand(this)">' + '<img id="' + idImage + '" class="imglexique" src="' + srcImage + '" data-image-name="' + lexiqueContenu + '" height="0">' + '</a></div>'; 																		
								break;
								
								case "son":
									lexiqueTypeMedia = 'son';															
									
									var theSoundId = "page-lexique-son-" + i;
									var theSoundPath;
									
									mediaId = theSoundId;								
																											
									if (lexiqueLocalisation == 1) {
											subCategory = 1;
											theSoundPath = gNQ4.mediasFolder + "/" + lexiqueContenu;
									}
									else if (lexiqueLocalisation == 2) {
											subCategory = 1;
											theSoundPath = lexiqueContenu;
											
											if (theSoundPath.charAt(0) == '<' && theSoundPath.charAt(theSoundPath.length - 1) == '>') {
													subCategory = 2;
											}
									}
									
									lexiqueContenuMedia = theSoundPath;
																																																
									htmlLexiqueContenu = '<div class="container_contenu_lexique"><div id="' + theSoundId + '"></a></div>';
								break;
								
								case "video":
									lexiqueTypeMedia = 'video';
									
									var theVideoId = "page-lexique-video-" + i;
									var theVideoPath;
									
									mediaId = theVideoId;									
																											
									if (lexiqueLocalisation == 1) {
										subCategory = 1;
										theVideoPath = gNQ4.mediasFolder + "/" + lexiqueContenu;
									}
									else if (lexiqueLocalisation == 2) {
											subCategory = 1;
											theVideoPath = lexiqueContenu;
																																	
											if (theVideoPath.charAt(0) == '<' && theVideoPath.charAt(theVideoPath.length - 1) == '>') {																	  												
													subCategory = 2;
													
													var wmode = theVideoPath.toLowerCase().indexOf("wmode");
													var isEmbed = theVideoPath.toLowerCase().indexOf("<embed");
													var isIframe = theVideoPath.toLowerCase().indexOf("<iframe");
													
													if (wmode == -1) {												  	
														if (isEmbed != -1) {													
															theVideoPath = theVideoPath.replace("<embed","<embed wmode='transparent'");
															
														}	
														else if (isIframe != -1) {
															var srcValue = "";
															var hasParamDelimiter = false;
															var startSrc = -1;
															
															for (k = 0; k < theVideoPath.length; k++) {
																if (theVideoPath.charAt(k) == "s" && theVideoPath.charAt(k + 1) == "r" && theVideoPath.charAt(k + 2) == "c" && theVideoPath.charAt(k + 3) == "=") {
																	if (startSrc == -1) {
																		startSrc = k + 5;
																	}
																}
																
																
																if (k >= startSrc && startSrc > -1) {																																		
																	if (theVideoPath.charAt(k) != '"') {
																		if (theVideoPath.charAt(k) == '?') {
																			hasParamDelimiter = true;																	
																		}
																		
																		srcValue = srcValue + theVideoPath.charAt(k);
																	}
																	else {
																		// Fin de la valeur src
																		startSrc = -1;
																	}
																}
															}
																														
															if (hasParamDelimiter == true) {
																var newSrcValue = srcValue + "&wmode=transparent";
																theVideoPath = theVideoPath.replace(srcValue, newSrcValue);																																									
															}
															else {
																theVideoPath = theVideoPath.replace(srcValue, srcValue + "?wmode=transparent");
															}
														}
													}
											}																																	
									}
									
									lexiqueContenuMedia = theVideoPath;
																																																
									htmlLexiqueContenu = '<div class="container_contenu_lexique"><div id="' + theVideoId + '" class="video_lexique_' + subCategory + '"></a></div>';
								break;
								
								case "lien":
									//str.length;
									var lexiqueContenuStr = lexiqueContenu;
									
									if (lexiqueContenu.length > 70) {
										lexiqueContenuStr = lexiqueContenu.substr(0, 70) + "[...]";	
									}
																											
									htmlLexiqueContenu = '<div class="container_contenu_lexique"><a onclick="javascript:window.open(\'' + lexiqueContenu + '\'); return false;" href="#">' + lexiqueContenuStr + '</a></div>';									
								break;
							}																					
						}						
					});		
					
					
					var indexLettre = 0;
															
					switch (lexiqueExpressionOnlyTop.toLowerCase().charAt(0)) {
						case "a":
							indexLettre = 1;
						break;
						
						case "b":
							indexLettre = 2;
						break;
						
						case "c":
							indexLettre = 3;
						break;
						
						case "d":
							indexLettre = 4;
						break;
						
						case "e":
							indexLettre = 5;
						break;
						
						case "f":
							indexLettre = 6;
						break;
						
						case "g":
							indexLettre = 7;
						break;
						
						case "h":
							indexLettre = 8;
						break;
						
						case "i":
							indexLettre = 9;
						break;
						
						case "j":
							indexLettre = 10;
						break;
						
						case "k":
							indexLettre = 11;
						break;
						
						case "l":
							indexLettre = 12;
						break;
						
						case "m":
							indexLettre = 13;
						break;
						
						case "n":
							indexLettre = 14;
						break;
						
						case "o":
							indexLettre = 15;
						break;
						
						case "p":
							indexLettre = 16;
						break;
						
						case "q":
							indexLettre = 17;
						break;
						
						case "r":
							indexLettre = 18;
						break;
						
						case "s":
							indexLettre = 19;
						break;
						
						case "t":
							indexLettre = 20;
						break;
						
						case "u":
							indexLettre = 21;
						break;
						
						case "v":
							indexLettre = 22;
						break;
						
						case "w":
							indexLettre = 23;
						break;
						
						case "x":
							indexLettre = 24;
						break;
						
						case "y":
							indexLettre = 25;
						break;
						
						case "z":
							indexLettre = 26;
						break;
					}
					
					
					
					if (arrAnchorsLexique[indexLettre] == "" && indexLettre != 0) {												
						arrAnchorsLexique[indexLettre] = lexiqueExpressionOnlyTop.toLowerCase().charAt(0);			
						
						jQuery("#lexiquecontenu").append('<a class="a_id_lexique" id="' + arrAnchorsLexique[indexLettre] + '">' + htmlLexiqueExpression + '</a>');
								
						// Ne peut pas mettre un simple anchor à cause du sticky header.
						// Doit scroller "manuellement"
						jQuery("#lexique-lettre-" + lexiqueExpressionOnlyTop.toLowerCase().charAt(0)).bind({
							click: function() {																				
								var thisIndexLettre = jQuery(this).attr("data-index-lettre");								
																
								var p = jQuery("#" + arrAnchorsLexique[thisIndexLettre]);
								var position = p.position();
																								
								jQuery(window).scrollTop(position.top - jQuery("#header").height());				
								
								
								if (isMobile.any()) {		
									var scrollTop = jQuery(window).scrollTop();  
						
									if (scrollTop > 15 || position.top > 15) {
										jQuery('#contentpageup').fadeIn();
									}
									else {
										jQuery('#contentpageup').fadeOut();				
									}
								}
							}
						});

					}
					else {
						jQuery("#lexiquecontenu").append(htmlLexiqueExpression);	
					}
															
					jQuery("#lexiquecontenu").append(htmlLexiqueContenu);
					
					
					if (lexiqueTypeMedia == 'son') {												
						if (subCategory == 1) {														
							jwplayer(mediaId).setup({
								file: lexiqueContenuMedia, 			
								width: gNQ4.jwp_audiowidth,
								height: gNQ4.jwp_audioheight,						
								primary: 'flash'     
							});					 
						}
						else if (subCategory == 2) {
							jQuery("#" + mediaId).html(lexiqueContenuMedia);
						}
					}
					else if (lexiqueTypeMedia == 'video') {												
						if (subCategory == 1) {														
							 jwplayer(mediaId).setup({
								 file: lexiqueContenuMedia, 			
								 width: gNQ4.jwp_width,
								 height: gNQ4.jwp_height,	         
								 primary: 'flash',
								 autostart: false
								 //aspectratio: "16:9"			
							 });
							 
						
							 jwplayer(mediaId).onReady(function() {							 								 		 
								 if (!isMobile.any()) {									 
									 jQuery(".video_lexique_1").css('border', '1px solid black');
								 }
							 });								 
						}
						else if (subCategory == 2) {																																																																					
							jQuery("#" + mediaId).html(lexiqueContenuMedia);														
						}
					}
				}
				
				
				jQuery(".lexique_lettre a").each(function() {
					var dataIndexLettre = jQuery(this).attr("data-index-lettre");
					
					if (arrAnchorsLexique[dataIndexLettre] == "") {						
						jQuery(this).addClass('lettre_link_disabled');
					}
					else {						
						jQuery(this).addClass('lettre_link_enabled');
					}
				});
				
																				
				ajusterWrapperAll();
    },
    
    showResultIdentForm: function(){
        var width = $('resultIdentForm').getWidth();
        var height = $('resultIdentForm').getHeight();

        var screenVisibleW = document.getElementById('pagewrapper').offsetWidth / 2;
        var screenVisibleH = document.getElementById('pagewrapper').offsetHeight / 4;
        
        var indiceW = width / 2;
        var left = screenVisibleW - indiceW;

        $('txtrifLastName').value = this.userLastName;
        $('txtrifName').value = this.userName;
        $('txtrifCode').value = this.userCode;
        $('txtrifGroup').value = this.userGroup;
        $('txtrifEmail').value = this.userEmail;
        $('txtrifOther').value = this.userOther;

        $('resultIdentForm').style.left = left + 'px';;
        $('resultIdentForm').style.top = '135px';

        $('resultIdentForm').show();
    },
    
    showSendToForm: function(){
        var width = $('sendtoForm').getWidth();
        var height = $('sendtoForm').getHeight();

        var screenVisibleW = document.getElementById('pagewrapper').offsetWidth / 2;
        var screenVisibleH = document.getElementById('pagewrapper').offsetHeight / 4;
        
        var indiceW = width / 2;
        var left = screenVisibleW - indiceW;

        $('txtrifLastNameST').value = this.userLastName;
        $('txtrifNameST').value = this.userName;
        $('txtrifCodeST').value = this.userCode;
        $('txtrifGroupST').value = this.userGroup;
        $('txtrifEmailST').value = this.userEmail;
        $('txtrifEmailDestST').value = this.userSendTo;
        $('txtrifOtherST').value = this.userOther;

        $('sendtoForm').style.left = left + 'px';;
        $('sendtoForm').style.top = '135px';

        $('sendtoForm').show();
    },
    
    hideResultIdentForm: function(){
        $('resultIdentForm').hide();
    },
    
    hideSendToForm: function(){
        $('sendtoForm').hide();
    },
    
    includeMediaFeedback: function (){
        var mediaToInclude = '';
        var feedbackHTML = '';
        var classReponse = jQuery("#feedbackcontent span:first-child").attr('class');
        
        if (classReponse == 'Green'){
            if (this.pages[this.currentPageIndex].goodAnswerMedia != null) {
                jQuery("#feedbackcontent ." + classReponse).after('<div id="idRetroMultimedia" class="retroMultimedia"></div>');
                this.buildMediaFeedback(this.pages[this.currentPageIndex].goodAnswerMedia, classReponse);
            }
        }
        else if (classReponse == 'Red'){
            if (this.pages[this.currentPageIndex].wrongAnswerMedia != null) {
                jQuery("#feedbackcontent ." + classReponse).after('<div id="idRetroMultimedia" class="retroMultimedia"></div>');
                this.buildMediaFeedback(this.pages[this.currentPageIndex].wrongAnswerMedia, classReponse);
            }
        }
        else if (classReponse == 'Yellow'){
            if (this.pages[this.currentPageIndex].incompleteAnswerMedia != null) {
                jQuery("#feedbackcontent ." + classReponse).after('<div id="idRetroMultimedia" class="retroMultimedia"></div>');
                this.buildMediaFeedback(this.pages[this.currentPageIndex].incompleteAnswerMedia, classReponse);
            }
        }
    },
    
    buildMediaFeedback: function(answerMedia, classReponse){
        if (answerMedia[0][0] == 1) { //image ?
            var srcImage;
                
            if (answerMedia[0][1] == 1){
                srcImage = this.mediasFolder + "/" + answerMedia[0][2];
            }
            else if (answerMedia[0][1] == 2){
                srcImage = answerMedia[0][2];
            }
        
        
            this.imagePathHS = srcImage;

            var myImage = new Image();
            myImage.name = srcImage;
            myImage.onload = findHHandWWFeedback;
            myImage.src = srcImage;
        }
        else if (answerMedia[0][0] == 2) { //son ?
            var theSoundId = classReponse + '_' + 'soundFeedback' + parseInt(this.currentPageIndex); //START AT 0
            var theSoundPath;
            
            var sWidth = 280;
            var sHeight = 24;
            
            var subCategory = 1;
            
            if (answerMedia[0][1] == 1) {
                theSoundPath = this.mediasFolder + "/" + answerMedia[0][2];
            }
            else if (answerMedia[0][1] == 2) {
                theSoundPath = answerMedia[0][2];
        
                if (theSoundPath.charAt(0) == '<' && theSoundPath.charAt(theSoundPath.length - 1) == '>') {
                    subCategory = 2;
                }
            }
            
            
            if (isMobile.any()) {
                var theControls = "";
                var theAutoPlay = "";
                    
                if (answerMedia[0][4] == true){
                    theControls = " controls";
                }
                    
                if (answerMedia[0][3] == true){
                    theAutoPlay = " autoplay";
                }
                 

                if (subCategory == 1) {
                    htmlSound = "<audio" + theControls + theAutoPlay + " id=\"" + theSoundId + "\" style=\"width:" + sWidth + "px; height:" + sHeight + "px;\">";
                    htmlSound += "<source src=\"" + theSoundPath + "\" type=\"audio/mp3\">";
                    htmlSound += "</audio>";
                }
                else if (subCategory == 2) {
                    htmlSound = theSoundPath;
                }
                 
                jQuery("#idRetroMultimedia").html(htmlSound);
             }
             else{
                 var theControlBar = "bottom";
                 var theAutostart = false;
                 var theScreenColor = "000000";
                         
                 if (answerMedia[0][4] == true){
                     
                 }
                 else{
                     sWidth = 1;
                     sHeight = 1;
                     theScreenColor = "FFFFFF";
                     theControlBar = "none";
                 }
                        
                 if (answerMedia[0][3] == true){
                     theAutostart = true;
                 }
                         
                         
                 if (subCategory == 1) {
                     jQuery("#idRetroMultimedia").html('<div id="' + theSoundId + '"></div>');
                         
                     jwplayer(theSoundId).setup({
                         width: sWidth,
                         height: sHeight,
                         autostart: theAutostart,
                         controlbar: theControlBar,
                         screencolor: theScreenColor,
                         flashplayer: "scripts/jwplayer/player.swf",
                         file: theSoundPath
                     });
                 }
                 else if (subCategory == 2) {
                     jQuery("#idRetroMultimedia").html(theSoundPath);
                 }
             }
        }
        else if (answerMedia[0][0] == 3) { //video ?
            var videoWidth;
            var videoHeight;
            
            var theVideoId = classReponse + '_' + 'videoFeedback' + parseInt(this.currentPageIndex); //START AT 0
            var theVideoPath;

            var theControls = "";
            var theAutoPlay = "";

            var theControlBar = "bottom";
            var theAutostart = false;
            
            var subCategory = 1;
            
            jQuery("#idRetroMultimedia").html('<div id="feedbackVideoContainer"><div id="' + theVideoId + '"></div></div>');
            
            if (answerMedia[0][4] == true){
                theControls = " controls";
            }
            else{
                theControlBar = "none";
            }
            
            if (answerMedia[0][3] == true){
               theAutostart = true;
               theAutoPlay = " autoplay";
            }
            
            
            if (answerMedia[0][1] == 1) {
                theVideoPath = this.mediasFolder + "/" + answerMedia[0][2];
            }
            else if (answerMedia[0][1] == 2) {
                theVideoPath = answerMedia[0][2];
                
                if (theVideoPath.charAt(0) == '<' && theVideoPath.charAt(theVideoPath.length - 1) == '>') {
                    subCategory = 2;
                }
            }
        
                  
            if (subCategory == 1){
               var htmlVideo = "<video" + theControls + theAutoPlay + " id=\"" + theVideoId + "\">";
               htmlVideo += "<source src=\"" + theVideoPath + "\" type=\"video/mp4\">";
               htmlVideo += "</video>";

               jQuery("#feedbackVideoContainer").html(htmlVideo);
               
               jwplayer(theVideoId).setup({
                       autostart: theAutostart,
                       controlbar: theControlBar,
                       stretching: "exactfit",
                       modes: [
                              { type: "flash", src: "scripts/jwplayer/player.swf" },
                              { type: "html5" }
                       ]
               });
            
            
               jwplayer(theVideoId).onPlay(function() {
                   videoWidth = jwplayer(theVideoId).getMeta()['width'];
                   videoHeight = jwplayer(theVideoId).getMeta()['height'];
                   
                   var maxWidth;
                   var maxVideoWidth = 640;
                   
                   if (videoWidth > maxVideoWidth) {
                       maxWidth = maxVideoWidth;
                   }
                   else {
                       maxWidth = videoWidth;
                   }
                   
                   var w = videoWidth;
                   var h = videoHeight;
                   var r = gcd (w, h);
                   var arrVideoDimensions = new Array();
                      
                   arrVideoDimensions = adjustVideoSize(maxWidth, w/r, h/r);
                   videoWidth = arrVideoDimensions[0];
                   videoHeight = arrVideoDimensions[1];

                   jwplayer(theVideoId).resize(videoWidth, videoHeight);
               });
               
               if (!jQuery.browser.msie){
                  jQuery("#feedbackVideoContainer").css('border', '1px solid black');
               }
            }
            else if (subCategory == 2) {
                jQuery("#feedbackVideoContainer").html(theVideoPath);
            }
        }
    },
    
    resultIdentFormOK: function(){
        this.userLastName = $F('txtrifLastName');
        this.userName = $F('txtrifName');
        this.userCode = $F('txtrifCode');
        this.userGroup = $F('txtrifGroup');
        this.userEmail = $F('txtrifEmail');
        this.userOther = $F('txtrifOther');
        
        $('resultIdentForm').hide();
        this.updateHTMLVersion();

        var url = 'printable.html';
        var width = 700;
        var height = 600;
        var left = parseInt((screen.availWidth/2) - (width/2));
        var top = parseInt((screen.availHeight/2) - (height/2));
        var windowFeatures = "width=" + width + ",height=" + height + ",resizable,scrollbars,status,toolbar,menubar,left=" + left + ",top=" + top + ",screenX=" + left + ",screenY=" + top;

        window.open(url, "printable", windowFeatures);
    },
    
    sendtoFormOK: function(){
        var destinataire = $F('txtrifEmailDestST');
        
        this.userLastName = $F('txtrifLastNameST');
        this.userName = $F('txtrifNameST');
        this.userCode = $F('txtrifCodeST');
        this.userGroup = $F('txtrifGroupST');
        this.userEmail = $F('txtrifEmailST');
        this.userSendTo = $F('txtrifEmailDestST');
        this.userOther = $F('txtrifOtherST');
        
        var userOtherEscaped = this.userOther;
        userOtherEscaped = replaceCharsHTML(userOtherEscaped);
        
        var topPart = '<b>' + this.userName + " " + this.userLastName + '</b><br>';
        if (this.userCode != '') {
            topPart += '<b>' + this.rifLblCode + "</b> : " + this.userCode + '<br>';
        }
        
        if (this.userGroup != '') {
            topPart += '<b>' + this.rifLblGroup + "</b> : " + this.userGroup + '<br><br>';
        }
        topPart = replaceCharsHTML(topPart);
        topPart = topPart + userOtherEscaped;
        
        var theSubjectMail = this.subjectMail + " : " + this.quizTitle + " - " + this.userName + " " + this.userLastName;
        var theResultMailTop1 = '<b>' + this.resultMailTop1 + ' : ' + " " + this.quizTitle + " - " + this.userName + " " + this.userLastName + '</b>';
        var theResultMail = topPart + '<br><br><br>' + theResultMailTop1 + this.resultMailTop2 + this.resultMail;
        
        jQuery.ajax({
             type: "GET",
             url: "scripts/send_results.php",
             data : { from: this.userEmail, destinataire: destinataire, sujet: theSubjectMail, resultats: theResultMail, msgok: this.msgEmailOk, msgemailerror: this.msgEmailError },
             dataType : "json", 
             success: function(obj){
                      alert(obj.responseString);
                      
                      if (obj.response == 1) {
                          $('sendtoForm').hide();
                      }
             }
         });
        
    },
    
    resultIdentFormCancel: function(){
        this.hideResultIdentForm();
    },
    
    sendtoFormCancel: function(){
        this.hideSendToForm();
    },
    
    updateHTMLVersion: function(){
        var n = new Date();
        var theMonth;
        var theDay;
        var theMinutes;

        theMonth = (n.getMonth()+1);
        theDay = n.getDate();
        theMinutes = n.getMinutes();

        if (theMonth < 10)
            theMonth = "0" + theMonth;

        if (theDay < 10)
            theDay = "0" + theDay;

        if (theMinutes < 10)
            theMinutes = "0" + theMinutes;


        this.resultHTMLVersion = '';
        this.resultHTMLVersion += '<strong><span id="printtitle">' + this.quizTitle + '</span></strong>&nbsp;&nbsp;&nbsp;' + n.getFullYear() + '-' + theMonth + '-' + theDay + '&nbsp;' + n.getHours() + 'h' + theMinutes + '<br /><br />';

        if (this.userLastName && !this.userName)
            this.resultHTMLVersion += '<strong>' + this.rifLblLastName + ' :  ' + this.userLastName + '</strong><br />';

        if (this.userLastName && this.userName)
            this.resultHTMLVersion += '<strong>' + this.rifLblLastName + ',&nbsp;' + this.rifLblName + '</strong> : ' + this.userLastName + ',&nbsp;' + this.userName + '<br />';

        if (!this.userLastName && this.userName)
            this.resultHTMLVersion += '<strong>' + this.rifLblName + ' :  ' + this.userName + '</strong><br />';

        if (this.userGroup)
            this.resultHTMLVersion += '<strong>' + this.rifLblGroup + '</strong> : ' + this.userGroup + '<br />';

        if (this.userCode)
            this.resultHTMLVersion += '<strong>' + this.rifLblCode + '</strong> : ' + this.userCode + '<br />';

        if (this.userEmail)
            this.resultHTMLVersion += '<strong>' + this.rifLblEmail + '</strong> : ' + this.userEmail + '<br />';

        if (this.userOther)
            this.resultHTMLVersion += '<strong>' + this.rifLblOther + '</strong> : ' + this.userOther + '<br />';
        
        this.resultHTMLVersion += '<br /><hr><br /><br />';
        
        this.resultHTMLVersion += '<strong>' + $('resulttitle').innerHTML + '</strong><br /><br />';


        if(this.answerTimerEnabled){
            this.resultHTMLVersion += '<table cellspacing="0" cellpadding="0" border="0" class="resultheader" id="resultheaderWtimer">' + $('resultheaderWtimer').innerHTML + '</table>';
            this.resultHTMLVersion += $('resultcontentWtimer').innerHTML;
        }
        else{
            this.resultHTMLVersion += '<table cellspacing="0" cellpadding="0" border="0" class="resultheader" id="resultheaderWOtimer">' + $('resultheaderWOtimer').innerHTML + '</table>';
            this.resultHTMLVersion += $('resultcontentWOtimer').innerHTML;
        }


        for($i = 0;$i < this.pages.length;$i++){
            var currPage = this.pages[$i];

            var pageNumber = $i + 1;
            var triesCount = '-';
            var answerTime = '-';
            var score = '-';
            var status = '-';

            if(currPage.question){
                if (currPage.question.triesCount > 0){
                    var nbSeconds = Math.round(this.pageTimer[$i] / 100);  //Data in seconds
                    var minVar = Math.floor(nbSeconds / 60);  // The minutes
                    var secVar = nbSeconds % 60;  // The balance of seconds

                    if (secVar >= 10)
                        answerTime = minVar + ":" + secVar;
                    else
                        answerTime = minVar + ":0" + secVar;
                }

                triesCount = currPage.question.triesCount;

                if (this.decimalSymbol == 0)
                    score = changeDecimalSymbol(currPage.question.currentScore,",") + ' / ' + changeDecimalSymbol(currPage.question.ponderation,",");
                else
                    score = currPage.question.currentScore + ' / ' + currPage.question.ponderation;

                status = currPage.question.status;
            }

            //IE
            var stringReplace = "<a href=\"" + "javascript:top.ccdmd.nq4.pageGoto(" + $i + ")\">";
            this.resultHTMLVersion = this.resultHTMLVersion.replace(stringReplace,"");

            stringReplace = "</a>";
            this.resultHTMLVersion = this.resultHTMLVersion.replace(/stringReplace/g,"");


            //firefox
            stringReplace = "<a href=\"" + "javascript:top.ccdmd.nq4.pageGoto(" + $i + ")\">";
            this.resultHTMLVersion = this.resultHTMLVersion.replace(stringReplace,"");

            stringReplace = "</a>";
            this.resultHTMLVersion = this.resultHTMLVersion.replace(/stringReplace/g,"");
        }
    },
            	      
    resultPrint: function(){
        this.showResultIdentForm();
    },
    
    resultSend: function () {
        this.showSendToForm();
    },
    
    timerFinish: function(){
       this.pageNext(false);
    },
    
    onunload: function(){
        return this.msgUnonload;
    }
});

function ajusterWrapperAll() {
	jQuery("#wrapperallcontentwrapper").height('0px');
		
	var wrapperHeight = jQuery(document).height();
	
	if (jQuery(window).height() > wrapperHeight) {
		wrapperHeight = jQuery(window).height();
	}
	
	if (jQuery("#contentwrapper").outerHeight(true) > wrapperHeight) {
		wrapperHeight = jQuery("#contentwrapper").outerHeight(true);
	}
			
	if (jQuery("#quizpage").outerHeight(true) > wrapperHeight) {
		wrapperHeight = jQuery("#quizpage").outerHeight(true);
	}
		
  jQuery("#wrapperallcontentwrapper").height(wrapperHeight + 'px');  
}

function findHHandWW() {
    var srcImageHS = top.ccdmd.nq4.imagePathHS;
    var imgLoadedProp = this.width / this.height;

    if (this.width > top.ccdmd.nq4.imgMaxWidth){
        var newWidth = top.ccdmd.nq4.imgMaxWidth;
        var newHeight = parseInt(newWidth / imgLoadedProp);

        var newInnerHTMLImg = "<a href=\"" + srcImageHS + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageOnPageId\" border=\"0\" src=\"" + srcImageHS + "\" style=\"width: " + newWidth + "px; height: " + newHeight + "px\">" + "</a>";
        $('imageContainer').innerHTML = newInnerHTMLImg;
    }
    else {
         $('imageContainer').innerHTML = "<a href=\"" + srcImageHS + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageOnPageId\" border=\"0\" src=\"" + srcImageHS + "\">" + "</a>";
    }

    $('imageContainer').show();

    return true;
}

function findHHandWWFeedback() {
    var srcImageHS = top.ccdmd.nq4.imagePathHS;
    var imgLoadedProp = this.width / this.height;
    
    if (this.width > top.ccdmd.nq4.imgFeedbackMaxWidth){
        var newWidth = top.ccdmd.nq4.imgFeedbackMaxWidth;
        var newHeight = parseInt(newWidth / imgLoadedProp);

        var newInnerHTMLImg = "<a href=\"" + srcImageHS + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageOnPageId\" border=\"0\" src=\"" + srcImageHS + "\" style=\"width: " + newWidth + "px; height: " + newHeight + "px\">" + "</a>";
        jQuery('#idRetroMultimedia').html(newInnerHTMLImg);
    }
    else {
         var newInnerHTMLImg = "<a href=\"" + srcImageHS + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageOnPageId\" border=\"0\" src=\"" + srcImageHS + "\">" + "</a>";
         jQuery('#idRetroMultimedia').html(newInnerHTMLImg);
    }

    //in case of long image load, does not go right place the first time (in this.validate();)
    var element = document.getElementById('feedback');
    element.scrollIntoView(true);
    
    return true;
}

function findHHandWWLexique() {	  	
    var srcImageHS = top.ccdmd.nq4.imagePathHS;
    var imgLoadedProp = this.width / this.height;
        
    if (this.width > top.ccdmd.nq4.lexiqueImgMaxWidth){    	      	
        var newWidth = top.ccdmd.nq4.lexiqueImgMaxWidth;
        var newHeight = parseInt(newWidth / imgLoadedProp);
                       
        if (newHeight > top.ccdmd.nq4.lexiqueImgMaxHeight) {        	        	            	        	
        	newHeight = top.ccdmd.nq4.lexiqueImgMaxHeight;
        	var newInnerHTMLImg = "<a href=\"" + srcImageHS + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageLexique\" class=\"imglexique\" src=\"" + srcImageHS + "\" style=\"height: " + newHeight + "px\">" + "</a>";
        }
        else {
        	var newInnerHTMLImg = "<a href=\"" + srcImageHS + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageLexique\" class=\"imglexique\" src=\"" + srcImageHS + "\" style=\"width: " + newWidth + "px; height: " + newHeight + "px\">" + "</a>";	        	
        }                                                
    }
    else if (this.height > top.ccdmd.nq4.lexiqueImgMaxHeight) {
    		newHeight = top.ccdmd.nq4.lexiqueImgMaxHeight;
    		
    		var newInnerHTMLImg = "<a href=\"" + srcImageHS + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageLexique\" class=\"imglexique\" src=\"" + srcImageHS + "\" style=\"height: " + newHeight + "px\">" + "</a>";    		
    }
    else {    		     	
        var newInnerHTMLImg = "<a href=\"" + srcImageHS + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageLexique\" class=\"imglexique\" src=\"" + srcImageHS + "\">" + "</a>";         
    }
    
    
    newInnerHTMLImg = "<div style='width:200px;height:15px;background-image:none'></div>" + newInnerHTMLImg + "<div style='width:200px;height:40px;background-image:none'></div>";
            
    jQuery("#indicewrapper div:first").addClass('indicelexiquecontent');
    $$('#indicewrapper div')[0].update(newInnerHTMLImg);    
    
    return true;
}


function findHHandWWLexiquePage() {	
  var imgLoadedProp = this.width / this.height;
            
	if (this.width > top.ccdmd.nq4.lexiqueImgMaxWidth){    	      	
			var newWidth = top.ccdmd.nq4.lexiqueImgMaxWidth;
			var newHeight = parseInt(newWidth / imgLoadedProp);
										 
			if (newHeight > top.ccdmd.nq4.lexiqueImgMaxHeight) {        	        	            	        	
				newHeight = top.ccdmd.nq4.lexiqueImgMaxHeight;
				top.ccdmd.nq4.lexiquePageImg.push([[this.src], ['0'], [newHeight], [this.localisation], [this.dataimagename]]);				
			}
			else {
				top.ccdmd.nq4.lexiquePageImg.push([[this.src], [newWidth], [newHeight], [this.localisation], [this.dataimagename]]);					        	
			}                                                
	}
	else if (this.height > top.ccdmd.nq4.lexiqueImgMaxHeight) {
			newHeight = top.ccdmd.nq4.lexiqueImgMaxHeight;
			top.ccdmd.nq4.lexiquePageImg.push([[this.src], ['0'], [newHeight], [this.localisation], [this.dataimagename]]);			    		
	}
	else {    		  
		top.ccdmd.nq4.lexiquePageImg.push([[this.src], ['0'], ['0'], [this.localisation], [this.dataimagename]]);   				      
	}
		
						
	jQuery('.imglexique').each(function() {						
		for (var i = 0; i < top.ccdmd.nq4.lexiquePageImg.length; i++) {				
			var theSrcA;
			var theSrcB;
			
			if (top.ccdmd.nq4.lexiquePageImg[i][3] == 1) {
				theSrcA = jQuery(this).attr("data-image-name");
				theSrcB = top.ccdmd.nq4.lexiquePageImg[i][4];					
			}
			else {
				theSrcA = jQuery(this).attr("src");
				theSrcB = top.ccdmd.nq4.lexiquePageImg[i][0];
			}
			
			if (theSrcA == theSrcB && jQuery(this).attr("height") == "0") {					
				if (top.ccdmd.nq4.lexiquePageImg[i][1] != '0') {
					jQuery(this).attr("width", top.ccdmd.nq4.lexiquePageImg[i][1]);
					jQuery(this).attr("height", top.ccdmd.nq4.lexiquePageImg[i][2]);
				}
				else if (top.ccdmd.nq4.lexiquePageImg[i][2] != '0') {
					jQuery(this).attr("height", top.ccdmd.nq4.lexiquePageImg[i][2]);
				}
				else {
					jQuery(this).removeAttr("height");						
				}										
			}					
		}					 
	});
		
	return true;			
}


function gcd(a, b) {
    return (b == 0) ? a : gcd (b, a%b);
}

function adjustVideoSize(maxWidth, widthRation, heightRatio) {
    var arrVideoDimensions = new Array();
    
    var vW = maxWidth;
    var vH;
    
    vH = heightRatio * vW / widthRation;
    vH = parseInt(vH);
    
    arrVideoDimensions[0] = vW;
    arrVideoDimensions[1] = vH;
    
    return arrVideoDimensions;
}

function resizeUnprocessedImagesFeedback() {
    // En lien avec preload images safari / chrome. Voir nq4_buildHTMLElement();     
    jQuery("#feedbackcontent img").each(function(){
        var imgFb = this;
        var srcImgFeedback = jQuery(imgFb).attr("src");
        var fbImgW = jQuery(imgFb).attr("width");
        var fbImgH = jQuery(imgFb).attr("height");
            
        if (fbImgW == "0" || fbImgH == "0") {
            jQuery("#quizpage img").each(function(){
                var imgQuiz = this;
                var srcImgQuiz = jQuery(imgQuiz).attr("src");
                    
                if (srcImgQuiz == srcImgFeedback) {
                    var quizImgW = jQuery(imgQuiz).attr("width");
                    var quizImgH = jQuery(imgQuiz).attr("height");
                        
                    jQuery(imgFb).attr("width", quizImgW);
                    jQuery(imgFb).attr("height", quizImgH);
                }
            });
        }
    });
}

function addHighSlideToOtherImages(section) {
    if (section == 'page') {
       // imagepart, choix de réponses.
       jQuery("#question #idStrChoices img").each(function() {
           if (jQuery(this).parent().hasClass("addedHS") == false) {
               jQuery(this).wrap('<a onclick="return hs.expand(this)" class="highslide addedHS" href="' + jQuery(this).attr('src') + '"></a>');
               jQuery(this).css('border', '0px');
           }
       });
       
       // multiple answers, choix de réponses.
       jQuery(".questionChoice img").each(function() {
           if (jQuery(this).parent().hasClass("addedHS") == false) {
               jQuery(this).wrap('<a onclick="return hs.expand(this)" class="highslide addedHS" href="' + jQuery(this).attr('src') + '"></a>');
               jQuery(this).css('border', '0px');
           }
       });
       
       // association, colonne de gauche,
       jQuery(".liAssLabel img").each(function() {
           if (jQuery(this).parent().hasClass("addedHS") == false) {
               jQuery(this).wrap('<a onclick="return hs.expand(this)" class="highslide addedHS" href="' + jQuery(this).attr('src') + '"></a>');
               jQuery(this).css('border', '0px');
           }
       });
       
       // classement, image des contenants.
       jQuery(".tagImgStyle1 div img").each(function() {
           if (jQuery(this).parent().hasClass("addedHS") == false) {
               jQuery(this).wrap('<a onclick="return hs.expand(this)" class="highslide addedHS" href="' + jQuery(this).attr('src') + '"></a>');
               jQuery(this).css('border', '0px');
           }
       });
       
       jQuery(".tagImgStyle2 div img").each(function() {
           if (jQuery(this).parent().hasClass("addedHS") == false) {
               jQuery(this).wrap('<a onclick="return hs.expand(this)" class="highslide addedHS" href="' + jQuery(this).attr('src') + '"></a>');
               jQuery(this).css('border', '0px');
           }
       });
       
       
       // page de section, if leftovers...
       if (jQuery(this).parent().hasClass("addedHS") == false) {
          jQuery("#imageOnPageId").wrap('<a onclick="return hs.expand(this)" class="highslide addedHS" href="' + jQuery('#imageOnPageId').attr('src') + '"></a>');
       }
    }         
    else if (section == 'feedback') {
         //association, feedback + solution.
         jQuery(".feedbackTable img").each(function() {
             if (jQuery(this).parent().hasClass("addedHS") == false) {
                 jQuery(this).wrap('<a onclick="return hs.expand(this)" class="highslide addedHS" href="' + jQuery(this).attr('src') + '"></a>');
                 jQuery(this).css('border', '0px');
             }
         });
    }
}


function initLexiqueVariantes() {				
	var expressionsAllPM = new Array();
	var expressionsFromVariantesObj = [];
	
	if (lexiqueObj == undefined) {
		// Pas de lexique	
	}
	else {						
		jQuery.each(lexiqueObj.elements, function(j) {									
			var expressionsFromVariantes = new Array();			
			var lexiqueType;
			var lexiqueContenu;
			var lexiqueLocalisation;		
				
			jQuery.each(lexiqueObj.elements[j], function(key, val) {							
				switch (key)
				{								
					case "expression":																																							
						expressionsAllPM.push(val);					
					break;
					
					case "variantes":
						if (val != "") {
							var arrVariantes = val.split("||");	
							
							for (var x = 0; x < arrVariantes.length; x++) {		
								expressionsFromVariantes.push(arrVariantes[x]);																		
								expressionsAllPM.push(arrVariantes[x]);						
							}
						}
					break;
					
					case "type":
						lexiqueType = val;
					break;
					
					case "contenu":
						lexiqueContenu = val;					
					break;
					
					case "localisation":
						lexiqueLocalisation = val;
					break;
				}												
			});
			
			for (var i = 0; i < expressionsFromVariantes.length; i++) {								
				lexiqueObj.elements.push({
					"expression": expressionsFromVariantes[i].toLowerCase(),				
					"variantes": "",
					"type": lexiqueType,			
					"contenu": lexiqueContenu,
					"localisation": lexiqueLocalisation,
					"is_variante": 1,
				});	
			}
		});	
			
			
		// AVANT 2014-05-22
		/*
		// Ajustement pour pas avoir d'expressions exact match avec plusieurs mots dans le contenu d'une expression.
		// Autant pour les expressions que pour les variantres.
		for (var i = 0; i < expressionsAllPM.length; i++) {						
			var newExpressionsAllPM = expressionsAllPM[i].replace(/ /gi, "<span> </span>");
			newExpressionsAllPM = newExpressionsAllPM.replace(/&nbsp;/gi, "<span>&nbsp;</span>");
							
			jQuery.each(lexiqueObj.elements, function(j) {		
				jQuery.each(lexiqueObj.elements[j], function(key, val) {
					if (key == "contenu") {												
						var regex = new RegExp("\\b" + escapeRegExp(expressionsAllPM[i]) + "\\b", "g"); // fonctionnera pas si met "gi" car si première lettre en majuscule comme expression et le mot se trouve dans le contenu (minuscule), ce mot dans le contenu sera remplacé par le mot de l'expression (première lettre majuscule).															
						lexiqueObj.elements[j].contenu = lexiqueObj.elements[j].contenu.replace(regex, newExpressionsAllPM);
												
						regex = new RegExp("\\b" + escapeRegExp(capitaliseFirstLetter(expressionsAllPM[i])) + "\\b", "g");															
						lexiqueObj.elements[j].contenu = lexiqueObj.elements[j].contenu.replace(regex, newExpressionsAllPM);
												
						regex = new RegExp("\\b" + escapeRegExp(expressionsAllPM[i].toLowerCase()) + "\\b", "g");															
						lexiqueObj.elements[j].contenu = lexiqueObj.elements[j].contenu.replace(regex, newExpressionsAllPM);												
					}
				});
			});
		}
		*/
		
		
		// Enlever les doublons
		var arrExpressionsLexique = new Array();
		
		jQuery.each(lexiqueObj.elements, function(j) {				
			jQuery.each(lexiqueObj.elements[j], function(key, val) {
				if (key == "expression") {
					arrExpressionsLexique.push(val);
				}
			});
		});
		
		jQuery.each(lexiqueObj.elements, function(j) {				
			jQuery.each(lexiqueObj.elements[j], function(key, val) {
				if (key == "expression") {
					for (var i = 0; i < arrExpressionsLexique.length; i++) {		
						if (val.toLowerCase() == arrExpressionsLexique[i].toLowerCase() && i != j) {												
							lexiqueObj.elements[i] = [];
						}
					}
				}					
			});
		});
								
		/*
		jQuery.each(lexiqueObj.elements, function(j) {				
			jQuery.each(lexiqueObj.elements[j], function(key, val) {
				if (key == "expression") {
					alert("after = " + val);
				}
			});
		});	
		*/
	}	
}


function initLexiqueGeneral() {		
	var strToEvaluate;
	var lexiqueStr;
				
	strToEvaluate = jQuery("#pageTitle").html();	 				
	lexiqueStr = evalStringForLexique(strToEvaluate);	
	jQuery("#pageTitle").html(lexiqueStr);
	
		
	strToEvaluate = jQuery("#textGuidelinequiz").html();	
	lexiqueStr = evalStringForLexique(strToEvaluate);	
	jQuery("#textGuidelinequiz").html(lexiqueStr);
	
		
	strToEvaluate = jQuery("#statement").html();			
	lexiqueStr = evalStringForLexique(strToEvaluate);	
	jQuery("#statement").html(lexiqueStr);								
}


function evalStringForLexique(strToEvaluate) {
	var arrMots = new Array();
	var arrMotsPreEvaluate = new Array();
	var strLexique = '';
	var motFiltre = '';
	var motNow;
	var goodToEvaluate = true;
	var arrLexiqueExpressionPlusieursMots = new Array();
	var arrLexiqueExpressionPlusieursMotsHTML = new Array();
	var arrLexiqueExpressionsAll = new Array();
	
				
	if (strToEvaluate == '' || strToEvaluate == undefined) {
		goodToEvaluate = false;
	}
	
	if (lexiqueObj == undefined) {		
		goodToEvaluate = false;
	}
	
	
	if (goodToEvaluate == true) {	
		//alert("strToEvaluate = " + strToEvaluate);
		strToEvaluate = strToEvaluate.replace(/«/gi, 'rep%«rep% ');
		strToEvaluate = strToEvaluate.replace(/»/gi, ' rep%»rep%');
		strToEvaluate = strToEvaluate.replace(/&nbsp;/gi, ' ');
		strToEvaluate = strToEvaluate.replace(/<br\/>/gi, ' <br>');
		strToEvaluate = strToEvaluate.replace(/<br \/>/gi, ' <br>');
		strToEvaluate = strToEvaluate.replace(/<br>/gi, ' <br>');
		strToEvaluate = replaceCharsHTML(strToEvaluate);		
		
		arrMots = strToEvaluate.split(" ");
															
		for (var i = 0; i < arrMots.length; i++) {		
			if (jQuery.trim(arrMots[i]) != '') {
				var foundSomething = false;
				var tempMotFiltre2 = '';
								
				motFiltre = arrMots[i];
				motNow = arrMots[i];
																
				// Remplace lest balises html (exemple <p></p> et les caractères de ponctuation
				motFiltre = motFiltre.replace(/<([^>]+)>/gi, '');
				motFiltre = motFiltre.replace(/[.,!¿?();]/gi, '');
																							
				jQuery.each(lexiqueObj.elements, function(j) {					
					var foundLexique = false;	
					var foundPlusieursMots = false;					
					var motFiltrePlus = '';
					var motFiltrePlusieurs = '';
					var lexiqueType = '';
					var lexiqueContenu = '';
					var lexiqueLocalisation = '';
					var lexiqueHTML = '';
						
					// Expressions
					jQuery.each(lexiqueObj.elements[j], function(key, val) {																		
						if (key == 'expression') {							
							val = replaceCharsHTML(val);
																																			
							if (motFiltre.toLowerCase() == val.toLowerCase()) {																
								// Un seul mot
								foundLexique = true;		
								foundPlusieursMots = false;
							}
							else {
								// Plusieurs mots ?
								var arrMotsLexique = val.split(" ");
								var indexMot = i;								
								
								if (arrMotsLexique.length > 1) {
									foundPlusieursMots = true;
									
									for (var k = 0; k < arrMotsLexique.length; k++) {																				
										if (foundPlusieursMots == true) {																																	
											if (indexMot >= arrMots.length) {																																				
												foundPlusieursMots = false;
											}
											else {																																				
												motFiltrePlusieurs = arrMots[indexMot];
																																			
												// Remplace les balises html (exemple <p></p> et les caractères de ponctuation
												motFiltrePlusieurs = motFiltrePlusieurs.replace(/<([^>]+)>/gi, '');
												motFiltrePlusieurs = motFiltrePlusieurs.replace(/[.,!¿?();]/gi, '');
																																		
												if (arrMotsLexique[k].toLowerCase() == motFiltrePlusieurs.toLowerCase()) {																										
													if (motFiltrePlus == '') {														
														motFiltrePlus = motFiltrePlusieurs;
													}
													else {														
														motFiltrePlus = motFiltrePlus + ' ' + motFiltrePlusieurs;														
													}
																																							
													indexMot++;
												}
												else {
													foundPlusieursMots = false;
												}											
											}
										}
									}																		
								}								
							}														
						}
						else if (foundLexique == true || foundPlusieursMots == true) {
							if (foundPlusieursMots == true) {
								motFiltrePlusG = motFiltrePlus;																							
							}
							
							switch (key)
							{
								case "type":
									lexiqueType = val;
								break;
								
								case "contenu":
									lexiqueContenu = val;
									lexiqueContenu = lexiqueContenu.replace(/'/g, "\\'");
									lexiqueContenu = lexiqueContenu.replace(/"/g, "&#34;");
								break;
								
								case "localisation":
									lexiqueLocalisation = val;
								break;
							}														
						}					
					});
					
															
					if (foundLexique == true) {																		
						var tempMotFiltre = motFiltre;
												
						motFiltre = motFiltre.replace(/'/g, "\\'");
						motFiltre = motFiltre.replace(/"/g, "&quot;");
																												
						if (lexiqueType == "lien") {
							lexiqueHTML = "<a class=\"lexique_link\" href=\"#\" onclick=\"javascript:window.open('" + lexiqueContenu + "'); return false;\">" + motFiltre + "</a>";											
						}
						else {														
							lexiqueHTML = "<a class=\"lexique_link\" href=\"javascript:top.ccdmd.nq4.showLexique('" + motFiltre + "', '" + lexiqueType + "', '" + lexiqueLocalisation + "')\">" + tempMotFiltre + "</a>";	
						}
						
						var regex = new RegExp(escapeRegExp(tempMotFiltre), "gi");
						arrMots[i] = arrMots[i].replace(regex, lexiqueHTML);					
						
						arrLexiqueExpressionsAll.push(tempMotFiltre);
						
						foundSomething = true;
					}
					else if (foundPlusieursMots == true) {												
						var tempMotFiltre = jQuery.trim(motFiltrePlus);						
						
						motFiltrePlus = motFiltrePlus.replace(/'/g, "\\'");
						motFiltrePlus = motFiltrePlus.replace(/"/g, "&quot;");
																												
						if (lexiqueType == "lien") {
							lexiqueHTML = "<a class=\"lexique_link\" href=\"#\" onclick=\"javascript:window.open('" + lexiqueContenu + "'); return false;\">" + motFiltrePlus + "</a>";											
						}
						else {
							lexiqueHTML = "<a class=\"lexique_link\" href=\"javascript:top.ccdmd.nq4.showLexique('" + motFiltrePlus + "', '" + lexiqueType + "', '" + lexiqueLocalisation + "')\">" + tempMotFiltre + "</a>";														
						}												
																		
						arrLexiqueExpressionPlusieursMots.push(tempMotFiltre);
						arrLexiqueExpressionPlusieursMotsHTML.push(lexiqueHTML);
						
						arrLexiqueExpressionsAll.push(tempMotFiltre);
						
						foundSomething = true;
					}
					
					tempMotFiltre2 = tempMotFiltre;
				});
				
				
				if (foundSomething == true) {										
					for (var y = 0; y < arrLexiqueExpressionPlusieursMots.length; y++) {
						if (arrLexiqueExpressionPlusieursMots[y].indexOf(tempMotFiltre2) != -1) {
							// Oui, on retrouve le mot " + tempMotFiltre2 + " dans " + arrLexiqueExpressionPlusieursMots[y]
							var tempStrLexique = strLexique + tempMotFiltre2;
														
							if (tempStrLexique.indexOf(arrLexiqueExpressionPlusieursMots[y]) != -1) {
								// On ne modifie pas le mot (mettre hyperlien) car on a un mot dans une expression à plusieurs mots.													
								arrMots[i] = motNow;											
							}																		
						}
					}
				}
				
								
				if ((i + 1) < arrMots.length) {															
					strLexique += arrMots[i] + " ";
				}			
				else {
					strLexique += arrMots[i];
				}												
			}
		}

						
		if (arrLexiqueExpressionPlusieursMots.length > 0) {
			var arrTempExpressions = new Array();
			var arrTempExpressions2 = new Array();
			var arrExpressionsSensitive = new Array();
			
			for (var i = 0; i < arrLexiqueExpressionPlusieursMots.length; i++) {
				if (jQuery.inArray(arrLexiqueExpressionPlusieursMots[i], arrTempExpressions) == -1) {																				
					for (var j = 0; j < arrLexiqueExpressionPlusieursMots.length; j++) {						
						if (arrLexiqueExpressionPlusieursMots[i].toLowerCase() == arrLexiqueExpressionPlusieursMots[j].toLowerCase() && i != j) {																									
							arrExpressionsSensitive[i] = arrLexiqueExpressionPlusieursMots[i];														
						}
					}																			
				}
				
				arrTempExpressions.push(arrLexiqueExpressionPlusieursMots[i]);								
			}
			
			
			for (var i = 0; i < arrLexiqueExpressionPlusieursMots.length; i++) {
				if (jQuery.inArray(arrLexiqueExpressionPlusieursMots[i], arrTempExpressions2) == -1) {										
					var caseSensitive = false; 
					
					for (var j = 0; j < arrExpressionsSensitive.length; j++) {
						if (arrExpressionsSensitive[j]) {						
							if (arrLexiqueExpressionPlusieursMots[i].toLowerCase() == arrExpressionsSensitive[j].toLowerCase()) {
								caseSensitive = true;																	
							}
						}
					}	
					
					
					if (caseSensitive == true) {																	
						var regex = new RegExp(escapeRegExp(arrLexiqueExpressionPlusieursMots[i]), "g");
					}
					else {
						var regex = new RegExp(escapeRegExp(arrLexiqueExpressionPlusieursMots[i]), "gi");
					}
					
					strLexique = strLexique.replace(regex, arrLexiqueExpressionPlusieursMotsHTML[i]);	
					
					arrTempExpressions2.push(arrLexiqueExpressionPlusieursMots[i]);		
				}
			}
		}		
								
		
		strLexique = strLexique.replace(/rep%«rep% /gi, '«');
		strLexique = strLexique.replace(/ rep%»rep%/gi, '»');
		
		return strLexique;		
	}
	else {
		return strToEvaluate;
	}		
}


function sortLexique() {
	var arrSortedExpressionsLexique = new Array();
	
	jQuery.each(lexiqueObj.elements, function(i) {	
		jQuery.each(lexiqueObj.elements[i], function(key, val) {
			if (key == 'expression') {
				arrSortedExpressionsLexique.push(val.toLowerCase());
			}
		});
	});
	
	arrSortedExpressionsLexique.sort(); 
	
	return arrSortedExpressionsLexique;		
}


function escapeRegExp(string){
    return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}


function capitaliseFirstLetter(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}


function menuPageEndDrag() {
	var elementDrag = jQuery("#menupageswrapper");
	var position = elementDrag.position();
	
	if (position.top < 0) {
		jQuery("#menupageswrapper").css({top: '0px'});
	}
}


function calculerDimensionsMenuPages() {									
	if (jQuery(window).width() < 1550) {
		if (jQuery("#menupageswrapper").width() > 290) {					
			jQuery("#menupageswrapper").css('width', '290px');
			jQuery("#menupageswrapper").css('max-width', '290px');
		}		
	}
	else {
		jQuery("#menupageswrapper").css('width', '');
		jQuery("#menupageswrapper").css('max-width', '500px');
	}
	
	var hMenuPagesHeight = jQuery(window).height();
	hMenuPagesHeight = hMenuPagesHeight - 70;
	
	if (hMenuPagesHeight > 550) {
		hMenuPagesHeight = 550;
	}
	
	jQuery("#menupages").css('max-height', hMenuPagesHeight + 'px');
	
	
	var wMenuPagesWidth = jQuery("#menupageswrapper").width() - 50;
  jQuery("#menupageshandle").css('width', wMenuPagesWidth + 'px');      
}


function addslashes(str) {
    str=str.replace(/\'/g,'\\\'');
    str=str.replace(/\"/g,'\\"');
    str=str.replace(/\\/g,'\\\\');
    str=str.replace(/\0/g,'\\0');
    return str;
}


function getNewNetquiz(){
    if (top.ccdmd == null) top.ccdmd = {};
    top.ccdmd.nq4 = new Netquiz();
    return top.ccdmd.nq4;
}

var labelLetters = new Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");

function getLetterLabel(i){
    var label = '';
    
    if(i < 27){
        label = labelLetters[i - 1];
    }else if((i % 26) == 0) {
        label = labelLetters[Math.floor(i  / 26) - 2] + labelLetters(26);
    }else{
        label = labelLetters[Math.floor(i  / 26) - 1] + labelLetters(i % 26);
    }
    
    return label;
}

function NQ4TimerFinish(){
    top.ccdmd.nq4.timerFinish();
}
function nq4_onImgsPreloadFinish(){
    top.ccdmd.nq4.begin();
}
function nq4_onConnectSuccess(transport){
    top.ccdmd.nq4.connectSuccess(transport);
}
function nq4_onAuthSuccess(transport){
    top.ccdmd.nq4.authSuccess(transport);
}
function nq4_auth(){
    top.ccdmd.nq4.auth();
}

function closeIndice(){
    indiceWindow.close();
}

function closeFeedback(){
    feedbackWindow.close();
}

function toggleMenuPages(openOrClose){
	if (openOrClose == '1') {
		var screenVisibleW = document.getElementById('pagewrapper').offsetWidth / 2;
		var divW = document.getElementById('menupageswrapper').offsetWidth / 2;
		
		var windowLeft = screenVisibleW - divW;
		var windowTop = 120;
		
		if (divW > 0) {
			windowLeft = screenVisibleW;
		}
										
		if (jQuery(window).height() <= 600 || isMobile.any()) {
			windowTop = 5;        	        	
		}
								
		windowLeft = windowLeft - (jQuery("#menupageswrapper").width() / 2);
		
		jQuery("#menupageswrapper").css('left', windowLeft + 'px');
		jQuery("#menupageswrapper").css('top', windowTop + 'px'); 
		
		calculerDimensionsMenuPages();
		jQuery("#menupageswrapper").show();
	}
	else {
		jQuery("#menupageswrapper").hide();
	}	
}

function nq4_buildHTMLElement(sTagName,aAtts){
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
                                                                                             
                    var src = jQuery(img).attr("src");
                    var currentPage = top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex];
                    
                    if (top.ccdmd.nq4.recalculerDimensionsAssRank.length > 0) {
                        if (top.ccdmd.nq4.recalculerDimensionsAssRank[0] == "ranking") {
                            currentPage.question.reBalanceLists(top.ccdmd.nq4.recalculerDimensionsAssRank[1], top.ccdmd.nq4.recalculerDimensionsAssRank[2], iNewWidth, iNewHeight, src);
                        }
                        
                        if (top.ccdmd.nq4.recalculerDimensionsAssRank[0] == "association") {
                            currentPage.question.reBalanceLists(top.ccdmd.nq4.recalculerDimensionsAssRank[1], top.ccdmd.nq4.recalculerDimensionsAssRank[2], top.ccdmd.nq4.recalculerDimensionsAssRank[3], iNewWidth, iNewHeight, src);
                        }
                    }
                    
                    if (top.ccdmd.nq4.recalculerDimensionsDamier == true) {
                         currentPage.question.setWHImgSolution(iNewWidth, iNewHeight, src);
                    }
                    
                    
                    // Juste pour être certain que l'image est correcte.
                    jQuery('#scrollwrapper img').each(function(){
                    		if (jQuery(this).attr("width") == "0" || jQuery(this).attr("height") == "0") {
                    			if (src == jQuery(this).attr("src")) {
                    				jQuery(this).attr("width", iNewWidth);
                    				jQuery(this).attr("height", iNewHeight);
                    			}
                    		}
                    	
                    });
                });
    }
    
    return oElement;

}

function nq4_buildImageObject(sFileName,iMaxWidth,iMaxHeight,sImgFolder){
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
    
    if(iMaxWidth && oImg.width > iMaxWidth){
        iNewWidth = iMaxWidth;
        iNewHeight = nq4_b2(oImg.width,oImg.height,iMaxWidth);
    }else{
        iNewWidth = oImg.width;
        iNewHeight = oImg.height;
    }
    
    if(iMaxHeight && iNewHeight > iMaxHeight){
        iNewWidth = nq4_b2(iNewHeight,iNewWidth,iMaxHeight);
        iNewHeight = iMaxHeight;
    }
    
    iNewWidth = Math.round(iNewWidth);
    iNewHeight = Math.round(iNewHeight);
                    
    return nq4_buildHTMLElement('img',{src:sImgSrc, width:iNewWidth, height:iNewHeight, datamw:iMaxWidth, datamh:iMaxHeight});
}

function nq4_b2(a1,a2,b1){
    return (a2 * b1) / a1;
}

function changeDecimalSymbol(number, symbol){
    var numberToReplace = number + '';
    numberToReplace = numberToReplace.replace(".",symbol);
    
    return numberToReplace;
}

function InitializeTimer()
{
    if(this.secs){
        if (gNQ4.currentPageIndex < gNQ4.numberPagesQuiz)
            gNQ4.pageTimer[gNQ4.currentPageIndex] = gNQ4.pageTimer[gNQ4.currentPageIndex] + this.secs;
    }

    this.secs = 0;
    StopTheClock();
    StartTheTimer();
}

function StopTheClock()
{
    if(this.timerRunning)
        clearTimeout(this.timerID);
    this.timerRunning = false;
}

function StartTheTimer()
{
    this.secs = this.secs + 1;
    this.timerRunning = true;
    this.timerID = self.setTimeout("StartTheTimer()", this.delay);
}

function nq4_entity_decode(str) {
  //Fonction du net. Doit avoir : var ta = document.createElement("textarea"); Si met dans
  //une variable ex : var newString = str.replace(/</g,"&lt;").replace(/>/g,"&gt;");
  //ne fonctionne pas...
  var ta = document.createElement("textarea");
  ta.innerHTML = str.replace(/</g,"&lt;").replace(/>/g,"&gt;");
  return ta.value;
}

function getElementPos(oElement)
{
    var oGeo = {x:0, y:0, height:0, width:0};
    
    oGeo.height = oElement.offsetHeight;
    oGeo.width = oElement.offsetWidth;
    if(oGeo.height == 0 && oGeo.width == 0 && typeof oElement.width != 'undefined')
    {
        oGeo.height = oElement.height;
        oGeo.width = oElement.width;
    }
    
    if (oElement.offsetParent)
    {
        while (oElement)
        {
            oGeo.x += oElement.offsetLeft;
            oGeo.y += oElement.offsetTop
            oElement = oElement.offsetParent;
        }
    }
    else if (oElement.x)
    {
        oGeo.x += oElement.x;
        oGeo.y += oElement.y;
    }
    
    return oGeo;
}

function tcals_createElement(nodeName, name){
    //FONCTION REQUISE
    var node;
    
    try {
        node = document.createElement("<"+nodeName+" name="+name+">");
    } catch (e) {
        node = document.createElement(nodeName);
        node.name = name;
    }
    
    return node;
}

function tcals_onunload(){
    //FONCTION REQUISE
    return top.ccdmd.nq4.onunload();
}

function nq4_navbartext_onkeypress(e){
	if (jQuery.isNumeric(jQuery("#navBarTxtPageIndex").val())) {
    var unicode = e.keyCode? e.keyCode : e.charCode;

    if (unicode == '13') {
        var pageIndex = $F('navBarTxtPageIndex') - 1;
        top.ccdmd.nq4.pageGoto(pageIndex);
    }
  }
}


//FONCTIONS DE NETQUIZ3
function getShuffledOrder(N) {
    var J, K, Q = new Array(N);
    for (J = 0; J < N; J++) {
        K = nq4_random(J + 1);
        Q[J] = Q[K];
        Q[K] = J;
    }
    return Q;
}
function nq4_random(N) {
    return Math.floor(N * (Math.random() % 1));
}
function cleanForValid(s){
    var toReturn = s;
    
    toReturn = toReturn.replace(/^\s*|\s*$/g,"");
    toReturn = toReturn.replace(/  */g,' ');
    toReturn = toReturn.replace(/<br \/>/g,'');
    toReturn = toReturn.replace(/<br>*/g,'');
    toReturn = toReturn.replace(/\n*/g,'');
    
    return toReturn;
}

function cleanForValidDictee(s){
    var toReturn = s;
    
    toReturn = toReturn.replace(/^\s*|\s*$/g,"");
    toReturn = toReturn.replace(/  */g,' ');
    toReturn = toReturn.replace(/<br \/>/g,'');
    toReturn = toReturn.replace(/<br>*/g,'');
    
    toReturn = jQuery.trim(toReturn); // Removes also non-breaking spaces
    
    var hasReturnLine = toReturn.match(/\n/g);
    
    if (hasReturnLine !== null){    
       toReturn = toReturn.replace(/\n/g,'<%%%>'); // sera remplacé par <br> dans le feedback.
    }
    else {
       toReturn = toReturn.replace(/\n*/g,'');
    }
    
    return toReturn;
}

var car = new Array(50);
var car0 = new Array(50);

car0 [1] = "%26agrave%3B";
car0 [2] = "%26aacute%3B";
car0 [3] = "%26acirc%3B";
car0 [4] = "%26auml%3B";
car0 [5] = "%26ccedil%3B";
car0 [6] = "%26egrave%3B";
car0 [7] = "%26eacute%3B";
car0 [8] = "%26ecirc%3B";
car0 [9] = "%26euml%3B";
car0 [10] = "%26igrave%3B";
car0 [11] = "%26iacute%3B";
car0 [12] = "%26icirc%3B";
car0 [13] = "%26iuml%3B";
car0 [14] = "%26ntilde%3B";
car0 [15] = "%26ograve%3B";
car0 [16] = "%26oacute%3B";
car0 [17] = "%26ocirc%3B";
car0 [18] = "%26ouml%3B";
car0 [19] = "%26ugrave%3B";
car0 [20] = "%26uacute%3B";
car0 [21] = "%26ucirc%3B";
car0 [22] = "%26uuml%3B";
car0 [23] = "%26Agrave%3B";
car0 [24] = "%26Aacute%3B";
car0 [25] = "%26Acirc%3B";
car0 [26] = "%26Auml%3B";
car0 [27] = "%26Ccedil%3B";
car0 [28] = "%26Egrave%3B";
car0 [29] = "%26Eacute%3B";
car0 [30] = "%26Ecirc%3B";
car0 [31] = "%26Euml%3B";
car0 [32] = "%26Igrave%3B";
car0 [33] = "%26Iacute%3B";
car0 [34] = "%26Icirc%3B";
car0 [35] = "%26Iuml%3B";
car0 [36] = "%26Ntilde%3B";
car0 [37] = "%26Ograve%3B";
car0 [38] = "%26Oacute%3B";
car0 [39] = "%26Ocirc%3B";
car0 [40] = "%26Ouml%3B";
car0 [41] = "%26Ugrave%3B";
car0 [42] = "%26Uacute%3B";
car0 [43] = "%26Ucirc%3B";
car0 [44] = "%26Uuml%3B";
car0 [45] = "%26szlig%3B";
car0 [46] = "%26#171%3B";
car0 [47] = "%26#187%3B";
car0 [48] = "%26quot%3B";
  
function convertir(chaine) {
    var caraca = "";
    var caracb = "";
    for (var i = 1; i < 49; i++)  {
        caraca = car0[i];
        if (chaine.indexOf(caraca) >= 0) {
            caracb = car[i];
            chaine = caractere(chaine, caraca, caracb);
        }
    }
    return(chaine);
}

function caractere(chaine, caraca, caracb) {
    var y = -1;
    var n = chaine.length;
    var chaineNew = chaine;
    var longueur = caraca.length;
    
    while (chaine.indexOf(caraca) >= 0) {
        y = chaine.indexOf(caraca);
        if (y > 0) {
            chaineNew = chaine.substring(0,y) + caracb + chaine.substring(y+longueur, n);
            n = chaineNew.length;
            chaine = chaineNew;
        } else if (y == 0) {
            chaineNew = caracb + chaine.substring(y+longueur, n);
            n = chaineNew.length;
            chaine = chaineNew;
        }
    }
    return(chaine);
}
function trim(s) {
  while (s.substring(0,1) == ' ') {
    s = s.substring(1,s.length);
  }
  while (s.substring(s.length-1,s.length) == ' ') {
    s = s.substring(0,s.length-1);
  }
  return s;
}
function makeArray1(n) {
  this.length = n;
  for (var i = 0; i < n; i++) this[i] = false;
  return this;
}
function makeArray2(n) {
  this.length = n;
  for (var i = 0; i < n; i++) this[i] = "";
  return this;
}
function makeArray3(n) {
  this.length = n;
  for (var i = 0; i < n; i++) this[i] = 0;
  return this;
}
function makeArray4(n) {
  this.length = n;
  for (var i = 0; i < n; i++) this[i] = " ";
  return this;
}
function detectBrowser(){
  var BrowserDetect = {
  init: function () {
    this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
    this.version = this.searchVersion(navigator.userAgent)
      || this.searchVersion(navigator.appVersion)
      || "an unknown version";
    this.OS = this.searchString(this.dataOS) || "an unknown OS";
  },
  searchString: function (data) {
    for (var i=0;i<data.length;i++)  {
      var dataString = data[i].string;
      var dataProp = data[i].prop;
      this.versionSearchString = data[i].versionSearch || data[i].identity;
      if (dataString) {
        if (dataString.indexOf(data[i].subString) != -1)
          return data[i].identity;
      }
      else if (dataProp)
        return data[i].identity;
    }
  },
  searchVersion: function (dataString) {
    var index = dataString.indexOf(this.versionSearchString);
    if (index == -1) return;
    return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
  },
  dataBrowser: [
    {
      string: navigator.userAgent,
      subString: "Chrome",
      identity: "Chrome"
    },
    {   string: navigator.userAgent,
      subString: "OmniWeb",
      versionSearch: "OmniWeb/",
      identity: "OmniWeb"
    },
    {
      string: navigator.vendor,
      subString: "Apple",
      identity: "Safari",
      versionSearch: "Version"
    },
    {
      prop: window.opera,
      identity: "Opera"
    },
    {
      string: navigator.vendor,
      subString: "iCab",
      identity: "iCab"
    },
    {
      string: navigator.vendor,
      subString: "KDE",
      identity: "Konqueror"
    },
    {
      string: navigator.userAgent,
      subString: "Firefox",
      identity: "Firefox"
    },
    {
      string: navigator.vendor,
      subString: "Camino",
      identity: "Camino"
    },
    {    // for newer Netscapes (6+)
      string: navigator.userAgent,
      subString: "Netscape",
      identity: "Netscape"
    },
    {
      string: navigator.userAgent,
      subString: "MSIE",
      identity: "Explorer",
      versionSearch: "MSIE"
    },
    {
      string: navigator.userAgent,
      subString: "Gecko",
      identity: "Mozilla",
      versionSearch: "rv"
    },
    {     // for older Netscapes (4-)
      string: navigator.userAgent,
      subString: "Mozilla",
      identity: "Netscape",
      versionSearch: "Mozilla"
    }
  ],
  dataOS : [
    {
      string: navigator.platform,
      subString: "Win",
      identity: "Windows"
    },
    {
      string: navigator.platform,
      subString: "Mac",
      identity: "Mac"
    },
    {
         string: navigator.userAgent,
         subString: "iPhone",
         identity: "iPhone/iPod"
      },
    {
      string: navigator.platform,
      subString: "Linux",
      identity: "Linux"
    }
  ]

  };
  BrowserDetect.init();

  return BrowserDetect.browser;
}

function rgb2hex(rgb){
         rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
         return "#" +
                ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
                ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
                ("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
}

function hexToRgb(hex) { 
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex); 
    return result ? { 
        r: parseInt(result[1], 16), 
        g: parseInt(result[2], 16), 
        b: parseInt(result[3], 16) 
    } : null; 
} 

function replaceCharsHTML(str) {
    str = str.replace(/&aacute;/g,"á");
    str = str.replace(/&agrave;/g,"à");
    str = str.replace(/&acirc;/g,"â");
    str = str.replace(/&ccedil;/g,"ç");
    str = str.replace(/&eacute;/g,"é");
    str = str.replace(/&egrave;/g,"è");
    str = str.replace(/&ecirc;/g,"ê");
    str = str.replace(/&euml;/g,"ë");
    str = str.replace(/&iacute;/g,"í");
    str = str.replace(/&icirc;/g,"î");
    str = str.replace(/&iuml;/g,"ï");
    str = str.replace(/&ntilde;/g,"ñ");
    str = str.replace(/&oacute;/g,"ó");
    str = str.replace(/&ocirc;/g,"ô");
    str = str.replace(/&uacute;/g,"ú");
    str = str.replace(/&ugrave;/g,"ù");
    str = str.replace(/&ucirc;/g,"û");
    str = str.replace(/&uuml;/g,"ü");
        
    str = str.replace(/&Aacute;/g,"Á");
    str = str.replace(/&Agrave;/g,"À");
    str = str.replace(/&Acirc;/g,"Â");
    str = str.replace(/&Ccedil;/g,"Ç");
    str = str.replace(/&Eacute;/g,"É");
    str = str.replace(/&Egrave;/g,"È");
    str = str.replace(/&Ecirc;/g,"Ê");
    str = str.replace(/&Euml;/g,"Ë");
    str = str.replace(/&Iacute;/g,"Í");
    str = str.replace(/&Icirc;/g,"Î");
    str = str.replace(/&Iuml;/g,"Ï");
    str = str.replace(/&Ntilde;/g,"Ñ");
    str = str.replace(/&Oacute;/g,"Ó");
    str = str.replace(/&Ocirc;/g,"Ô");
    str = str.replace(/&Uacute;/g,"Ú");
    str = str.replace(/&Ugrave;/g,"Ù");
    str = str.replace(/&Ucirc;/g,"Û");
    str = str.replace(/&Uuml;/g,"Ü");

    return str;
}

function encode_utf8(s){
         return unescape( encodeURIComponent( s ) );
}

function decode_utf8(s){
         return decodeURIComponent( escape( s ) );
}
