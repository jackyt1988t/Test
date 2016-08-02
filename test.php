<?php
    class Test
    {
        const loop = 20;
        const size = 1024;
        const sleep = 1000;
	const rtrim = "/'/";
	const space = "/\s+/";
        const ltrim = "/word='/";
        const pattern = "/word='[^']+/";

        public $error = '';
        public $morph = [];

        protected $value = '';      

        function __construct($val)
        {
            $array = preg_split(Test::space, addslashes($val), 20);
	    foreach ($array as $value)
            {
		 $this->value .= ' ' . urlencode($value);
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
        public function StartFork($command)
        {
            $ppython = false;
            $presult;
            $command = $command . ' ' .  $this->value . ' 2>&1';
            if (($ppython = popen( $command, "r")) === false)
            {
                $this->error['error'] = "Ошибка при в-нии скрипта python";
                return;
            }
            $i = 0;
            $res = false;
            while(!feof($ppython))
            {
                //if ($i++ > Test::loop)
                    //usleep(Test::sleep);
                if (($res = fread($ppython, Test::size)) !== false)
                {
                    $presult .= $res;
                }
                else
                {
                    $this->error['error'] = 'ошибка при чтении данных скрипта python';
                    break;
                }
            }
            pclose($ppython);
            if (!$this->error)
                $this->ParseResult($presult);
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
            $this->morph = array_unique($array, SORT_STRING);
        }
    }
    if (isset($_POST['value']) && !empty($_POST['value']))
    {
        $obj = new Test($_POST['value']);

        $obj->StartFork('PYTHONIOENCODING=utf8 python3 test.py');
        if (!$obj->error)
        {
            echo json_encode( $obj->morph, JSON_UNESCAPED_UNICODE );
        }
        else
        {
            echo json_encode( $obj->error, JSON_UNESCAPED_UNICODE );
        }
    }
?>
