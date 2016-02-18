The [[Email]] modules will allow you to send email messages via various services providers.


### Choosing the email service
---

You can choose the Email service via the `using` method. The optional second parameter is dictionary of init paramenters to pass to the selected driver.

The default driver is **Native**, the a service that uses the PHP `mail` function.

```php
Email::using('native');
```

Init a service with parameters :

```php
Email::using('SMTP',[
  'host' => 'smtp.starkindustries.com',
  'port' => 25,
  'username' => 'tony',
  'password' => 'pepperpotts',
]);
```

### Sending an email
---

You can send an email with the chosen service via the `Email::send` method.

An associative array must be passed with the email definition.

```php
Email::send([
  'to'       =>  'info@shield.com',
  'from'     =>  'Tony <tony@starkindustries.com>',
  'subject'  =>  'About your proposal...',
  'message'  =>  '<b>NOT</b> interested.',
]);
```

### Email address formats
---

The `to` and `from` properties accepts one or an array of email addresses in these formats :

1. `user@host.com`
1. `<user@host.com>`
1. `Name Surname <user@host.com>`
