<?php

namespace Rwcoding\Tests\Pscc;

use PHPUnit\Framework\TestCase;
use Rwcoding\Pscc\Util\ConsoleUtil;

class ConsoleTest extends TestCase
{
    public function testConsole()
    {
        $console = new ConsoleUtil();
        $argv = [
            "bin.php",
            "start",
            "--host=127.0.0.1",
            "-p=8080",
            "d=1",
            "--enable-redis",
            "-enable-mysql",
            "server",
        ];
        list($script, $flags, $commands) = $console->parse($argv);
        $this->assertEquals("bin.php", $script);
        $this->assertEquals("127.0.0.1", $flags["host"]);
        $this->assertEquals("8080", $flags["p"]);
        $this->assertEquals("1", $flags["d"]);
        $this->assertTrue($flags["enable-redis"]);
        $this->assertTrue($flags["enable-mysql"]);

        $this->assertTrue($commands["start"]);
        $this->assertTrue($commands["server"]);
    }
}