<?php
namespace Dpscan\Contracts;

interface Dpscan{
	public function setdir(string $dir);
	public function get();
	public function all();
	public function cache(int $minutes,string $key,string $option);
	public function onlyfiles();
	public function onlydir();
	public function notcontains(array $array = []);
	public function notcontainsfiles(array $array = []);
	public function notcontainsdir(array $array = []);
	public function contains(array $array = []);
	public function containsfiles(array $array = []);
	public function containsdir(array $array = []);
	public function except(array $array);
	public function regexcept(array $array);
	public function regexceptfiles(array $array);
	public function regexceptdir(array $array);
	public function regexonly(array $array);
	public function regexonlyfiles(array $array);
	public function regexonlydir(array $array);
	public function items();
	public function rootchange(string $dir);
}