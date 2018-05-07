<?php
namespace Dpscan;

use Dpscan\Contracts\Dpscan as DpscanInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Exception;

class Dpscan implements DpscanInterface
{

	/**
     * The rootfolder.
     *
     * @var string
     */

	protected $rootfolder;

	/**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

	public function __construct(){
		if(func_num_args() !== 0){
			if($this->config('debug')===true){
				throw new Exception("Arguments must be 0:", 1);
			}
			return;
		}
		$this->rootfolder = $this->setRootFolder();
	}

	public function setdir(string $dir){
		return $this->getdir($dir);
	}

	protected function getdir(string $dir = null){
		if(func_num_args() !== 1){
			if($this->config('debug')===true){
				throw new Exception("Arguments must be 0:", 1);
			}
			return;
		}
		if(strpos($dir,$this->rootfolder) === false){
			$dir = $this->rootfolder.DIRECTORY_SEPARATOR.$dir;
		}
		if(is_dir($dir) !== true){
			if($this->config('debug')===true){
				throw new Exception("not Dir:". $dir, 1);
			}
			return;
		}
		$this->rootfolder = $dir;
		return $this;
	}

	public function cache(int $minutes,string $key,string $option){
		return $this->getCache($minutes,$key,$option);
	}

	protected function getcache(int $minutes,string $key,string $option){
		$keylock = md5($key.$option);
		if($this->cacheMan()->has($keylock) === true){
			$this->checkUpdateCache($minutes,$key,$option);
				$cache = json_decode(
					base64_decode($this->cacheMan()->get($keylock)),true
				);
				return $this->createItems($cache);
		}
		$this->setCache($minutes,$key,$option);
		$this->setAutoCache($key,$option);
		return $this->getcache(0,$key,$option);
	}

	protected function setcache(int $minutes,string $key,string $option){
		$this->listmethod($option);
		$keylock = md5($key.$option);
		$this->cacheMan()->remember($keylock,$minutes, function ()
			use($option){
			$data = new static;
			$data = $data->setdir($this->rootfolder)->$option()->items;
		    return base64_encode(
		    	json_encode(
		    		$data
		    	)
		    );
		});
	}

	protected function listmethod(string $func){
		$listmethod = array_flip(get_class_methods(DpscanInterface::class));
		unset($listmethod['cache']);
		unset($listmethod['forgetcache']);
		unset($listmethod['items']);
		$listmethod = isset($listmethod[$func]);
		if($listmethod === false){
			if($this->config('debug')===true){
				throw new Exception("Unknow Action: ".$func, 1);
			}
		}
		return $listmethod;
	}

	protected function setAutoCache(string $key,string $option){
		if($this->config('cacheautoupdate') === true){
			$data = [];
			$lockey = md5($this->cachePath());
			if($this->cacheMan()->has($lockey)){
				$data = json_decode(
					base64_decode($this->cacheMan()->pull($lockey)),
					true
				);
			}
			$key = md5($key.$option);
			$this->cacheMan()->remember($lockey,$this->config('cacheduration'),function() use($key,$data){
				$data[$key]	= filemtime($this->rootfolder);
				return base64_encode(json_encode($data));
			});
		}
	}

	protected function getAutoCache(){
		$lockey = md5($this->cachePath());
		return json_decode(
					base64_decode($this->cacheMan()->get($lockey)),true);
	}

	protected function checkUpdateCache(int $minute,string $key,string $option){
		if($this->config('cacheautoupdate') === true){
			$cache = $this->getAutoCache();
			$lockey = (string) md5($key.$option);
			if($cache[$lockey] !== filemtime($this->rootfolder)){
				$this->forgetcache($key,$option);
				return $this->cache($minute, $key,$option);
			}
		}
	}

	public function forgetcache(string $key,string $option){
		return $this->setforgetcache($key,$option);
	}

	protected function setforgetcache(string $key, string $option){
		$key = md5($key.$option);
		if($this->cacheMan()->has($key)){
			$this->cacheMan()->forget($key);
			return $this;
		}
		if($this->config('debug')===true){
			throw new Exception("Not found key cache", 1);
		}
		return null;
	}

	protected function cacheMan(){
		if(func_num_args() !== 0){
			if($this->config('debug')===true){
				throw new Exception("Arguments must be 0:", 1);
			}
			return null;
		}
		$lc = new Container;
		$lc['config'] = [
        'cache.default' => 'file',
        'cache.stores.file' => [
            'driver' => 'file',
            'path' => $this->cachePath()
	        ]
	    ];
	    $lc['files'] = new Filesystem;
	    $cacheManager = new CacheManager($lc);
	    return $cacheManager->store();
	}

	public function rootchange(string $dir){
		return $this->setrootchange($dir);
	}

	protected function setrootchange(string $dir){
		$items = $this->resoleveItem();
		$key = array_keys($items);
		$results = [];
		for ($i=0; $i < count($items) ; $i++) {
			$item = str_replace(
				[$this->rootfolder,DIRECTORY_SEPARATOR],
				[$dir,'/'],
				$items[$key[$i]]);
			$results[$item] = $item;
		}
		$this->rootfolder = $dir;
		return $this->createItems($results);
	}

	public function get(){
		return $this->setget();
	}

	protected function setget(){
		return $this->createItems($this->resoleveItem());
	}

	public function all(){
		return $this->setall();
	}

	protected function setall(){
		return $this->getAllContent($this->resoleveItem());
	}

	public function onlydir(){
		return $this->setonlydir();
	}

	protected function setonlydir(){
		return $this->getResultByType(0);
	}

	protected function getResultByType(int $option=0){
		$range = range(0,1);
		if(isset($range[$option]) === false){
			return [];
		}
		$lists = $this->resoleveItem();
		$results = [];
		$key = array_keys($lists);
		for ($i=0; $i < count($lists); $i++){
			if(is_file($lists[$key[$i]])){
				$results[1][$lists[$key[$i]]]=$lists[$key[$i]];
			}
			if(is_dir($lists[$key[$i]])){
				$results[0][$lists[$key[$i]]]=$lists[$key[$i]];
			}
		}
		$results = (isset($results[$option]))?$results[$option]:[];
		return $this->createItems($results);
	}

	public function onlyfiles(){
		return $this->setonlyfiles();
	}

	protected function setonlyfiles(){
		return $this->getResultByType(1);
	}

	public function except(array $array = []){
		return $this->setexcept($array);
	}

	protected function setexcept(array $array = []){
		$lists = $this->resoleveItem();
		$lists = array_flip($lists);
		$key = array_keys($lists);
		for ($i=0; $i < count($array); $i++) {
			$items = $this->rootfolder.DIRECTORY_SEPARATOR.$array[$i];
			if(isset($lists[$items])!== null){
				unset($lists[$items]);
			}
		}
		return $this->createItems(array_values($lists));
	}

	protected function getContent(){
		return $this->setContent();
	}

	public function notcontains(array $array = []){
		return $this->setnotcontains($array);
	}

	protected function setnotcontains(array $array = []){
		return $this->getResultsByContains($array,$this->resoleveItem(),0);
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
		return $this->getResultsByContains($array,$this->resoleveItem(),1);
	}

	public function containsfiles(array $array = []){
		return $this->getResultsByContains($array,$this->onlyfiles()->items,1);
	}

	public function containsdir(array $array = []){
		return $this->getResultsByContains($array,$this->onlydir()->items,1);
	}

	protected function getResultsByContains(array $contains,array $lists,int $option){
		$range = range(0,1);
		if(isset($range[$option]) === false){
			return [];
		}
		if(count($lists) === 0){
			$lists = $this->resoleveItem();
		}
		$results = [];
		$key = array_keys($lists);
		for ($i=0; $i < count($contains); $i++) {
			$item = $contains[$i];
			for ($l=0; $l < count($lists); $l++) {
				if(strpos($lists[$key[$l]], $item) === false){
					if($option === 0){
						$results[$lists[$key[$l]]][$i] = $lists[$key[$l]];
					}
				}else{
					if($option === 1){
						$results[$lists[$key[$l]]] = $lists[$key[$l]];
					}
				}
			}
		}
		if($option===0){
			$newResult = array_values($results);
			$results = [];
			for ($i=0; $i < count($newResult); $i++) {
				if(count($newResult[$i]) === count($contains)){
					$results[$newResult[$i][0]] = $newResult[$i][0];
				}
			}
		}
		$results = (count(array_values($results)) !== 0)?$results:[];
		return $this->createItems($results);
	}

	public function regexcept(array $array = []){
		return $this->setRegExcept($array);
	}

	protected function setregexcept(array $array = []){
		return $this->getResultsByRegex($array,$this->get()->items,0);
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
		return $this->getResultsByRegex($array,$this->resoleveItem(),1);
	}

	public function regexonlyfiles(array $array = []){
		return $this->getResultsByRegex($array,$this->onlyfiles()->items,1);
	}

	public function regexonlydir(array $array = []){
		return $this->getResultsByRegex($array,$this->onlydir()->items,1);
	}

	protected function getResultsByRegex(array $contains,array $lists,int $option){
		$range = range(0,1);
		if(isset($range[$option]) === false){
			return [];
		}
		if(count($lists) === 0){
			$lists = $this->resoleveItem();
		}
		$key = array_keys($lists);
		$results = [];
		for ($i=0; $i < count($contains); $i++) {
			$item = $contains[$i];
			for ($l=0; $l < count($lists); $l++) {
				if(preg_match($item, $lists[$key[$l]]) === 0){
					if($option === 0){
						$results[$lists[$key[$l]]][$i] = $lists[$key[$l]];
					}
				}else{
					if($option === 1){
						$results[$lists[$key[$l]]] = $lists[$key[$l]];
					}
				}
			}
		}
		if($option===0){
			$newResult = array_values($results);
			$results = [];
			for ($i=0; $i < count($newResult); $i++) {
				if(count($newResult[$i]) === count($contains)){
					$results[$newResult[$i][0]] = $newResult[$i][0];
				}
			}
		}
		$results = (count(array_values($results)) !== 0)?$results:[];
		return $this->createItems($results);
	}

	protected function setContent(){
		if(func_num_args() !== 0){
			return;
		}
		if(str_contains($this->rootfolder,$this->setRootFolder()) !== true){
			throw new Exception($this->rootfolder
				." must be inside of config path", 1);
		}
		if(is_dir($this->rootfolder)){
		    if ($dh = opendir($this->rootfolder)) {
		        while (($file = readdir($dh)) !== false) {
		            if ($file != "." && $file != "..") {
			            if(strpos($this->protectedFile(),$file) === false){
				       	$item = $this->rootfolder.DIRECTORY_SEPARATOR.$file;
			           	$this->items[$item] = $item;
				    	}
			        }
		        }
		        closedir($dh);
		    }
		}
		return $this->createItems($this->items);
	}

	public function items(){
		return $this->getitems();
	}

	protected function getitems(){
		return $this->setitems();
	}

	protected function setitems(){
		return $this->items;
	}

	protected function getItemKey(){
		return array_keys($this->resoleveItem());
	}

	protected function getAllContent(array $lists,$now = 0,array $results = []){
		$total = count($lists);
		if($now !== $total){
		$key = array_keys($lists);
			$results[$lists[$key[$now]]] = $lists[$key[$now]];
			if(is_dir($lists[$key[$now]])){
				$newfolder = new static;
				$newfolder->rootfolder = $lists[$key[$now]];
				$new = $newfolder->all();
				$results = $results + $new->items;
			}
			$next = $now + 1;
			return $this->getAllContent($lists,$next,$results);
		}
		return $this->createItems($results);
	}

	protected function resoleveItem(){
		if(count($this->items) === 0){
			$this->items = $this->getContent()->items;
		}
		return $this->items;
	}

	protected function createItems(array $items){
		$new = new static;
		$new->rootfolder = $this->rootfolder;
		$new->items = $items;
		return $new;
	}

	protected function config(string $params = null){
		if(func_num_args() !== 1){
			throw new Exception("Arguments must be 1:", 1);
		}
		$config = $this->getConfig();
        return (true === isset($config[$params]))?
        $config[$params]:exit(1);
    }

    protected function getConfig(){
    	$path = realpath(getcwd()."/../");
    	$root = $path.DIRECTORY_SEPARATOR.
    		'config'.
    		DIRECTORY_SEPARATOR.
    		'dpscan.php';
    	if(file_exists($root) === true){
    		return include($root);
    	}else{
    		return $this->localConfig();
    	}
    }

    protected function localConfig(){
    	if(func_num_args() !== 0){
			return null;
		}
        return include(
        	__DIR__.
        	DIRECTORY_SEPARATOR.'config'.
        	DIRECTORY_SEPARATOR.'dpscan.php');
    }

    protected function setRootFolder(){
    	return (false === $this->config('root'))?
    	realpath(getcwd()."/../"):$this->config('root');
    }

    protected function protectedFile(){
    	return (null === $this->config('protected'))?
    	[]:implode(' ',$this->config('protected'));
    }

    protected function cachePath(){
		$path = $this->setRootFolder().$this->config('cache');
		if($path === $this->setRootFolder()){
			throw new Exception("Cache cant set on root dir:".$path, 1);
		}
		if(is_dir($path) === true){
			return $path;
		}
		throw new Exception("Not Found Cache Config: ".$path, 1);
	}
}