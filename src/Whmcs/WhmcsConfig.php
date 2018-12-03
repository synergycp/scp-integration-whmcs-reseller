<?php

namespace Scp\WhmcsReseller\Whmcs;

use Scp\Support\Collection;

class WhmcsConfig
{
    /**
     * Functions
     */
    const FORM = 'ConfigOptions';

    /**
     * Config Options (make sure to update count below when adding).
     */
    const API_USER = 1;
    const TICKET_DEPT = 2;

    /**
     * The 1-based index of the last Config Option.
     *
     * @var int
     */
    protected $countOptions = self::TICKET_DEPT;

    const API_USER_DESC = 'This must be a WHMCS administrator user with API access enabled.';
    const TICKET_DEPT_DESC = 'When provisioning fails due to low inventory, a ticket will be filed on behalf of the client in this support department.';

    /**
     * @var Whmcs
     */
    protected $whmcs;

    public function __construct(
        Whmcs $whmcs
    ) {
        $this->whmcs = $whmcs;
    }

    public function get($key)
    {
        $params = $this->whmcs->getParams();

        return $params[$key];
    }

    public function option($key)
    {
        $value = $this->get('configoption'.$key);

        switch ($key) {
            case static::TICKET_DEPT:
                return (string) $this->getDepartmentIdByName($value);
        }

        return $value;
    }

    public function options()
    {
        return $this->get('configoptions');
    }

    /**
     * @return mixed
     */
    public function getOption($option)
    {
        return $this->options()[$option];
    }

    public function form()
    {
        $config = [];

        for ($i = 1; $i <= $this->countOptions; ++$i) {
            $this->addFormOption($config, $i);
        }

        return $config;
    }

    protected function addFormOption(array &$config, $key)
    {
        switch ($key) {
            case static::API_USER:
                return $config['API User'] = [
                    'Type' => 'text',
                    'Size' => '50',
                    'Description' => static::API_USER_DESC,
                ];
            case static::TICKET_DEPT:
                return $config['Ticket Department'] = [
                    'Type' => 'dropdown',
                    'Description' => static::TICKET_DEPT_DESC,
                    'Options' => $this->getDepartmentNames()->implode(','),
                ];
        }
    }

    protected function getDepartmentNames()
    {
        $admin = $this->option(static::API_USER);
        $results = localAPI('getsupportdepartments', [], $admin);
        $departments = $this->getDepartmentsFromResults($results);
        $getName = function ($department) {
            return $department['name'];
        };

        return with(new Collection($departments))
            ->keyBy('id')
            ->map($getName);
    }

    /**
     * @param  array  $results
     *
     * @return array
     */
    protected function getDepartmentsFromResults(array $results)
    {
        if ($results['result'] != 'success') {
            return [[
                'name' => 'Error: ' . json_encode($results),
            ]];
        }

        return $results['departments']['department'];
    }

    /**
     * @param  string $value
     *
     * @return int
     */
    protected function getDepartmentIdByName($value)
    {
        $escaped = htmlspecialchars($value);

        return $this->getDepartmentNames()->search($escaped);
    }

    public static function functions()
    {
        return [
            static::FORM => 'form',
        ];
    }
}
