<?php
class EmailConfig {

	public $default = array(
		'transport' => 'Mail',
		'from' => 'noreply@edncsolutions.com',
	);

	public $smtp = array(
		'transport' => 'Smtp',
		'from' => array('noreply@edncsolutions.com' => 'E D & C SOLUTIONS'),
		'host' => 'ssl://smtp.gmail.com',
		'port' => 465,
		'timeout' => 30,
		'username' => 'christianllamesmillanes@gmail.com',
		'password' => 'psgy zevk aqro lcbl', // Replace with your Gmail App Password
		'client' => null,
		'log' => true,
	);

}
