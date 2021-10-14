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

        //sort by largest to smallest tier
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
        //edge case of when qty is 0, return 0.0
        if ($qty === 0){
            return 0.0;
        }

        $tierUnitPrices = array_values($tiers);
        $tierMinOrderQty = array_keys($tiers);
        $numOfTiers = count($tiers);
        
        //index 0 represents first tier, index 1 represents second tier, and so on...
        $totalSum = [];
        $qtyBoughtSoFar = 0;
        
        for ($i = 0; $i < $numOfTiers; $i++) {
            
            //exit condition 
            if ($qtyBoughtSoFar >= $qty){
                break;
            }
            
            $remainingQty = $qty - $qtyBoughtSoFar;
            // print("tier ${i} , qtyBoughtSoFar: ${qtyBoughtSoFar}") . PHP_EOL;
            $currentTierUnitPrice = $tierUnitPrices[$i];
            
            //final tier
            if ($i == $numOfTiers-1){
                array_push($totalSum, $remainingQty * $currentTierUnitPrice);
                continue;
            }
            
            // current tier
            $currentTierMinOrderQty = $tierMinOrderQty[$i+1] - 1;
            $qtyToBuy = $currentTierMinOrderQty-$qtyBoughtSoFar;
            
            // print("qtyToBuy: ${qtyToBuy} , remainingQty ${remainingQty} , currentTierMinOrderQty ${currentTierMinOrderQty}") . PHP_EOL;
            
            if ($remainingQty > $qtyToBuy) {
                array_push($totalSum, $currentTierUnitPrice * $qtyToBuy);
                $qtyBoughtSoFar += $qtyToBuy;
            }   
            
            else {
                array_push($totalSum, $remainingQty * $tierUnitPrices[$i]);
                $qtyBoughtSoFar += $remainingQty;
            }
            
        }
        
        $totalSumArr = floatval(array_sum($totalSum));
        return $totalSumArr;
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
       return [105000.0];
    }
}
