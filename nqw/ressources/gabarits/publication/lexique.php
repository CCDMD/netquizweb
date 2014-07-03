var lexiqueObj = 
{
	elements: 
	[

<?php 
		
		$nbTermes = 0;
		foreach ($quest->get("listeTermes") as $terme ) {
			
			// Ajouter une virgule 
			if ($nbTermes > 0) {
				echo ",";
			}
			$nbTermes++;
?>
	
			{
				"expression": "<?php echo $terme->getJS("expression") ?>",
				"variantes": "<?php echo $terme->getJS("variantes") ?>",
				"type": "<?php echo $terme->getJS("type") ?>",
				"contenu": "<?php echo $terme->getJS("contenu") ?>",
				"localisation": <?php echo $terme->get("localisation") ?>
				
			}
	
<?php
		} 
?>


  ]
};