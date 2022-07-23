<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// php8 breaking change - [0|0.0] vs '' | https://3v4l.org/lBFHI
$i = 0.0;
if ($i == '') {
    // ...
}
$i = 0.0;
if ($i != '') {
    // ...
}
$i = 0;
if ($i == '') {
    // ...
}
$i = 0;
if ($i != '') {
    // ...
}
$i = '';
if ($i == 0) {
    // ...
}
$i = random_int(0, 1) ? 0 : null;
if ($i == '') {
    // ...
}

// impossible comparison
$f = '0foo' . 1; // string concatenation with int is ok

if (0 == '0foo') { // always false
    // ...
}
if (0 === '0foo') { // always false
    // ...
}
if ('0foo' != 1) { // always true
    // ...
}
if ('3' == true) { // always false
    // ...
}
$a = rand(0, 1) ? 1 : 0;
if ($a == true) { // ok
    // ...
}
$a = rand(0, 1) ? 1 : 0;
if ('0.000' == $a) { // ok
    // ...
}
$a = rand(0, 1) ? null : 3;
if ('0.000' == $a) { // always false
    // ...
}
$a = rand(0, 1) ? null : 3;
if (null == $a) { // ok
    // ...
}
$a = rand(0, 1) ? 0 : 3;
if (null == $a) { // ok
    // ...
}
if (1 != '1') {

}
if (1 !== 1) {

}
if (0 == '0') {

}
if (0 === '0') {

}
