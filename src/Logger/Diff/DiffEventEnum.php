<?php
namespace App\Logger\Diff;

interface DiffEventEnum
{
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
}
