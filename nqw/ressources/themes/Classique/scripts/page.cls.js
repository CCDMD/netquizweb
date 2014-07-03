var Page = Class.create({
    quiz: null,
    
    //Medias
    imagePath: null,
    imageCategory: null,
    soundPath: null,
    soundCategory: null, 
    videoPath: null, 
    videoCategory: null, 
    
    autoplayVideo: false,
    autoplaySound: false, 
    showVideoController: true, 
    showSoundController: true,
    
    goodAnswerMedia: null,
    wrongAnswerMedia: null,
    incompleteAnswerMedia: null,
    
    //Indice, source, consigne
    indice: null,
    indiceTag: null,
    source: null,
    sourceTag: null,
    consigne: null,
    
    //Settings
    displaySolution: false, 
    timerDelay: null,
    timerEnabled: false,
    pageNextEnabled: true,
    pageBackEnabled: true,
    showNavBSolution: true,
    
    //Text vars
    readableType: null,
    title: null,
    itemTitle: null,
    statement: null,
    goodAnswerLabel: null,
    wrongAnswerLabel: null,
    incompleteAnswerLabel: null,
    textGuideline: null, //used?
    textMediaTitle: null, //used?
    textMedia: null, //used?

    //Question
    question: null,
    
    initialize: function(quiz){
        this.quiz = quiz;
    },
    
    initQuestionMultipleChoices: function(questionNb){
        this.question = new QuestionMultipleChoices(this.quiz, this, questionNb);
    },
    initQuestionMultipleAnswers: function(questionNb){
        this.question = new QuestionMultipleAnswers(this.quiz, this, questionNb);
    },
    initQuestionLongText: function(questionNb){
        this.question = new QuestionLongText(this.quiz, this, questionNb);
    },
    initQuestionDictee: function(questionNb){
        this.question = new QuestionDictee(this.quiz, this, questionNb);
        this.question.sMsgMotsMOrtho = this.quiz.sMsgMotsMOrtho;
        this.question.sMsgMotsManq = this.quiz.sMsgMotsManq;
        this.question.sMsgMotsTrop = this.quiz.sMsgMotsTrop;
    },
    initQuestionShortText: function(questionNb){
        this.question = new QuestionShortText(this.quiz, this, questionNb);
    },
    initQuestionAssociation: function(questionNb){
        this.question = new QuestionAssociation(this.quiz, this, questionNb);
    },
    initQuestionRanking: function(questionNb){
        this.question = new QuestionRanking(this.quiz, this, questionNb);
    },
    initQuestionBlankText: function(questionNb){
        this.question = new QuestionBlankText(this.quiz, this, questionNb);
    },
    initQuestionCheckerBoard: function(questionNb){
        this.question = new QuestionCheckerBoard(this.quiz, this, questionNb);
    },
    initQuestionImagePart: function(questionNb){
        this.question = new QuestionImagePart(this.quiz, this, questionNb);
    },
    initQuestionClassement: function(questionNb){
        this.question = new QuestionClassement(this.quiz, this, questionNb);
    },
    initQuestionMarquage: function(questionNb){
        this.question = new QuestionMarquage(this.quiz, this, questionNb);
        this.question.removeHiliteLabel = this.quiz.removeHiliteLabel;
    },
    
    validate: function(){
        if(this.question){
            this.question.validate();
        }
    },
    showSolution: function(){
        if(this.question){
            this.question.showSolution();
        }
    },
    redo: function(){
        if(this.question)
            this.question.redo();
    },
    
    addFeedbackMedia: function(mediaType, mediaCategory, mediaPath, autoPlay, showController){
        var returnArray = new Array();
    
        returnArray.push([mediaType, mediaCategory, mediaPath, autoPlay, showController]);
        return returnArray;
    }
});