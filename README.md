# Dpscan
Directory Scaner Recursive Laravel

## Install via Composer

```
composer require novanandriyono/dpscan
```

## How to use

```
use Dpscan\Support\Facades\Dpscan;

$root = public_path();
$dir = Dpscan::setdir($root);

echo "<pre>";
var_dump($dir->get());
//get collection items
var_dump($dir->get()->items());
echo "</pre>";

//or

echo "<pre>";
var_dump($dir->onlyfiles());
get collection items
var_dump($dir->onlyfiles()->items());
echo "</pre>";

//or

echo "<pre>";
var_dump($dir->onlydir());
//get collection items
var_dump($dir->onlydir()->items());
echo "</pre>";

```
## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details