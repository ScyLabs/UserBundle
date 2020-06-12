<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/02/2020
 * Time: 14:25
 */

namespace ScyLabs\UserBundle\Form;


use FOS\UserBundle\Form\Type\UsernameFormType;
use ScyLabs\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options){
        $builder
            ->add('name',TextType::class,[
                'label' =>  'form.name'
            ])
            ->add('firstname',TextType::class,[
                'label' =>  'form.firstname'
            ])
            ->add('actualPassword',PasswordType::class,[
                'label' =>  'form.password_if',
                'mapped'    =>  false,
                'required'  =>  true
            ])
            ->add('plainPassword',RepeatedType::class,[
                'type'  =>  PasswordType::class,
                'required'  =>  false,
                'first_options' =>  [
                    'label' =>  'form.new_password'
                ],
                'second_options'    =>  [
                    'label' =>  'form.new_password_confirmation'
                ]

            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => null,
            'data_class' => User::class,
            'translation_domain'    =>  'ScyLabsUserBundle',
        ]);
    }
}