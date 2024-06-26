<?php

namespace App\Form\GuildEvent;

use App\Entity\GuildEvent;
use App\Enum\InstanceTypeEnum;
use App\Enum\RolesEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class GuildEventType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEventNew = $options['data']->getId() === null;
        $isUserAdmin = $this->security->isGranted(RolesEnum::ADMIN->value);

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('start', null, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'attr' => [
                    'data-controller' => 'datetime-picker',
                    'class' => 'form-control'
                ]
            ])
            ->add('end', null, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'attr' => [
                    'data-controller' => 'datetime-picker',
                    'class' => 'form-control'
                ]
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur',
            ])
            ->add('oldMembersAllowed', ChoiceType::class, [
                'label' => 'Anciens membres',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
            ])
            ->add('membersManageEvent', ChoiceType::class, [
                'label' => 'Gestion par les membres',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
            ])
            ->add('guildRaid', ChoiceType::class, [
                'label' => "GRAID",
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'data' => $isEventNew ? $isUserAdmin : $options['data']->isGuildRaid(),
                'disabled' => !$isUserAdmin
            ]);

        if ($isEventNew) {
            $builder->add('type', EnumType::class, [
                'class' => InstanceTypeEnum::class,
                'label' => 'Type d\'évènement',
                'choice_label' => fn(InstanceTypeEnum $enum) => $enum->value,
            ]);
        }
    }

    public function validate(GuildEvent $guildEvent, ExecutionContextInterface $context): void
    {
        if ($guildEvent->getEnd() <= $guildEvent->getStart()) {
            $context->buildViolation('La date de fin doit être après la date de début.')
                ->atPath('end')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GuildEvent::class,
            'constraints' => new Callback([$this, 'validate'])
        ]);
    }
}
