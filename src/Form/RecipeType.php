<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use Symfony\Component\Form\FormEvents;
use App\Form\CategoryAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug', TextType::class, [
                'required' => false,
            ])
            ->add('thumbnailFile', FileType::class, [
                // 'mapped' => false,
                // 'constraints' => [
                //     new Image()
                // ]
            ])
            ->add('category', CategoryAutocompleteField::class)
            // ->add('category', EntityType::class, [
            //     'class' => Category::class,
            //     'choice_label' => 'name',
            //     // 'expanded' => true,
            //     'autocomplete' => true
            // ])
            ->add('content')
            ->add('duration');
            // ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...));
    }

    // public function autoSlug(PreSubmitEvent $event): void
    // {
    //     $data = $event->getData();
    //     if (empty($data["slug"])) {
    //         $slugger = new AsciiSlugger();
    //         $data['slug'] = strtolower($slugger->slug($data['title']));
    //     }
    //     $event->setData($data);
    // }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
