<?php error_reporting(E_ALL);
class Db
{
	private $_pdo=null;
	private $_workdir='';
	public $error=false;
	public $error_message='';
	
	/*
		$place = base (это базовый набор баз данных: local, nodes и т.д.)
		$place = file (указываем конкретную базу данных по полному пути)
	*/
	function __construct($place='base',$database='')
	{
		$this->_workdir=getenv('WORKDIR');	// /usr/jails/
		
		// sqlite:/usr/jails/var/db/local.sqlite
		if($place=='base')
		{
			$file_name=$this->_workdir.'/var/db/'.$database.'.sqlite';
			$connect='sqlite:'.$file_name;
		}elseif($place=='file'){
			
		}
		/*
		$databases=array(
			'tasks'=>'cbsdtaskd',
			'jails'=>'local',
		);
		
		switch($driver)
		{
			case 'sqlite_webdev':
				$connect='sqlite:/var/db/webdev/webdev.sqlite';
				break;
			case 'forms':
				$connect='sqlite:/var/db/webdev/forms.sqlite';
				break;
			case 'helpers':
				if(is_array($database))
				{
					$connect='sqlite:'.$this->_workdir.'/jails-system/'.
						$database['jname'].'/helpers/'.$database['helper'].".sqlite";
				}else{
					$connect='';
				}
				break;
			case 'sqlite_cbsd':
				if($database!='')
				{
					if(!isset($databases[$database])) break;
					$db=$databases[$database];
					$connect='sqlite:'.$this->_workdir.'/var/db/'.$db.'.sqlite';
				}
				break;
			case 'pkg':
				$connect='sqlite:'.$this->_workdir.'/jails-data/'.$database.'-data/var/db/pkg/local.sqlite';
				break;
/-*
			case 'from_file':
				echo $this->_workdir.$database;
				$connect='sqlite:'.$this->_workdir.$database;
				//"/jails-system/jail".$this->jailId."/helpers/".$this->helper.".sqlite"
				break;
*-/
			default:
				throw new Exception('Unknown database driver!');
				break;
		}
		*/
		
		if(!file_exists($file_name))
		{
			$this->error=true;
			$this->error_message='DB file not found!';
			return false;
		}
		
		if(!empty($connect))
		{
			try
			{
				$this->_pdo = new PDO($connect);
				$this->_pdo->setAttribute(PDO::ATTR_TIMEOUT,5000);
			}catch (PDOException $e){
				$this->error=true;
				$this->error_message=$e->getMessage();	//'DB Error';
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getWorkdir()
	{
		return $this->_workdir;
	}
	
	function select($query)
	{
		if($quer=$this->_pdo->query($query))
		{
			$res=$quer->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}
		return array();
	}
	
	function selectAssoc($query)
	{
		if($quer=$this->_pdo->query($query))
		{
			$res=$quer->fetch(PDO::FETCH_ASSOC);
			return $res;
		}
		return array();
	}
	
	function insert($query)
	{
		if($quer=$this->_pdo->query($query))
		{
			$lastID=$this->_pdo->lastInsertId();
			return array('error'=>false,'lastID'=>$lastID);
		}else{
			$error=array('error'=>true,'info'=>$this->_pdo->errorInfo());
			return $error;
		}
		return false;
	}
	
	function update($query)
	{
		if($quer=$this->_pdo->query($query))
		{
			$rowCount=$quer->rowCount();
			return array('rowCount'=>$rowCount);
		}else{
			$error=$this->_pdo->errorInfo();
			return $error;
		}
		return false;
	}
}