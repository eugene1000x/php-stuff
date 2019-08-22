<?php


namespace eugenejonas\php_stuff\observer;


use \eugenejonas\php_stuff\phpunit\MyTestCase;


class ObservableImpl
{
	use Observable;
	
	
	public function triggerEvent()
	{
		$this->notifyObservers(null);
	}
}

class UnitTest_Observable extends MyTestCase
{
	private $observable;
	
	
	protected function customSetUp(): void
	{
		$this->observable = new ObservableImpl();
	}
	
	public function test_attaching_same_observer_twice__with_unique_flag()
	{
		$observer = $this->createMock(Observer::class);
		$observer->expects($this->once())
		         ->method('onEvent');
		
		$this->observable->attachObserver($observer);
		$this->observable->attachObserver($observer);
		$this->observable->triggerEvent();
	}
	
	public function test_attaching_different_observers__with_unique_flag()
	{
		$observer1 = $this->createMock(Observer::class);
		$observer1->expects($this->once())
		          ->method('onEvent');
		
		$observer2 = $this->createMock(Observer::class);
		$observer2->expects($this->once())
		          ->method('onEvent');
		
		$this->assertFalse($observer1 === $observer2);
		
		$this->observable->attachObserver($observer1);
		$this->observable->attachObserver($observer2);
		$this->observable->triggerEvent();
	}
	
	public function test_attaching_same_observer_twice__without_unique_flag()
	{
		$observer = $this->createMock(Observer::class);
		$observer->expects($this->exactly(2))
		         ->method('onEvent');
		
		$this->observable->attachObserver($observer, false);
		$this->observable->attachObserver($observer, false);
		$this->observable->triggerEvent();
	}
	
	public function test_attaching_different_observers__without_unique_flag()
	{
		$observer1 = $this->createMock(Observer::class);
		$observer1->expects($this->once())
		          ->method('onEvent');
		
		$observer2 = $this->createMock(Observer::class);
		$observer2->expects($this->once())
		          ->method('onEvent');
		
		$this->assertFalse($observer1 === $observer2);
		
		$this->observable->attachObserver($observer1, false);
		$this->observable->attachObserver($observer2, false);
		$this->observable->triggerEvent();
	}
}
