var indiceWindow = null;
var identFormWindow = null;
var sendtoFormWindow = null;

var DraggableWindow = Class.create({
    mainElement: null,
    handleElement: null,
    
    //Construct
    initialize: function(me,he){
        this.mainElement = me;
        this.handleElement = he;
        
        new Draggable(me,{handle:he, scroll:window, zindex:1000, starteffect:effectFunction(me), endeffect:effectFunction(me), onDrag:updateShime, onEnd:updateShimeEnd});
        
        this.mainElement.hide();
        $('shime').hide();
        try{
            $('shime').update();
        }catch(e){}
    },
    
    open: function(divId){
        this.mainElement.show();

        /*OLD CODE. */
        /*
        var screenVisibleW = document.getElementById('pagewrapper').offsetWidth / 2;
        var screenVisibleH = document.getElementById('pagewrapper').offsetHeight / 4;
        screenVisibleH = screenVisibleH - 25;
        
        var divW = document.getElementById(divId).offsetWidth / 2;
        var divH = document.getElementById(divId).offsetHeight / 2;

        var windowLeft = screenVisibleW - divW;
        var windowTop = screenVisibleH;// - indiceH;
        
        this.mainElement.style.left = windowLeft + 'px';
        this.mainElement.style.top = windowTop + 'px';
        */
        
        
        /*NEW CODE*/
        // Ajouté 2014-01-22 suite au module de lexique.
        // Étant donné que des mots peuvent se retrouver dans le feedback,
        // la fenêtre doit être visible près de la zone de feedback et non en haut du document
        
        var screenVisibleW = document.getElementById('pagewrapper').offsetWidth / 2;
        var divW = document.getElementById(divId).offsetWidth / 2;
        
        var windowLeft = screenVisibleW - divW;
        var windowTop = jQuery(document).scrollTop() + 120;
        
        this.mainElement.style.left = windowLeft + 'px';
        this.mainElement.style.top = windowTop + 'px';
        
        $('shime').show();
        updateShime();
    },
    
    close: function(){
        this.mainElement.hide();
        $('shime').hide();
    }
});

function effectFunction(element)
{
   new Effect.Opacity(element, {from:0.2, to:1.0});
}

function updateShime(){
    $('shime').clonePosition(indiceWindow.mainElement);

}

function updateShimeEnd(){
    $('shime').clonePosition(indiceWindow.mainElement);

    var elementDrag = jQuery("#indice");
    var position = elementDrag.position();
    
    if (position.top < 0) {
       jQuery("#indice").css({top: '0px'});
    }
    
    
    elementDrag = jQuery("#resultIdentForm");
    position = elementDrag.position();

    if (position.top < 0) {
       jQuery("#resultIdentForm").css({top: '0px'});
    }
    
    
    elementDrag = jQuery("#sendtoForm");
    position = elementDrag.position();

    if (position.top < 0) {
       jQuery("#sendtoForm").css({top: '0px'});
    }
}