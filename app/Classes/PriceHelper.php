<?php

namespace App\Classes;

class PriceHelper
{
    /*
     * This variable is used to keep track of the cumulative purchases
     */
    public static $cumulativeQty= 0;
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

        //assume that $tiers is sorted ascending
        //resort largest to smallest tier
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
    public static function getTotalPriceTierAtQty(int $qty, array $tiers, $isCumulative = False): float
    {
        // For NON-CUMULATIVE monthly purchases it will reset to 0
        // For CUMULATIVE monthly purchases it will NOT reset to 0
        if ($isCumulative == False){
            self::$cumulativeQty = 0;
        }

        //edge case of when qty is 0, return 0.0
        if ($qty === 0){
            return 0.0;
        }

        //assume that $tiers is sorted ascending
        $tierUnitPrices = array_values($tiers);
        $tierMinOrderQty = array_keys($tiers);
        $numOfTiers = count($tiers);
        
        //index 0 represents first tier
        $totalSumOfPurchasesArray = [];
        $qtyBoughtSoFar = 0;
        $remainingQtyToBuy = $qty;
        
        // Loop through each tier and determin how much to buy
        for ($i = 0; $i < $numOfTiers; $i++) {
            
            //exit condition 
            if ($qtyBoughtSoFar >= $qty){
                break;
            }
            
            // print("tier${i}-> qtyBoughtSoFar: ${qtyBoughtSoFar} , remainingQtyToBuy: ${remainingQtyToBuy} , cumulativeQty: ${self::cumulativeQty}") . PHP_EOL;
            $currentTierUnitPrice = $tierUnitPrices[$i];
            
            //final tier
            if ($i == $numOfTiers-1){
                // print("buying last...") . PHP_EOL;
                array_push($totalSumOfPurchasesArray, $remainingQtyToBuy * $currentTierUnitPrice);
                $qtyBoughtSoFar += $remainingQtyToBuy;
                $remainingQtyToBuy = $qty - $qtyBoughtSoFar;
                self::$cumulativeQty += $qtyBoughtSoFar;
                // print("qtyBoughtSoFar: ${qtyBoughtSoFar} , remainingQtyToBuy: ${remainingQtyToBuy} , cumulativeQty: ${self::cumulativeQty}") . PHP_EOL;
                break;
            }
            
            // current tier
            $currentTierMinOrderQty = max(0,$tierMinOrderQty[$i] - 1);
            $currentTierMaxOrderQty = $tierMinOrderQty[$i+1] - 1;
            // print("tier${i}->  min: ${currentTierMinOrderQty} , max: ${currentTierMaxOrderQty}") . PHP_EOL;

            
            if (self::$cumulativeQty < $currentTierMaxOrderQty) {
                // print("buying...") . PHP_EOL;
                $buyMaxQty = $currentTierMaxOrderQty - self::$cumulativeQty;
                // print ("check -> remainingQtyToBuy: ${remainingQtyToBuy} , buyMaxQty: ${buyMaxQty}") . PHP_EOL;
                if ($remainingQtyToBuy >= $buyMaxQty){
                    array_push($totalSumOfPurchasesArray, $buyMaxQty * $currentTierUnitPrice);
                    $qtyBoughtSoFar += $buyMaxQty;
                    self::$cumulativeQty += $buyMaxQty;
                }
                else {
                    array_push($totalSumOfPurchasesArray, $remainingQtyToBuy * $currentTierUnitPrice);
                    $qtyBoughtSoFar += $remainingQtyToBuy;
                    self::$cumulativeQty += $remainingQtyToBuy;
                }
                
            }   
            
            $remainingQtyToBuy = $qty - $qtyBoughtSoFar;
            // print("qtyBoughtSoFar: ${qtyBoughtSoFar} , remainingQtyToBuy: ${remainingQtyToBuy} , cumulativeQty: ${self::cumulativeQty}") . PHP_EOL;
            
        }
        
        $totalSumOfPurchases = floatval(array_sum($totalSumOfPurchasesArray));
        // print("End of calculation: ====>") . PHP_EOL;
        // print_r($totalSumOfPurchasesArray) . PHP_EOL;
        // print ($totalSumOfPurchases) . PHP_EOL;
        return $totalSumOfPurchases;
        
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
        self::$cumulativeQty = 0;

        // Cumulative calculation
        if ($cumulative){

            $cumulativeResult = [];

            foreach ($qtyArr as $qty) {
                array_push($cumulativeResult, self::getTotalPriceTierAtQty($qty, $tiers, True));
            }

            return $cumulativeResult;
        }

        // Non-cumulative calculation
        // note to self: consider using the map function here when you refactor

        $nonCumulativeResult = [];
    
        foreach ($qtyArr as $qty) {
            array_push($nonCumulativeResult, self::getTotalPriceTierAtQty($qty, $tiers));
        }

        return $nonCumulativeResult;

    }
}
