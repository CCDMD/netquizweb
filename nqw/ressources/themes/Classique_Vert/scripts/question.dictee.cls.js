var QuestionDictee = Class.create({
    sQuestionType: 'DICTEE',
    
    sRepAct: "", //Réponse actuelle
    sBRep: "", //Bonne réponse
    bPoncCompte: true,
    sRetroPos: "",
    sRetroNeg: "",
    iFautePond: 1,
    bCaseSens: true,
    sReponse: "",
    sMsgMotsMOrtho: "",
    sMsgMotsManq: "",
    sMsgMotsTrop: "",
    
    input: null,
    
    currentScore: 0,
    ponderation: 0,
    triesCount: 0,
    status:'',
    statusMenuPages: -1,
    
    initialize: function(quiz, page, questionNb){
        this.quiz = quiz;
        this.page = page;
        this.questionNb = questionNb;
        
        this.status = this.quiz.statusToDo;
    },
    
    display : function(){
        this.input = tcals_createElement('textarea','txtDictee');
        this.input.value = this.sRepAct;
        this.input.style.padding = '2px';
        $('question').update(this.input);
    },

    save: function(){
        this.sRepAct = '';

        if (this.input != null)
            this.sRepAct = this.input.value;

        return this.sRepAct;
    },
    validate: function(){
        bSilent = false;
        this.save();
        this.triesCount++;
        var iPointage = 0;
        var sRetroAAfficher = "";
        var u1 = "<U><FONT COLOR='#b22222'>"
        var u2 = "</FONT></U>"
        //var compar = cleanForValidDictee(this.sBRep);
        
        var tempCompar = this.sBRep.replace(/<br>/g,"<%%%>");
        tempCompar = tempCompar.replace(/<br\/>/g,"<%%%>"); 
        tempCompar = tempCompar.replace(/<br \/>/g,"<%%%>");
        var compar = cleanForValidDictee(tempCompar);
        var lireentree = "";
        
        if(trim(this.sRepAct) == "" && !bSilent){
            if(!bSilent){
                sRetroAAfficher = "<span class=\"Yellow\">" + this.page.incompleteAnswerLabel + "</span><br><br>";
                this.currentScore = 0;
                this.statusMenuPages = -1;
                
                setFeedback(sRetroAAfficher);
                openFeedback();
            }
            
            return 0;
        }
        
        
        lireentree = cleanForValidDictee(this.sRepAct);
        lireentree = lireentree.replace(/\ \ /g, ' ');
        compar = compar.replace(/\ \ /g, ' ');
        
        
        var hasReturnLine = this.sRepAct.match(/\n/g);
    
        if (hasReturnLine !== null){

        }
        else {
           compar = tempCompar.replace(/<%%%>/g," ");
        }
        
        
        // Dans la bonne réponse
        hasReturnLine = tempCompar.match(/<%%%>/g);
        
        if (hasReturnLine !== null){
        
        }
        else {
            lireentree = lireentree.replace(/<%%%>/g," ");
        }
        
        
        if (this.bPoncCompte == false) {
            var lireentreenoponc = lireentree;
            lireentreenoponc = lireentreenoponc.replace(/\,/g, '');
            lireentreenoponc = lireentreenoponc.replace(/\./g, '');
            lireentreenoponc = lireentreenoponc.replace(/\;/g, '');
            lireentreenoponc = lireentreenoponc.replace(/\:/g, '');
            lireentreenoponc = lireentreenoponc.replace(/\!/g, '');
            lireentreenoponc = lireentreenoponc.replace(/\?/g, '');
            lireentreenoponc = lireentreenoponc.replace(/\«/g, '');
            lireentreenoponc = lireentreenoponc.replace(/\»/g, '');
            
            compar = compar.replace(/\,/g, '');
            compar = compar.replace(/\./g, '');
            compar = compar.replace(/\;/g, '');
            compar = compar.replace(/\:/g, '');
            compar = compar.replace(/\!/g, '');
            compar = compar.replace(/\?/g, '');
            compar = compar.replace(/\«/g, '');
            compar = compar.replace(/\»/g, '');
            
            compar = trim(compar) + " ";
            lireentree = lireentreenoponc;
            lireentree = trim(lireentree) + " ";
        } else {
            compar = trim(compar) + " ";
            lireentree = trim(lireentree) + " ";
        }
        compar = escape(compar);
        compar = convertir(compar);
        compar = unescape(compar);
        
        
        if (lireentree.toString() == compar.toString()) {
            if(!bSilent){
                sRetroAAfficher = "<span class=\"Green\">" + this.page.goodAnswerLabel + "</span><br><br>";
                sRetroAAfficher += "<h1 class=\"Retro\">" + this.sRetroPos + "</h1>";
                
                setFeedback(sRetroAAfficher);
                openFeedback();
                this.status = this.quiz.statusCompleted;
                this.statusMenuPages = 1;
                this.currentScore = this.ponderation
            }
            return this.ponderation;
        } else {
            if (this.sRepAct != "") {
            comparNew = compar + " ---fin---";
            lireentree += " ---fin---";
            var nbMots1 = 0; // Trouver le nombre de mots dans le texte de l'usager
            var trouve = 0;
            for (var i = 0; i < lireentree.length; i++) {
                if (trouve == 1) {
                    if (lireentree.charAt(i) == ' ' || lireentree.charAt(i) == '\r' || lireentree.charAt(i) == '\n') {
                        trouve = 0;
                    }
                } else {
                    if (lireentree.charAt(i) != ' ' && lireentree.charAt(i) != '\r' && lireentree.charAt(i) != '\n' && trouve == 0) {
                        nbMots1++;
                        trouve = 1;
                    }
                }
            }
            
            
            if (nbMots1 == 0) {
                if(!bSilent){
                    //retro.showHTML("&nbsp;");
                }
                return 0;
            }
            
            nbMots2 = 0;  // Trouver le nombre de mots dans la reponse
            trouve = 0;
            for (i = 0; i < comparNew.length; i++) {
                if (trouve == 1) {
                    if (comparNew.charAt(i) == ' ' || comparNew.charAt(i) == '\r' || comparNew.charAt(i) == '\n') {
                        trouve = 0;
                    }
                } else {
                    if (comparNew.charAt(i) != ' ' && comparNew.charAt(i) != '\r' && comparNew.charAt(i) != '\n' && trouve == 0) {
                        nbMots2++;
                        trouve = 1;
                    }
                }
            }
            if (nbMots1 > 0) {
                var tab1 = new makeArray4(nbMots1);  // tab1 : texte de l'usager
                var posReturn0 = new makeArray4(nbMots1);
                var indiceTab = 0;
                trouve = 0;
                for (var j = 0; j < lireentree.length-1; j++) {
                    if (lireentree.charAt(j) == ' ' || lireentree.charAt(j) == '\r' || lireentree.charAt(j) == '\n') {
                        if (navigator.userAgent.indexOf('Mac') > 0) {
                            if ((lireentree.charAt(j) == '\n' || lireentree.charAt(j) == '\r') && posReturn0[indiceTab] == "+") posReturn0[indiceTab] = "-";
                            if ((lireentree.charAt(j) == '\n' || lireentree.charAt(j) == '\r') && posReturn0[indiceTab] != "+" && posReturn0[indiceTab] != "-") posReturn0[indiceTab] = "+";
                        } else {
                            if ((lireentree.charAt(j) == '\n') && posReturn0[indiceTab] == "+") posReturn0[indiceTab] = "-";
                            if ((lireentree.charAt(j) == '\n') && posReturn0[indiceTab] != "+" && posReturn0[indiceTab] != "-") posReturn0[indiceTab] = "+";
                        }
                        if (trouve == 1) {
                            if (indiceTab < nbMots1)
                                indiceTab++;
                            else
                                j = lireentree.length+10;  // Pour arreter
                            trouve = 0;
                        }
                    } else {
                        
                        tab1[indiceTab] += lireentree.charAt(j);
                        trouve = 1;
                    }
                }
            }
            if (nbMots2 > 0) {
                var tab2 = new makeArray4(nbMots2)   // tab2 : reponse
                indiceTab = 0;
                trouve = 0;
                for (j = 0; j < comparNew.length-1; j++) {
                if (comparNew.charAt(j) == ' ' || comparNew.charAt(j) == '\r' || comparNew.charAt(j) == '\n') {
                    if (trouve == 1) {
                    if (indiceTab < nbMots2)
                        indiceTab++;
                    else
                        j = lireentree.length+10;  // Pour arreter
                    trouve = 0;
                    }
                } else {
                    tab2[indiceTab] += comparNew.charAt(j);
                    trouve = 1;
                }
                }
            }
            var motsIncorrects = 0;
            var motsManquants = 0;
            var motsEnTrop = 0;
            var messa = "";
            var positions = new makeArray3(nbMots1);
            for (i = 0; i < nbMots1; i++) positions[i] = -1;
            var pos = new makeArray1(nbMots2);
            for (i = 0; i < nbMots2-1; i++) {  // Trouver les mots identiques dans tab2
                for (ij = i+1; ij < nbMots2; ij++) {
                if (tab2[i].length == tab2[ij].length) {
                    trouve = false;
                    for (j = 0; j < tab2[i].length; j++) {
                    car1 = tab2[i].charAt(j);
                    car2 = tab2[ij].charAt(j);
                    if (this.bCaseSens == false) {
                        car1 = tab2[i].charAt(j).toLowerCase();
                        car2 = tab2[ij].charAt(j).toLowerCase();
                    }
                    if (car1 != car2) trouve = true;
                    }
                    if (trouve == false) {
                    pos[i] = true;
                    pos[ij] = true;
                    }
                }
                }
            }
            
            
            for (i = 0; i < nbMots1; i++) {
                for (ii = 0; ii < nbMots2; ii++) {
                if (tab1[i].length == tab2[ii].length) {
                    trouve = false;
                    for (j = 0; j < tab1[i].length; j++) {
                    car1 = tab1[i].charAt(j);
                    car2 = tab2[ii].charAt(j);
                    if (this.bCaseSens == false) {
                        car1 = tab1[i].charAt(j).toLowerCase();
                        car2 = tab2[ii].charAt(j).toLowerCase();
                    }
                    if (car1 != car2) trouve = true;
                    }
                    if (trouve == false && pos[ii] == false) {
                    positions[i] = ii;
                    pos[ii] = true;
                    ii = nbMots2 + 1;
                    }
                }
                }
            }
            for (i = 0; i < nbMots1-1; i++) {  // Trouver les mots identiques dans tab1
                for (ij = i+1; ij < nbMots1; ij++) {
                if (tab1[i].length == tab1[ij].length) {
                    trouve = false;
                    for (j = 0; j < tab1[i].length; j++) {
                    car1 = tab1[i].charAt(j);
                    car2 = tab1[ij].charAt(j);
                    if (this.bCaseSens == false) {
                        car1 = tab1[i].charAt(j).toLowerCase();
                        car2 = tab1[ij].charAt(j).toLowerCase();
                    }
                    if (car1 != car2) trouve = true;
                    }
                    if (trouve == false) {
                    positions[i] = -1;
                    positions[ij] = -1;
                    }
                }
                }
            }
            for (i = 0; i < nbMots1-1; i++) {
                for (j = i+1; j < nbMots1; j++) {
                if (positions[i] > positions[j] && positions[j] > -1)
                    positions[i] = -1;
                }
            }
            for (i = 1; i < nbMots1; i++) {
                if (positions[i] > -1 && positions[i-1] == -1) {
                k = positions[i];
                for (j = i-1; j >= 0; j--) {
                    k--;
                    if (k < 0) {
                    j = -1; // arreter
                    } else {
                    mot1 = tab1[j];
                    mot2 = tab2[k];
                    if (this.bCaseSens == false) {
                        mot1 = tab1[j].toLowerCase();
                        mot2 = tab2[k].toLowerCase();
                    }
                    if (mot1 == mot2 && positions[j] == -1)
                        positions[j] = k;
                    else
                        j = -1;  // arreter
                    }
                }
                }
            }
            
            //bug en haut de ca ya des undefined dans tab1
            
            compar1 = " ";
            compar2 = " ";
            var chaineReturn = "";
            var depassement = false;
            var indice = 0;
            for (i = 0; i < nbMots1; i++) { // Le dernier mot est ---fin---
                if (depassement == false) {
                if (positions[i] > -1) {
                    if (positions[i] == indice) {
                    compar1 += tab1[i] + " ";
                    
                    if (indice < nbMots2-1) compar2 += tab2[indice] + " ";
                    chaineReturn += posReturn0[i];
                    if (indice < nbMots2-1)
                        indice++;
                    else
                        depassement = true;
                    } else {
                    if ((positions[i] - indice) > 0) {
                        while (indice < positions[i] && depassement == false) {
                        motsManquants++;
                        compar1 += "--------" + " ";
                        if (indice < nbMots2-1) compar2 += tab2[indice] + " ";
                        chaineReturn += " ";
                        if (indice < nbMots2-1)
                            indice++;
                        else
                            depassement = true;
                        }
                    } else {
                        ij = indice;
                        while (ij > positions[i]) {
                        temp = compar2.lastIndexOf(" ");
                        compar2 = compar2.substring(0, temp);
                        temp = compar2.lastIndexOf(" ");
                        compar2 = compar2.substring(0, temp);
                        ij--;
                        }
                        while (indice > positions[i]) {
                        motsEnTrop++;
                        if (motsIncorrects > 0) motsIncorrects--
                        compar2 += "--------" + " ";
                        indice--;
                        }
                    }
                    compar1 += tab1[i] + " ";
                    if (indice < nbMots2-1) compar2 += tab2[indice] + " ";
                    chaineReturn += posReturn0[i];
                    if (indice < nbMots2-1)
                        indice++;
                    else
                        depassement = true;
                    }
                } else {
                    compar1 += tab1[i] + " ";
                    if (indice < nbMots2-1) compar2 += tab2[indice] + " ";
                    chaineReturn += posReturn0[i];
                    mot1 = tab1[i];
                    mot2 = tab2[indice];
                    if (this.bCaseSens == false) {
                    mot1 = tab1[i].toLowerCase();
                    mot2 = tab2[indice].toLowerCase();
                    }
                    if (mot1 == mot2) {
                    messa += tab1[i] + " ";
                    } else {
                    if (indice < nbMots2-1) motsIncorrects++;
                    }
                    if (indice < nbMots2-1)
                    indice++;
                    else
                    depassement = true;
                }
                } else {
                motsEnTrop++;
                compar1 += tab1[i] + " ";
                compar2 += "--------" + " ";
                chaineReturn += posReturn0[i];
                }
            }
            var nbMots1 = 0;
            var trouve = 0;
            for (var i = 0; i < compar1.length; i++) {
                if (trouve == 1) {
                if (compar1.charAt(i) == ' ') {
                    trouve = 0;
                }
                } else {
                if (compar1.charAt(i) != ' ' && trouve == 0) {
                    nbMots1++;
                    trouve = 1;
                }
                }
            }
            var nbMots2 = 0;
            var trouve = 0;
            for (var i = 0; i < compar2.length; i++) {
                if (trouve == 1) {
                if (compar2.charAt(i) == ' ') {
                    trouve = 0;
                }
                } else {
                if (compar2.charAt(i) != ' ' && trouve == 0) {
                    nbMots2++;
                    trouve = 1;
                }
                }
            }
            var nbMots = nbMots1;
            if (nbMots < nbMots1) nbMots = nbMots2;
            var tab1_new = new makeArray4(nbMots);
            var tab2_new = new makeArray4(nbMots);
            var posReturn = new makeArray4(nbMots);
            for (var j = 0; j < chaineReturn.length; j++) {
                posReturn[j] = chaineReturn.charAt(j);
                if (posReturn[j] == '+') posReturn[j] = "<BR>";
                if (posReturn[j] == '-') posReturn[j] = "<BR><BR>";
            }
            for (var j = chaineReturn.length; j < nbMots; j++) {
                posReturn[j] = " ";
            }
            var indiceTab = 0;
            trouve = 0;
            for (var j = 0; j < compar1.length-1; j++) {
                if (compar1.charAt(j) == ' ') {
                if (trouve == 1) {
                    if (indiceTab < nbMots)
                    indiceTab++;
                    else
                    j = compar1.length+10;  // Pour arreter
                    trouve = 0;
                }
                } else {
                
                tab1_new[indiceTab] += compar1.charAt(j);
                trouve = 1;
                }
            }
            var indiceTab = 0;
            trouve = 0;
            for (var j = 0; j < compar2.length-1; j++) {
                if (compar2.charAt(j) == ' ') {
                if (trouve == 1) {
                    if (indiceTab < nbMots)
                    indiceTab++;
                    else
                    j = compar2.length+10;  // Pour arreter
                    trouve = 0;
                }
                } else {
                tab2_new[indiceTab] += compar2.charAt(j);
                trouve = 1;
                }
            }
            if (nbMots1 != 0 && nbMots2 != 0) {
                messa = this.messageDictee(tab1_new, tab2_new, u1, u2, posReturn);
                this.sReponse = messa;
                if (parseInt(messa) == 999) {
                    if(!bSilent){
                        sRetroAAfficher = "<span class=\"Green\">" + this.page.goodAnswerLabel + "</span><br><br>";
                        sRetroAAfficher += "<h1 class=\"Retro\">" + this.sRetroPos + "</h1>";
                        
                        setFeedback(sRetroAAfficher);
                        openFeedback();
                        this.status = this.quiz.statusCompleted;
                        this.statusMenuPages = 1;
                        this.currentScore = this.ponderation
                    }
                    return this.ponderation;
                }
                
                
                var regex = new RegExp("<U><FONT COLOR='#b22222'><</FONT></U><U><FONT COLOR='#b22222'>%</FONT></U><U><FONT COLOR='#b22222'>%</FONT></U><U><FONT COLOR='#b22222'>%</FONT></U><U><FONT COLOR='#b22222'>></FONT></U>", 'g');
                var newmessa = messa.replace(regex, '<br>');
                
                messa = newmessa;
                
                regex = new RegExp("<%%%>", 'g');
                newmessa = messa.replace(regex, '<br>');
                
                messa = newmessa;
               
                
                
                var toutOk = false;
                
                if (motsIncorrects == 0 && motsManquants == 0 && motsEnTrop == 0) {
                   toutOk = true;
                }
                
                if (toutOk == true) {
                   sRetroAAfficher = "<span class=\"Green\">" + this.page.goodAnswerLabel + "</span><br><br>";
                   sRetroAAfficher += "<h1 class=\"Retro\">" + this.sRetroPos + "</h1>";
                
                   setFeedback(sRetroAAfficher);
                   openFeedback();
                   this.status = this.quiz.statusCompleted;
                   this.statusMenuPages = 1;
                   this.currentScore = this.ponderation
                }
                else {
                     messa = "<h1 class='retro'>" + messa + "</h1>";
                     messa += "<BR><BR><TABLE WIDTH='350' BORDER='0'>"
                     messa += "<TR><TD><h1 class='retro'>" + this.sMsgMotsMOrtho + "&nbsp;:&nbsp;</h1></TD><TD><h1 class='retro'>" + motsIncorrects + "</h1></TD></TR>"
                     messa += "<TR><TD><h1 class='retro'>" + this.sMsgMotsManq + "&nbsp;:&nbsp;</h1></TD><TD><h1 class='retro'>" + motsManquants + "</h1></TD></TR>"
                     messa += "<TR><TD><h1 class='retro'>" + this.sMsgMotsTrop + "&nbsp;:&nbsp;</h1></TD><TD><h1 class='retro'>" + motsEnTrop + "</h1></TD></TR></TABLE>"
                     
                     if(this.sRetroNeg != ""){
                         messa = "<span class=\"Red\">" + this.page.wrongAnswerLabel + "</span><BR><BR><h1 class=\"Retro\">" + this.sRetroNeg + "</h1><br><br>" + messa;
                         this.statusMenuPages = 0;
                     }else{
                         messa = "<span class=\"Red\">" + this.page.wrongAnswerLabel + "</span><BR><BR>" + messa;
                         this.statusMenuPages = 0;
                     }
                     
                     if(!bSilent){
                         setFeedback(messa);
                         openFeedback();
                     }
                     
                     motsIncorrects += motsManquants + motsEnTrop;
                     
                     var iPointsPerdus = parseInt(motsIncorrects) * this.iFautePond;
                     
                     iPointage = Math.max(this.ponderation - iPointsPerdus,0);
                     
                     if(bSilent){
                         return iPointage;
                     }else{
                         if(iPointage == this.ponderation){
                             this.status = this.quiz.statusCompleted
                             this.statusMenuPages = 1;
                         }else{
                             // Mauvaise réponse
                             this.status = this.quiz.statusToRedo;
                         }
                         
                         setFeedback(messa);
                         openFeedback();
                         
                         this.currentScore = iPointage;
                         
                    }

                     jQuery("#feedbackcontent u").each(function() {
                         jQuery(this).addClass('font_e_dictee');
                     });
                     
                     jQuery("#feedbackcontent font").each(function() {
                         jQuery(this).addClass('font_e_dictee');
                     });
                    //feedbackcontent
                }
                
                
                
            } else {
                if(!bSilent){
                    setFeedback("Aucune correction n'a pu &ecirc;tre effectu&eacute;e car aucun mot n'a &eacute;t&eacute; d&eacute;tect&eacute;. Essayez &agrave; nouveau.");
                    openFeedback();
                }
            }
            } else {
            //parent.documentWrite(infoclic)
            }
        }
    },
    
    messageDictee: function(tab1, tab2, u1, u2, posReturn) {
        var messa = "";
        var mot1 = "";
        var mot2 = "";
        var detect = 0;
        var i = 0;
        
        while (i < tab1.length) {
            mot1 = tab1[i];
            mot2 = tab2[i];
            if (mot1.charAt(1) == '-' && mot1.charAt(2) == '-' && mot1.charAt(3) == '-' && mot1.charAt(4) == '-') {
                detect = 1;
                messa += u1+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+u2 + posReturn[i];
                i++;
                continue;
            }
            if (mot2.charAt(1) == '-' && mot2.charAt(2) == '-' && mot2.charAt(3) == '-' && mot2.charAt(4) == '-') {
                detect = 1;
                messa += u1+"[";
                for (jj = 1; jj < mot1.length; jj++) messa += mot1.charAt(jj);
                messa += "]" + u2+ posReturn[i];
                i++;
                
                continue;
            }
            if (mot1.charAt(1) == '-' && mot1.charAt(2) == '-' && mot1.charAt(3) == '-' && mot1.charAt(4) == 'f' && mot1.charAt(5) == 'i') {
                i++;
                
                continue;
            }
            if (mot1.length == mot2.length) {
                for (j = 0; j < mot1.length; j++) {
                    car1 = mot1.charAt(j);
                    car2 = mot2.charAt(j);
                    if (this.bCaseSens == false) {
                        car1 = mot1.charAt(j).toLowerCase();
                        car2 = mot2.charAt(j).toLowerCase();
                    }
                    if (car1 == car2) {
                    messa += mot1.charAt(j);
                    } else {
                    messa += u1+mot1.charAt(j)+u2;
                    detect = 1;
                    }
                    
                }
                i++;
                
            } else {
            detect = 1;
            if (mot1.length > mot2.length) {
                var j1 = 0;
                var lettresAjoutees = 0;
                var tabl1 = new makeArray3(mot1.length);
                var tabl2 = new makeArray3(mot1.length);
                for (j = 0; j < mot1.length; j++) {
                car1 = mot2.charAt(j1);
                car2 = mot1.charAt(j);
                if (this.bCaseSens == false) {
                    car1 = mot2.charAt(j1).toLowerCase();
                    car2 = mot1.charAt(j).toLowerCase();
                }
                if (car1 == car2 || lettresAjoutees == (mot1.length - mot2.length)) {
                    tabl2[j] = mot2.charAt(j1);
                    j1++;
                } else {
                    tabl2[j] = "&nbsp;";
                    lettresAjoutees++;
                }
                tabl1[j] = mot1.charAt(j);
                }
                for (j = 0; j < mot1.length; j++) {
                    car1 = tabl1[j];
                    car2 = tabl2[j];
                    if (this.bCaseSens == false) {
                        car1 = tabl1[j].toLowerCase();
                        car2 = tabl2[j].toLowerCase();
                    }
                    if (car1 == car2) {
                        messa += tabl1[j];
                    } else {
                        messa += u1+tabl1[j]+u2;
                    }
                }
                
            } else {
                var j1 = 0;
                var lettresAjoutees = 0;
                var tabl1 = new makeArray3(mot2.length);
                var tabl2 = new makeArray3(mot2.length);
                for (j = 0; j < mot2.length; j++) {
                car1 = mot1.charAt(j1);
                car2 = mot2.charAt(j);
                if (this.bCaseSens == false) {
                    car1 = mot1.charAt(j1).toLowerCase();
                    car2 = mot2.charAt(j).toLowerCase();
                }
                if (car1 == car2 || lettresAjoutees == (mot2.length - mot1.length)) {
                    tabl1[j] = mot1.charAt(j1);
                    j1++;
                } else {
                    tabl1[j] = "&nbsp;";
                    lettresAjoutees++;
                }
                tabl2[j] = mot2.charAt(j);
                }
                for (j = 0; j < mot2.length; j++) {
                car1 = tabl1[j];
                car2 = tabl2[j];
                if (this.bCaseSens == false) {
                    car1 = tabl1[j].toLowerCase();
                    car2 = tabl2[j].toLowerCase();
                }
                if (car1 == car2) {
                    messa += tabl1[j];
                } else {
                    messa += u1+tabl1[j]+u2;
                }
                }
            }
            i++
            }
            messa += posReturn[i];
        }
        if (detect == 0) return "999"
        var y = messa.indexOf(u1+"&nbsp;");
        while (y > -1) {
            messa = messa.substring(0, y+u1.length) + "#" + messa.substring(y+u1.length);
            y = messa.indexOf(u1+"&nbsp;");
        }
        var y = messa.indexOf(u1+"#");
        while (y > -1) {
            messa = messa.substring(0, y+u1.length) + "&nbsp;" + messa.substring(y+u1.length+1);
            y = messa.indexOf(u1+"#");
        }
        return messa;
        
    },
    
    showSolution: function(){
        var solutionHTML =  this.quiz.solutionLabel + '<br /><br />';
        var strToEvaluate;
        var filteredSolution;
        
        strToEvaluate = this.sBRep;
        filteredSolution = evalStringForLexique(strToEvaluate);
        
        solutionHTML += filteredSolution;
        
        setFeedback(solutionHTML);
        openFeedback();
    },
    
    redo: function(){
        this.sRepAct = '';
        this.currentScore = 0;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;

        closeFeedback();
        this.display();
    },

    redoQuiz: function(){
        this.sRepAct = '';
        this.currentScore = 0;
        this.status = this.quiz.statusToRedo;
        this.statusMenuPages = -1;
    },
    
    isAnswered: function() {
        var toReturn = false;
        
        if(this.input.value.length > 0)
            toReturn = true;
        
        return toReturn;
    },

    getConsigne: function(){
        return this.quiz.consigneDictee;
    }
});