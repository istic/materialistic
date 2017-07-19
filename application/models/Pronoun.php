<?PHP

class Pronoun extends MY_Model {

	function __construct() {
        // Call the Model constructor
        parent::__construct();
		
		$this->options = array(
			'MALE'   => 'He',
			'FEMALE' => 'She',
			'SPIVAK' => 'Ey (Spivak)',
			'NONE'   => 'They'
		);
		
		$this->pronouns = array(
			'MALE' => array(
				'nominative'             => 'he',
				'objective'              => 'his',
				'possesssive_determiner' => 'his',
				'possessive_pronoun'     => 'his',
				'reflexive'              => 'himself',
			),
			
			'FEMALE' => array(
				'nominative'             => 'she',
				'objective'              => 'her',
				'possesssive_determiner' => 'her',
				'possessive_pronoun'     => 'hers',
				'reflexive'              => 'herself',
			),
			
			'SPIVAK' => array(
				'nominative'             => 'ey',
				'objective'              => 'em',
				'possesssive_determiner' => 'eir',
				'possessive_pronoun'     => 'eirs',
				'reflexive'              => 'emself',
			),
			
			'NONE' => array(
				'nominative'             => 'they',
				'objective'              => 'them',
				'possesssive_determiner' => 'their',
				'possessive_pronoun'     => 'theirs',
				'reflexive'              => 'themself',
			),

		);

    }

	
    function list_options(){
		return $this->options;
	}

    function pronoun($type, $gender){
		return $this->pronouns[$gender][$type];
	}
	
    function nominative($gender){
		return $this->pronoun('nominative', $gender);
	}
	
    function objective($gender){
		return $this->pronoun('objective', $gender);
	}
		
    function possessive_pronoun($gender){
		return $this->pronoun('possessive_pronoun', $gender);
	}
	
    function possesssive_determiner($gender){
		return $this->pronoun('possesssive_determiner', $gender);
	}
	
    function reflexive($gender){
		return $this->pronoun('reflexive', $gender);
	}
	
}