--TEST--
Using unsupported types with operators
--FILE--
<?php

$binops = [
    '+',
    '-',
    '*',
    '/',
    '%',
    '**',
    '<<',
    '>>',
    '&',
    '|',
    '^',
    // Works on booleans, never errors.
    'xor',
    // Only generates errors that string conversion emits.
    '.',
];
$illegalValues = [
    '[]',
    'new stdClass',
    'STDOUT',
];
$legalValues = [
    'null',
    'true',
    'false',
    '2',
    '3.5',
    '"123"',
    '"foo"', // Semi-legal.
];

set_error_handler(function($errno, $errstr) {
    assert($errno == E_WARNING);
    echo "Warning: $errstr\n";
});

function evalBinOp(string $op, string $value1, string $value2) {
    try {
        eval("return $value1 $op $value2;");
        echo "No error for $value1 $op $value2\n";
    } catch (Throwable $e) {
        echo $e->getMessage() . "\n";
    }
}

function evalAssignOp(string $op, string $value1, string $value2) {
    $x = $origX = eval("return $value1;");
    try {
        eval("\$x $op= $value2;");
        echo "No error for $value1 $op= $value2\n";
    } catch (Throwable $e) {
        echo $e->getMessage() . "\n";
        if ($x !== $origX) {
            die("Value corrupted!");
        }
    }
}

echo "BINARY OP:\n";
foreach ($binops as $op) {
    foreach ($illegalValues as $illegalValue1) {
        foreach ($illegalValues as $illegalValue2) {
            evalBinOp($op, $illegalValue1, $illegalValue2);
        }
    }
    foreach ($illegalValues as $illegalValue) {
        foreach ($legalValues as $legalValue) {
            evalBinOp($op, $illegalValue, $legalValue);
            evalBinOp($op, $legalValue, $illegalValue);
        }
    }
}

echo "\n\nASSIGN OP:\n";
foreach ($binops as $op) {
    if ($op === 'xor') continue;

    foreach ($illegalValues as $illegalValue1) {
        foreach ($illegalValues as $illegalValue2) {
            evalAssignOp($op, $illegalValue1, $illegalValue2);
        }
    }
    foreach ($illegalValues as $illegalValue) {
        foreach ($legalValues as $legalValue) {
            evalAssignOp($op, $illegalValue, $legalValue);
            evalAssignOp($op, $legalValue, $illegalValue);
        }
    }
}

echo "\n\nUNARY OP:\n";
foreach ($illegalValues as $illegalValue) {
    try {
        eval("return ~$illegalValue;");
        echo "No error for ~$copy\n";
    } catch (TypeError $e) {
        echo $e->getMessage() . "\n";
    }
}

echo "\n\nINCDEC:\n";
foreach ($illegalValues as $illegalValue) {
    $copy = eval("return $illegalValue;");
    try {
        $copy++;
        echo "No error for $copy++\n";
    } catch (TypeError $e) {
        echo $e->getMessage() . "\n";
    }
    $copy = eval("return $illegalValue;");
    try {
        $copy--;
        echo "No error for $copy--\n";
    } catch (TypeError $e) {
        echo $e->getMessage() . "\n";
    }
}

?>
--EXPECT--
BINARY OP:
No error for [] + []
Unsupported operand types: array + object
Unsupported operand types: array + resource
Unsupported operand types: object + array
Unsupported operand types: object + object
Unsupported operand types: object + resource
Unsupported operand types: resource + array
Unsupported operand types: resource + object
Unsupported operand types: resource + resource
Unsupported operand types: array + null
Unsupported operand types: null + array
Unsupported operand types: array + bool
Unsupported operand types: bool + array
Unsupported operand types: array + bool
Unsupported operand types: bool + array
Unsupported operand types: array + int
Unsupported operand types: int + array
Unsupported operand types: array + float
Unsupported operand types: float + array
Unsupported operand types: array + string
Unsupported operand types: string + array
Unsupported operand types: array + string
Warning: A non-numeric value encountered
Unsupported operand types: string + array
Unsupported operand types: object + null
Unsupported operand types: null + object
Unsupported operand types: object + bool
Unsupported operand types: bool + object
Unsupported operand types: object + bool
Unsupported operand types: bool + object
Unsupported operand types: object + int
Unsupported operand types: int + object
Unsupported operand types: object + float
Unsupported operand types: float + object
Unsupported operand types: object + string
Unsupported operand types: string + object
Unsupported operand types: object + string
Warning: A non-numeric value encountered
Unsupported operand types: string + object
Unsupported operand types: resource + null
Unsupported operand types: null + resource
Unsupported operand types: resource + bool
Unsupported operand types: bool + resource
Unsupported operand types: resource + bool
Unsupported operand types: bool + resource
Unsupported operand types: resource + int
Unsupported operand types: int + resource
Unsupported operand types: resource + float
Unsupported operand types: float + resource
Unsupported operand types: resource + string
Unsupported operand types: string + resource
Unsupported operand types: resource + string
Warning: A non-numeric value encountered
Unsupported operand types: string + resource
Unsupported operand types: array - array
Unsupported operand types: array - object
Unsupported operand types: array - resource
Unsupported operand types: object - array
Unsupported operand types: object - object
Unsupported operand types: object - resource
Unsupported operand types: resource - array
Unsupported operand types: resource - object
Unsupported operand types: resource - resource
Unsupported operand types: array - null
Unsupported operand types: null - array
Unsupported operand types: array - bool
Unsupported operand types: bool - array
Unsupported operand types: array - bool
Unsupported operand types: bool - array
Unsupported operand types: array - int
Unsupported operand types: int - array
Unsupported operand types: array - float
Unsupported operand types: float - array
Unsupported operand types: array - string
Unsupported operand types: string - array
Unsupported operand types: array - string
Warning: A non-numeric value encountered
Unsupported operand types: string - array
Unsupported operand types: object - null
Unsupported operand types: null - object
Unsupported operand types: object - bool
Unsupported operand types: bool - object
Unsupported operand types: object - bool
Unsupported operand types: bool - object
Unsupported operand types: object - int
Unsupported operand types: int - object
Unsupported operand types: object - float
Unsupported operand types: float - object
Unsupported operand types: object - string
Unsupported operand types: string - object
Unsupported operand types: object - string
Warning: A non-numeric value encountered
Unsupported operand types: string - object
Unsupported operand types: resource - null
Unsupported operand types: null - resource
Unsupported operand types: resource - bool
Unsupported operand types: bool - resource
Unsupported operand types: resource - bool
Unsupported operand types: bool - resource
Unsupported operand types: resource - int
Unsupported operand types: int - resource
Unsupported operand types: resource - float
Unsupported operand types: float - resource
Unsupported operand types: resource - string
Unsupported operand types: string - resource
Unsupported operand types: resource - string
Warning: A non-numeric value encountered
Unsupported operand types: string - resource
Unsupported operand types: array * array
Unsupported operand types: object * array
Unsupported operand types: resource * array
Unsupported operand types: object * array
Unsupported operand types: object * object
Unsupported operand types: object * resource
Unsupported operand types: resource * array
Unsupported operand types: object * resource
Unsupported operand types: resource * resource
Unsupported operand types: array * null
Unsupported operand types: null * array
Unsupported operand types: array * bool
Unsupported operand types: bool * array
Unsupported operand types: array * bool
Unsupported operand types: bool * array
Unsupported operand types: array * int
Unsupported operand types: int * array
Unsupported operand types: array * float
Unsupported operand types: float * array
Unsupported operand types: array * string
Unsupported operand types: string * array
Unsupported operand types: array * string
Warning: A non-numeric value encountered
Unsupported operand types: string * array
Unsupported operand types: object * null
Unsupported operand types: object * null
Unsupported operand types: object * bool
Unsupported operand types: object * bool
Unsupported operand types: object * bool
Unsupported operand types: object * bool
Unsupported operand types: object * int
Unsupported operand types: object * int
Unsupported operand types: object * float
Unsupported operand types: object * float
Unsupported operand types: object * string
Unsupported operand types: object * string
Unsupported operand types: object * string
Unsupported operand types: object * string
Unsupported operand types: resource * null
Unsupported operand types: resource * null
Unsupported operand types: resource * bool
Unsupported operand types: resource * bool
Unsupported operand types: resource * bool
Unsupported operand types: resource * bool
Unsupported operand types: resource * int
Unsupported operand types: resource * int
Unsupported operand types: resource * float
Unsupported operand types: resource * float
Unsupported operand types: resource * string
Unsupported operand types: resource * string
Unsupported operand types: resource * string
Unsupported operand types: resource * string
Unsupported operand types: array / array
Unsupported operand types: array / object
Unsupported operand types: array / resource
Unsupported operand types: object / array
Unsupported operand types: object / object
Unsupported operand types: object / resource
Unsupported operand types: resource / array
Unsupported operand types: resource / object
Unsupported operand types: resource / resource
Unsupported operand types: array / null
Unsupported operand types: null / array
Unsupported operand types: array / bool
Unsupported operand types: bool / array
Unsupported operand types: array / bool
Unsupported operand types: bool / array
Unsupported operand types: array / int
Unsupported operand types: int / array
Unsupported operand types: array / float
Unsupported operand types: float / array
Unsupported operand types: array / string
Unsupported operand types: string / array
Unsupported operand types: array / string
Warning: A non-numeric value encountered
Unsupported operand types: string / array
Unsupported operand types: object / null
Unsupported operand types: null / object
Unsupported operand types: object / bool
Unsupported operand types: bool / object
Unsupported operand types: object / bool
Unsupported operand types: bool / object
Unsupported operand types: object / int
Unsupported operand types: int / object
Unsupported operand types: object / float
Unsupported operand types: float / object
Unsupported operand types: object / string
Unsupported operand types: string / object
Unsupported operand types: object / string
Warning: A non-numeric value encountered
Unsupported operand types: string / object
Unsupported operand types: resource / null
Unsupported operand types: null / resource
Unsupported operand types: resource / bool
Unsupported operand types: bool / resource
Unsupported operand types: resource / bool
Unsupported operand types: bool / resource
Unsupported operand types: resource / int
Unsupported operand types: int / resource
Unsupported operand types: resource / float
Unsupported operand types: float / resource
Unsupported operand types: resource / string
Unsupported operand types: string / resource
Unsupported operand types: resource / string
Warning: A non-numeric value encountered
Unsupported operand types: string / resource
Unsupported operand types: array % array
Unsupported operand types: array % object
Unsupported operand types: array % resource
Unsupported operand types: object % array
Unsupported operand types: object % object
Unsupported operand types: object % resource
Unsupported operand types: resource % array
Unsupported operand types: resource % object
Unsupported operand types: resource % resource
Unsupported operand types: array % null
Unsupported operand types: null % array
Unsupported operand types: array % bool
Unsupported operand types: bool % array
Unsupported operand types: array % bool
Unsupported operand types: bool % array
Unsupported operand types: array % int
Unsupported operand types: int % array
Unsupported operand types: array % float
Unsupported operand types: float % array
Unsupported operand types: array % string
Unsupported operand types: string % array
Unsupported operand types: array % string
Warning: A non-numeric value encountered
Unsupported operand types: string % array
Unsupported operand types: object % null
Unsupported operand types: null % object
Unsupported operand types: object % bool
Unsupported operand types: bool % object
Unsupported operand types: object % bool
Unsupported operand types: bool % object
Unsupported operand types: object % int
Unsupported operand types: int % object
Unsupported operand types: object % float
Unsupported operand types: float % object
Unsupported operand types: object % string
Unsupported operand types: string % object
Unsupported operand types: object % string
Warning: A non-numeric value encountered
Unsupported operand types: string % object
Unsupported operand types: resource % null
Unsupported operand types: null % resource
Unsupported operand types: resource % bool
Unsupported operand types: bool % resource
Unsupported operand types: resource % bool
Unsupported operand types: bool % resource
Unsupported operand types: resource % int
Unsupported operand types: int % resource
Unsupported operand types: resource % float
Unsupported operand types: float % resource
Unsupported operand types: resource % string
Unsupported operand types: string % resource
Unsupported operand types: resource % string
Warning: A non-numeric value encountered
Unsupported operand types: string % resource
Unsupported operand types: array ** array
Unsupported operand types: array ** object
Unsupported operand types: array ** resource
Unsupported operand types: object ** array
Unsupported operand types: object ** object
Unsupported operand types: object ** resource
Unsupported operand types: resource ** array
Unsupported operand types: resource ** object
Unsupported operand types: resource ** resource
Unsupported operand types: array ** null
Unsupported operand types: null ** array
Unsupported operand types: array ** bool
Unsupported operand types: bool ** array
Unsupported operand types: array ** bool
Unsupported operand types: bool ** array
Unsupported operand types: array ** int
Unsupported operand types: int ** array
Unsupported operand types: array ** float
Unsupported operand types: float ** array
Unsupported operand types: array ** string
Unsupported operand types: string ** array
Unsupported operand types: array ** string
Warning: A non-numeric value encountered
Unsupported operand types: string ** array
Unsupported operand types: object ** null
Unsupported operand types: null ** object
Unsupported operand types: object ** bool
Unsupported operand types: bool ** object
Unsupported operand types: object ** bool
Unsupported operand types: bool ** object
Unsupported operand types: object ** int
Unsupported operand types: int ** object
Unsupported operand types: object ** float
Unsupported operand types: float ** object
Unsupported operand types: object ** string
Unsupported operand types: string ** object
Unsupported operand types: object ** string
Warning: A non-numeric value encountered
Unsupported operand types: string ** object
Unsupported operand types: resource ** null
Unsupported operand types: null ** resource
Unsupported operand types: resource ** bool
Unsupported operand types: bool ** resource
Unsupported operand types: resource ** bool
Unsupported operand types: bool ** resource
Unsupported operand types: resource ** int
Unsupported operand types: int ** resource
Unsupported operand types: resource ** float
Unsupported operand types: float ** resource
Unsupported operand types: resource ** string
Unsupported operand types: string ** resource
Unsupported operand types: resource ** string
Warning: A non-numeric value encountered
Unsupported operand types: string ** resource
Unsupported operand types: array << array
Unsupported operand types: array << object
Unsupported operand types: array << resource
Unsupported operand types: object << array
Unsupported operand types: object << object
Unsupported operand types: object << resource
Unsupported operand types: resource << array
Unsupported operand types: resource << object
Unsupported operand types: resource << resource
Unsupported operand types: array << null
Unsupported operand types: null << array
Unsupported operand types: array << bool
Unsupported operand types: bool << array
Unsupported operand types: array << bool
Unsupported operand types: bool << array
Unsupported operand types: array << int
Unsupported operand types: int << array
Unsupported operand types: array << float
Unsupported operand types: float << array
Unsupported operand types: array << string
Unsupported operand types: string << array
Unsupported operand types: array << string
Warning: A non-numeric value encountered
Unsupported operand types: string << array
Unsupported operand types: object << null
Unsupported operand types: null << object
Unsupported operand types: object << bool
Unsupported operand types: bool << object
Unsupported operand types: object << bool
Unsupported operand types: bool << object
Unsupported operand types: object << int
Unsupported operand types: int << object
Unsupported operand types: object << float
Unsupported operand types: float << object
Unsupported operand types: object << string
Unsupported operand types: string << object
Unsupported operand types: object << string
Warning: A non-numeric value encountered
Unsupported operand types: string << object
Unsupported operand types: resource << null
Unsupported operand types: null << resource
Unsupported operand types: resource << bool
Unsupported operand types: bool << resource
Unsupported operand types: resource << bool
Unsupported operand types: bool << resource
Unsupported operand types: resource << int
Unsupported operand types: int << resource
Unsupported operand types: resource << float
Unsupported operand types: float << resource
Unsupported operand types: resource << string
Unsupported operand types: string << resource
Unsupported operand types: resource << string
Warning: A non-numeric value encountered
Unsupported operand types: string << resource
Unsupported operand types: array >> array
Unsupported operand types: array >> object
Unsupported operand types: array >> resource
Unsupported operand types: object >> array
Unsupported operand types: object >> object
Unsupported operand types: object >> resource
Unsupported operand types: resource >> array
Unsupported operand types: resource >> object
Unsupported operand types: resource >> resource
Unsupported operand types: array >> null
Unsupported operand types: null >> array
Unsupported operand types: array >> bool
Unsupported operand types: bool >> array
Unsupported operand types: array >> bool
Unsupported operand types: bool >> array
Unsupported operand types: array >> int
Unsupported operand types: int >> array
Unsupported operand types: array >> float
Unsupported operand types: float >> array
Unsupported operand types: array >> string
Unsupported operand types: string >> array
Unsupported operand types: array >> string
Warning: A non-numeric value encountered
Unsupported operand types: string >> array
Unsupported operand types: object >> null
Unsupported operand types: null >> object
Unsupported operand types: object >> bool
Unsupported operand types: bool >> object
Unsupported operand types: object >> bool
Unsupported operand types: bool >> object
Unsupported operand types: object >> int
Unsupported operand types: int >> object
Unsupported operand types: object >> float
Unsupported operand types: float >> object
Unsupported operand types: object >> string
Unsupported operand types: string >> object
Unsupported operand types: object >> string
Warning: A non-numeric value encountered
Unsupported operand types: string >> object
Unsupported operand types: resource >> null
Unsupported operand types: null >> resource
Unsupported operand types: resource >> bool
Unsupported operand types: bool >> resource
Unsupported operand types: resource >> bool
Unsupported operand types: bool >> resource
Unsupported operand types: resource >> int
Unsupported operand types: int >> resource
Unsupported operand types: resource >> float
Unsupported operand types: float >> resource
Unsupported operand types: resource >> string
Unsupported operand types: string >> resource
Unsupported operand types: resource >> string
Warning: A non-numeric value encountered
Unsupported operand types: string >> resource
Unsupported operand types: array & array
Unsupported operand types: object & array
Unsupported operand types: resource & array
Unsupported operand types: object & array
Unsupported operand types: object & object
Unsupported operand types: object & resource
Unsupported operand types: resource & array
Unsupported operand types: object & resource
Unsupported operand types: resource & resource
Unsupported operand types: array & null
Unsupported operand types: null & array
Unsupported operand types: array & bool
Unsupported operand types: bool & array
Unsupported operand types: array & bool
Unsupported operand types: bool & array
Unsupported operand types: array & int
Unsupported operand types: int & array
Unsupported operand types: array & float
Unsupported operand types: float & array
Unsupported operand types: array & string
Unsupported operand types: string & array
Unsupported operand types: array & string
Warning: A non-numeric value encountered
Unsupported operand types: string & array
Unsupported operand types: object & null
Unsupported operand types: object & null
Unsupported operand types: object & bool
Unsupported operand types: object & bool
Unsupported operand types: object & bool
Unsupported operand types: object & bool
Unsupported operand types: object & int
Unsupported operand types: object & int
Unsupported operand types: object & float
Unsupported operand types: object & float
Unsupported operand types: object & string
Unsupported operand types: object & string
Unsupported operand types: object & string
Unsupported operand types: object & string
Unsupported operand types: resource & null
Unsupported operand types: resource & null
Unsupported operand types: resource & bool
Unsupported operand types: resource & bool
Unsupported operand types: resource & bool
Unsupported operand types: resource & bool
Unsupported operand types: resource & int
Unsupported operand types: resource & int
Unsupported operand types: resource & float
Unsupported operand types: resource & float
Unsupported operand types: resource & string
Unsupported operand types: resource & string
Unsupported operand types: resource & string
Unsupported operand types: resource & string
Unsupported operand types: array | array
Unsupported operand types: object | array
Unsupported operand types: resource | array
Unsupported operand types: object | array
Unsupported operand types: object | object
Unsupported operand types: object | resource
Unsupported operand types: resource | array
Unsupported operand types: object | resource
Unsupported operand types: resource | resource
Unsupported operand types: array | null
Unsupported operand types: null | array
Unsupported operand types: array | bool
Unsupported operand types: bool | array
Unsupported operand types: array | bool
Unsupported operand types: bool | array
Unsupported operand types: array | int
Unsupported operand types: int | array
Unsupported operand types: array | float
Unsupported operand types: float | array
Unsupported operand types: array | string
Unsupported operand types: string | array
Unsupported operand types: array | string
Warning: A non-numeric value encountered
Unsupported operand types: string | array
Unsupported operand types: object | null
Unsupported operand types: object | null
Unsupported operand types: object | bool
Unsupported operand types: object | bool
Unsupported operand types: object | bool
Unsupported operand types: object | bool
Unsupported operand types: object | int
Unsupported operand types: object | int
Unsupported operand types: object | float
Unsupported operand types: object | float
Unsupported operand types: object | string
Unsupported operand types: object | string
Unsupported operand types: object | string
Unsupported operand types: object | string
Unsupported operand types: resource | null
Unsupported operand types: resource | null
Unsupported operand types: resource | bool
Unsupported operand types: resource | bool
Unsupported operand types: resource | bool
Unsupported operand types: resource | bool
Unsupported operand types: resource | int
Unsupported operand types: resource | int
Unsupported operand types: resource | float
Unsupported operand types: resource | float
Unsupported operand types: resource | string
Unsupported operand types: resource | string
Unsupported operand types: resource | string
Unsupported operand types: resource | string
Unsupported operand types: array ^ array
Unsupported operand types: object ^ array
Unsupported operand types: resource ^ array
Unsupported operand types: object ^ array
Unsupported operand types: object ^ object
Unsupported operand types: object ^ resource
Unsupported operand types: resource ^ array
Unsupported operand types: object ^ resource
Unsupported operand types: resource ^ resource
Unsupported operand types: array ^ null
Unsupported operand types: null ^ array
Unsupported operand types: array ^ bool
Unsupported operand types: bool ^ array
Unsupported operand types: array ^ bool
Unsupported operand types: bool ^ array
Unsupported operand types: array ^ int
Unsupported operand types: int ^ array
Unsupported operand types: array ^ float
Unsupported operand types: float ^ array
Unsupported operand types: array ^ string
Unsupported operand types: string ^ array
Unsupported operand types: array ^ string
Warning: A non-numeric value encountered
Unsupported operand types: string ^ array
Unsupported operand types: object ^ null
Unsupported operand types: object ^ null
Unsupported operand types: object ^ bool
Unsupported operand types: object ^ bool
Unsupported operand types: object ^ bool
Unsupported operand types: object ^ bool
Unsupported operand types: object ^ int
Unsupported operand types: object ^ int
Unsupported operand types: object ^ float
Unsupported operand types: object ^ float
Unsupported operand types: object ^ string
Unsupported operand types: object ^ string
Unsupported operand types: object ^ string
Unsupported operand types: object ^ string
Unsupported operand types: resource ^ null
Unsupported operand types: resource ^ null
Unsupported operand types: resource ^ bool
Unsupported operand types: resource ^ bool
Unsupported operand types: resource ^ bool
Unsupported operand types: resource ^ bool
Unsupported operand types: resource ^ int
Unsupported operand types: resource ^ int
Unsupported operand types: resource ^ float
Unsupported operand types: resource ^ float
Unsupported operand types: resource ^ string
Unsupported operand types: resource ^ string
Unsupported operand types: resource ^ string
Unsupported operand types: resource ^ string
No error for [] xor []
No error for [] xor new stdClass
No error for [] xor STDOUT
No error for new stdClass xor []
No error for new stdClass xor new stdClass
No error for new stdClass xor STDOUT
No error for STDOUT xor []
No error for STDOUT xor new stdClass
No error for STDOUT xor STDOUT
No error for [] xor null
No error for null xor []
No error for [] xor true
No error for true xor []
No error for [] xor false
No error for false xor []
No error for [] xor 2
No error for 2 xor []
No error for [] xor 3.5
No error for 3.5 xor []
No error for [] xor "123"
No error for "123" xor []
No error for [] xor "foo"
No error for "foo" xor []
No error for new stdClass xor null
No error for null xor new stdClass
No error for new stdClass xor true
No error for true xor new stdClass
No error for new stdClass xor false
No error for false xor new stdClass
No error for new stdClass xor 2
No error for 2 xor new stdClass
No error for new stdClass xor 3.5
No error for 3.5 xor new stdClass
No error for new stdClass xor "123"
No error for "123" xor new stdClass
No error for new stdClass xor "foo"
No error for "foo" xor new stdClass
No error for STDOUT xor null
No error for null xor STDOUT
No error for STDOUT xor true
No error for true xor STDOUT
No error for STDOUT xor false
No error for false xor STDOUT
No error for STDOUT xor 2
No error for 2 xor STDOUT
No error for STDOUT xor 3.5
No error for 3.5 xor STDOUT
No error for STDOUT xor "123"
No error for "123" xor STDOUT
No error for STDOUT xor "foo"
No error for "foo" xor STDOUT
Warning: Array to string conversion
Warning: Array to string conversion
No error for [] . []
Warning: Array to string conversion
Object of class stdClass could not be converted to string
Warning: Array to string conversion
No error for [] . STDOUT
Warning: Array to string conversion
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Warning: Array to string conversion
No error for STDOUT . []
Object of class stdClass could not be converted to string
No error for STDOUT . STDOUT
Warning: Array to string conversion
No error for [] . null
Warning: Array to string conversion
No error for null . []
Warning: Array to string conversion
No error for [] . true
Warning: Array to string conversion
No error for true . []
Warning: Array to string conversion
No error for [] . false
Warning: Array to string conversion
No error for false . []
Warning: Array to string conversion
No error for [] . 2
Warning: Array to string conversion
No error for 2 . []
Warning: Array to string conversion
No error for [] . 3.5
Warning: Array to string conversion
No error for 3.5 . []
Warning: Array to string conversion
No error for [] . "123"
Warning: Array to string conversion
No error for "123" . []
Warning: Array to string conversion
No error for [] . "foo"
Warning: Array to string conversion
No error for "foo" . []
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
No error for STDOUT . null
No error for null . STDOUT
No error for STDOUT . true
No error for true . STDOUT
No error for STDOUT . false
No error for false . STDOUT
No error for STDOUT . 2
No error for 2 . STDOUT
No error for STDOUT . 3.5
No error for 3.5 . STDOUT
No error for STDOUT . "123"
No error for "123" . STDOUT
No error for STDOUT . "foo"
No error for "foo" . STDOUT


ASSIGN OP:
No error for [] += []
Unsupported operand types: array + object
Unsupported operand types: array + resource
Unsupported operand types: object + array
Unsupported operand types: object + object
Unsupported operand types: object + resource
Unsupported operand types: resource + array
Unsupported operand types: resource + object
Unsupported operand types: resource + resource
Unsupported operand types: array + null
Unsupported operand types: null + array
Unsupported operand types: array + bool
Unsupported operand types: bool + array
Unsupported operand types: array + bool
Unsupported operand types: bool + array
Unsupported operand types: array + int
Unsupported operand types: int + array
Unsupported operand types: array + float
Unsupported operand types: float + array
Unsupported operand types: array + string
Unsupported operand types: string + array
Unsupported operand types: array + string
Warning: A non-numeric value encountered
Unsupported operand types: string + array
Unsupported operand types: object + null
Unsupported operand types: null + object
Unsupported operand types: object + bool
Unsupported operand types: bool + object
Unsupported operand types: object + bool
Unsupported operand types: bool + object
Unsupported operand types: object + int
Unsupported operand types: int + object
Unsupported operand types: object + float
Unsupported operand types: float + object
Unsupported operand types: object + string
Unsupported operand types: string + object
Unsupported operand types: object + string
Warning: A non-numeric value encountered
Unsupported operand types: string + object
Unsupported operand types: resource + null
Unsupported operand types: null + resource
Unsupported operand types: resource + bool
Unsupported operand types: bool + resource
Unsupported operand types: resource + bool
Unsupported operand types: bool + resource
Unsupported operand types: resource + int
Unsupported operand types: int + resource
Unsupported operand types: resource + float
Unsupported operand types: float + resource
Unsupported operand types: resource + string
Unsupported operand types: string + resource
Unsupported operand types: resource + string
Warning: A non-numeric value encountered
Unsupported operand types: string + resource
Unsupported operand types: array - array
Unsupported operand types: array - object
Unsupported operand types: array - resource
Unsupported operand types: object - array
Unsupported operand types: object - object
Unsupported operand types: object - resource
Unsupported operand types: resource - array
Unsupported operand types: resource - object
Unsupported operand types: resource - resource
Unsupported operand types: array - null
Unsupported operand types: null - array
Unsupported operand types: array - bool
Unsupported operand types: bool - array
Unsupported operand types: array - bool
Unsupported operand types: bool - array
Unsupported operand types: array - int
Unsupported operand types: int - array
Unsupported operand types: array - float
Unsupported operand types: float - array
Unsupported operand types: array - string
Unsupported operand types: string - array
Unsupported operand types: array - string
Warning: A non-numeric value encountered
Unsupported operand types: string - array
Unsupported operand types: object - null
Unsupported operand types: null - object
Unsupported operand types: object - bool
Unsupported operand types: bool - object
Unsupported operand types: object - bool
Unsupported operand types: bool - object
Unsupported operand types: object - int
Unsupported operand types: int - object
Unsupported operand types: object - float
Unsupported operand types: float - object
Unsupported operand types: object - string
Unsupported operand types: string - object
Unsupported operand types: object - string
Warning: A non-numeric value encountered
Unsupported operand types: string - object
Unsupported operand types: resource - null
Unsupported operand types: null - resource
Unsupported operand types: resource - bool
Unsupported operand types: bool - resource
Unsupported operand types: resource - bool
Unsupported operand types: bool - resource
Unsupported operand types: resource - int
Unsupported operand types: int - resource
Unsupported operand types: resource - float
Unsupported operand types: float - resource
Unsupported operand types: resource - string
Unsupported operand types: string - resource
Unsupported operand types: resource - string
Warning: A non-numeric value encountered
Unsupported operand types: string - resource
Unsupported operand types: array * array
Unsupported operand types: array * object
Unsupported operand types: array * resource
Unsupported operand types: object * array
Unsupported operand types: object * object
Unsupported operand types: object * resource
Unsupported operand types: resource * array
Unsupported operand types: resource * object
Unsupported operand types: resource * resource
Unsupported operand types: array * null
Unsupported operand types: null * array
Unsupported operand types: array * bool
Unsupported operand types: bool * array
Unsupported operand types: array * bool
Unsupported operand types: bool * array
Unsupported operand types: array * int
Unsupported operand types: int * array
Unsupported operand types: array * float
Unsupported operand types: float * array
Unsupported operand types: array * string
Unsupported operand types: string * array
Unsupported operand types: array * string
Warning: A non-numeric value encountered
Unsupported operand types: string * array
Unsupported operand types: object * null
Unsupported operand types: null * object
Unsupported operand types: object * bool
Unsupported operand types: bool * object
Unsupported operand types: object * bool
Unsupported operand types: bool * object
Unsupported operand types: object * int
Unsupported operand types: int * object
Unsupported operand types: object * float
Unsupported operand types: float * object
Unsupported operand types: object * string
Unsupported operand types: string * object
Unsupported operand types: object * string
Warning: A non-numeric value encountered
Unsupported operand types: string * object
Unsupported operand types: resource * null
Unsupported operand types: null * resource
Unsupported operand types: resource * bool
Unsupported operand types: bool * resource
Unsupported operand types: resource * bool
Unsupported operand types: bool * resource
Unsupported operand types: resource * int
Unsupported operand types: int * resource
Unsupported operand types: resource * float
Unsupported operand types: float * resource
Unsupported operand types: resource * string
Unsupported operand types: string * resource
Unsupported operand types: resource * string
Warning: A non-numeric value encountered
Unsupported operand types: string * resource
Unsupported operand types: array / array
Unsupported operand types: array / object
Unsupported operand types: array / resource
Unsupported operand types: object / array
Unsupported operand types: object / object
Unsupported operand types: object / resource
Unsupported operand types: resource / array
Unsupported operand types: resource / object
Unsupported operand types: resource / resource
Unsupported operand types: array / null
Unsupported operand types: null / array
Unsupported operand types: array / bool
Unsupported operand types: bool / array
Unsupported operand types: array / bool
Unsupported operand types: bool / array
Unsupported operand types: array / int
Unsupported operand types: int / array
Unsupported operand types: array / float
Unsupported operand types: float / array
Unsupported operand types: array / string
Unsupported operand types: string / array
Unsupported operand types: array / string
Warning: A non-numeric value encountered
Unsupported operand types: string / array
Unsupported operand types: object / null
Unsupported operand types: null / object
Unsupported operand types: object / bool
Unsupported operand types: bool / object
Unsupported operand types: object / bool
Unsupported operand types: bool / object
Unsupported operand types: object / int
Unsupported operand types: int / object
Unsupported operand types: object / float
Unsupported operand types: float / object
Unsupported operand types: object / string
Unsupported operand types: string / object
Unsupported operand types: object / string
Warning: A non-numeric value encountered
Unsupported operand types: string / object
Unsupported operand types: resource / null
Unsupported operand types: null / resource
Unsupported operand types: resource / bool
Unsupported operand types: bool / resource
Unsupported operand types: resource / bool
Unsupported operand types: bool / resource
Unsupported operand types: resource / int
Unsupported operand types: int / resource
Unsupported operand types: resource / float
Unsupported operand types: float / resource
Unsupported operand types: resource / string
Unsupported operand types: string / resource
Unsupported operand types: resource / string
Warning: A non-numeric value encountered
Unsupported operand types: string / resource
Unsupported operand types: array % array
Unsupported operand types: array % object
Unsupported operand types: array % resource
Unsupported operand types: object % array
Unsupported operand types: object % object
Unsupported operand types: object % resource
Unsupported operand types: resource % array
Unsupported operand types: resource % object
Unsupported operand types: resource % resource
Unsupported operand types: array % null
Unsupported operand types: null % array
Unsupported operand types: array % bool
Unsupported operand types: bool % array
Unsupported operand types: array % bool
Unsupported operand types: bool % array
Unsupported operand types: array % int
Unsupported operand types: int % array
Unsupported operand types: array % float
Unsupported operand types: float % array
Unsupported operand types: array % string
Unsupported operand types: string % array
Unsupported operand types: array % string
Warning: A non-numeric value encountered
Unsupported operand types: string % array
Unsupported operand types: object % null
Unsupported operand types: null % object
Unsupported operand types: object % bool
Unsupported operand types: bool % object
Unsupported operand types: object % bool
Unsupported operand types: bool % object
Unsupported operand types: object % int
Unsupported operand types: int % object
Unsupported operand types: object % float
Unsupported operand types: float % object
Unsupported operand types: object % string
Unsupported operand types: string % object
Unsupported operand types: object % string
Warning: A non-numeric value encountered
Unsupported operand types: string % object
Unsupported operand types: resource % null
Unsupported operand types: null % resource
Unsupported operand types: resource % bool
Unsupported operand types: bool % resource
Unsupported operand types: resource % bool
Unsupported operand types: bool % resource
Unsupported operand types: resource % int
Unsupported operand types: int % resource
Unsupported operand types: resource % float
Unsupported operand types: float % resource
Unsupported operand types: resource % string
Unsupported operand types: string % resource
Unsupported operand types: resource % string
Warning: A non-numeric value encountered
Unsupported operand types: string % resource
Unsupported operand types: array ** array
Unsupported operand types: array ** object
Unsupported operand types: array ** resource
Unsupported operand types: object ** array
Unsupported operand types: object ** object
Unsupported operand types: object ** resource
Unsupported operand types: resource ** array
Unsupported operand types: resource ** object
Unsupported operand types: resource ** resource
Unsupported operand types: array ** null
Unsupported operand types: null ** array
Unsupported operand types: array ** bool
Unsupported operand types: bool ** array
Unsupported operand types: array ** bool
Unsupported operand types: bool ** array
Unsupported operand types: array ** int
Unsupported operand types: int ** array
Unsupported operand types: array ** float
Unsupported operand types: float ** array
Unsupported operand types: array ** string
Unsupported operand types: string ** array
Unsupported operand types: array ** string
Warning: A non-numeric value encountered
Unsupported operand types: string ** array
Unsupported operand types: object ** null
Unsupported operand types: null ** object
Unsupported operand types: object ** bool
Unsupported operand types: bool ** object
Unsupported operand types: object ** bool
Unsupported operand types: bool ** object
Unsupported operand types: object ** int
Unsupported operand types: int ** object
Unsupported operand types: object ** float
Unsupported operand types: float ** object
Unsupported operand types: object ** string
Unsupported operand types: string ** object
Unsupported operand types: object ** string
Warning: A non-numeric value encountered
Unsupported operand types: string ** object
Unsupported operand types: resource ** null
Unsupported operand types: null ** resource
Unsupported operand types: resource ** bool
Unsupported operand types: bool ** resource
Unsupported operand types: resource ** bool
Unsupported operand types: bool ** resource
Unsupported operand types: resource ** int
Unsupported operand types: int ** resource
Unsupported operand types: resource ** float
Unsupported operand types: float ** resource
Unsupported operand types: resource ** string
Unsupported operand types: string ** resource
Unsupported operand types: resource ** string
Warning: A non-numeric value encountered
Unsupported operand types: string ** resource
Unsupported operand types: array << array
Unsupported operand types: array << object
Unsupported operand types: array << resource
Unsupported operand types: object << array
Unsupported operand types: object << object
Unsupported operand types: object << resource
Unsupported operand types: resource << array
Unsupported operand types: resource << object
Unsupported operand types: resource << resource
Unsupported operand types: array << null
Unsupported operand types: null << array
Unsupported operand types: array << bool
Unsupported operand types: bool << array
Unsupported operand types: array << bool
Unsupported operand types: bool << array
Unsupported operand types: array << int
Unsupported operand types: int << array
Unsupported operand types: array << float
Unsupported operand types: float << array
Unsupported operand types: array << string
Unsupported operand types: string << array
Unsupported operand types: array << string
Warning: A non-numeric value encountered
Unsupported operand types: string << array
Unsupported operand types: object << null
Unsupported operand types: null << object
Unsupported operand types: object << bool
Unsupported operand types: bool << object
Unsupported operand types: object << bool
Unsupported operand types: bool << object
Unsupported operand types: object << int
Unsupported operand types: int << object
Unsupported operand types: object << float
Unsupported operand types: float << object
Unsupported operand types: object << string
Unsupported operand types: string << object
Unsupported operand types: object << string
Warning: A non-numeric value encountered
Unsupported operand types: string << object
Unsupported operand types: resource << null
Unsupported operand types: null << resource
Unsupported operand types: resource << bool
Unsupported operand types: bool << resource
Unsupported operand types: resource << bool
Unsupported operand types: bool << resource
Unsupported operand types: resource << int
Unsupported operand types: int << resource
Unsupported operand types: resource << float
Unsupported operand types: float << resource
Unsupported operand types: resource << string
Unsupported operand types: string << resource
Unsupported operand types: resource << string
Warning: A non-numeric value encountered
Unsupported operand types: string << resource
Unsupported operand types: array >> array
Unsupported operand types: array >> object
Unsupported operand types: array >> resource
Unsupported operand types: object >> array
Unsupported operand types: object >> object
Unsupported operand types: object >> resource
Unsupported operand types: resource >> array
Unsupported operand types: resource >> object
Unsupported operand types: resource >> resource
Unsupported operand types: array >> null
Unsupported operand types: null >> array
Unsupported operand types: array >> bool
Unsupported operand types: bool >> array
Unsupported operand types: array >> bool
Unsupported operand types: bool >> array
Unsupported operand types: array >> int
Unsupported operand types: int >> array
Unsupported operand types: array >> float
Unsupported operand types: float >> array
Unsupported operand types: array >> string
Unsupported operand types: string >> array
Unsupported operand types: array >> string
Warning: A non-numeric value encountered
Unsupported operand types: string >> array
Unsupported operand types: object >> null
Unsupported operand types: null >> object
Unsupported operand types: object >> bool
Unsupported operand types: bool >> object
Unsupported operand types: object >> bool
Unsupported operand types: bool >> object
Unsupported operand types: object >> int
Unsupported operand types: int >> object
Unsupported operand types: object >> float
Unsupported operand types: float >> object
Unsupported operand types: object >> string
Unsupported operand types: string >> object
Unsupported operand types: object >> string
Warning: A non-numeric value encountered
Unsupported operand types: string >> object
Unsupported operand types: resource >> null
Unsupported operand types: null >> resource
Unsupported operand types: resource >> bool
Unsupported operand types: bool >> resource
Unsupported operand types: resource >> bool
Unsupported operand types: bool >> resource
Unsupported operand types: resource >> int
Unsupported operand types: int >> resource
Unsupported operand types: resource >> float
Unsupported operand types: float >> resource
Unsupported operand types: resource >> string
Unsupported operand types: string >> resource
Unsupported operand types: resource >> string
Warning: A non-numeric value encountered
Unsupported operand types: string >> resource
Unsupported operand types: array & array
Unsupported operand types: array & object
Unsupported operand types: array & resource
Unsupported operand types: object & array
Unsupported operand types: object & object
Unsupported operand types: object & resource
Unsupported operand types: resource & array
Unsupported operand types: resource & object
Unsupported operand types: resource & resource
Unsupported operand types: array & null
Unsupported operand types: null & array
Unsupported operand types: array & bool
Unsupported operand types: bool & array
Unsupported operand types: array & bool
Unsupported operand types: bool & array
Unsupported operand types: array & int
Unsupported operand types: int & array
Unsupported operand types: array & float
Unsupported operand types: float & array
Unsupported operand types: array & string
Unsupported operand types: string & array
Unsupported operand types: array & string
Warning: A non-numeric value encountered
Unsupported operand types: string & array
Unsupported operand types: object & null
Unsupported operand types: null & object
Unsupported operand types: object & bool
Unsupported operand types: bool & object
Unsupported operand types: object & bool
Unsupported operand types: bool & object
Unsupported operand types: object & int
Unsupported operand types: int & object
Unsupported operand types: object & float
Unsupported operand types: float & object
Unsupported operand types: object & string
Unsupported operand types: string & object
Unsupported operand types: object & string
Warning: A non-numeric value encountered
Unsupported operand types: string & object
Unsupported operand types: resource & null
Unsupported operand types: null & resource
Unsupported operand types: resource & bool
Unsupported operand types: bool & resource
Unsupported operand types: resource & bool
Unsupported operand types: bool & resource
Unsupported operand types: resource & int
Unsupported operand types: int & resource
Unsupported operand types: resource & float
Unsupported operand types: float & resource
Unsupported operand types: resource & string
Unsupported operand types: string & resource
Unsupported operand types: resource & string
Warning: A non-numeric value encountered
Unsupported operand types: string & resource
Unsupported operand types: array | array
Unsupported operand types: array | object
Unsupported operand types: array | resource
Unsupported operand types: object | array
Unsupported operand types: object | object
Unsupported operand types: object | resource
Unsupported operand types: resource | array
Unsupported operand types: resource | object
Unsupported operand types: resource | resource
Unsupported operand types: array | null
Unsupported operand types: null | array
Unsupported operand types: array | bool
Unsupported operand types: bool | array
Unsupported operand types: array | bool
Unsupported operand types: bool | array
Unsupported operand types: array | int
Unsupported operand types: int | array
Unsupported operand types: array | float
Unsupported operand types: float | array
Unsupported operand types: array | string
Unsupported operand types: string | array
Unsupported operand types: array | string
Warning: A non-numeric value encountered
Unsupported operand types: string | array
Unsupported operand types: object | null
Unsupported operand types: null | object
Unsupported operand types: object | bool
Unsupported operand types: bool | object
Unsupported operand types: object | bool
Unsupported operand types: bool | object
Unsupported operand types: object | int
Unsupported operand types: int | object
Unsupported operand types: object | float
Unsupported operand types: float | object
Unsupported operand types: object | string
Unsupported operand types: string | object
Unsupported operand types: object | string
Warning: A non-numeric value encountered
Unsupported operand types: string | object
Unsupported operand types: resource | null
Unsupported operand types: null | resource
Unsupported operand types: resource | bool
Unsupported operand types: bool | resource
Unsupported operand types: resource | bool
Unsupported operand types: bool | resource
Unsupported operand types: resource | int
Unsupported operand types: int | resource
Unsupported operand types: resource | float
Unsupported operand types: float | resource
Unsupported operand types: resource | string
Unsupported operand types: string | resource
Unsupported operand types: resource | string
Warning: A non-numeric value encountered
Unsupported operand types: string | resource
Unsupported operand types: array ^ array
Unsupported operand types: array ^ object
Unsupported operand types: array ^ resource
Unsupported operand types: object ^ array
Unsupported operand types: object ^ object
Unsupported operand types: object ^ resource
Unsupported operand types: resource ^ array
Unsupported operand types: resource ^ object
Unsupported operand types: resource ^ resource
Unsupported operand types: array ^ null
Unsupported operand types: null ^ array
Unsupported operand types: array ^ bool
Unsupported operand types: bool ^ array
Unsupported operand types: array ^ bool
Unsupported operand types: bool ^ array
Unsupported operand types: array ^ int
Unsupported operand types: int ^ array
Unsupported operand types: array ^ float
Unsupported operand types: float ^ array
Unsupported operand types: array ^ string
Unsupported operand types: string ^ array
Unsupported operand types: array ^ string
Warning: A non-numeric value encountered
Unsupported operand types: string ^ array
Unsupported operand types: object ^ null
Unsupported operand types: null ^ object
Unsupported operand types: object ^ bool
Unsupported operand types: bool ^ object
Unsupported operand types: object ^ bool
Unsupported operand types: bool ^ object
Unsupported operand types: object ^ int
Unsupported operand types: int ^ object
Unsupported operand types: object ^ float
Unsupported operand types: float ^ object
Unsupported operand types: object ^ string
Unsupported operand types: string ^ object
Unsupported operand types: object ^ string
Warning: A non-numeric value encountered
Unsupported operand types: string ^ object
Unsupported operand types: resource ^ null
Unsupported operand types: null ^ resource
Unsupported operand types: resource ^ bool
Unsupported operand types: bool ^ resource
Unsupported operand types: resource ^ bool
Unsupported operand types: bool ^ resource
Unsupported operand types: resource ^ int
Unsupported operand types: int ^ resource
Unsupported operand types: resource ^ float
Unsupported operand types: float ^ resource
Unsupported operand types: resource ^ string
Unsupported operand types: string ^ resource
Unsupported operand types: resource ^ string
Warning: A non-numeric value encountered
Unsupported operand types: string ^ resource
Warning: Array to string conversion
Warning: Array to string conversion
No error for [] .= []
Warning: Array to string conversion
Object of class stdClass could not be converted to string
Warning: Array to string conversion
No error for [] .= STDOUT
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Warning: Array to string conversion
No error for STDOUT .= []
Object of class stdClass could not be converted to string
No error for STDOUT .= STDOUT
Warning: Array to string conversion
No error for [] .= null
Warning: Array to string conversion
No error for null .= []
Warning: Array to string conversion
No error for [] .= true
Warning: Array to string conversion
No error for true .= []
Warning: Array to string conversion
No error for [] .= false
Warning: Array to string conversion
No error for false .= []
Warning: Array to string conversion
No error for [] .= 2
Warning: Array to string conversion
No error for 2 .= []
Warning: Array to string conversion
No error for [] .= 3.5
Warning: Array to string conversion
No error for 3.5 .= []
Warning: Array to string conversion
No error for [] .= "123"
Warning: Array to string conversion
No error for "123" .= []
Warning: Array to string conversion
No error for [] .= "foo"
Warning: Array to string conversion
No error for "foo" .= []
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
Object of class stdClass could not be converted to string
No error for STDOUT .= null
No error for null .= STDOUT
No error for STDOUT .= true
No error for true .= STDOUT
No error for STDOUT .= false
No error for false .= STDOUT
No error for STDOUT .= 2
No error for 2 .= STDOUT
No error for STDOUT .= 3.5
No error for 3.5 .= STDOUT
No error for STDOUT .= "123"
No error for "123" .= STDOUT
No error for STDOUT .= "foo"
No error for "foo" .= STDOUT


UNARY OP:
Cannot perform bitwise not on array
Cannot perform bitwise not on object
Cannot perform bitwise not on resource


INCDEC:
Cannot increment array
Cannot decrement array
Cannot increment object
Cannot decrement object
Cannot increment resource
Cannot decrement resource
