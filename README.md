# Julien-ITS

## emails-queue-s8

Emails queue bundle for **Symfony 8** (PHP ≥ 8.4, Doctrine ORM 3).

### Features

Service you can use to send your emails to a queue system. All your emails will be stored in your database to keep logs of them.
Send your emails directly or with a cron using the queue.
Define how many emails you want to send each time you call the process queue action.

### Installation

Before installation, add the Julien-ITS recipes endpoint in your `composer.json`:

```json
"extra": {
  "symfony": {
     "endpoint": [
        "https://api.github.com/repos/julien-its/symfony-recipes/contents/index.json",
        "flex://defaults"
     ]
  }
}
```

The `extra.symfony` key will most probably already exist in your `composer.json`. In that case, add the `endpoint` key to the existing `extra.symfony` entry.

You can now install it via composer:

```sh
composer require julien-its/emails-queue-s8
```

Without the Flex recipe, register the bundle manually in `config/bundles.php`:

```php
JulienIts\EmailsQueueBundle\EmailsQueueBundle::class => ['all' => true],
```

### Configuration

`config/packages/emails_queue.yaml`:

```yaml
emails_queue:
    mode: '%env(EMAILS_QUEUE_MODE)%'          # "prod" sends to the real recipients, anything else (default "dev") to debug_to / debug_cc
    debug_to: '%env(EMAILS_QUEUE_DEBUG_TO)%'  # optional
    debug_cc: '%env(EMAILS_QUEUE_DEBUG_CC)%'  # optional
```

`.env`:

```dotenv
EMAILS_QUEUE_MODE=dev
EMAILS_QUEUE_DEBUG_TO=me@example.com
EMAILS_QUEUE_DEBUG_CC=
```

The Doctrine mapping of the bundle entities is registered automatically (attribute mapping).

**Optional: cron route.** To process the queue over HTTP, import the bundle routes in `config/routes/emails_queue.yaml`:

```yaml
emails_queue:
    resource: '@EmailsQueueBundle/config/routes.yaml'
```

This exposes `/emails-queue/cron/process-mail-queue` (route `emails_queue_cron_process_mail_queue`). Protect it (firewall / access_control / IP restriction) since anybody calling it triggers the sending of the queue.

### Create the tables

```sh
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Usage

**Create your own email service** where you will define all your email methods. Example for a contact form:

```php
<?php

namespace App\Service;

use JulienIts\EmailsQueueBundle\Entity\EmailQueue;
use JulienIts\EmailsQueueBundle\Services\EmailService as JitsEmailService;

class EmailService
{
    private const DEFAULT_SUBJECT = 'My App';

    public function __construct(
        private readonly JitsEmailService $jitsEmailService,
    ) {
    }

    public function contact(array $message): void
    {
        $this->jitsEmailService->createNewAndProcess([
            'template' => '@EmailsQueue/mail/contact.html.twig',
            'templateVars' => ['message' => $message],
            'emailFrom' => 'from@from.com',
            'emailFromName' => 'My app',
            'contextName' => 'contact',
            'priority' => EmailQueue::HIGH_PRIORITY,
            'subject' => self::DEFAULT_SUBJECT.' : Contact',
            'emailTo' => 'toemail@to.com',
            'emailsBcc' => 'contact@from.com;email2@email.com',
        ]);
    }
}
```

Available config keys:

| Key | Required | Description |
| --- | --- | --- |
| `template` + `templateVars` | yes (or `emailHtml`) | Twig template for the HTML body |
| `emailHtml` | yes (or `template`) | Raw HTML body |
| `templateText` | no | Twig template for the text body (uses `templateVars`) |
| `contextName` | yes | Free label used to group emails (created if missing) |
| `emailFrom`, `emailFromName` | yes | Sender |
| `emailTo` | yes | Recipient |
| `subject` | yes | Subject |
| `priority` | no | `EmailQueue::HIGH_PRIORITY`, `NORMAL_PRIORITY` (default), `LOW_PRIORITY` |
| `replyTo` | no | Reply-to address |
| `emailsCc`, `emailsBcc` | no | Multiple addresses separated by `;` |

To customize the templates, override them in your app: `templates/bundles/EmailsQueueBundle/mail/contact.html.twig` and `templates/bundles/EmailsQueueBundle/layout/email.html.twig`.

**Two possibilities when creating an email:**

```php
$this->jitsEmailService->createNew($config);           // only queue it, returns the EmailQueue
$this->jitsEmailService->createNewAndProcess($config); // queue it and process the queue right away
```

### Send an email

Call your service in a controller:

```php
$emailService->contact([
    'name' => 'Julien Gustin',
    'phone' => '+320484010203',
    'message' => 'Hello!',
]);
```

### Process the queue with a cron

```sh
php bin/console jits:queue:process --limit 30
```

### Upgrading from emails-queue-s6

- Requires PHP ≥ 8.4, Symfony 8, Doctrine ORM 3.
- The database schema is unchanged (same tables and columns), no migration needed.
  ⚠️ On PostgreSQL, Doctrine ORM 3 maps `GeneratedValue(strategy: 'AUTO')` to `IDENTITY` instead of `SEQUENCE`: run `doctrine:migrations:diff` and review the generated migration.
- Template names: `EmailsQueueBundle:mail:contact.html.twig` → `@EmailsQueue/mail/contact.html.twig`.
- `EmailService::createNew()` now throws exceptions instead of `echo` + `die`, and returns the created `EmailQueue`.
- The cron route is no longer loaded automatically: import `@EmailsQueueBundle/config/routes.yaml` if you use it.
