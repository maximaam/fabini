<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 19.02.18
 * Time: 18:37
 */

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichFileType::class, array(
                //'required'      => true,
                //'multiple'  => true,
                //'mapped'       => 'product',
                'allow_delete'  => true,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Image::class]);
    }

    public function getName()
    {
        return 'product_images';
    }
}