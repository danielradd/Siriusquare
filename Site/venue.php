<?
/*
* Daniel Radd
* danielraddps@gmail.com
*/
class Venue {

    var $id;
    var $lat;
	var $lng;
	var $cont;
	var $cat;
	
    function Venue($id, $lat, $lng, $cont, $cat)
    {
        $this->id = $id;
        $this->lat = $lat;
		$this->lng = $lng;
		$this->cont = $cont;
		$this->cat = $cat;
		
    }
	
	function longitude()
    {
        return $this->lng;
    }
	
	function latitude()
    {
        return $this->lat;
    }
	
	function categoria()
	{
		return $this->cat;
	}
	
	function contador()
	{
		return $this->cont;
	}

} // Fim da classe Venue
?>