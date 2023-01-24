<?php

namespace App\Form;

use App\Service\LocaleProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserParamsType extends AbstractType
{
    public function __construct(
        private readonly LocaleProvider $localeProvider
    ) {
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('user_name')
            ->add(
                'locale',
                ChoiceType::class,
                ['choices' => $this->localeProvider->getSelectValues()]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
