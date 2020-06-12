<?php

namespace ScyLabs\UserBundle\Controller;

use ScyLabs\NeptuneBundle\Model\NeptuneFrontVarsInterface;
use ScyLabs\NeptuneBundle\Services\NeptuneFrontVars;
use ScyLabs\UserBundle\Entity\User;
use ScyLabs\UserBundle\Form\RegistrationFormType;
use ScyLabs\UserBundle\Form\RegistrationType;
use ScyLabs\UserBundle\Security\EmailVerifier;
use ScyLabs\UserBundle\Security\UserAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController {
    

    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    
    /**
     * @Route("/{_locale}/register",name="register")
     */
    public function registerAction(Request $request,NeptuneFrontVarsInterface $netuneFrontVars,RequestStack $requestStack,UserPasswordEncoderInterface $passwordEncoder,GuardAuthenticatorHandler $guardHandler,UserAuthenticator $authenticator)
    {
       
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user,['action'=>$this->generateUrl('register')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@moi.com', 'Yolo'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('@ScyLabsUser/registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('registered');
        }

        $isSubRequest = $requestStack->getParentRequest() !== null;

        $params = [
            'form' => $form->createView(),
            'isSubRequest'   =>  $isSubRequest
        ];
        
        $params['loginForm'] = ($isSubRequest) ? null : $this->forward(SecurityController::class.'::login',[
            '_locale'   =>  $request->getLocale()
        ]);
    
    

        return $this->render('@ScyLabsUser/registration/register.html.twig', array_merge($params,$netuneFrontVars->getVars($request)));
    }

    /**
     * @Route("/registred",name="registered")
     */
    public function registred(NeptuneFrontVarsInterface $neptuneFrontVars,Request $request){
        return $this->render('@ScyLabsUser/registration/registered.html.twig',$neptuneFrontVars->getVars($request));
    }
    /**
     * @Route("/verify/email", name="verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
       
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('register');
    }
}