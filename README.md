# formdin5

![Logo formDin](https://raw.githubusercontent.com/bjverde/formDin/master/base/imagens/formdin_logo.png)

FormDin or Dynamic Form is a simple php Framework for creating web system quickly and easily.

---

FormDin ou Formulário Dinâmico é um Framework php antigo e simples para criar sistema web de forma rápida e fácil. O Formdin 4 foi criado em 2004 por Luís Eugênio Barbosa baseado nas ideias dos livros de [Pablo Dall'Oglio](http://www.dalloglio.net/c5?livros).

O FormDin 5 é um meta FrameWork ou um adaptador ou uma extensão, por ser construído sobre o [adianti FrameWork 7.0.0 ou superior](https://adiantiframework.com.br) do [Pablo Dall'Oglio](http://www.dalloglio.net/). Portando não é um FrameWork completo, ele tem total dependência do [adianti](https://www.adianti.com.br/). É um FrameWork de transição do [FormDin 4](https://github.com/bjverde/formDin) para o Adianti FrameWork, facilitando a migração. É uma abstração das chamadas do FormDin 4 no Adianti. 

O [adianti](https://www.adianti.com.br/) é um FrameWork muito bom, com mais recursos e uma comunidade muito maior. Logo o ideal é juntar forçar pegando o que tem de melhor dos dois.

Em 2026 com o avança das IAs o projeto FormDin 5 vai cair em desuso, se deseja migrar do FormDin 4 para o Adianti use o projeto [skill-antigravity](https://github.com/bjverde/skill-antigravity) que tem várias skill para auxiliar com adianti e migração do FormDin 4 para Adianti.

# Sobre

## Conteúdo do Projeto
* FormDin5 - é o FrameWork para instalar sobre o adianti 8.4
* appexemplo_v1.0 - um software completo de exemplo, com os diversos usos dos componentes do FormDin5
* lab - pequenos testes
* phpunit-code-coverage - resultado da cobertura dos testes do PHPUnit

# Instalação
1. Baixar o [adianti FrameWork 8.4.0](https://adiantiframework.com.br) funciona com o template ou FrameWork Puro.
1. Copiar o conteudo pasta FormDin5 conforme orientação abaixo
    1. No arquivo `app/config/application.php` incluir as linhas abaixo
    1. No arquivo index.php da raiz do projeto incluir as linhas abaixo
    1. No arquivo init.php da raiz do projeto incluir as linhas abaixo
    1. Copiar a pasta `lib/widget/FormDin5` para `/app/lib/widget/FormDin5`

## Arquivo application.php
Editar `app/config/application.php` incluir as linhas abaixo no final do array.
```php
    'system' =>  [
        'system_version' => '5.11.0',
        'system_name_sub'=> 'Aplicação de Exemplo 1 do FormDin5 com Adianti',
        'adianti_min_version'=> '8.4.0',
        'formdin_min_version'=> '5.10.0',
    ],
```

## Arquivo index.php
Editar o arquivo index.php, abaixo das linhas
```php
$menu_string = AdiantiMenuBuilder::parse('menu.xml', $theme);
$content     = file_get_contents("app/templates/{$theme}/layout.html");
```
Incluir as linhas

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
//---FIM FORMDIN 5 -----------------------
```

## Arquivo init.php
Editar o arquivo init.php, abaixo das linhas
```php
define('PATH', dirname(__FILE__));
define('LANG', $ini['general']['language']);
```
Incluir as linhas

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


# Aplicações de Exemplo usando FormDin 5
* appexemplo_v1.0 - está neste projeto
* appexemplo_v2.0 - https://github.com/bjverde/appexemplo_v2

# Videos Sobre
Abaixo um vídeo sobre o FormDin 5 e está esperado para o Futuro !
[![FormDin 5 o futuro !!](http://img.youtube.com/vi/Sf8mQn1-CQc/0.jpg)](http://www.youtube.com/watch?v=Sf8mQn1-CQc "FormDin 5 o futuro !!")


## Branchs
* Master - tem as modificações e novidades
* [bk20200410_formdin4to5](https://github.com/bjverde/formDin5/tree/bk20200410_formdin4to5)- versão congelada, primeira tentatica de criar o FormDin5.
