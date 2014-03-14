<?php

define ( 'PLUGINS_FOLDER', 'modules/SDK/plugins/' );

class Hook {
    private $hooks;
    function __construct()
    {
        $this->hooks=array();
    }

	function action_exist($where) {
		return isset( $this->hooks [$where] );
	}
	
    function add_action($where,$callback,$priority=50)
    {
        if(!isset($this->hooks[$where]))
        {
            $this->hooks[$where]=array();
        }
        $this->hooks[$where][$callback]=$priority;
    }

    function remove_action($where,$callback)
    {
        if(isset($this->hooks[$where][$callback]))
            unset($this->hooks[$where][$callback]);
    }

    function execute($where,$args='')
    {
        if(isset($this->hooks[$where]) && is_array($this->hooks[$where]))
        {
            arsort($this->hooks[$where]);
            foreach($this->hooks[$where] as $callback=>$priority)
            {
				$args [] = $result;
                $result = call_user_func_array($callback,$args);
            }
			return $result;
        }
    }
	
	function filter($where,$args='')
    {
		$result = $args;
        if(isset($this->hooks[$where]) && is_array($this->hooks[$where]))
        {
            arsort($this->hooks[$where]);
            foreach($this->hooks[$where] as $callback=>$priority)
            {
				$args = $result;
                $result = call_user_func_array($callback,$args);
            }
			return $result;
        }
    }

	function load_plugins($from_folder = PLUGINS_FOLDER) {
		if ($handle = @opendir ( $from_folder )) {
			
			while ( $file = readdir ( $handle ) ) {
				if (is_file ( $from_folder . $file )) {
					if ( strpos ( $from_folder . $file, '.plugin.php' )) {
						require_once $from_folder . $file;
					}
				} else if ((is_dir ( $from_folder . $file )) && ($file != '.') && ($file != '..')) {
					$this->load_plugins ( $from_folder . $file . '/' );
				}
			}
			
			closedir ( $handle );
		}
	}
};

$hooking_daemon = new Hook;
$hooking_daemon->load_plugins();
function add_action($where,$callback,$priority=50)
{
    global $hooking_daemon;
    if(isset($hooking_daemon))
        $hooking_daemon->add_action($where,$callback,$priority);
}
function remove_action($where,$callback)
{
    global $hooking_daemon;
    if(isset($hooking_daemon))
    $hooking_daemon->remove_action($where,$callback);
}
function execute_action($where,$args='')
{
    global $hooking_daemon;
    if( isset($hooking_daemon) && $hooking_daemon->action_exist($where))
		$hooking_daemon->execute($where,$args);
}

function filter_action($where,$args='')
{
    global $hooking_daemon;
    if(isset($hooking_daemon))
    return $hooking_daemon->filter($where,$args);
}

function exist_action($where)
{
    global $hooking_daemon;
    if(isset($hooking_daemon))
		return $hooking_daemon->action_exist($where);
	else
		return false;
}


?>