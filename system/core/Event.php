<?php namespace core;

/**
 * Класс событий. Позволяет установить событие и выполнить его.
 *
 * Class Event
 * @package core
 */
class Event extends Collection
{
    protected static $instance;
    protected static $events = [];
    protected static $params = [];

    /**
     * Фабрика
     * @return Event
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Функция регистрирует событие на тригер. (хук)
     * @param $event
     * @param $callable
     */
    public static function on($event, $callable)
    {
        if (!is_callable($callable)) return;

        if (!array_key_exists($event, self::$events)) {
            self::$events[$event] = [];
        }

        self::$events[$event][] = $callable;


    }

    /**
     *  Функция которая вызывает тригер. Точнее активирует тригер, который в свою очередь запускает зарегистрированный ранее метод.
     *  По сути это хуки.
     * @param $event
     */
    public static function trigger($event, $params = false)
    {
        if (array_key_exists($event, self::$events)) {
            if ($params) {
                $args = $params;
            } else {
                $args = func_get_args();
                array_shift($args);
            }

            foreach (self::$events[$event] as $callable) {
                if (is_array($args)) {
                    call_user_func_array($callable, [$args]);
                } else {
                    call_user_func($callable, $args);
                }
            }
        }
    }
} 