# Dpscan
Just a directory scanner.

## Install via Composer
```
composer require novanandriyono/dpscan
```

## List of Actions
Set base dir
* setdir(string $dir);

### Results Items
* get(); get all items inside directory
* all(); to get recursive

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

### Change root folder
After root change DIRECTORY_SEPARATOR will be close '/';
* rootchange(string dir);

### Change root folder
After root change DIRECTORY_SEPARATOR will be close '/';
* rootchange(string dir);

### Saving last result with Cache
option = action
* cache(int $minutes,string $key,string $option);
```
$dir = storage_path();
setdir($dir)->cache(10,$dir,'all')->contains(['.jpg','.png'])->rootchange(url())->items()->toJson();
```
or
```
$userdir = public_path('/data/'.$user->id);
setdir($userdir)->cache(10,$userdir,'all')->onlyfiles()->contains(['.jpg','.png'])->rootchange(url())->items()->toJson();
```
or
```
$username = \Auth::user()->username;
$userdir = public_path($username.'/media');
$contains = \Request::input()->all() + ['.jpg'];
setdir($userdir)->cache(10,$userdir,'all')->onlyfiles()->contains($contains)->rootchange(null)->items()->toJson();
```
and I never tried XD

## How to use
Results on array. both key and value are same. use array_values/array_keys to
get int key or using collection
```
use Dpscan;

$root = public_path();
$dir = Dpscan::setdir($root);

echo "<pre>";
var_dump($dir->all());
echo "</pre>";
//or
echo "<pre>";
dd($dir->onlyfiles()->contains(['js','css'])->rootchange(null)->items()->toJson());
echo "</pre>";

```

## Why
i made dpscan because lexroute need this XD.

## License
This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details