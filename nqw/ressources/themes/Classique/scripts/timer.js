var nq_Timer = Class.create({
    sliderImage: null,
    bkgColor: null,
    delay: null,
    width: null,
    height: null,
    onFinish: null,
    container: null,
    mainElement: null,
    sliderElement: null,
    pe: null,
    increment: null,
    progress: null,
    leDelay: false,
    stopCompletely: false,
    
    //Settings
    peDelay: 1,
    
    //Init
    initialize: function(){},
    
    //Public
    show: function(element){
        this.container = element;
        
        //Main element
        this.mainElement = document.createElement('div');
        
        if(this.sliderImage)
            this.mainElement.style.background = 'url(' + this.sliderImage + ') no-repeat top left';
        if(this.width)
            this.mainElement.style.width = this.width + 'px';
        if(this.height)
            this.mainElement.style.height = this.height + 'px';
            
        //Slider element
        this.sliderElement = document.createElement('div');
        this.sliderElement.style.marginLeft = 'auto';
        
        if(this.bkgColor)
            this.sliderElement.style.backgroundColor = this.bkgColor;
        if(this.width)
            this.sliderElement.style.width = this.width + 'px';
        if(this.height)
            this.sliderElement.style.height = this.height + 'px';
        
        this.container.update(this.mainElement);
        this.mainElement.appendChild(this.sliderElement);
    },
    
    start: function(){
        if(this.pe)
            this.pe.stop();
        
        this.progress = 0;
        this.increment = Math.round(this.width / 100);
        this.pe = new PeriodicalExecuter(_nqTimer_updateProgress, this.peDelay);

    },
    stop: function(){

        this.sliderElement.style.width = Math.round(this.width - (this.width * 0)) + 'px';
    },
    pause: function(){
        this.pe.stop();
    },
    
    //Private
    _onComplete: function(){
        this.pe.stop();

        if (this.stopCompletely == false)
           this.onFinish();
    },
    _updateProgress: function(){
        this.progress++;

        var pcProgress = (this.progress / (this.delay / this.peDelay));

        if(pcProgress >= 1 && this.leDelay == false){
            this.sliderElement.style.width = 0;          
            this.leDelay = true;
       }
       else if (pcProgress >= 1 && this.leDelay == true){
            this.leDelay = false;
            this._onComplete();
       }
       else{
            if (this.leDelay == false)
               this.sliderElement.style.width = Math.round(this.width - (this.width * pcProgress)) + 'px';
       }
        //this.width * (progress / (delai total / pedelay))
    }
});

function getNewNQTimer(oMainContainer,oNavBarContainers){
    if (top.ccdmd == null) top.ccdmd = {};
    top.ccdmd.nqtimer = new nq_Timer();
    return top.ccdmd.nqtimer;
}
function _nqTimer_updateProgress(){

    if (top.ccdmd.nqtimer.stopCompletely == false){
       top.ccdmd.nqtimer._updateProgress();
    }
}