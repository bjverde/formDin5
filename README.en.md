# formdin5

![Logo formDin](https://raw.githubusercontent.com/bjverde/formDin/master/base/imagens/formdin_logo.png)

FormDin or Dynamic Form is a simple PHP framework for creating web systems quickly and easily.

---

FormDin (or Dynamic Form) is an older, simple PHP framework designed to create web systems quickly and easily. FormDin 4 was created in 2004 by Luís Eugênio Barbosa, based on the ideas from [Pablo Dall'Oglio](http://www.dalloglio.net/c5?livros)'s books.

FormDin 5 is a meta-framework, adapter, or extension, built on top of [Adianti Framework 7.0.0 or higher](https://adiantiframework.com.br) by [Pablo Dall'Oglio](http://www.dalloglio.net/). Therefore, it is not a complete framework and has a total dependency on [Adianti](https://www.adianti.com.br/). It is a transition framework from [FormDin 4](https://github.com/bjverde/formDin) to the Adianti Framework, facilitating the migration process. It acts as an abstraction layer for FormDin 4 calls within Adianti.

[Adianti](https://www.adianti.com.br/) is an excellent framework, offering more features and a much larger community. So, the ideal approach is to join forces, taking the best of both worlds.

In 2026, with the advancement of AI, the FormDin 5 project will become obsolete. If you wish to migrate from FormDin 4 to Adianti, use the [skill-antigravity](https://github.com/bjverde/skill-antigravity) project, which contains several skills to assist with Adianti and the migration from FormDin 4 to Adianti.

# About

## Project Contents
* FormDin5 - The framework to be installed over Adianti 8.4.
* appexemplo_v1.0 - A complete example software demonstrating the various uses of FormDin5 components.
* lab - Small tests.
* phpunit-code-coverage - PHPUnit test coverage results.

# Installation
1. Download [Adianti Framework 8.4.0](https://adiantiframework.com.br). It works with the template or the pure framework.
2. Copy the contents of the FormDin5 folder according to the instructions below:
    1. In the `app/config/application.php` file, include the lines below.
    2. In the `index.php` file (at the root of the project), include the lines below.
    3. In the `init.php` file (at the root of the project), include the lines below.
    4. Copy the `lib/widget/FormDin5` folder to `/app/lib/widget/FormDin5`.

## application.php File
Edit the `app/config/application.php` file and include the lines below at the end of the `system` array.
```php
    'system' =>  [
        'system_version' => '5.14.00',
        'system_name_sub'=> 'FormDin5 Example Application 1 with Adianti',
        'adianti_min_version'=> '8.4.0',
        'formdin_min_version'=> '5.14.00',
    ],
```

## index.php File
Edit the `index.php` file. Right below these lines:
```php
$menu_string = AdiantiMenuBuilder::parse('menu.xml', $theme);
$content     = file_get_contents("app/templates/{$theme}/layout.html");
```
Include the following lines:

```php
//---FORMDIN 5 -------------------------
$content     = str_replace('{head_title}', $ini['general']['application'], $content);
$content     = str_replace('{formdin_version}', FormDinHelper::version(), $content);
$content     = str_replace('{system_version}', $ini['system']['version'], $content);
$content     = str_replace('{system_name}', $ini['system']['system_name'], $content);
$content     = str_replace('{system_name_sub}', $ini['system']['system_name_sub'], $content);
$content     = str_replace('{logo-mini}', $ini['system']['logo-mini'], $content);
$content     = str_replace('{logo-lg}', $ini['system']['logo-lg'], $content);
$content     = str_replace('{logo-link-class}', $ini['system']['logo-link-class'], $content);
$content     = str_replace('{login-link}', $ini['system']['login-link'], $content);
//---END FORMDIN 5 -----------------------
```

## init.php File
Edit the `init.php` file. Right below these lines:
```php
define('PATH', dirname(__FILE__));
define('LANG', $ini['general']['language']);
```
Include the following lines:

```php
//--- FORMDIN 5 START ---------------------------------------------------------
FormDinHelper::verifyFormDinMinimumVersion($ini['system']['formdin_min_version']);
FormDinHelper::verifyMinimumVersionAdiantiFrameWorkToSystem($ini['system']['adianti_min_version']);

if(!defined('SYSTEM_VERSION') )  { define('SYSTEM_VERSION', $ini['system']['system_version']); }
if(!defined('SYSTEM_NAME') )     { define('SYSTEM_NAME', $ini['general']['application']); }
if(!defined('DS') )  { define('DS', DIRECTORY_SEPARATOR); }
if(!defined('EOL') ) { define('EOL', "\n"); }
if(!defined('ESP') ) { define('ESP', chr(32).chr(32).chr(32).chr(32) ); }
if(!defined('TAB') ) { define('TAB', chr(9)); }
//--- FORMDIN 5 END -----------------------------------------------------------
```


# Example Applications using FormDin 5
* appexemplo_v1.0 - Included in this project.
* appexemplo_v2.0 - https://github.com/bjverde/appexemplo_v2

# Videos About
Below is a video about FormDin 5 and what to expect for the future!
[![FormDin 5 the future !!](http://img.youtube.com/vi/Sf8mQn1-CQc/0.jpg)](http://www.youtube.com/watch?v=Sf8mQn1-CQc "FormDin 5 the future !!")


## Branches
* Master - Contains modifications and new features.
* [bk20200410_formdin4to5](https://github.com/bjverde/formDin5/tree/bk20200410_formdin4to5) - Frozen version, first attempt to create FormDin5.
