<?php
/**
 * @package Blox Page Builder
 * @author UniteCMS.net
 * @copyright (C) 2017 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

	
	class UniteCreatorDB{
		
		const ISNULL = "dbisnull";
		
		private $pdb;
		private $lastRowID;
		
		/**
		 * 
		 * constructor - set database object
		 */
		public function __construct(){
			$this->pdb = new UniteProviderDBUC();
		}
		
		/**
		 * 
		 * throw error
		 */
		private function throwError($message,$code=-1){
			UniteFunctionsUC::throwError($message,$code);
		}
		

		/**
		 * validate for errors
		 * @param unknown_type $prefix
		 */
		private function checkForErrors($prefix = ""){

			$message = $this->pdb->getErrorMsg();
			
			if(!$message)
				return(false);
			
			if(!empty($prefix))
				$message = $prefix." ".$message;
			
			$errorNum = $this->pdb->getErrorNum();
			
			$this->throwError($message, $errorNum);
		}
		
		
		/**
		 * 
		 * insert variables to some table
		 */
		public function insert($tableName,$arrItems){
			
			$strFields = "";
			$strValues = "";
			foreach($arrItems as $field=>$value){
				$value = "'".$this->escape($value)."'";
				if($field == "id") continue;
				if($strFields != "") $strFields .= ",";
				if($strValues != "") $strValues .= ",";
				$strFields .= $field;
				$strValues .= $value;
			}
			
			$insertQuery = "insert into $tableName($strFields) values($strValues)";									
			
			$this->runSql($insertQuery,"insert");
			$this->lastRowID = $this->pdb->insertid();
			
			return($this->lastRowID);
		}
		
		
		/**
		 * 
		 * get last insert id
		 */
		public function getLastInsertID(){
			$this->lastRowID = $this->pdb->insertid();
			return($this->lastRowID);			
		}
		
		
		/**
		 * 
		 * delete rows
		 */
		public function delete($table,$where){
			
			UniteFunctionsUC::validateNotEmpty($table,"table name");
			UniteFunctionsUC::validateNotEmpty($where,"where");
			
			if(is_array($where))
				$where = $this->getWhereString($where);
			
			$query = "delete from $table where $where";
			
			$success = $this->runSql($query, "delete error");
			return($success);
		}
		
		
		/**
		 * 
		 * get where string from where array
		 */
		private function getWhereString($where){
			$where_format = null;
			
			foreach ( $where as $key=>$value ) {
				
				if($value == self::ISNULL){
					$wheres[] = "($key = '' or $key is null)";
					continue;
				}
				
				if(is_numeric($value) == false){
					$value = $this->escape($value);
					$value = "'$value'";
				}
				
				$wheres[] = "$key = {$value}";
			}
			
			$strWhere = implode( ' AND ', $wheres );
						
			return($strWhere);
		}
		
		
		/**
		 * 
		 * insert variables to some table
		 */
		public function update($tableName,$arrData,$where){
			
			UniteFunctionsUC::validateNotEmpty($tableName,"table cannot be empty");
			UniteFunctionsUC::validateNotEmpty($where,"where cannot be empty");
			UniteFunctionsUC::validateNotEmpty($arrData,"data cannot be empty");
			
			if(is_array($where))
				$where = $this->getWhereString($where);
			
			$strFields = "";
			foreach($arrData as $field=>$value){
				$value = "'".$this->escape($value)."'";
				if($strFields != "") $strFields .= ",";
				$strFields .= "$field=$value";
			}
									
			$updateQuery = "update $tableName set $strFields where $where";
						
			$numRows = $this->runSql($updateQuery, "update error");
			
			return($numRows);
		}
		
		
			/**
		 * 
		 * run some sql query
		 */
		public function runSql($query){
						
			$response = $this->pdb->query($query);
															
			$this->checkForErrors("Regular query error");
						
			return($response);
		}
				
		
		/**
		 * 
		 * fetch rows from sql query
		 */
		public function fetchSql($query){
						
			$rows = $this->pdb->fetchSql($query);
			
			$this->checkForErrors("fetch");
			
			$rows = UniteFunctionsUC::convertStdClassToArray($rows);
			
			return($rows);
		}
		
		
		/**
		 * 
		 * get row wp emulator
		 */
		public function get_row($query = null){
			
			$rows = $this->pdb->fetchSql($query);
						
			$this->checkForErrors("get_row");
			
			if(count($rows) == 1)
				$result = $rows[0];
			else
				$result = $rows;
			
			return($result);
		}
		
		
		/**
		 * get "where" query part
		 */
		private function getQueryPart_where($where = ""){
			
			if($where){
			
				if(is_array($where))
					$where = $this->getWhereString($where);
				
				$where = " where $where";
			}
			
			return($where);
		}
		
		
		/**
		 * create fetch query
		 */
		private function createFetchQuery($tableName, $fields=null, $where="", $orderField="", $groupByField="", $sqlAddon=""){
			
			if(empty($fields)){
				$fields = "*";
			}else{
				if(is_array($fields))
					$fields = implode(",", $fields);
			}
			
			$query = "select $fields from $tableName";
			
			$where = $this->getQueryPart_where($where);
			
			if(!empty($where))
				$query .= $where;
			
			if($orderField){
				$orderField = $this->escape($orderField);
				$query .= " order by $orderField";
			}
			
			if($groupByField){
				$groupByField = $this->escape($groupByField);
				$query .= " group by $groupByField";
			}
			
			if($sqlAddon)
				$query .= " ".$sqlAddon;
			
			return($query);
		}
		
		
		/**
		 * 
		 * get data array from the database
		 * 
		 */
		public function fetch($tableName, $where="", $orderField="", $groupByField="", $sqlAddon=""){
			
			$query = $this->createFetchQuery($tableName, null, $where, $orderField, $groupByField, $sqlAddon);
			
			$rows = $this->fetchSql($query);
			
			return($rows);
		}
		
		/**
		 * get total rows
		 */
		public function getTotalRows($tableName, $where=""){
			
			$where = $this->getQueryPart_where($where);
			
			$query = "select count(*) as numrows from $tableName".$where;
			
			$response = $this->fetchSql($query);
			
			$totalRows = $response[0]["numrows"];
			
			return($totalRows);			
		}
		
		
		/**
		 *
		 * get data array from the database
		 * pagingOptions - page, inpage
		 */
		public function fetchPage($tableName, $pagingOptions, $where="", $orderField="", $groupByField="", $sqlAddon=""){
			
			$page = UniteFunctionsUC::getVal($pagingOptions, "page");
			$rowsInPage = UniteFunctionsUC::getVal($pagingOptions, "inpage");
			
			
			//valdiate and sanitize
			UniteFunctionsUC::validateNumeric($page);
			UniteFunctionsUC::validateNumeric($rowsInPage);
			UniteFunctionsUC::validateNotEmpty($rowsInPage);
			if($page < 1)
				$page = 1;
			
			//get total
			$totalRows = $this->getTotalRows($tableName, $where);
			$numPages = $pages = ceil($totalRows / $rowsInPage);
			
			//build query
			$offset = ($page - 1)  * $rowsInPage;
						
			$query = $this->createFetchQuery($tableName, null, $where, $orderField, $groupByField, $sqlAddon);
			
			$query .= " limit $rowsInPage offset $offset";
			
			$rows = $this->fetchSql($query);
		
			//output response
			$response = array();
			$response["total"] = $totalRows;
			$response["page"] = $page;
			$response["num_pages"] = $numPages;
			$response["inpage"] = $rowsInPage;
			
			$response["rows"] = $rows;
			
			return($response);
		}
		
		
		/**
		 * fields could be array or string comma saparated
		 */
		public function fetchFields($tableName, $fields, $where="", $orderField="", $groupByField="", $sqlAddon=""){
			
			$query = $this->createFetchQuery($tableName, $fields, $where, $orderField, $groupByField, $sqlAddon);
			
			$rows = $this->fetchSql($query);
			
			return($rows);
		}
		
		
		/**
		 * 
		 * fetch only one item. if not found - throw error
		 */
		public function fetchSingle($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
			
			$errorEmpty = "";
			
			if(is_array($tableName)){
				$arguments = $tableName;
				
				$tableName = UniteFunctionsUC::getVal($arguments, "tableName");
				$where = UniteFunctionsUC::getVal($arguments, "where");
				$orderField = UniteFunctionsUC::getVal($arguments, "orderField");
				$groupByField = UniteFunctionsUC::getVal($arguments, "groupByField");
				$sqlAddon = UniteFunctionsUC::getVal($arguments, "sqlAddon");
				$errorEmpty = UniteFunctionsUC::getVal($arguments, "errorEmpty");
			}
			
			if(empty($errorEmpty))
				$errorEmpty = "Record not found";
			
			$response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);
			
			if(empty($response))
				$this->throwError($errorEmpty);
			
			$record = $response[0];
			return($record);
		}
		
		
		/**
		 * 
		 * escape data to avoid sql errors and injections.
		 */
		public function escape($string){
			$newString = $this->pdb->escape($string);
			return($newString);
		}
		
		
	}
	
?>