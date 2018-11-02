<?php

use App\Form\Type\CustomType;
use Blackfire\Client;
use Blackfire\Profile\Configuration;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Validator\Validation;

require 'vendor/autoload.php';

$formFactory = Forms::createFormFactoryBuilder()
    ->addExtension(new CoreExtension())
    ->addExtension(new HttpFoundationExtension())
    ->addExtension(new CsrfExtension(new CsrfTokenManager(new UriSafeTokenGenerator(), new SessionTokenStorage(new Session()))))
    ->addExtension(new ValidatorExtension(Validation::createValidator()))
    ->getFormFactory();

$samples = 100;
$blackfire = new Client();
$config = (new Configuration())
    ->setTitle('OptionsResolver/Form')
    ->setSamples($samples)
;
$probe = $blackfire->createProbe($config, false);

for ($i = 0; $i < $samples; ++$i) {
    $probe->enable();

    $builder = $formFactory->createBuilder();
    for ($n = 0; $n < 1000; ++$n) {
        $builder
            // core types
            ->add('text', TextType::class)
            ->add('number', NumberType::class)
            ->add('integer', IntegerType::class)
            ->add('datetime', DateTimeType::class)
            ->add('datetimeinterval', DateIntervalType::class)
            ->add('checkbox', CheckboxType::class)
            ->add('file', FileType::class)
            ->add('money', MoneyType::class)
            ->add('country', CountryType::class)
            ->add('choice', ChoiceType::class, ['choices' => ['A', 'B', 'C', 'D', 'E']])
            ->add('collection', CollectionType::class, [
                'entry_type' => TextareaType::class,
            ])
            // custom type
            ->add('custom', CustomType::class, ['deprecated2' => false])
        ;
    }
    $builder->getForm();

    $probe->close();
}

echo $blackfire->endProbe($probe)->getUrl()."\n";
