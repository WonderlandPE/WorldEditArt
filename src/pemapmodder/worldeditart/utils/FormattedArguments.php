<?php

/*
 * WorldEditArt
 *
 * Copyright (C) 2015 PEMapModder
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace pemapmodder\worldeditart\utils;

class FormattedArguments{
	public $plain = [];
	public $switches = [];
	public $opts = [];
	public $unterminated = false;

	/**
	 * Converts a string array into a <code>FormattedArguments</code> instance.
	 * <br>
	 * Rules:
	 * <div style="border: groove">
	 * A "word" refers to a string delimited by spaces in the command, regardless of any other rules.
	 * <br>
	 * A "phrase" refers to a consecutive sequence of words, enclosed in ` "` and `" ` in the whole command input,
	 * or ONE word not enclosed by them.
	 * <br>
	 * A "switch" refers to a boolean option in the command, which is represented by a word starting with a `.` and
	 * named by the rest of the word.
	 * <br>
	 * An "opt" refers to a string option in the command. It is represented by a word starting with a `,`, and named
	 * by the rest of the word. The phrase following this word is the value of the option.
	 * <br>
	 * All phrases that aren't part of a switch or an opt (including both the name part and the value part) are, in
	 * ascending order of occurrences, "plain arguments".
	 * <br>
	 * If an opt name or a switch follows an opt name word, it will be considered as the value phrase
	 * of the opt specified by the previous word.
	 * </div>
	 *
	 * The output returns an instance of {@link FormattedArguments}, which contains the <code>plain</code>,
	 * <code>switches</code> and <code>opts</code> properties, representing plain arguments,
	 * switches and opts respectively.
	 *
	 * @param string[] $args
	 *
	 * @return FormattedArguments
	 */
	public function __construct($args){
		$this->plain = [];
		$this->switches = [];
		$this->opts = [];
		$currentOpt = null;
		$quotesOn = false;
		$currentLongString = "";
		foreach($args as $arg){
			if($quotesOn){ // continue/break quote on
				$currentLongString .= $arg;
				if(substr($arg, -1) === '"'){
					$currentLongString = substr($currentLongString, -1);
					$quotesOn = false;
					if($currentOpt === null){
						$this->plain[] = $currentLongString;
					}else{
						$this->opts[$currentOpt] = $currentLongString;
					}
					$currentLongString = "";
				}
			}elseif($arg{0} === '"'){ // start quote on
				$quotesOn = true;
				$currentLongString = substr($arg, 1);
			}elseif($currentOpt !== null){
				$this->opts[$currentOpt] = $arg;
				$currentOpt = null;
			}elseif($arg{0} === "."){
				$this->switches[$arg] = true;
			}elseif($arg{0} === ","){
				$currentOpt = $arg;
			}else{
				$this->plain[] = $arg;
			}
		}
		if($currentOpt !== null or $currentLongString !== "" or $quotesOn){
			$this->unterminated = true;
		}
	}
}
