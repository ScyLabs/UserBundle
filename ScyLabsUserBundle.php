<?php


namespace ScyLabs\UserBundle;


use ScyLabs\UserBundle\DependencyInjecton\ScyLabsUserProfileExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ScyLabsUserBundle extends Bundle
{

    public function getContainerExtension()
    {
        return new ScyLabsUserProfileExtension();

    }

    public function build(ContainerBuilder $container){
        parent::build($container);
    }
}