<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
date_default_timezone_set('Europe/Kiev');
// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read https://symfony.com/doc/current/setup.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
/**
 * Check if REMOTE_ADDR within allowed IP-ranges
 * @return bool
 */
function ipInRange()
{
    $allowedIP = array(  // IPv4 only
        array(
            'min' => '172.18.0.1',
            'max' => '172.18.0.254',
        ),
        array(
            'min' => '172.21.0.1',
            'max' => '172.21.0.254',
        ),
        array(
            'min' => '172.19.0.1',
            'max' => '172.19.0.254',
        ),
        [
            'min'=> '172.22.0.1',
            'max'=> '172.22.0.254',
        ],
        array(
            'min' => '172.30.0.1',
            'max' => '172.30.255.254',
        ),
        array(
            'min' => '192.168.100.1',
            'max' => '192.168.100.254',
        ),
        array(
            'min' => '192.168.56.1',
            'max' => '192.168.56.254',
        ),
        array(
            'min' => '109.68.46.238',
            'max' => '109.68.46.238',
        ),
    );

    $myIP = ip2long(@$_SERVER['REMOTE_ADDR']);

    $filtered = array_filter($allowedIP, function ($range) use ($myIP) {
        return (ip2long($range['min']) <= $myIP && $myIP <= ip2long($range['max']));
    });

    return !empty($filtered);
}

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '93.74.147.1', '192.168.10.1', '::1'], true) || ipInRange() || PHP_SAPI === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require __DIR__.'/../vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
