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


class UserPrivilegesManager
{

	private $host, $user;
	private        $global = [], $schema = [], $table = [], $column = [];

	public function __construct($host, $user)
	{

		$this->host = $host;
		$this->user = $user;

		// Load settings
		$this->loadPrivileges();

	}

	public function loadPrivileges()
	{
		Yii::app()->db->setActive(true);

		$cmdGlobal    = Yii::app()->db->createCommand('SELECT DISTINCT PRIVILEGE_TYPE FROM information_schema.USER_PRIVILEGES');
		$global       = $cmdGlobal->queryAll();
		$this->global = [];
		foreach ($global AS $priv) {
			$this->global[] = $priv['PRIVILEGE_TYPE'];
		}

		$cmdSchema    = Yii::app()->db->createCommand('SELECT DISTINCT TABLE_SCHEMA, PRIVILEGE_TYPE FROM information_schema.SCHEMA_PRIVILEGES');
		$schema       = $cmdSchema->queryAll();
		$this->schema = [];
		foreach ($schema AS $priv) {
			if (!isset($this->schema[$priv['TABLE_SCHEMA']])) {
				$this->schema[$priv['TABLE_SCHEMA']] = [];
			}
			$this->schema[$priv['TABLE_SCHEMA']][] = $priv['PRIVILEGE_TYPE'];
		}

		$cmdTable    = Yii::app()->db->createCommand('SELECT DISTINCT TABLE_NAME, TABLE_SCHEMA, PRIVILEGE_TYPE FROM information_schema.TABLE_PRIVILEGES');
		$table       = $cmdTable->queryAll();
		$this->table = [];
		foreach ($table AS $priv) {

			if (!isset($this->table[$priv['TABLE_SCHEMA'].'.'.$priv['TABLE_NAME']])) {
				$this->table[$priv['TABLE_SCHEMA'].'.'.$priv['TABLE_NAME']] = [];
			}
			$this->table[$priv['TABLE_SCHEMA'].'.'.$priv['TABLE_NAME']][] = $priv['PRIVILEGE_TYPE'];
		}

		$cmdColumn    = Yii::app()->db->createCommand('SELECT DISTINCT COLUMN_NAME, TABLE_NAME, TABLE_SCHEMA, PRIVILEGE_TYPE FROM information_schema.COLUMN_PRIVILEGES');
		$column       = $cmdColumn->queryAll();
		$this->column = [];
		foreach ($column AS $priv) {
			if (!isset($this->column[$priv['TABLE_SCHEMA'].'.'.$priv['TABLE_NAME'].'.'.$priv['COLUMN_NAME']])) {
				$this->column[$priv['TABLE_SCHEMA'].'.'.$priv['TABLE_NAME'].'.'.$priv['COLUMN_NAME']] = [];
			}
			$this->column[$priv['TABLE_SCHEMA'].'.'.$priv['TABLE_NAME'].'.'.$priv['COLUMN_NAME']][] = $priv['PRIVILEGE_TYPE'];
		}

	}

	public function checkColumn($schema, $table, $column, $priv)
	{
		if ($this->checkTable($schema, $table, $priv)) {
			return true;
		}
		if (isset($this->column[$schema.'.'.$table.'.'.$column])) {
			return in_array($priv, $this->column[$schema.'.'.$table.'.'.$column]);
		}
		else {
			return false;
		}
	}

	public function checkTable($schema, $table, $priv)
	{
		if ($this->checkSchema($schema, $priv)) {
			return true;
		}
		if (isset($this->table[$schema.'.'.$table])) {
			return in_array($priv, $this->table[$schema.'.'.$table]);
		}
		else {
			return false;
		}
	}

	public function checkSchema($schema, $priv)
	{
		if ($this->checkGlobal($priv)) {
			return true;
		}
		foreach ($this->schema AS $scope => $privs) {
			$escaped = $this->escape($scope);
			if (preg_match('/^'.$escaped.'$/', $schema)) {
				return in_array($priv, $privs);
			}
		}

		return false;
	}

	public function checkGlobal($priv)
	{
		return in_array($priv, $this->global);
	}

	private function escape($value)
	{
		$value = str_replace('.', '\.', $value);
		if (strstr($value, '.')) {
			return $value;
		}
		else {
			$value = preg_replace('/([^\\\])_/', '$1.', $value);
			$value = preg_replace('/([^\\\])%/', '$1.*', $value);
			$value = str_replace([
									 '\_',
									 '\%'
								 ], [
									 '_',
									 '%'
								 ], $value);

			return $value;
		}
	}

}

?>