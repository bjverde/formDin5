ECHO Teste PhpUnit
cd ../../

REM ECHO PHP 7.3.5 and PHPUnit 7.2.4 with Config XML file
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-7.2.4.phar --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php --configuration D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\phpunit-conf-win.xml D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

REM ECHO PHP 7.3.5 and PHPUnit 7.2.4 Simples
REM D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-7.2.4.phar --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

ECHO PHP 7.3.5 and PHPUnit 7.2.4 Simples with Coverage
D:\wamp\bin\php\php7.3.5\php.exe D:\wamp\bin\phpunit\phpunit-7.2.4.phar --bootstrap D:\wamp\www\formDin5\appexemplo_v1.0\init.php --whitelist D:\wamp\www\formDin5\appexemplo_v1.0\app\lib\widget --coverage-html D:\wamp\www\formDin5\phpunit-code-coverage D:\wamp\www\formDin5\appexemplo_v1.0\app\tests\

cd app\tests\