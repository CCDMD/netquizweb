function ImgPreloader(){
    this.sImgs = new Array();
    this.sFolder = '';
    this.oImgs = new Array();
    this.iLoaded = 0;
    
    this.preload = function(){
        
        if(this.sImgs.length == 0){
            this.onFinish();
            return;
        }
        
        for(var i = 0;i < this.sImgs.length;i++){
            
            this.oImgs[i] = new Image();
            this.oImgs[i].onload = ip_imgOnLoad;
            this.oImgs[i].onerror = ip_imgOnLoad;
            
            this.oImgs[i].src = this.sImgs[i];
        }
    }
    
    this.addImage = function(s){
        this.sImgs[this.sImgs.length] = s;
    }
    
    this.onFinish = function(){}
}

function getImgPreloader(){
    if(!top.oImgPreloader)top.oImgPreloader = new ImgPreloader();
    return top.oImgPreloader;
}

function ip_imgOnLoad(){
    top.oImgPreloader.iLoaded++;
    if(top.oImgPreloader.iLoaded == top.oImgPreloader.sImgs.length){
        top.oImgPreloader.onFinish();
    }
}