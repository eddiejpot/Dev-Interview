<?php

namespace App\Classes;

class PriceHelper
{
    /*
     * Todo: Coding Test for Technical Hires
     * Please read the instructions on the README.md
     * Your task is to write the functions for the PriceHelper class
     * A set of sample test cases and expected results can be found in PriceHelperTest
     */

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the unit price of an item based on the quantity.
     *
     * Question:
     * If I purchase 10,000 bicycles, the unit price of the 10,000th bicycle would be 1.50
     * If I purchase 10,001 bicycles, the unit price of the 10,001st bicycle would be 1.00
     * If I purchase 100,001 bicycles, what would be the unit price of the 100,001st bicycle?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getUnitPriceTierAtQty(int $qty, array $tiers): float
    {   
        //edge case of when qty is 0, return 0.0
        if ($qty === 0){
            return 0.0;
        }

        //assume that $tiers input is sorted in ascending order
        //sort from largest to smallest tier
        krsort($tiers);
        // return appropriate tier unit cost
        foreach($tiers as $key => $val) {
            // print($val);
            if ($qty >= $key) {
            return $val;
            }  
        }
    }

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the total price of an order of items based on the quantity ordered
     *
     * Question:
     * If I purchase 10,000 bicycles, the total price would be 1.5 * 10,000 = $15,000
     * If I purchase 10,001 bicycles, the total price would be (1.5 * 10,000) + (1 * 1) = $15,001
     * If I purchase 100,001 bicycles, what would the total price be?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getTotalPriceTierAtQty(int $qty, array $tiers): float
    {
        // We assume that $tiers input is sorted in ascending order

        //edge case of when qty is less than 0, return 0.0
        if ($qty <= 0){
            return 0.0;
        }

        $tierUnitPrices = array_values($tiers);
        $tierMinOrderQty = array_keys($tiers);
        $numOfTiers = count($tiers);

        $totalSum = 0;
        $qtyBoughtSoFar = 0;

        for ($i = 0; $i < $numOfTiers; $i++) {

            //exit condition once there is nothing left to buy
            if ($qtyBoughtSoFar >= $qty){
                break;
            }

            $remainingQtyToBuy = $qty - $qtyBoughtSoFar;
            $currentTierUnitPrice = $tierUnitPrices[$i];

            //final tier
            // in the final tier the user will buy the remainder at the current tier's price and exit loop
            if ($i == $numOfTiers-1){
                $totalSum += $remainingQtyToBuy * $currentTierUnitPrice;
                break;
            }

            // current tier
            $currentTierMinOrderQty = $tierMinOrderQty[$i+1] - 1;
            $qtyToBuy = $currentTierMinOrderQty-$qtyBoughtSoFar;

            // buy the max quantity that the user is allowed to buy at current tier's price
            if ($remainingQtyToBuy > $qtyToBuy) {
                $totalSum +=  $qtyToBuy * $currentTierUnitPrice;
                $qtyBoughtSoFar += $qtyToBuy;
            }   

            // else buy the remaining quantity at current tier's price
            else {
                $totalSum += $remainingQtyToBuy * $currentTierUnitPrice;
                $qtyBoughtSoFar += $remainingQtyToBuy;
            }

        }

        return floatval($totalSum);        
    }

    /**
     * Task: Given an array of quantity of items ordered per month and an associative array of minimum order quantities and their respective prices, write a function to return an array of total charges incurred per month. Each item in the array should reflect the total amount the user has to pay for that month.
     *
     * Question A:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on a special pricing tier where the quantity does not reset each month and is thus CUMULATIVE.
     *
     * Question B:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on the typical pricing tier where the quantity RESETS each month and is thus NOT CUMULATIVE.
     *
     */
    public static function getPriceAtEachQty(array $qtyArr, array $tiers, bool $cumulative = false): array
    {
        // We assume that $tiers input is sorted in ascending order

        // Case where user IS on the special pricing tier
        if ($cumulative){
            
            $tierUnitPrices = array_values($tiers);
            $tierMinOrderQty = array_keys($tiers);
            $numOfTiers = count($tiers);
            $cumulativeQty = 0; 
            $cumulativePricePlanResult = [];
            
            // Loop through the order quantities month by month
            foreach ($qtyArr as $currentMonthQty) {

                //edge case for when current month's qty is less than 0, append 0.0
                if ($currentMonthQty <= 0 ){
                    array_push($cumulativePricePlanResult, 0.0);
                    continue;
                }
                
                $totalBillForTheMonth = 0; 
                $qtyBoughtSoFar = 0;
                $remainingQtyToBuy = $currentMonthQty;
                
                // Loop through each tier and determin quantity to buy at each tier
                for ($i = 0; $i < $numOfTiers; $i++) {
                    // print("tier${i}-> qtyBoughtSoFar: ${qtyBoughtSoFar} , remainingQtyToBuy: ${remainingQtyToBuy} , cumulativeQty: ${cumulativeQty}") . PHP_EOL;

                    //exit condition once there is nothing left to buy
                    if ($qtyBoughtSoFar >= $currentMonthQty){
                        break;
                    }
                            
                    $currentTierUnitPrice = $tierUnitPrices[$i];
        
                    // final tier
                    // in the final tier the user will buy the remainder at the current tier's price and exit loop
                    if ($i == $numOfTiers-1){
                        $totalBillForTheMonth += $remainingQtyToBuy * $currentTierUnitPrice;
                        $qtyBoughtSoFar += $remainingQtyToBuy;
                        $remainingQtyToBuy = $currentMonthQty - $qtyBoughtSoFar;
                        $cumulativeQty += $qtyBoughtSoFar;
                        // print("qtyBoughtSoFar: ${qtyBoughtSoFar} , remainingQtyToBuy: ${remainingQtyToBuy} , cumulativeQty: ${cumulativeQty}") . PHP_EOL;
                        break;
                    }
                    
                    // current tier
                    $currentTierMinOrderQty = max(0,$tierMinOrderQty[$i] - 1);
                    $currentTierMaxOrderQty = $tierMinOrderQty[$i+1] - 1;
                    // print("tier${i}->  min: ${currentTierMinOrderQty} , max: ${currentTierMaxOrderQty}") . PHP_EOL;

                    // if cumulative quantity is less than current max order qty then move on to the next tier
                    if ($cumulativeQty < $currentTierMaxOrderQty) {
                        // set the max quantity that the user is allowed to buy at the current tiers price
                        $maxQtyAllowedAtCurrentTiersPrice = $currentTierMaxOrderQty - $cumulativeQty;
        
                        // buy the max quantity that the user is allowed to buy at current tier's price
                        if ($remainingQtyToBuy >= $maxQtyAllowedAtCurrentTiersPrice){
                            $totalBillForTheMonth += $maxQtyAllowedAtCurrentTiersPrice * $currentTierUnitPrice;
                            $qtyBoughtSoFar += $maxQtyAllowedAtCurrentTiersPrice;
                            $cumulativeQty += $maxQtyAllowedAtCurrentTiersPrice;
                        }

                        // else buy the remaining quantity at current tier's price
                        else {
                            $totalBillForTheMonth += $remainingQtyToBuy * $currentTierUnitPrice;
                            $qtyBoughtSoFar += $remainingQtyToBuy;
                            $cumulativeQty += $remainingQtyToBuy;
                        }
                        
                    }   
                    
                    $remainingQtyToBuy = $currentMonthQty - $qtyBoughtSoFar;
                    // print("qtyBoughtSoFar: ${qtyBoughtSoFar} , remainingQtyToBuy: ${remainingQtyToBuy} , cumulativeQty: ${cumulativeQty}") . PHP_EOL;

                }

                array_push($cumulativePricePlanResult, floatval($totalBillForTheMonth));

            }

            return $cumulativePricePlanResult;  
        }
        
        // Case where user is NOT the special pricing tier
        else {

            $nonCumulativePricePlanResult = [];

            // Loop through the order quantities month by month
            foreach ($qtyArr as $currentMonthQty) {
                array_push($nonCumulativePricePlanResult, self::getTotalPriceTierAtQty($currentMonthQty,$tiers));
            }

            return $nonCumulativePricePlanResult;
        }
    }
}
