# FormFilterBundle
The simplest way to build search forms in Symfony.

## Installation

`composer require padam87/form-filter-bundle`

## Usage

My goal was to create a simpler, lighter way to build to search forms than what is currently available.
No learning curve, just a simple abstraction. The bundle uses built-in form types, with some extra filter types for convenience.

The bundle provides a form type extension, and makes 2 new options available for every type:
- `filter` bool / callable, default: true
- `filter_expr` string, has to be a valid doctrine expr, default: eq

### Build a form

```php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('email', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'method' => Request::METHOD_GET,
                    'csrf_protection' => false,
                ]
            )
        ;
    }
}
```

As you can see, the search fields are using the built in `TextType`.

### Controller

```php
$filters = $this->createForm(UserFilterType::class);
$filters->handleRequest($request);

$qb = $em->getRepository(User::class)->createQueryBuilder('alias');

$this->get('padam87_form_filter.filters')->apply($qb, $filters);

// paginate, render template etc.
```

## Advanced usage

`filter_expr` - You can change the expression used in the filter, for example in the example above it would nice to use a `like` expression instead of `eq`.

```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['filter_expr' => 'like'])
            ->add('email', TextType::class, ['filter_expr' => 'like'])
        ;
    }
```

`filter` - The filter option gives you full control over the field's behavior.
If a simple expression is not enough, you can use a callback to customize the filter.

```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['filter_expr' => 'like'])
            ->add('email', TextType::class, ['filter_expr' => 'like'])
            ->add(
                'city',
                TextType::class,
                [
                    'filter' => function(QueryBuilder $qb, $alias, $value) {
                        $qb
                            //->join('u.address', 'a')
                            ->andWhere($qb->expr->eq('a.city', ':city'))
                            ->setParameter('city', $value)
                        ;
                    
                        return $qb;
                    }
                ]
            )
        ;
    }
```

NOTE: You should not use joins here, write a custom method in the repository, eg `getListQb` and join everything you need to filter there.

### Filter types
- [BooleanFilterType](https://github.com/Padam87/FormFilterBundle/blob/master/Form/BooleanFilterType.php) - 3 state filter for boolean values. (A simple checkbox would only have 2 states).
- [RangeFilterType](https://github.com/Padam87/FormFilterBundle/blob/master/Form/RangeFilterType.php) - A filter for ranges (numeric, date, any other)
```
$builder
	->add(
		'createdAt',
		RangeFilterType::class,
		[
			'from_field_type' => DateType::class,
			'from_field_options' => [
				'widget' => 'single_text',
			],
			'to_field_type' => DateType::class,
			'to_field_options' => [
				'widget' => 'single_text',
			],
            'to_field_expr' => 'lt'
		]
	)
;
```
