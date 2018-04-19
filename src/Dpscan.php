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

	public function __construct(array $items = []){
		if(func_num_args() !== 1){
			return;
		}
		$this->items = $items;
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
		$lists = $this->get()->items()->toArray();
		$class = get_class();
		$onlydir = new static(array_keys(array_flip($lists)));
		return $onlydir->setdir($this->rootfolder);
	}

	public function onlyfiles(){
		return $this->setonlyfiles();
	}

	protected function setonlyfiles(){
		$lists = array_keys($this->get()->items()->toArray());
		$onlydir = array_flip($this->onlydir()->items()->toArray());
		for ($i=0; $i < count($lists) ; $i++) {
				$item = $lists[$i];
			if(isset($onlydir[$item]) === true){
				unset($lists[$i]);
			}
		}
		$results = new static(array_values($lists));
		return $results->setdir($this->rootfolder);
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
			$this->items = $this->fixArray(scandir($this->rootfolder));
			$this->items = new static($this->items);
			return $this->items->setdir($this->rootfolder)->getAllContent();
		}
		return $this->items;
	}

	public function items(){
		return $this->getitems();
	}

	protected function getitems(){
		$this->items = new static($this->items);
		return $this->items->setitems();
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
				$newfolder = new static([]);
				$newitems = $newfolder->setdir($item)->get();
				$results = $results + $newitems->items;
			}
			$next = $now + 1;
			return $this->getAllContent($next,$results);
		}
		$results = new static($results);
		$results = $results->setdir($this->rootfolder);
		return $results;
	}

	protected function fixArray($array = []){
		unset($array[0]);
        unset($array[1]);
        return array_values($array);
	}

	private function config(){
        return (object) config('dpscan');
    }
}