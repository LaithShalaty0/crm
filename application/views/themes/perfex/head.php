<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="csrf-token" content="<?php echo $this->security->get_csrf_hash(); ?>">
	<title><?php if (isset($title)){ echo $title; } ?></title>
	<?php echo compile_theme_css(); ?>
	<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
	<?php app_customers_head(); ?>
</head>
<body class="customers <?php echo strtolower($this->agent->browser()); ?><?php if(is_mobile()){echo ' mobile';}?><?php if(isset($bodyclass)){echo ' ' . $bodyclass; } ?>" <?php if($isRTL == 'true'){ echo 'dir="rtl"';} ?>>
	<?php hooks()->do_action('customers_after_body_start'); ?>
