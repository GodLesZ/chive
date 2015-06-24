<?php

/*
 * Chive - web based MySQL database management
 * Copyright (C) 2010 Fusonic GmbH
 *
 * This file is part of Chive.
 *
 * Chive is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * Chive is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 */


class User extends ActiveRecord
{
	public $plainPassword;

	public static function splitId($id)
	{
		if (preg_match('/(.*)@(.*)$/', base64_decode($id), $res)) {
			return [
				'User' => $res[1],
				'Host' => $res[2],
			];
		}
		else {
			return null;
		}
	}

	/**
	 * @see        ActiveRecord::model()
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @see        ActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @see        ActiveRecord::primaryKey()
	 */
	public function primaryKey()
	{
		return [
			'Host',
			'User',
		];
	}

	/**
	 * @see        ActiveRecord::rules()
	 */
	public function rules()
	{
		return [
			[
				'User',
				'type',
				'type' => 'string'
			],
			[
				'Host',
				'type',
				'type' => 'string'
			],
			[
				'plainPassword',
				'type',
				'type' => 'string'
			],
			[
				'GlobalPrivileges',
				'type',
				'type' => 'string'
			],
		];
	}

	/**
	 * @see        ActiveRecord::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
			'User'          => Yii::t('core', 'username'),
			'Password'      => Yii::t('core', 'password'),
			'plainPassword' => Yii::t('core', 'password'),
		];
	}

	public function getDomId()
	{
		return md5($this->getId());
	}

	public function getId()
	{
		return base64_encode($this->User.'@'.$this->Host);
	}

	public function setGlobalPrivileges($data)
	{
		// Set all privileges to No
		foreach (array_keys(self::getAllGlobalPrivileges()) AS $priv) {
			$this->{$this->getPrivilegeColumn($priv)} = 'N';
		}

		// Set given privileges to Yes
		foreach (array_keys($data) AS $priv) {
			$this->{$this->getPrivilegeColumn($priv)} = 'Y';
		}
	}

	public static function getAllGlobalPrivileges($group = null)
	{
		$privs = [
			'data'           => [
				'SELECT' => null,
				'INSERT' => null,
				'UPDATE' => null,
				'DELETE' => null,
				'FILE'   => null,
			],
			'structure'      => [
				'CREATE'                  => null,
				'ALTER'                   => null,
				'INDEX'                   => null,
				'DROP'                    => null,
				'CREATE TEMPORARY TABLES' => null,
				'SHOW VIEW'               => null,
				'CREATE ROUTINE'          => null,
				'ALTER ROUTINE'           => null,
				'EXECUTE'                 => null,
				'CREATE VIEW'             => null,
			],
			'administration' => [
				'GRANT'              => null,
				'SUPER'              => null,
				'PROCESS'            => null,
				'RELOAD'             => null,
				'SHUTDOWN'           => null,
				'SHOW DATABASES'     => null,
				'LOCK TABLES'        => null,
				'REFERENCES'         => null,
				'REPLICATION CLIENT' => null,
				'REPLICATION SLAVE'  => null,
				'CREATE USER'        => null,
			],
		];

		if ($group) {
			return $privs[$group];
		}
		else {
			return array_merge($privs['data'], $privs['structure'], $privs['administration']);
		}
	}

	private function getPrivilegeColumn($priv)
	{
		switch ($priv) {
			case 'SHOW DATABASES':
				return 'Show_db_priv';
			case 'CREATE TEMPORARY TABLES':
				return 'Create_tmp_table_priv';
			case 'REPLICATION SLAVE':
				return 'Repl_slave_priv';
			case 'REPLICATION CLIENT':
				return 'Repl_client_priv';
			default:
				return ucfirst(strtolower(str_replace(' ', '_', $priv))).'_priv';
		}
	}

	public function getDbConnection()
	{
		return self::$db;
	}

	/**
	 * @see        ActiveRecord::getUpdateSql()
	 */
	protected function getUpdateSql()
	{
		// Rename user or not
		if ($this->originalAttributes['User'] != $this->User || $this->originalAttributes['Host'] != $this->Host) {
			$sql = [
				'RENAME USER '.self::$db->quoteValue($this->originalAttributes['User']).'@'.self::$db->quoteValue($this->originalAttributes['Host']).' TO '.self::$db->quoteValue($this->User).'@'.self::$db->quoteValue($this->Host).';',
			];
		}
		else {
			$sql = [];
		}

		// Revoke global privileges
		$sql[] = 'REVOKE ALL PRIVILEGES'."\n"."\tON *.*\n"."\tFROM ".self::$db->quoteValue($this->User).'@'.self::$db->quoteValue($this->Host).';';

		// Revoke global grant option
		$sql[] = 'REVOKE GRANT OPTION'."\n"."\tON *.*\n"."\tFROM ".self::$db->quoteValue($this->User).'@'.self::$db->quoteValue($this->Host).';';

		$sql = array_merge($sql, $this->getInsertSql());

		return $sql;
	}

	/**
	 * @see        ActiveRecord::getInsertSql()
	 */
	protected function getInsertSql()
	{
		$privileges = $this->getGlobalPrivileges();
		$canGrant   = array_search('GRANT', $privileges);

		$sql   = [];
		$sql[] = 'GRANT '.implode(', ', array_diff($privileges, ['GRANT']))."\n"."\tON *.*\n"."\tTO ".self::$db->quoteValue($this->User).'@'.self::$db->quoteValue($this->Host).($this->plainPassword !== null ? "\n\tIDENTIFIED BY ".self::$db->quoteValue($this->plainPassword) : '').($canGrant ? "\n\tWITH GRANT OPTION" : '').';';

		$sql[] = 'FLUSH PRIVILEGES';

		return $sql;
	}

	/**
	 * Returns an array containing all global privileges of the user.
	 *
	 * @return    array                    global privileges
	 * @return    array                    do not summarize to ALL PRIVILEGES
	 */
	public function getGlobalPrivileges($group = null, $notAllPrivileges = false)
	{
		$res = [];

		// Retrieve all privileges
		$privs = array_keys(self::getAllGlobalPrivileges($group));

		// Check all privileges for this user
		foreach ($privs AS $priv) {
			if ($this->checkGlobalPrivilege($priv)) {
				$res[] = $priv;
			}
		}

		// Return USAGE if user has no privileges
		if (count($res) == 0) {
			return [
				'USAGE',
			];
		}
		elseif (count($res) == 1 && $res[0] == 'GRANT') {
			return [
				'USAGE',
				'GRANT',
			];
		}

		if ($group || $notAllPrivileges) {
			// Return result if we are only looking for a group
			return $res;
		}
		else {
			// Remove GRANT privilege from privs array
			$resWithoutGrant = array_diff($res, ['GRANT']);

			// Compare privilege count
			if (count($resWithoutGrant) == count($privs) - 1) {
				// User has ALL PRIVILEGES
				$userPrivs = [
					'ALL PRIVILEGES',
				];
				// Also check GRANT privilege
				if (array_search('GRANT', $res) !== false) {
					$userPrivs[] = 'GRANT';
				}

				return $userPrivs;
			}
			else {
				// User doesn't have ALL PRIVILEGES
				return $res;
			}
		}
	}

	public function checkGlobalPrivilege($priv)
	{
		return $this->attributes[$this->getPrivilegeColumn($priv)] == 'Y';
	}

	/**
	 * @see        ActiveRecord::getDeleteSql()
	 */
	protected function getDeleteSql()
	{
		$sql   = ['DROP USER '.self::$db->quoteValue($this->User).'@'.self::$db->quoteValue($this->Host).';'];
		$sql[] = 'FLUSH PRIVILEGES';

		return $sql;
	}

}