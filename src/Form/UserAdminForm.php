<?php
/**
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 * Copyright (C) 2019 Jan Böhmer (https://github.com/jbtronics)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace App\Form;

use App\Entity\Base\NamedDBElement;
use App\Entity\Base\StructuralDBElement;
use App\Entity\UserSystem\Group;
use App\Entity\UserSystem\User;
use App\Form\Permissions\PermissionsType;
use App\Form\Type\CurrencyEntityType;
use App\Form\Type\StructuralEntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserAdminForm extends AbstractType
{
    protected $security;
    protected $trans;

    public function __construct(Security $security, TranslatorInterface $trans)
    {
        $this->security = $security;
        $this->trans = $trans;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver); // TODO: Change the autogenerated stub
        $resolver->setRequired('attachment_class');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var StructuralDBElement $entity */
        $entity = $options['data'];
        $is_new = null === $entity->getID();

        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
                'label' => $this->trans->trans('user.username.label'),
                'attr' => ['placeholder' => $this->trans->trans('user.username.placeholder')],
                'disabled' => !$this->security->isGranted('edit_username', $entity),
            ])

            ->add('group', StructuralEntityType::class, [
                'class' => Group::class,
                'required' => false,
                'label' => $this->trans->trans('group.label'),
                'disable_not_selectable' => true,
                'disabled' => !$this->security->isGranted('change_group', $entity), ])

            ->add('first_name', TextType::class, [
                'empty_data' => '',
                'label' => $this->trans->trans('user.firstName.label'),
                'attr' => ['placeholder' => $this->trans->trans('user.firstName.placeholder')], 'required' => false,
                'disabled' => !$this->security->isGranted('edit_infos', $entity),
            ])

            ->add('last_name', TextType::class, [
                'empty_data' => '',
                'label' => $this->trans->trans('user.lastName.label'),
                'attr' => ['placeholder' => $this->trans->trans('user.lastName.placeholder')],
                'required' => false,
                'disabled' => !$this->security->isGranted('edit_infos', $entity),
            ])

            ->add('email', TextType::class, [
                'empty_data' => '',
                'label' => $this->trans->trans('user.email.label'),
                'attr' => ['placeholder' => $this->trans->trans('user.email.placeholder')],
                'required' => false,
                'disabled' => !$this->security->isGranted('edit_infos', $entity), ])

            ->add('department', TextType::class, [
                'empty_data' => '',
                'label' => $this->trans->trans('user.department.label'),
                'attr' => ['placeholder' => $this->trans->trans('user.department.placeholder')],
                'required' => false,
                'disabled' => !$this->security->isGranted('edit_infos', $entity),
            ])

            //Config section
            ->add('language', LanguageType::class, [
                'required' => false,
                'attr' => ['class' => 'selectpicker', 'data-live-search' => true],
                'placeholder' => $this->trans->trans('user_settings.language.placeholder'),
                'label' => $this->trans->trans('user.language_select'),
                'preferred_choices' => ['en', 'de'],
                'disabled' => !$this->security->isGranted('change_user_settings', $entity),
            ])
            ->add('timezone', TimezoneType::class, [
                'required' => false,
                'attr' => ['class' => 'selectpicker', 'data-live-search' => true],
                'placeholder' => $this->trans->trans('user_settings.timezone.placeholder'),
                'label' => $this->trans->trans('user.timezone.label'),
                'preferred_choices' => ['Europe/Berlin'],
                'disabled' => !$this->security->isGranted('change_user_settings', $entity),
            ])
            ->add('theme', ChoiceType::class, [
                'required' => false,
                'choices' => User::AVAILABLE_THEMES,
                'choice_label' => function ($entity, $key, $value) {
                    return $value;
                },
                'attr' => ['class' => 'selectpicker'],
                'placeholder' => $this->trans->trans('user_settings.theme.placeholder'),
                'label' => $this->trans->trans('user.theme.label'),
                'disabled' => !$this->security->isGranted('change_user_settings', $entity),
            ])
            ->add('currency', CurrencyEntityType::class, [
                'required' => false,
                'label' => $this->trans->trans('user.currency.label'),
                'disabled' => !$this->security->isGranted('change_user_settings', $entity),
            ])

            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => $this->trans->trans('user.settings.pw_new.label')],
                'second_options' => ['label' => $this->trans->trans('user.settings.pw_confirm.label')],
                'invalid_message' => 'password_must_match',
                'required' => false,
                'mapped' => false,
                'disabled' => !$this->security->isGranted('set_password', $entity),
                'constraints' => [new Length([
                    'min' => 6,
                    'max' => 128,
                ])],
            ])

            ->add('need_pw_change', CheckboxType::class, [
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom'],
                'label' => $this->trans->trans('user.edit.needs_pw_change'),
                'disabled' => !$this->security->isGranted('set_password', $entity),
            ])

            ->add('disabled', CheckboxType::class, [
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom'],
                'label' => $this->trans->trans('user.edit.user_disabled'),
                'disabled' => !$this->security->isGranted('set_password', $entity)
                    || $entity === $this->security->getUser(),
            ])

            //Permission section
            ->add('permissions', PermissionsType::class, [
                'mapped' => false,
                'data' => $builder->getData(),
                'disabled' => !$this->security->isGranted('edit_permissions', $entity),
            ])
        ;
        /*->add('comment', CKEditorType::class, ['required' => false,
            'label' => 'comment.label', 'attr' => ['rows' => 4], 'help' => 'bbcode.hint',
            'disabled' => !$this->security->isGranted($is_new ? 'create' : 'edit', $entity)]); */

        $this->additionalFormElements($builder, $options, $entity);

        //Attachment section
        $builder->add('attachments', CollectionType::class, [
            'entry_type' => AttachmentFormType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
            'entry_options' => [
                'data_class' => $options['attachment_class'],
            ],
            'by_reference' => false,
            'disabled' => !$this->security->isGranted($is_new ? 'create' : 'edit', $entity),
        ]);

        //Buttons
        $builder->add('save', SubmitType::class, [
            'label' => $is_new ? $this->trans->trans('user.create') : $this->trans->trans('user.edit.save'),
            'attr' => ['class' => $is_new ? 'btn-success' : ''],
        ])
            ->add('reset', ResetType::class, [
                'label' => $this->trans->trans('entity.edit.reset'),
            ]);
    }

    protected function additionalFormElements(FormBuilderInterface $builder, array $options, NamedDBElement $entity)
    {
        //Empty for Base
    }
}
