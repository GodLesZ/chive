<?php

class ChiveHttpSession extends CHttpSession
{
	/**
	 * @see yii/CHttpSession::setSavePath()
	 */
	public function setSavePath($value)
	{
		if(($value=realpath($value))===false || !is_dir($value) || !is_writable($value))
		{
			throw new CException(Yii::t('yii','Application runtime path "{path}" is not valid. Please make sure it is a directory writable by the Web server process.',
				array('{path}' => $value)));
		}

		parent::setSavePath($value);
	}

    /**
   	 * Starts the session if it has not started yet.
   	 */
   	public function open()
   	{
   		if($this->getUseCustomStorage())
   			session_set_save_handler(array($this,'openSession'),array($this,'closeSession'),array($this,'readSession'),array($this,'writeSession'),array($this,'destroySession'),array($this,'gcSession'));
   		session_start();
   	}

   	/**
   	 * Ends the current session and store session data.
   	 */
   	public function close()
   	{
   		if(session_id()!=='')
   			session_write_close();
   	}

   	/**
   	 * Frees all session variables and destroys all data registered to a session.
   	 */
   	public function destroy()
   	{
   		if(session_id()!=='')
   		{
   			session_unset();
   			session_destroy();
   		}
   	}

}