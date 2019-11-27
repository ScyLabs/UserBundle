<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19/11/2019
 * Time: 11:02
 */

namespace ScyLabs\UserProfileBundle\Model;


use Symfony\Component\HttpFoundation\Request;

interface NeptuneFrontVarsFounderInterface
{
    public function getVars(Request $request) : array;
}