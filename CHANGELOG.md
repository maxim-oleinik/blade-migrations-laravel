# Release Notes

## [0.2.1 (2019-06-09)](https://github.com/maxim-oleinik/blade-migrations-laravel/compare/0.2.0...0.2.1)

#### Added
- `migrate -t|test` - добавлен режим тестировавания Rollback при запуске миграции (UP-DOWN-UP)
- `reload` - новая команда для перезапуска выбранной миграции (DOWN-UP)
- `rollback -l|load-file` - "-l" shortcut для "--load-file"

#### Changed
- `status` - поменялась цветовая схема (зеленый для новых миграций)
