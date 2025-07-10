# 🌐 WordPress Web3 Integration Project

Современный WordPress сайт с интеграцией Web3 технологий и поддержкой входа через Metamask на сети Arbitrum.

## 🚀 Описание проекта

Этот проект представляет собой WordPress сайт с расширенной функциональностью Web3:
- **Вход через Metamask** - пользователи могут авторизоваться через криптокошелек
- **Интеграция с Arbitrum** - автоматическое подключение к сети Arbitrum One
- **Взаимодействие со смарт-контрактом** - возможность отправки транзакций
- **Современный дизайн** - адаптивная тема с красивым интерфейсом

## 🛠 Технологии

- **WordPress 6.8.1** - основная CMS
- **PHP 8.2** - серверная часть
- **Web3.js/Ethers.js** - взаимодействие с блокчейном
- **Metamask** - криптокошелек
- **Arbitrum One** - L2 сеть Ethereum
- **Composer** - управление зависимостями
- **Neve Theme** - современная WordPress тема

## 📋 Требования

- PHP 8.2+
- MySQL/MariaDB
- Apache/Nginx
- Composer
- Расширения PHP: `gmp`, `bcmath`

## 🚀 Установка

### 1. Клонирование репозитория
```bash
git clone https://github.com/your-username/wordpress-web3-project.git
cd wordpress-web3-project
```

### 2. Установка зависимостей
```bash
composer install
```

### 3. Настройка базы данных
- Создайте базу данных MySQL
- Скопируйте `wp-config-sample.php` в `wp-config.php`
- Настройте параметры подключения к БД

### 4. Настройка веб-сервера
```bash
# Установка необходимых PHP расширений
sudo apt-get install php8.2-gmp php8.2-bcmath

# Настройка прав доступа
sudo chown -R www-data:www-data wp-content/uploads
sudo chmod -R 775 wp-content/uploads
```

### 5. Установка WordPress через WP-CLI
```bash
# Скачивание WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

# Установка WordPress
php wp-cli.phar core install --url=your-domain.com --title="Web3 Site" --admin_user=admin --admin_password=your-password --admin_email=your@email.com
```

### 6. Установка плагинов и тем
```bash
# Установка темы Neve
composer require wpackagist-theme/neve

# Установка Web3 Authentication плагина
composer require wpackagist-plugin/web3-authentication

# Установка русского языка
php wp-cli.phar language core install ru_RU
php wp-cli.phar language core activate ru_RU
```

## 🎯 Использование

### Web3 Тестовая страница
После установки перейдите на страницу: `/web3-test`

Функции:
- ✅ Подключение Metamask
- ✅ Автоматическое переключение на сеть Arbitrum
- ✅ Отображение адреса кошелька
- ✅ Взаимодействие со смарт-контрактом

### Смарт-контракт
- **Адрес**: `0x429f97aC898ed39e020D98492A75D8B0c0c7d2a9`
- **Сеть**: Arbitrum One
- **Тип**: ERC20 Token

## 📁 Структура проекта

```
wordpress-web3-project/
├── wp-content/
│   ├── themes/
│   │   └── neve/
│   │       └── page-web3test.php    # Кастомная страница Web3
│   └── plugins/
│       └── web3-authentication/     # Web3 плагин
├── wp-config.php                    # Конфигурация WordPress
├── composer.json                    # Зависимости Composer
└── README.md                       # Этот файл
```

## 🔧 Настройка

### Конфигурация Web3
1. Откройте файл `wp-content/themes/neve/page-web3test.php`
2. Измените адрес смарт-контракта при необходимости
3. Настройте ABI для взаимодействия с контрактом

### Настройка темы
1. В админке WordPress перейдите в "Внешний вид" → "Темы"
2. Активируйте тему Neve
3. Настройте меню и виджеты

## 🌐 Демо

- **Сайт**: http://88.218.66.21
- **Web3 Тест**: http://88.218.66.21/web3-test
- **Смарт-контракт**: https://arbiscan.io/address/0x429f97ac898ed39e020d98492a75d8b0c0c7d2a9

## 🤝 Вклад в проект

1. Форкните репозиторий
2. Создайте ветку для новой функции (`git checkout -b feature/amazing-feature`)
3. Зафиксируйте изменения (`git commit -m 'Add amazing feature'`)
4. Отправьте в ветку (`git push origin feature/amazing-feature`)
5. Откройте Pull Request

## 📄 Лицензия

Этот проект распространяется под лицензией MIT. См. файл `LICENSE` для получения дополнительной информации.

## 📞 Поддержка

Если у вас есть вопросы или проблемы:
- Создайте Issue в репозитории
- Обратитесь к документации WordPress
- Проверьте логи ошибок в `wp-content/debug.log`

---

⭐ **Не забудьте поставить звезду репозиторию, если проект оказался полезным!** 