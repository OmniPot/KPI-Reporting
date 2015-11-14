<?php

namespace KPIReporting\Framework\Helpers;

class AnnotationParser {

    // Action regexes
    const ROUTE_REGEX = '/@(customRoute)\((?:\'|\")(.*)(?:\'|\")\)/';
    const METHOD_REGEX = '/@(method)\s+(POST|GET|PUT|DELETE)/';
    const ADMIN_REGEX = '/@(admin)/';
    const AUTHORIZE_REGEX = '/@(authorize)/';

    private static $actionRegexes = [
        self::ROUTE_REGEX,
        self::METHOD_REGEX,
        self::ADMIN_REGEX,
        self::AUTHORIZE_REGEX
    ];

    // Property regexes
    const REQUIRED_REGEX = '/@(required)/';

    private static $propertyRegexes = [ self::REQUIRED_REGEX ];

    public static function getDoc( $method ) {
        if ( $method ) {
            $docComment = $method->getDocComment();
            if ( $docComment ) {
                return $docComment;
            }
        }

        return false;
    }

    public static function parseDoc( $doc, $template, $type = 'action' ) {
        if ( !$doc ) {
            return $template;
        }

        $regexCollection = $type . 'Regexes';
        foreach ( self::$$regexCollection as $regex ) {
            preg_match( $regex, $doc, $routeMatches );
            if ( $routeMatches ) {
                $parseMethod = 'parse' . ucfirst( $routeMatches[ 1 ] );
                $template[ $routeMatches[ 1 ] ] = self::$parseMethod( $routeMatches );
            }
        }

        return $template;
    }

    private static function parseCustomRoute( $matches ) {
        $exploded = explode( '/', $matches[ 2 ] );
        $routeResult = [ 'uri' => '' ];

        foreach ( $exploded as $key ) {
            $routeResult[ 'uri' ] .= "$key/";
        }

        $routeResult[ 'uri' ] = rtrim( $routeResult[ 'uri' ], '/' );

        return $routeResult;
    }

    private static function parseMethod( $matches ) {
        return $matches[ 2 ];
    }

    private static function parseAdmin( $matches ) {
        return isset( $matches[ 1 ] ) ? true : false;
    }

    private static function parseAuthorize( $matches ) {
        return isset( $matches[ 1 ] ) ? true : false;
    }

    private static function parseRequired( $matches ) {
        return isset( $matches[ 1 ] ) ? true : false;
    }

}