<?php
/* Template Name: Blockchain Test */
get_header();
?>

<main id="main" class="site-main">
  <div class="container">
    <h1>Регистрация НКО</h1>
    <button id="connect-metamask" style="background:#f6851b;color:#fff;padding:12px 24px;border:none;border-radius:8px;font-size:18px;cursor:pointer;margin-right:10px;">
      Подключить Кошелек
    </button>
    <button id="disconnect-metamask" style="background:#dc3545;color:#fff;padding:12px 24px;border:none;border-radius:8px;font-size:18px;cursor:pointer;">
      Отключить кошелек
    </button>
    <button id="force-disconnect" style="background:#6c757d;color:#fff;padding:12px 24px;border:none;border-radius:8px;font-size:18px;cursor:pointer;margin-left:10px;">
      Принудительно отключить все
    </button>
    <div id="metamask-result" style="margin-top:20px;"></div>
    
    <!-- Форма регистрации НКО -->
    <div id="ngo-form" style="margin-top:30px;display:none;">
      <h2>Регистрация НКО</h2>
      <div id="owner-info" style="background:#e7f3ff;border:1px solid #b3d9ff;padding:10px;border-radius:4px;margin-bottom:15px;">
        <strong>ℹ️ Информация о правах:</strong><br>
        <small>Только владелец контракта может регистрировать организации</small>
      </div>
      <form id="register-ngo-form" style="max-width:500px;">
        <div style="margin-bottom:15px;">
          <label for="ngo-name" style="display:block;margin-bottom:5px;font-weight:bold;">Название НКО:</label>
          <input type="text" id="ngo-name" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;">
        </div>
        <div style="margin-bottom:15px;">
          <label for="ngo-address" style="display:block;margin-bottom:5px;font-weight:bold;">EVM адрес:</label>
          <input type="text" id="ngo-address" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;" placeholder="0x...">
        </div>
        <button type="submit" style="background:#28a745;color:#fff;padding:12px 24px;border:none;border-radius:8px;font-size:16px;cursor:pointer;">
          Зарегистрировать НКО
        </button>
      </form>
      <div id="registration-result" style="margin-top:15px;"></div>
    </div>

    <!-- Список зарегистрированных НКО -->
    <div id="ngo-list" style="margin-top:30px;display:none;">
      <h2>Зарегистрированные НКО</h2>
      <div id="ngo-items"></div>
    </div>

    <!-- Подключаем ethers.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/5.7.2/ethers.umd.min.js" type="text/javascript"></script>
    <script>
      // Проверяем загрузку ethers.js
      function checkEthersLoaded() {
        if (typeof ethers === 'undefined') {
          console.error('ethers.js не загружен');
          document.getElementById('metamask-result').innerHTML = '❌ Ошибка: библиотека ethers.js не загружена';
          return false;
        }
        return true;
      }
      
      // Проверяем через небольшую задержку
      setTimeout(checkEthersLoaded, 1000);
      
      let currentAddress = null;
      let provider = null;
      let signer = null;
      
      // ABI для смарт-контракта  
      const contractABI =[{"inputs":[{"internalType":"address","name":"admin","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"inputs":[{"internalType":"string","name":"name","type":"string"}],"name":"CharityOrganizationIsNotDefined","type":"error"},{"inputs":[{"internalType":"address","name":"owner","type":"address"}],"name":"OwnableInvalidOwner","type":"error"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"OwnableUnauthorizedAccount","type":"error"},{"inputs":[],"name":"ZeroAddress","type":"error"},{"inputs":[],"name":"ZeroAmount","type":"error"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"wallet","type":"address"},{"indexed":false,"internalType":"string","name":"name","type":"string"}],"name":"CharityRegistered","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"donor","type":"address"},{"indexed":true,"internalType":"address","name":"charity","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"},{"indexed":false,"internalType":"address","name":"token","type":"address"}],"name":"DonationReceived","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"charities","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"name","type":"string"}],"name":"deleteOrganization","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"organizationName","type":"string"}],"name":"donateETH","outputs":[],"stateMutability":"payable","type":"function"},{"inputs":[{"internalType":"string","name":"organizationName","type":"string"},{"internalType":"address","name":"addressToken","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"donateToken","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"name","type":"string"}],"name":"isOrganizationRegistered","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"addressCheck","type":"address"}],"name":"isZeroAddress","outputs":[],"stateMutability":"pure","type":"function"},{"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"isZeroAmount","outputs":[],"stateMutability":"pure","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"name","type":"string"},{"internalType":"address","name":"wallet","type":"address"}],"name":"registerOrganization","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"renounceOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"}];
      
      // Адрес смарт-контракта  
      //const contractAddress = "0x429f97aC898ed39e020D98492A75D8B0c0c7d2a9";
      const contractAddress = "0xe0f7ebAFe0F8D31a1BE5EE685D9a0e30CA64307b";
      
      // Адрес владельца контракта
      const contractOwner = "0xB98BC23f1EdDb754d01DBc7B62B28039eC9A0cD9";
      
      // Функция для получения курса ETH
      async function getEthPrice() {
        try {
          const response = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=usd,rub');
          const data = await response.json();
          return {
            usd: data.ethereum.usd,
            rub: data.ethereum.rub
          };
        } catch (error) {
          console.error('Ошибка получения курса ETH:', error);
          return { usd: 0, rub: 0 };
        }
      }

      // Функция для переключения на сеть Arbitrum
      async function switchToArbitrum() {
        if (window.ethereum) { 
          try {
            await window.ethereum.request({
              method: 'wallet_addEthereumChain',
              params: [{
                chainId: '0xa4b1', // 42161 в hex
                chainName: 'Arbitrum One',
                nativeCurrency: {
                  name: 'Ether',
                  symbol: 'ETH',
                  decimals: 18
                },
                rpcUrls: ['https://arb1.arbitrum.io/rpc'],
                blockExplorerUrls: ['https://arbiscan.io/']
              }]
            });
            return true;
          } catch (addError) {
            console.error('Ошибка добавления сети:', addError);
            return false;
          }
        }
        return false;
      }

      // Функция для получения баланса
      async function getBalance(address) {
        try {
          const balance = await window.ethereum.request({
            method: 'eth_getBalance',
            params: [address, 'latest']
          });
          // Конвертируем из wei в ETH
          const balanceInEth = parseInt(balance, 16) / Math.pow(10, 18);
          return balanceInEth.toFixed(4);
        } catch (error) {
          console.error('Ошибка получения баланса:', error);
          return 'Ошибка';
        }
      }

      // Функция отключения кошелька
      async function disconnectWallet() {
        currentAddress = null;
        provider = null;
        signer = null;
        
        const resultDiv = document.getElementById('metamask-result');
        resultDiv.innerHTML = '✅ Кошелек отключен. Теперь можете подключить другой аккаунт.';
        
        // Обновляем текст кнопки
        document.getElementById('connect-metamask').textContent = 'Подключить Metamask и Arbitrum';
        
        // Скрываем формы
        document.getElementById('ngo-form').style.display = 'none';
        document.getElementById('ngo-list').style.display = 'none';
      }

      // Обработчик отключения
      document.getElementById('disconnect-metamask').onclick = disconnectWallet;
      
      // Обработчик принудительного отключения
      document.getElementById('force-disconnect').onclick = async function() {
        console.log('Принудительное отключение всех кошельков...');
        
        // Очищаем все переменные
        currentAddress = null;
        provider = null;
        signer = null;
        
        // Очищаем интерфейс
        const resultDiv = document.getElementById('metamask-result');
        resultDiv.innerHTML = '🔄 Все кошельки отключены. Перезагрузите страницу для полной очистки.';
        
        // Скрываем формы
        document.getElementById('ngo-form').style.display = 'none';
        document.getElementById('ngo-list').style.display = 'none';
        
        // Обновляем кнопки
        document.getElementById('connect-metamask').textContent = 'Подключить Metamask и Arbitrum';
        
        // Пытаемся отключить от Metamask
        if (window.ethereum && window.ethereum.removeAllListeners) {
          try {
            window.ethereum.removeAllListeners();
            console.log('Слушатели Metamask удалены');
          } catch (error) {
            console.error('Ошибка удаления слушателей:', error);
          }
        }
        
        // Предлагаем перезагрузить страницу
        setTimeout(() => {
          if (confirm('Рекомендуется перезагрузить страницу для полной очистки. Перезагрузить сейчас?')) {
            window.location.reload();
          }
        }, 2000);
      };

      // Обработчик подключения
      document.getElementById('connect-metamask').onclick = async function() {
        const resultDiv = document.getElementById('metamask-result');
        resultDiv.innerHTML = 'Подключение...';
        
        // Проверяем загрузку ethers.js
        if (!checkEthersLoaded()) {
          resultDiv.innerHTML = '❌ Ошибка: библиотека ethers.js не загружена';
          return;
        }
        
        if (typeof window.ethereum !== 'undefined') {
          try {
            // Сначала переключаемся на Arbitrum
            await switchToArbitrum();
            
            // Запрашиваем аккаунты (это откроет окно выбора в Metamask)
            const accounts = await window.ethereum.request({ 
              method: 'eth_requestAccounts' 
            });
            
            const address = accounts[0];
            
            if (!address) {
              resultDiv.innerHTML = '❌ Не удалось получить адрес кошелька';
              return;
            }

            currentAddress = address;
            
            // Получаем баланс
            const balance = await getBalance(address);
            
            // Получаем курс ETH
            const ethPrice = await getEthPrice();
            
            // Рассчитываем стоимость в долларах и рублях
            const balanceNum = parseFloat(balance);
            const usdValue = (balanceNum * ethPrice.usd).toFixed(2);
            const rubValue = (balanceNum * ethPrice.rub).toFixed(2);
            
            // Формируем результат
            let result = '✅ <b>Metamask подключён!</b><br>';
            result += '📍 <b>Адрес:</b> ' + address + '<br>';
            result += '🌐 <b>Сеть:</b> Arbitrum One<br>';
            result += '💰 <b>Баланс:</b> ' + balance + ' ETH';
            if (ethPrice.usd > 0) {
              result += ' (~$' + usdValue + ' / ~₽' + rubValue + ')';
            }
            
            resultDiv.innerHTML = result;
            
            // Обновляем текст кнопки
            document.getElementById('connect-metamask').textContent = 'Переподключить кошелек';
            
            // Инициализируем ethers.js
            provider = new ethers.providers.Web3Provider(window.ethereum);
            signer = provider.getSigner();
            
            // Показываем форму регистрации НКО
            document.getElementById('ngo-form').style.display = 'block';
            document.getElementById('ngo-list').style.display = 'block';
            
            // Загружаем список НКО
            loadNGOs();
            
          } catch (err) {
            resultDiv.innerHTML = '❌ Ошибка: ' + err.message;
          }
        } else {
          resultDiv.innerHTML = '❌ Metamask не установлен!';
        }
      };

      // Функция автоматического подключения при загрузке страницы
      async function autoConnect() {
        if (typeof window.ethereum !== 'undefined') {
          try {
            console.log('Проверяем автоматическое подключение...');
            
            // Проверяем, есть ли уже подключенные аккаунты
            const accounts = await window.ethereum.request({ 
              method: 'eth_accounts' 
            });
            
            console.log('Найденные аккаунты:', accounts);
            
            if (accounts.length > 0) {
              const address = accounts[0];
              console.log('Автоматическое подключение к аккаунту:', address);
              
              // Проверяем, что это действительно Metamask
              if (window.ethereum.isMetaMask) {
                console.log('Подтверждено: это Metamask');
              } else {
                console.log('Внимание: это не Metamask!');
              }
              
              currentAddress = address;
              
              // Инициализируем ethers.js
              provider = new ethers.providers.Web3Provider(window.ethereum);
              signer = provider.getSigner();
              
              // Получаем баланс и информацию
              const balance = await getBalance(address);
              const ethPrice = await getEthPrice();
              
              const balanceNum = parseFloat(balance);
              const usdValue = (balanceNum * ethPrice.usd).toFixed(2);
              const rubValue = (balanceNum * ethPrice.rub).toFixed(2);
              
              // Обновляем интерфейс
              const resultDiv = document.getElementById('metamask-result');
              let result = '✅ <b>Metamask подключён!</b><br>';
              result += '📍 <b>Адрес:</b> ' + address + '<br>';
              result += '🌐 <b>Сеть:</b> Arbitrum One<br>';
              result += '💰 <b>Баланс:</b> ' + balance + ' ETH';
              if (ethPrice.usd > 0) {
                result += ' (~$' + usdValue + ' / ~₽' + rubValue + ')';
              }
              
              resultDiv.innerHTML = result;
              document.getElementById('connect-metamask').textContent = 'Переподключить кошелек';
              
              // Проверяем права доступа
              await checkAccessRights(address);
              
              // Показываем форму регистрации НКО
              document.getElementById('ngo-form').style.display = 'block';
              document.getElementById('ngo-list').style.display = 'block';
              
              // Загружаем список НКО
              loadNGOs();
            } else {
              console.log('Нет подключенных аккаунтов');
            }
          } catch (error) {
            console.error('Ошибка автоматического подключения:', error);
          }
        } else {
          console.log('Metamask не обнаружен');
        }
      }

      // Слушаем изменения аккаунтов в Metamask
      if (window.ethereum) {
        window.ethereum.on('accountsChanged', function (accounts) {
          if (accounts.length === 0) {
            // Пользователь отключил кошелек
            disconnectWallet();
          } else {
            // Пользователь сменил аккаунт
            currentAddress = accounts[0];
            // Автоматически обновляем информацию
            document.getElementById('connect-metamask').click();
          }
        });
        
        // Слушаем изменения сети
        window.ethereum.on('chainChanged', function (chainId) {
          console.log('Сеть изменилась на:', chainId);
          // Перезагружаем страницу при смене сети
          window.location.reload();
        });
      }
      
      // Автоматическое подключение при загрузке страницы
      window.addEventListener('load', function() {
        setTimeout(autoConnect, 1000); // Небольшая задержка для загрузки ethers.js
      });

      // Функция валидации EVM адреса
      function isValidAddress(address) {
        if (typeof ethers === 'undefined') {
          throw new Error('Библиотека ethers.js не загружена');
        }
        return ethers.utils.isAddress(address);
      }

      // Функция регистрации НКО
      async function registerNGO(name, address) {
        if (typeof ethers === 'undefined') {
          throw new Error('Библиотека ethers.js не загружена');
        }
        
        if (!provider || !signer) {
          throw new Error('Кошелек не подключен');
        }

        if (!isValidAddress(address)) {
          throw new Error('Неверный EVM адрес');
        }

        const contract = new ethers.Contract(contractAddress, contractABI, signer);
        
        try {
          // Проверим, что адрес не нулевой
          if (address === '0x0000000000000000000000000000000000000000') {
            throw new Error('Адрес не может быть нулевым');
          }
          
          // Проверим, не зарегистрирована ли уже организация
          // Используем try-catch для обработки ошибки CharityOrganizationIsNotDefined
          let isRegistered = false;
          try {
            isRegistered = await contract.isOrganizationRegistered(name);
          } catch (checkError) {
            // Если организация не найдена, isRegistered остается false
            console.log('Организация не найдена (это нормально для новой регистрации)');
          }
          
          if (isRegistered) {
            throw new Error('Организация с таким названием уже зарегистрирована');
          }
          
          console.log('Начинаем регистрацию организации:', name, 'с адресом:', address);
          
          // Получаем текущий аккаунт
          const accounts = await window.ethereum.request({ method: 'eth_accounts' });
          const currentAccount = accounts[0];
          console.log('Текущий аккаунт:', currentAccount);
          
          // Проверяем, является ли текущий аккаунт владельцем контракта
          const contract = new ethers.Contract(contractAddress, contractABI, provider);
          const owner = await contract.owner();
          console.log('Владелец контракта:', owner);
          console.log('Текущий аккаунт:', currentAccount);
          
          if (currentAccount.toLowerCase() !== owner.toLowerCase()) {
            throw new Error(`Только владелец контракта может регистрировать организации. Владелец: ${owner}, Ваш адрес: ${currentAccount}`);
          }
          
          console.log('✅ Права на регистрацию подтверждены');
          
          // Попробуем оценить газ
          console.log('Оцениваем газ для регистрации...');
          const gasEstimate = await contract.estimateGas.registerOrganization(name, address);
          console.log('Оценка газа:', gasEstimate.toString());
          
          // Выполняем транзакцию с явным указанием газа
          console.log('Отправляем транзакцию...');
          const tx = await contract.registerOrganization(name, address, {
            gasLimit: gasEstimate.mul(120).div(100) // +20% к оценке
          });
          
          console.log('Транзакция отправлена:', tx.hash);
          console.log('Ожидаем подтверждения...');
          await tx.wait();
          console.log('Транзакция подтверждена!');
          return tx;
        } catch (error) {
          console.error('Детали ошибки:', error);
          
          if (error.code === 'UNPREDICTABLE_GAS_LIMIT') {
            throw new Error('Транзакция отклонена смарт-контрактом. Возможно, у вас нет прав на регистрацию или организация уже существует.');
          } else if (error.message.includes('execution reverted')) {
            throw new Error('Транзакция отклонена. Проверьте права доступа и убедитесь, что организация не зарегистрирована.');
          } else if (error.errorName === 'CharityOrganizationIsNotDefined') {
            // Это нормально для новой организации, продолжаем регистрацию
            console.log('Организация не найдена, продолжаем регистрацию');
          } else {
            throw new Error('Ошибка регистрации: ' + error.message);
          }
        }
      }

      // Функция загрузки списка НКО
      async function loadNGOs() {
        if (typeof ethers === 'undefined') {
          console.error('Библиотека ethers.js не загружена');
          return;
        }
        
        if (!provider) return;

        try {
          const contract = new ethers.Contract(contractAddress, contractABI, provider);
          
          // Поскольку в контракте нет функции для получения всех НКО,
          // показываем сообщение о том, что нужно использовать другой способ
          const ngoItems = document.getElementById('ngo-items');
          ngoItems.innerHTML = '<p>Для просмотра списка НКО используйте функцию проверки регистрации</p>';
          
          // Можно добавить форму для проверки конкретной организации
          ngoItems.innerHTML += `
            <div style="margin-top:15px;">
              <h3>Проверить регистрацию организации</h3>
              <input type="text" id="check-org-name" placeholder="Название организации" style="width:200px;padding:8px;margin-right:10px;">
              <button onclick="checkOrganization()" style="background:#007bff;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;">Проверить</button>
              <div id="check-result" style="margin-top:10px;"></div>
            </div>
          `;
        } catch (error) {
          console.error('Ошибка загрузки НКО:', error);
          document.getElementById('ngo-items').innerHTML = '<p>Ошибка загрузки списка НКО</p>';
        }
      }

      // Функция удаления НКО
      async function removeNGO(name) {
        if (typeof ethers === 'undefined') {
          alert('Библиотека ethers.js не загружена');
          return;
        }
        
        if (!provider || !signer) {
          alert('Кошелек не подключен');
          return;
        }

        if (!confirm('Вы уверены, что хотите удалить организацию "' + name + '"?')) {
          return;
        }

        try {
          const contract = new ethers.Contract(contractAddress, contractABI, signer);
          const tx = await contract.deleteOrganization(name);
          await tx.wait();
          alert('Организация успешно удалена!');
          loadNGOs(); // Перезагружаем список
        } catch (error) {
          alert('Ошибка удаления: ' + error.message);
        }
      }

      // Функция проверки прав доступа
      async function checkAccessRights(address) {
        try {
          const contract = new ethers.Contract(contractAddress, contractABI, provider);
          const owner = await contract.owner();
          
          const ownerInfo = document.getElementById('owner-info');
          if (address.toLowerCase() === owner.toLowerCase()) {
            ownerInfo.innerHTML = `
              <div style="background:#d4edda;border:1px solid #c3e6cb;padding:10px;border-radius:4px;">
                <strong>✅ Вы являетесь владельцем контракта!</strong><br>
                <small>Вы можете регистрировать и удалять организации</small>
              </div>
            `;
          } else {
            ownerInfo.innerHTML = `
              <div style="background:#f8d7da;border:1px solid #f5c6cb;padding:10px;border-radius:4px;">
                <strong>❌ У вас нет прав на регистрацию</strong><br>
                <small>Владелец контракта: ${owner}<br>Ваш адрес: ${address}</small>
              </div>
            `;
          }
        } catch (error) {
          console.error('Ошибка проверки прав доступа:', error);
          const ownerInfo = document.getElementById('owner-info');
          ownerInfo.innerHTML = `
            <div style="background:#fff3cd;border:1px solid #ffeaa7;padding:10px;border-radius:4px;">
              <strong>⚠️ Не удалось проверить права доступа</strong><br>
              <small>Ошибка: ${error.message}</small>
            </div>
          `;
        }
      }

      // Функция проверки регистрации организации
      async function checkOrganization() {
        if (typeof ethers === 'undefined') {
          alert('Библиотека ethers.js не загружена');
          return;
        }
        
        if (!provider) {
          alert('Кошелек не подключен');
          return;
        }

        const name = document.getElementById('check-org-name').value.trim();
        if (!name) {
          alert('Введите название организации');
          return;
        }

        try {
          const contract = new ethers.Contract(contractAddress, contractABI, provider);
          
          // Проверяем регистрацию организации
          let isRegistered = false;
          let address = '0x0000000000000000000000000000000000000000';
          
          try {
            isRegistered = await contract.isOrganizationRegistered(name);
            if (isRegistered) {
              address = await contract.charities(name);
            }
          } catch (checkError) {
            // Если организация не найдена, это нормально
            console.log('Организация не найдена:', checkError.message);
          }
          
          const resultDiv = document.getElementById('check-result');
          if (isRegistered && address !== '0x0000000000000000000000000000000000000000') {
            resultDiv.innerHTML = `
              <div style="background:#d4edda;border:1px solid #c3e6cb;padding:10px;border-radius:4px;margin-top:10px;">
                ✅ <strong>Организация "${name}" зарегистрирована</strong><br>
                <small>Адрес: ${address}</small>
                <button onclick="removeNGO('${name}')" style="background:#dc3545;color:#fff;padding:5px 10px;border:none;border-radius:4px;margin-left:10px;cursor:pointer;">Удалить</button>
              </div>
            `;
          } else {
            resultDiv.innerHTML = `
              <div style="background:#f8d7da;border:1px solid #f5c6cb;padding:10px;border-radius:4px;margin-top:10px;">
                ❌ <strong>Организация "${name}" не зарегистрирована</strong>
              </div>
            `;
          }
        } catch (error) {
          document.getElementById('check-result').innerHTML = `
            <div style="background:#f8d7da;border:1px solid #f5c6cb;padding:10px;border-radius:4px;margin-top:10px;">
              ❌ Ошибка проверки: ${error.message}
            </div>
          `;
        }
      }

      // Обработчик формы регистрации НКО
      document.getElementById('register-ngo-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('ngo-name').value.trim();
        const address = document.getElementById('ngo-address').value.trim();
        const resultDiv = document.getElementById('registration-result');
        
        if (!name || !address) {
          resultDiv.innerHTML = '❌ Заполните все поля';
          return;
        }

        if (!isValidAddress(address)) {
          resultDiv.innerHTML = '❌ Неверный EVM адрес';
          return;
        }

        resultDiv.innerHTML = '⏳ Регистрация НКО...';
        
        try {
          const tx = await registerNGO(name, address);
          resultDiv.innerHTML = `✅ НКО "${name}" успешно зарегистрировано!<br>Транзакция: ${tx.hash}`;
          
          // Очищаем форму
          document.getElementById('ngo-name').value = '';
          document.getElementById('ngo-address').value = '';
          
          // Перезагружаем список
          loadNGOs();
        } catch (error) {
          resultDiv.innerHTML = '❌ Ошибка: ' + error.message;
        }
      });
    </script>
  </div>
</main>

<?php get_footer(); ?>