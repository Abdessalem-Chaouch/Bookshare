@echo off
echo Demarrage des services pour LibroLink...

echo.
echo 1. Tentative de demarrage de MySQL...
net start mysql 2>nul
if %errorlevel% neq 0 (
    net start mysql80 2>nul
    if %errorlevel% neq 0 (
        net start mysql57 2>nul
        if %errorlevel% neq 0 (
            echo ERREUR: Impossible de demarrer MySQL. Verifiez que MySQL est installe.
            echo Vous pouvez demarrer MySQL manuellement via XAMPP ou WAMP.
        ) else (
            echo MySQL57 demarre avec succes!
        )
    ) else (
        echo MySQL80 demarre avec succes!
    )
) else (
    echo MySQL demarre avec succes!
)

echo.
echo 2. Demarrage du serveur Laravel...
start cmd /k "cd /d %~dp0 && php artisan serve"

echo.
echo 3. Services demarres!
echo   - Serveur Laravel: http://localhost:8000
echo   - Assurez-vous que MySQL est actif
echo.
pause