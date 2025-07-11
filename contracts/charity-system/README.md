# Charity Platform

Децентрализованная платформа для пожертвований в ETH и токенах ERC-20 зарегистрированным благотворительным организациям.
Проект построен на основе **Foundry** и использует контракты **OpenZeppelin** для обеспечения безопасности и надёжности.

---

##  Возможности

- Поддержка пожертвований в ETH и ERC-20 токенах
- Управление списком благотворительных организаций
- Гибкая система комиссий
- Использование стандартов OpenZeppelin Ownable и ReentrancyGuard
- Скрипты деплоя и тестирования



##  Установка и запуск

### 1.  Клонирование репозитория

```bash
git clone --recurse-submodules  https://github.com/zhenek73/unknown-brains-singularity.git
cd unknown-brains-singularity/contracts/charity-system
```


### 2.  Установка зависимостей (Forge)
```bash
forge install
```

### 3.  Компиляция контрактов
```bash
forge build
```

### 4.  Конфигурация .env файла
```bash
PRIVATE_KEY=

# RPC URLS
ARBITRUM_MAINNET_RPC_URL=

# Scanner tokens to verify
ARBITRUM_TOKEN=
```

### 5. Запуск тестов
```bash
forge test
```


### 6. Деплой контракта
```bash
forge script CharityPlatformDeploy --broadcast --verify
```