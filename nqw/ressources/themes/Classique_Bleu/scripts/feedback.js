var feedbackClosedHeight = 35;

window.onresize = function(){
    if (gNQ4.numberPagesQuiz != '0') {
      updateWrappersSize();
    }
}
function updateWrappersSize()
{
    var windowSize = getWindowSize();
    $("allcontentwrapper").style.height = windowSize.h + 'px';
    
    var topContentPageUp = windowSize.h - jQuery("#contentpageup").height();
    jQuery("#contentpageup").css('top', topContentPageUp + 'px');
    
    calculerDimensionsMenuPages();
    ajusterWrapperAll();
}

function openFeedback(){
    $('feedback').style.backgroundImage = 'url(./theme/imagestheme/feedback_header.png)';
    $('btnCloseFeedback').show();
    $('feedbackcontent').show();
    
    addHighSlideToOtherImages('feedback');
    ajusterWrapperAll();
}

function closeFeedback(){
    $('feedbackcontent').hide();
    $('feedback').style.backgroundImage = 'none';
    $('btnCloseFeedback').hide();
    ajusterWrapperAll();
}

function setFeedback(s){
    $('feedbackcontent').update(s);
}

function hideFeedback(){
    $('feedback').hide();
}

function showFeedback(){
    $('feedback').show();
}

function getWindowSize() {
  var toReturn = {w:0, h:0};
  
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    toReturn.w = window.innerWidth;
    toReturn.h = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    toReturn.w = document.documentElement.clientWidth;
    toReturn.h = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    toReturn.w = document.body.clientWidth;
    toReturn.h = document.body.clientHeight;
  }
  
  return toReturn;
}