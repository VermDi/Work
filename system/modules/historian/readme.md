Базоый модуль для сохранения истории изменений в разделах.

Пример использования класса:

####Сохранения в историю (данный кусочек удобно использовать в beforeUpdate модели):

    if (class_exists('modules\historian\helpers\History')) {
            History::set('pages', $this->id, self::getOne($this->id));
            /*         Ключ раздела, Ключ записи, объект или массив данных */
    }
    
#### Пример получения истории изменений:
    
     if (class_exists('modules\historian\helpers\History')) {
        History::getHistory('pages', $id));
        /*                  Ключ раздела, ключ записи                   */
     }
     
#### Пример отката по полученному значению:

    
    public function actionRallBack($id)
    {
        if (!class_exists('modules\historian\helpers\History')) {
            echo "NO HISTORIAN";
            return;
        }
        
        /* Сюда передается ID полученный из метода getHistory */
        
        $obj = History::getById($id);
        
        /* Проверим, что данные получили верно  */
        
        if ($obj->mod_key != 'pages') {
            throw  new \Exception('Не верно передан модуль');
        }
       
        /* Восстанавливаем данные, что было дано при сохранении то и вернется */
        
        $val = unserialize($obj->value);
        
        if (!is_object($val)) {
            throw new \Exception('Получен не объект, вероятно есть поломка');
        }
        
        /* --- Так как в объекте есть первичный ключ, это будет АПДЕЙТ -- */
        $this->model->save($val);     

    }
    
    