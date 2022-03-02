<?php

use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$connection = ConnectionCreator::createConnection();
$studentRepository = new PdoStudentRepository($connection);

/** @var \Alura\Pdo\Domain\Model\Student $studentList**/
$studentList = $studentRepository->allStudents();


var_dump($studentList);