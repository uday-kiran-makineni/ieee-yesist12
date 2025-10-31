<?php
/**
 * He5 Framework Class Stubs for IDE Support
 * 
 * This file contains stub declarations for He5 Framework classes
 * to provide IDE support and eliminate "Undefined class" warnings.
 * 
 * These are NOT the actual implementations - they are loaded from
 * the He5-Frame-work-1.0.3.phar file at runtime.
 * 
 * @package He5Framework
 * @version 1.0.3
 */

if (false) { // This block will never execute, it's just for IDE
    
    /**
     * He5ED - Encryption/Decryption utility class
     */
    class He5ED {
        /**
         * Encrypt data using He5ED algorithm
         * @param string $data Data to encrypt
         * @param string $key Encryption key
         * @return string Encrypted data
         */
        public static function encryptData(string $data, string $key): string {
            return '';
        }
        
        /**
         * Decrypt data using He5ED algorithm
         * @param string $encryptedData Encrypted data
         * @param string $key Decryption key
         * @return string Decrypted data
         */
        public static function decryptData(string $encryptedData, string $key): string {
            return '';
        }
    }
    
    /**
     * Logger - He5 Framework logging utility
     */
    class Logger {
        public function __construct(string $logDir) {}
        public function log(string $message): void {}
        public function error(string $message): void {}
        public function info(string $message): void {}
    }
    
    /**
     * Router - He5 Framework routing utility
     */
    class Router {
        public static function DB(): PDO { return new PDO('', '', ''); }
        public function get(string $route, callable $handler): void {}
        public function post(string $route, callable $handler): void {}
    }
    
    /**
     * AuthConstants - Authentication constants
     */
    class AuthConstants {
        public const DEFAULT_SESSION_TIMEOUT = 3600;
    }
}
?>