<?php

interface MiddlewareInterface
{
    const BEFORE = 'before';
    const AFTER  = 'after';

    public static function direction(): string;

    public static function run();
}