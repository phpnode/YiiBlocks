<?php

class AWebDavBrowserPlugin extends Sabre_DAV_Browser_Plugin {
	/**
     * Generates the html directory index for a given url
     *
     * @param string $path
     * @return string
     */
    public function generateDirectoryIndex($path) {
		$files = $this->server->getPropertiesForPath($path,array(
				'{DAV:}displayname',
				'{DAV:}resourcetype',
				'{DAV:}getcontenttype',
				'{DAV:}getcontentlength',
				'{DAV:}getlastmodified',
			),1);
		$processedFiles = $this->processFilesForDataProvider($files, $path);
		if ($path) {
			array_unshift($processedFiles,array(
										"id" => -1,
										"name" => "..",
										"displayName" => "..",
										"size" => null,
										"type" => "Parent Collection",
										"link" => CHtml::link("..",Yii::app()->controller->createUrl("/".Yii::app()->controller->route).$path."/..",array("class" => "icon folder")),
										"lastModified" => null,
									  ));
		}
		$dataProvider = new CArrayDataProvider($processedFiles,
								array(
									"keyField" => "name",
									"pagination" => false,
									"sort" => false,
								));
		Yii::app()->controller->render("packages.webdav.views.browserPlugin.directoryListing",
									   array(
											"dataProvider" => $dataProvider,
											"path" => $path,
									   ));
    	$parent = $this->server->tree->getNodeForPath($path);
	}
	/**
	 * Processes a list of files for display in a CArrayDataProvider
	 * @param array $files the files from the server
	 * @param string $path the path to the container directory
	 * @return array the files to be
	 */
	protected function processFilesForDataProvider(array $files, $path) {
		$rows = array(
			"directories" => array(),
			"files" => array(),
		);
		foreach($files as $k=>$file) {
        	if (rtrim($file['href'],'/')==$path) {
				// This is the current directory, we can skip it
				continue;
			}
        	list(, $name) = Sabre_DAV_URLUtil::splitPath($file['href']);
        	$type = null;
        	if (isset($file[200]['{DAV:}resourcetype'])) {
	            $type = $file[200]['{DAV:}resourcetype']->getValue();
    	        // resourcetype can have multiple values
	            if (!is_array($type)) {
					$type = array($type);
				}

            	foreach($type as $k=>$v) {
	                // Some name mapping is preferred
    	            switch($v) {
        	            case '{DAV:}collection' :
            	            $type[$k] = 'Collection';
                	        break;
                    	case '{DAV:}principal' :
	                        $type[$k] = 'Principal';
	                        break;
    	                case '{urn:ietf:params:xml:ns:carddav}addressbook' :
        	                $type[$k] = 'Addressbook';
            	            break;
                	    case '{urn:ietf:params:xml:ns:caldav}calendar' :
                    	    $type[$k] = 'Calendar';
                        	break;
                	}
	            }
    	        $type = implode(', ', $type);
        	}

			// If no resourcetype was found, we attempt to use
			// the contenttype property
			if (!$type && isset($file[200]['{DAV:}getcontenttype'])) {
				$type = $file[200]['{DAV:}getcontenttype'];
			}
			if (!$type) {
				$type = 'Unknown';
			}

        	$size = isset($file[200]['{DAV:}getcontentlength'])?(int)$file[200]['{DAV:}getcontentlength']:'';
        	$lastmodified = isset($file[200]['{DAV:}getlastmodified'])?$file[200]['{DAV:}getlastmodified']->getTime()->format(DateTime::ATOM):'';

        	$fullPath = Sabre_DAV_URLUtil::encodePath('/' . trim($this->server->getBaseUri() . ($path?$path . '/':'') . $name,'/'));

        	$displayName = isset($file[200]['{DAV:}displayname'])?$file[200]['{DAV:}displayname']:$name;
			if ($type == "Collection") {
				$class = "folder";
			}
			else {
				$class = "ext ".array_pop(explode(".",$name));
			}

			$rows[($type == "Collection" ? "directories" : "files")][] = array(
				"id" => $k,
				"name" => $name,
				"displayName" => $displayName,
				"fullPath" => $fullPath,
				"type" => $type,
				"size" => $size,
				"lastModified" => $lastmodified,
				"link" => CHtml::link(
							$displayName,
							rtrim(Yii::app()->controller->createUrl("/".Yii::app()->controller->route),"/")."/".$path."/".$name,
							array(
								"class" => $class." icon"
							)
						),
				);
	    }
		return array_merge($rows['directories'],$rows['files']);
	}
}