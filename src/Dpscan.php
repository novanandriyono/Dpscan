<?php
namespace Dpscan;
use Dpscan\Contracts\Dpscan as DpscanInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class Dpscan implements DpscanInterface
{
	protected $rootfolder,$results;

	public function __construct($rootfolder = null,$results = null){
		$this->rootfolder = $rootfolder;
		$this->results = $results;
	}

	public function setdir($dir = null){
		if((is_dir($dir) === true) || (str_contains($dir,base_path()) === true)){
			$this->rootfolder = $dir;
			return $this;
		}
		throw new Exception("Not Dir", 1);
	}

	public function get(){
		$this->rootfolder = $this->rootfolder;
		$this->results = $this->getContent($this->rootfolder);
		return $this;
	}

	public function all(){
		return $this->setAll();
	}

	protected function setAll(){
		$this->rootfolder = $this->rootfolder;
		$this->results = $this->getAllContent($this->get()->results,$this->rootfolder);
		return $this;
	}

	public function results(){
		return $this->setResults();
	}

	protected function setResults(){
		$results = $this->results;
		unset($results['file']);
		unset($results['dir']);
		return $this->items(array_values($results));
	}

	public function getfiles(){
		return $this->setGetFiles();
	}

	protected function setGetFiles(){
		$results = $this->results['file'];
		return $this->items(array_values($results));
	}

	public function getdir(){
		return $this->setGetDir();
	}

	protected function setGetDir(){
		$results = $this->results['dir'];
		return $this->items(array_values($results));
	}

	protected function getAllContent($list = [], $root = null, $now = 0, $results = []){
		if(is_object($list)){
			$total = $list->count();
		}elseif(is_string($list)){
			$total = 0;
		}else{
			$total = count($list);
		}
		if($now !== $total){
			$item = $root.DIRECTORY_SEPARATOR.$list[$now];
			//unset($this->results[$now]);
			$results[$item] = $item;
			if(is_file($item)){
				$results['file'][] = $item;
			}
			if(is_dir($item)){
				$child = (new Dpscan)->setdir($item)->all();
				$results['dir'][] = $child->rootfolder;
				$results[$item] = $child->rootfolder;;
				$results = $results + $child->results;
			}
			$next = $now + 1;
			return $this->getAllContent($list,$root, $next,$results);
		}
		return $results;
	}

	protected function items(array $array = []){
		return new Collection($array);
	}

	protected function getContent($dir=null){
		if(is_dir($dir)){
			return $this->fixArray(scandir($dir));
		}
	}

	protected function fixArray($array = []){
		unset($array[0]);
        unset($array[1]);
        return array_values($array);
	}

}