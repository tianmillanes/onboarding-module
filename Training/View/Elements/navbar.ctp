<div class="header header-new-flex">
	<!-- Left Side: Logo, Divider, Profile, Divider, Logout -->
	<div class="header-left">
		<a href="#/dashboard" class="logo-link-new" style="text-decoration: none;">
			<span class="logo-text">EDNC</span>
		</a>
		
		<div class="header-divider"></div>
		
		<div class="minimized-profile">
			<div class="profile-avatar">
				<?php
					$image = '/assets/img/user-male.jpg';
					echo $this->Thumbnail->render($image, array('path' => '',
						'width' => '32', 'height' => '32',
						'resize' => 'crop','quality' => '100'
					), array('class' => 'img-circle', 'alt' => ''));
				?>
			</div>
			<div class="profile-details">
				<span class="profile-name"><?php echo $currentUser['User']['first_name'] . ' ' . $currentUser['User']['last_name'] ?></span>
				<span class="profile-role"><?php echo $currentUser['Role']['name'] ?></span>
			</div>
		</div>
		
		<div class="header-divider"></div>
		
		<a href="<?php echo Router::url(array('controller'=>'main', 'action'=>'logout')) ?>" class="minimized-logout-btn">
			<i class="fa fa-sign-out"></i> SIGN OUT
		</a>
	</div>

	<!-- Right Side: Clean Modern Nav Menu -->
	<div class="header-right">
		<nav class="modern-nav">
			<a href="#/crud-status" class="modern-nav-item">
				<i class="fa fa-tags"></i> CRUD STATUS
			</a>
			<a href="#/users" class="modern-nav-item">
				<i class="fa fa-users"></i> USERS
			</a>
			<a href="#/crud" class="modern-nav-item">
				<i class="fa fa-database"></i> CRUD
			</a>
		</nav>
	</div>
</div>
</script>
