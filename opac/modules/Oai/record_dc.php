<?php
/** \file
 * \brief Definition of Dublin Core handler.
 *
 * It is not working as it does not provide any content to the metadata node. It only included
 * to demonstrate how a new metadata can be supported. For a working
 * example, please see record_rif.php.
 *
 * @author: Ismail Fahmi, ismail.fahmi@gmail.com
 *
 * \sa oaidp-config.php 
	*/

function create_metadata($outputObj, $cur_record, $identifier, $setspec, $db) {
		$metadata_node = $outputObj->create_metadata($cur_record);

    $oai_node = $outputObj->addChild($metadata_node, "oai_dc:dc");
	$oai_node->setAttribute("xmlns:oai_dc","http://www.openarchives.org/OAI/2.0/oai_dc/");
	$oai_node->setAttribute("xmlns:dc","http://purl.org/dc/elements/1.1/");
	$oai_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
	$oai_node->setAttribute("xsi:schemaLocation", "http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd");

	$record 	= get_record($identifier, $db);
	$tag_detail = get_tag_detail($identifier, $db);
	$files 		= get_files($identifier, $db);
	
	
	if (!empty($record['dc_title'])) 				$outputObj->addChild($oai_node,'dc:title', htmlspecialchars($record['dc_title']));
	if (!empty($record['dc_creator'])) 				$outputObj->addChild($oai_node,'dc:creator', htmlspecialchars($record['dc_creator']));
	if (!empty($record['dc_subject'])) 				$outputObj->addChild($oai_node,'dc:subject', htmlspecialchars($record['dc_subject']));
	if (!empty($record['dc_publisher'])) 			$outputObj->addChild($oai_node,'dc:publisher', htmlspecialchars($record['dc_publisher']));
	if (!empty($record['dc_publishYear'])) 			$outputObj->addChild($oai_node,'dc:publishYear', htmlspecialchars($record['dc_publishYear']));
	if (!empty($record['dc_description_1'])) 		$outputObj->addChild($oai_node,'dc:description', htmlspecialchars(strip_tags(html_entity_decode($record['dc_description_1']))));
	
	foreach ($tag_detail as $tag_detail){
		if (!empty($tag_detail['Tag'])) 			$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($tag_detail['Tag']));
		if (!empty($tag_detail['Ind1'])) 			$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($tag_detail['Ind1']));
		if (!empty($tag_detail['Ind2'])) 			$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($tag_detail['Ind2']));
		if (!empty($tag_detail['Value'])) 			$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($tag_detail['Value']));
		if (!empty($tag_detail['SubRuas'])) 		$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($tag_detail['SubRuas']));
		if (!empty($tag_detail['Val'])) 			$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($tag_detail['Val']));
	}
	
	if (!empty($record['dc_description_2'])) 		$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($record['dc_description_2']));
	if (!empty($record['dc_description_3'])) 		$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($record['dc_description_3']));
	if (!empty($record['dc_description_4'])) 		$outputObj->addChild($oai_node,'dc:description', htmlspecialchars($record['dc_description_4']));
	if (!empty($record['dc_date'])) 				$outputObj->addChild($oai_node,'dc:date', htmlspecialchars($record['dc_date']));
	if (!empty($record['dc_format'])) 				$outputObj->addChild($oai_node,'dc:format', htmlspecialchars($record['dc_format']));
	if (!empty($record['dc_language'])) 			$outputObj->addChild($oai_node,'dc:language', htmlspecialchars($record['dc_language']));
	if (!empty($record['dc_identifier_1'])) 		$outputObj->addChild($oai_node,'dc:identifier', htmlspecialchars($record['dc_identifier_1']));
	if (!empty($record['dc_identifier_2'])) 		$outputObj->addChild($oai_node,'dc:identifier', htmlspecialchars($record['dc_identifier_2']));
	if (!empty($record['dc_identifier_3'])) 		$outputObj->addChild($oai_node,'dc:identifier', htmlspecialchars($record['dc_identifier_3']));
	if (!empty($record['dc_identifier_4'])) 		$outputObj->addChild($oai_node,'dc:identifier', htmlspecialchars($record['dc_identifier_4']));
	
	foreach ($files as $files){
		if (!empty($files['url'])) 					$outputObj->addChild($oai_node,'dc:identifier', htmlspecialchars($files['url']));
		if (!empty($files['flash'])) 				$outputObj->addChild($oai_node,'dc:identifier', htmlspecialchars($files['flash']));
	}
		
}

function get_record ($identifier, $db){
	
	$query = 'SELECT * FROM t_oai_dc WHERE identifier=' .$identifier;

	$res = $db->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$r = $res->execute();
 	if ($r===false) {
		if (SHOW_QUERY_ERROR) {
			echo __FILE__.','.__LINE__."<br />";
			echo "Query: $query<br />\n";
			print_r($db->errorInfo());
			exit();
		} else {
			return array();
		}		
	} else {
		$record = $res->fetch(PDO::FETCH_ASSOC);
		return $record;
	}
}

function get_tag_detail($identifier, $db) {
	
	$query = 'SELECT 
				B.Tag AS \'Tag\',
				B.Indicator1 AS \'Ind1\',
				B.Indicator2 AS \'Ind2\',
				B.Value AS \'Value\',
				C.SubRuas AS \'SubRuas\',
				C.Value AS \'Val\'
				FROM
				t_oai_dc A
				Left Join catalog_ruas B ON A.id_record = B.CatalogId
				Left Join catalog_subruas C ON B.ID = C.RuasID
				where A.dc_subject = A.dc_subject
				AND A.identifier='.$identifier;
	
	$res = $db->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$r = $res->execute();
	if ($r===false) {
		if (SHOW_QUERY_ERROR) {
			echo __FILE__.','.__LINE__."<br />";
			echo "Query: $query<br />\n";
			print_r($db->errorInfo());
			exit();
		} else {
			return array();
		}		
	} else {
		$records = array();
		$hasNext = 1;
		while($hasNext){
			$record = $res->fetch(PDO::FETCH_ASSOC);
			if ($record){
				array_push($records, $record);
			} else {
				$hasNext = 0;
			}
		}

		return $records;
	}
}

function get_files($identifier, $db) {
	
	$query = 'SELECT 
				B.FileURL AS \'url\',
				B.FileFlash AS \'flash\'
				FROM
				t_oai_dc A
				Left Join catalogfiles B ON A.id_record = B.Catalog_id
				where A.dc_subject = A.dc_subject
				AND A.identifier='.$identifier;
	
	$res = $db->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$r = $res->execute();
	if ($r===false) {
		if (SHOW_QUERY_ERROR) {
			echo __FILE__.','.__LINE__."<br />";
			echo "Query: $query<br />\n";
			print_r($db->errorInfo());
			exit();
		} else {
			return array();
		}		
	} else {
		$records = array();
		$hasNext = 1;
		while($hasNext){
			$record = $res->fetch(PDO::FETCH_ASSOC);
			if ($record){
				array_push($records, $record);
			} else {
				$hasNext = 0;
			}
		}

		return $records;
	}
}
