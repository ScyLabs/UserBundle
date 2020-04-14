<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19/11/2019
 * Time: 12:00
 */

namespace ScyLabs\UserProfileBundle\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Controller\ProfileController;
use FOS\UserBundle\Controller\RegistrationController;
use FOS\UserBundle\Controller\ResettingController;
use FOS\UserBundle\Controller\SecurityController;
use ScyLabs\GiftCodeBundle\Hook\ProfileBoxesHook;
use ScyLabs\NeptuneBundle\Manager\HookManager;
use ScyLabs\NeptuneBundle\Model\NeptuneFrontVarsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class ControllerListener
{
    /**
     * @var \Twig\Environment
     */
    private $twig;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;
    /**
     * @var \Symfony\Component\Security\Core\Security
     */
    private $security;

    private $neptuneFrontVarsFounder;

    private $container;

    const CONTROLLERS = [RegistrationController::class,SecurityController::class,ProfileController::class,ResettingController::class];
    public function __construct( Environment $twig, EntityManagerInterface $manager, Security $security ,NeptuneFrontVarsInterface $neptuneFrontVarsFounder,ContainerInterface $container) {
        $this->twig     = $twig;
        $this->manager  = $manager;
        $this->security = $security;
        $this->neptuneFrontVarsFounder = $neptuneFrontVarsFounder;
        $this->container = $container;


    }
    public function onKernelController( ControllerEvent $event ): void {
        $controller = (is_array($event->getController())) ? $event->getController()[0] : $event->getController();


        if(in_array(get_class($controller),self::CONTROLLERS)){
            $vars = $this->neptuneFrontVarsFounder->getVars($event->getRequest());
            foreach ($vars as $key => $var){
                $this->twig->addGlobal($key,$var);
            }
        }
    }

}