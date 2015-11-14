<?php

namespace KPIReporting\Framework\Helpers;

use DateInterval;
use DateTime;
use KPIReporting\Config\AppConfig;
use KPIReporting\Framework\Config\FrameworkConfig;

class FileHelper {

    private static $_builder = null;

    public static function writeFile( $appContents, $actionContents ) {
        if ( !strpos( self::$_builder, '<?php' ) || !self::$_builder ) {
            self::$_builder = "<?php";
        }

        self::appendVariable( AppConfig::APP_STRUCTURE_EXPIRES );
        self::appendExpirationDate();

        self::appendVariable( AppConfig::APP_STRUCTURE );
        self::appendContents( $appContents, true );

        self::appendVariable( AppConfig::APP_ACTION_STRUCTURE );
        self::appendContents( $actionContents, true );

        return self::$_builder;
    }

    private function appendContents( $array, $final = false ) {
        $endLine = $final ? ';' : ',';

        if ( empty( $array ) ) {
            self::$_builder .= "[ ]" . $endLine;
        }
        else {
            self::$_builder .= "[";

            foreach ( $array as $key => $value ) {
                self::appendKey( $key );

                if ( is_array( $value ) ) {

                    if ( end( $array ) == $key ) {
                        self::appendContents( $value, true );
                    }
                    else {
                        self::appendContents( $value );
                    }
                }
                else {
                    self::appendValue( $value );
                }
            }

            self::$_builder .= "]$endLine";
        }
    }

    private function appendExpirationDate() {
        $expirationTime = new DateTime( 'now', new \DateTimeZone( AppConfig::TIME_ZONE ) );
        $formatted = $expirationTime
            ->add( new DateInterval( FrameworkConfig::APP_STRUCTURE_CONFIG_RENEW_TIME ) )
            ->format( 'Y-m-d H:i:s' );

        self::$_builder .= '\'' . $formatted . '\';';
    }

    private function appendVariable( $name ) {
        self::$_builder .= "\n$$name=";
    }

    private function appendKey( $key ) {
        self::$_builder .= '\'' . $key . '\'=>';
    }

    private function appendValue( $value ) {
        self::$_builder .= '\'' . $value . '\',';
    }
}