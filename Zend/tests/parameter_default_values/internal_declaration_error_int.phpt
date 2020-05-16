--TEST--
The default value is an integer in the parent class method's signature.
--FILE--
<?php
class MyDateTime extends DateTime
{
    public function setTime(int $hour, int $minute, int $second = 0, bool $microseconds = false)
    {
    }
}
--EXPECTF--
Fatal error: Declaration of MyDateTime::setTime(int $hour, int $minute, int $second = 0, bool $microseconds = false) must be compatible with DateTime::setTime(int $hour, int $minute, int $second = 0, int $microseconds = 0) in %s on line %d
