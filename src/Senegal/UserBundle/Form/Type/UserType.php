<?php
namespace Senegal\UserBundle\Form\Type;

use Senegal\UserBundle\Form\Transformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class UserType extends AbstractType
{
    /**
     * @var string
     */
    protected $uploadFileDir;

    /**
     * @param string $uploadFileDir
     */
    public function __construct($uploadFileDir)
    {
        $this->uploadFileDir = $uploadFileDir;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->setAction($this->router->generate('profideo_innovation_search'));

        // id 	username 	firstname 	lastname 	email 	salt 	password 	active 
        $transformer = new DateTimeToStringTransformer(null, null, 'd/m/Y');

//        $releasedAt = $builder->create('releasedAt', 'text', array(
//                'label'    => 'Date d\'édition (jj/mm/yyyy)',
//                'attr'     => ['pattern' => '[0-9]{2}/[0-9]{2}/[0-9]{4}', 'class' => 'date filter-date'],
//                'required' => false
//            )
//        )->addModelTransformer($transformer);

//        $fileOptions = array(
//            'label'       => 'Fichier (PDF uniquement)',
//            'required'    => false,
//            'attr'        => array('class'  => 'col-12 filter-file form-control jquery-file-upload')
//        );
//
//        
//            ->add('type', 'sdk_model',
//                array(
//                    'label'         => 'Nature',
//                    'class'         => 'Pfd\Sdk\Model\DocumentType',
//                    'empty_value'   => 'Veuillez choisir une nature',
//                    'required'      => true,
//                    'order_by'      => 'name',
//                    'attr'        => array('class' => 'col-12 filter-type form-control')
//                )
//            )
//            ->add('file', 'file', $fileOptions)
        $builder
            ->add('username', 'text',
                array(
                    'label'    => "Nom d'utilisateur",
                    "required" => true,
                    'attr'     => array('class' => 'col-12 filter-username form-control')
                )
            )
            ->add('firstname', 'text',
                array(
                    'label'    => "Nom",
                    "required" => true,
                    'attr'     => array('class' => 'col-12 filter-firstname form-control')
                )
            )
            ->add('lastname', 'text',
                array(
                    'label'    => "Prénom",
                    "required" => true,
                    'attr'     => array('class' => 'col-12 filter-lastname form-control')
                )
            )
            ->add('email', 'email',
                array(
                    'label'    => "Email",
                    "required" => true,
                    'attr'     => array('class' => 'col-12 filter-firstname form-control')
                )
            )
            ->add('password', 'password',
                array(
                    'label'         => "Mot de passe",
                    "required"      => true,
                    'always_empty'  => false,
                    'attr'          => array('class' => 'col-12 filter-password form-control')
                )
            )
            ->add('active', 'checkbox',
                array(
                    'label'    => "Activer",
                    "required" => true,
                    'attr'     => array('class' => 'col-12 filter-active form-control')
                )
            )
            ->add('roles', 'entity', 
                array(
                     'label'    => 'Roles',
                     'class'    => 'Api\Sdk\Model\Role',
                     'property' => 'name',
                     'multiple' => true,
                     //'order_by' => 'name',
                     //'choices'  => $user->getRoles(),
                     //'data'     => $options['data']->getRoles(),
                     'attr'     => array('class' => 'col-12 filter-type form-control')
                    )
            )
            ->add('id', 'hidden')
            ->add('submit', 'submit', 
                array(
                    'label' => 'Enregistrer',
                    'attr' => array('class' => 'btn btn-primary', 'id' => '_submit')
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Api\Sdk\Model\User',
            'csrf_protection' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
