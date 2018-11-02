<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('connection', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // nested options
                'database' => function (OptionsResolver $database) {
                    $database->setDefaults([
                        'driver' => 'mysql',
                        'host' => 'localhost',
                        'pass' => 'pa$$',
                        'options' => function (OptionsResolver $options) {
                            $options->setDefaults([
                                'name' => 'test',
                                'timeout' => 30,
                                'etc' => null,
                            ]);
                        },
                    ]);
                },
                // arrays
                'range' => [1, 2, 3],
                'multi_range' => [[1, 2, 3], [1, 2, 3], [1, 2, 3]],
                // deprecations
                'deprecated1' => null,
                'deprecated2' => null,
            ])
            ->setAllowedTypes('range', 'int[]')
            ->setAllowedTypes('multi_range', 'int[][]')
            ->setDeprecated('deprecated1')
            ->setDeprecated('deprecated2', function (Options $options, $value) {
                return false === $value ? 'Deprecated null value' : '';
            })
        ;
    }
}
