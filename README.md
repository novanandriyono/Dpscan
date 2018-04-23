# Dpscan
Directory Scaner Recursive Laravel

## Install via Composer

```
composer require novanandriyono/dpscan:dev-master
```

## List of Actions
* setdir(string $dir); //set root dir;

### Results Only Files Or Directory
* onlyfiles(); // results only files
* onlydir(); // result only dir

### Results Contains
* contains(array $array = []);
* containsfiles(array $array = []);
* containsdir(array $array = []);

### Results Not Contains
* notcontains(array $array = []);
* notcontainsfiles(array $array = []);
* notcontainsdir(array $array = []);

### Results Except
* except(array $array); // except full line of items

### Results Regex Only
Using Regex only
* regexonly(array $array);
* regexonlyfiles(array $array);
* regexonlydir(array $array);

### Results Regex Except
Using Regex to except
* regexcept(array $array);
* regexceptfiles(array $array);
* regexceptdir(array $array);

### Results Items
* items(); //get collection
* get();

### Change root folder
After root change DIRECTORY_SEPARATOR will be close '/';
* rootchange(string dir);

## How to use
Value is root dir from key array, use get() or items() to make key become value;
```
use Dpscan\Support\Facades\Dpscan;

$root = public_path();
$dir = Dpscan::setdir($root);

echo "<pre>";
var_dump($dir->get());
echo "</pre>";
//or
echo "<pre>";
var_dump($dir->contains(['js'])->onlydir());
echo "</pre>";

```

## Why
i made dpscan because lexroute need this XD.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details