<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\RolesName;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Enum\UserLanguage;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('FirstName'),
            TextField::new('LastName'),
            EmailField::new('Email'),
            TextField::new('Password')
                ->setFormType(PasswordType::class)
                ->hideOnIndex(),
            ChoiceField::new('Language')
                ->setChoices([
                    'English' => UserLanguage::English,
                    'Français' => UserLanguage::Francais,
                ])
                ->setFormTypeOption('choice_label', fn (UserLanguage $choice) => $choice->value),

            // TODO: Add registration date and last connection date fields coorectly !?
            
            DateTimeField::new('registrationDateTime'),
            DateTimeField::new('last_connection'),
            ChoiceField::new('roles')
                ->setChoices([
                    'Admin' => RolesName::ADMIN,
                    'User' => RolesName::USER,
                ])
                ->allowMultipleChoices(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud;
    }

    private function addPasswordHashingListener(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $user = $event->getData();  
            $form = $event->getForm();

            if (isset($user['Password']) && !empty($user['Password'])) {
                $plainPassword = $user['Password'];
                $userEntity = $form->getData();

               
                if ($userEntity instanceof \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface) {
                   
                    $hashedPassword = $this->userPasswordHasher->hashPassword($userEntity, $plainPassword);
                    $user['Password'] = $hashedPassword;
                }
            } else {
             
                unset($user['Password']);
            }

            $event->setData($user);
        });
    }

    public function createEditFormBuilder(
        \EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto $entityDto, 
        \EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore $formOptions, 
        AdminContext $context
    ): FormBuilderInterface {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        $this->addPasswordHashingListener($formBuilder);
        return $formBuilder;
    }

    public function createNewFormBuilder(
        \EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto $entityDto, 
        \EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore $formOptions, 
        AdminContext $context
    ): FormBuilderInterface {

        print_r("TTest: Creating new");

        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        $this->addPasswordHashingListener($formBuilder);

        return $formBuilder;
    }
}