## Bluz::Validator

### Создание правил валидации

Создание правил и валидация данных:

```php
use Bluz\Validator\Validator;

$rule = new Validator\IntegerRule();
$rule->validate('123123');
// or
$rule('123123');
```

Правила могут выстраиваться в цепочку:

```php
use Bluz\Validator\ValidatorChain;

$chain = new ValidatorChain('some name');
$chain->required()
      ->alphaNumeric()
      ->validate('first');
// or
$chain('first')
```

Цепочки можно объединять в формы:

```php
use Bluz\Validator\ValidatorForm;

$form = new ValidatorForm();
$form->add('first_name')->required()->alphaNumeric();
$form->add('last_name')->required()->alphaNumeric();
if (!$form->validate([
        'first_name' => 'Anton',
        'last_name' => 'Shevchuk'
    ]) {
    $form->getErrors();
}
```

Так же правила валидации доступны через статический вызов для быстрой проверки:

```php
Validator::integer()->validate('42.42');
```

Статический вызов возвращает цепочку `ValidatorChain`:

```php
Validator::string()->length(25, 40);
```

### Текстовое описание правил

Для получения описания правил в текстовом виде следует использовать метод `getDescription()`:

```php
$rule->getDescription();
// 'is required'

$chain->getDescription();
// ['is required', 'must be alphanumeric']

$form->getDescription();
// ['first_name' => ['is required', 'must be alphanumeric']]
```

Альтернативный способ:

```php
echo $rule;
// >> is required

echo $chain;
// >> is required
// >> must be alphanumeric'

echo $form;
// !!! Exception, is not supported !!!
```

Поддержка возможности локализации и кастомизации ошибок:

```php
// for rule
$rule->setDescription("Should be alphanumeric")

// for chain
$chain->setDescription("First name is required");

// for form
$form->add('first_name')->setDescription("First name is required");
```

### Обработка ошибок

Поддержка как валидация true/false так и `Exception`:

```php
$rule->validate($input);
// throw ValidatorException
$rule->assert($input);

$chain->validate($input);
// or throw ValidatorException
$chain->assert($input);

$form->validate($formData);
// or throw ValidatorException
$form->assert($formData);
```

Обработка ошибок:

```php
// for chain, and the same for rule
try {
    $chain->assert('some data');
} catch (ValidatorException $e) {
    echo $e->getMessage();
}

// for form
try {
    $form->assert($_REQUEST)
} catch (ValidatorException $e) {
    foreach ($e->getErrors() as $field => $error) {
        echo $error;
    }
}
```

### Подключение новых правил валидации

Для подключения новых правил валидации следует вызвать статический метод `Validator::addRuleNamespace($namespace)`:

```php
use Bluz\Validator\Validator;
Validator::addRuleNamespace('\\MyNameSpace\\Rule\\');
// your class \MyNameSpace\Rule\MyCustomRule 
// should load by composer autoloader
Validator::myCustom()->validate($input);
```
