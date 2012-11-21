Bluz, a lightweight PHP Framework
=================================
Easy to setup, easy to use.


## Installation

Bluz works with PHP 5.4 or later and MySQL 5.1 and later

First you need to download composer.phar

```
curl -s https://getcomposer.org/installer | php
```

Then you need to install all the necessary project dependencies by this command:

```
php composer.phar install
```

Requried FollowSymlinks option. Create symlink to public directory:

```
ln -s /path/to/public /var/www/htdocs
```

Run install.sh:

```
./install.sh
```

Restore database structure from `structure.ddl` file.
Restore default database data from `dump.sql`

Edit your own configuration file ```/path/to/application/configs/app.dev.php```


## Usage

Controller:

```php
<?php
return
/**
 * @privilege View-User-Profile
 * @cache 5 minutes
 * @param integer $id
 * @return closure
 */
function($id) use ($view) {
    /**
     * @var Application $this
     * @var View $view
     */
     $view->user = Users\Table::getInstance()->findRow($id);
};
```

View:

```php
<h2><?=$user->login?></h2>
```

Model:

```php
<?php
namespace Application\Users;
class Table extends \Bluz\Db\Table
{
    protected $table = 'users';
    protected $primary = array('id');
}
```

```php
<?php
namespace Application\Users;
class Row extends \Bluz\Db\Row {
    public $id;
    public $login;
}
```


## License

Read LICENSE file

## Contributors

* [Anton Shevchuk][1] 
* [Eugene Zabolotniy][2] 
* [Maks Slesarenko][3] 
* [EagleMoor][4]

## Vendors

* jQuery - https://github.com/jquery/jquery
* Bootstrap - http://twitter.github.com/bootstrap/javascript.html

[1]: https://github.com/AntonShevchuk
[2]: https://github.com/Baziak
[3]: https://github.com/MaksSlesarenko
[4]: https://github.com/EagleMoor