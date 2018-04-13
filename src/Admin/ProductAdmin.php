<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 13.04.18
 * Time: 11:49
 */

namespace App\Admin;


use App\Admin\AbstractAdmin as AbstractAdmin;

use App\Repository\CategoryRepository;
use Sonata\AdminBundle\Datagrid\{ListMapper, DatagridMapper};
use Sonata\AdminBundle\Form\FormMapper,
    Sonata\AdminBundle\Show\ShowMapper,
    Sonata\CoreBundle\Form\Type\DateRangeType,
    Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter,
    Sonata\DoctrineORMAdminBundle\Filter\DateFilter,
    Sonata\CoreBundle\Form\Type\DatePickerType,
    Sonata\CoreBundle\Validator\ErrorElement;

use Doctrine\ORM\EntityManager;

use Symfony\Bridge\Doctrine\Form\Type\EntityType,
    Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    UrlType, FileType, ChoiceType, EmailType, TextareaType, FormType
};

use App\Entity\{
    Product, Category
};

/**
 * Class LeatherAdmin
 * @package App\Admin
 */
class ProductAdmin extends AbstractAdmin
{
    /**
     * Form configure
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category', EntityType::class, [
                'placeholder'   => 'Select a category...',
                'required'      => true,
                'class'         => Category::class,
                'choice_label'  => 'nameWithSubCat',
                'query_builder' => function (CategoryRepository $category) {
                    return $category->fetchChildren();
                },
            ])
            ->add('productName')
            ->add('productNumber')
            ->add('title')
            ->add('description')
            ->add('price')
        ;
    }

    /**
     * @param ErrorElement $errorElement
     * @param $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
    }

    /**
     * Filters
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
        ;
    }

    /**
     * View list
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            //->addIdentifier('id')

            ->add('createdAt', null, [
                'format' => parent::GLOBAL_DATE_FORMAT,
                'label' => 'Created'
            ])
            ->add('category.nameWithSubCat')
            ->add('title')
            ->add('description')
            ->add('price')

            ->add('_action', null, [
                'actions' => [
                    'show'      => [],
                    'edit'      => [],
                    'delete'    => [],
                ]
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('createdAt', null, [
                'format' => parent::GLOBAL_DATETIME_FORMAT,
            ])

            ->add('title')
            ->add('description', null, [
                'safe' => true
            ])
        ;
    }


}