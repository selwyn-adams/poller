<?php

namespace Poller\DeviceMappers\Cambium;

use Poller\DeviceMappers\BaseDeviceMapper;
use Poller\Models\SnmpResult;
use Poller\Services\Formatter;

class PTP670Backhaul extends BaseDeviceMapper
{
    public function map(SnmpResult $snmpResult)
    {
        return $this->getRemoteBackhaul(parent::map($snmpResult));
    }

    private function getRemoteBackhaul(SnmpResult $snmpResult):SnmpResult
    {
        $interfaces = $this->interfaces;
        foreach ($interfaces as $id => $deviceInterface)
        {
            if (strpos($deviceInterface->getName(),"wireless") !== false)
            {
                $macs = $deviceInterface->getConnectedLayer1Macs();
                $result = $this->device->getSnmpClient()->get("1.3.6.1.4.1.17713.11.5.4.0");
                if ($result->has("1.3.6.1.4.1.17713.11.5.4.0")) {
                    $macs[] = Formatter::formatMac($result->get("11.3.6.1.4.1.17713.11.5.4.0")->getValue()->__toString());
                }

                $interfaces[$id]->setConnectedLayer1Macs($macs);
                break;
            }
        }

        $snmpResult->setInterfaces($interfaces);
        return $snmpResult;
    }
}