<?php
    class Test
    {
	    const rtrim = "/'/";
	    const space = "/\s+/";
        const ltrim = "/word='/";
        const pattern = "/word='[^']+/";

        public $error = '';
        public $morph = [];

        protected $value = [];      

        function __construct($val)
        {
            $array = preg_split(Test::space, addslashes($val), 20);
	        foreach ($array as $value)
            {
		        $this->value[] = urlencode($value);
	        }
        }
	
	    public function ShowMorph()
	    {
	        if (!$this->morph)
		        echo 'Empty <br>';

	        foreach ($this->morph as $value)
            {
		        echo $value . '<br>';
	        }
	    }
        public function ShellCommand($command)
        {
	        foreach($this->value as $value)
	        {
                $presult;
            	$command = $command . ' ' . $value . ' 2>&1';
            	if (($presult = shell_exec($command)) === NULL)
		            continue;

            	$this->ParseResult($presult);
	        }
	
	        if (count($this->morph) > 1)
	    	    $this->morph = array_unique($this->morph, SORT_STRING);
        }

        protected function ParseResult($presult)
        {
            if (preg_match_all(Test::pattern, $presult, $match) == 0)
            {
		        $this->error['error'] = $presult;
                return;
            }

            $array = [];
            foreach ($match[0] as $value)
            {
	            $array[] = rtrim(ltrim($value, Test::ltrim), 
					                           Test::rtrim);
            }
            $this->morph = array_merge($this->morph, 
				                        array_unique($array, SORT_STRING));
        }
    }
    if (isset($_POST['value']) && !empty($_POST['value']))
    {
        $obj = new Test($_POST['value']);

        $obj->ShellCommand('PYTHONIOENCODING=utf8 python3 test.py');
        if (count($obj->morph) > 0)
        {
            echo json_encode( $obj->morph, JSON_UNESCAPED_UNICODE );
        }
        else
        {
            echo json_encode( $obj->error, JSON_UNESCAPED_UNICODE );
        }
    }
?>
