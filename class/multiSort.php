<?php
/************************************
*    Allows sorting multi-dimensional
*    arrays by a specific key and in
*    asc or desc order
**/
class multiSort
{
    var $key;    //key in your array

    //runs the sort, and returns sorted array
    function run ($myarray, $key_to_sort, $type_of_sort = '')
    {
        $this->key = $key_to_sort;
       
        if ($type_of_sort == 'desc')
            uasort($myarray, array($this, 'myreverse_compare'));
        else
            uasort($myarray, array($this, 'mycompare'));
           
        return $myarray;
    }
   
    //for ascending order
    function mycompare($x, $y)
    {
        if ( $x[$this->key] == $y[$this->key] )
            return 0;
        else if ( $x[$this->key] < $y[$this->key] )
            return -1;
        else
            return 1;
    }
   
    //for descending order
    function myreverse_compare($x, $y)
    {
        if ( $x[$this->key] == $y[$this->key] )
            return 0;
        else if ( $x[$this->key] > $y[$this->key] )
            return -1;
        else
            return 1;
    }
}
?>