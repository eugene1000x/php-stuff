<?php


function error($message)
{
	
}

function enableAssertions()
{
	assert_options(ASSERT_ACTIVE, 1);
	// Example of warning message: "Warning: [description] "false" failed in /www/htdocs/www.abc.com/dev.../index.php on line 34"
	// Now custom error handler will intercept it and add full backtrace.
	assert_options(ASSERT_WARNING, 1);
	assert_options(ASSERT_BAIL, 0);
	assert_options(ASSERT_QUIET_EVAL, 0);
	//assert_options(ASSERT_CALLBACK, 'assertFailure');
	// No need for callback because "ASSERT_WARNING" = 1 already does everything.
	//assert_options(ASSERT_CALLBACK, null);
}
