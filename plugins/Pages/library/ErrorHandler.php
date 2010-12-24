<?php

namespace Plugin\Pages;

class ErrorHandler
{
    function __invoke($request, $response) {
        if (!$response->hasExceptions()) return;
        
        $exceptions = $response->getExceptions();
        $exception  = array_pop($exceptions);
        
        $code = $exception->getCode();
        
        if ($code == null or (ENVIRONMENT === "production" and $code > 500)) {
            $code = 500;
        }
        
        if (!$page = Page::find("_errors/{$code}")) {
            $e = "No Error Page Template found. Make sure you have a file named "
               . "\"{$code}.txt\" in your \"/pages/_error\" directory";
            throw new \Spark\Controller\Exception($e);
        }
        
        $page->code = $code;
        
        $page->message       = $exception->getMessage();
        $page->stackTrace    = $exception->getTrace();
        $page->requestedPage = $request->getMetadata("page");
        $page->exceptionType = get_class($exception);
        $page->requestUri    = $request->getRequestUri();
        $page->requestMethod = $request->getMethod();
        
        $response->renderExceptions(false);
        $response->append($page);
    }
}
