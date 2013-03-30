<?php

namespace Renton56\PaginatorBundle\Twig;

class PaginatorExtension extends \Twig_Extension
{
	protected $request;
    /**
     *
     * @var \Twig_Environment
     */
    protected $environment;
		     
    public function __construct($request)
    {
        $this->request = $request;
    }
     
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
	     
    public function getFunctions()
    {
        return array(
                'get_paginator' => new \Twig_Function_Method($this, 'getPaginator'),                
        );
    }
	     
    public function getPaginator($amount_entitys, $maxEntityOnPage, $amount_signs)
    {
    	$count_pages = max( ceil($amount_entitys/$maxEntityOnPage), 1 );
    	
    	preg_match ('/(.*)(?=\/page\/(\d))|(.*)/i', $this->request->getUri(), $matches );
    	if( isset( $matches[0] ) )
    		$url_cur = $matches[0];    	
    	if( isset( $matches[2] ) )
    	{
    		$page_cur = ( ($matches[2] > 1) ? $matches[2] : 1 );
    	}
    	
    	$base_signs = floor( $amount_signs / 2 );    	
    	if ($base_signs < 1)
    		$base_signs = 1;    	
    	$page_begin = $page_cur - $base_signs;
    	if ( $page_begin < 1 )
    		$page_end_add = 1 - $page_begin;
    	else
    		$page_end_add = 0;
    	$page_end =  $page_cur + $base_signs;
    	if ( $page_end > $count_pages )
    		$page_begin_sub = $page_end - $count_pages;
    	else
    		$page_begin_sub = 0;
    	$page_begin = $page_cur - $base_signs - $page_begin_sub;
    	if ( $page_begin < 1 )
    		$page_begin = 1;
    	$page_end =  $page_cur + $base_signs + $page_end_add;
    	if ( $page_end > $count_pages )
    		$page_end = $count_pages;        
        
        $templateFile = "Renton56PaginatorBundle:Paginator:paginator.html.twig";
        $templateContent = $this->environment->loadTemplate($templateFile);
        
        if ($count_pages > 1)        
        	return $templateContent->display( array( "page_begin" => $page_begin, "page_end" => $page_end, "url_cur" => $url_cur,
        											"count_pages" => $count_pages, "page_cur" => $page_cur ) );
        else 
        	return null;
    } 
    
    public function getName()
    {
        return 'paginator_twig_extension';
    }
}
