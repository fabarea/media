Installation
=============

Behat is an open source behavior driven development framework for PHP 5.3 & 5.4.
What’s behavior driven development you ask? It’s the idea that you start by writing
human-readable sentences that describe a feature of your application and how it should work.

http://docs.behat.org/

The simplest way to install Behat is through Composer. Copy the composer.json given as example in this directory
and run following command::

	# Copy composer.json at the root of the project.
	# This could look like something as below.
	cd /project/home/
	cp htdocs/typo3conf/ext/media/Tests/Resources/Behat/composer.json .

	# Download composer and install behat and its dependencies
	curl http://getcomposer.org/installer | php
    php composer.phar install

    # Test behat is working
    bin/behat --help

We can copy and configure the ``behat.yml`` file to give the framework default configuration::

	# Copy behat.yml at the root of the project.
	cp htdocs/typo3conf/ext/media/Tests/Resources/Behat/behat.example.yml behat.yml

	# Edit default configuration
    edit behat.yml

We also need to install `Selenium2 Server`_  which enables driving a full-fledged browser such a Firefox or Chrome::

	# Download from jar file from http://code.google.com/p/selenium/downloads/list

	# Launch the server in a different shell
	java -jar selenium-server-standalone-2.x.y.jar

Time to run some feature tests in the console::

	# Run all tests
	bin/behat

	# Run tests according to a tag.
	# Tags are put at the top of the scenario.
	# Check out a *.feature file for an example
	bin/behat --tags base

	# Run a particular test file
	bin/behat htdocs/typo3conf/ext/media/Tests/Feature/hello.feature

.. _Selenium2 Server: http://code.google.com/p/selenium/downloads/list

