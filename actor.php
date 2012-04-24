<?php
  global $api_key;
  $api_key = 'd269fbd71c790bdbebae323a284e9862';
  $actor = $_POST['actor'];
  $actor_compare = $_POST['actor_compare'];
  $actor_movies = getMoviesByActorID(getActorID($actor));
  $actor_compare_movies = getMoviesByActorID(getActorID($actor_compare));
  $intersect = findCommonMovies($actor_movies, $actor_compare_movies);
  print json_encode(array_values($intersect));
  
  function curl_get_file_contents($URL)
  {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents)
      return $contents;
    else
      return FALSE;
  }
  function generateAPIUrl($method, $query)
  {
    global $api_key;
    $method = urlencode($method);
    $query = urlencode($query);
    return ("http://api.themoviedb.org/2.1/" . $method . "/en/json/" . $api_key . "/" . $query);
  }
  function getAPIResults($method, $query)
  {
    $json = curl_get_file_contents(generateAPIURL($method, $query));
    return json_decode($json, true);
  }
  function getActorID($name)
  { 
    $actor_info = getAPIResults('Person.search', $name);
    return $actor_info[0]['id'];
  }
  function getMoviesByActorID($actor_id)
  {
    $actor_info = getAPIResults('Person.getInfo', $actor_id);
    $movies = array();
    foreach($actor_info[0]['filmography'] as $movie)
    {
      array_push($movies, $movie['name']);
    }
    return $movies;
  }
  function findCommonMovies($movies, $movies_compare)
  {
    return array_unique(array_intersect($movies, $movies_compare));
  }
?>