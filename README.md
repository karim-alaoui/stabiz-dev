# Stabiz

### Overview

> Stabiz works as Tinder where it matches founders to entrepreneurs and vice versa.
> Japan has an aging population where the children of founders don't want to do business anymore, thus such companies
> goes to the Govt. By using Stabiz, founders would like to handle the operations over to
> entrepreneur(s) who share the similar vision.

### Tech stack

- Laravel 8
- PHP 8 <small>(heavily depend on it. Anything lesser would not work)</small>
- Redis for Caching
- PostgreSQL 13
- Stripe for payment (Laravel cashier)

## Notes and Structure

> Notes on different sections on certain things work. Knowing these help to understand how things are done.

### Libraries

Important libraries are the backbone of the application.

- Passport for handling API
- The entire permission is handled
  by [laravel-spatie](https://spatie.be/docs/laravel-permission/v3/basic-usage/basic-usage#breadcrumb)
  <small>(used for staff mostly, see below)</small>

### Notes on Users Table

Each user has an email and user type which is either `entrepreneur` or `founder`. Usually, in most applications the
email is kept unique. However, in this application, email is not unique, and it's the combination of `email`
and `user type` which is unique. So, it means if you had an email `user@example.com` registered as `founder`, then you
could use the same email to register as `entrepreneur`. However, once an email is registered for a `user type`, it can't
be used again.

### Notes on User of the application

This application has two types of users

1. the actual users who pay to use this app
2. the staff

They are saved in separate tables called `users` and `staff`. To handle this, there are two providers under `guard` key
in `config/auth.php` file

### Error handling

The error handling have been manipulated to some extent to make it easier for front end devs to understand error and to
make a better representation of error. Find the `Handler.php` to see the changes in error handling.

#### Validation error in Japanese

For example a normal validation error in English might look like this for `first_name`
`first name is required` Now in Japanese, it would look `first_name フィールドは必須です` which stands
for `first name is required`. However, the client doesn't want the `first name` word to be in English. They want the
complete sentence to be Japanese. For that when we have the `locale` set `ja`(_japanese_), we find the english word in
the sentence and translate that English. The translation for each such phrase would be found in `ja/phrases.php`. Check
out `Handler.php`
file for more on how it's actually done.

### Users

There are two users

1. users (*entrepreneurs* and *founders*)
2. Staff (The staff)

They both have different tables.
``Staff`` table for staff
``users`` table for users

### Staff permission handling

Each staff can either of two roles

- Super admin (Can do each and everything)
- Matchmaker <small>(Super admin can assign entrepreneurs and founders to these staff. Then the staff can link
  entrepreneurs to founders and vice versa. The staff have access to the data of those users)</small>

### Guards

The application has multiple guards. Kindly read the comments in `auth.php`

### Gate and Policy

superadmin can do anything. If you want something to have access to only super admin, then you can provide
`Gate::authorize('')`
You can read this article
[laravel superadmin](https://murze.be/when-to-use-gateafter-in-laravel)

In any policy, make sure to return null if the condition doesn't match Check existing policies for that

## Payment

We use Stripe for handling payment. Laravel Cashier is used for this.

### Flow of Subscription

#### Storing Payment method (card)

- Returns intent from the backend
- User puts in their card. Front end sends the card information with the intent (returned from the backend) to Stripe
  API to store the payment method (each card is a payment method)
- Stripe stores the card

### Subscribing

- Backend sends the list of payment methods
- front end sends a payment and plan details (like package name and plan be it monthly or whatever)
- Backend subscribes to the plan
    - If the user is not grace period, then create a new subscription
    - if on grace period, then resume the subscription

**Grace period means**
<br/>Example - User subscribes on 1st jan for a monthly plan, the plan is getting expired on 30th Jan since it's a
monthly plan. Now on 10th Jan if the user cancels the subscription the plan is still active because the user already
paid for a month, it's just that the user is not automatically billed anymore since the user cancelled subscription.
This is what grace period means. It means the plan is cancelled but completely not expired yet. If the user is in grace
period, just resume subscription. This will auto start the billing on 30th Jan and will continue.

### Setting locale

Check the `LanguageSwitcher` `middleware` to see how the `locale` is set. The locale is `en` by default. However, one
can supply the header
`lang`: `lang_code` and the locale will be set in that lang.

### `BaseController`

Every controller extends `BaseController` so that it can return the response in a certain standardized way. Check the
controller, and some controller extending it to see how the responses are returned.

### Abbreviations

Abbreviations that are used throughout the application.

- ja - Japanese
- en - English
- entr - Entrepreneur
- fdr - Founder
- pfd - Preferred
- apl - Application
- cust - Customer
- jpy - Japanese yen
- gte - Greater than equal

### OTP

Each OTP is valid for 5 mins. The otp is hashed before storing in the database so that the otp can never be recovered
just like password. If you apply for an otp, the otps that you requested in the past but didn't verify would be marked
as invalid.

### CI/CD pipeline

CI/CD pipeline is done using GitHub action. Currently workflows

1. Deploying for dev and staging server handled by `deployment_dev.yml`
2. Deploying to production server using `deployment_prod.yml`

### Action class

The application follows single action class method. What it means is that we keep an action in a class. To know more,
read this
[dry. action class method](https://medium.com/@remi_collin/keeping-your-laravel-applications-dry-with-single-action-classes-6a950ec54d1d)

**Note** - I've used static `execute` method for easy access. Kindly keep it that way.

**Note** - Sometimes to validate the data that comes into the `action` class, I've used `validator`. Have a look at
them. Check the `ActionValidationException` to see the validation is handled in the `action` class.
Check `UpdateEntrProfile` class to see how `validaiton` in `action` class works.

### Boolean value on PostgreSQL

There is a known issue on inserting `true` `false` using Laravel.
[error on inserting boolean](https://stackoverflow.com/questions/35159267/laravel-eloquent-postgresql-save-array-error-preg-replace-parameter-mismatc)
To avoid that, use the helper function called
`boolValue` which exists in `helper.php` file.

Edit - In `PHP 8.0.8` this problem was fixed. Make sure to use that version or above

### Testing

This application is heavily tested. Have a lot of test cases. Kindly try to do wide coverage while writing test cases.
`AppBaseTestCase` for all your testing. This base class has a lot of useful methods.

Make sure to create `.env.testing` for testing `env` and have a database for testing mentioned
in `config.database.testing`

Any user created during testing has default password as `password`

### Mail Template

Most of the mails are handled by the CMS using email Templates. Email templates are usually edited by the staff in the
CMS. In the email template, there would be tags which would be dynamically changed. For example, <br/>
`##first_name## has applied to you.` <br/>

Here `##first_name##` would change based on the user. This entire thing is set by
`ExtractTxt4mMailTemplate` class. Check the usage of this class to see how it's played out.
