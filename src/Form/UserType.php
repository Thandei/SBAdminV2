<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{

    public function __construct(private  TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ["label" => $this->translator->trans("app.user"), "attr" => ["placeholder" => "DENEME", "class" => "bg-primary"]])
            ->add('password')
            ->add('roles', ChoiceType::class, [
                "label" => "ROLE TİPİ",
                "choices" => [
                    "ROLE_ADMIN" => "DENEME",
                    "ROLE_USER" => "DENEME",
                ],
                "attr" => [
                    "class" => "select2"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
