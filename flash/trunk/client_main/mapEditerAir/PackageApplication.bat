@echo off

:: AIR application packaging
:: More information:
:: http://livedocs.adobe.com/flex/3/html/help.html?content=CommandLineTools_5.html#1035959


:: Path to Flex SDK binaries
set PATH=%PATH%;D:\Program Files\adobe\Adobe Flash Builder 4.5\sdks\4.5.1\bin
set APP_NAME=%PROJECT_ID%
set APK_FILE=%OUTPUT_APK%

set CERTIFICATE=%PATH%;slamjj.p12

:: Signature (see 'CreateCertificate.bat')
set CERTIFICATE=mapEditerAir.pfx
set APP_NAME=%PROJECT_ID%
set SIGNING_OPTIONS=-storetype pkcs12 -keystore %CERTIFICATE% -tsa none 

:: Output
if not exist air md air
set AIR_FILE=air/mapEditerAir.air

:: Input
set APP_XML=application.xml
set FILE_OR_DIR= -C bin mapEditerAir.swf -C bin data.txt -C bin swf -C bin data -C bin classdata  

echo Signing AIR setup using certificate %CERTIFICATE%.
call adt -package %SIGNING_OPTIONS% %AIR_FILE% %APP_XML% %FILE_OR_DIR%
if errorlevel 1 goto failed

echo.
echo AIR setup created: %AIR_FILE%
echo.
goto end

:certificate
echo Certificate not found: %CERTIFICATE%
echo.
echo Troubleshotting: 
echo A certificate is required, generate one using 'CreateCertificate.bat'
echo.
goto end

:failed
echo AIR setup creation FAILED.
echo.
echo Troubleshotting: 
echo did you configure the Flex SDK path in this Batch file?
echo.

:end
pause
