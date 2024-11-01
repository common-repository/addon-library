<?php
/**
 * @package Blox Page Builder
 * @author UniteCMS.net
 * @copyright (C) 2017 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


	class UniteElementsBaseUC{
		
		protected $db;
		protected $imageView;
		
		public function __construct(){
			
			$this->db = new UniteCreatorDB();
			$this->imageView = new UniteImageViewUC();
			
		}
		
	}

?>