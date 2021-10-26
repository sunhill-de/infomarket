# InfoMarket

## What is InfoMarket?
InfoMarket provides an unified interface to manager the information flow from and to different kind of sources and destinations. The informations are accessed through a laravel facade (InfoMarket) and use one or more unique information identifier (like "system.hardware.hdd.capacity")

## Core concepts
1. The InfoMarket should provide as few methods as possible, all kinds of informations should be accessed through them (see [facade interface](facade))
2. The result of any method should be a json-object (or StdClass in php). One field must be "state" which can be "OK" or "FAILED". (see [results](results))
3. All informations are accessed through one ore more hirarchic strings ("system.hardware.hdd.capacity") (see [hirarchy](hirachy) and [installation of InformationCollectors](informationcollectors) )
4. Astreriks are allowed in the information identifier too ("system.hardware.`*`") or ("home.*.light") (see [catchalls](catchalls))
5. Value, unit and semantic meaning are seperated entities (like "3", "seconds", "Duration") (see [values](values), [units](units) and [semantic meaning](semantic))
6. Units are normalized (A °C is always a °C) (see [units](units))
7. Semantic meaning should be normalized as far as possible and they should be hirarchic (meaning "running time" is a sub class of "duration") (see [semantic meaning](semantic))

## Information flow
* All requests are performed through the InfoMarket [facade](facade). 
* The information identifier is routed to one ore more InformationCollector
* The InformationCollector returns the requested json answer
* The InfoMarket assembles the results to one result (if there are more than one) and returns it
