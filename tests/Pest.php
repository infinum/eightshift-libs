<?php

uses()->group('integration')->in('Integration');
uses()->group('unit')->in('Unit');

uses(WP_UnitTestCase::class)->in('Integration');
