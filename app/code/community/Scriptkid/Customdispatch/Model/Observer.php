<?php

class Scriptkid_Customdispatch_Model_Observer
{
	//this is hook to Magento's event dispatched before action is run
	public function hookToControllerActionPreDispatch($observer)
	{
		

		//we compare action name to see if that's action for which we want to add our own event
		if($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') 
		{
			//We are dispatching our own event before action ADD is run and sending parameters we need
			Mage::dispatchEvent("add_to_cart_before", array('request' => $observer->getControllerAction()->getRequest()));
		}
		if(strpos($observer->getEvent()->getControllerAction()->getFullActionName(), 'noRoute') !== false) {
			Mage::dispatchEvent("cms_nopage_found", array('request' => $observer->getControllerAction()->getRequest()));
		}
	}

	public function hookToControllerActionPostDispatch($observer)
	{
		 //we compare action name to see if that's action for which we want to add our own event 
		if($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') 
		{
			//We are dispatching our own event before action ADD is run and sending parameters we need
			Mage::dispatchEvent("add_to_cart_after", array('request' => $observer->getControllerAction()->getRequest()));
		} elseif($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_delete') {
			Mage::dispatchEvent("cart_delete_item_after", array('request' => $observer->getControllerAction()->getRequest()));
		}
	}

	public function hookToAddToCartBefore($observer) 
	{   
			//Hooking to our own event
		$request = $observer->getEvent()->getRequest()->getParams();
			// do something with product
		Mage::log("Product ".$request['product']." will be added to cart.");
	}

	public function hookToAddToCartAfter($observer) 
	{
			//Hooking to our own event
		$request = $observer->getEvent()->getRequest()->getParams();
			// do something with product
		Mage::log("Product ".$request['product']." is added to cart.");
	}

	public function layoutDebug($observer)
	{
		
		$req  = Mage::app()->getRequest();
		$info = sprintf(
			"\nRequest: %s\nFull Action Name: %s_%s_%s\nHandles:\n\t%s\nUpdate XML:\n%s",
			$req->getRouteName(),
			$req->getRequestedRouteName(),      //full action name 1/3
			$req->getRequestedControllerName(), //full action name 2/3
			$req->getRequestedActionName(),     //full action name 3/3
			implode("\n\t",$observer->getLayout()->getUpdate()->getHandles()),
			$observer->getLayout()->getUpdate()->asString()
		);

		// Force logging to var/log/layout.log
		Mage::log($info, Zend_Log::INFO, 'layout.log', true);
	}

}