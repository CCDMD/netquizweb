<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_QUESTIONNAIRES ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel='stylesheet' type='text/css' href='../css/netquiz.css' />
    <link rel='stylesheet' type='text/css'  href='../css/netquiz-print.css' media="print" />
</head>

<body id="bImprime" onload="//window.print();window.close()">
	<div id="bodyContenu">
		<?php include '../ressources/includes/entete-print.php' ?>
	
		<div id="corps">

			<h1><?php echo TXT_QUESTIONNAIRE ?>&nbsp;<?php echo TXT_PREFIX_QUESTIONNAIRE . $quest->get("id_questionnaire")?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $quest->get("titre") ?> (<?php echo $quest->get("nb_items")?> <?php echo TXT_ELEMENT ?><?php if ($quest->get("nb_items") > 1) echo 's'?>)</h1>
			
	
			<p><span class="champTitre"><?php echo TXT_DATE_DE_CREATION ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("date_creation",1) ?></span>
				<span class="champTitre padGa25"><?php echo TXT_DATE_DE_MODIFICATION ?></span>
				<span class="champValeur"><?php echo $quest->getImpression("date_modification",1) ?></span></p>
			
			<?php include '../ressources/gabarits/impression/quest-accueil-details.php' ?>
	
			<?php include '../ressources/gabarits/impression/quest-fin-details.php' ?>
	
			<h2><?php echo TXT_LISTE_DES_ELEMENTS ?></h2>
		
			<?php 
				// Afficher la liste sommaire des items
				$idx = 1;
				foreach ($listeItems as $i) {
					//echo "I : '$i'\n";
					$itemFactory = new Item($log, $dbh);
					$item = $itemFactory->instancierItemParType('', $idProjetActif, $i);
					print "<p>" . $idx . ".&nbsp;&nbsp;I" . $item->get("id_item") . "&nbsp;&nbsp;-&nbsp;&nbsp;" . $item->getTypeItemTxt() . "&nbsp;:&nbsp;";
					print $item->get("titre") . "</p>\n";
					$idx++;
				}
			?>
		
			<h2><?php echo TXT_CONTENU_DETAILLE_DE_CHAQUE_ELEMENT_DU_QUESTIONNAIRE ?></h2>
			
			<?php 
				// Afficher le détail des items
				foreach ($listeItems as $i) {
					
					// Obtenir les données
					$itemFactory = new Item($log, $dbh);
					$item = $itemFactory->instancierItemParType('', $idProjetActif, $i);
					
					// Détails
					print $item->imprimer($quest, $item);
				}
			?>
		
		</div> <!-- /corps -->
	
	</div> <!-- /bodyContenu -->
</body>
</html>
