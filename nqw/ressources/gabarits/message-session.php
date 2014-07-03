<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo TXT_NETQUIZ_WEB ?> - <?php echo TXT_MEDIAS ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<!-- JQuery + UI -->
	<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.8.17.custom.min.js"></script>
	<script type="text/javascript" src="../js/jquery.mjs.nestedSortable.js"></script>
  
	<!-- Fancybox -->
	<script type="text/javascript" src="../js/jquery.fancybox-1.3.4.pack.js"></script>
	<script type="text/javascript" src="../js/jquery.easing-1.3.pack.js"></script>
	<script type="text/javascript" src="../js/jquery.mousewheel-3.0.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/jquery.fancybox-1.3.4.css" media="screen" />
	
	<!-- JQuery Splitter -->
    <script type="text/javascript" src="../jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="../jqwidgets/jqxsplitter.js"></script>
    <script type="text/javascript" src="../jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="../jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="../jqwidgets/jqxpanel.js"></script>	
	<link rel="stylesheet" type="text/css" href="../jqwidgets/styles/jqx.base.css" />
	<link rel="stylesheet" type="text/css" href="../css/netquiz-jqx-base.css" />
	
	<!-- NetquizWeb -->
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' />
    <link rel='stylesheet' type='text/css' href='../css/netquiz.css' />
    <link rel='stylesheet' type='text/css' href='../css/netquiz-print.css' media="print" />

	<script type="text/javascript">
		// Fermer la fenêtre de message
		function fermer() {
			parent.jQuery.fancybox.close();
			
		}
	</script>
	
</head>

<body>

	<div class="boxSession">
		<div class="boxTitre">
			<p>
				<?php echo TXT_AVERTISSEMENT ?>
			</p>
		</div>
	
		<div class="boxContenu">
			
			<div class="boxPrincipal">
			
                <p class="padTo20">
			
			<?php 
			
				if ($sessionStatut == "1") {
					echo TXT_AVERTISSEMENT_FIN_SESSION;
				} else {
					echo TXT_AVERTISSEMENT_SESSION_EXPIREE;
				} 
			?>
					
			</p>
			
			</div>
		</div>
		
		<div class="boxBottom">
			<input class="btnSubmit" name="btnSubmit" id="btnSubmit1" type="submit" value="<?php echo TXT_FERMER ?>" onclick="fermer()" />
		</div>
	
	</div>						
						
</body>
</html>