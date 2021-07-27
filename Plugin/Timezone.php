<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Luxinten\FixTimezone\Plugin;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Phrase;

class Timezone
{

    /**
     * @var string
     */
    protected $_scopeType;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var string
     */
    protected $_defaultTimezonePath;
    /**
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param ScopeConfigInterface $scopeConfig
     * @param string $scopeType
     * @param string $defaultTimezonePath
     */

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $scopeType,
        $defaultTimezonePath
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_scopeType = $scopeType;
        $this->_defaultTimezonePath = $defaultTimezonePath;
    }


    /**
     * @inheritdoc
     */
    public function getDefaultTimezonePath()
    {
        return $this->_defaultTimezonePath;
    }

    public function afterScopeDate($scope = null, $date = null, $includeTime = false)
    {
        {
            $timezone = new \DateTimeZone(
                $this->_scopeConfig->getValue($this->getDefaultTimezonePath(), $this->_scopeType, $scope)
            );
            switch (true) {
                case (empty($date)):
                    $date = new \DateTime('now', $timezone);
                    break;
                case ($date instanceof \DateTime):
                case ($date instanceof \DateTimeImmutable):
                    $date = $date->setTimezone($timezone);
                    break;
                default:
                    $date = new \DateTime(is_numeric($date) ? '@' . $date : $date);
                    $date->setTimezone($timezone);
                    break;
            }

            if (!$includeTime) {
                $date->setTime(0, 0, 0);
            }

            return $date;
        }
    }
}
