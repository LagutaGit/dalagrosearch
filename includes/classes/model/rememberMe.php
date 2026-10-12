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

namespace rk\auth;


class rememberMe
{
    private static function generate_tokens(): array
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));

        return [$selector, $validator, $selector . ':' . $validator];
    }
    
    private static function getCookieName()
    {               
        return 'user_auth_token';
    }
    
    private static function parse_token(): ?array
    {
        if(_cookieHas(self::getCookieName()))
        {
            $parts = explode(':', _cookie(self::getCookieName()));

            if ($parts && count($parts) == 2) 
            {
                return [filter_str($parts[0]), $parts[1]];
            }
        }
        
        return null;
    }
    
    private static function resetExpiredToken()
    {
        db_query("delete from app_auth_tokens where date_expiry<now()");
    }
    
    static function getTokenExpiryTime()
    {
        $auth_token_expiry_days = 30;
        
        return strtotime("+{$auth_token_expiry_days} day");
    }
    
    static function setAuthToken(int$user_id, bool $remember_me = false)
    {              
        if(!strlen(get_user_ip()) or !$remember_me) return;
                            
        [$selector, $validator, $token] = self::generate_tokens();
        
        $validator_hash = password_hash($validator, PASSWORD_DEFAULT);
        
        $expiry_time = self::getTokenExpiryTime();
        
        $sql_data = [
            'user_id' => $user_id,            
            'token' => $selector,            
            'token_hashed' => $validator_hash,            
            'user_ip' => get_user_ip(),
            'date_expiry' => _dateSql($expiry_time),            
        ];
        
        //insert token in db
        db_insert('app_auth_tokens', $sql_data);
        
        //insert token in cookie
        _cookieSet(self::getCookieName(), $token, $expiry_time);
                
        //reset expired tokens
        self::resetExpiredToken();
    }
    
    static function resetAuthToken($user_id)
    {       
        if($tokens = self::parse_token())
        {        
            db_query("delete from app_auth_tokens where token='" . db_input($tokens[0]) . "' and user_id=" . $user_id . "  and user_ip='" .  get_user_ip() ."'");

            _cookieUnset(self::getCookieName());
        }
        
        self::resetExpiredToken();
    }
    
    static function loginByToken()
    {                
        if(CFG_AUTO_LOGOUT_INACTION_USERS!=1 and $tokens = self::parse_token())
        {
            
            //check if token exist 
            $row_query = db_query("select id, user_id, token_hashed from app_auth_tokens where token='" . db_input($tokens[0]) . "' and user_ip='" .  get_user_ip() . "' and date_expiry>now()", false);
            if($row = db_fetch($row_query))
            {
                //check if user exist
                $user_query = db_query("select id from app_entity_1 where id={$row->user_id} and field_5=1");
                if($user = db_fetch($user_query) and password_verify($tokens[1], $row->token_hashed))
                {
                    //login user by ID
                    self::userLoginById($user->id);
                                                           
                    //update token date expiry
                    db_query("update app_auth_tokens set date_expiry='" . _dateSql(self::getTokenExpiryTime()) . "' where id=" . $row->id);
                    
                    //check if route has action and reidrect to home if ture
                    //we can't redirect to page with action 
                    if(_cookieHas('app_login_redirect_to'))
                    {          
                        $redirect_to = _cookie('app_login_redirect_to');
                        _cookieUnset('app_login_redirect_to');
                        
                        redirect_to(str_replace('module=', '', $redirect_to));
                    }                    
                    
                    return true;
                }
            }
        }
                        
        return false;
    }
        
    static private function userLoginById($user_id)
    {
        global $two_step_verification_info;
        
        $two_step_verification_info['is_checked'] = true;
        
        app_session_register('app_logged_users_id', $user_id);
    }
}
