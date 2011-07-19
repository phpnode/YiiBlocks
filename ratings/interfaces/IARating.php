<?php
/**
 * IARating is a dummy interface for items that have the required properties for ratings.
 * It's a dummy interface because PHP doesn't allow properties in an interface.
 *  
 * Classes that implement this interface should provide the following public properties: 
 *  
 * @property integer $raterId The the id of the user voting
 * @property string $raterIP The IP address of the voter
 * @property string $raterUserAgent The useragent string of the voter
 * @property integer $score The rating score, usually 0 to 10
 * 
 * @author Charles Pick
 * @package blocks.voting.interfaces
 */
interface IARating {
}
