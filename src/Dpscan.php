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

	public function setdir($dir = null){
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
		return $this;
	}

	public function get(){
		return $this->getContent();
	}

	public function onlydir(){
		return $this->setonlydir();
	}

	protected function setonlydir(){
		$items = array_keys(array_flip($this->get()->items));
		return $this->createItems($items);
	}

	public function onlyfiles(){
		return $this->setonlyfiles();
	}

	protected function setonlyfiles(){
		$lists = array_keys($this->get()->items);
		$onlydir = array_flip($this->onlydir()->items);
		for ($i=0; $i < count($lists) ; $i++) {
			$item = $lists[$i];
			if(isset($onlydir[$item]) === true){
				unset($lists[$i]);
			}
		}
		return $this->createItems(array_values($lists));
	}

	public function except(array $array = []){
		return $this->setexcept($array);
	}

	protected function setexcept(array $array = []){
		$lists = array_flip($this->items);
		for ($i=0; $i < count($array); $i++) {
			$items = $this->rootfolder.DIRECTORY_SEPARATOR.$array[$i];
			if(isset($lists[$items])){
				unset($lists[$items]);
			}
		}
		return $this->createItems(array_values(array_flip($lists)));
	}

	protected function getContent(){
		return $this->setContent();
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
		$this->items = new static($this);
		return $this->items->items->setitems();
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
				$newitems = $newfolder->setdir($item)->get();
				$results = $results + $newitems->items;
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