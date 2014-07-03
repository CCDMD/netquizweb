<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB?> - <?php echo TXT_ACCUEIL ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php include '../ressources/includes/librairies.php' ?>
	 
</head>

<body id="bIndexOptions">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-nouveau-projet.php' ?>
		
		<div id="corps">
			<div id="colG">

				<h1><?php echo TXT_BIENVENUE_DANS_NETQUIZ_WEB ?></h1>
				<p><?php echo TXT_IDENTIFICATION_LIGNE1?></p>
				
				<h1><?php echo TXT_NOUVEL_UTILISATEUR ?></h1>
				<p><?php echo TXT_NOUVEL_UTILISATEUR_LIGNE1?></p>
				
				<!--  Messages -->
				<?php if (isset($messages)) { ?>
			
				<div class="boxMsg<?php echo $messages->getTypeMessage() ?>" id="message1"><ul><?php echo $messages->getMessages(); ?></ul></div>
					
				<?php } ?>
				<!--  /Messages -->
				
				<form name="frmLogin" id="frmLogin" action="questionnaires.php" method="post">
					<input type="hidden" name="demande" value="projet_creer" />
					<p><label for="projet_titre"><?php echo TXT_TITRE_DU_PROJET ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr></label>
					<input class="wmax100" type="text" name="projet_titre" maxlength="150" placeholder="<?php echo TXT_INSCRIRE_UN_TITRE ?>" value="<?php echo $nouvProjet->get("titre") ?>" /></p>
					
                    <p><label for="projet_description"><?php echo TXT_DESCRIPTION ?></label>
					<textarea id="projet_description" class="wmax editeur suiviModif" name="projet_description" rows="4" cols="200" placeholder="<?php echo TXT_INSCRIRE_UNE_DESCRIPTION ?>"><?php echo $nouvProjet->get("description") ?></textarea></p>
					
                    <p><label for="projet_repertoire"><?php echo TXT_IDENTIFIANT_UNIQUE_DU_PROJET ?> <abbr title="<?php echo TXT_CHAMPS_REQUIS ?>">*</abbr> <span>(<?php echo TXT_IDENTIFIANT_UNIQUE_DU_PROJET_CONTRAINTES ?>)</span></label>
					<input class="wmax100" type="text" id="projet_repertoire" name="projet_repertoire" maxlength="150" placeholder="<?php echo TXT_INSCRIRE_UN_IDENTIFIANT_UNIQUE ?>"  value="<?php echo $nouvProjet->get("repertoire") ?>"/></p>
					
                    <p><input class="btnSubmit" type="submit" name="btnSubmit" id="btnSubmit" value="<?php echo TXT_CREER_ET_COMMENCER ?>" /></p>
				</form>	

			</div> <!-- /colG -->
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
</body>
</html>