<?php

namespace app\server;

use Closure;
use Com\Alibaba\Otter\Canal\Protocol\Column;
use Com\Alibaba\Otter\Canal\Protocol\Entry;
use Com\Alibaba\Otter\Canal\Protocol\EntryType;
use Com\Alibaba\Otter\Canal\Protocol\EventType;
use Com\Alibaba\Otter\Canal\Protocol\RowChange;
use Com\Alibaba\Otter\Canal\Protocol\RowData;
use Exception;
use xingwenge\canal_php\CanalConnectorFactory;
use xingwenge\canal_php\ICanalConnector;

class CanalServer
{
    private ICanalConnector $client;

    /**
     * @throws Exception
     */
    public function __construct(string $host = "127.0.0.1", int $port = 11111)
    {
        $client = CanalConnectorFactory::createClient(CanalConnectorFactory::CLIENT_SOCKET);
        $client->connect($host, $port);
        $client->checkValid();
        $client->subscribe("1001");

        $this->client = $client;
    }

    /**
     * @param Closure $closure
     * @return mixed
     */
    public function pull(Closure $closure): mixed
    {
        while (true) {
            $message = $this->client->get();
            if ($entries = $message->getEntries()) {
                foreach ($entries as $entry) {
                    $data = self::getData($entry);
                    if (!empty($data)) {
                        $closure($data);
                    }
                }
            }
        }
    }

    /**
     * @param Entry $entry
     * @return array
     * @throws Exception
     */
    public static function getData(Entry $entry): array
    {
        switch ($entry->getEntryType()) {
            case EntryType::TRANSACTIONBEGIN:
            case EntryType::TRANSACTIONEND:
                return [];
        }

        $rowChange = new RowChange();
        $rowChange->mergeFromString($entry->getStoreValue());
        $evenType = $rowChange->getEventType();
        $header   = $entry->getHeader();

        $data = [
            'log_file_name'   => $header->getLogfileName(),
            'log_file_offset' => $header->getLogfileOffset(),
            'schema'          => $header->getSchemaName(),
            'table'           => $header->getTableName(),
            'event_type'      => EventType::name($evenType),
            'sql'             => $rowChange->getSql(),
        ];

        /** @var RowData $rowData */
        foreach ($rowChange->getRowDatas() as $rowData) {
            $data['data'] = match ($evenType) {
                EventType::DELETE => [
                    'before' => self::getColumns($rowData->getBeforeColumns())
                ],
                EventType::INSERT => [
                    'after' => self::getColumns($rowData->getAfterColumns())
                ],
                default           => [
                    'before' => self::getColumns($rowData->getBeforeColumns()),
                    'after'  => self::getColumns($rowData->getAfterColumns())
                ],
            };
        }

        return $data;
    }

    /**
     * @param $columns
     * @return array
     */
    private static function getColumns($columns): array
    {
        $list = [];
        /** @var Column $column */
        foreach ($columns as $column) {
            $list[] = [
                'field'     => $column->getName(),
                'value'     => $column->getValue(),
                'isUpdated' => $column->getUpdated()
            ];
        }

        return $list;
    }
}