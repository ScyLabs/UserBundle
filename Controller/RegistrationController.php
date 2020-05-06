<?php

namespace ScyLabs\UserProfileBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use ScyLabs\NeptuneBundle\Model\NeptuneFrontVarsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController {
    

    private $formFactory;
    public function __construct(FactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }
    
    /**
     * @Route("/{_locale}/register",name="register")
     */
    public function registerAction(Request $request,UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher,NeptuneFrontVarsInterface $netuneFrontVars,RequestStack $requestStack)
    {
        
        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm([
            'action'    =>  $this->generateUrl('fos_user_registration_register_lang',[
                '_locale'    =>  $request->getLocale()
            ])
        ]);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                
                return $response;
            }

            $event = new FormEvent($form, $request);
            $eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }
        $isSubRequest = $requestStack->getParentRequest() !== null;

        $params = [
            'form' => $form->createView(),
            'isSubRequest'   =>  $isSubRequest
        ];
        
        $params['loginForm'] = ($isSubRequest) ? null : $this->forward(SecurityController::class.'::login',[
            '_locale'   =>  $request->getLocale()
        ]);
    
        

        return $this->render('@ScyLabsUserProfile/register.html.twig', array_merge($params,$netuneFrontVars->getVars($request)));
    }
}