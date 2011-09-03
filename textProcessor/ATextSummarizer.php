<?php
/**
 * A Pure PHP text summarizing implementation.
 *
 * @author W-Shadow
 * @package packages.textProcessor
 * @licence http://www.opensource.org/licenses/bsd-license.php
 */

class ATextSummarizer extends CComponent {
	/**
	 * List of common words from Open Text Summarizer
	 * @var array
	 */
	public $stopWords = array(
	'--', '-', 'a', 'about', 'again', 'all', 'along', 'almost', 'also', 'always', 'am', 'among', 'an', 'and',
	 'another', 'any', 'anybody', 'anything', 'anywhere', 'apart', 'are', 'around', 'as', 'at', 'be', 'because',
	 'been', 'before', 'being', 'between', 'both', 'but', 'by', 'can', 'cannot', 'comes', 'could', 'couldn', 'did',
	 'didn','different', 'do', 'does', 'doesn', 'done', 'don', 'down', 'during', 'each', 'either', 'enough', 'etc',
	 'even', 'every', 'everybody', 'everything', 'everywhere', 'except', 'few', 'final', 'first', 'for', 'from',
	 'get', 'go', 'goes', 'gone', 'good', 'got', 'had', 'has', 'have', 'having', 'he', 'hence', 'her', 'him', 'his',
	 'how', 'however', 'i', 'i.e', 'if', 'in', 'initial', 'into', 'is', 'isn', 'it', 'its', 'it', 'itself', 'just',
	 'last','least', 'less', 'let', 'lets', 'let\'s', 'like', 'lot', 'made', 'make', 'many', 'may', 'maybe', 'me',
	 'might', 'mine', 'more', 'most', 'Mr', 'much', 'must', 'my', 'near', 'need', 'next', 'niether', 'no', 'nobody',
	 'nor', 'not', 'nothing', 'now', 'nowhere', 'of', 'off', 'often', 'oh', 'ok', 'okay', 'on', 'once', 'one',
	 'only', 'onto', 'or', 'other', 'our', 'ours', 'out', 'over', 'own', 'perhaps', 'previous', 'quite', 'rather',
	 're', 'really', 's', 'said', 'same', 'say', 'see', 'seems', 'several', 'shall', 'she', 'should',
	 'shouldn\'t', 'since', 'so', 'some', 'somebody', 'something', 'somewhere', 'still', 'stuff', 'such', 'than',
	 't', 'that', 'the', 'their', 'theirs', 'them', 'then', 'there', 'these', 'they', 'thing', 'things', 'this',
	 'those', 'through', 'thus', 'to', 'too', 'top', 'two', 'under', 'unless', 'until', 'up', 'upon', 'us',
	 'use', 'v', 've', 'very', 'want', 'was', 'we', 'well', 'went', 'were', 'what', 'when', 'where', 'which',
	 'while', 'who', 'whom', 'why', 'will', 'with', 'without', 'won', 'would', 'x', 'yes', 'yet', 'you', 'you',
	 'your', 'yours', 'll', 'm', 'shouldn', 'won\'t', 'hadn'
	 );

	/**
	 * Holds statistics for each word
	 * @var array
	 */
	public $wordStats = array();


	/**
	 * Summarizes a string
	 * @param string $text The text to summarize
	 * @param float $percent - what percentage of text should be used as the summary (in sentences).
	 * @param integer $minSentences - the minimum length of the summary in sentences.
	 * @param integer $maxSentences - the maximum length of the summary.
	 * @return string The summarized text
	 */
	public function summarize($text, $percent=0.2, $minSentences=1, $maxSentences=0){
		$sentences = $this->tokenizeSentence($text);
		$sentenceList = array();

		for($i=0; $i<count($sentences); $i++) {
			$words = $this->tokenizeWords($sentences[$i]);
			$wordStats = array();
			foreach ($words as $word) {
				if (in_array($word, $this->stopWords)) {
					continue;
				}

				$word = APorterStemmer::stem($word);
				//skip stopwords by stem
				if (in_array($word, $this->stopWords)) {
					continue;
				}

				//per-sentence word counts
				if (!isset($wordStats[$word])) {
					$wordStats[$word]=1;
				} else {
					$wordStats[$word]++;
				}

				//global word counts
				if (!isset($this->wordStats[$word])) {
					$this->wordStats[$word]=1;
				} else {
					$this->wordStats[$word]++;
				}
			}

			$sentenceList[] = array(
				'sentence' => $sentences[$i],
				'wordStats' => $wordStats,
				'order' => $i
			);
		}

		//sort words by frequency
		arsort($this->wordStats);
		//only consider top 20 most common words. Throw away the rest.
		$this->wordStats = array_slice($this->wordStats,0,20);

		for($i=0; $i<count($sentenceList); $i++){
			$rating = $this->rateSentence($sentenceList[$i]['wordStats']);
			$sentenceList[$i]['rating'] = $rating;
		}

		//Sort sentences by importance rating
		usort($sentenceList, array(&$this, 'compareArraysByRating'));

		//How many sentences do we need?
		if ($maxSentences==0) {
			$maxSentences = count($sentenceList);
		}
		$summaryCount = min(
			$maxSentences,
			max(
				min($minSentences, count($sentenceList)) ,
				round($percent*count($sentenceList))
			)
		);
		if ($summaryCount<1) {
			$summaryCount = 1;
		}

		//Take the X highest rated sentences (from the end of the array)
		$summaryList = array_slice($sentenceList, -$summaryCount);

		//Restore the original sentence order
		usort($summaryList, array(&$this, 'compareArraysByOrder'));

		$summary = array();
		foreach($summaryList as $sentence){
			$summary[] = $sentence['sentence'];
		}

		return implode("\n",$summary);
	}
	/**
	 * Compares arrays by their rating
	 * @param array $a the first array
	 * @param array $b the second array
	 * @return float|int the value to use when sorting
	 */
	protected function compareArraysByRating($a, $b){
		return $this->compareArrays($a, $b, 'rating');
	}
	/**
	 * Compares arrays by their order
	 * @param array $a the first array
	 * @param array $b the second array
	 * @return float|int the value to use when sorting
	 */
	protected function compareArraysByOrder($a, $b){
		return $this->compareArrays($a, $b, 'order');
	}
	/**
	 * Compares 2 arrays based on the given key
	 * @param array $a the first array
	 * @param array $b the second array
	 * @param string $key the key to use when comparing
	 * @return float|int the value to use when sorting
	 */
	protected function compareArrays($a, $b, $key){
		if (is_int($a[$key]) || is_float($a[$key])) {
			return floatval($a[$key])-floatval($b[$key]);
		}
		else {
			return strcmp(strval($a[$key]), strval($b[$key]));
		}
	}
	/**
	 * Splits text into sentences. Treats newlines as end-of-sentence markers too.
	 * @param string $text the text to tokenize
	 * @return array an array of sentences
	 */
	protected function tokenizeSentence($text){

		if (preg_match_all('/["\']*.+?([.?!\n\r]+["\']*\s+|$)/si', $text, $matches, PREG_SET_ORDER)){
			$sentences = array();
			foreach ($matches as $match){
				array_push($sentences, trim($match[0]));
			}
			return $sentences;
		}
		else {
			return array($text);
		}
	}
	/**
	 * Splits a sentence into an array of words and does some cleanup
	 * @param string $sentence
	 * @return array an array of words
	 */
	protected function tokenizeWords($sentence){
		$rawWords = preg_split('/[\'\s\r\n\t$]+/', $sentence);
		$words = array();
		foreach($rawWords as $word){
			$word = preg_replace('/(^[^a-z0-9]+|[^a-z0-9]$)/i','', $word);
			$word = strtolower($word);
			if (strlen($word)>0) {
				$words[] = $word;
			}
		}
		return $words;
	}
	/**
	 * Calculates the rating for a sentence
	 * @param array $words array of words in a sentence
	 * @return int the rating for this sentence
	 */
	protected function rateSentence($words){
		$rating = 0;
		foreach ($words as $word => $count){
			if (!isset($this->wordStats[$word])) {
				continue;
			}
			$wordRating = $count * $this->wordStats[$word];
			$rating += $wordRating;
		}
		return $rating;
	}

}




