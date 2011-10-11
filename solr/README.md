#### Configuring your solr connection

Before we can use solr, we must configure a connection to use.
In the application config, add the following
<pre>
"components" => array(
	...
	"solr" => array(
	 	"class" => "packages.solr.ASolrConnection",
	 	"clientOptions" => array(
	 		"hostname" => "localhost",
	 		"port" => 8983,
	 	),
	 ),
),
</pre>

This will configure an application component called "solr".
If you're dealing with more than one index, define a new solr connection for each one, giving each a unique name.


#### Indexing a document with solr


To add a document to solr we use the {@link ASolrDocument} class.
Example:
<pre>
$doc = new ASolrDocument;
$doc->id = 123;
$doc->name = "test document";
$doc->save(); // adds the document to solr
</pre>
Remember - Your chances won't appear in solr until a commit occurs.
If you need your data to appear immediately, use the following syntax:
<pre>
$doc->getSolrConnection()->commit();
</pre>
If you need to deal with multiple solr indexes, it's often best to define a model for
each index you're dealing with. To do this we extend ASolrDocument in the same way that we would extend CActiveRecord when defining a model
For example:
<pre>
class Job extends ASolrDocument {
	/**
	 * Required for all ASolrDocument sub classes
	 * @see ASolrDocument::model()
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	/**
	 * @return ASolrConnection the solr connection to use for this model
	 */
	public function getSolrConnection() {
		return Yii::app()->yourCustomSolrConnection;
	}
}
</pre>

#### Searching solr

To find documents in solr, we use the following methods:
<ul>
	<li>{@link ASolrDocument::find()}</li>
	<li>{@link ASolrDocument::findAll()}</li>
	<li>{@link ASolrDocument::findByAttributes()}</li>
	<li>{@link ASolrDocument::findAllByAttributes()}</li>
	<li>{@link ASolrDocument::findByPk()}</li>
	<li>{@link ASolrDocument::findAllByPk()}</li>
</ul>

The most useful of these methods are find() and findAll(). Both these methods take a criteria parameter, this criteria parameter should be an instance of {@link ASolrCriteria}.
Example: Find all documents with the name "test"
<pre>
$criteria = new ASolrCriteria;
$criteria->query = "name:test"; // lucene query syntax
$docs = ASolrDocument::model()->findAll($criteria);
</pre>
Alternative method:
<pre>
$docs = ASolrDocument::model()->findAllByAttributes(array("name" => "test"));
</pre>


Example: Find a job with the unique id of 123
<pre>
$job = Job::model()->findByPk(123);
</pre>
Example: Find the total number of jobs in the index
<pre>
$criteria = new ASolrCriteria;
$criteria->query = "*"; // match everything
$total = Job::model()->count($criteria); // the total number of jobs in the index
</pre>

#### Removing items from the index
To remove an item from the index, use the following syntax:
<pre>
$job = Job::model()->findByPk(234);
$job->delete();
</pre>