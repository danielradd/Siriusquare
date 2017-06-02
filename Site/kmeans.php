<?php
/*
* Daniel Radd
* danielraddps@gmail.com
*/
	include "venue.php";
	
/**
	* Esta função recebe um array de inteiros e o número de clusters para criar.
	* Ele retorna um array multidimensional contendo os dados originais organizados
	* em clusters.
	*
	* @param array $data
	* @param int $k
	*
	* @return array
	*/
	function kmeans($data, $k)
	{
			$cPositions = assign_initial_positions($data, $k);
			$clusters = array();
	
			while(true)
			{
				$changes = kmeans_clustering($data, $cPositions, $clusters);

				if(!$changes)
				{
					return kmeans_get_cluster_values($clusters, $data);
				}
				$cPositions = kmeans_recalculate_cpositions($cPositions, $data, $clusters);
			}
	}


	/**
	*
	*/
	function kmeans_clustering($data, $cPositions, &$clusters)
	{
		$nChanges = 0;
		foreach($data as $dataKey => $value)
		{
			$minDistance = null;
			$cluster = null;
			
			foreach($cPositions as $k => $position)
			{
				$distance = dist($value->latitude(), $value->longitude(),
				$position->latitude(), $position->longitude());
					
				if(is_null($minDistance) || $minDistance > $distance)
				{
					$minDistance = $distance;
					$cluster = $k;
				}
						
			}
			
			if(!isset($clusters[$dataKey]) || $clusters[$dataKey] != $cluster)
			{
				$nChanges++;
			}
			$clusters[$dataKey] = $cluster;
		}
	    
		return $nChanges;
	} // fim kmeans_clustering
	
	/*
	*
	*/
	function kmeans_recalculate_cpositions($cPositions, $data, $clusters)
	{
		$kValues = kmeans_get_cluster_values($clusters, $data);
		
		foreach($cPositions as $k => $position)
		{		
			$clat = empty($kValues[$k]) ? 0 : kmeans_avg_lat($kValues[$k]);
			$clng = empty($kValues[$k]) ? 0 : kmeans_avg_lng($kValues[$k]);
			$cPositions[$k] =  new Venue($k, $clat, $clng, $k, $k);
		}
		return $cPositions;
		
	}// fim kmeans_recalculate_cpositions
		
	/*
	*
	*/
	function kmeans_get_cluster_values($clusters, $data)
	{
		$values = array();
		foreach($clusters as $dataKey => $cluster)
		{
			$values[$cluster][] = $data[$dataKey];// grava objs na matriz
		}
		return $values;
	}
			
	
	/*
	*
	*/	
	function kmeans_avg_lat($values)
	{
		$n = count($values);
		for ($i=0;$i < count($values);$i++)
			$sum=$sum+ $values[$i]->latitude();
		return ($n == 0) ? 0 : $sum / $n;
	}

	/*
	*
	*/
	function kmeans_avg_lng($values)
	{
		$n = count($values);
		for ($i=0;$i < count($values);$i++)
			$sum=$sum+ $values[$i]->longitude();
		return ($n == 0) ? 0 : $sum / $n;
	}
			
	/**
	* Calcula a distancia entre dois pontos
	* Utiliza o calculo da Distancia Euclidiana
	*/
	function dist($p1, $p2, $q1, $q2)
	{
		$x = abs($p1 - $q1);
		$y = abs($p2 - $q2);
		//return round(sqrt(($x * $x) + ($y * $y)),2);
		return sqrt(($x * $x) + ($y * $y));//****
	}
			
	/**
	* Cria as posições iniciais para o dado
	* Número de clusters e dados.
	* @param array $data
	* @param int $k
	*
	* @return array
	*/
	function assign_initial_positions($data, $k)
	{
		for ($i=0;$i < count($data);$i++)
			$datalat[$i]=$data[$i]->latitude();
		
		for ($j=0;$j<count($data);$j++)
			$datalng[$j]=$data[$j]->longitude();
		
		$min1 = min($datalat);
		$min2 = min($datalng);
		$max1 = max($datalat);
		$max2 = max($datalng);
		
		//$int1 = ceil(abs($max1 - $min1) / $k);
		//$int2 = ceil(abs($max2 - $min2) / $k);
		$int1 = (abs($max1 - $min1) / $k);//****
		$int2 = (abs($max2 - $min2) / $k);//****
		
		while($k-- > 0)
		{
			$cPositions[$k] =  new Venue($k, ($min1 + $int1 * $k), ($min2 + $int2 * $k), $k, $k);
		}
		
		return $cPositions;	
	}
	
?>