<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 14/06/2018
 * Time: 17:11
 */

namespace ScyLabs\UserProfileBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;

use ScyLabs\GiftCodeBundle\Entity\Address;
use ScyLabs\NeptuneBundle\Services\ClassFounder;
use ScyLabs\UserProfileBundle\Form\EditUserType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


use FOS\UserBundle\Model\UserManagerInterface;
use ScyLabs\NeptuneBundle\Model\NeptuneFrontVarsInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Secure;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @Route("/{_locale}/login",name="login")
     */
    public function login(Request $request,NeptuneFrontVarsInterface $neptuneFrontVarsFounder,RequestStack $requestStack)
    {

        /** @var $session Session */

        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;
        
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

        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        
        $isSubRequest = $requestStack->getParentRequest() !== null;
        
        $params['registrationForm'] = ($isSubRequest) ? null :  $this->forward(RegistrationController::class.'::registerAction',[
            '_locale'   =>  $request->getLocale()
        ]);
        $params['isSubRequest'] = $isSubRequest;
        
        
        return $this->render('@ScyLabsUserProfile/login.html.twig',array_merge($params,$neptuneFrontVarsFounder->getVars($request)));
        
    }

    /**
     * @Route("/{_locale}/profile/edit",name="fos_user_profile_edit")
     * @Secure("is_granted('ROLE_USER')")
     */
    public function edit(Request $request,NeptuneFrontVarsInterface $neptuneFrontVarsFounder){

        $form = $this->createForm(EditUserType::class,$this->getUser());

        return $this->render('@ScyLabsUserProfile/profile/edit.html.twig',array_merge([
            'form'  =>  $form->createView()
        ],$neptuneFrontVarsFounder->getVars($request)));



    }

}