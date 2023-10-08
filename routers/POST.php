<?php
function route($urlData, $formData)
{
    $urlDataAmount = count($urlData);
    if ($urlDataAmount == 1) {
        //изменить запись с указанным id
        $id = $urlData['0'];
        $formData = validateUpdateFormData($formData);
        if ($formData) {
            //есть подходящие для обновления записи данные
            editNote($id, $formData);
        } else {
            //нет необходимых данных, отказ в изменении
            echo json_encode(array(
                'error' => 'not enough data to edit'
            ));
        }

    } elseif ($urlDataAmount == 0) {
        //добавить новую запись
        $formData = validateCreationFormData($formData);
        if ($formData) {
            //отправлены все нужные данные, можно добавлять
            addNewNote($formData);
        } else {
            //нет необходимых данных, отказ в создании
            echo json_encode(array(
                'error' => 'not enough data to create'
            ));
        }

    } else {
        //возвращаем ошибку
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
            'error' => 'Bad Request'
        ));
    }
}