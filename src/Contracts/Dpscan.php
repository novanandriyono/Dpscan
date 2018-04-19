<?php
namespace Dpscan\Contracts;

interface Dpscan{
	public function setdir($dir);
	public function get();
	public function onlyfiles();
	public function onlydir();
	public function items();
}