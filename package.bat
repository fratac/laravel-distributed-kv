##@echo off
set package = "DistributedKv"
set package_dir = "laravel-distributed-kv"
set vendor = "fratac"

##IF NOT EXIST %package_dir% (mkdir %package_dir%)

mkdir src
echo "Create src\%package%.php"
echo "" > "src\%package%ServiceProvider.php"
mkdir src\Facades
echo "" > "src\Facades\%package%Facade.php"

mkdir src\Http
mkdir src\Http\Controllers
echo "" > "src\Http\Controllers\%package%Controller.php"

mkdir src\Http\Middleware
echo "" > "src\Http\Middleware\%package%Middleware.php"

mkdir src\Models
echo "" > "src\Models\%package%.php"

mkdir src\Services
echo "" > "src\Services\%package%Service.php"

mkdir config
echo "" > "config\%package%.php"

mkdir database
mkdir database\migrations

mkdir routes
echo "" > routes\web.php
echo "" > routes\api.php

echo "" > composer.json
echo "" > README.md

echo 'Struttura Package creata'
