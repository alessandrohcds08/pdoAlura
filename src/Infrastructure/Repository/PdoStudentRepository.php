<?php


namespace Alura\Pdo\Infrastructure\Repository;


use Alura\Pdo\Domain\Model\Phone;
use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Repository\StudentRepository;
use PDO;

class PdoStudentRepository implements StudentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function allStudents(): array
    {
        $statement = $this->connection->query('SELECT * FROM students;');

        return $this->hydrateStudentList($statement);
    }

    public function studentBirthAt(\DateTimeInterface $birthDate): array
    {
        $statement = $this->connection->query('SELECT * FROM students WHERE birth_date = ?;');
        $statement->bindValue(1, $birthDate->format('Y-m-d'));
        $statement->execute();
        return $this->hydrateStudentList($statement);
    }

    private function hydrateStudentList(\PDOStatement $stmt): array
    {
        $studentDataList = $stmt->fetchAll();
        $studentList = [];
        foreach ($studentDataList as $studentData) {
            $studentList[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new \DateTimeImmutable($studentData['birth_date'])
            );

        }

        return $studentList;
    }

    public function save(Student $student): bool
    {
        if ($student->id() === null) {
            return $this->insert($student);
        }

        return $this->update($student);
    }

    private function insert(Student $student): bool
    {
        $stmt = $this->connection->prepare("INSERT INTO students (name,birth_date) VALUES (:name,:birth_date);");

        $sucess = $stmt->execute([
            ':name' => $student->name(),
            ':birth_date' => $student->birthDate()->format('Y-m-d'),
        ]);

        if ($sucess) {
            $student->defineId($this->connection->lastInsertId());
        }

        return $sucess;
    }

    public function update(Student $student): bool
    {
        $stmt = $this->connection->prepare("UPDATE students SET name = :name, birth_date = :birth_date WHERE id = :id;");
        $stmt->bindValue(':name', $student->name());
        $stmt->bindValue(':birth_date', $student->birth_date()->format('Y-m-d'));
        $stmt->bindValue(':id', $student->id(), PDO::PARAM_INT);


        return $stmt->execute();
    }

    public function remove(Student $student): bool
    {
        $preparedStatement = $this->connection->prepare("DELETE FROM students WHERE id = ?;");
        $preparedStatement->bindValue(1, $student->id(), PDO::PARAM_INT);

        return $preparedStatement->execute();
    }

    public function studentsWithPhones():array
    {
        $stmt = $this->connection->query('SELECT students.id,
                                                           students.name,
                                                           students.birth_date,
                                                           phones.id AS phones_id, 
                                                           phones.area_code,
                                                           phones.number  
                                                    FROM students
                                                    JOIN phones ON students.id = phones.student_id');
        $result = $stmt->fetchAll();
        $studentList = [];

        foreach ($result as $row){
            if(!array_key_exists($row['id'],$studentList)){
                $studentList[$row['id']] = new Student(
                  $row['id'],
                  $row['name'],
                  new \DateTimeImmutable($row['birth_date'])
                );
            }

            $phone = new Phone($row['phones_id'],$row['area_code'],$row['number']);
            $studentList[$row['id']]->addPhones($phone);
        }
        return $studentList;
    }
}