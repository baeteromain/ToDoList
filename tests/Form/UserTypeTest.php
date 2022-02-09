<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidDataUser()
    {
        $formData = [
            'username' => 'Alexandre',
            'password' => [
                'first' => 'password',
                'second' => 'password',
            ],
            'email' => 'tual.alexandre@gmail.com',
            'roles' => 'ROLE_ADMIN',
        ];

        $object = new User();

        $form = $this->factory->create(UserType::class, $object);

        $objectToCompare = new User();
        $objectToCompare->setUsername('Alexandre');
        $objectToCompare->setPassword('password');
        $objectToCompare->setEmail('tual.alexandre@gmail.com');
        $objectToCompare->setRoles(['ROLE_ADMIN']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($objectToCompare, $object);

        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}