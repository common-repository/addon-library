<?php
/**
 * @package Blox Page Builder
 * @author UniteCMS.net
 * @copyright (C) 2017 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

$folderIncludes = dirname(__FILE__)."/";
$folderProvider = $folderIncludes."../../provider/";
	

	//include provider classes
	require_once $folderIncludes . 'functions.php';
	require_once $folderIncludes . 'functions.class.php';

	require_once $folderProvider."include_provider.php";
	
	require_once $folderIncludes . 'db.class.php';
	require_once $folderIncludes . 'settings.class.php';
	require_once $folderIncludes . 'cssparser.class.php';
	require_once $folderIncludes . 'settings_advances.class.php';
	require_once $folderIncludes . 'settings_output.class.php';
	require_once $folderProvider . 'provider_settings_output.class.php';
	require_once $folderIncludes . 'settings_output_wide.class.php';
	require_once $folderIncludes . 'settings_output_inline.class.php';
	require_once $folderIncludes . 'settings_output_sidebar.class.php';
	
	require_once $folderIncludes . 'html_output_base.class.php';
	
	require_once $folderIncludes . 'image_view.class.php';
	require_once $folderIncludes . 'zip.class.php';
	
	require_once $folderIncludes . 'base_admin.class.php';
	
	require_once $folderIncludes . 'elements_base.class.php';
	require_once $folderIncludes . 'base_output.class.php';
	require_once $folderIncludes . 'helper_base.class.php';
	require_once $folderIncludes . 'table.class.php';
	require_once $folderIncludes . 'font_manager.class.php';
	require_once $folderIncludes . 'services.class.php';

	
	//included twig
	//include composer - twig
	$isTwigExists = interface_exists("Twig_LoaderInterface");
	
	if($isTwigExists == false)
		require $folderIncludes."../../vendor/autoload.php";
		
