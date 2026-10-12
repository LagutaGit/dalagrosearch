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

if(!isset($_GET['access_token']))
{
    exit();
}

$params = [
    'client_id' => CFG_VKONTAKTE_APP_ID,
    'access_token' =>  $_GET['access_token'],
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://id.vk.ru/oauth2/user_info');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
$data = curl_exec($curl);
curl_close($curl);

if($data)
{            
    $data = json_decode($data);

    if(isset($data->error))
    {        
        $alerts->add($data->error . ' ' . $data->error_description,'error');
        redirect_to('users/login');
    }
    elseif(!isset($data->user->email))
    {        
        $alerts->add(TEXT_ERROR_USEREMAL_EMPTY,'error');
        redirect_to('users/login');
    }
    else
    {
        $social_login = new social_login();
        
        $social_login->set_user([
                    'first_name' => $data->user->first_name,
                    'last_name' => $data->user->last_name,
                    'photo' => $data->user->avatar,
                    'email' => $data->user->email
                ]);
            
        $social_login->login();
        
                   
    }                       
}
exit();
