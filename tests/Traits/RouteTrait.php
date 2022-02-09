<?php
namespace App\Tests\Traits;

trait RouteTrait {

    protected function getRoute(string $route, $client){

        return $client->getContainer()->get('router')->generate($route, array(), false);
    }
}