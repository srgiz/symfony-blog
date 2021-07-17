<?php
namespace App\Logger\Diff;

interface DiffEvents
{
    const CREATE = 'Create';
    const UPDATE = 'Update';
    const DELETE = 'Delete';
}
