# Logic-as-Data for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/larawave/logic-as-data.svg?style=flat-square)](https://packagist.org/packages/larawave/logic-as-data)
[![Total Downloads](https://img.shields.io/packagist/dt/larawave/logic-as-data.svg?style=flat-square)](https://packagist.org/packages/larawave/logic-as-data)
[![License](https://img.shields.io/packagist/l/larawave/logic-as-data.svg?style=flat-square)](https://packagist.org/packages/larawave/logic-as-data)

## 🌟 Introduction

**Logic-as-Data** is a high-performance, database-driven rules engine designed specifically for Laravel applications. 

In modern applications — especially in **e-commerce marketing and loyalty programs** — business logic changes faster than code can be deployed. Hardcoding conditional logic (like "If user is Gold AND cart > $100") into your controllers creates technical debt and requires a developer for every minor campaign adjustment.

This package flips the script by allowing you to store complex business logic as **structured JSON data** in your database.

> **⚠️ Beta Version**  
> **This package is currently in Beta.** Expect frequent updates and potential breaking changes until we reach version 1.0.0. Please report any issues or bugs on the GitHub repository.


### Why Logic-as-Data?

* **Decouple Logic from Code:** Move your "if/else" blocks out of your codebase and into your database. Empower non-technical stakeholders to influence application behavior without a deployment.
* **Context-Aware Evaluation:** Pass any real-time data (Context) to the engine to evaluate against your stored rules (Predicates).
* **Total Observability:** Every time the engine fires, it leaves a "Telemetry" trail. You can see exactly why a rule passed or failed, how long it took to execute, and who triggered it.
* **Developer First:** Built with a modern Vue 3 + Tailwind CSS administrative interface, clean Laravel Facades, and a focus on DX (Developer Experience).

Whether you are building a **dynamic discount engine**, a **tiered loyalty system**, or a **complex notification router**, Logic-as-Data provides the infrastructure to make your application's logic as flexible as your data.

## ✨ Key Features

* **JSON-Based Predicates & Actions:** Define both your conditional logic (**Predicates**) and the resulting tasks (**Actions**) as structured JSON. This allows you to store entire business workflows in the database instead of hardcoding them in PHP.
* **Automated Action Execution:** The engine doesn't just evaluate; it acts. Whenever a predicate is successfully met, the package can automatically trigger your predefined actions—such as applying a discount, awarding loyalty points, or firing off a system event.
* **High-Fidelity Telemetry:** Stop guessing why a rule didn't fire. Every execution is logged with a detailed audit trail, including execution duration, the authenticated user (**Causer**), and the exact request/session IDs.
* **Deep Trace Inspection:** Visualize the "Logic Trace" of every event. Through the dashboard, you can see exactly which part of a complex predicate passed, failed, or was skipped during evaluation.
* **Ready-to-Use Admin Dashboard:** A fully responsive, modern UI built with **Vue 3** and **Tailwind CSS**. Monitor your engine's performance and debug logic flows in real-time through a dedicated interface that works out of the box.
* **Context & Subject Mapping:** Dynamically pass real-time data (**Context**) and evaluate it against specific database entities (**Source and Target**) for hyper-targeted loyalty and marketing logic.
* **Developer-First Integration:** Engineered for rapid implementation with a robust Laravel Facade, clear PSR-4 namespacing, and a focus on clean DX (Developer Experience).

## 📋 Requirements

To use **Logic-as-Data**, your Laravel application must meet the following requirements:

* **PHP:** `^8.3`
* **Laravel:** `^10.0`, `^11.0`, or `^12.0`
* **Composer:** `^2.0`

> **Note:** No frontend dependencies (Node.js/NPM) are required for the host application to use this package. All Admin UI assets are pre-compiled and can be published directly to your public directory.

## 🚀 Installation

The package is designed to be up and running in seconds using an automated installation command. Follow these steps to integrate **Logic-as-Data** into your Laravel application.

### 1. Install via Composer

Since the package is currently in Beta, ensure you specify the version or allow beta releases:

```bash
composer require larawave/logic-as-data:"^0.1.0-beta.1"
```

---

### 2. Run the Automated Installer

We provide a dedicated install command that handles publishing the configuration, migrations, frontend assets and a customizable App-level Service Provider.

```bash
php artisan logic-as-data:install
```

**During this process, the installer will:**
* Publish the `logic-as-data.php` configuration file.
* Copy the `LogicAsDataServiceProvider.php` to your `app/Providers` directory.
* Publish the database migrations.
* Publish the pre-compiled Vue/Tailwind Admin UI assets.
* **Prompt you** to run the migrations immediately.

---

### 3. Register the Service Provider

To ensure your custom source extractors, operators, target resolvers, evaluators, actions are loaded correctly, you must register the newly published Service Provider in your application.

#### For Laravel 11 & 12:
Add the provider to `bootstrap/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\LogicAsDataServiceProvider::class, // Add this line
];
```

#### For Laravel 10:
Add the provider to the `providers` array in `config/app.php`:

```php
'providers' => [
    // ... other providers
    App\Providers\LogicAsDataServiceProvider::class, // Add this line
],
```

---

### 4. Verify the Installation

Once installed and registered, you can access the Logic as Data Dashboard to verify that the installation is successful and assets are loading correctly:

* **URL:** `your-app.test/admin/logic-as-data`
* **Gate:** By default, access is restricted to the `local` environment. To customize access for production, modify the `viewLogicAsData` gate inside your `app/Providers/LogicAsDataServiceProvider.php`.


## 🔒 Access Control & Security

By default, the **Logic-as-Data** dashboard is strictly protected. The package defines a Laravel Gate named `viewLogicAsData` to prevent unauthorized access to your telemetry logs and logic rules in production.

### Default Behavior
In the core service provider, the access is restricted to the `local` environment only:
```php
Gate::define('viewLogicAsData', function () {
    return app()->environment('local');
});
```

---

### Customizing Access
To authorize specific users or roles in production, you should modify the Gate definition inside your **published** Service Provider located at:
`app/Providers/LogicAsDataServiceProvider.php`

Here are common real-world examples of how to restrict access:

#### 1. Restrict by Admin Email
If you want to give access only to specific team members:
```php
Gate::define('viewLogicAsData', function ($user) {
    return in_array($user->email, [
        'admin@yourdomain.com',
        'developer@yourdomain.com',
    ]);
});
```

#### 2. Restrict by Role (e.g., Spatie Permissions)
If your application uses a role/permission system, you can check for a specific capability:
```php
Gate::define('viewLogicAsData', function ($user) {
    // Allows access if the user has the 'view-admin-panels' permission
    return $user->can('view-admin-panels');
});
```

#### 3. Combine Environment & User ID
A robust approach that allows local development for everyone but restricts production to a specific Super Admin:
```php
Gate::define('viewLogicAsData', function ($user) {
    if (app()->environment('local')) {
        return true;
    }

    return $user && $user->id === 1; // Replace 1 with actual value
});
```

## ⚙️ Configuration

After running the installer, a configuration file will be published to `config/logic-as-data.php`. You can customize the behavior of the Logic as Data engine and the dashboard here.

Most settings are pre-configured to work with `.env` variables for easy environment management.

### 🏠 Dashboard & Routes
Customize how you access the Admin UI.

* **`route.prefix`**: The URL path for the dashboard. (Default: `admin/logic-as-data`).
* **`route.middleware`**: The middleware stack applied to the dashboard routes. By default, it uses `['web', 'auth']` to ensure only logged-in users can access it.

### 📊 Telemetry & Tracing
The telemetry system records every logic evaluation for debugging and auditing.

* **`telemetry.enabled`**: Set to `false` if you want to stop recording logs to the database (useful for extreme high-traffic environments).
* **`telemetry.strict`**: 
    * **True (Default for Local):** Any failure in the telemetry logging process (like a DB error) will throw an exception to help you debug.
    * **False (Recommended for Production):** Telemetry failures will be caught silently so they never crash a critical user request.

### ⚡ Performance (Caching)
Since rules are often evaluated on every request, the package includes an intelligent caching layer.

* **`cache.enabled`**: Highly recommended to keep this `true`. The cache is **automatically cleared** whenever you create, update, or delete a `LogicRule`.
* **`cache.ttl`**: How long the rules remain in cache (Default: `86400` seconds / 24 hours).

### 🗄️ Database Tables
If the default table names conflict with your existing database schema, you can rename them here before running the migrations:

* **`tables.rules`**: Stores your JSON conditions and actions (Default: `logic_rules`).
* **`tables.telemetry`**: Stores the high-level execution logs (Default: `logic_telemetry`).
* **`tables.traces`**: Stores the granular pass/fail details for every condition (Default: `logic_traces`).

---

### 🔑 Environment Variables
You can quickly configure the package by adding these keys to your `.env` file:

| Variable | Default | Description |
| :--- | :--- | :--- |
| `LOGIC_AS_DATA_PREFIX` | `admin/logic-as-data` | Dashboard URL path |
| `LOGIC_AS_DATA_TELEMETRY_ENABLED` | `true` | Toggle all logging |
| `LOGIC_AS_DATA_TELEMETRY_STRICT` | `auto` | Throw errors on telemetry fail |
| `LOGIC_AS_DATA_CACHE_ENABLED` | `true` | Toggle rule caching |
| `LOGIC_AS_DATA_DEV_MODE` | `false` | Enable Vite HMR for UI development |


## 🧠 Core Concepts

To effectively use **Logic-as-Data**, it helps to understand the vocabulary of the engine. The package revolves around evaluating a **Predicate** (a set of conditions) and executing **Actions** if those conditions are met.

### 1. Hooks & Context
When you want the engine to run, you dispatch an event to a specific **Hook** and pass it real-time **Context**.

* **Hook:** A string identifier where rules are attached (e.g., `cart.checkout` or `user.login`).
* **Context:** An array of real-time data passed from your application to the engine (e.g., the current `$user` model or `$cart` totals).

### 2. The Predicate (The Rule)
A Predicate is the actual JSON logic stored in the database. It is evaluated by the `PredicateEvaluator` and consists of two main parts:

* **Combinators:** Logical groupings (`AND` / `OR`). The engine supports infinite nesting of combinators and automatically handles short-circuiting for maximum performance.
* **Clauses:** The individual conditions being checked. 

Every single **Clause** is made up of three distinct parts: a Source, an Operator, and a Target.

#### A. Source (Extractors)
The **Source** is the "left hand side" of the equation. It defines what data we are looking at. 
Sources are resolved using **Extractors**. For example, the alias `user.email` uses the `LaraWave\LogicAsData\Extractors\UserExtractor` to pull the email address from the Context.

```json
"source": { 
    "alias": "user.email" 
}
```

#### B. Operators
The **Operator** is the comparison engine. It compares the extracted Source against the resolved Target. 
The package includes dozens of built-in operators mapped in the `LogicRegistry`, including:
* *Equality:* `equals`, `not_equals`
* *Relational:* `greater_than`, `less_than_or_equal`
* *String/Array:* `contains`, `starts_with`, `in_array`, `has_any_of`
* *State:* `is_empty`, `is_not_null`

```json
"operator": "ends_with"
```

#### C. Target (Resolvers)
The **Target** is the "right hand side" of the equation. It defines what we are comparing the Source against.
Targets use **Resolvers**. The most common resolver is `core.literal`, which allows you to hardcode a value (like checking if an email ends with `@example.com`). However, targets can also be dynamic, like checking if a user's setting matches a `system.config` value.

```json
"target": { 
    "alias": "core.literal", 
    "params": { "value": "@example.com", "value_type": "string" } 
}
```

### 3. Actions
While the engine can be used purely for boolean evaluation (returning `true` or `false`), it is also capable of **Execution**. 

If a Predicate successfully passes, the engine can execute an array of **Actions**. Actions are predefined PHP classes (like `LogMessageAction` or `LogoutAction`) that map to a specific alias. They accept parameters defined directly in your JSON.

```json
"actions": [
    {
        "alias": "system.log",
        "params": {
            "level": "info",
            "message": "Logic-As-Data: Rule triggered!"
        }
    }
]
```

### 🧩 Extending the Engine

While **Logic-as-Data** ships with few built-in source extractors, operators, target resolvers and actions, every application has unique business logic. You will inevitably need to create custom Extractors/Resolvers (e.g., to get a user's loyalty tier) or custom Actions (e.g., to apply a specific discount code).

To make this seamless, the package includes dedicated Artisan generators that scaffold these classes for you, complete with the required `metadata()` methods for the Admin UI.

#### Artisan Generators

Run any of the following commands in your terminal to generate a new component. By default, these classes are published to your `app/LogicAsData` directory.

```bash
# 1. Create a Source Extractor (Extracts real-time data from Context)
php artisan make:logic-extractor UserLoyaltyExtractor

# 2. Create an Operator (Defines a custom comparison rule)
php artisan make:logic-operator IsPrimeNumberOperator

# 3. Create a Target Resolver (Resolves dynamic targets)
php artisan make:logic-resolver DatabaseConfigResolver

# 4. Create an Action (Executes a side-effect when a rule passes)
php artisan make:logic-action ApplyDiscountAction
```

#### The Custom Component Workflow

Adding a custom component to your logic engine always follows three simple steps:

##### Step 1: Generate & Implement
Run the Artisan command and open the generated file (e.g., `app/Logic/Extractors/UserLoyaltyExtractor.php`). Implement your PHP logic in the primary method (like `extract()` or `execute()`).

##### Step 2: Configure UI Metadata
Inside the generated class, you will see a `metadata()` method. Update the `label`, `description`, and `fields` array. This ensures your custom component seamlessly appears in the Vue 3 Admin Dashboard with the correct input fields.

##### Step 3: Register the Component
For the Logic Engine to recognize your new component, you simply add it to the corresponding array in the **App-level Service Provider**. 

When you ran `php artisan logic-as-data:install`, the package published a dedicated provider to your application at **`app/Providers/LogicAsDataServiceProvider.php`**. 

Open this file and map your new classes to their JSON aliases:

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\LogicAsData\Extractors\UserLoyaltyExtractor;
use App\LogicAsData\Actions\ApplyDiscountAction;

class LogicAsDataServiceProvider extends ServiceProvider
{
    /**
     * Map source aliases to their Source Extractor classes.
     */
    private array $extractors = [
        'user.loyalty_tier' => UserLoyaltyExtractor::class,
    ];

    /**
     * Map action aliases to their Action classes.
     */
    private array $actions = [
        'cart.apply_discount' => ApplyDiscountAction::class,
    ];
    
    // ... operators, resolvers, and evaluators arrays
}
```

The Service Provider will automatically loop through these arrays during the boot cycle and register them with the underlying `LogicRegistry`. Once registered, your business team can immediately start using `"user.loyalty_tier"` and `"cart.apply_discount"` in their JSON rules via the Admin UI!
 


### The Two Execution Methods
Because of this architecture, the `LogicEngine` provides two distinct ways to interact with your rules:

1. **`LogicEngine::passes()`**: Evaluates the predicate. Returns a `boolean`, but does not execute actions. (Great for "Should I show this button?" checks).
2. **`LogicEngine::trigger()`**: Evaluates the predicate and, if it passes, **automatically executes** the attached Actions. (Great for automated workflows).
