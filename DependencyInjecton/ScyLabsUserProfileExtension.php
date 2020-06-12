<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 18/11/2019
 * Time: 11:43
 */

namespace ScyLabs\UserBundle\DependencyInjecton;



use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class ScyLabsUserProfileExtension extends Extension
{

    public function load(array $configs,ContainerBuilder $container){



        $bundleRoot = new FileLocator(dirname(__DIR__));

        $loader = new YamlFileLoader($container,$bundleRoot);

        $loader->load('Resources/config/services.yaml');


    }
}