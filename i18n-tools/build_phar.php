<?php
// Build es.phar for osTicket from a compiled messages.mo, using osTicket's own
// Translation::buildHashFile() so the runtime format matches exactly.

define('INCLUDE_DIR', '/app/include/');
require_once INCLUDE_DIR . 'class.translation.php';

$mo = Translation::buildHashFile('/tmp/messages.mo', false, true);

@unlink('/tmp/es.phar');
$phar = new Phar('/tmp/es.phar');
$phar->startBuffering();

$phar->addFromString('LC_MESSAGES/messages.mo.php', $mo);

$info = array(
    'Build-Date' => date(DATE_RFC822),
    'Phrases-Version' => '1.18',
    'Build-Version' => 'devlov-1',
    'Build-Major-Version' => 1,
    'Language' => 'Spanish',
    'Id' => 'lang:es',
    'Last-Revision' => date(DATE_RFC822),
    'Version' => (int) (time() / 10000),
);
$phar->addFromString('MANIFEST.php', sprintf('<?php return %s;', var_export($info, true)));

$phar->setStub('<?php __HALT_COMPILER();');
$phar->setSignatureAlgorithm(Phar::SHA1);
$phar->stopBuffering();

echo "Built /tmp/es.phar\n";
