# Xcoin Module ![GitHub](https://img.shields.io/github/license/Coinsence/coinsence-monorepo.svg) [![Build Status](https://travis-ci.org/Coinsence/humhub-modules-xcoin.svg?branch=master)](https://travis-ci.org/Coinsence/humhub-modules-xcoin) [![Coverage Status](https://coveralls.io/repos/github/Coinsence/humhub-modules-xcoin/badge.svg?branch=master)](https://coveralls.io/github/Coinsence/humhub-modules-xcoin?branch=master)


Xcoin is a collaborative currency system for coin creation and exchange.


# Table of content

- **[Overview](#Overview)**
- **[Development](#Development)**
	- **[Installation](#0)**
	- **[Testing](#1)**

# Overview 

From a global perspective Coinsence is basically based on **Xcoin Module** since it's the only module that offers accounting utilities where all the magic happens. 

---

**Spaces** within this module may have many accounts with different types : 

* One `DEFAULT` account 
* One `ISSUE` account 
* Many `STANDARD` accounts 
* Many `TASK` accounts 
* Many `INVESTOR` accounts 

Although **Users** can have only one single `DEFAULT` account .

---

This module provides all sorts of coins manipulation : 

* Coins issuing
* Coins transfer
* Coins exchange 

---

Besides it comes with other features such as : 

* Crowdfunding : Funding your project by raising targeted coin from a large number of users in return of your own coin.

* Marketplace : An open space where a market of products or services is held, and where payments are done through coins.

# Development 

### Installation 

Two ways are possible : 

- External Installation (recommended for development purpose) : 

	Clone the module outside your [Humhub](http://docs.humhub.org/admin-installation.html) root directory for example in a folder called `modules` : 

		 $ cd modules 
   		 $ git clone https://github.com/Coinsence/humhub-modules-xcoin.git

	Configure `Autoload` path by adding this small code block in the `humhub_root_direcotry/protected/config/common.php` file : 

		return [
          	'params' => [
            	'moduleAutoloadPaths' => ['/path/to/modules'],        
        	],
    	]


- Internal Installation (recommended for direct usage purpose) :

	Just clone the module directly under `humhub_root_direcotry/protected/humhub/modules` 
    
=> Either ways you need to enable the module through through *Browse online* tab in the *Administration menu* under modules section.

### Testing

Codeception framework is used for testing, you can check some of the implemented tests in `tests` folder.

* To simply run tests : 

		$ humhub_root_directory/protected/vendor/bin/codecept run  
    
* To run specific type of tests (`acceptance`, `unit` or `functional`) : 

	 	$ humhub_root_directory/protected/vendor/bin/codecept run unit  
    
* To extract `xml` or `html` output : 

		$ humhub_root_directory/protected/vendor/bin/codecept run unit --coverage-xml --coverage-html
