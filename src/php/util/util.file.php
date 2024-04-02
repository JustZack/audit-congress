<?php

namespace Util {
    class File {
        public static function getAbsolutePath($pathRelativeToProject) {
            return realpath(ROOTFOLDER.$pathRelativeToProject);
        }

        public static function getLastSlashPos($absolutePathToFile) {
            return strrpos($absolutePathToFile, DIRECTORY_SEPARATOR);
        }

        public static function getFolderPath($absolutePathToFile) {
            $lastSlash = self::getLastSlashPos($absolutePathToFile);
            return substr($absolutePathToFile, 0, $lastSlash);
        }

        public static function getFileName($absolutePathToFile) {
            $lastSlash = self::getLastSlashPos($absolutePathToFile);
            return substr($absolutePathToFile, $lastSlash+1);
        }
    }
}

?>