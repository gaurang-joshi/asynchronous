<?php
/**
 * ## Asynchronous
 * This function will open socket and ask socket to initiate php call by given $url and parameters,
 * In that function we have functionality of our needs. It just initiate just url with parameters
 * and returns your process will complete soon so, it's work like asynchronous.
 *
 * User: Gaurang Joshi
 * Date: 31/05/2017
 * Time: 4:33 PM
 *
 * @throws Throwable
 * @author Gaurang Joshi <gaurangnil@gmail.com>
 */

namespace Asynchronous;

use Throwable;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * ### Asynchronous
 * Class Asynchronous
 * @package Asynchronous
 */
class Asynchronous {

    /**
     * ## Thread
     * Function to start php server url as in saperate thread
     *
     * @param string $url
     * @param string $params
     *
     * @return void
     * @throws Throwable
     */
    public static function thread($url, $params) {
        try {
            Self::write_log('info', 'ENTRY : thread :: Asynchronous.php');
            Self::write_log('debug', 'DEBUG : URL = ' . $url . ' PARAM = ' . json_encode($params));
            $post_string = http_build_query($params);
            $parts = parse_url($url);
            $err_no = 0;
            $err_str = "";
            /*
             * Checking is given $url for opening as thread is secure server or non-secure server
             */
            if (parse_url($url, PHP_URL_SCHEME) === 'https') {
                // Use SSL & port 443 for secure servers (For secure server)
                $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $err_no, $err_str, 30);
            } else {
                // Use otherwise for localhost and non-secure servers (For non-secure server)
                $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $err_no, $err_str, 30);
            }
            if (!$fp) {
                Self::write_log('critical', 'Woh! An Error occurs while creating socket connection.');
            }
            $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
            $out .= "Host: " . $parts['host'] . "\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "Content-Length: " . strlen($post_string) . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            if (isset($post_string)) {
                $out .= $post_string;
            }
            fwrite($fp, $out);
            fclose($fp);
        } catch (Exception $exception) {
            Self::write_log(
                'critical',
                'Exception occurs while calling Asynchronous thread function, 
                Exception Message : '.$exception->getMessage()
            );
        }
    }

    /**
     * Creating function for print the longs to the logs file, created by application on configuration basis
     *
     * @param string $level
     * @param string $message
     *
     * @return void
     * @throws Throwable
     *
     */
    private static function write_log($level, $message) {
        try {
            Log::stack(array('stack'))->log($level, $message);
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}