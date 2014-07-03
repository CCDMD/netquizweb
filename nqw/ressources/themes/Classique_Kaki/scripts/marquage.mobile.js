var intervalPosition = null;

function startGetPosition(){
         intervalPosition = setInterval(function(){
                var mainDiv = document.getElementById("containerMarquage");
                var sel = getSelectionCharOffsetsWithin(mainDiv);
                
                if (sel.start != sel.end && top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex].question.nomTypeQuestion == 'marquage'){
                   top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex].question.debutSelection = parseInt(sel.start);
                   top.ccdmd.nq4.pages[top.ccdmd.nq4.currentPageIndex].question.finSelection = parseInt(sel.end);
                }
                else{
                    /*SOIT LA QUESTION N'EST PAS DE TYPE MARQUAGE OU SOIT start = end*/
                }
                
             }, 1000);
}
        
function getSelectionCharOffsetsWithin(element) {
         var start = 0, end = 0;
         var sel, range, priorRange;

         if (typeof window.getSelection != "undefined") {
            range = window.getSelection().getRangeAt(0);

            priorRange = range.cloneRange();
            priorRange.selectNodeContents(element);
            priorRange.setEnd(range.startContainer, range.startOffset);

            start = priorRange.toString().length;
            end = start + range.toString().length;
        }
        else if (typeof document.selection != "undefined" && (sel = document.selection).type != "Control") {
            range = sel.createRange();

            priorRange = document.body.createTextRange();
            priorRange.moveToElementText(element);
            priorRange.setEndPoint("EndToStart", range);

            start = priorRange.text.length;
            end = start + range.text.length;
        }

        return {
               start: start,
               end: end
        };
}