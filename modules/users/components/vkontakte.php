<?php
/**
 * Этот файл является частью программы "CRM Руководитель" - конструктор CRM систем для бизнеса
 * https://www.rukovoditel.net.ru/
 * 
 * CRM Руководитель - это свободное программное обеспечение, 
 * распространяемое на условиях GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * 
 * Автор и правообладатель программы: Харчишина Ольга Александровна (RU), Харчишин Сергей Васильевич (RU).
 * Государственная регистрация программы для ЭВМ: 2023664624
 * https://fips.ru/EGD/3b18c104-1db7-4f2d-83fb-2d38e1474ca3
 */
?>

<div style="margin-bottom: 5px;">
  <script nonce="csp_nonce" src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
  <script nonce="csp_nonce" type="text/javascript">
    //if (VKIDSDK in window) 
    {        
      const VKID = window.VKIDSDK;

      VKID.Config.init({
        app: '<?= CFG_VKONTAKTE_APP_ID ?>',
        redirectUrl: '<?= url_for_file('index.php') ?>',
        responseMode: VKID.ConfigResponseMode.Callback,
        source: VKID.ConfigSource.LOWCODE,
        scope: 'email', // Заполните нужными доступами по необходимости
      });

      const oneTap = new VKID.OneTap();

      oneTap.render({
        container: document.currentScript.parentElement,
        fastAuthEnabled: false,
        showAlternativeLogin: true,
        styles: {
          height: 37
        }
      })
      .on(VKID.WidgetEvents.ERROR, vkidOnError)
      .on(VKID.OneTapInternalEvents.LOGIN_SUCCESS, function (payload) {
        const code = payload.code;
        const deviceId = payload.device_id;

        VKID.Auth.exchangeCode(code, deviceId)
          .then(vkidOnSuccess)
          .catch(vkidOnError);
      });
    
      function vkidOnSuccess(data) {
        // Обработка полученного результата
        //console.log(data)
        
        if(data.access_token)
        {            
            location.href = '<?= url_for('social_login/vkontakte') ?>'+"&access_token="+data.access_token;
        }
      }
    
      function vkidOnError(error) {
        // Обработка ошибки
        console.log(error)
      }
    }
  </script>
</div>  
