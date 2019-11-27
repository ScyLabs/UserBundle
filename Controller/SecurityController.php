<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 14/06/2018
 * Time: 17:11
 */

namespace ScyLabs\UserProfileBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;

use ScyLabs\NeptuneBundle\Services\ClassFounder;
use ScyLabs\UserProfileBundle\Model\NeptuneFrontVarsFounderInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class SecurityController extends \FOS\UserBundle\Controller\SecurityController
{

    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, UserManagerInterface $userManager,CsrfTokenManager $tokenManager = null)
    {
        parent::__construct($tokenManager);
        $this->tokenManager = $tokenManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;

    }


    /**
     * @Route("/login",name="fos_user_security_login")
     */
    public function login(Request $request,NeptuneFrontVarsFounderInterface $neptuneFrontVarsFounder)
    {

        /** @var $session Session */

        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        $target = $request->getSession()->get('_security.main.target_path');
        if(null !== $target && !($request->attributes->has($authErrorKey) || (null !== $session && $session->has($authErrorKey)))){
            $targetDate = $request->getSession()->get('_security.main.target_date');
            if(null !== $targetDate && preg_match('/\/admin/Ui',$target)){
                $now = new \DateTime('now');
                $limit = $targetDate->add(new \DateInterval('PT2S'));
                if($now > $limit){
                    $request->getSession()->remove('_security.main.target_date');
                    $request->getSession()->remove('_security.main.target_path');
                    $target = null;
                }
            }
            if(preg_match('/\/admin/Ui',$target)){
                $request->getSession()->set('_security.main.target_date',new \DateTime());
            }
        }

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        $em = $this->getDoctrine()->getManager();



        $params = [
            'admin' =>   $request->getSession()->get('_security.main.target_admin'),
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,

        ];

        return $this->renderLogin(array_merge($params,$neptuneFrontVarsFounder->getVars($request)));
    }

}