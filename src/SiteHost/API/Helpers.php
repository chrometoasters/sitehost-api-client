<?php

namespace Chrometoaster\SiteHost\API;

class Helpers
{
    /**
     * Decode and parse docker-compose.yml from the stack info
     *
     * @param Response $stackInfo
     * @return array|null
     */
    public static function decodeDockerComposeYAMLFile(Response $stackInfo): ?array
    {
        return $stackInfo->isValid() ? yaml_parse($stackInfo->getDataItem('docker_file')) : null;
    }


    /**
     * Encode array-based docker-compose.yml data structure back into a json string that can be passed back to the API
     *
     * @param array $dockerComposeFile
     * @return string
     */
    public static function encodeDockerComposeYAMLFile(array $dockerComposeFile): string
    {
        return yaml_emit($dockerComposeFile);
    }


    /**
     * Add item to a comma-delimited list within a sections
     *
     * Some aspects of the configuration are held in comma-delimited string lists within docker-compose.yaml sections,
     * consisting of an array of records with a specific prefix.
     *
     * This method allows to find the specific record starting with a specific prefix within the section and add a value
     * to the list, filtering the values to keep a unique set only.     *
     *
     * @param array $section
     * @param string $listPrefix
     * @param $value
     * @return bool
     */
    private static function pushValueToSectionList(array &$section, string $listPrefix, $value): bool
    {
        foreach ($section as $key => $val) {
            if (mb_strpos($val, $listPrefix) === 0) {
                $list = explode(',', mb_substr($val, mb_strlen($listPrefix) + 1)); // +1 as we're adding = after the key

                // add our alias and remove duplicates if any
                $list[] = $value;
                $list   = array_unique($list);

                $section[$key] = $listPrefix . '=' . implode(',', $list);

                return true;
            }
        }

        return false;
    }


    /**
     * Add an alias to a stack/container
     *
     * @param array $dockerComposeFile
     * @param string $stack
     * @param string $alias
     * @return bool
     */
    public static function addStackAlias(array &$dockerComposeFile, string $stack, string $alias): bool
    {
        if (!isset($dockerComposeFile['services'][$stack])) {
            return null;
        }

        $environments = &$dockerComposeFile['services'][$stack]['environment'];
        $labels       = &$dockerComposeFile['services'][$stack]['labels'];

        return self::pushValueToSectionList($environments, Constants::VHOSTS_KEY_ENVIRONMENTS, $alias)
            &&
            self::pushValueToSectionList($labels, Constants::VHOSTS_KEY_LABELS, $alias);
    }
}
