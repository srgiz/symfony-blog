<?php
namespace App\Logger;

interface ObjDiffEventEnum
{
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
}
