<?php
/**
 * Runs a list of benchmarks using apache ab
 * @author Charles Pick
 * @package packages.benchmark.components
 */
class ABenchmarkRunner extends CComponent {
	/**
	 * An array of benchmarks to run
	 * @var ABenchmark[]
	 */
	public $benchmarks = array();
	/**
	 * The number of requests to perform
	 * @var integer
	 */
	public $requests = 1000;
	/**
	 * The number of concurrent requests
	 * @var integer
	 */
	public $concurrency = 20;

	/**
	 * Runs the benchmarks and collects the results
	 */
	public function run() {
		foreach($this->benchmarks as /* @var ABenchmark $benchmark */ $benchmark) {
			$this->runBenchmark($benchmark);
		}
	}
	/**
	 * Runs a benchmark
	 * @param ABenchmark $benchmark
	 * @return boolean Whether the benchmark ran or not
	 */
	protected function runBenchmark(ABenchmark $benchmark) {
		$initialLoadAverage = Yii::app()->sysinfo->getCurrentLoad();
		$abPath = Yii::app()->getModule("admin")->getModule("benchmark")->abPath;
		$command = $abPath."ab ".$this->buildAbParameters($benchmark);
		echo $command."\n";
		$results = shell_exec($command);
		$result = $this->parseAbResults($results,$benchmark);
		$result->initialLoadAverage = $initialLoadAverage;
		$result->finalLoadAverage = Yii::app()->sysinfo->getCurrentLoad();
		return $result->save();
	}
	/**
	 * Returns the parameters to pass to ab
	 * @param ABenchmark $benchmark the benchmark to run
	 * @return string the parameters
	 */
	protected function buildAbParameters(ABenchmark $benchmark) {
		$params = array();
		$params[] = "-n ".$this->requests; // the number of requests to make
		$params[] = "-c ".$this->concurrency; // the number of concurrent requests
		// last item, the URL
		$params[] = $benchmark->getUrl();
		return implode(" ",$params);
	}
	/**
	 * @param string $input the results from ab
	 * @param ABenchmark $benchmark the benchmark being run
	 * @return ABenchmarkResult the saved benchmark result
	 */
	protected function parseAbResults($input, ABenchmark $benchmark) {
		$patterns = array(
			"serverSoftware" => "/^Server Software: (.*)/",
			"serverHostname" => "/^Server Hostname: (.*)/",
			"serverPort" => "/^Server Port: (.*)/",
			"documentPath" => "/^Document Path: (.*)/",
			"documentSize" => "/^Document Length: (.*) bytes/",
			"concurrency" => "/^Concurrency Level: (.*)/",
			"duration" => "/^Time taken for tests: (.*) seconds/",
			"completedRequests" => "/^Complete requests: (.*)/",
			"failedRequests" => "/^Failed requests: (.*)/",
			"writeErrors" => "/^Write errors: (.*)/",
			"totalTransferred" => "/^Total transferred: (.*) bytes/",
			"htmlTransferred" => "/^HTML transferred: (.*)/",
			"requestsPerSecond" => "/^Requests per second: (.*) \\[#\\/sec\\] \\(mean\\)/",
			"timePerRequest" => "/^Time per request: (.*) \\[ms\\] \\(mean\\)/",
			"longestRequest" => "/^100% (.*) \\(longest request\\)/",
			"transferRate" => "/^Transfer rate: (.*) \\[Kbytes\\/sec\\] received/",
		);
		$model = new ABenchmarkResult();
		$model->benchmarkId = $benchmark->id;
		foreach(explode("\n",$input) as $line) {
			$line = trim($line);
			foreach($patterns as $attribute => $pattern) {
				if (preg_match($pattern,$line,$matches)) {
					$model->{$attribute} = trim($matches[1]);
				}
			}
		}
		return $model;
	}
}