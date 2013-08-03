<?php
/**
 * @author    Janek Ostendorf (ozzy) <ozzy2345de@gmail.com>
 * @copyright Copyright (c) 2013 SilexLab
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 */

/**
 * Database errors
 */
class SilexDatabaseException extends Exception {
	protected $errorNumber = 0;
	protected $errorDesc = '';
	protected $sqlVersion = '';

	/**
	 * @var SilexDatabase
	 */
	protected $db = null;

	/**
	 * @var string
	 */
	protected $dbType = '';

	/**
	 * @var SilexPreparedStatement
	 */
	protected $preparedStatement = null;

	/**
	 * @param string                 $message
	 * @param SilexDatabase          $db
	 * @param SilexPreparedStatement $preparedStatement
	 */
	public function __construct($message, SilexDatabase $db, SilexPreparedStatement $preparedStatement = null) {
		$this->db = $db;
		$this->preparedStatement = $preparedStatement;
		$this->dbType = $this->db->getType();

		// Prefer statement's errors
		if($this->preparedStatement !== null && $this->preparedStatement->getErrorNumber()) {
			$this->errorNumber = $this->preparedStatement->getErrorNumber();
			$this->errorDesc = $this->preparedStatement->getErrorDesc();
		} else {
			$this->errorNumber = $this->db->getErrorNumber();
			$this->errorDesc = $this->db->getErrorDesc();
		}

		parent::__construct($message, intval($this->errorNumber));
	}

	/**
	 * @return string
	 */
	public function getDbType() {
		return $this->dbType;
	}

	/**
	 * @return int
	 */
	public function getErrorNumber() {
		return $this->errorNumber;
	}

	/**
	 * return string
	 */
	public function getSqlVersion() {
		if($this->sqlVersion === '') {
			try {
				$this->sqlVersion = $this->db->getVersion();
			} catch(DatabaseException $e) {
				$this->sqlVersion = 'unknown';
			}
		}
		return $this->sqlVersion;
	}
}
