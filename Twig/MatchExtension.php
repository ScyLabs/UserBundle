<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19/11/2019
 * Time: 15:19
 */

namespace ScyLabs\UserBundle\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MatchExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('match',array($this,'match')),

        );
    }

    public function match($str,string $pattern){
        if(null === $str)
            return false;

        return preg_match($pattern,$str);
    }
}