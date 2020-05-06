<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/11/2019
 * Time: 16:26
 */

namespace ScyLabs\UserProfileBundle\Controller;


use FOS\UserBundle\Model\UserInterface;
use ScyLabs\NeptuneBundle\Manager\HookManager;
use ScyLabs\NeptuneBundle\Model\NeptuneFrontVarsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class ProfileController extends AbstractController
{

    /**
     * @Route("/{_locale}/profile",name="fos_user_profile_show",defaults={"_locale":"fr"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function showAction(Request $request,NeptuneFrontVarsInterface $neptuneFrontVarsFounder)
    {
        $user = $this->getUser();

        return $this->render('@ScyLabsUserProfile/profile/show.html.twig', array_merge([
            'user' => $user,
            ], $neptuneFrontVarsFounder->getVars($request)
        ));
    }

}