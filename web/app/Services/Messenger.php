<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Str;
use ReflectionClass;

class Messenger
{
    /**
     * @param $gateway
     *
     * @return object
     * @throws \ReflectionException
     */
    public static function getInstance($gateway): object
    {

        $class = '\App\Services\Messengers\\' . Str::ucfirst($gateway) . 'Manager';
        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Payment gateway [$class] is not instantiable.");
        }

        /*  if($reflector->getProperty('gateway') === null){
              throw new \Exception("Can't init gateway [$gateway].");
          }*/

        return $reflector->newInstance();
    }
}
