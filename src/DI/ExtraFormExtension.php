<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\DI;

use Chomenko\ExtraForm\Extend\Date\DateEvent;
use Chomenko\ExtraForm\Extend\Pair\PairEvent;
use Chomenko\ExtraForm\FormEvents;
use Chomenko\ExtraForm\FormFactory;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

class ExtraFormExtension extends CompilerExtension
{

	const TAG_EVENT = 'extraForm.events';

	/**
	 * @var array
	 */
	private $defaultEvents = [
		PairEvent::class,
		DateEvent::class
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$events = $builder->addDefinition($this->prefix("FormEvents"))
			->setFactory(FormEvents::class);

		foreach ($this->defaultEvents as $class) {
			$events->addSetup("addEvent", [new $class()]);
		}

		$builder->addDefinition($this->prefix("FormFactory"))
			->setFactory(FormFactory::class);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$event = $builder->getDefinition($this->prefix("FormEvents"));
		foreach ($builder->findByTag(self::TAG_EVENT) as $name => $item) {
			$event->addSetup("addEvent", [$builder->getDefinition($name)]);
		}
	}

	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Compiler $compiler){
			$compiler->addExtension('ExtraForm', new ExtraFormExtension());
		};
	}

}