<?php

function showData($data): void
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function echoBR($data): void
{
    echo $data . '<br>';
}

// Получение данных из тела запроса
function getFormData($method) {

    // GET или POST: данные возвращаем как есть
    if ($method === 'GET') {
        return $_GET;
    }
    if ($method === 'POST') {
        return $_POST;
    }

    // PUT, PATCH или DELETE
    $data = array();
    $exploded = explode('&', file_get_contents('php://input'));
    showData($exploded);

    foreach($exploded as $pair) {
        $item = explode('=', $pair);
        if (count($item) == 2) {
            $data[urldecode($item[0])] = urldecode($item[1]);
        }
    }
    unset($data['url']);
    return $data;
}

function connectToDb(): PDO
{
    static $connection = null;

    if ($connection !== null) {
        return $connection;
    }
    $config = require_once(APP_DIR . '/src/dbConfig.php');

    $host_db = 'mysql:host=' . $config["hostname"] . ';dbname=' . $config["database"] . ';charset=' . $config["charset"];
    $connection = new PDO(
        $host_db,
        $config['username'],
        $config['password'],
    );
    return $connection;
}

function singleNoteInfo(int $id): ?array
{
    $connection = connectToDb();

    $query = $connection->prepare('select fullName, company, phone, email, birthDate, photo from notes where id = :id');
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    $group = $query->fetch(PDO::FETCH_ASSOC);
    if (empty($group)) {
        return null;
    } else {
        return $group;
    }
}

function multiNotesInfo(): ?array
{
    $connection = connectToDb();

    $query = $connection->prepare('select fullName, company, phone, email, birthDate, photo from notes');
    $query->execute();

    $groups = [];
    while($group = $query->fetch(PDO::FETCH_ASSOC)) {
        $groups[] = $group;
    }
    if (empty($groups)) {
        return null;
    } else {
        return $groups;
    }
}

function deleteNote(int $id): void
{
    $connection = connectToDb();

    $query = $connection->prepare('delete from notes where id = :id');
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
}

function validateCreationFormData(array $formData)
{
    if (isset($formData['fullName'], $formData['phone'], $formData['email']) && !empty($formData['fullName']) && !empty($formData['phone']) &&  !empty($formData['email'])) {
        $newFormData = [
            'fullName' => $formData['fullName'],
            'phone' => $formData['phone'],
            'email' => $formData['email']
        ];
        if (isset($formData['company']) && !empty($formData['company'])) {
            $newFormData['company'] = $formData['company'];
        }
        if (isset($formData['birthDate']) && !empty($formData['birthDate'])) {
            $newFormData['birthDate'] = $formData['birthDate'];
        }
        if (isset($formData['photo']) && !empty($formData['photo'])) {
            $newFormData['photo'] = $formData['photo'];
        }
        return $newFormData;
    }
    return false;
}

function addNewNote($formData)
{
    $connection = connectToDb();

    $setParams = [];
    foreach ($formData as $index => $element) {
        $setParams[] = $index . ' = "' . $element . '"';
    }
    $setParams = implode(', ', $setParams);
    $sqlRequest = 'insert into notes set ' . $setParams;

    $query = $connection->prepare($sqlRequest);
    $query->execute();
}

function validateUpdateFormData(array $formData)
{
    $params = ['fullName', 'company', 'phone', 'email', 'birthDate', 'photo'];
    $newFormData = [];
    foreach ($formData as $index => $value) {
        if (in_array($index, $params)) {
            $newFormData[$index] = $value;
        }
    }
    if (empty($newFormData)) {
        return false;
    } else {
        return $newFormData;
    }
}

function editNote(int $id, array $formData)
{
    $connection = connectToDb();

    $setParams = [];
    foreach ($formData as $index => $element) {
        $setParams[] = $index . ' = "' . $element . '"';
    }
    $setParams = implode(', ', $setParams);
    $sqlRequest = 'UPDATE notes set ' . $setParams . ' where id = :id';
    echoBR($sqlRequest);

    $query = $connection->prepare($sqlRequest);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
}