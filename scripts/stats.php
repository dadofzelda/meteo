<?php

    /**
     * Compute the arithmetic mean, it is calculated by adding a group of numbers
     * and then dividing by the count of those numbers.
     * For example, the mean of 2, 3, 3, 5, 7, and 10 is 30 divided by 6, which is 5.
     *
     * @param array  $x    List of float values for which you want to calculate the mean.
     * @param string $type Mean type [arithmetic|geometric|harmonic], default is arithmetic.
     *
     * @return float Mean
     * @url http://en.wikipedia.org/wiki/Mean
     */
    function mean($x, $type="arithmetic")
    {
        $type = strtolower($type);
        
		
        if ($type == "arithmetic") {
            $total = 0;

            foreach ($x as $value) {
                $total += $value;
            }
            
            $mean = $total/count($x);
        } elseif ($type == "geometric") {
            $total = 1;

            foreach ($x as $value) {
                $total *= $value;
            }
            
            $mean = pow($total, 1/count($x));
        } elseif ($type == "harmonic") {
            $total = 0;

            foreach ($x as $value) {
                $total += 1/$value;
            }
            
            $mean = count($x)/$total;
        }

        return $mean;
    }

    /**
     * Compute the sample median which is the middle number of a group of numbers; that is,
     * half the numbers have values that are greater than the median, and half the numbers
     * have values that are less than the median.
     * For example, the median of 2, 3, 3, 5, 7, and 10 is 4.
     *
     * @param array $x List of float values for which you want to calculate the median.
     *
     * @return float Sample median
     * @url http://en.wikipedia.org/wiki/Median
     */
    function median($x)
    {
        sort($x);

        $count = count($x);

        if ($count % 2 == 0) {
            $median = ($x[($count / 2) - 1] + $x[$count / 2]) / 2;
        } else {
            $median = $x[floor($count / 2)];
        }

        return $median;
    }

    /**
     * Compute the mode which is the most frequently occurring number in a group of numbers.
     * For example, the mode of 2, 3, 3, 5, 7, and 10 is 3.
     *
     * @param array $x List of float values for which you want to calculate the mode.
     *
     * @return array Returns the most frequently occurring or repetitive value
     * @url http://en.wikipedia.org/wiki/Mode_(statistics)
     */
    function mode($x)
    {
        $counter = array();

        foreach ($x as $value) {
            if (isset($counter[$value])) {
                $counter[$value]++;
            } else {
                $counter[$value] = 1;
            }
        }

        return array_keys($counter, max($counter));
    }

    /**
     * Estimates variance based on a sample, the variance is a measure of how far a set 
     * of numbers is spread out.
     *
     * @param array $x List of float values corresponding to a sample of a population.
     *
     * @return float Returns the sample variance
     * @url http://en.wikipedia.org/wiki/Variance
     */
    function variance($x)
    {
        $mean = mean($x);

        $var = 0;

        foreach ($x as $value) {
            $var += ($value - $mean) * ($value - $mean);
        }

        $var = $var / (count($x) - 1);

        return $var;
    }

    /**
     * Compute the standard deviation based on a sample. The standard deviation is a measure of
     * how widely values are dispersed from the average value (the mean).
     *
     * @param array $x List of float values for which you want to calculate the standard deviation.
     *
     * @return float Returns the standard deviation

     * @url http://en.wikipedia.org/wiki/Standard_deviation
     */
    function sd($x)
    {
        $sd = sqrt(variance($x));
        
        return $sd;
    }

    /**
     * Compute the skewness of a distribution. Skewness characterizes the degree of asymmetry of
     * a distribution around its mean. Positive skewness indicates a distribution with an asymmetric
     * tail extending toward more positive values. Negative skewness indicates a distribution with
     * an asymmetric tail extending toward more negative values.
     *
     * @param array $x List of float values for which you want to calculate the skewness.
     *
     * @return float Returns the skewness of a distribution

     * @url http://en.wikipedia.org/wiki/Skewness
     */
    function skew($x)
    {
        $mean = mean($x);
        $sd   = sd($x);
		if($sd==0){$sd=0.000001;}
        $n    = count($x);

        $skew = 0;

        foreach ($x as $value) {
            $skew += pow(($value - $mean) / $sd, 3);
        }

        $skew = ($skew * $n) / (($n - 1) * ($n - 2));

        return $skew;
    }
    
    /**
     * Test skewness against 0
     *
     * @param array $x List of float values for which you want to test the skewness for.
     *
     * @return boolean Returns if skewness is significant

     * @url http://en.wikipedia.org/wiki/Skewness
     */
    function isSkew($x)
    {
        $n      = count($x);
        $skew   = skew($x);
        $skewSE = sqrt(6 / $n);
        
        if (abs($skew) > 2 * $skewSE) {
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }

    /**
     * Compute the kurtosis of a distribution. Kurtosis characterizes the relative peakedness or
     * flatness of a distribution compared with the normal distribution. Positive kurtosis indicates
     * a relatively peaked distribution. Negative kurtosis indicates a relatively flat distribution.
     *
     * @param array $x List of float values for which you want to calculate the kurtosis.
     *
     * @return float Returns the kurtosis of a distribution

     * @url http://en.wikipedia.org/wiki/Kurtosis
     */
    function kurt($x)
    {
        $mean = mean($x);
        $sd   = sd($x);
		if($sd==0){$sd=0.000001;}
        $n    = count($x);

        $kurt = 0;

        foreach ($x as $value) {
            $kurt += pow(($value - $mean) / $sd, 4);
        }

        $kurt = ($kurt * $n * ($n + 1)) / (($n - 1) * ($n - 2) * ($n - 3));
        $kurt = $kurt - ((3 * ($n - 1) * ($n - 1)) / (($n - 2) * ($n - 3)));

        return $kurt;
    }

    /**
     * Test kurtosis against 0
     *
     * @param array $x List of float values for which you want to test the kurtosis for.
     *
     * @return boolean Returns if kurtosis is significant

     * @url http://en.wikipedia.org/wiki/Kurtosis
     */
    function isKurt($x)
    {
        $n      = count($x);
        $kurt   = kurt($x);
        $kurtSE = sqrt(24 / $n);
        
        if (abs($kurt) > 2 * $kurtSE) {
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }

    /**
     * Compute the coefficients of variation, it shows the extent of variability in relation 
     * to mean of the population.
     *
     * @param array $x List of float values for which you want to calculate the coefficients of variation.
     *
     * @return float Returns the coefficients of variation

     * @url http://en.wikipedia.org/wiki/Coefficient_of_variation
     */
    function cv($x)
    {
        $mean = mean($x);
        $sd   = sd($x);

        $cv = ($sd / $mean) * 100;

        return $cv;
    }

    /**
     * Compute the covariance, the average of the products of deviations for each data point pair.
     * Use covariance to determine the relationship between two data sets. For example, you can
     * examine whether greater income accompanies greater levels of education.
     *
     * @param array $x First list of float values
     * @param array $y Second list of float values
     *
     * @return float Returns the covariance

     * @url http://en.wikipedia.org/wiki/Covariance
     */
    function cov($x, $y)
    {
        $meanX = mean($x);
        $meanY = mean($y);

        $count = count($x);
        $total = 0;

        for ($i=0; $i<$count; $i++) {
            $total += ($x[$i] - $meanX) * ($y[$i] - $meanY);
        }

        $cov = (1 / ($count - 1)) * $total;
        
        return $cov;
    }

    /**
     * Compute the correlation coefficient. Use the correlation coefficient to determine the
     * relationship between two properties. It uses different measures of association, all
     * in the range [-1, 1] with 0 indicating no association.
     *
     * @param array $x First list of float values
     * @param array $y Second list of float values
     *
     * @return float Returns the correlation coefficient

     * @url http://en.wikipedia.org/wiki/Correlation
     */
    function cor($x, $y)
    {
        $cov = cov($x, $y);
        $sdX = sd($x);
        $sdY = sd($y);
        
        $cor = $cov / ($sdX * $sdY);

        return $cor;
    }

    /**
     * Test of the null hypothesis that true correlation is equal to 0
     *
     * @param float   $r Correlation value
     * @param integer $n Number of observations
     *
     * @return float Returns null hypothesis probability: true correlation is equal to 0

     */
    function corTest($r, $n)
    {
        $t = $r / sqrt((1 - ($r * $r)) / ($n - 2));

        $result = tDist($t, $n - 2);

        return $result;
    }

    /**
     * Compute the simple linear regression fits a linear model to represent
     * the relationship between a response (or y-) variate, and an explanatory
     * (or x-) variate.
     *
     * @param array   $y      List of float values of the response (or y-) variate.
     * @param array   $x      List of float values of the first explanatory (or x1) variate.
     * @param boolean $origin If TRUE then Intercept value set to 0 (default is FALSE)
     *
     * @return array Returns [intercept], [slope], [r-square], [adj-r-square] as float 
     *               values in addition to standard error of regression model parameters 
     *               [intercept-se] and [slope-se] as well as confidence intervals 
     *               at level 95% [intercept-2.5%], [intercept-97.5%], [slope-2.5%],
     *               and [slope-97.5%]

     * @url http://en.wikipedia.org/wiki/Regression_analysis
     */
    function lm($y, $x, $origin=false)
    {
        $n = count($x);

        $meanX = mean($x);
        $meanY = mean($y);

        $nominator   = 0;
        $denominator = 0;
        
        $x2 = 0;
        $y2 = 0;
        $xy = 0;

        for ($i=0; $i<$n; $i++) {
            $nominator   += ($x[$i] - $meanX) * ($y[$i] - $meanY);
            $denominator += ($x[$i] - $meanX) * ($x[$i] - $meanX);

            $x2 += $x[$i] * $x[$i];
            $y2 += $y[$i] * $y[$i];
            $xy += $x[$i] * $y[$i];
        }

        if ($origin) {
            $b  = $xy / $x2;
            $a  = 0;
            $df = $n - 1;
        } else {
            $b  = $nominator / $denominator;
            $a  = $meanY - $b * $meanX;
            $df = $n - 2;
        }

        // Total sum of squares (ss) and Residual sum of squares (rss)
        $total_ss      = 0;
        $regression_ss = 0;
        $residual_ss   = 0;

        for ($i=0; $i<$n; $i++) {
            $est   = $a + ($b * $x[$i]);

            $total_ss      += pow($y[$i] - $meanY, 2);
            $residual_ss   += ($y[$i] - $est) * ($y[$i] - $est);

            if ($origin) {
                $regression_ss += $est * $est;
            } else {
                $regression_ss += ($est - $meanY) * ($est - $meanY);
            }
        }

        // R-square value and Standard error of regression intercept and slope
        if ($origin) {
            $r2  = $regression_ss / $y2;

            $ase = 0;
            $bse = sqrt($residual_ss/$df) / sqrt($x2);
        } else {
            $r2 = 1 - ($regression_ss/$total_ss);

            $ase = sqrt($residual_ss/$df) * sqrt($x2/($n*$denominator));
            $bse = sqrt($residual_ss/$df) / sqrt($denominator);
        }
        
        // Significance of regression
        $regression_ms = $regression_ss / 1;
        $residual_ms   = $residual_ss / $df;
        
        $regression_f  = $regression_ms / $residual_ms;
        $regression_p  = fDist($regression_f, 1, $df);
        
        // Output
        $result = array('intercept'=>$a, 'slope'=>$b, 'r-square'=>$r2);

        $result['adj-r-square'] = $r2 - (1 - $r2) / $df;
        
        $result['intercept-se']    = $ase;
        $result['intercept-2.5%']  = $a - inverseTCDF(0.05, $df) * $ase;
        $result['intercept-97.5%'] = $a + inverseTCDF(0.05, $df) * $ase;

        $result['slope-se']    = $bse;
        $result['slope-2.5%']  = $b - inverseTCDF(0.05, $df) * $bse;
        $result['slope-97.5%'] = $b + inverseTCDF(0.05, $df) * $bse;
        
        $result['F-statistic'] = $regression_f;
        $result['p-value']     = $regression_p;

        return $result;
    }

    /**
     * Compute the Student's t-Test value to determine whether two samples are likely
     * to have come from the same two underlying populations that have the same mean.
     *
     * @param array   $a      First list of float values
     * @param array   $b      Second list of float values
     * @param boolean $paired Logical indicating whether you want a paired t-test, default
     *                        value is FALSE for unpaired set of data
     *
     * @return float Returns the associated with a Student's t-Test

     * @url http://en.wikipedia.org/wiki/T-Test
     */
    function tTest ($a, $b, $paired=false)
    {
        if ($paired == true) {
            $count = count($a);

            $diff = array();

            for ($i=0; $i<$count; $i++) {
                $diff[$i] = $a[$i] - $b[$i];
            }

            $mean = mean($diff);
            $var  = variance($diff);

            $t = $mean / sqrt($var / $count);
        } else {
            $meanA = mean($a);
            $meanB = mean($b);

            $varA = variance($a);
            $varB = variance($b);

            $countA = count($a);
            $countB = count($b);

            $t = ($meanA - $meanB) / sqrt(($varA / $countA) + ($varB / $countB));
        }

        return $t;
    }

    /**
     * Returns the standard normal cumulative distribution function. The distribution
     * has a mean of 0 (zero) and a standard deviation of one. Use this function in
     * place of a table of standard normal curve areas.
     *
     * @param float $x    Is the value for which you want the distribution.
     * @param float $mean The distribution mean (default is zero).
     * @param float $sd   The distribution standard deviation (default is one).
     *
     * @return float the standard normal cumulative distribution function.

     * @url http://en.wikipedia.org/wiki/Normal_distribution
     */
    function norm ($x, $mean=0, $sd=1)
    {
        $y = (1 / sqrt(2 * pi())) * exp(-0.5 * pow($x, 2));

        return $y;
    }

    function _zip ($q, $i, $j, $b)
    {
        $zz = 1;
        $z  = $zz;
        $k  = $i;

        while ($k <= $j) {
            $zz *= $q * $k / ($k - $b);
            $z  += $zz;
            $k  += 2;
        }

        return $z;
    }

    /**
     * Returns the Percentage Points (probability) for the Student t-distribution
     * where a numeric value (t) is a calculated value of t for which the Percentage
     * Points are to be computed.
     *
     * @param float   $t    Is the numeric value at which to evaluate the distribution.
     * @param integer $n    Is an integer indicating the number of degrees of freedom.
     * @param integer $tail Specifies the number of distribution tails to return.
     *                      If tail = 1, TDIST returns the one-tailed distribution.
     *                      If tail = 2, TDIST returns the two-tailed distribution.
     *
     * @return float the Percentage Points (probability) for the Student t-distribution

     * @url http://en.wikipedia.org/wiki/T-distribution
     */
    function tDist ($t, $n, $tail=1)
    {
        $pj2 = pi() / 2;

        $t = abs($t);

        $rt = $t / sqrt($n);
        $fk = atan($rt);

        if ($n == 1) {
            $result = 1 - $fk / $pj2;
        } else {
            $ek = sin($fk);
            $dk = cos($fk);

            if (($n % 2) == 1) {
                $result = 1 - ($fk + $ek * $dk * _zip($dk * $dk, 2, $n-3, -1)) / $pj2;
            } else {
                $result = 1 - $ek * _zip($dk * $dk, 1, $n-3, -1);
            }
        }

        return $result / $tail;
    }

    /**
     * Returns the F probability distribution. You can use this function to determine
     * whether two data sets have different degrees of diversity.
     *
     * @param float   $f   Is the value at which to evaluate the function.
     * @param integer $df1 Is the numerator degrees of freedom.
     * @param integer $df2 Is the denominator degrees of freedom.
     *
     * @return float the F probability distribution

     * @url http://en.wikipedia.org/wiki/F-distribution
     */
    function fDist ($f, $df1, $df2)
    {
        $pj2 = pi() / 2;

        $x = $df2 / ($df1 * $f + $df2);

        if (($df1 % 2) == 0) {
            return _zip(1 - $x, $df2, $df1 + $df2 - 4, $df2 - 2) * pow($x, $df2 / 2);
        }

        if (($df2 % 2) == 0) {
            return 1 - _zip($x, $df1, $df1 + $df2 - 4, $df1 - 2) * pow(1 - $x, $df1 / 2);
        }

        $tan = atan(sqrt($df1 * $f / $df2));
        $a   = $tan / $pj2;
        $sat = sin($tan);
        $cot = cos($tan);

        if ($df2 > 1) {
            $a = $a + $sat * $cot * _zip($cot * $cot, 2, $df2 - 3, -1) / $pj2;
        }

        if ($df1 == 1) {
            return 1 - $a;
        }

        $c = 4 * _zip($sat * $sat, $df2 + 1, $df1 + $df2 - 4, $df2 - 2) * $sat * pow($cot, $df2) / pi();

        if ($df2 == 1) {
            return 1 - $a + $c / 2;
        }

        $k = 2;

        while ($k <= ($df2 - 1) / 2) {
            $c *= $k / ($k - 0.5);
            $k++;
        }

        return 1 - $a + $c;
    }

    /**
     * Return the probability of normal z value
     * Adapted from a polynomial approximation in:
     *     Ibbetson D, Algorithm 209
     *     Collected Algorithms of the CACM 1963 p. 616
     *
     * @param float $z Is the value at which you want to evaluate the probability.
     *
     * @return float the probability of normal z value

     */
    function normCDF ($z)
    {
        $max = 6;

        if ($z == 0) {
            $x = 0;
        } else {
            $y = abs($z) / 2;
            if ($y >= ($max / 2)) {
                $x = 1;
            } elseif ($y < 1) {
                $w = $y * $y;
                $x = ((((((((0.000124818987 * $w
                          - 0.001075204047) * $w + 0.005198775019) * $w
                          - 0.019198292004) * $w + 0.059054035642) * $w
                          - 0.151968751364) * $w + 0.319152932694) * $w
                          - 0.531923007300) * $w + 0.797884560593) * $y * 2;
            } else {
                $y -= 2;
                $x = (((((((((((((-0.000045255659 * $y
                                + 0.000152529290) * $y - 0.000019538132) * $y
                                - 0.000676904986) * $y + 0.001390604284) * $y
                                - 0.000794620820) * $y - 0.002034254874) * $y
                                + 0.006549791214) * $y - 0.010557625006) * $y
                                + 0.011630447319) * $y - 0.009279453341) * $y
                                + 0.005353579108) * $y - 0.002141268741) * $y
                                + 0.000535310849) * $y + 0.999936657524;
            }
        }

        if ($z > 0) {
            $result = ($x + 1) / 2;
        } else {
            $result = (1 - $x) / 2;
        }

        return $result;
    }

    /**
     * Returns the one-tailed probability of the chi-squared distribution. The chi-squared
     * distribution is associated with a chi-squared test. Use the chi-squared test to
     * compare observed and expected values.
     * Adapted from:
     *     Hill, I. D. and Pike, M. C.  Algorithm 299
     *     Collected Algorithms for the CACM 1967 p. 243
     * Updated for rounding errors based on remark in
     *     ACM TOMS June 1985, page 185
     *
     * @param float   $x  Is the value at which you want to evaluate the distribution.
     * @param integer $df Is the number of degrees of freedom.
     *
     * @return float the probability of the chi-squared distribution

     */
    function chiDist ($x, $df)
    {
        define(LOG_SQRT_PI, log(sqrt(pi())));
        define(I_SQRT_PI, 1 / sqrt(pi()));

        if ($x <= 0 || $df < 1) {
            $result = 1;
        }

        $a = $x / 2;

        if ($df % 2 == 0) {
            $even = true;
        } else {
            $even = false;
        }

        if ($df > 1) {
            $y = exp(-1 * $a);
        }

        if ($even) {
            $s = $y;
        } else {
            $s = 2 * normCDF(-1 * sqrt($x));
        }

        if ($df > 2) {
            $x = ($df - 1) / 2;

            if ($even) {
                $z = 1;
            } else {
                $z = 0.5;
            }

            if ($a > 20) {
                if ($even) {
                    $e = 0;
                } else {
                    $e = LOG_SQRT_PI;
                }

                $c = log($a);

                while ($z <= $x) {
                    $e += log($z);
                    $s += exp($c * $z - $a - $e);
                    $z += 1;
                }

                $result = $s;
            } else {
                if ($even) {
                    $e = 1;
                } else {
                    $e = I_SQRT_PI / sqrt($a);
                }

                $c = 0;

                while ($z <= $x) {
                    $e *= ($a / $z);
                    $c += $e;
                    $z += 1;
                }

                $result = $c * $y + $s;
            }
        } else {
            $result = $s;
        }

        return $result;
    }

    /**
     * Performs chi-squared contingency table tests and goodness-of-fit tests.
     *
     * Example:
     * <code>
     * $table['Automatic'] = array('4 Cylinders' => 3, '6 Cylinders' => 4, '8 Cylinders' => 12);
     * $table['Manual']    = array('4 Cylinders' => 8, '6 Cylinders' => 3, '8 Cylinders' => 2);
     *
     * $results = $stats->chiTest($table);
     * </code>
     *
     * @param array $table Specifies the two-way, n x m table containing the counts
     *
     * @return array [chi] => the value the chi-squared test statistic
     *               [df] => the degrees of freedom of the approximate chi-squared distribution of the test statistic.
     *               [expected] => the expected counts under the null hypothesis.

     */
    function chiTest ($table)
    {
        $total = 0;
        $chi   = 0;

        foreach ($table as $category => $row) {
            foreach ($row as $sample => $cell) {
                if (isset($rows[$category])) {
                    $rows[$category] += $cell;
                } else {
                    $rows[$category] = $cell;
                }

                if (isset($cols[$sample])) {
                    $cols[$sample] += $cell;
                } else {
                    $cols[$sample] = $cell;
                }

                $total += $cell;
            }
        }

        $r  = count($rows);
        $c  = count($cols);
        $df = ($r - 1) * ($c - 1);

        $expected = array();

        foreach ($table as $category => $row) {
            foreach ($row as $sample => $cell) {
                // fo frequency of the observed value
                // fe frequency of the expected value
                $fo  = $cell;
                $fe  = ($rows[$category] * $cols[$sample]) / $total;
                $chi += pow($fo - $fe, 2) / $fe;

                $expected[$category][$sample] = $fe;
            }
        }

        return array('chi'=>$chi, 'df'=>$df, 'expected'=>$expected);
    }

    /**
     * Returns the inverse of the standard normal cumulative distribution. 
     * The distribution has a mean of zero and a standard deviation of one.
     * This is an implementation of the algorithm published at:
     * http://home.online.no/~pjacklam/notes/invnorm/
     *
     * @param float $p Is a probability corresponding to the normal distribution between 0 and 1.
     *
     * @return float Inverse of the standard normal cumulative distribution, with a probability of $p

     */
    function inverseNormCDF($p) 
    {
        /* coefficients for the rational approximants for the normal probit: */
        $a1	= -3.969683028665376e+01;
        $a2	=  2.209460984245205e+02;
        $a3	= -2.759285104469687e+02;
        $a4	=  1.383577518672690e+02;
        $a5	= -3.066479806614716e+01;
        $a6	=  2.506628277459239e+00;
        $b1	= -5.447609879822406e+01;
        $b2	=  1.615858368580409e+02;
        $b3	= -1.556989798598866e+02;
        $b4	=  6.680131188771972e+01;
        $b5	= -1.328068155288572e+01;
        $c1	= -7.784894002430293e-03;
        $c2	= -3.223964580411365e-01;
        $c3	= -2.400758277161838e+00;
        $c4	= -2.549732539343734e+00;
        $c5	=  4.374664141464968e+00;
        $c6	=  2.938163982698783e+00;
        $d1	=  7.784695709041462e-03;
        $d2	=  3.224671290700398e-01;
        $d3	=  2.445134137142996e+00;
        $d4	=  3.754408661907416e+00;

        $p_low  = 0.02425;
        $p_high	= 1.0 - $p_low;

        if (0 < $p && $p < $p_low) {
            /* rational approximation for the lower region */
            $q = sqrt(-2 * log($p));
            $x = ((((($c1 * $q + $c2) * $q + $c3) * $q + $c4) * $q + $c5) * $q + $c6) / (((($d1 * $q + $d2) * $q + $d3) * $q + $d4) * $q + 1);
        } elseif ($p_low <= $p && $p <= $p_high) {
            /* rational approximation for the central region */
            $q = $p - 0.5;
            $r = $q * $q;
            $x = ((((($a1 * $r + $a2) * $r + $a3) * $r + $a4) * $r + $a5) * $r + $a6) * $q / ((((($b1 * $r + $b2) * $r + $b3) * $r + $b4) * $r + $b5) * $r + 1);
        } else {
            /* rational approximation for the upper region */
            $q = sqrt(-2 * log(1 - $p));
            $x = -((((($c1 * $q + $c2) * $q + $c3) * $q + $c4) * $q + $c5) * $q + $c6) / (((($d1 * $q + $d2) * $q + $d3) * $q + $d4) * $q + 1);
        }

        return $x;
    }

    /**
     * Returns the t-value of the Student's t-distribution as a function of 
     * the probability and the degrees of freedom. This is an implementation 
     * of the algorithm in:
     * G. W. Hill. "Algorithm 396: Student's t-Quantiles." Communications
     * of the ACM 13(10):619--620.  ACM Press, October, 1970.
     *
     * @param float   $p Is the probability associated with the two-tailed Student's t-distribution between 0 and 1.
     * @param integer $n Is the number of degrees of freedom with which to characterize the distribution.
     *
     * @return float t-value of the Student's t-distribution for the terms above (i.e. $p and $n).

     */
    function inverseTCDF($p, $n) 
    {
        if ($n == 1) {
            $p     *= M_PI_2;
            $result = cos($p) / sin($p);
        } else {
            $a = 1 / ($n - 0.5);
            $b = 48 / ($a * $a);
            $c = ((20700 * $a / $b - 98) * $a - 16) * $a + 96.36;
            $d = ((94.5 / ($b + $c) - 3) / $b + 1) * sqrt($a * M_PI_2) * $n;
            $y = pow(2 * $d * $p, 2 / $n);
            
            if ($y > (0.05 + $a)) {
                /* asymptotic inverse expansion about the normal */
                $x = inverseNormCDF($p * 0.5);
                $y = $x * $x;

                if ($n < 5) {
                    $c += 0.3 * ($n - 4.5) * ($x + 0.6);
                }

                $c  = (((0.05 * $d * $x - 5) * $x - 7) * $x - 2) * $x + $b + $c;
                $y  = (((((0.4 * $y + 6.3) * $y + 36) * $y + 94.5) / $c - $y - 3) / $b + 1) * $x;
                $y *= $a * $y;

                if ($y > 0.002) {
                    $y = exp($y) - 1;
                } else {
                    $y += 0.5 * $y * $y;
                }
            } else {
                $y = ((1 / ((($n + 6) / ($n * $y) - 0.089 * $d - 0.822) * ($n + 2) * 3) + 0.5 / ($n + 4)) * $y - 1) * ($n + 1) / ($n + 2) + 1 / $y;
            }
            
            $result = sqrt($n * $y);
        }

        return $result;
    }
    
    /**
     * Returns the Shannon or Simpson index value
     *
     * @param array  $abundances Associated array where keys refer to the categories and values refer to the observation counts in each category.
     * @param string $index      Which index you would like to calculate ["shannon"|"simpson"] (default is "shannon")
     * @param float  $base       Base of the logarithmic transformation used in the Shannon index (default is M_E)
     *
     * @return float The index (either Shannon or Simpson as selected in the $index parameter)

     * @url http://en.wikipedia.org/wiki/Diversity_index
     */
    function diversity ($abundances, $index='shannon', $base=M_E)
    {
        $index = strtolower($index);
        $N = array_sum($abundances);

        // Calculate the proportion of characters belonging to each category
        foreach ($abundances as $key=>$value) {
            $P["$key"] = $value / $N;
        }

        $result = 0;

        if ($index == 'shannon') {
            foreach ($P as $key=>$value) {
                $result += $value * log($value, $base);
            }
            $result = -1 * $result;
        } elseif ($index == 'simpson') {
            if ($N < 20) {
                foreach ($abundances as $key=>$value) {
                    $result += $value * ($value - 1);
                }
                $result = $result / ($N * ($N - 1));
            } else {
                foreach ($P as $key=>$value) {
                    $result += $value * $value;
                }
            }
        }

        return $result;
    }

    /**
     * Perform standardize transformation; variables are commonly standardized to 
     * zero mean and unit variance, and this will usually be necessary if they are 
     * measured in different units.
     *
     * @param array   $x   List of float values for which you want to standardize.
     * @param boolean $var Standardize variance to be one, default is TRUE.
     *
     * @return array Returns the standardize list of float values using same keys.

     * @url http://en.wikipedia.org/wiki/Feature_scaling#Standardization
     */
    function standardize ($x, $var=true) 
    {
        $mean = mean($x);
        $sd   = sd($x);
        $min  = min($x);
        $max  = max($x);

        foreach ($x as $key => $value) {
            if ($var) {
                $x[$key] = ($value - $mean) / $sd;
            } else {
                $x[$key] = $value - 0.5 * ($min + $max);
            }
        }

        return $x;
    }

    /**
     * The boxplot summarizes a great deal of information very clearly. The 
     * horizontal line shows the median. The bottom and top of the box show 
     * the 25th and 75th percentiles, respectively. The vertical dashed lines 
     * show one of two things: either the maximum value or 1.5 times the 
     * interquartile range of the data (roughly 2 standard deviations). Points 
     * more than 1.5 times the interquartile range above and below are defined 
     * as outliers and plotted individually.
     *          
     * @param array $x List of float values corresponding to a sample of a population.
     *                    
     * @return array Returns min, q1, median, q3, max, and array of outliers 
     *               to render requested boxplot.

     * @url http://en.wikipedia.org/wiki/Boxplot
     */
    function boxplot ($x) 
    {
        sort($x);

        $count  = count($x);
        $median = median($x);
        $sd     = sd($x);
        $q1     = $x[round($count / 4)];
        $q3     = $x[round($count * 3 / 4)];
        $min	= $x[0];
        $max	= $x[$count - 1];

        // Outliars ]median + 2sd, median - 2sd[
        $outliers = array();
        
        if ($min < $median - 2 * $sd) {
            $min = round($median - 2 * $sd, precision);

            foreach ($x as $value) {
                if ($value < $min) {
                    $outliers[] = $value;
                }
            }
        } 

        if ($max > $median + 2 * $sd) {
            $max = round($median + 2 * $sd, precision);

            foreach ($x as $value) {
                if ($value > $max) {
                    $outliers[] = $value;
                }
            }
        }

        return array('min'=>$min, 'q1'=>$q1, 'median'=>$median, 'q3'=>$q3, 'max'=>$max, 'outliers'=>$outliers);
    }

    /**
     * Histogram is a graphical representation showing a visual impression of the 
     * distribution of data. It is consists of tabular  frequencies, shown as adjacent 
     * rectangles, erected over discrete intervals (bins), with an area equal to the 
     * frequency of the observations in the interval.
     *
     * @param array   $x    List of float values corresponding to a sample of a population.
     * @param integer $bins Total number of bins (default is FALSE to calculate optimum number of bins).
     *                    
     * @return array Returns associated array where keys are bin ranges and values are count of 
     *               samples exists in this range to render requested histogram.

     * @url http://en.wikipedia.org/wiki/Histogram
     */
    function hist ($x, $bins=false) 
    {
        if ($bins === false) {
            $bins = ceil(sqrt(count($x)));
        }
    
        $max = max($x);
        $min = min($x);
        $bar = ($max - $min) / $bins;
        
        $bars   = array();
        $ranges = array();
        
        for ($i = 0; $i < $bins; $i++) {
            $bars[$i] = 0;
            
            $low  = round($min + $i * $bar, precision);
            $high = round($min + ($i + 1) * $bar, precision);
            
            $ranges[$i] = "$low-$high";
        }
        
        foreach ($x as $value) {
            if ($value == $max) {
                $bars[$bins-1]++;
            } else {
                $bars[floor(($value - $min) / $bar)]++;
            }
        }

        return(array_combine($ranges, $bars));
    }
    
    /**
     * A normal Q–Q plot comparing randomly generated, independent 
     * standard normal data on the vertical axis to a standard normal 
     * population on the horizontal axis. The linearity of the points 
     * suggests that the data are normally distributed.
     *
     * @param array $y List of float values corresponding to a sample of a population.
     *                    
     * @return array Returns x,y coordinates for each point to render requested Q-Q plot.

     * @url http://en.wikipedia.org/wiki/Q%E2%80%93Q_plot
     */
    function qqnorm ($y) 
    {
        asort($y);
        
        $qqx = array();
        $qqy = array();
        
        $n = count($y);
        $i = 1;

        foreach ($y as $k=>$v) {
            $z = inverseNormCDF(($i - 0.5) / $n);
            $qqx[$k] = $z;
            $qqy[$k] = $v;
            $i++;
        }
        
        ksort($qqx);
        ksort($qqy);
        
        $qq = array();

        $qq['x'] = array_values($qqx);
        $qq['y'] = array_values($qqy);
        
        return $qq;
    }
    
    /**
     * A ternary plot is a barycentric plot on three variables which sum to a constant. 
     * It graphically depicts the ratios of the three variables as positions in an equilateral 
     * triangle. It is used to show the compositions of systems composed of three species.
     *
     * @param array $x List of float values corresponding to the X sample of a population.
     * @param array $y List of float values corresponding to the Y sample of a population.
     * @param array $z List of float values corresponding to the Z sample of a population.
     *                    
     * @return array Returns x,y coordinates for each data point to render requested ternary plot.

     * @url http://en.wikipedia.org/wiki/Ternary_plot
     */
    function ternary ($x, $y, $z)
    {
        $n = count($x);
        
        $x2d = array();
        $y2d = array();
        
        for ($i = 0; $i < $n; $i++) {
            $x2d[] = round(0.5 * (2 * $y[$i] + $z[$i]) / ($x[$i] + $y[$i] + $z[$i]), precision);
            $y2d[] = round((sqrt(3) / 2) * $z[$i] / ($x[$i] + $y[$i] + $z[$i]), precision);
        }
        
        $tri = array();
        
        $tri['x'] = $x2d;
        $tri['y'] = $y2d;
        
        return $tri;
    }
    
    /**
     * A moving average is commonly used with time series data to smooth out 
     * short-term fluctuations and highlight longer-term trends or cycles.
     * Financial applications use mean of the previous n points. However, in 
     * science and engineering the mean is normally taken from an equal number 
     * of data on either side of a central value. This ensures that variations 
     * in the mean are aligned with the variations in the data rather than being 
     * shifted in time.
     *
     * @param array   $x         Vector of input samples listed as float values.
     * @param integer $window    Number of datum points used to calculate moving average.
     * @param boolean $financial If TRUE use mean of the previous n datum points, else mean
     *                           is taken from an equal number of data on either side, 
     *                           default is FALSE.
     *                    
     * @return array List of float values represet calculated moving average.

     * @url http://en.wikipedia.org/wiki/Moving_average
     */
    function movingAvg($x, $window=3, $financial=false) 
    {
        $n = count($x);
        $y = array();
        
        $y[$window - 1] = array_sum(array_slice($x, 0, $window)) / $window;

        for ($i=$window; $i<$n; $i++) {
            $y[$i] = $y[$i-1] + ($x[$i] - $x[$i-$window]) / $window;
        }
        
        if ($financial) {
            $sma = $y;
        } else {
            $shift = floor($window / 2);
            $sma   = array();
            
            for ($i=$window-1; $i<$n; $i++) {
                $sma[$i-$shift] = $y[$i];
            }
        }

        return $sma;
    }
    
    /**
     * A ranking is a relationship between a set of items such that, for any two 
     * items, the first is either 'ranked higher than', 'ranked lower than' or 
     * 'ranked equal to' the second. This method implement standard competition 
     * ranking strategy ("1224" ranking). In competition ranking, items that compare 
     * equal receive the same ranking number, and then a gap is left in the ranking 
     * numbers. The number of ranking numbers that are left out in this gap is one 
     * less than the number of items that compared equal.
     *
     * @param array $list Vector of input samples listed as float values.
     *                    
     * @return array List of integer values represet ranks of input items list.

     * @url http://en.wikipedia.org/wiki/Ranking
     */
    function rank($list) 
    {
        $ranked = array();
        $sorted = $list;
        sort($sorted);
        
        foreach ($list as $x) {
            $ranked[] = array_search($x, $sorted) + 1;
        }
        
        return $ranked;
    }
