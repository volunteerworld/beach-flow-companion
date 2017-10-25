<?php
namespace Flownative\BeachFlowCompanion\Cache;

use Neos\Flow\Exception;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\PdoHelper;

/**
 * Class PdoBackend
 */
class PdoBackend extends \Neos\Cache\Backend\PdoBackend
{
    /**
     * @Flow\InjectConfiguration(path="persistence.backendOptions", package="Neos.Flow")
     * @var array
     */
    protected $backendOptions;

    /**
     * @Flow\InjectConfiguration(path="persistence.pdoCacheBackendOptions", package="Neos.Flow")
     * @var array
     */
    protected $pdoCacheBackendOptions;

    /**
     *
     */
    public function initializeObject()
    {
        if(empty($this->pdoCacheBackendOptions)){
            $this->dataSourceName = str_replace('pdo_','', $this->backendOptions['driver']) . ':host=' . $this->backendOptions['host'] . ';dbname=' . $this->backendOptions['dbname'];
            $this->username = $this->backendOptions['user'];
            $this->password = $this->backendOptions['password'];
        }else{
            $this->dataSourceName = str_replace('pdo_','', $this->pdoCacheBackendOptions['driver']) . ':host=' . $this->pdoCacheBackendOptions['host'] . ';dbname=' . $this->pdoCacheBackendOptions['dbname'];
            $this->username = $this->pdoCacheBackendOptions['user'];
            $this->password = $this->pdoCacheBackendOptions['password'];
        }
        parent::initializeObject();
    }

    /**
     * @return void
     * @throws Exception
     * @throws \Neos\Cache\Exception
     */
    public function createTableIfNeeded()
    {
        $this->connect();
        try {
            PdoHelper::importSql($this->databaseHandle, $this->pdoDriver, 'resource://Flownative.BeachFlowCompanion/Private/CreateCacheTables.sql');
        } catch (\PDOException $exception) {
            throw new Exception('Could not create cache tables with DSN "' . $this->dataSourceName . '". PDO error: ' . $exception->getMessage(), 1259576985);
        }
    }
}
