<?php

class ImportView {
    public function showImportResult($success, $elapsedTime) {
        if ($success) {
            echo "Данные успешно импортированы. Время добавления данных: $elapsedTime секунд.";
        } else {
            echo "Ошибка при импорте данных.";
        }
    }
}
