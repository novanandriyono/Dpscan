<?php
namespace Tests;
use PHPUnit\Framework\TestCase;
use Dpscan\Support\Facades\Dpscan;

class DpscanTests extends TestCase
{
	public function testone()
    {
        $stub = $this->createMock(Dpscan::class);
        $stub->method('items')->willReturn(null);
        $this->assertSame(null, $stub->doSomething());
    }
}