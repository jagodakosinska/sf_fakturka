<?php

namespace App\Form;

use DateTimeImmutable;
use App\Entity\Customer;
use App\Entity\CustomerAddress;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CustomerFormType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nazwa', 'required' => true])
            ->add('description', TextType::class, ['label' => 'Dodatkowy opis'])
            ->add('nip', TextType::class, ['label' => 'NIP'])
            ->add('submit', SubmitType::class, ['label' => 'Zapisz']);


        if ($options['unset_empty_form']) {
            $field_name = $options['unset_empty_form'];
            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($field_name) {

                $data = $event->getData();
                $form = $event->getForm();

                $today = new DateTimeImmutable();
                $today = $today->format('Y-m-d');

                if (
                    isset($data[$field_name]) &&
                    $data[$field_name]['street'] == '' &&
                    $data[$field_name]['zipCode'] == '' &&
                    $data[$field_name]['city'] == '' &&
                    $data[$field_name]['validFrom'] ==  $today
                ) {

                    $form->remove($field_name);
                    unset($data[$field_name]);
                    // dd($data);
                    $event->setData($data);
                }
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
            'unset_empty_form' => '',

        ]);
    }
}
