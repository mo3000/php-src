--TEST--
Request #71855 (PDO placeholder escaping, part 2)
--SKIPIF--
<?php
if (!extension_loaded('pdo') || !extension_loaded('pdo_pgsql')) die('skip not loaded');
if (!extension_loaded('json')) die('skip json extension not available');
require_once dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
require_once dirname(__FILE__) . '/config.inc';
PDOTest::skip();

$db = PDOTest::factory();
if (version_compare($db->getAttribute(PDO::ATTR_SERVER_VERSION), '9.4.0') < 0) {
        die("skip Requires 9.4+");
}

?>
--FILE--
<?php
require_once dirname(__FILE__) . '/../../../ext/pdo/tests/pdo_test.inc';
require_once dirname(__FILE__) . '/config.inc';
$db = PDOTest::test_factory(dirname(__FILE__) . '/common.phpt');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_NUM);

$jsonb = $db->quote(json_encode(['a' => 1]));

foreach ([false, true] as $emulate) {
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, $emulate);

    $stmt = $db->prepare("SELECT {$jsonb}::jsonb ?? ?");
    $stmt->execute(['b']);
    var_dump($stmt->fetch());

    $stmt = $db->prepare("SELECT {$jsonb}::jsonb ???");
    $stmt->execute(['a']);
    var_dump($stmt->fetch());
}

?>
==OK==
--EXPECT--
array(1) {
  [0]=>
  bool(false)
}
array(1) {
  [0]=>
  bool(true)
}
array(1) {
  [0]=>
  bool(false)
}
array(1) {
  [0]=>
  bool(true)
}
==OK==
