<p align="right">
    <a href="https://github.com/laravel-shift/real-time-factories/actions"><img src="https://github.com/laravel-shift/real-time-factories/workflows/Build/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/laravel-shift/real-time-factories"><img src="https://poser.pugx.org/laravel-shift/real-time-factories/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://github.com/badges/poser/blob/main/LICENSE"><img src="https://poser.pugx.org/laravel-shift/real-time-factories/license.svg" alt="License"></a>
</p>

_Real-Time Factories_ is an open-source package for **dynamically generating** model factories in your Laravel applications aimed at lessening the barrier to testing and factory maintenance.

## Requirements
Blueprint requires a Laravel application running a [supported version of Laravel](https://laravel.com/docs/releases#support-policy), currently that is Laravel 10.x or higher.


## Installation
You may install this package via Composer using the following command:

```sh
composer require -W --dev laravel-shift/real-time-factories
```

This package will automatically register itself using [package discovery](https://laravel.com/docs/packages#package-discovery).

## Usage
Once installed, simply add the `RealTimeFactories` trait to any model you want real-time factories generated for you.

## Support Policy
This package supports the latest stable version of Laravel (currently Laravel 11), as well as any version still receiving support (currently Laravel 10).
