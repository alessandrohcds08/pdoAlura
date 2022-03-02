<?php
require_once 'vendor/autoload.php';

use Alura\Pdo\Domain\Model\Student;

$pdo = \Alura\Pdo\Infrastructure\Persistence\ConnectionCreator::createConnection();

$student = new Student(null, 'Rosana Aparecida', new \DateTimeImmutable('1968-08-30'));

$sqlInsert = "INSERT INTO students (name,birth_date) VALUES (:name,:birth_date);";
$statement = $pdo->prepare($sqlInsert);
$statement->bindValue(':name',$student->name());
$statement->bindValue(':birth_date',$student->birthDate()->format('Y-m-d'));
$statement->execute();
if($statement->execute()){
    echo "Aluno incluido";
}