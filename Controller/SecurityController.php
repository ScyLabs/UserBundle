<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 14/06/2018
 * Time: 17:11
 */

namespace ScyLabs\UserBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;

use ScyLabs\GiftCodeBundle\Entity\Address;
use ScyLabs\NeptuneBundle\Services\ClassFounder;
use ScyLabs\UserBundle\Form\EditUserType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


use ScyLabs\NeptuneBundle\Model\NeptuneFrontVarsInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Secure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenManager;

    public function __construct(EventDispatcherInterface $eventDispatcher,CsrfTokenManager $tokenManager = null)
    {
        $this->tokenManager = $tokenManager;
        $this->eventDispatcher = $eventDispatcher;

    }


    /**
     * @Route("/{_locale}/login",name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils,Request $request,NeptuneFrontVarsInterface $neptuneFrontVarsFounder,RequestStack $requestStack)
    {

         if ($this->getUser()) {
             return $this->redirectToRoute('profile',['_locale'=>$request->getLocale()]);
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        $isSubRequest = $requestStack->getParentRequest() !== null;
        
        $params = [
            'last_username'     => $lastUsername, 
            'error'             => $error,
            'isSubRequest'      => $isSubRequest,
            'registrationForm'  => ($isSubRequest) ? null :  $this->forward(RegistrationController::class.'::registerAction',[
                '_locale'   =>  $request->getLocale()
            ]),

        ];
        
        
        return $this->render('@ScyLabsUser/login.html.twig',array_merge($params,$neptuneFrontVarsFounder->getVars($request)));
        
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    /**
     * @Route("/{_locale}/profile/edit",name="fos_user_profile_edit")
     * @Secure("is_granted('ROLE_USER')")
     */
    public function edit(Request $request,NeptuneFrontVarsInterface $neptuneFrontVarsFounder){

        $form = $this->createForm(EditUserType::class,$this->getUser());

        return $this->render('@ScyLabsUser/profile/edit.html.twig',array_merge([
            'form'  =>  $form->createView()
        ],$neptuneFrontVarsFounder->getVars($request)));



    }

}