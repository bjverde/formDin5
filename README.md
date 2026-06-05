# formdin5

![Logo formDin](https://raw.githubusercontent.com/bjverde/formDin/master/base/imagens/formdin_logo.png)

FormDin or Dynamic Form is a simple PHP framework for creating web systems quickly and easily.

[See this documentation in English](README.en.md)

---

O FormDin ou Formulário Dinâmico é um framework PHP antigo e simples para criar sistemas web de forma rápida e fácil. O FormDin 4 foi criado em 2004 por Luís Eugênio Barbosa, baseado nas ideias dos livros de [Pablo Dall'Oglio](http://www.dalloglio.net/c5?livros).

O FormDin 5 é um meta-framework, adaptador ou extensão, por ser construído sobre o [Adianti Framework 7.0.0 ou superior](https://adiantiframework.com.br), de [Pablo Dall'Oglio](http://www.dalloglio.net/). Portanto, não é um framework completo e possui total dependência do [Adianti](https://www.adianti.com.br/). É um framework de transição do [FormDin 4](https://github.com/bjverde/formDin) para o Adianti Framework, facilitando a migração. Ele atua como uma abstração das chamadas do FormDin 4 no Adianti. 

O [Adianti](https://www.adianti.com.br/) é um excelente framework, com mais recursos e uma comunidade muito maior. Logo, o ideal é juntar forças aproveitando o que há de melhor em ambos.

Em 2026, com o avanço das IAs, o projeto FormDin 5 vai cair em desuso. Se você deseja migrar do FormDin 4 para o Adianti, utilize o projeto [skill-antigravity](https://github.com/bjverde/skill-antigravity), que possui várias skills para auxiliar no uso do Adianti e na migração do FormDin 4 para o Adianti.

# Sobre

## Conteúdo do Projeto
* FormDin5 - É o framework para instalar sobre o Adianti 8.4.
* appexemplo_v1.0 - Um software completo de exemplo, demonstrando os diversos usos dos componentes do FormDin5.
* lab - Pequenos testes.
* phpunit-code-coverage - Resultado da cobertura de testes do PHPUnit.

# Instalação
1. Baixe o [Adianti Framework 8.4.0](https://adiantiframework.com.br). Funciona com o template ou framework puro.
2. Copie o conteúdo da pasta FormDin5 conforme as orientações abaixo:
    1. No arquivo `app/config/application.php`, inclua as linhas abaixo.
    2. No arquivo index.php (na raiz do projeto), inclua as linhas abaixo.
    3. No arquivo init.php (na raiz do projeto), inclua as linhas abaixo.
    4. Copie a pasta `lib/widget/FormDin5` para `/app/lib/widget/FormDin5`.

## Arquivo application.php
Edite o arquivo `app/config/application.php` e inclua as linhas abaixo no final do array.
```php
    'system' =>  [
        'system_version' => '5.11.0',
        'system_name_sub'=> 'Aplicação de Exemplo 1 do FormDin5 com Adianti',
        'adianti_min_version'=> '8.4.0',
        'formdin_min_version'=> '5.10.0',
    ],
```

## Arquivo index.php
Edite o arquivo index.php. Logo abaixo das linhas:
```php
$menu_string = AdiantiMenuBuilder::parse('menu.xml', $theme);
$content     = file_get_contents("app/templates/{$theme}/layout.html");
```
Inclua as seguintes linhas:

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
Edite o arquivo init.php. Logo abaixo das linhas:
```php
define('PATH', dirname(__FILE__));
define('LANG', $ini['general']['language']);
```
Inclua as seguintes linhas:

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
* appexemplo_v1.0 - Está neste projeto.
* appexemplo_v2.0 - https://github.com/bjverde/appexemplo_v2

# Vídeos Sobre
Abaixo, um vídeo sobre o FormDin 5 e o que é esperado para o futuro!
[![FormDin 5 o futuro !!](http://img.youtube.com/vi/Sf8mQn1-CQc/0.jpg)](http://www.youtube.com/watch?v=Sf8mQn1-CQc "FormDin 5 o futuro !!")


## Branches
* Master - Contém as modificações e novidades.
* [bk20200410_formdin4to5](https://github.com/bjverde/formDin5/tree/bk20200410_formdin4to5) - Versão congelada, primeira tentativa de criar o FormDin5.
