<!DOCTYPE html>
<html>
<head>
	<title>E D & C SOLUTIONS</title>
	<link rel="stylesheet" href="<?php echo $this->base ?>/assets/plugins/bootstrap-3.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $this->base ?>/assets/plugins/scrollbar/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="<?php echo $this->base ?>/assets/plugins/angular-loading/loading-bar.css">
	<link rel="stylesheet" href="<?php echo $this->base ?>/assets/datepicker/custom-datepicker.css?v=<?php echo time(); ?>">
	<!--<link rel="icon" href = "<?php echo $this->base ?>/assets/img/favicon.ico">-->
	<link rel="stylesheet" href="<?php echo $this->base ?>/assets/css/style.css?v=<?php echo time(); ?>">
  <script>
    var base = '<?php echo $base ?>';
    var api  = '<?php echo $api  ?>';
    var tmp  = '<?php echo $tmp  ?>';
  </script>
	<script type="text/javascript" src="<?php echo $this->base ?>/assets/plugins/jquery/jquery.min.js"></script>

	<base href="<?php echo $this->base ?>/">
</head>
<body ng-app="ednc">
	<?php echo $this->element('navbar') ?>
	<div class="main">
		<div class="col-md-12">
			<div ng-view></div>
		</div>
	</div>

	<!-- Apple-style Loading HUD -->
	<div id="apple-loading-modal" class="apple-loading-overlay">
		<div class="apple-loading-hud">
			<div class="apple-spinner">
				<div class="bar1"></div>
				<div class="bar2"></div>
				<div class="bar3"></div>
				<div class="bar4"></div>
				<div class="bar5"></div>
				<div class="bar6"></div>
				<div class="bar7"></div>
				<div class="bar8"></div>
				<div class="bar9"></div>
				<div class="bar10"></div>
				<div class="bar11"></div>
				<div class="bar12"></div>
			</div>
			<div class="apple-loading-text">Saving...</div>
		</div>
	</div>

  <?php echo $this->element('angularjs') ?>
  <?php echo $this->element('scripts') ?>
	<?php echo $this->fetch('extrajs') ?>
  
</body>
</html>
