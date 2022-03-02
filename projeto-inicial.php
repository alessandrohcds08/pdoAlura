<?php

use Alura\Pdo\Domain\Model\Student;

require_once 'vendor/autoload.php';

$student = new Student(
    null,
    'Alessandro Henrique',
    new \DateTimeImmutable('1994-06-08')
);

echo $student->age();
