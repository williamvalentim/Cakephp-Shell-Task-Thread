<?php

class ThreadTask extends Shell
{
	public $pids = array();
	public $processLimit = 20;
	public $secondsWait = 5;
	public $logFile = '/tmp/thread.log';
	
	public function processExec($commandJob) { 
        $command = $commandJob.' > '.$this->logFile.' 2>&1 & echo $!';
        exec($command ,$op); 
        $pid = (int)$op[0]; 
        if ($pid != "") {
	        return $pid; 
        }
        return false; 
    } 

    public function checkLimitProcess() { 
		foreach ($this->pids as $key => $pid) {
	        exec("ps ax | grep $pid 2>&1", $output);
	        $runningProcess = false;
	        while( list(,$row) = each($output) ) {
                $row_array = explode(" ", $row); 
                $check_pid = $row_array[0];
                if($pid == $check_pid) {
                	$runningProcess = true; 
                	break 2;
                }
	        }
			if (!$runningProcess) {
				unset($this->pids[$key]);
			}
		}
		
		if (count($this->pids) >= $this->processLimit) {
			$this->log("Wait : Count process running: ".count($this->pids));
			sleep($this->secondsWait);
			$this->checkLimitProcess();
		}
		return true;
    } 
}
