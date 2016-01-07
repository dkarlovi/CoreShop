<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tool;


use Pimcore\Model\Object\AbstractObject;

use CoreShop\Model\Product;

class Service
{
    /**
     * Gets all Differences in the variants
     *
     * @param CoreShopProduct $product
     * @return array
     */
    public static function getDimensions(Product $product)
    {
        $variants = $product->getChilds(array(AbstractObject::OBJECT_TYPE_VARIANT));
        $fieldDefinition = $product->getClass()->getFieldDefinition("dimensions");
        
        $variantsAndMaster = array_merge(array($product), $variants);
        
        $currentInheritedValue = AbstractObject::getGetInheritedValues();
        AbstractObject::setGetInheritedValues(false);
        
        $overwrittenKeyValues = array();
        $overwrittenKeys = array();

        if(count($variants) > 0)
        {
            foreach($variants as $variant)
            {
                $fieldData = $variant->getDimensions();
                $value = $fieldDefinition->getDataForEditmode($fieldData, $variant);

                //Search for not inherited fields
                foreach($value as $singleBrickData)
                {
                    if(!$singleBrickData)
                        continue;

                    if(!array_key_exists($singleBrickData['type'], $overwrittenKeys))
                        $overwrittenKeys[$singleBrickData['type']] = array();

                    foreach($singleBrickData['metaData'] as $key=>$meta)
                    {
                        if(!$meta['inherited'])
                        {
                            if(!in_array($key, $overwrittenKeys[$singleBrickData['type']]))
                                $overwrittenKeys[$singleBrickData['type']][] = $key;
                        }
                    }
                }
            }
            
            //We now have the keys and reloop the variants to get all the values
            foreach($variantsAndMaster as $variant)
            {
                $fieldData = $variant->getDimensions();
                $value = $fieldDefinition->getDataForEditmode($fieldData, $variant);
                
                foreach($value as $singleBrickData) 
                {
                    if(!$singleBrickData)
                        continue;
                    
                    if(array_key_exists($singleBrickData['type'], $overwrittenKeys))
                    {
                        if(!is_array($overwrittenKeyValues[$singleBrickData['type']]))
                            $overwrittenKeyValues[$singleBrickData['type']] = array();
                        
                        foreach($overwrittenKeys[$singleBrickData['type']] as $key)
                        {
                            $found = false;
                            
                            foreach($overwrittenKeyValues[$singleBrickData['type']][$key] as $existingValue)
                            {
                                if($existingValue['value'] == $singleBrickData['data'][$key])
                                {
                                    $found = true;
                                    break;
                                }
                            }
                            
                            if(!$found)
                            {
                                $overwrittenKeyValues[$singleBrickData['type']][$key][] = array(
                                    "value" => $singleBrickData['data'][$key],
                                    "object" => $variant->getId()
                                );
                            }
                        }
                    }
                }
            }
        }

        AbstractObject::setGetInheritedValues($currentInheritedValue);
        
        return $overwrittenKeyValues;
    }
}