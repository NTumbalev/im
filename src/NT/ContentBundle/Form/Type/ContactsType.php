<?php
namespace NT\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class ContactsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('name', 'text', array(
                'label' => 'contact.name',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry',
                    'placeholder' => 'contact.name'
                )
            ))
            ->add('subject', 'text', array(
                'label' => 'contact.subject',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry',
                    'placeholder' => 'contact.topic'
                )
            ))
            // ->add('phone', 'text', array(
            //     'label' => 'contact.phone',
            //     'translation_domain' => 'NTFrontendBundle',
            //     'required' => true,
            //     'constraints' => array(
            //         new Regex(array(
            //             'pattern' => '/^(0|\+)[0-9]+$/',
            //             'match' => true,
            //             'message' => 'only_numbers',
            //         )),
            //         new NotBlank(array('message' => 'required_field')),
            //     ),
            //     'attr' => array(
            //         'class' => 'validation-phone form-control',
            //         'placeholder' => 'contact.phone'
            //     ),
            //     'label_attr' => array(
            //         'class' => 'col-md-3 control-label'
            //     )
            // ))
            ->add('email', 'text', array(
                'label' => 'contact.email',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => array(
                    new NotBlank(array('message' => 'required_field')),
                    new Email()
                ),
                'attr' => array(
                    'class' => 'required-entry validation-email',
                    'placeholder' => 'contact.email'
                )
            ))
            ->add('message', 'textarea', array(
                'label' => 'contact.message',
                'translation_domain' => 'NTFrontendBundle',
                'required' => true,
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'attr' => array(
                    'class' => 'required-entry',
                    'placeholder' => 'contact.message',
                    'cols' => 6,
                    'rows' => 5
                )
            ))
            // ->add('captcha', 'ds_re_captcha', array('mapped' => false))
            ;
    }

    public function getName()
    {
        return 'contacts';
    }
}
