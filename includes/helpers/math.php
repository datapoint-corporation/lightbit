<?php

// -----------------------------------------------------------------------------
// Pastelaria Amaral
//
// Copyright (c) 2017 Datapoint — Sistemas de Informação, Unipessoal, Lda.
// https://www.datapoint.pt/
//
// This file is part of "Pastelaria Amaral "(the "Software") which is licensed 
// to you (the "Customer") according to the terms and conditions defined in
// Datapoint Software License (the "License"), as described next:
//
// Permission is hereby granted to the Customer to use the Software and/or 
// associated documentation files, for personal and/or comercial purposes,
// free of charge; this permission does not grant the rights to copy, modify, 
// merge, publish, distribute, sublicense and/or sell the Software, parts
// of the Software or associated documentation files.
//
// The Software may include, make use of and/or depend on source code, files,
// software, libraries, documentation and other files created by third-party 
// individuals and/or organizations; this files, if any, may be licensed to
// the Customer through a different set of terms and conditions, which he must
// accept and meet in order to use the Software.
//
// The Software is provided "as is", without warranty of any kind, express or
// implied, including but not limited to the warranties of merchantability,
// fitness for a particular purposes and noninfringiment; in no event shall the
// Software authors or copyright holders be liable for any claim, damages or
// other liability, whether in action of contract, tort or otherwise, arising
// from, out of or in connection with the Software, its usage or dealing.
// -----------------------------------------------------------------------------

use \Lightbit\Helpers\MathHelper;

/**
 * Rounds a number up.
 *
 * @param string $number
 *	The number.
 *
 * @param int $precision
 *	The precision.
 *
 * @return string
 *	The result.
 */
function lbceil(string $number, int $precision = 0) : string
{
	return MathHelper::ceil($number, $precision);
}

/**
 * Rounds a number down.
 *
 * @param string $number
 *	The number.
 *
 * @param int $precision
 *	The precision.
 *
 * @return string
 *	The result.
 */
function lbfloor(string $number, int $precision = 0) : string
{
	return MathHelper::floor($number, $precision);
}

/**
 * Rounds a number.
 *
 * @param string $number
 *	The number.
 *
 * @param int $precision
 *	The precision.
 *
 * @return string
 *	The result.
 */
function lbround(string $number, int $precision = 0) : string
{
	return MathHelper::round($number, $precision);
}