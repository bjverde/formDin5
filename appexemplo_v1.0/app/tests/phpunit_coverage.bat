@echo off 

ECHO Teste PhpUnit
cd ../../

REM ---------------- 7.2.4 -------------------------

REM ECHO PHP 7.3.5 and PHPUnit 7.2.4 Simples
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-7.2.4.phar --colors=auto --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

REM ECHO PHP 7.3.5 and PHPUnit 7.2.4 Simples with Coverage
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-7.2.4.phar --colors=auto --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php --whitelist D:\wamp\www\formDin5\appexemplo_v1.0\app\lib\widget --coverage-html D:\wamp\www\formDin5\phpunit-code-coverage D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

REM ECHO PHP 7.3.5 and PHPUnit 7.2.4 with Config XML file
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-7.2.4.phar --colors=auto --configuration D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\phpunit-conf-win.xml 

REM ---------------- 8.1.3 -------------------------

REM ECHO PHP 7.3.5 and PHPUnit 8.1.3 Simples
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-8.1.3.phar --colors=auto --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

REM ECHO PHP 7.3.5 and PHPUnit 8.1.3 Simples with Coverage
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-8.1.3.phar --colors=auto --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php --whitelist D:\wamp\www\formDin5\appexemplo_v1.0\app\lib\widget --coverage-html D:\wamp\www\formDin5\phpunit-code-coverage D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

REM ECHO PHP 7.3.5 and PHPUnit 8.1.3 with Config XML file
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-8.1.3.phar --colors=auto --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php --configuration D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\phpunit-conf-win.xml D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

REM ---------------- 9.1.4 -------------------------

REM ECHO PHP 7.3.5 and PHPUnit 9.1.4 Simples
REM D:\wamp64\bin\php\php7.4.26\php.exe D:\wamp64\bin\phpunit\phpunit-9.1.4.phar --colors=auto --bootstrap D:\wamp64\www\adianti\formDin5\appexemplo_v1.0\init.php D:\wamp64\www\adianti\formDin5\appexemplo_v1.0\app\tests\

REM ECHO PHP php7.4.26 and PHPUnit 9.1.4 Simples with Coverage
REM D:\wamp64\bin\php\php7.4.26\php.exe D:\wamp64\bin\phpunit\phpunit-9.1.4.phar --colors=auto --bootstrap D:\wamp64\www\adianti\formDin5\appexemplo_v1.0\init.php --whitelist D:\wamp64\www\adianti\formDin5\appexemplo_v1.0\app\lib\widget\FormDin5 --coverage-html D:\wamp64\www\adianti\formDin5\phpunit-code-coverage D:\wamp64\www\adianti\formDin5\appexemplo_v1.0\app\tests\

REM ECHO PHP 7.3.5 and PHPUnit 9.1.4 with Config XML file
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-9.1.4.phar --colors=auto --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php --configuration D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\phpunit-conf-win.xml D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

REM ECHO PHP 8.0.13 and PHPUnit 9.1.4 Simples with Coverage
REM D:\wamp64\bin\php\8.0.13\php.exe D:\wamp64\bin\phpunit\phpunit-9.1.4.phar --colors=auto --bootstrap D:\wamp64\www\adianti\formDin5\appexemplo_v1.0\init.php --whitelist D:\wamp64\www\adianti\formDin5\appexemplo_v1.0\app\lib\widget\FormDin5 --coverage-html D:\wamp64\www\adianti\formDin5\phpunit-code-coverage D:\wamp64\www\adianti\formDin5\appexemplo_v1.0\app\tests\

REM ---------------- 9.5.9 -------------------------

REM ECHO PHP 8.1.13 and PHPUnit 9.5.9 Simples with Coverage
REM c:\wamp64\bin\php\php8.1.13\php.exe c:\wamp64\bin\phpunit\phpunit-9.5.9.phar --colors=auto --bootstrap C:\wamp64\www\adinatiApp\formDin5\appexemplo_v1.0\init.php --whitelist C:\wamp64\www\adinatiApp\formDin5\appexemplo_v1.0\app\lib\widget\FormDin5 --coverage-html C:\wamp64\www\adinatiApp\formDin5\phpunit-code-coverage\html C:\wamp64\www\adinatiApp\formDin5\appexemplo_v1.0\app\tests\

ECHO php 8.1.13 and PHPUnit 9.5.9 with Config XML file
d:\wamp64\bin\php\php8.1.13\php.exe  d:\wamp64\bin\phpunit\phpunit-9.5.9.phar --configuration D:\wamp64\www\adiantiApp\formDin5\appexemplo_v1.0\app\tests\phpunit-conf-win.xml

REM ---------------- 10.0.11 -------------------------

REM ECHO PHP 8.1.0 and PHPUnit 10.0.11 Simples with Coverage
REM c:\wamp64\bin\php\php8.1.0\php.exe c:\wamp64\bin\phpunit\phpunit-10.0.11.phar --colors=auto --bootstrap c:\wamp64\www\adiantiApp\formDin5\appexemplo_v1.0\init.php --whitelist D:\wamp64\www\adiantiApp\formDin5\appexemplo_v1.0\app\lib\widget\FormDin5 --coverage-html c:\wamp64\www\adianti\formDin5\phpunit-code-coverage\html c:\wamp64\www\adianti\formDin5\appexemplo_v1.0\app\tests\

cd app\tests\