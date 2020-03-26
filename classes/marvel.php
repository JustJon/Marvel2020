<?php
/**********************************************************************************
marvel.php
This class is for accessing the Marvel API 
Copyright Jonathan Lazar 2020
**********************************************************************************/
class Marvel
{
	private $oauth_token;
	private $oauth_token_secret;

	private $link;

	//Constructor
	function __construct($consumertoken, $consumersecrettoken)
	{
		$this->oauth_token=$consumertoken;
		$this->oauth_token_secret=$consumersecrettoken;

		$this->link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DBNAME);
                if (!$this->link) {
                        die('Could not connect: ' . mysqli_error($this->link));
                }
	}

	//Destructor
	function __destruct()
	{
	}

	//Get single character information
	function getCharacter($name) {

		$ts=time();
		$key=$ts.$this->oauth_token_secret.$this->oauth_token;
		$md5key=md5($key);

		$params['name']=$name;
		$params['ts']=$ts;
		$params['apikey']=$this->oauth_token;
		$params['hash']=md5($ts.$this->oauth_token_secret.$this->oauth_token);

		$url = 'http://gateway.marvel.com:80/v1/public/characters';
		$url .= '?'.http_build_query($params);
		$url .= '&';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$buffer=curl_exec($ch);
		curl_close($ch);

		$data = $this->processJson($buffer);

		if (empty($data['name'])) {
			$formatted='';
		} else {
                	$formatted = $this->formatData($data);
		}

                return $formatted;
	}

	//Get multiple characters from Marvel API starting with a specific letter
	//alpha - single alphabetic character
	function getCharacters($alpha) {

		$characters = [];
		$i = 0;
		$totalCharacters = 0;

		$dbData = $this->getRecords('characters', $alpha);

		if (count($dbData) > 0) {
			foreach ($dbData as $inCh) {
                        	$characters[$i]['id'] = $inCh['id'];
                        	$characters[$i]['name'] = $inCh['name'];
                        	$characters[$i]['description'] = $inCh['description'];
                        	$characters[$i]['thumbnail'] = $inCh['thumbnail'];

                        	$i++;
                        }

		} else {

                	$ts=time();
                	$key=$ts.$this->oauth_token_secret.$this->oauth_token;
                	$md5key=md5($key);
	
                	$params['nameStartsWith']=$alpha;
                	$params['ts']=$ts;
                	$params['apikey']=$this->oauth_token;
                	$params['hash']=md5($ts.$this->oauth_token_secret.$this->oauth_token);
                	$params['limit']=50;
                	$params['offset']=0;
	
			do {
				$url = 'http://gateway.marvel.com/v1/public/characters';
                		$url .= '?'.http_build_query($params);
	
                		$ch = curl_init($url);
                		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                		$buffer=curl_exec($ch);
                		curl_close($ch);
		
				$incomingCharacters = json_decode($buffer);
	
				$totalCharacters = $incomingCharacters->data->total;
	
				foreach ($incomingCharacters->data->results as $inCh) {
					$characters[$i]['id'] = $inCh->id;
					$characters[$i]['name'] = $inCh->name;
					$characters[$i]['description'] = $inCh->description;
					$characters[$i]['thumbnail'] = $inCh->thumbnail->path;
					$this->saveRecord('characters', $characters[$i]);

					$i++;
				}
	
				$params['offset'] += $params['limit'];
	
			} while (count($characters) < $totalCharacters);

		}

		return $characters;

        }

	//Get multiple series titles from Marvel API starting with a specific letter
	//alpha - single alphabetic character
	function getSeries($alpha) {

		$series = [];
		$i = 0;
		$totalSeries = 0;

		$dbData = $this->getRecords('series', $alpha);

                if (count($dbData) > 0) {
                        foreach ($dbData as $inSe) {
                                $series[$i]['id'] = $inSe['id'];
                                $series[$i]['title'] = $inSe['title'];
                                $series[$i]['description'] = $inSe['description'];
                                $series[$i]['thumbnail'] = $inSe['thumbnail'];

                                $i++;
                        }

                } else {

                	$ts=time();
                	$key=$ts.$this->oauth_token_secret.$this->oauth_token;
                	$md5key=md5($key);
	
                	$params['titleStartsWith']=$alpha;
                	$params['ts']=$ts;
                	$params['apikey']=$this->oauth_token;
                	$params['hash']=md5($ts.$this->oauth_token_secret.$this->oauth_token);
                	$params['limit']=50;
                	$params['offset']=0;
	
	
			do {
                		$url = 'http://gateway.marvel.com/v1/public/series';
                        	$url .= '?'.http_build_query($params);
	
                        	$ch = curl_init($url);
                        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        	$buffer=curl_exec($ch);
                        	curl_close($ch);
	
                        	$incomingSeries = json_decode($buffer);
	
                        	$totalSeries = $incomingSeries->data->total;
                        	foreach ($incomingSeries->data->results as $inSe) {
                                	$series[$i]['id'] = $inSe->id;
                                	$series[$i]['title'] = $inSe->title;
                                	$series[$i]['description'] = $inSe->description;
                                	$series[$i]['thumbnail'] = $inSe->thumbnail->path;
		
					$this->saveRecord('series', $series[$i]);

                                	$i++;
                        	}
	
                        	$params['offset'] += $params['limit'];
	
                	} while (count($series) < $totalSeries);
		}

                return $series;

        }


	//Format single character data
	//data - array of character data
        private function formatData($data) {

                $output = '<div id="bio">';

		$output .= '<span id="title">'.$data['name'].'</span>';
		$output .= '<br style="clear:left">';
		$output .= '<img src="'.$data['img'].'" id="img">';
		$output .= $data['desc'];
		$output .= '<br style="clear:both"><div id="attrib">'.$data['attrib'].'</div>';
                $output .= '</div>';

                return $output;
        }


        //Retrieve stored data that is less than 5 days old
	//table - database table to select from
        //alpha - single alphabetic character
	function getRecords($table, $alpha) {

		switch ($table) {
		case 'series':
			$name = 'title';
			break;
		case 'characters':
			$name = 'name';
			break;
		}

		$sql = 'SELECT id, '.$name.', description, thumbnail FROM '.$table.' WHERE '.$name.' LIKE "'.$alpha.'%" AND created_at > DATE_SUB(NOW(), INTERVAL 5 DAY) ';
		$result=mysqli_query($this->link, $sql);
		$res=mysqli_fetch_all($result, MYSQLI_ASSOC);

                return $res;

	}

	//Store record from Marvel API for faster retieval on future lookups
	//table - database table to select from
        //alpha - single alphabetic character
	function saveRecord($table, $data) {

		$sql = 'REPLACE INTO '.$table
			.' SET ';
		foreach ($data as $key => $val) {
			$sql .= $key .' = "'. $val .'", ';
		}	
		$sql = substr($sql, 0, -2);

		$result=mysqli_query($this->link, $sql);

		return;

	}
}
