# API ITS client for Laravel

The purpose of this package is ...

## Installation

```bash
composer require ifaniqbal/api-its-laravel
```

If using Laravel 5.1, include the service provider within `config/app.php`.

```php
'providers' => [
    'Ifaniqbal\ApiIts\ApiItsServiceProvider',
];
```

Add a facade alias to this same file at the bottom:

```php
'aliases' => [
    'ApiIts' => 'Ifaniqbal\ApiIts\Facades\ApiIts',
];
```

## Configuration

These are list of `.env` keys that can be configured:

```php
API_ITS_TOKEN_URL= # access token (client_credentials) token URL, default: https://my.its.ac.id/token
API_ITS_CLIENT_ID= # client ID
API_ITS_CLIENT_SECRET= # client secret
```

For example, if most of the values you need is same as default values, configure only these values within your `.env` file:

```html
API_ITS_CLIENT_ID=
API_ITS_CLIENT_SECRET=
```

```

## Usage

```php
<?php
namespace App\Http\Controllers;

use App\Mahasiswa;
use Ifaniqbal\ApiIts\Facades\ApiIts;

class MahasiswaController extends Controller
{
    public function show($id)
    {
        $mahasiswa = Mahasiswa::find($id);
        $frsKuliah = ApiIts::username($mahasiswa->username)
            ->mahasiswaFrsKuliah();
    }
}
```
