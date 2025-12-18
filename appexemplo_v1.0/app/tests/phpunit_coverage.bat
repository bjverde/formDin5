@echo off 

ECHO Teste PhpUnit
cd ../../

REM ---------------- 12.3.6 -------------------------
ECHO PHP 8.3.6 and PHPUnit 12.3.6 Simples with Coverage
d:\wamp64\bin\php\php8.3.6\php.exe d:\wamp64\bin\phpunit\phpunit-12.3.6.phar --coverage-html app\tests\phpunit-reports\code-coverage --testdox-html app\tests\phpunit-reports\code-report.html


cd app\tests\