# Sunscreen [![Build Status](https://travis-ci.org/jenkoian/sunscreen.svg?branch=master)](https://travis-ci.org/jenkoian/sunscreen)

> Protecting you from the harmful rays of third party dependencies.

Re-inventing the wheel is often touted as a bad thing to do in software development. At the same time, relying too heavily
on third party dependencies is also touted as a bad thing. What a conundrum?!

The general advice around this seems to be, sure, use third party dependencies but use them responsibly. Wrap them in your
own interfaces so that if the dependency becomes out of date, defunct or not fit for purpose, you have your interface, so
you can just swap out the dependency or provide your own implementation. [Ports and adapters and all that](http://alistair.cockburn.us/Hexagonal+architecture).

Sunscreen aims to aid this process by automatically creating interfaces and adapters for supported dependencies. Mega!

One thing to be aware of, this aims to simply give you a leg up, get you in the habit of wrapping dependencies in your
own interfaces. It does this by copying pretty much verbatim the main interface/class of the third party dependency. This 
is probably fine for simple dependencies but falls down a little for those with long interfaces or classes with many methods.
The advice here would be to define your interface as being whatever *you* need and adapt the other interface to that.  In
fact that is desired usage for wrapping dependencies in this way. This library won't do that, it will just use the interface/class 
as is. Therefore, once this library has done it's thing you should definitely review and change/update the interface as per your needs.

![Sunscreen in action](sunscreen.gif)

## Installation

```
composer require jenko/sunscreen --dev
```

## How it works

The script will kick in after a package is installed via composer. It will check that package's composer file and look for 
the following:

```json
    "extra": {
        "sunscreen": {
            "interfaces": [
                "Acme\\Package\\MyPackageInterface"
            ]
        }
    }
```

Armed with this information about a package it will use this to generate interfaces/classes and an adapter from the configured
interfaces/classes.

If the configuration doesn't exist for the package it will attempt to load configuration from a pool of preconfigured json files.

If it still can't find any configuration it will attempt to guess the main interface by assuming a package has an interface named
`PackageNameInterface` or an abstract class `AbstractPackageName` in the main source directory. 
This is obviously quite unreliable though and the config is favoured.

## Sounds great, I author a package, what can I do?

As mentioned above, simply add the following bit of config to the `composer.json` file of your package.

```json
    "extra": {
        "sunscreen": {
            "interfaces": [
                "Acme\\Package\\MyPackageInterface"
            ]
        }
    }
```

Or if you don't have a main interface but a class, it would be:

```json
    "extra": {
        "sunscreen": {
            "classes": [
                "Acme\\Package\\MyPackage"
            ]
        }
    }
```

## Ok my favourite package isn't merging my PR, what now?

Create a PR to this repository with the preconfigured json for your dependency.
