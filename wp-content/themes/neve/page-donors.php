<?php
/* Template Name: Donors */
get_header();
?>

<main id="main" class="site-main">
  <div class="container">
    <h1>Пожертвования НКО</h1>
    <p>Выберите организацию и сделайте пожертвование. Наш сервис берет 3% комиссии.</p>
    
    <!-- Подключение кошелька -->
    <div style="background:#f8f9fa;padding:20px;border-radius:8px;margin-bottom:20px;">
      <h3>Подключение кошелька</h3>
      <button id="connect-metamask" style="background:#f6851b;color:#fff;padding:12px 24px;border:none;border-radius:8px;font-size:16px;cursor:pointer;margin-right:10px;">
        Подключить Кошелек
      </button>
      <button id="disconnect-metamask" style="background:#dc3545;color:#fff;padding:12px 24px;border:none;border-radius:8px;font-size:16px;cursor:pointer;">
        Отключить кошелек
      </button>
      <div id="metamask-result" style="margin-top:15px;"></div>
    </div>

    <!-- Поиск организаций -->
    <div style="margin-bottom:20px;">
      <h3>Поиск организаций</h3>
      <input type="text" id="search-ngo" placeholder="Введите название организации..." style="width:300px;padding:10px;border:1px solid #ddd;border-radius:4px;font-size:16px;">
    </div>

    <!-- Таблица НКО -->
    <div style="background:#fff;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
      <div style="background:#f8f9fa;padding:15px;border-bottom:1px solid #ddd;">
        <h3 style="margin:0;">Список организаций</h3>
      </div>
      <div id="ngo-table-container">
        <table id="ngo-table" style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="background:#f8f9fa;">
              <th style="padding:12px;text-align:left;border-bottom:1px solid #ddd;">Название организации</th>
              <th style="padding:12px;text-align:left;border-bottom:1px solid #ddd;">Адрес кошелька</th>
              <th style="padding:12px;text-align:left;border-bottom:1px solid #ddd;">Баланс организации</th>
              <th style="padding:12px;text-align:center;border-bottom:1px solid #ddd;">Действия</th>
            </tr>
          </thead>
          <tbody id="ngo-table-body">
            <!-- Данные будут загружены динамически -->
          </tbody>
        </table>
        <div id="loading-ngo" style="padding:20px;text-align:center;color:#666;">
          Загрузка организаций...
        </div>
        <div id="no-ngo" style="padding:20px;text-align:center;color:#666;display:none;">
          Организации не найдены
        </div>
      </div>
    </div>

    <!-- Таблица моих донатов -->
    <div id="my-donations-container" style="display:none;margin-top:40px;background:#fff;border:1px solid #ddd;border-radius:8px;overflow:hidden;">
      <div style="background:#f8f9fa;padding:15px;border-bottom:1px solid #ddd;">
        <h3 style="margin:0;">Мои донаты</h3>
      </div>
      <div id="my-donations-table-wrap">
        <table id="my-donations-table" style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="background:#f8f9fa;">
              <th style="padding:12px;text-align:left;border-bottom:1px solid #ddd;">Организация</th>
              <th style="padding:12px;text-align:left;border-bottom:1px solid #ddd;">Сумма (ETH)</th>
              <th style="padding:12px;text-align:left;border-bottom:1px solid #ddd;">Транзакция</th>
            </tr>
          </thead>
          <tbody id="my-donations-table-body">
            <!-- Данные будут загружены динамически -->
          </tbody>
        </table>
        <div id="my-donations-loading" style="padding:20px;text-align:center;color:#666;display:none;">Загрузка донатов...</div>
        <div id="my-donations-empty" style="padding:20px;text-align:center;color:#666;display:none;">Донатов не найдено</div>
      </div>
    </div>

    <!-- Модальное окно для пожертвования -->
    <div id="donation-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;">
      <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:30px;border-radius:8px;min-width:400px;max-width:500px;">
        <h3 id="modal-ngo-name">Пожертвование</h3>
        <p id="modal-ngo-address" style="color:#666;font-size:14px;"></p>
        
        <div style="margin:20px 0;">
          <label for="donation-amount" style="display:block;margin-bottom:5px;font-weight:bold;">Сумма пожертвования (ETH):</label>
          <input type="number" id="donation-amount" min="0.0001" step="0.0001" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;font-size:16px;">
          <small style="color:#666;">Минимальная сумма: 0.0001 ETH</small>
        </div>
        
        <div style="background:#e7f3ff;padding:15px;border-radius:4px;margin:15px 0;">
          <strong>Информация о комиссии:</strong><br>
          <small>Сумма пожертвования: <span id="donation-sum">0</span> ETH<br>
          Комиссия сервиса (3%): <span id="service-fee">0</span> ETH<br>
          <strong>Итого к отправке: <span id="total-amount">0</span> ETH</strong></small>
        </div>
        
        <div style="text-align:right;margin-top:20px;">
          <button id="cancel-donation" style="background:#6c757d;color:#fff;padding:10px 20px;border:none;border-radius:4px;margin-right:10px;cursor:pointer;">
            Отмена
          </button>
          <button id="confirm-donation" style="background:#28a745;color:#fff;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;">
            Подтвердить пожертвование
          </button>
        </div>
      </div>
    </div>

    <!-- Подключаем ethers.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/5.7.2/ethers.umd.min.js" type="text/javascript"></script>
    <script>
      let currentAddress = null;
      let provider = null;
      let signer = null;
      let allNGOs = []; // Все НКО
      let filteredNGOs = []; // Отфильтрованные НКО
      let isMetamaskConnected = false;
      
      // ABI для смарт-контракта
      const contractABI = [
        {"inputs":[{"internalType":"address","name":"admin","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},
        {"inputs":[{"internalType":"string","name":"name","type":"string"}],"name":"CharityOrganizationIsNotDefined","type":"error"},
        {"inputs":[{"internalType":"address","name":"owner","type":"address"}],"name":"OwnableInvalidOwner","type":"error"},
        {"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"OwnableUnauthorizedAccount","type":"error"},
        {"inputs":[],"name":"ZeroAddress","type":"error"},
        {"inputs":[],"name":"ZeroAmount","type":"error"},
        {"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"wallet","type":"address"},{"indexed":false,"internalType":"string","name":"name","type":"string"}],"name":"CharityRegistered","type":"event"},
        {"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"donor","type":"address"},{"indexed":true,"internalType":"address","name":"charity","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"},{"indexed":false,"internalType":"address","name":"token","type":"address"}],"name":"DonationReceived","type":"event"},
        {"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},
        {"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"charities","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},
        {"inputs":[{"internalType":"string","name":"name","type":"string"}],"name":"deleteOrganization","outputs":[],"stateMutability":"nonpayable","type":"function"},
        {"inputs":[{"internalType":"string","name":"organizationName","type":"string"}],"name":"donateETH","outputs":[],"stateMutability":"payable","type":"function"},
        {"inputs":[{"internalType":"string","name":"organizationName","type":"string"},{"internalType":"address","name":"addressToken","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"donateToken","outputs":[],"stateMutability":"nonpayable","type":"function"},
        {"inputs":[{"internalType":"string","name":"name","type":"string"}],"name":"isOrganizationRegistered","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},
        {"inputs":[{"internalType":"address","name":"addressCheck","type":"address"}],"name":"isZeroAddress","outputs":[],"stateMutability":"pure","type":"function"},
        {"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"isZeroAmount","outputs":[],"stateMutability":"pure","type":"function"},
        {"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},
        {"inputs":[{"internalType":"string","name":"name","type":"string"},{"internalType":"address","name":"wallet","type":"address"}],"name":"registerOrganization","outputs":[],"stateMutability":"nonpayable","type":"function"},
        {"inputs":[],"name":"renounceOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},
        {"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"}
      ];
      
      // Адрес смарт-контракта
      const contractAddress = "0xe0f7ebAFe0F8D31a1BE5EE685D9a0e30CA64307b";
      
      // Адрес сборщика комиссий
      const feeCollector = "0xB98BC23f1EdDb754d01DBc7B62B28039eC9A0cD9";
      
      // Известные организации из блокчейна
      const knownOrganizations = [
        {
          name: "Lighthouse Charity",
          address: "0x889A54728511DAC5f5399584Fd014Ef57e634894"
        },
        {
          name: "Green Tomorrow Foundation", 
          address: "0x2F1EE9B5CC1464Bb49a67c52C387493d79256230"
        },
        {
          name: "Little Miracles",
          address: "0x700114A467b43B094C84196A9FdCb4dfA90543ec"
        },
        {
          name: "Global Kindness Network",
          address: "0x4223E67a1DFdB6A2D0C299Ba5EA03a57d5865Be6"
        }
      ];

      // Тестовые НКО для демонстрации
      const testNGOs = [
        {
          name: "Тестовая НКО - Помощь детям",
          address: "0x1234567890123456789012345678901234567890",
          balance: "2.5"
        },
        {
          name: "Тестовая НКО - Экология",
          address: "0x2345678901234567890123456789012345678901",
          balance: "1.8"
        },
        {
          name: "Тестовая НКО - Медицина",
          address: "0x3456789012345678901234567890123456789012",
          balance: "3.2"
        }
      ];

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
          console.log('Получаем баланс для адреса:', address);
          const balance = await window.ethereum.request({
            method: 'eth_getBalance',
            params: [address, 'latest']
          });
          console.log('Баланс в wei:', balance);
          
          // Конвертируем из wei в ETH
          const balanceInEth = parseInt(balance, 16) / Math.pow(10, 18);
          console.log('Баланс в ETH:', balanceInEth);
          return balanceInEth.toFixed(4);
        } catch (error) {
          console.error('Ошибка получения баланса:', error);
          return 'Ошибка';
        }
      }

      // Функция инициализации провайдера (публичный или Metamask)
      function initProvider() {
        if (window.ethereum && isMetamaskConnected) {
          provider = new ethers.providers.Web3Provider(window.ethereum);
          signer = provider.getSigner();
        } else {
          // Публичный RPC Arbitrum
          provider = new ethers.providers.JsonRpcProvider('https://arb1.arbitrum.io/rpc');
          signer = null;
        }
      }

      // Функция загрузки НКО из смарт-контракта
      async function loadNGOsFromContract() {
        if (!provider) {
          console.log('Провайдер не подключен, возвращаем пустой массив');
          return [];
        }
        
        try {
          console.log('Создаем контракт с адресом:', contractAddress);
          const contract = new ethers.Contract(contractAddress, contractABI, provider);
          const ngos = [];
          
          console.log('Загрузка НКО из смарт-контракта...');
          console.log('Проверяем организации:', knownOrganizations);
          
          // Проверяем каждую известную организацию
          for (const org of knownOrganizations) {
            try {
              console.log(`Проверяем организацию: ${org.name}`);
              
              // Проверяем, зарегистрирована ли организация в контракте
              const isRegistered = await contract.isOrganizationRegistered(org.name);
              console.log(`Организация ${org.name} зарегистрирована:`, isRegistered);
              
              if (isRegistered) {
                // Получаем баланс организации
                const balance = await getBalance(org.address);
                console.log(`Баланс ${org.name}:`, balance);
                
                ngos.push({
                  name: org.name,
                  address: org.address,
                  balance: balance,
                  isFromBlockchain: true
                });
                
                console.log(`Организация ${org.name} найдена в блокчейне`);
              } else {
                console.log(`Организация ${org.name} НЕ найдена в блокчейне`);
              }
            } catch (error) {
              console.error(`Ошибка проверки организации ${org.name}:`, error);
            }
          }
          
          console.log(`Загружено ${ngos.length} организаций из блокчейна:`, ngos);
          return ngos;
        } catch (error) {
          console.error('Ошибка загрузки НКО из контракта:', error);
          return [];
        }
      }

      // Функция загрузки всех НКО (тестовые + из блокчейна), сортировка: реальные сверху
      async function loadAllNGOs() {
        initProvider();
        const contractNGOs = await loadNGOsFromContract();
        // Сортируем: сначала реальные, потом тестовые
        allNGOs = [...contractNGOs, ...testNGOs];
        filteredNGOs = [...allNGOs];
        renderNGOTable();
      }

      // Функция рендеринга таблицы НКО
      function renderNGOTable() {
        const tbody = document.getElementById('ngo-table-body');
        const loading = document.getElementById('loading-ngo');
        const noNGO = document.getElementById('no-ngo');
        
        loading.style.display = 'none';
        
        if (filteredNGOs.length === 0) {
          noNGO.style.display = 'block';
          tbody.innerHTML = '';
          return;
        }
        
        noNGO.style.display = 'none';
        
        tbody.innerHTML = filteredNGOs.map((ngo, index) => `
          <tr style="border-bottom:1px solid #eee;">
            <td style="padding:12px;">
              <strong>${ngo.name}</strong>
              ${ngo.name.includes('Тестовая') ? '<br><small style="color:#ff6b35;">⚠️ Тестовая организация (не из блокчейна)</small>' : ''}
              ${ngo.isFromBlockchain ? '<br><small style="color:#28a745;">✅ Организация из блокчейна</small>' : ''}
            </td>
            <td style="padding:12px;font-family:monospace;font-size:14px;">${ngo.address}</td>
            <td style="padding:12px;">${ngo.balance} ETH</td>
            <td style="padding:12px;text-align:center;">
              <button onclick="openDonationModal('${ngo.name}', '${ngo.address}')" style="background:#28a745;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;">
                Сделать пожертвование
              </button>
            </td>
          </tr>
        `).join('');
      }

      // Функция поиска НКО
      function searchNGOs(query) {
        if (!query.trim()) {
          filteredNGOs = [...allNGOs];
        } else {
          const lowerQuery = query.toLowerCase();
          filteredNGOs = allNGOs.filter(ngo => 
            ngo.name.toLowerCase().includes(lowerQuery)
          );
        }
        renderNGOTable();
      }

      // Функция открытия модального окна пожертвования
      function openDonationModal(ngoName, ngoAddress) {
        if (!currentAddress) {
          alert('Сначала подключите кошелек!');
          return;
        }
        
        document.getElementById('modal-ngo-name').textContent = `Пожертвование: ${ngoName}`;
        document.getElementById('modal-ngo-address').textContent = `Адрес: ${ngoAddress}`;
        document.getElementById('donation-amount').value = '';
        document.getElementById('donation-modal').style.display = 'block';
        
        // Обновляем расчеты при изменении суммы
        document.getElementById('donation-amount').oninput = updateDonationCalculation;
      }

      // Функция обновления расчетов пожертвования
      function updateDonationCalculation() {
        const amount = parseFloat(document.getElementById('donation-amount').value) || 0;
        const serviceFee = amount * 0.03; // 3% комиссия
        const total = amount + serviceFee;
        
        document.getElementById('donation-sum').textContent = amount.toFixed(3);
        document.getElementById('service-fee').textContent = serviceFee.toFixed(3);
        document.getElementById('total-amount').textContent = total.toFixed(3);
      }

      // Функция отправки пожертвования
      async function sendDonation(ngoName, ngoAddress, amount) {
        if (!provider || !signer) {
          throw new Error('Кошелек не подключен');
        }

        try {
          const contract = new ethers.Contract(contractAddress, contractABI, signer);
          
          // Конвертируем ETH в wei
          const amountInWei = ethers.utils.parseEther(amount.toString());
          
          // Отправляем пожертвование через смарт-контракт
          const tx = await contract.donateETH(ngoName, {
            value: amountInWei
          });
          
          console.log('Транзакция пожертвования отправлена:', tx.hash);
          await tx.wait();
          console.log('Пожертвование подтверждено!');
          
          return tx;
        } catch (error) {
          console.error('Ошибка пожертвования:', error);
          throw new Error('Ошибка отправки пожертвования: ' + error.message);
        }
      }

      // --- МОИ ДОНАТЫ ---
      // Функция загрузки донатов пользователя
      async function loadMyDonations() {
        if (!provider || !currentAddress) {
          document.getElementById('my-donations-container').style.display = 'none';
          return;
        }
        document.getElementById('my-donations-container').style.display = 'block';
        document.getElementById('my-donations-loading').style.display = 'block';
        document.getElementById('my-donations-empty').style.display = 'none';
        const tbody = document.getElementById('my-donations-table-body');
        tbody.innerHTML = '';
        try {
          const contract = new ethers.Contract(contractAddress, contractABI, provider);
          // Получаем события DonationReceived, где donor == currentAddress
          const filter = contract.filters.DonationReceived(currentAddress);
          // Можно ограничить поиск по блокам, если нужно ускорить (например, {fromBlock: 0, toBlock: 'latest'})
          const events = await contract.queryFilter(filter, 0, 'latest');
          if (!events.length) {
            document.getElementById('my-donations-loading').style.display = 'none';
            document.getElementById('my-donations-empty').style.display = 'block';
            return;
          }
          // Сопоставим адреса организаций с их названиями
          const addressToName = {};
          for (const org of knownOrganizations) {
            addressToName[org.address.toLowerCase()] = org.name;
          }
          // Формируем строки таблицы
          tbody.innerHTML = events.map(ev => {
            const orgAddr = ev.args.charity.toLowerCase();
            const orgName = addressToName[orgAddr] || orgAddr;
            const amountEth = ethers.utils.formatEther(ev.args.amount);
            const txHash = ev.transactionHash;
            return `<tr>
              <td style="padding:12px;">${orgName}</td>
              <td style="padding:12px;">${parseFloat(amountEth).toFixed(4)}</td>
              <td style="padding:12px;"><a href='https://arbiscan.io/tx/${txHash}' target='_blank' style='color:#007bff;'>Смотреть</a></td>
            </tr>`;
          }).join('');
          document.getElementById('my-donations-loading').style.display = 'none';
        } catch (error) {
          document.getElementById('my-donations-loading').style.display = 'none';
          document.getElementById('my-donations-empty').style.display = 'block';
          console.error('Ошибка загрузки донатов:', error);
        }
      }

      // Отключение кошелька
      async function disconnectWallet() {
        currentAddress = null;
        isMetamaskConnected = false;
        initProvider();
        signer = null;
        
        const resultDiv = document.getElementById('metamask-result');
        resultDiv.innerHTML = '✅ Кошелек отключен. Теперь можете подключить другой аккаунт.';
        
        // Обновляем текст кнопки
        document.getElementById('connect-metamask').textContent = 'Подключить Кошелек';
        // Перезагружаем список НКО (с публичным провайдером)
        await loadAllNGOs();
        document.getElementById('my-donations-container').style.display = 'none';
      }

      // Обработчик отключения
      document.getElementById('disconnect-metamask').onclick = disconnectWallet;

      // Обработчик подключения
      document.getElementById('connect-metamask').onclick = async function() {
        const resultDiv = document.getElementById('metamask-result');
        resultDiv.innerHTML = 'Подключение...';
        
        if (typeof window.ethereum !== 'undefined') {
          try {
            // Сначала переключаемся на Arbitrum
            await switchToArbitrum();
            
            // Запрашиваем аккаунты
            const accounts = await window.ethereum.request({ 
              method: 'eth_requestAccounts' 
            });
            
            const address = accounts[0];
            
            if (!address) {
              resultDiv.innerHTML = '❌ Не удалось получить адрес кошелька';
              return;
            }

            currentAddress = address;
            isMetamaskConnected = true;
            initProvider();
            
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
            
            // Загружаем НКО (с обновленным провайдером)
            await loadAllNGOs();
            // Загружаем мои донаты
            await loadMyDonations();
            
          } catch (err) {
            resultDiv.innerHTML = '❌ Ошибка: ' + err.message;
          }
        } else {
          resultDiv.innerHTML = '❌ Metamask не установлен!';
        }
      };

      // Обработчик поиска
      document.getElementById('search-ngo').addEventListener('input', function(e) {
        searchNGOs(e.target.value);
      });

      // Обработчик отмены пожертвования
      document.getElementById('cancel-donation').onclick = function() {
        document.getElementById('donation-modal').style.display = 'none';
      };

      // Обработчик подтверждения пожертвования
      document.getElementById('confirm-donation').onclick = async function() {
        const amount = parseFloat(document.getElementById('donation-amount').value);
        const ngoName = document.getElementById('modal-ngo-name').textContent.replace('Пожертвование: ', '');
        const ngoAddress = document.getElementById('modal-ngo-address').textContent.replace('Адрес: ', '');
        
        if (!amount || amount < 0.0001) {
          alert('Введите корректную сумму (минимум 0.0001 ETH)');
          return;
        }

        try {
          this.disabled = true;
          this.textContent = 'Отправка...';
          
          const tx = await sendDonation(ngoName, ngoAddress, amount);
          
          alert(`✅ Пожертвование успешно отправлено!\nТранзакция: ${tx.hash}`);
          
          // Закрываем модальное окно
          document.getElementById('donation-modal').style.display = 'none';
          
          // Обновляем список НКО
          await loadAllNGOs();
          await loadMyDonations(); // Обновляем мои донаты после отправки
          
        } catch (error) {
          alert('❌ Ошибка: ' + error.message);
        } finally {
          this.disabled = false;
          this.textContent = 'Подтвердить пожертвование';
        }
      };

      // Автоматическая загрузка НКО при загрузке страницы
      window.addEventListener('load', function() {
        isMetamaskConnected = false;
        initProvider();
        loadAllNGOs();
      });

      // Слушаем изменения аккаунтов в Metamask
      if (window.ethereum) {
        window.ethereum.on('accountsChanged', function (accounts) {
          if (accounts.length === 0) {
            disconnectWallet();
          } else {
            currentAddress = accounts[0];
            document.getElementById('connect-metamask').click();
          }
        });
      }
    </script>
  </div>
</main>

<?php get_footer(); ?> 