<?php
/**
 * @file Response.php
 * Provides the Response class for returning the json response
 * Lang en
 * Reviewstatus: 2021-10-30
 * Localization: none
 * Documentation: complete
 * Tests:Unit/Elements/Response/ResponseTest.php
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: complete
 */

namespace Sunhill\InfoMarket\Marketeers\Response;

use \StdClass;
use Sunhill\InfoMarket\Marketeers\MarketeerException;

class Response
{

    protected $elements;
    
    public function __construct()
    {
        $this->elements = new StdClass();
    }
    
    protected function setElement(string $name,$value)
    {
        $this->elements->$name = $value;
    }
    
    /**
     * Checks if a update field was given. If not assume asap
     */
    protected function checkUpdate()
    {
        if (!$this->hasElement('update')) {
            $this->setElement('update','asap');
        }
    }
    
    /**
     * Returns the json response
     * @return string The response as a json string
     */
    public function get(): string
    {
        $this->checkUpdate();
        return json_encode($this->elements);
    }
    
    /**
     * Returns the response as a StdClass
     * The response as a StdClass
     */
    public function getStdClass(): StdClass
    {
        $this->checkUpdate();
        return $this->elements;    
    }
    
    /**
     * Returns if a specific element is defined in the response. Normally only for
     * debugging and testing purposed
     * @param string $element
     * @return bool
     */
    public function hasElement(string $element): bool
    {
        return property_exists($this->elements,$element);    
    }
    
    public function getElement(string $element)
    {
        return $this->elements->$element;    
    }
    
    /**
     * Inidcates that the request was successful
     * @return Response
     */
    public function OK(): Response
    {
        $this->setElement('result','OK');
        return $this;        
    }
    
    /**
     * Indicates that the request failed
     * @return Response
     */
    public function failed(): Response
    {
        $this->setElement('result','FAILED');
        return $this;    
    }
    
    /**
     * Sets the request string
     * @param string $request
     * @return Response
     */
    public function request(string $request): Response
    {
        $this->setElement('request',$request);
        return $this;
    }
    
    /**
     * Sets the type and subtype (if neccessary)
     * First it checks if the type exists then if the combination is valid
     * @param string $type
     * @param unknown $subtype
     * @throws MarketeerException
     * @return Response
     */
    public function type(string $type,$subtype=null): Response
    {
        $type = ucfirst(strtolower($type));
        switch ($type) {
            case 'Array':
            case 'Record':
                if (is_null($subtype)) {
                    throw new MarketeerException("An array or record needs a subtype. None given.");
                }
                if (($type == 'Array') && ($subtype == 'Array')) {
                    throw new MarketeerException("No nested arrays allowed.");                    
                }
                if (($type == 'Array') &&
                    !(in_array(ucfirst(strtolower($subtype)),
                        ['Integer','Float','String','Boolean','Date','Time','Datetime','Record']))) {
                        throw new MarketeerException("Unknown type for array: '$subtype");
                    }
                $this->setElement('subtype',$subtype);
            case 'Integer':
            case 'Float':
            case 'String':
            case 'Boolean':
            case 'Date':
            case 'Time':
            case 'Datetime':
                $this->setElement('type',$type);
                break;
            default:
                throw new MarketeerException("Unknown type '$type'.");
        }
        return $this;   
    }
    
    /**
     * Sets the unit and unit_int field according to the given (internal) unit
     * @param string $unit
     * @throws MarketeerException
     * @return Response
     */
    public function unit(string $unit): Response
    {
        switch ($unit) {
            case 's':       
            case 'K':
            case 'p':
            case 'm':
            case 'c':
            case 'l':
            case 'M':
            case 'G':
            case 'T':
            case 'P':
            case 'd':
            case 'C':
            case ' ':
                $this->setElement('unit_int',$unit);
                $this->setUnit($unit);
                break;
            default:
                throw new MarketeerException("Unkown unit '$unit'.");
        }
        return $this;        
    }
    
    /**
     * Sets the unit field depending of unit_int
     * @param string $unit
     */
    protected function setUnit(string $unit)
    {
        switch ($unit) {
            case 's':
            case 'm':
                $this->setElement('unit',$unit);
                break;
            case ' ':
                $this->setElement('unit','');
                break;
            case 'C':
                $this->setElement('unit','°C');
                break;
            case 'p':
                $this->setElement('unit','mmHg');
                break;
            case 'c':
                $this->setElement('unit','cm');
                break;
            case 'l':
                $this->setElement('unit','lx');
                break;
            case 'M':
                $this->setElement('unit','MB');
                break;
            case 'G':
                $this->setElement('unit','GB');
                break;
            case 'T':
                $this->setElement('unit','TB');
                break;
            case 'P':
                $this->setElement('unit','%');
                break;
        }        
    }

    public function update(string $key)
    {
        switch ($key) {
            case 'asap':
            case 'second':
            case 'minute':
            case 'hour':
            case 'day':
            case 'late':
                $this->setElement('update',$key);  
                break;
            default:
                throw new MarketeerException("Unkown update frequency '$key'.");
        }
        return $this;
    }
    
    /**
     * Sets the semantic and semantic_int field according to the given (internal) semantic vaoue
     * @param string $unit
     * @throws MarketeerException
     * @return Response
     */
    public function semantic(string $unit): Response
    {
        switch ($unit) {
            case 'temp':
            case 'air_temp':
            case 'uptime':
            case 'number':
            case 'name':
            case 'capacity':
                $this->setElement('semantic_int',$unit);
                $this->setSemantic($unit);
                break;
            default:
                throw new MarketeerException("Unkown semantic meaning '$unit'.");
        }
        return $this;
    }
    
    /**
     * Sets the semantic field depending of unit_int
     * @param string $unit
     */
    protected function setSemantic(string $unit)
    {
        switch ($unit) {
            case 'temp':
                $this->setElement('semantic',$this->translate('Temperature'));
                break;
            case 'air_temp':
                $this->setElement('semantic',$this->translate('Air temperature'));
                break;
            case 'uptime':
                $this->setElement('semantic',$this->translate('Uptime'));
                break;
            case 'number':
                $this->setElement('semantic',$this->translate('Number'));
                break;
            case 'capacity':
                $this->setElement('semantic',$this->translate('Capacity'));
                break;
                
        }
    }

    /**
     * Tries to translate the given text in the current language. If no translation is found
     * the original is returned
     * @param string $text
     * @return \Sunhill\InfoMarket\Elements\Response\string
     */
    protected function translate(string $text)
    {
        return $text;    
    }

    /**
     * Sets the value and at the same time the human_readable_value depending on unit which
     * has to be set before. 
     * @param unknown $value
     * @throws MarketeerException
     * @return Response
     */
    public function value($value): Response
    {
        $this->setElement('value',$value);
        if (property_exists($this->elements,'unit_int')) {
            switch ($this->elements->unit_int) {
                case 'd':
                    $this->setElement('human_readable_value',$this->getDuration($value));
                    break;
                case 'K':
                    $this->setElement('human_readable_value',$this->getCapacity($value));
                    break;
                case ' ':
                    $this->setElement('human_readable_value',$value);
                    break;
                default:
                    $this->setElement('human_readable_value',$value.' '.$this->elements->unit);                 
            }
        } else {
            throw new MarketeerException("Unit has to be set before value.");
        }
        return $this;
    }
    
    public function errorCode(string $code): Response
    {
        $this->setElement('error_code',$code);
        return $this;
    }
    
    public function errorMessage(string $message): Reponse
    {
        $this->setElement('error_message',$this->translate($message));
        return $this;
    }
    
    public function infoNotFound(): Response
    {
       return $this->errorCode('INFONOTFOUND')->errorMessage('The information was not found.');
    }
    
    /**
     * When the internal unit marks a duration the best duration is calculated
     * @param unknown $timespan
     * @return string
     */
    protected function getDuration($timespan)
    {
        $seconds = $timespan%60;
        $timespan = intdiv($timespan,60);
        $minutes = $timespan%60;
        $timespan = intdiv($timespan,60);
        $hours = $timespan%24;
        $timespan = intdiv($timespan,24);
        $days = $timespan%365;
        $years = intdiv($timespan,365);
        if ($years > 0) {
            return $years.' '.(($years == 1)?$this->translate('year'):$this->translate('years')).
            ' '.$this->translate('and').' '.$days.' '.(($days == 1)?$this->translate('day'):$this->translate('days'));
        } elseif ($days > 0) {
            return $days.' '.(($days == 1)?$this->translate('day'):$this->translate('days')).
            ' '.$this->translate('and').' '.$hours.' '.(($hours == 1)?$this->translate('hour'):$this->translate('hours'));
        } elseif ($hours > 0) {
            return $hours.' '.(($hours == 1)?$this->translate('hour'):$this->translate('hours')).
            ' '.$this->translate('and').' '.$minutes.' '.(($minutes == 1)?$this->translate('minute'):$this->translate('minutes'));
        } elseif ($minutes > 0) {
            return $minutes.' '.(($minutes == 1)?$this->translate('minute'):$this->translate('minutes')).
            ' '.$this->translate('and').' '.$seconds.' '.(($seconds == 1)?$this->translate('second'):$this->translate('seconds'));
        } else {
            return $seconds.' '.(($seconds == 1)?$this->translate('second'):$this->translate('seconds'));
        }        
    }

    /**
     * If the internal unit marks a capacity the best capacity is calculated
     * @param unknown $value
     * @return string
     */
    protected function getCapacity($value)
    {
        if ($value >= 1000*1000*1000*1000) {
            return round($value/(1000*1000*1000*1000),1).' TB';
        } elseif ($value >= 1000*1000*1000) {
            return round($value/(1000*1000*1000),1).' GB';                
        } elseif ($value >= 1000*1000) {
            return round($value/(1000*1000),1).' MB';
        } elseif ($value >= 1000) {
            return round($value/1000,1).' kB';
        } else {
            return $value.' Byte';
        }
    }
    
    public function number($number) 
    {
        return $this->OK()->type('Integer')->unit(' ')->semantic('number')->value($number);        
    }

    public function capacity($number)
    {
        return $this->OK()->type('Integer')->unit('c')->semantic('number')->value($number);
    }
    
}
