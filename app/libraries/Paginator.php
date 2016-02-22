<?php
// http://www.catchmyfame.com/2007/07/28/finally-the-simple-pagination-class/
// http://www.catchmyfame.com/2014/02/06/php-pagination-class-version-3/

namespace App\Lib;

class Paginator
{
	var $items_per_page;
	var $items_total;
	var $current_page;
	var $num_pages;
	var $mid_range;
	var $low;
	var $high;
	var $limit;
	var $return;
	var $default_ipp = 25;
	var $querystring;
	var $current_url;
	var $offset = 0;

	var $ipp_array = array(10,25,50,100,'All');
	
	var $next_word = "Next";
	var $previous_word = "Previous";

	function Paginator()
	{
		$this->current_page = 1;
		$this->mid_range = 7; // Phai la so le, so chan tinh sai
		$this->items_per_page = $this->default_ipp;
	}

	function paginate()
	{
		$this->current_page = ($this->offset / $this->items_per_page) + 1;
		
		if(!is_numeric($this->items_per_page) OR $this->items_per_page <= 0) $this->items_per_page = $this->default_ipp;
		$this->num_pages = ceil($this->items_total/$this->items_per_page);
		
		if($this->current_page < 1 Or !is_numeric($this->current_page)) $this->current_page = 1;
		if($this->current_page > $this->num_pages) $this->current_page = $this->num_pages;
		$prev_page = $this->current_page-1;
		$next_page = $this->current_page+1;
		
		if ($this->num_pages <= 1) 
		{
			$this->return = "";
			return;
		}

		if($this->num_pages > 10)
		{
			$offset = ($prev_page - 1) * $this->items_per_page;
			$this->return = ($this->current_page != 1 And $this->items_total >= 10) ? "<a class=\"paginate\" href=\"".sprintf($this->current_url, $offset)."\">{$this->previous_word}</a> ":"<span class=\"inactive\" href=\"#\">{$this->previous_word}</span> ";

			$this->start_range = $this->current_page - floor($this->mid_range/2);
			$this->end_range = $this->current_page + floor($this->mid_range/2);

			if($this->start_range <= 0)
			{
				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;
			}
			if($this->end_range > $this->num_pages)
			{
				$this->start_range -= $this->end_range-$this->num_pages;
				$this->end_range = $this->num_pages;
			}
			$this->range = range($this->start_range,$this->end_range);

			for($i=1;$i<=$this->num_pages;$i++)
			{
				if($this->range[0] > 2 And $i == $this->range[0]) $this->return .= " ... ";
				// loop through all pages. if first, last, or in range, display
				if($i==1 Or $i==$this->num_pages Or in_array($i,$this->range))
				{
					$offset = ($i - 1) * $this->items_per_page;
					$this->return .= ($i == $this->current_page) ? "<a title=\"Go to page $i of $this->num_pages\" class=\"current\" href=\"#\">$i</a> ":"<a class=\"paginate\" title=\"Go to page $i of $this->num_pages\" href=\"".sprintf($this->current_url, $offset)."\">$i</a> ";
				}
				if($this->range[$this->mid_range-1] < $this->num_pages-1 And $i == $this->range[$this->mid_range-1]) $this->return .= " ... ";
			}
			$offset = ($next_page - 1) * $this->items_per_page;
			$this->return .= (($this->current_page != $this->num_pages And $this->items_total >= 10)) ? "<a class=\"paginate\" href=\"".sprintf($this->current_url, $offset)."\">{$this->next_word}</a>\n":"<span class=\"inactive\" href=\"#\">{$this->next_word}</span>\n";
		}
		else
		{
			for($i=1;$i<=$this->num_pages;$i++)
			{
				$offset = ($i - 1) * $this->items_per_page;
				$this->return .= ($i == $this->current_page) ? "<a class=\"current\" href=\"#\">$i</a> ":"<a class=\"paginate\" href=\"".sprintf($this->current_url, $offset)."\">$i</a> ";
			}
			//$this->return .= "<a class=\"paginate\" href=\"".sprintf($this->current_url, 0)."\">All</a> \n";
		}
		$this->low = ($this->current_page-1) * $this->items_per_page;
	}

	function display_items_per_page()
	{
		$items = '';
// 		$ipp_array = array(10,25,50,100,'All');
		foreach($this->ipp_array as $ipp_opt)	$items .= ($ipp_opt == $this->items_per_page) ? "<option selected value=\"$ipp_opt\">$ipp_opt</option>\n":"<option value=\"$ipp_opt\">$ipp_opt</option>\n";
		return "<span class=\"paginate\">Items per page:</span><select class=\"paginate select_item\" onchange=\"window.location='".sprintf($this->current_url, 0)."/ipp/'+this[this.selectedIndex].value;return false\">$items</select>\n";
	}

	function display_jump_menu()
	{
		$option = '';
		for($i=1;$i<=$this->num_pages;$i++)
		{
			$offset = ($i - 1) * $this->items_per_page;
			$option .= ($i==$this->current_page) ? "<option value=\"$offset\" selected>$i</option>\n":"<option value=\"$offset\">$i</option>\n";
		}
		return "<span class=\"paginate\">Page:</span><select class=\"paginate select_item\" onchange=\"window.location='".str_replace('%d','',$this->current_url)."'+this[this.selectedIndex].value;return false\">$option</select>\n";
	}

	function display_pages()
	{
		return $this->return;
	}
}