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

#use Sonata\CoreBundle\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType,
    Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    HiddenType, UrlType, FileType, ChoiceType, EmailType, TextareaType, FormType, CollectionType
};

use Sonata\CoreBundle\Form\Type\CollectionType as SonataCollectionType;

use App\Entity\{
    Product, Category
};

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class LeatherAdmin
 * @package App\Admin
 */
class ProductAdmin extends AbstractAdmin
{

    protected static $colors = ['white', 'black', 'red', 'blue', 'green', 'yellow', 'pink', 'purple', 'brown', 'orange', 'grey'];

    /**
     * Form configure
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var Product $product */
        $product = $this->getSubject();


        $formMapper
            ->add('category', EntityType::class, [
                'placeholder'   => 'Select category...',
                'required'      => true,
                'class'         => Category::class,
                'choice_label'  => 'nameWithSubCat',
                'query_builder' => function (CategoryRepository $category) {
                    return $category->fetchChildren();
                },
            ])
            ->add('productNumber')
            ->add('productName', null, [
                'required'  => false
            ])
            ->add('titleDe')
            ->add('titleEn')
            ->add('descriptionDe')
            ->add('descriptionEn')

            ->add('colors', ChoiceType::class, [
                'choices' => array_combine(self::$colors, self::$colors),
                'expanded'  => true,
                'multiple'  => true
            ])


            ->add('price')
            ->add('topItem')




            ->add('images', HiddenType::class, [
                'attr' => [
                    'class' => 'images-names-container',
                    'data-pk' => $product->getId() ?: 0,
                ]
            ])

            //Images uploaded but not persisted - delete on unload
            ->add('imagesTmp', HiddenType::class, [
                'mapped'        => false,
                'attr' => [
                    'class' => 'images-tmp-names-container',
                ]
            ])

            ->add('imagesList', FileType::class, [
                //'entry_type'    => ImagesType::class,
                //'allow_add'     => true,
                //'allow_delete'  => true,
                'label' => 'Upload new images',
                'required'      => $product->getId() === null,
                'mapped'        => false,
                'attr'  => [
                    'class' => 'js_upload-image',
                    'accept'    => 'image/*'
                    //'style' => 'display: none',
                ],
                'label_attr' => [
                    //'class' => 'btn btn-default'
                ],
                //'help'          => $product->getId() ? '<img src="/images/products/">' : '',
            ])
        ;

    }


    /**
     * @param Product $product
     * @return string
     */

    /*
    private function getImages(Product $product)
    {
        $ret = '';
        foreach ($product->getImages() as $image) {
            $ret .= $image->getImage() . '-';
        }
        return trim($ret, '-');
    }
    */



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
            ->add('titleDe')
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
            ->add('category.nameWithSubCat', null, [
                'label' => 'Category'
            ])
            ->add('titleDe')
            ->add('titleen')
            //->add('title')
            //->add('description')
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

            ->add('titleDe')
            ->add('descriptionDe', null, [
                'safe' => true
            ])
        ;
    }


}