<?php

namespace App\Form;



use DateTimeImmutable;
use App\Entity\CustomerAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CustomerAddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class, ['label' => 'Miasto'])
            ->add('zipCode', TextType::class, ['label' => 'Kod pocztowy'])
            ->add('street', TextType::class, ['label' => 'Ulica'])
            ->add(
                'validFrom',
                DateType::class,
                ['label' => 'Obowiązuje od', 'input' => 'datetime_immutable', 'widget' => 'single_text', 'data' => new DateTimeImmutable()]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerAddress::class,

        ]);
    }
}
