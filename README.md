# Dpscan
Directory Scaner Recursive Laravel
Results use Laravel Collection

## Install via Composer

```
composer require novanandriyono/dpscan
```

## How to use

```
use Dpscan\Support\Facades\Dpscan;

$root = public_path();
$dir = Dpscan::setdir($root);
$allfiles = $dir->all();

echo "<pre>";
var_dump($allfiles->results());
echo "</pre>";

//or

echo "<pre>";
var_dump($allfiles->getfiles());
echo "</pre>";

//or

echo "<pre>";
var_dump($allfiles->getdir());
echo "</pre>";

```
## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details