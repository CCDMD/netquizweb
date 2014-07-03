<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_MEDIAS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include '../ressources/includes/librairies.php' ?>
	
	<script type="text/javascript" src="../jwplayer/jwplayer.js"></script>
	<style type="text/css">
		html { background:#FFFFFF; }
		body { background:none; }
	</style>
	
</head>

<body id="bMediaPresenter">

	<div id="bodyContenu">		
		<div id="corps">

			<h1><?php echo $media->get("titre") . " (" . TXT_PREFIX_MEDIA . $media->get("id_media") . ")"?></h1>
			
			<div class="padTo20">
	
				<?php if ($media->get("source") != "web" && $media->get("fichier") != "") {
					
						if ($media->get("type") == "image" || $media->get("type") == "") { ?>
							<p class="image_media"><img src="media.php?demande=media_afficher&media_id_media=<?php echo $media->get("id_media") ?>" alt="" /></p>
							
				<?php 	} elseif( $media->get("type") == "video") { ?>
				
							<div id="container"><?php echo TXT_CHARGEMENT_EN_COURS ?></div>
							<script type="text/javascript">
								jwplayer("container").setup({
									'flashplayer': "../jwplayer/player.swf",
									'controlbar': 'bottom',
									'file': 'media.php?demande=media_afficher&media_id_media=<?php echo $media->get("id_media") ?>',
									'provider': 'video',
									'height': 270,
									'width': 480
								});
							</script>
		
				<?php 	} elseif( $media->get("type") == "son") { ?>
				
							<div id="container"><?php echo TXT_CHARGEMENT_EN_COURS ?></div>
							<script type="text/javascript">
								jwplayer("container").setup({
									'flashplayer': "../jwplayer/player.swf",
									'controlbar': 'bottom',
									'file': 'media.php?demande=media_afficher&media_id_media=<?php echo $media->get("id_media") ?>',
									'provider': 'audio',
									'height': 22,
									'width': 480
								});
							</script>
				
				<?php 	}
					 } ?>
				
				<?php if ($media->get("source") == "web" && $media->get("url") != "") {
					
							if ($media->get("type") == "image") { ?>
				
							<p class="image_media"><img src="<?php echo $media->get("url") ?>" width="400" alt="" /></p>
					
				<?php 	} elseif( $media->get("type") == "video") {
				
							if ( strpos(strtolower($media->get("url")), "iframe" ) > 0 || 
								 strpos(strtolower($media->get("url")), "embed" ) > 0 || 
								 strpos(strtolower($media->get("url")), "youtu.be" ) > 0 ||
								 strpos(strtolower($media->get("url")), "vimeo" ) > 0 ) { 
							
								// Afficher iframe
								print html_entity_decode($media->get("url"));
							
							} else { ?>
				
							<div id="container"><?php echo TXT_CHARGEMENT_EN_COURS ?></div>
							<script type="text/javascript">
								jwplayer("container").setup({
									'flashplayer': "../jwplayer/player.swf",
									'controlbar': 'bottom',
									'file': '<?php echo $media->get("url") ?>',
									'provider': 'video',
									'height': 270,
									'width': 480
								});
							</script>
		
				<?php 		}
				
						} elseif( $media->get("type") == "son") { ?>
				
							<div id="container"><?php echo TXT_CHARGEMENT_EN_COURS ?></div>
							<script type="text/javascript">
								jwplayer("container").setup({
									'flashplayer': "../jwplayer/player.swf",
									'controlbar': 'bottom',
									'file': 'http://<?php echo $media->get("url") ?>',
									'provider': 'audio',
									'height': 22,
									'width': 480
								});
							</script>
					
				<?php 	}
					 } ?>
				 
			</div>
		</div> <!-- /corps -->
	</div> <!-- /bodyContenu -->
										
</body>
</html>
