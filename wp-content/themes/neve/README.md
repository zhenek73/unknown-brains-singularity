# Unknown Brains: Singularity

Проект для хакатона на WordPress — демонстрация идей, связанных с сингулярностью и взаимодействием сознаний.

## 🚀 Описание

Веб-сайт, разработанный на WordPress, размещён на удалённом сервере под управлением Debian.  
Проект реализован с учётом кастомного дизайна и может быть расширен плагинами или интеграцией с Web3.

## 🌐 Демо

[Сайт на сервере](http://88.218.66.21)

## 📦 Технологии

- WordPress
- Apache 2.4
- PHP 8.2
- MariaDB 10.11
- Git + GitHub
- Debian 12 (Cloud.ru VPS)

## ⚙️ Установка

1. Клонируйте репозиторий:
   ```bash
   git clone git@github.com:zhenek73/unknown-brains-singularity.git
Разместите содержимое в директории веб-сервера:

bash
Копировать
Редактировать
/var/www/html
Настройте базу данных и файл wp-config.php:

Укажите имя БД, пользователя и пароль

Пример:

php
Копировать
Редактировать
define( 'DB_NAME', 'wordpress_db' );
define( 'DB_USER', 'wp_user' );
define( 'DB_PASSWORD', 'SuperSecretPassword!' );
define( 'DB_HOST', 'localhost' );
Убедитесь, что веб-сервер и база данных запущены:

bash
Копировать
Редактировать
sudo systemctl restart apache2
sudo systemctl restart mariadb
Перейдите в браузере по адресу:

cpp
Копировать
Редактировать
http://88.218.66.21
📄 Лицензия
MIT License

yaml
Копировать
Редактировать

---

### 📌 Что дальше:

1. Создай файл:

```bash
nano README.md
Вставь туда содержимое выше ⬆️

Сохрани и закрой (Ctrl+O, Enter, Ctrl+X)

Добавь, закоммить и запушь:

bash
Копировать
Редактировать
git add README.md
git commit -m "Add README with project description"
git push
Готово ✅