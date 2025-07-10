<?php
   /* Template Name: Web3 Test */
   get_header();
   ?>

   <main id="main" class="site-main">
     <div class="container">
       <h1>Web3 Test</h1>
       <button id="connect-metamask" style="background:#f6851b;color:#fff;padding:12px 24px;border:none;border-radius:8px;font-size:18px;cursor:pointer;">
         Подключить Metamask
       </button>
       <div id="metamask-result" style="margin-top:20px;"></div>
       <script>
         document.getElementById('connect-metamask').onclick = async function() {
           const resultDiv = document.getElementById('metamask-result');
           if (typeof window.ethereum !== 'undefined') {
             try {
               const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
               resultDiv.innerHTML = 'Metamask подключён! Ваш адрес: <b>' + accounts[0] + '</b>';
             } catch (err) {
               resultDiv.innerHTML = 'Ошибка: ' + err.message;
             }
           } else {
             resultDiv.innerHTML = 'Metamask не установлен!';
           }
         };
       </script>
     </div>
   </main>

   <?php get_footer(); ?>