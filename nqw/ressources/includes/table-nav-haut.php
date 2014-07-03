							<div class="txt"><?php echo $pagination->getNbResultats() ?> <?php echo TXT_ELEMENT_MINUSCULES ?><?php if ($pagination->getNbResultats() > 1) {?>s<?php } ?></div>
							<select name="pagination_nb_elements_haut" onchange="soumettre()">
								<option value="5" <?php if ($pagination->getNbElemParPage() == "5") { echo HTML_SELECTED; } ?> >5 <?php echo TXT_PAR_PAGE ?></option>
								<option value="15" <?php if ($pagination->getNbElemParPage() == "15") { echo HTML_SELECTED; } ?> >15 <?php echo TXT_PAR_PAGE ?></option>
								<option value="30" <?php if ($pagination->getNbElemParPage() == "30") { echo HTML_SELECTED; } ?> >30 <?php echo TXT_PAR_PAGE ?></option>
								<option value="60" <?php if ($pagination->getNbElemParPage() == "60") { echo HTML_SELECTED; } ?> >60 <?php echo TXT_PAR_PAGE ?></option>
							</select>
							
							<input class="btnSubmit <?php if ($pagination->getPagePrec() == $pagination->getPageCour()) echo "btnPrevOff"; else echo "btnPrev"; ?>" type="button" name="btnPrev" value="" onclick="changerPage('<?php echo $pagination->getPagePrec() ?>','<?php echo $pagination->getPageCour() ?>')" />
							<input class="noPage" type="text" name="pagination_page_haut" size="4" maxlength="4"  value="<?php echo $pagination->getPageCour() ?>" /><div class="txt">&nbsp;&nbsp;<?php echo TXT_DE ?> <?php echo $pagination->getNbPages() ?></div> 
							<input class="btnSubmit <?php if ($pagination->getPageSuiv() == $pagination->getPageCour()) echo "btnNextOff"; else echo "btnNext"; ?>" type="button" name="btnNext" value="" onclick="changerPage('<?php echo $pagination->getPageSuiv() ?>','<?php echo $pagination->getPageCour() ?>')" />