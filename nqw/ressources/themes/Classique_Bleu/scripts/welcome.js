var imageOnPage;
var srcImageWelcome;

var jwp_width = 480; 		
var jwp_audiowidth = 400;
var jwp_maxwidth = 480;		
var jwp_height = 270; 		
var jwp_audioheight = 30;


function load_welcome() {
	  if (self == top) {
			// Le quiz n'est pas dans un iframe.                
		}
		else {
			// Le quiz est dans un iframe.
			jQuery("body").css('background', '#FFFFFF');
			jQuery("body").css('overflow-x', 'hidden');
			jQuery("#pagewrapper").css('background', '#FFFFFF');
			jQuery("#wrapperallcontentwrapper").css('box-shadow', 'none');
			jQuery("#wrapperallcontentwrapper").css('margin-left', '0');
			jQuery("#wrapperallcontentwrapper").css('margin-right', '0');
			jQuery("#contentwrapper").css('margin-left', '0');
			jQuery("#contentwrapper").css('margin-right', '0');
		}
	
    jQuery("#intro").html(W_INTRO);
    jQuery("#title").html('<a href="main.html" class="quiz_title_link">' + WM_QUIZ_TITLE + '</a>');
    jQuery("#misc1").html(W_AVERTISSEMENT);
    jQuery("#misc2").html(W_CREDITS);
    jQuery("#btn-commencer").attr("value", W_CONTINUE);

    $('textGuidelineWelcome').hide();
    $('imageContainer').hide();
    $('videoContainer').hide();
    $('soundContainer').hide();
    
    if(textGuidelineWelcome){
        $('textGuidelineWelcome').show();
        $('textGuidelineWelcome').update(textGuidelineWelcome);
    }

    //image
    if(imagePath){
        if (imageCategory == 1){
            srcImageWelcome = this.mediasFolder + "/" + imagePath;
        }
        else if (imageCategory == 2){
            srcImageWelcome = imagePath;
        }
    
        imageOnPage = new Image();
        imageOnPage.name = srcImageWelcome;
        imageOnPage.onload = findHHandWW;
        imageOnPage.src = srcImageWelcome;
    }

    //video
    if(videoPath){
       var videoWidth = jwp_width;
       var videoHeight = jwp_height;
        
       var theVideoId = 'videoContainer2';
       var theVideoPath;
                
       var theAutostart = false;
       var theControlBar = "bottom";
        
       var subCategory = 1;
                
        
       if (autoplayVideo == true) {
           theAutostart = true;           
       }       
        
        
        if (videoCategory == 1) {
            theVideoPath = this.mediasFolder + "/" + videoPath;
        }
        else if (videoCategory == 2) {
            theVideoPath = videoPath;
        
            if (theVideoPath.charAt(0) == '<' && theVideoPath.charAt(theVideoPath.length - 1) == '>') {
                subCategory = 2;
            }
        }
        
        
        if (subCategory == 1){           
           $('videoContainer').show();
                                            
           jwplayer(theVideoId).setup({
             file: theVideoPath, 			
             width: videoWidth,
             height: videoHeight,             
             primary: 'flash',
             autostart: theAutostart
             //aspectratio: "16:9"			
           });
           
           
           if (showVideoController == false) {
						 jwplayer(theVideoId).onPlay(function() {   
								jwplayer(theVideoId).setControls(showVideoController);
						 });
						 
						 jwplayer(theVideoId).onComplete(function() {   
								jwplayer(theVideoId).setControls(true);
						 });
           }
                      
           jwplayer(theVideoId).onReady(function() {            	
             if (!isMobile.any()) {
               jQuery("#videoContainerWrapper").css('border', '1px solid black');
             }
           });
                      
           //jwplayer(theVideoId).onPlay(function() {  alert('Play Pressed');   for (var detail in jwplayer(theVideoId).getMeta()){    alert('Detail: ' + detail );    for (var innerdetail in detail){      alert('InnerDetail: ' + innerdetail );    }  }});           
        }
        else if (subCategory == 2) {
            $('videoContainer').show();
            $('videoContainer2').update(theVideoPath);
        }
    }

    //sound
    if(soundPath){    		
        var theSoundId = 'soundContainer2';
        var theSoundPath; 
        
        var theAutostart = false;
                           
        var subCategory = 1;
        
        if (soundCategory == 1) {
            theSoundPath = this.mediasFolder + "/" + soundPath;
        }
        else if (soundCategory == 2) {
            theSoundPath = soundPath;
        
            if (theSoundPath.charAt(0) == '<' && theSoundPath.charAt(theSoundPath.length - 1) == '>') {
                subCategory = 2;
            }
        }
                        
        $('soundContainer').show();        
        
        if (subCategory == 1) {
        	var theScreenColor = '000000';
        	
        	if (autoplaySound == true) {
        		theAutostart = true;           
        	}
        	
        	if (showSoundController == false) {
        		theScreenColor = 'FFFFFF';
        		
        		jwp_audiowidth = 1;
        		jwp_audioheight = 1;        		
        	}
        	        	        	
        	jwplayer(theSoundId).setup({
						file: theSoundPath, 			
						width: jwp_audiowidth,
						height: jwp_audioheight,						
						primary: 'flash',
						screencolor: theScreenColor,
						autostart: theAutostart            
					});					        	        
        }
        else if (subCategory == 2) {
        	$('soundContainer').update(theSoundPath);            
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
    
    jQuery(window).scroll(function() {  																						
    	ajusterWrapperAllWelcome();
		});
		
		ajusterWrapperAllWelcome();
}

function ajusterWrapperAllWelcome() {
	jQuery("#wrapperallcontentwrapper").height('0px');
		
	var wrapperHeight = jQuery(document).height();
	
	if (jQuery(window).height() > wrapperHeight) {
		wrapperHeight = jQuery(window).height();
	}
	
	if (jQuery("#contentwrapper").outerHeight(true) > wrapperHeight) {
		wrapperHeight = jQuery("#contentwrapper").outerHeight(true);
	}
						
  jQuery("#wrapperallcontentwrapper").height(wrapperHeight + 'px');  
}

function findHHandWW() { 
    var srcImage = srcImageWelcome;
    
    var imgW = this.width;
    var imgH = this.height;
    var imgProp = imgW / imgH;

    if (imgW > 640){
        var newWidth = 640;
        var newHeight = parseInt(newWidth / imgProp);

        var newInnerHTMLImg = "<a href=\"" + srcImage + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageOnPageId\" border=\"0\" src=\"" + srcImage + "\" style=\"width: " + newWidth + "px; height: " + newHeight + "px\">" + "</a>";
        $('imageContainer').innerHTML = newInnerHTMLImg;
    }
    else {
         $('imageContainer').innerHTML = "<a href=\"" + srcImage + "\" class=\"highslide\" onclick=\"return hs.expand(this)\">" + "<img id=\"imageOnPageId\" border=\"0\" src=\"" + srcImage + "\">" + "</a>";
    }

    $('imageContainer').show();

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