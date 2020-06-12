<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/11/2019
 * Time: 11:28
 */

namespace ScyLabs\UserBundle\Form;


use FOS\UserBundle\Form\Type\RegistrationFormType;
use ScyLabs\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationType extends AbstractType
{

    private $translator;
    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder,array $options){

        $builder->setAction($options['action']);
        $builder
            ->add('name',TextType::class,[
                'translation_domain'    => 'ScyLabsUserBundle',
                'label' =>  'form.name'
            ])
            ->add('firstname',TextType::class,[
                'translation_domain'    => 'ScyLabsUserBundle',
                'label' =>  'form.firstname'
            ])
            ->add('email', RepeatedType::class, array(
                'translation_domain'    => 'ScyLabsUserBundle',
                'first_options' => array('label' => 'form.email'),
                'second_options' => array('label' => 'form.email_confirmation'),
                'constraints'          =>  [
                    new Email()
                ]
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'translation_domain'    => 'ScyLabsUserBundle',
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'constraints'   =>  [
                    new Regex([
                        'pattern'   =>  "/^.*(?=.{8,})((?=.*[!@#$%^&*()\-_=+{};:,<.>]){1})(?=.*\d)((?=.*[a-z]){1})((?=.*[A-Z]){1}).*$/",
                        'message'   =>  'password_false'
                    ])
                ],
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('rgpd', CheckboxType::class, array(
                'label' => "J'accepte que mes données soient enregistrées. *",
                "mapped" => false,
                'constraints' => array(
                    new NotBlank(),
                )
            ))

        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => null,
            'data_class' => User::class,
            'default_trans_domain'  =>  'ScyLabsUserBundle'
        ])
        ;
    }

}