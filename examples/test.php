<?php
interface A
{
    public function g();
}

class B implements A
{
    public function g()
    {
        // TODO: Implement g() method.
    }
}

class C extends B
{

}

$c = new C();
var_dump($c instanceof A);