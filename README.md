<div align="center">
  <img width="635" height="217" src="media/nylas-php.png" />
</div>

# Nylas PHP SDK
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FAgencyzoom%2Fnylas-php.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2FAgencyzoom%2Fnylas-php?ref=badge_shield)


PHP bindings for the Nylas REST API (V2.1). https://docs.nylas.com/reference</br>
I'll try to keep up with [NyLas Changelog](https://changelog.nylas.com) in future updates.

**What's new?**</br>

1. API 2.1 support</br>
2. All Nylas APIs have been implemented within this SDK.</br>
3. Support send & get message in raw type</br>
4. Support async batch upload & download</br>
   -- Contact picture download</br>
   -- File upload & download<br>
5. The parameters that required by methods almost the same as nylas official api required.</br>
6. Support async batch get & delete & send (since version 3.1).
7. Chained calls and good code hints, easy to use</br>

<div align="center">
  <img width="664" height="596" src="media/autocomplete.gif" />
</div>


## Installation (PHP 7.4 required since version 4.0)

This library is available on https://packagist.org/packages/lanlin/nylas-php</br>
You can install it by running

```shell
composer require lanlin/nylas-php
```


## Usage

### App ID and Secret

Before you can interact with the Nylas REST API,</br>
you need to create a Nylas developer account at [https://www.nylas.com/](https://www.nylas.com/).</br>
After you've created a developer account, you can create a new application to generate an App ID / Secret pair.</br>

Generally, you should store your App ID and Secret into environment variables to avoid adding them to source control.</br>
The test projects use configuration files instead, to make it easier to get started.</br>

### Init Nylas-PHP

```php
use Nylas\Client;

$options =
[
    'debug'            => true,
    'log_file'         => dirname(__FILE__) . '/test.log',  // a file path or a resource handler
    'account_id'       => 'your account id',
    'access_token'     => 'your access token',

    'client_id'        => 'your client id',        // required
    'client_secret'    => 'your client secret'     // required
    'off_decode_error' => false,                   // disable json_decode error or not, default enable.
];

$nylas = new Client($options);
```

### Options Setting

You can modify options with these methods:

```php
$nylas->Options()->
```
<div align="center">
  <img width="515" height="307" src="media/options.png" />
</div>


### Batch Request

Most of the methods that have the get & delete prefix support batch request.

```php
$id  = 'id_xxx';
$ids = ['id_xxx', 'id_yyy', ...];

// one per time
$dataA = $nylas->Contacts()->Contact()->getContact($id);
$dataB = $nylas->Contacts()->Contact()->deleteContact($id);

// batch request
$dataC = $nylas->Contacts()->Contact()->getContact($ids);
$dataD = $nylas->Contacts()->Contact()->deleteContact($ids);
```

For more detail about the batch request, you should have to read the source code.</br>
Sorry, I have no time to write documents.


### Authentication

There are two ways you can authenticate users to your application.</br>
Hosted & Native are both supported.</br>

Here's the server-side(three-legged) OAuth example:</br>

1. You redirect the user to nylas login page, along with your App Id and Secret</br>
2. Your user logs in</br>
3. She is redirected to a callback URL of your own, along with an access code</br>
4. You use this access code to get an authorization token to the API</br>

For more information about authenticating with Nylas,</br>
visit the [Developer Documentation](https://docs.nylas.com/reference#authentication).</br>

In practice, the Nylas REST API client simplifies this down to two steps.</br>

**Step 1: Redirect the user to Nylas:**

```php
$params =
[
    'state'        => 'testing',
    'login_hint'   => 'test@gmail.com',
    'redirect_uri' => 'https://www.test.com/redirect_callback',
];

// generate the url that your user need be redirect to.
$url = $nylas->Authentication()->Hosted()->getOAuthAuthorizeUrl($params);
```

**Step 2: your user logs in:**</br>
**Step 3: you got the access code from the nylas callback:**</br>
Please implement the above 2 & 3 steps yourself.</br>

**Step 4: Get authorization token with access code:**

```php
$data = $nylas->Authentication()->Hosted()->postOAuthToken($params);

// save your token some where
// or update the client option
$nylas->Options()->setAccessToken("pass the token you got");
```

## Supported Methods

The parameters that required by methods almost the same as nylas official api required.

For more detail, you can view the tests or the source code of validation rules for that method.

### [Accounts](https://docs.nylas.com/reference#accounts)

```php
$nylas->Accounts()->Account()->xxx();
$nylas->Accounts()->Manage()->xxx();
```


### [Authentication](https://docs.nylas.com/reference#authentication)

```php
$nylas->Authentication()->Hosted()->xxx();
$nylas->Authentication()->Native()->xxx();
```


### [Calendars](https://docs.nylas.com/reference#calendars)

```php
$nylas->Calendars()->Calendar()->xxx();
```


### [Contacts](https://docs.nylas.com/reference#contacts-intro)

```php
$nylas->Contacts()->Contact()->xxx();
```

```php
// multiple contact pictures download
$params =
[
    [
        'id'   => 'contact id',
        'path' => 'this can be a file path, resource or stream handle',
    ],
    [
        'id'   => 'xxxx',
        'path' => dirname(__FILE__) . '/correct.png',
    ],
    // ...
];

$nylas->Contacts()->Contact()->getContactPicture($params);
```


### [Deltas](https://docs.nylas.com/reference#deltas)

```php
$nylas->Deltas()->Delta()->xxx();
```


### [Draft](https://docs.nylas.com/reference#drafts)

```php
$nylas->Drafts()->Draft()->xxx();
$nylas->Drafts()->Sending()->xxx();
```


### [Events](https://docs.nylas.com/reference#events)

```php
$nylas->Events()->Event()->xxx();
```


### [Files](https://docs.nylas.com/reference#files)

```php
$nylas->Files()->File()->xxx();
```


```php
// multiple files download
$params =
[
    [
        'id'   => 'file id',
        'path' => 'this can be a file path, resource or stream handle',
    ],
    [
        'id'   => 'xxxx',
        'path' => dirname(__FILE__) . '/correct.png',
    ],
    // ...
];

$nylas->Files()->File()->downloadFile($params);


// multiple files upload
$params =
[
    [
        'contents' => 'this can be a file path, resource or stream handle',
        'filename' => 'your file name'
    ],
    [
        'contents' => dirname(__FILE__) . '/correct.png',
        'filename' => 'test_correct.png'
    ],
    // ...
];

$nylas->Files()->File()->uploadFile($params);
```


### [Folders](https://docs.nylas.com/reference#folders)

```php
$nylas->Folders()->Folder()->xxx();
```


### [Labels](https://docs.nylas.com/reference#labels)

```php
$nylas->Labels()->Label()->xxx();
```


### [Messages](https://docs.nylas.com/reference#messages)

```php
$nylas->Messages()->Message()->xxx();
$nylas->Messages()->Search()->xxx();
$nylas->Messages()->Sending()->xxx();
$nylas->Messages()->Smart()->xxx();
```


### [Threads](https://docs.nylas.com/reference#threads)

```php
$nylas->Threads()->Search()->xxx();
$nylas->Threads()->Thread()->xxx();
$nylas->Threads()->Smart()->xxx();
```


### [Webhooks](https://docs.nylas.com/reference#webhooks)

```php
$nylas->Webhooks()->Webhook()->xxx();
```


## Contributing

For more usage demos, please view the tests.</br>
Please feel free to use it and send me a pull request if you fix anything or add a feature, though.</br>

### Launching the tests

1. Initialise composer dependency ´composer install´
1. Add your info in ´tests/Abs.php´
1. Launch the test with ´vendor/bin/phpunit tests´

## License

This project is licensed under the MIT license.


[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FAgencyzoom%2Fnylas-php.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2FAgencyzoom%2Fnylas-php?ref=badge_large)