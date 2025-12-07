<?php
require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

// Create a new capsule instance
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'forensic',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();

// Count records in each table
$armDealers = Capsule::table('armorers')->count();
$weaponTypes = Capsule::table('weapon_types')->count();
$bores = Capsule::table('bores')->count();
$makes = Capsule::table('makes')->count();
$licenseIssuers = Capsule::table('license_issuers')->count();

echo "Arm Dealers: " . $armDealers . "\n";
echo "Weapon Types: " . $weaponTypes . "\n";
echo "Bores: " . $bores . "\n";
echo "Makes: " . $makes . "\n";
echo "License Issuers: " . $licenseIssuers . "\n";
?>