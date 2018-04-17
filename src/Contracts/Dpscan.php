<?php
namespace Dpscan\Contracts;

interface Dpscan{
	public function setdir($dir);
	public function get();
	public function all();
	public function results();
	public function getfiles();
	public function getdir();
}