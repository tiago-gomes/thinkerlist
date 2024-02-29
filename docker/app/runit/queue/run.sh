#!/bin/sh

exec php artisan queue:listen
