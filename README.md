Get Linux Server Specs
======================

Provides a simple API to get server specifications with PHP.

Installation with Composer
--------------------------

```shell
curl -s http://getcomposer.org/installer | php
php composer.phar require cyberdummy/server-spec
```

OR

```shell
composer require cyberdummy/server-spec
```

# Usage
## Local System
```php
use Cyberdummy\ServerSpec\Spec;
use Cyberdummy\ServerSpec\LocalRunner;

$spec = new Spec(new LocalRunner());
$spec->ram();
```

## Via SSH
The SSH runner takes an instance of phpseclib\Net\SSH2.

```php
use Cyberdummy\ServerSpec\Spec;
use Cyberdummy\ServerSpec\SshRunner;
use phpseclib\Net\SSH2;

$ssh = new SSH2('192.168.1.1');
$ssh->login('root', 'rootpass');

$spec = new Spec(new SshRunner($ssh));
$spec->ram();
```

# API
| Method          | Description              |
|:----------------|:-------------------------|
| ram()           | The ram in GB.           |
| cpuClock()      | The CPU clock speed.     |
| cpuCores()      | The number of CPU cores. |
| os()            | The OS description.      |
| kernelVersion() | The linux kernel version |
