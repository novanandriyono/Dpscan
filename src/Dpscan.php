<?php
namespace Dpscan;

use Dpscan\Contracts\Dpscan as DpscanInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class Dpscan implements DpscanInterface
{

	protected $rootfolder;
	/**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

	public function __construct(){
		if(func_num_args() !== 0){
			return;
		}
	}

	public function setdir(string $dir){
		if(func_num_args() !== 1){
			return;
		}
		if(str_contains($dir,$this->config()->root) !== true){
			return;
		}
		if((is_dir($dir) !== true)
			|| (str_contains($dir,base_path()) !== true)){
			return;
		}
		$this->rootfolder = $dir;
		// return $this->createItems($this->getContent()->items);
		return $this;
	}

	public function rootchange(string $dir){
		return $this->setrootchange($dir);
	}

	protected function setrootchange(string $dir){
		if(func_num_args() !== 1){
			return;
		}
		if(is_numeric(key($this->items)) === false){
			$items = array_keys($this->getContent()->items);
		}else{
			$items = $this->items;
		}
		for ($i=0; $i < count($items) ; $i++) {
			$items[$i] = str_replace(
				[$this->rootfolder,DIRECTORY_SEPARATOR],
				[$dir,'/'],
				$items[$i]);
		}
		$new = new static;
		$new->rootfolder = $dir;
		$new->items = $items;
		return $new;
	}

	public function get(){
		return $this->setget();
	}

	protected function setget(){
		if(is_numeric(key($this->items)) === false){
			return $this->createItems(array_keys($this->getContent()->items));
		}else{
			return $this->createItems($this->items);
		}
	}

	public function onlydir(){
		return $this->setonlydir();
	}

	protected function setonlydir(){
		if(is_numeric(key($this->items)) === false){
			return $this->createItems(array_keys(array_flip($this->getContent()->items)));
		}else{
			return $this->getResultByType($this->items);
		}
	}

	protected function getResultByType(array $lists,int $option=0){
		$range = range(0,1);
		if(isset($range[$option]) === false){
			return [];
		}
		$results = [];
		for ($i=0; $i < count($lists); $i++) {
			if($option===0){
				if(is_dir($lists[$i])){
					$results[]=$lists[$i];
				}
			}
			if($option===1){
				if(is_file($lists[$i])){
					$results[]=$lists[$i];
				}
			}
		}
		return $this->createItems($results);
	}

	public function onlyfiles(){
		return $this->setonlyfiles();
	}

	protected function setonlyfiles(){
		if(is_numeric(key($this->items)) === false){
			return $this->getResultByType(array_keys($this->getContent()->items),1);
		}else{
			return $this->getResultByType($this->items,1);
		}
	}

	public function except(array $array = []){
		return $this->setexcept($array);
	}

	protected function setexcept(array $array = []){
		if(is_numeric(key($this->items)) === false){
			$listss =  $this->getContent()->items;
		}else{
			$lists = $this->items;
		}
		$lists = array_flip($lists);
		for ($i=0; $i < count($array); $i++) {
			$items = $this->rootfolder.DIRECTORY_SEPARATOR.$array[$i];
			if(file_exists($items) === false){
				throw new Exception("NotFound except: ".$items);
			}
			if(isset($lists[$items])){
				unset($lists[$items]);
			}
		}
		return $this->createItems(array_values(array_flip($lists)));
	}

	protected function getContent(){
		return $this->setContent();
	}

	public function notcontains(array $array = []){
		return $this->setnotcontains($array);
	}

	protected function setnotcontains(array $array = []){
		if(is_numeric(key($this->items)) === false){
			return $this->getResultsByContains($array,$this->get()->items,0);
		}else{
			return $this->getResultsByContains($array,$this->items,0);
		}
	}

	public function notcontainsfiles(array $array = []){
		return $this->getResultsByContains($array,$this->onlyfiles()->items,0);
	}

	public function notcontainsdir(array $array = []){
		return $this->getResultsByContains($array,$this->onlydir()->items,0);
	}

	public function contains(array $array = []){
		return $this->setcontains($array);
	}

	protected function setcontains(array $array = []){
		if(is_numeric(key($this->items)) === false){
			return $this->getResultsByContains($array,$this->get()->items,1);
		}else{
			return $this->getResultsByContains($array,$this->items,1);
		}
	}

	public function containsfiles(array $array = []){
		return $this->getResultsByContains($array,$this->onlyfiles()->items,1);
	}

	public function containsdir(array $array = []){
		return $this->getResultsByContains($array,$this->onlydir()->items,1);
	}

	protected function getResultsByContains(array $contains,array $lists,int $option, array $results = []){
		$range = range(0,1);
		if(isset($range[$option]) === false){
			return [];
		}
		for ($i=0; $i < count($contains); $i++) {
			$item = $contains[$i];
			for ($l=0; $l < count($lists); $l++) {
				if(strpos($lists[$l], $item) === false){
					if($option === 0){
						$results[$lists[$l]][$i] = $lists[$l];
					}
				}else{
					if($option === 1){
						$results[$lists[$l]] = $lists[$l];
					}
				}
			}
		}
		if($option===0){
			$newResult = array_values($results);
			$results = [];
			for ($i=0; $i < count($newResult); $i++) {
				if(count($newResult[$i]) === count($contains)){
					$results[] = $newResult[$i][0];
				}
			}
		}
		$results = array_values(array_keys(array_flip($results)));
		return $this->createItems($results);
	}

	public function regexcept(array $array = []){
		return $this->setRegExcept($array);
	}

	protected function setregexcept(array $array = []){
		if(is_numeric(key($this->items)) === false){
			return $this->getResultsByRegex($array,$this->get()->items,0);
		}else{
			return $this->getResultsByRegex($array,$this->items,0);
		}
	}

	public function regexceptfiles(array $array = []){
		return $this->getResultsByRegex($array,$this->onlyfiles()->items,0);
	}

	public function regexceptdir(array $array = []){
		return $this->getResultsByRegex($array,$this->onlydir()->items,0);
	}

	public function regexonly(array $array = []){
		return $this->setRegExonly($array);
	}

	protected function setregexonly(array $array = []){
		if(is_numeric(key($this->items)) === false){
			return $this->getResultsByRegex($array,$this->get()->items,1);
		}else{
			return $this->getResultsByRegex($array,$this->items,1);
		}
	}

	public function regexonlyfiles(array $array = []){
		return $this->getResultsByRegex($array,$this->onlyfiles()->items,1);
	}

	public function regexonlydir(array $array = []){
		return $this->getResultsByRegex($array,$this->onlydir()->items,1);
	}

	protected function getResultsByRegex(array $regex,array $lists,int $option,array $results = []){
		$range = range(0,1);
		if(isset($range[$option]) === false){
			return [];
		}
		for ($i=0; $i < count($regex); $i++) {
			$item = $regex[$i];
			for ($l=0; $l < count($lists); $l++) {
				if(preg_match($item, $lists[$l]) === $option){
					if($option === 0){
						$results[$lists[$l]][$i] = $lists[$l];
					}
					if($option === 1){
						$results[$lists[$l]] = $lists[$l];
					}
				}
			}
		}
		if($option===0){
			$newResult = array_values($results);
			$results = [];
			for ($i=0; $i < count($newResult); $i++) {
				if(count($newResult[$i]) === count($regex)){
					$results[] = $newResult[$i][0];
				}
			}
		}
		$results = array_values(array_keys(array_flip($results)));
		return $this->createItems($results);
	}

	protected function setContent(){
		if(func_num_args() !== 0){
			return;
		}
		if(str_contains($this->rootfolder,$this->config()->root) !== true){
			return;
		}
		if((is_dir($this->rootfolder) === true) &&
			(is_writable($this->rootfolder) === true)){
			$next = new static;
			$next->rootfolder = $this->rootfolder;
			$next->items = $this->fixArray(scandir($this->rootfolder));
			return $next->getAllContent();
		}
		return $this->items;
	}

	public function items(){
		return $this->getitems();
	}

	protected function getitems(){
		return $this->setitems();
	}

	protected function setitems(){
		return new Collection($this->items);
	}

	protected function getAllContent($now = 0, $results = []){
		if(str_contains($this->rootfolder,$this->config()->root) !== true){
			return;
		}
		$total = count($this->items);
		if($now !== $total){
			$item = $this->rootfolder.DIRECTORY_SEPARATOR.$this->items[$now];
			$results[$item] = $this->rootfolder;
			if(is_dir($item)){
				$newfolder = new static;
				$newfolder->rootfolder = $item;
				$results = $results + $newfolder->getContent()->items;
			}
			$next = $now + 1;
			return $this->getAllContent($next,$results);
		}
		return $this->createItems($results);
	}

	protected function fixArray($array = []){
		unset($array[0]);
        unset($array[1]);
        return array_values($array);
	}

	protected function createItems(array $items = []){
		$new = new static;
		$new->rootfolder = $this->rootfolder;
		$new->items = $items;
		return $new;
	}

	private function config(){
        return (object) config('dpscan');
    }
}