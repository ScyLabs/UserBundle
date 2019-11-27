<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 15/11/2019
 * Time: 14:07
 */

namespace ScyLabs\UserProfileBundle;


use ScyLabs\UserProfileBundle\DependencyInjecton\ScyLabsUserProfileExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ScyLabsUserProfileBundle extends Bundle
{

    public function getContainerExtension()
    {
        return new ScyLabsUserProfileExtension();

    }

    public function build(ContainerBuilder $container){
        parent::build($container);
    }
}