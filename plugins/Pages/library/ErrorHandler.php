<?php

namespace Plugin\Pages;

class ErrorHandler
{
    function __invoke($request, $response) {
        if (!$response->hasExceptions()) {
            return;
        }
        
        $exceptions = $response->getExceptions();
        $exception = array_pop($exceptions);
        $code = $exception->getCode();
        
        if ($code == null or (ENVIRONMENT === "production" and $code > 500)) {
            $code = 500;
        }
        
        if(!$page = Page::find("_errors/{$code}")) {
            throw new Exception("No Error Page Template found");
        }
        
        $page->code = $code;
        
        $page->message       = $exception->getMessage();
        $page->stackTrace    = $exception->getTrace();
        $page->requestedPage = $request->getParam("page");
        $page->exceptionType = get_class($exception);
        $page->requestUri    = $request->getRequestUri();
        $page->requestMethod = $request->getMethod();
        
        $response->renderExceptions(false);
        $response->append($page);
    }
}
