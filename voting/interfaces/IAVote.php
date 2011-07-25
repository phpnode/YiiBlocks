<?php
/**
 * IAVote is a dummy interface for items that have the required properties for votes.
 * It's a dummy interface because PHP doesn't allow properties in an interface.
 *  
 * Classes that implement this interface should provide the following public properties: 
 *  
 * @property integer $voterId The the id of the user voting
 * @property string $voterIP The IP address of the voter
 * @property string $voterUserAgent The useragent string of the voter
 * @property integer $score The vote score either -1 or 1
 * 
 * @author Charles Pick
 * @package packages.voting.interfaces
 */
interface IAVote {
}
