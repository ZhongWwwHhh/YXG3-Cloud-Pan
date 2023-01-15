<?php

// start session, with time control
function sessionStart(bool $reset = false)
{
    // lifetime should not change
    $lifeTime = 36000;

    // cookie configuration
    $sessionCookie = array('lifetime' => $lifeTime, 'domain' => '.yxg3.xyz', 'path' => '/', 'httponly' => true, 'samesite' => 'Lax');
    session_set_cookie_params($sessionCookie);
    session_cache_limiter('no-cache');

    session_start();

    $timeNow = time();

    if ($reset) {
        // focus reset session
        session_unset();
        $_SESSION['timefinish'] = $timeNow + 300;
        $_SESSION['valid'] = false;
        session_regenerate_id();
        $_SESSION['valid'] = true;
        $_SESSION['timeout'] = $timeNow + $lifeTime;
        unset($_SESSION['timefinish']);
        return true;
    } else {
        // normal start
        if (isset($_SESSION['valid'], $_SESSION['timeout'])) {
            // already have session
            if ($_SESSION['valid']) {
                // valid should be true
                if ($_SESSION['timeout'] < $timeNow) {
                    // old session is used, must reject
                    header('location:/');
                    session_destroy();
                    exit;
                } elseif ($_SESSION['timeout'] - $lifeTime * 0.9 < $timeNow) {
                    // session has been used for some time, so need be refresh
                    $_SESSION['timefinish'] = $timeNow + 300;
                    $_SESSION['valid'] = false;
                    session_regenerate_id();
                    $_SESSION['valid'] = true;
                    $_SESSION['timeout'] = $timeNow + $lifeTime;
                    unset($_SESSION['timefinish']);
                    return true;
                } else {
                    // session still has long life time, use it directly
                    return true;
                }
            } else {
                if ($_SESSION['timefinish'] >= $timeNow) {
                    // refresh within 5min, if the network is unstable, old session needs to be refreshed again
                    $_SESSION['timefinish'] -= 200;
                    $_SESSION['valid'] = false;
                    session_regenerate_id();
                    $_SESSION['valid'] = true;
                    $_SESSION['timeout'] = $timeNow + $lifeTime;
                    unset($_SESSION['timefinish']);
                    return true;
                } else {
                    // old session is used, must reject
                    header('location:/');
                    session_destroy();
                    exit;
                }
            }
        } else {
            // not have session
            session_unset();
            $_SESSION['timeout'] = $timeNow + $lifeTime;
            $_SESSION['valid'] = true;
            return true;
        }
    }
}
