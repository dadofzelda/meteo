<?php
    header('Content-Type: text/javascript');
    include("../../../../config.php");
    include($baseURL."css/design.php");
    include($baseURL."header.php");

    include("../../../../plugins/steelSeries/settings.php");


    // use SS default if available, if not use Meteotemplate language strings
    if($lang=="fr"){
        $ssLang = "LANG.FR";
    }
    else if($lang=="de"){
        $ssLang = "LANG.DE";
    }
    else if($lang=="nl"){
        $ssLang = "LANG.NL";
    }
    else if($lang=="se"){
        $ssLang = "LANG.SE";
    }
    else if($lang=="dk"){
        $ssLang = "LANG.DK";
    }
    else if($lang=="fi"){
        $ssLang = "LANG.FI";
    }
    else if($lang=="no"){
        $ssLang = "LANG.NO";
    }
    else if($lang=="it"){
        $ssLang = "LANG.IT";
    }
    else if($lang=="es"){
        $ssLang = "LANG.ES";
    }
    else if($lang=="gr"){
        $ssLang = "LANG.GR";
    }
    else if($lang=="pt"){
        $ssLang = "LANG.PT";
    }
    else if($lang=="cz"){
        $ssLang = "LANG.CS";
    }
    else{
        $ssLang = "LANG.EN";
    }

    // process colors
    $gaugeShadowColor = str_replace("rgb","rgba",$gaugeShadowColor);
    $gaugeShadowColor = str_replace(")",",".$gaugeShadowOpacity.")",$gaugeShadowColor);

    $gaugeMinMaxColor = str_replace("rgb","rgba",$gaugeMinMaxColor);
    $gaugeMinMaxColor = str_replace(")",",".$gaugeMinMaxOpacity.")",$gaugeMinMaxColor);

    $gaugeWindRangeColor = str_replace("rgb","rgba",$gaugeWindRangeColor);
    $gaugeWindRangeColor = str_replace(")",",".$gaugeWindRangeOpacity.")",$gaugeWindRangeColor);

    // knob
    if($gaugeKnobType=="stdSilver"){
        $knobType = "STANDARD_KNOB";
        $knobStyle = "SILVER";
    }
    if($gaugeKnobType=="stdBlack"){
        $knobType = "STANDARD_KNOB";
        $knobStyle = "BLACK";
    }
    if($gaugeKnobType=="stdBrass"){
        $knobType = "STANDARD_KNOB";
        $knobStyle = "BRASS";
    }
    if($gaugeKnobType=="metalSilver"){
        $knobType = "METAL_KNOB";
        $knobStyle = "SILVER";
    }
    if($gaugeKnobType=="metalBlack"){
        $knobType = "METAL_KNOB";
        $knobStyle = "BLACK";
    }
    if($gaugeKnobType=="metalBrass"){
        $knobType = "METAL_KNOB";
        $knobStyle = "BRASS";
    }
    if($gaugeKnobType=="custom"){
        $knobType = $knobCustomType;
        $knobStyle = "CUSTOM";
    }

    // switch to hPa if mmHg selected
    if($displayPressUnits=="mmhg"){
        $displayPressUnits = "hpa";
    }
?>

/*
 * Modified: Jachym, Meteotemplate, 2017
 */

/*!
 * Created by Mark Crossley, July 2011
 *  see scriptVer below for latest release
 *
 * Released under GNU GENERAL PUBLIC LICENSE, Version 2, June 1991
 * See the enclosed License file
 */


/*!
* Tiny Pub/Sub - v0.7.0 - 2013-01-29
* https://github.com/cowboy/jquery-tiny-pubsub
* Copyright (c) 2013 "Cowboy" Ben Alman; Licensed MIT
*/
(function ($) {
    'use strict';
    var o = $({});
    $.subscribe = function () {o.on.apply(o, arguments);};
    $.unsubscribe = function () {o.off.apply(o, arguments);};
    $.publish = function () {o.trigger.apply(o, arguments);};
}(jQuery));

var gauges = (
function () {
    'use strict';
    var strings = <?php echo $ssLang?>,         //Set to your default language. Store all the strings in one object
        config = {
            scriptVer          : '2.5.17',
            weatherProgram     : 4, // set to MB, but using custom Meteotemplate file instead
            imgPathURL         : './images/',
            oldGauges          : 'gauges.htm',
            realtimeInterval   : <?php echo $updateInterval?>,
            longPoll           : false,
            gaugeMobileScaling : 0.85,
            graphUpdateTime    : 15,
            stationTimeout     : <?php echo $offlineInterval?>,
            pageUpdateLimit    : <?php if(isset($pageUpdateLimit)){ echo $pageUpdateLimit;} else{ echo "20";}?>,//period after which the page stops automatically updating, in minutes (default 20)
            pageUpdatePswd     : 'its-me',
            <?php
                if($gaugeDigitalFont){
                    echo "digitalFont        : true,digitalForecast    : true,";
                }
                else{
                    echo "digitalFont        : false,digitalForecast    : false,";
                }
            ?>
            <?php
                if($gaugeTooltips){
                    echo "showPopupData  : true,";
                }
                else{
                    echo "showPopupData  : false,";
                }
            ?>
            showPopupGraphs    : false,
            mobileShowGraphs   : false,
            showWindVariation  : true,
            showWindMetar      : false,
            showIndoorTempHum  : false,
            showCloudGauge     : false,
            <?php
                if($showUV){
                    echo "showUvGauge        : true,";
                }
                else{
                    echo "showUvGauge        : false,";
                }
            ?>
            <?php
                if($solarSensor){
                    echo "showSolarGauge        : true,";
                }
                else{
                    echo "showSolarGauge        : false,";
                }
            ?>
            showSunshineLed    : true,
            showRoseGauge      : true,
            showRoseGaugeOdo   : true,
            showRoseOnDirGauge : true,
            <?php
                if($gaugeShadow){
                    echo "showGaugeShadow        : true,";
                }
                else{
                    echo "showGaugeShadow        : false,";
                }
            ?>
            roundCloudbaseVal  : true,
            realTimeUrlLongPoll: 'realtimegauges-longpoll.php',
            realTimeUrlCumulus : '',
            realTimeUrlWD      : '',
            realTimeUrlVWS     : '',
            realTimeUrlWC      : '',
            realTimeUrlMB      : '<?php echo $pageURL.$path?>plugins/steelSeries/ssMeteotemplate.php',
            realTimeUrlWView   : '',
            realTimeUrlWeewx   : '',
            useCookies         : false,
            tipImages          : [],
            dashboardMode      : false,
            dewDisplayType     : '<?php echo $gaugeAInitial?>'
        },

        gaugeGlobals = {
            minMaxArea            : '<?php echo $gaugeMinMaxColor?>',
            windAvgArea           : '<?php echo $gaugeWindRangeColor?>',
            windVariationSector   : 'rgba(120,200,120,0.7)',
            frameDesign           : steelseries.FrameDesign.<?php echo $gaugeFrameDesign?>,
            background            : steelseries.BackgroundColor.<?php echo $gaugeFrameBackground?>,
            foreground            : steelseries.ForegroundType.<?php echo $gaugeFrameForeground?>,
            pointer               : steelseries.PointerType.<?php echo $gaugeFramePointerType?>,
            pointerColour         : steelseries.ColorDef.<?php echo $gaugeFramePointerColor?>,
            dirAvgPointer         : steelseries.PointerType.<?php echo $gaugeFramePointerType?>,
            dirAvgPointerColour   : steelseries.ColorDef.BLUE,
            gaugeType             : steelseries.GaugeType.TYPE4,
            lcdColour             : steelseries.LcdColor.<?php echo $gaugeFrameLCDColor?>,
            knob                  : steelseries.KnobType.<?php echo $knobType?>,
            knobStyle             : steelseries.KnobStyle.<?php echo $knobStyle?>,
            labelFormat           : steelseries.LabelNumberFormat.STANDARD,
            tickLabelOrientation  : steelseries.TickLabelOrientation.HORIZONTAL,
            rainUseSectionColours : false,
            rainUseGradientColours: false,
            <?php
                if($gaugeTTrend){
                    echo "tempTrendVisible      : true,";
                }
                else{
                    echo "tempTrendVisible      : false,";
                }
            ?>
            <?php
                if($gaugePTrend){
                    echo "pressureTrendVisible  : true,";
                }
                else{
                    echo "pressureTrendVisible  : false,";
                }
            ?>
            <?php
                if($gaugeUVDecimals){
                    echo "uvLcdDecimals         : 1,";
                }
                else{
                    echo "uvLcdDecimals         : 0,";
                }
            ?>
            sunshineThreshold     : 50,
            sunshineThresholdPct  : 75,
            tempScaleDefMinC      : <?php echo number_format($gaugeLimitTempCMin,1,".","")?>,
            tempScaleDefMaxC      : <?php echo number_format($gaugeLimitTempCMax,1,".","")?>,
            tempScaleDefMinF      : <?php echo number_format($gaugeLimitTempFMin,1,".","")?>,
            tempScaleDefMaxF      : <?php echo number_format($gaugeLimitTempFMax,1,".","")?>,
            baroScaleDefMinhPa    : <?php echo $gaugeLimitPressHPaMin?>,
            baroScaleDefMaxhPa    : <?php echo $gaugeLimitPressHPaMax?>,
            baroScaleDefMinkPa    : 99,
            baroScaleDefMaxkPa    : 103,
            baroScaleDefMininHg   : <?php echo number_format($gaugeLimitPressInHgMin,2,".","")?>,
            baroScaleDefMaxinHg   : <?php echo number_format($gaugeLimitPressInHgMax,2,".","")?>,
            windScaleDefMaxMph    : <?php echo number_format($gaugeLimitWindMph,1,".","")?>,
            windScaleDefMaxKts    : <?php echo number_format($gaugeLimitWindKts,1,".","")?>,
            windScaleDefMaxMs     : <?php echo number_format($gaugeLimitWindMs,1,".","")?>,
            windScaleDefMaxKmh    : <?php echo number_format($gaugeLimitWindKmh,1,".","")?>,
            rainScaleDefMaxmm     : <?php echo number_format($gaugeLimitRainMM,1,".","")?>,
            rainScaleDefMaxIn     : <?php echo number_format($gaugeLimitRainIN,2,".","")?>,
            rainRateScaleDefMaxmm : <?php echo number_format($gaugeLimitRainRateMM,1,".","")?>,
            rainRateScaleDefMaxIn : <?php echo number_format($gaugeLimitRainRateIN,2,".","")?>,
            uvScaleDefMax         : <?php echo number_format($gaugeLimitUV,1,".","")?>,
            solarGaugeScaleMax    : <?php echo number_format($gaugeLimitSolar,1,".","")?>,
            cloudScaleDefMaxft    : 3000,
            cloudScaleDefMaxm     : 1000,
            shadowColour          : '<?php echo $gaugeShadowColor?>'
        },

        commonParams = {
            fullScaleDeflectionTime: <?php echo $gaugePointerSpeed?>,
            gaugeType              : gaugeGlobals.gaugeType,
            minValue               : 0,
            niceScale              : true,
            ledVisible             : false,
            frameDesign            : gaugeGlobals.frameDesign,
            backgroundColor        : gaugeGlobals.background,
            foregroundType         : gaugeGlobals.foreground,
            pointerType            : gaugeGlobals.pointer,
            pointerColor           : gaugeGlobals.pointerColour,
            knobType               : gaugeGlobals.knob,
            knobStyle              : gaugeGlobals.knobStyle,
            lcdColor               : gaugeGlobals.lcdColour,
            lcdDecimals            : 1,
            digitalFont            : config.digitalFont,
            tickLabelOrientation   : gaugeGlobals.tickLabelOrientation,
            labelNumberFormat      : gaugeGlobals.labelFormat
        },
        firstRun = true,
        userUnitsSet = false,
        data = {},
        tickTockInterval,
        ajaxDelay = config.longPoll ? Math.max(config.realtimeInterval - 20, 0) : config.realtimeInterval,
        downloadTimer,
        timestamp = 0,
        jqXHR = null,
        displayUnits = null,
        sampleDate,
        realtimeVer,
        programLink = ['','','', '','','',''],

        ledIndicator, statusScroller, statusTimer,

        gaugeTemp, gaugeDew, gaugeRain, gaugeRRate,
        gaugeHum, gaugeBaro, gaugeWind, gaugeDir,
        gaugeUV, gaugeSolar, gaugeCloud, gaugeRose,

        /* _imgBackground,    // Uncomment if using a background image on the gauges */
        init = function (dashboard) {
            $("#loadingDiv").hide();
            switch (config.weatherProgram) {
            case 4:
                realtimeVer = 10;
                config.realTimeURL = config.longPoll ? config.realTimeUrlLongPoll : config.realTimeUrlMB;
                config.showPopupGraphs = false;
                config.showRoseGauge = true;
                config.showCloudGauge = false;
                config.tipImgs = null;
                config.showWindVariation = true;
                break;
            default:
                realtimeVer = 0;
                config.realtimeURL = null;
                config.showPopupGraphs = false;
                config.tipImgs = null;
            }

            if ($(window).width() < 480) {
                config.gaugeScaling = config.gaugeMobileScaling;
                config.showPopupGraphs = config.mobileShowGraphs;
            } else {
                config.gaugeScaling = 1;
            }

            /*
            _imgBackground = document.createElement('img');
            $(_imgBackground).attr('src', config.imgPathURL + 'logoSmall.png');
            */

            displayUnits = getCookie('units');
            if (displayUnits !== null) {
                userUnitsSet = true;
                setRadioCheck('rad_unitsTemp', displayUnits.temp);
                data.tempunit = '°' + displayUnits.temp;
                setRadioCheck('rad_unitsRain', displayUnits.rain);
                data.rainunit = displayUnits.rain;
                setRadioCheck('rad_unitsPress', displayUnits.press);
                data.pressunit = displayUnits.press;
                setRadioCheck('rad_unitsWind', displayUnits.wind);
                data.windunit = displayUnits.wind;
                displayUnits.windrun = getWindrunUnits(data.windunit);
                setRadioCheck('rad_unitsCloud', displayUnits.cloud);
                data.cloudunit = displayUnits.cloud;
            } else {
                displayUnits = {
                    temp   : 'C',
                    rain   : 'mm',
                    press  : 'hPa',
                    wind   : 'km/h',
                    windrun: 'km',
                    cloud  : 'm'
                };

                data.tempunit = '°C';
                data.rainunit = 'mm';
                data.pressunit = 'hPa';
                data.windunit = 'km/h';
                data.cloudunit = 'm';
            }
            if (config.showPopupData) {
                ddimgtooltip.showTips = config.showPopupData;
            }

            if (config.showPopupGraphs) {
                ddimgtooltip.tiparray[0][0] = (config.tipImgs[0][0] === null ? null : '');
                ddimgtooltip.tiparray[1][0] = (config.tipImgs[1][0] === null ? null : '');
                ddimgtooltip.tiparray[2][0] = (config.tipImgs[2]    === null ? null : '');
                ddimgtooltip.tiparray[3][0] = (config.tipImgs[3]    === null ? null : '');
                ddimgtooltip.tiparray[4][0] = (config.tipImgs[4][0] === null ? null : '');
                ddimgtooltip.tiparray[5][0] = (config.tipImgs[5]    === null ? null : '');
                ddimgtooltip.tiparray[6][0] = (config.tipImgs[6]    === null ? null : '');
                ddimgtooltip.tiparray[7][0] = (config.tipImgs[7]    === null ? null : '');
                ddimgtooltip.tiparray[8][0] = (config.tipImgs[8]    === null ? null : '');
                ddimgtooltip.tiparray[9][0] = (config.tipImgs[9]    === null ? null : '');
                ddimgtooltip.tiparray[10][0] = (config.tipImgs[10]  === null ? null : '');
                ddimgtooltip.tiparray[11][0] = (config.tipImgs[11]  === null ? null : '');
            }
            ledIndicator = singleLed.getInstance();
            statusScroller = singleStatus.getInstance();
            statusTimer = singleTimer.getInstance();
            gaugeTemp = singleTemp.getInstance();
            if (gaugeTemp) {gauges.doTemp = gaugeTemp.update;}
            gaugeDew = singleDew.getInstance();
            if (gaugeDew) {gauges.doDew = gaugeDew.update;}
            gaugeHum = singleHum.getInstance();
            if (gaugeHum) {gauges.doHum = gaugeHum.update;}
            gaugeBaro = singleBaro.getInstance();
            gaugeWind = singleWind.getInstance();
            gaugeDir = singleDir.getInstance();
            gaugeRain = singleRain.getInstance();
            gaugeRRate = singleRRate.getInstance();
            if (!config.showUvGauge) {
                $('#canvas_uv').parent().remove();
            } else {
                gaugeUV = singleUV.getInstance();
            }
            if (!config.showSolarGauge) {
                $('#canvas_solar').parent().remove();
            } else {
                gaugeSolar = singleSolar.getInstance();
            }
            if (!config.showRoseGauge) {
                $('#canvas_rose').parent().remove();
            } else {
                gaugeRose = singleRose.getInstance();
                if (gaugeRose) {
                    gaugeRose.setTitle(strings.windrose);
                    gaugeRose.setCompassString(strings.compass);
                }
            }
            if (!config.showCloudGauge) {
                $('#canvas_cloud').parent().remove();
                $('#cloud').parent().remove();
            } else {
                gaugeCloud = singleCloudBase.getInstance();
            }
            changeLang(strings, false);

            if (!dashboard) {
                getRealtime();
                tickTockInterval = setInterval(
                    function () {
                        $.publish('gauges.clockTick', null);
                    },
                    1000);
                if (config.pageUpdateLimit > 0 && getUrlParam('pageUpdate') !== config.pageUpdatePswd) {
                    setTimeout(pageTimeout, config.pageUpdateLimit * 60 * 1000);
                }
            }
        },

        singleLed = (function () {
            var instance;   // Stores a reference to the Singleton
            var led;        // Stores a reference to the SS LED

            function init() {
                // create led indicator
                if ($('#canvas_led').length) {
                    led = new steelseries.Led(
                        'canvas_led', {
                            ledColor: steelseries.LedColor.GREEN_LED,
                            size    : $('#canvas_led').width()
                        });

                    setTitle(strings.led_title);
                }

                function setTitle(newTitle) {
                    $('#canvas_led').attr('title', newTitle);
                }

                function setLedColor(newColour) {
                    if (led) {
                        led.setLedColor(newColour);
                    }
                }

                function setLedOnOff(onState) {
                    if (led) {
                        led.setLedOnOff(onState);
                    }
                }

                function blink(blinkState) {
                    if (led) {
                        led.blink(blinkState);
                    }
                }

                return {
                    setTitle   : setTitle,
                    setLedColor: setLedColor,
                    setLedOnOff: setLedOnOff,
                    blink      : blink
                };
            }

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleStatus = (function () {
            var instance;   // Stores a reference to the Singleton
            var scroller;   // Stores a reference to the SS scrolling display

            function init() {
                // create forecast display
                if ($('#canvas_status').length) {
                    scroller = new steelseries.DisplaySingle(
                        'canvas_status', {
                            width            : $('#canvas_status').width(),
                            height           : $('#canvas_status').height(),
                            lcdColor         : gaugeGlobals.lcdColour,
                            unitStringVisible: false,
                            value            : strings.statusStr,
                            digitalFont      : config.digitalForecast,
                            valuesNumeric    : false,
                            autoScroll       : true,
                            <?php
                                if($gaugeStatusScroll){
                                    echo "alwaysScroll     : true";
                                }
                                else{
                                    echo "alwaysScroll     : false";
                                }
                            ?>

                        });
                }

                function setValue(newTxt) {
                    if (scroller) {
                        scroller.setValue(newTxt);
                    }
                }

                return {setText: setValue};
            }

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleTimer = (function () {
            var instance,   // Stores a reference to the Singleton
                lcd,        // Stores a reference to the SS LED
                count = 1;

            function init() {
                function tick() {
                    if (lcd) {
                        lcd.setValue(count);
                        count += config.longPoll ? 1 : -1;
                    }
                }

                function reset(val) {
                    count = val;
                }

                function setValue(newVal) {
                    if (lcd) {
                        lcd.setValue(newVal);
                    }
                }

                // create timer display
                if ($('#canvas_timer').length) {
                    lcd = new steelseries.DisplaySingle(
                        'canvas_timer', {
                            width            : $('#canvas_timer').width(),
                            height           : $('#canvas_timer').height(),
                            lcdColor         : gaugeGlobals.lcdColour,
                            lcdDecimals      : 0,
                            unitString       : strings.timer,
                            unitStringVisible: true,
                            digitalFont      : config.digitalFont,
                            value            : count
                        });
                    // subcribe to data updates
                    $.subscribe('gauges.clockTick', tick);
                }

                return {
                    reset   : reset,
                    setValue: setValue
                };
            }

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleTemp = (function () {
            var instance;
            var ssGauge;
            var cache = {};

            function init() {
                var params = $.extend(true, {}, commonParams);
                cache.sections = createTempSections(true);
                cache.areas = [];
                cache.minValue = gaugeGlobals.tempScaleDefMinC;
                cache.maxValue = gaugeGlobals.tempScaleDefMaxC;
                cache.title = strings.temp_title_out;
                cache.value = gaugeGlobals.tempScaleDefMinC + 0.0001;
                cache.maxMinVisible = false;
                cache.selected = 'out';

                // create temperature radial gauge
                if ($('#canvas_temp').length) {
                    params.size = Math.ceil($('#canvas_temp').width() * config.gaugeScaling);
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeT?>;
                    params.section = cache.sections;
                    params.area = cache.areas;
                    params.minValue = cache.minValue;
                    params.maxValue = cache.maxValue;

                    params.thresholdVisible = false;
                    params.minMeasuredValueVisible = cache.maxMinVisible;
                    params.maxMeasuredValueVisible = cache.maxMinVisible;
                    params.titleString = cache.title;
                    params.unitString = data.tempunit;
                    params.trendVisible = gaugeGlobals.tempTrendVisible;
                    //params.customLayer = _imgBackground;      // uncomment to add a background image - See Logo Images above

                    ssGauge = new steelseries.Radial('canvas_temp', params);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_temp').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_temp').css(gaugeShadow(params.size));
                    }

                    // remove indoor temperature/humidity options?
                    if (!config.showIndoorTempHum) {
                        $('#rad_temp1').remove();
                        $('#lab_temp1').remove();
                        $('#rad_temp2').remove();
                        $('#lab_temp2').remove();
                        $('#rad_hum1').remove();
                        $('#lab_hum1').remove();
                        $('#rad_hum2').remove();
                        $('#lab_hum2').remove();
                    }

                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    return null;
                }

                function update() {
                    var sel = cache.selected;
                    if (arguments.length === 1) {
                        sel = arguments[0].value;
                    }
                    var t1, scaleStep, tip;

                    cache.minValue = data.tempunit[1] === 'C' ? gaugeGlobals.tempScaleDefMinC : gaugeGlobals.tempScaleDefMinF;
                    cache.maxValue = data.tempunit[1] === 'C' ? gaugeGlobals.tempScaleDefMaxC : gaugeGlobals.tempScaleDefMaxF;

                    if (sel === 'out') {
                        cache.low = extractDecimal(data.tempTL);
                        cache.high = extractDecimal(data.tempTH);
                        cache.lowScale = getMinTemp(cache.minValue);
                        cache.highScale = getMaxTemp(cache.maxValue);
                        cache.value = extractDecimal(data.temp);
                        cache.title = strings.temp_title_out;
                        cache.loc = strings.temp_out_info;
                        cache.trendVal = extractDecimal(data.temptrend);
                        cache.popupImg = 0;
                        if (gaugeGlobals.tempTrendVisible) {
                            t1 = tempTrend(+cache.trendVal, data.tempunit, false);
                            if (t1 === -9999) {
                                // trend value isn't currently available
                                cache.trend = steelseries.TrendState.OFF;
                            } else if (t1 > 0) {
                                cache.trend = steelseries.TrendState.UP;
                            } else if (t1 < 0) {
                                cache.trend = steelseries.TrendState.DOWN;
                            } else {
                                cache.trend = steelseries.TrendState.STEADY;
                            }
                        }
                    } else {
                        cache.low = extractDecimal(data.intemp);
                        cache.lowScale = cache.low;
                        cache.high = cache.low;
                        cache.highScale = cache.low;
                        cache.value = cache.low;
                        cache.title = strings.temp_title_in;
                        cache.loc = strings.temp_in_info;
                        cache.maxMinVisible = false;
                        cache.popupImg = 1;
                        if (gaugeGlobals.tempTrendVisible) {
                            cache.trend = steelseries.TrendState.OFF;
                        }
                    }

                    // has the gauge type changed?
                    if (cache.selected !== sel) {
                        cache.selected = sel;
                        //Change gauge title
                        ssGauge.setTitleString(cache.title);
                        ssGauge.setMaxMeasuredValueVisible(cache.maxMinVisible);
                        ssGauge.setMinMeasuredValueVisible(cache.maxMinVisible);
                        if (config.showPopupGraphs && config.tipImgs[0][cache.popupImg] !== null) {
                            var cacheDefeat = '?' + $('#imgtip0_img').attr('src').split('?')[1];
                            $('#imgtip0_img').attr('src', config.imgPathURL + config.tipImgs[0][cache.popupImg] + cacheDefeat);
                        }
                    }

                    //auto scale the ranges
                    scaleStep = data.tempunit[1] === 'C' ? 10 : 20;
                    while (cache.lowScale < cache.minValue) {
                        cache.minValue -= scaleStep;
                        if (cache.highScale <= cache.maxValue - scaleStep) {
                            cache.maxValue -= scaleStep;
                        }
                    }
                    while (cache.highScale > cache.maxValue) {
                        cache.maxValue += scaleStep;
                        if (cache.minValue >= cache.minValue + scaleStep) {
                            cache.minValue += scaleStep;
                        }
                    }

                    if (cache.minValue !== ssGauge.getMinValue() || cache.maxValue !== ssGauge.getMaxValue()) {
                        ssGauge.setMinValue(cache.minValue);
                        ssGauge.setMaxValue(cache.maxValue);
                        ssGauge.setValue(cache.minValue);
                    }
                    if (cache.selected === 'out') {
                        cache.areas = [steelseries.Section(+cache.low, +cache.high, gaugeGlobals.minMaxArea)];
                    } else {
                        cache.areas = [];
                    }

                    if (gaugeGlobals.tempTrendVisible) {
                        ssGauge.setTrend(cache.trend);
                    }
                    ssGauge.setArea(cache.areas);
                    ssGauge.setValueAnimated(+cache.value);

                    if (ddimgtooltip.showTips) {
                        // update tooltip
                        if (cache.selected === 'out') {
                            tip = cache.loc + ' - ' + strings.lowestF_info + ': ' + cache.low + data.tempunit + ' ' + strings.at + ' ' + data.TtempTL +
                                 ' | ' +
                                 strings.highestF_info + ': ' + cache.high + data.tempunit + ' ' + strings.at + ' ' + data.TtempTH;
                            if (cache.trendVal !== -9999) {
                                tip += '<br>' +
                                    strings.temp_trend_info + ': ' + tempTrend(cache.trendVal, data.tempunit, true) +
                                    ' ' + cache.trendVal + data.tempunit + '/h';
                            }
                        } else {
                            tip = cache.loc + ': ' + data.intemp + data.tempunit;
                        }
                        $('#imgtip0_txt').html(tip);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[0][cache.popupImg] !== null) {
                        $('#imgtip0_img').attr('src', config.imgPathURL + config.tipImgs[0][cache.popupImg] + cacheDefeat);
                    }
                }

                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(), // End singleTemp()

        singleDew = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);
                var tmp;

                //define dew point gauge start values
                cache.sections = createTempSections(true);
                cache.areas = [];
                cache.minValue = gaugeGlobals.tempScaleDefMinC;
                cache.maxValue = gaugeGlobals.tempScaleDefMaxC;
                cache.value = gaugeGlobals.tempScaleDefMinC + 0.0001;
                // Has the end user selected a preferred 'scale' before
                tmp = getCookie('dewGauge');
                cache.selected = tmp !== null ? tmp : config.dewDisplayType;
                setRadioCheck('rad_dew', cache.selected);
                switch (cache.selected) {
                case 'dew':
                    cache.title = strings.dew_title;
                    cache.popupImg = 0;
                    break;
                case 'app':
                    cache.title = strings.apptemp_title;
                    cache.popupImg = 1;
                    break;
                case 'wnd':
                    cache.title = strings.chill_title;
                    cache.popupImg = 2;
                    break;
                case 'hea':
                    cache.title = strings.heat_title;
                    cache.popupImg = 3;
                    break;
                case 'hum':
                    cache.title = strings.humdx_title;
                    cache.popupImg = 4;
                // no default
                }
                cache.minMeasuredVisible = false;
                cache.maxMeasuredVisible = false;

                // create dew point radial gauge
                if ($('#canvas_dew').length) {
                    params.size = Math.ceil($('#canvas_dew').width() * config.gaugeScaling);
                    params.section = cache.sections;
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeA?>;
                    params.area = cache.areas;
                    params.minValue = cache.minValue;
                    params.maxValue = cache.maxValue;
                    params.thresholdVisible = false;
                    params.titleString = cache.title;
                    params.unitString = data.tempunit;

                    ssGauge = new steelseries.Radial('canvas_dew', params);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_dew').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_dew').css(gaugeShadow(params.size));
                    }

                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    // if rad isn't specified, just use existing value
                    var sel = cache.selected;

                    // Argument length === 2 when called from event handler
                    if (arguments.length === 1) {
                        sel = arguments[0].value;
                        // save the choice in a cookie
                        setCookie('dewGauge', sel);
                    }

                    var tip, scaleStep;

                    cache.minValue = data.tempunit[1] === 'C' ? gaugeGlobals.tempScaleDefMinC : gaugeGlobals.tempScaleDefMinF;
                    cache.maxValue = data.tempunit[1] === 'C' ? gaugeGlobals.tempScaleDefMaxC : gaugeGlobals.tempScaleDefMaxF;

                    cache.lowScale = getMinTemp(cache.minValue);
                    cache.highScale = getMaxTemp(cache.maxValue);

                    switch (sel) {
                    case 'dew': // dew point
                        cache.low = extractDecimal(data.dewpointTL);
                        cache.high = extractDecimal(data.dewpointTH);
                        cache.value = extractDecimal(data.dew);
                        cache.areas = [steelseries.Section(+cache.low, +cache.high, gaugeGlobals.minMaxArea)];
                        cache.title = strings.dew_title;
                        cache.minMeasuredVisible = false;
                        cache.maxMeasuredVisible = false;
                        cache.popupImg = 0;
                        tip = strings.dew_info + ':' +
                             '<br>' +
                             '- ' + strings.lowest_info + ': ' + cache.low + data.tempunit + ' ' + strings.at + ' ' + data.TdewpointTL +
                             ' | ' + strings.highest_info + ': ' + cache.high + data.tempunit + ' ' + strings.at + ' ' + data.TdewpointTH;
                        break;
                    case 'app': // apparent temperature
                        cache.low = extractDecimal(data.apptempTL);
                        cache.high = extractDecimal(data.apptempTH);
                        cache.value = extractDecimal(data.apptemp);
                        cache.areas = [steelseries.Section(+cache.low, +cache.high, gaugeGlobals.minMaxArea)];
                        cache.title = strings.apptemp_title;
                        cache.minMeasuredVisible = false;
                        cache.maxMeasuredVisible = false;
                        cache.popupImg = 1;
                        tip = tip = strings.apptemp_info + ':' +
                             '<br>' +
                             '- ' + strings.lowestF_info + ': ' + cache.low + data.tempunit + ' ' + strings.at + ' ' + data.TapptempTL +
                             ' | ' + strings.highestF_info + ': ' + cache.high + data.tempunit + ' ' + strings.at + ' ' + data.TapptempTH;
                        break;
                    case 'wnd': // wind chill
                        cache.low = extractDecimal(data.wchillTL);
                        cache.high = extractDecimal(data.wchill);
                        cache.value = extractDecimal(data.wchill);
                        cache.areas = [];
                        cache.title = strings.chill_title;
                        cache.minMeasuredVisible = true;
                        cache.maxMeasuredVisible = false;
                        cache.popupImg = 2;
                        tip = strings.chill_info + ':' +
                            '<br>' +
                            '- ' + strings.lowest_info + ': ' + cache.low + data.tempunit + ' ' + strings.at + ' ' + data.TwchillTL;
                        break;
                    case 'hea': // heat index
                        cache.low = extractDecimal(data.heatindex);
                        cache.high = extractDecimal(data.heatindexTH);
                        cache.value = extractDecimal(data.heatindex);
                        cache.areas = [];
                        cache.title = strings.heat_title;
                        cache.minMeasuredVisible = false;
                        cache.maxMeasuredVisible = true;
                        cache.popupImg = 3;
                        tip = strings.heat_info + ':' +
                            '<br>' +
                            '- ' + strings.highest_info + ': ' + cache.high + data.tempunit + ' ' + strings.at + ' ' + data.TheatindexTH;
                        break;
                    case 'hum': // humidex
                        cache.low = extractDecimal(data.humidex);
                        cache.high = extractDecimal(data.humidex);
                        cache.value = extractDecimal(data.humidex);
                        cache.areas = [];
                        cache.title = strings.humdx_title;
                        cache.minMeasuredVisible = false;
                        cache.maxMeasuredVisible = false;
                        cache.popupImg = 4;
                        tip = strings.humdx_info + ': ' + cache.value + data.tempunit;
                        break;
                    // no default
                    }

                    if (cache.selected !== sel) {
                        cache.selected = sel;
                        // change gauge title
                        ssGauge.setTitleString(cache.title);
                        // and graph image
                        if (config.showPopupGraphs && config.tipImgs[1][cache.popupImg] !== null) {
                            var cacheDefeat = '?' + $('#imgtip1_img').attr('src').split('?')[1];
                            $('#imgtip1_img').attr('src', config.imgPathURL + config.tipImgs[1][cache.popupImg] + cacheDefeat);
                        }
                    }

                    //auto scale the ranges
                    scaleStep = data.tempunit[1] === 'C' ? 10 : 20;
                    while (cache.lowScale < cache.minValue) {
                        cache.minValue -= scaleStep;
                        if (cache.highScale <= cache.maxValue - scaleStep) {
                            cache.maxValue -= scaleStep;
                        }
                    }
                    while (cache.highScale > cache.maxValue) {
                        cache.maxValue += scaleStep;
                        if (cache.minValue >= cache.minValue + scaleStep) {
                            cache.minValue += scaleStep;
                        }
                    }

                    if (cache.minValue !== ssGauge.getMinValue() || cache.maxValue !== ssGauge.getMaxValue()) {
                        ssGauge.setMinValue(cache.minValue);
                        ssGauge.setMaxValue(cache.maxValue);
                        ssGauge.setValue(cache.minValue);
                    }
                    ssGauge.setMinMeasuredValueVisible(cache.minMeasuredVisible);
                    ssGauge.setMaxMeasuredValueVisible(cache.maxMeasuredVisible);
                    ssGauge.setMinMeasuredValue(+cache.low);
                    ssGauge.setMaxMeasuredValue(+cache.high);
                    ssGauge.setArea(cache.areas);
                    ssGauge.setValueAnimated(+cache.value);

                    if (ddimgtooltip.showTips) {
                        // update tooltip
                        $('#imgtip1_txt').html(tip);
                    }
                }

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[1][cache.popupImg] !== null) {
                        $('#imgtip1_img').attr('src', config.imgPathURL + config.tipImgs[1][cache.popupImg] + cacheDefeat);
                    }
                }

                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(), // End of singleDew()

        singleRain = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);

                // define rain gauge start values
                cache.maxValue = gaugeGlobals.rainScaleDefMaxmm;
                cache.value = 0.0001;
                cache.title = strings.rain_title;
                cache.lcdDecimals = 1;
                cache.scaleDecimals = 1;
                cache.labelNumberFormat = gaugeGlobals.labelFormat;
                cache.sections = (gaugeGlobals.rainUseSectionColours ? createRainfallSections(true) : []);
                cache.valGrad = (gaugeGlobals.rainUseGradientColours ? createRainfallGradient(true) : null);

                // create rain radial bargraph gauge
                if ($('#canvas_rain').length) {
                    params.size = Math.ceil($('#canvas_rain').width() * config.gaugeScaling);
                    params.maxValue = cache.maxValue;
                    params.thresholdVisible = false;
                    params.titleString = cache.title;
                    params.unitString = data.rainunit;
                    params.valueColor = steelseries.ColorDef.<?php echo $gaugeRainColor?>;
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeR?>;
                    params.valueGradient = cache.valGrad;
                    params.useValueGradient = gaugeGlobals.rainUseGradientColours;
                    params.useSectionColors = gaugeGlobals.rainUseSectionColour;
                    params.useSectionColors = gaugeGlobals.rainUseSectionColours;
                    params.labelNumberFormat = cache.labelNumberFormat;
                    params.fractionalScaleDecimals = cache.scaleDecimals;

                    ssGauge = new steelseries.RadialBargraph('canvas_rain', params);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_rain').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_rain').css(gaugeShadow(params.size));
                    }

                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    cache.value = extractDecimal(data.rfall);

                    if (data.rainunit === 'mm') { // 10, 20, 30...
                        cache.maxValue = Math.max(Math.ceil(cache.value / 10) * 10, gaugeGlobals.rainScaleDefMaxmm);
                    } else {
                        // inches 0.5, 1.0, 2.0, 3.0 ... 10.0, 12.0, 14.0
                        if (cache.value < 6) {
                            cache.maxValue = Math.max(Math.ceil(cache.value), gaugeGlobals.rainRateScaleDefMaxIn);
                        } else {
                            cache.maxValue = Math.max(Math.ceil(cache.value / 2) * 2, gaugeGlobals.rainRateScaleDefMaxIn);
                        }
                        cache.scaleDecimals = cache.maxValue < 1 ? 2 : 1;
                    }

                    if (cache.maxValue !== ssGauge.getMaxValue()) {
                        ssGauge.setValue(0);
                        ssGauge.setFractionalScaleDecimals(cache.scaleDecimals);
                        ssGauge.setMaxValue(cache.maxValue);
                    }

                    ssGauge.setValueAnimated(cache.value);

                    if (ddimgtooltip.showTips) {
                        // update tooltip
                        $('#imgtip2_txt').html(strings.LastRain_info + ': ' + data.LastRained);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[2] !== null) {
                        $('#imgtip2_img').attr('src', config.imgPathURL + config.tipImgs[2] + cacheDefeat);
                    }
                }

                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleRRate = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);

                // define rain rate gauge start values
                cache.maxMeasured = 0;
                cache.maxValue = gaugeGlobals.rainRateScaleDefMaxmm;
                cache.value = 0.0001;
                cache.title = strings.rrate_title;
                cache.lcdDecimals = 1;
                cache.scaleDecimals = 0;
                cache.labelNumberFormat = gaugeGlobals.labelFormat;
                cache.sections = createRainRateSections(true);

                // create rain rate radial gauge
                if ($('#canvas_rrate').length) {
                    params.size = Math.ceil($('#canvas_rrate').width() * config.gaugeScaling);
                    params.section = cache.sections;
                    params.maxValue = cache.maxValue;
                    params.thresholdVisible = false;
                    params.maxMeasuredValueVisible = true;
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeRR?>;
                    params.titleString = cache.title;
                    params.unitString = data.rainunit + '/h';
                    params.lcdDecimals = cache.lcdDecimals;
                    params.labelNumberFormat = cache.labelNumberFormat;
                    params.fractionalScaleDecimals = cache.scaleDecimals;
                    params.niceScale = false;

                    ssGauge = new steelseries.Radial('canvas_rrate', params);
                    ssGauge.setMaxMeasuredValue(cache.maxMeasured);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_rrate').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_rrate').css(gaugeShadow(params.size));
                    }

                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    var tip;

                    cache.value = extractDecimal(data.rrate);
                    cache.maxMeasured = extractDecimal(data.rrateTM);
                    cache.overallMax = Math.max(cache.maxMeasured, cache.value);  // workaround for VWS bug, not supplying correct max value today

                    if (data.rainunit === 'mm') { // 10, 20, 30...
                        cache.maxValue = Math.max(Math.ceil(cache.overallMax / 10) * 10, 10);
                    } else {
                        // inches 0.5, 1.0, 2.0, 3.0 ...
                        if (cache.overallMax <= 0.5) {
                            cache.maxValue = 0.5;
                        } else {
                            cache.maxValue = Math.ceil(cache.overallMax);
                        }
                        cache.scaleDecimals = cache.maxValue < 1 ? 2 : (cache.maxValue < 7 ? 1 : 0);
                    }

                    if (cache.maxValue !== ssGauge.getMaxValue()) {
                        ssGauge.setValue(0);
                        ssGauge.setFractionalScaleDecimals(cache.scaleDecimals);
                        ssGauge.setMaxValue(cache.maxValue);
                    }

                    ssGauge.setValueAnimated(cache.value);
                    ssGauge.setMaxMeasuredValue(cache.maxMeasured);

                    if (ddimgtooltip.showTips) {
                        // update tooltip
                        tip = strings.rrate_info + ':<br>' +
                            '- ' + strings.maximum_info + ': ' + data.rrateTM + ' ' + data.rainunit + '/h ' + strings.at + ' ' + data.TrrateTM;
                        $('#imgtip3_txt').html(tip);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[3] !== null) {
                        $('#imgtip3_img').attr('src', config.imgPathURL + config.tipImgs[3] + cacheDefeat);
                    }
                }
                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleHum = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);

                // define humidity gauge start values
                cache.areas = [];
                cache.value = 0.0001;
                cache.title = strings.hum_title_out;
                cache.selected = 'out';

                // create humidity radial gauge
                if ($('#canvas_hum').length) {
                    params.size = Math.ceil($('#canvas_hum').width() * config.gaugeScaling);
                    params.section = [steelseries.Section(0, 20, 'rgba(139, 117, 0, 0.5)'),
                                      steelseries.Section(20, 40, 'rgba(180, 152, 4, 0.3)'),
                                      steelseries.Section(40, 60, 'rgba(170, 212, 4, 0.38)'),
                                      steelseries.Section(60, 80, 'rgba(0, 180, 18,0.3)'),
                                      steelseries.Section(80, 100, 'rgba(0, 180, 18, 0.5)')];
                    params.area = cache.areas;
                    params.maxValue = 100;
                    params.thresholdVisible = false;
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeH?>;
                    params.titleString = cache.title;
                    params.unitString = 'RH%';

                    ssGauge = new steelseries.Radial('canvas_hum', params);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_hum').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_hum').css(gaugeShadow(params.size));
                    }

                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    var radio;
                    // Argument length === 2 when called from event handler
                    if (arguments.length === 1) {
                        radio = arguments[0];
                    }

                    //if rad isn't specified, just use existing value
                    var sel = (typeof radio === 'undefined' ? cache.selected : radio.value),
                        tip;

                    if (sel === 'out') {
                        cache.value = extractDecimal(data.hum);
                        cache.areas = [steelseries.Section(+extractDecimal(data.humTL), +extractDecimal(data.humTH), gaugeGlobals.minMaxArea)];
                        cache.title = strings.hum_title_out;
                        cache.popupImg = 0;
                    } else {
                        cache.value = extractDecimal(data.inhum);
                        if (data.inhumTL && data.inhumTH) {
                            cache.areas = [steelseries.Section(+extractDecimal(data.inhumTL), +extractDecimal(data.inhumTH), gaugeGlobals.minMaxArea)];
                        } else {
                            cache.areas = [];
                        }
                        cache.title = strings.hum_title_in;
                        cache.popupImg = 1;
                    }

                    if (cache.selected !== sel) {
                        cache.selected = sel;
                        //Change gauge title
                        ssGauge.setTitleString(cache.title);
                        if (config.showPopupGraphs && config.tipImgs[4][cache.popupImg] !== null) {
                            var cacheDefeat = '?' + $('#imgtip4_img').attr('src').split('?')[1];
                            $('#imgtip4_img').attr('src', config.imgPathURL + config.tipImgs[4][cache.popupImg] + cacheDefeat);
                        }
                    }

                    ssGauge.setArea(cache.areas);
                    ssGauge.setValueAnimated(cache.value);

                    if (ddimgtooltip.showTips) {
                        //update tooltip
                        if (cache.selected === 'out') {
                            tip = strings.hum_out_info + ':' +
                                '<br>' +
                                '- ' + strings.minimum_info + ': ' + extractDecimal(data.humTL) + '% ' + strings.at + ' ' + data.ThumTL +
                                ' | ' + strings.maximum_info + ': ' + extractDecimal(data.humTH) + '% ' + strings.at + ' ' + data.ThumTH;
                        } else if (data.inhumTL && data.inhumTH) {
                            // we have indoor high/low data
                            tip = strings.hum_in_info + ':' +
                                 '<br>' +
                                '- ' + strings.minimum_info + ': ' + extractDecimal(data.inhumTL) + '% ' + strings.at + ' ' + data.TinhumTL +
                                ' | ' + strings.maximum_info + ': ' + extractDecimal(data.inhumTH) + '% ' + strings.at + ' ' + data.TinhumTH;
                        } else {
                            // no indoor high/low data
                            tip = strings.hum_in_info + ': ' + extractDecimal(data.inhum) + '%';
                        }
                        $('#imgtip4_txt').html(tip);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[4][cache.popupImg] !== null) {
                        $('#imgtip4_img').attr('src', config.imgPathURL + config.tipImgs[4][cache.popupImg] + cacheDefeat);
                    }
                }

                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleBaro = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);

                // define pressure/barometer gauge start values
                cache.sections = [];
                cache.areas = [];
                cache.minValue = gaugeGlobals.baroScaleDefMinhPa;
                cache.maxValue = gaugeGlobals.baroScaleDefMaxhPa;
                cache.value = cache.minValue + 0.0001;
                cache.title = strings.baro_title;
                cache.lcdDecimals = 1;
                cache.scaleDecimals = 0;
                cache.labelNumberFormat = gaugeGlobals.labelFormat;

                // create pressure/barometric radial gauge
                if ($('#canvas_baro').length) {
                    params.size = Math.ceil($('#canvas_baro').width() * config.gaugeScaling);
                    params.section = cache.sections;
                    params.area = cache.areas;
                    params.minValue = cache.minValue;
                    params.maxValue = cache.maxValue;
                    params.niceScale = false;
                    params.thresholdVisible = false;
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeP?>;
                    params.titleString = cache.title;
                    params.unitString = data.pressunit;
                    params.lcdDecimals = cache.lcdDecimals;
                    params.trendVisible = gaugeGlobals.pressureTrendVisible;
                    params.labelNumberFormat = cache.labelNumberFormat;
                    params.fractionalScaleDecimals = cache.scaleDecimals;

                    ssGauge = new steelseries.Radial('canvas_baro', params);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_baro').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_baro').css(gaugeShadow(params.size));
                    }

                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    var tip, t1, dps;

                    cache.recLow = +extractDecimal(data.pressL);
                    cache.recHigh = +extractDecimal(data.pressH);
                    cache.todayLow = +extractDecimal(data.pressTL);
                    cache.todayHigh = +extractDecimal(data.pressTH);
                    cache.value = +extractDecimal(data.press);
                    // Convert the WD change over 3 hours to an hourly rate
                    cache.trendVal = +extractDecimal(data.presstrendval) / (config.weatherProgram === 2 ? 3 : 1);

                    if (data.pressunit === 'hPa' || data.pressunit === 'mb') {
                        //  min range 990-1030 - steps of 10 hPa
                        cache.minValue = Math.min(Math.floor((cache.recLow - 2) / 10) * 10, gaugeGlobals.baroScaleDefMinhPa);
                        cache.maxValue = Math.max(Math.ceil((cache.recHigh + 2) / 10) * 10, gaugeGlobals.baroScaleDefMaxhPa);
                        dps = 1; // 1 decimal place
                    } else if (data.pressunit === 'kPa') {
                        //  min range 99-105 - steps of 1 kPa
                        cache.minValue = Math.min(Math.floor(cache.recLow - 0.2), 99);
                        cache.maxValue = Math.max(Math.ceil(cache.recHigh + 0.2), 105);
                        dps = 2;
                    } else {
                        // inHg: min range 29.5-30.5 - steps of 0.5 inHg
                        cache.minValue = Math.min(Math.floor((cache.recLow - 0.1) * 2) / 2, gaugeGlobals.baroScaleDefMininHg);
                        cache.maxValue = Math.max(Math.ceil((cache.recHigh + 0.1) * 2) / 2, gaugeGlobals.baroScaleDefMaxinHg);
                        dps = 3;
                    }
                    cache.trendValRnd = cache.trendVal.toFixed(dps);
                    cache.todayLowRnd = cache.todayLow.toFixed(dps);
                    cache.todayHighRnd = cache.todayHigh.toFixed(dps);

                    if (cache.minValue !== ssGauge.getMinValue() || cache.maxValue !== ssGauge.getMaxValue()) {
                        ssGauge.setMinValue(cache.minValue);
                        ssGauge.setMaxValue(cache.maxValue);
                        ssGauge.setValue(cache.minValue);
                    }
                    if (cache.recHigh === cache.todayHigh && cache.recLow === cache.todayLow) {
                        // VWS does not provide record hi/lo values
                        cache.sections = [];
                        cache.areas = [steelseries.Section(cache.todayLow, cache.todayHigh, gaugeGlobals.minMaxArea)];
                    } else {
                        cache.sections = [
                            steelseries.Section(cache.minValue, cache.recLow, 'rgba(255,0,0,0.5)'),
                            steelseries.Section(cache.recHigh, cache.maxValue, 'rgba(255,0,0,0.5)')
                        ];
                        cache.areas = [
                            steelseries.Section(cache.minValue, cache.recLow, 'rgba(255,0,0,0.5)'),
                            steelseries.Section(cache.recHigh, cache.maxValue, 'rgba(255,0,0,0.5)'),
                            steelseries.Section(cache.todayLow, cache.todayHigh, gaugeGlobals.minMaxArea)
                        ];
                    }

                    if (gaugeGlobals.pressureTrendVisible) {
                        // Use the baroTrend rather than simple arithmetic test - steady is more/less than zero!
                        t1 = baroTrend(cache.trendVal, data.pressunit, false);
                        if (t1 === -9999) {
                            // trend value isn't currently available
                            cache.trend = steelseries.TrendState.OFF;
                        } else if (t1 > 0) {
                            cache.trend = steelseries.TrendState.UP;
                        } else if (t1 < 0) {
                            cache.trend = steelseries.TrendState.DOWN;
                        } else {
                            cache.trend = steelseries.TrendState.STEADY;
                        }
                        ssGauge.setTrend(cache.trend);
                    }

                    ssGauge.setArea(cache.areas);
                    ssGauge.setSection(cache.sections);
                    ssGauge.setValueAnimated(cache.value);

                    if (ddimgtooltip.showTips) {
                        // update tooltip
                        tip = strings.baro_info + ':' +
                            '<br>' +
                            '- ' + strings.minimum_info + ': ' + cache.todayLowRnd + ' ' + data.pressunit + ' ' + strings.at + ' ' + data.TpressTL +
                            ' | ' + strings.maximum_info + ': ' + cache.todayHighRnd + ' ' + data.pressunit + ' ' + strings.at + ' ' + data.TpressTH;
                        if (cache.trendVal !== -9999) {
                            tip += '<br>' +
                                '- ' + strings.baro_trend_info + ': ' + baroTrend(cache.trendVal, data.pressunit, true) + ' ' +
                                (cache.trendValRnd > 0 ? '+' : '') + cache.trendValRnd + ' ' + data.pressunit + '/h';
                        }
                        $('#imgtip5_txt').html(tip);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[5] !== null) {
                        $('#imgtip5_img').attr('src', config.imgPathURL + config.tipImgs[5] + cacheDefeat);
                    }
                }

                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleWind = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);

                // define wind gauge start values
                cache.maxValue = gaugeGlobals.windScaleDefMaxKph;
                cache.areas = [];
                cache.maxMeasured = 0;
                cache.value = 0.0001;
                cache.title = strings.wind_title;

                // create wind speed radial gauge
                if ($('#canvas_wind').length) {
                    params.size = Math.ceil($('#canvas_wind').width() * config.gaugeScaling);
                    params.area = cache.areas;
                    params.maxValue = cache.maxValue;
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeW?>;
                    params.niceScale = false;
                    params.thresholdVisible = false;
                    params.maxMeasuredValueVisible = true;
                    params.titleString = cache.title;
                    params.unitString = data.windunit;

                    ssGauge = new steelseries.Radial('canvas_wind', params);
                    ssGauge.setMaxMeasuredValue(cache.maxMeasured);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_wind').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_wind').css(gaugeShadow(params.size));
                    }

                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    var tip;

                    cache.value = extractDecimal(data.wlatest);
                    cache.average = extractDecimal(data.wspeed);
                    cache.gust = extractDecimal(data.wgust);
                    cache.maxGustToday = extractDecimal(data.wgustTM);
                    cache.maxAvgToday = extractDecimal(data.windTM);

                    switch (data.windunit) {
                    case 'mph':
                    case 'kts':
                        cache.maxValue = Math.max(Math.ceil(cache.maxGustToday / 10) * 10, gaugeGlobals.windScaleDefMaxMph);
                        break;
                    case 'm/s':
                        cache.maxValue = Math.max(Math.ceil(cache.maxGustToday / 5) * 5, gaugeGlobals.windScaleDefMaxMs);
                        break;
                    default:
                        cache.maxValue = Math.max(Math.ceil(cache.maxGustToday / 20) * 20, gaugeGlobals.windScaleDefMaxKmh);
                    }
                    cache.areas = [
                        steelseries.Section(0, +cache.average, gaugeGlobals.windAvgArea),
                        steelseries.Section(+cache.average, +cache.gust, gaugeGlobals.minMaxArea)
                    ];
                    if (cache.maxValue !== ssGauge.getMaxValue()) {
                        ssGauge.setMaxValue(cache.maxValue);
                    }

                    ssGauge.setArea(cache.areas);
                    ssGauge.setMaxMeasuredValue(cache.maxGustToday);
                    ssGauge.setValueAnimated(cache.value);

                    if (ddimgtooltip.showTips) {
                        // update tooltip
                        tip = strings.tenminavgwind_info + ': ' + cache.average + ' ' + data.windunit + ' | ' +
                              strings.maxavgwind_info + ': ' + cache.maxAvgToday + ' ' + data.windunit + '<br>' +
                              strings.tenmingust_info + ': ' + cache.gust + ' ' + data.windunit + ' | ' +
                              strings.maxgust_info + ': ' + cache.maxGustToday + ' ' + data.windunit + ' ' +
                              strings.at + ' ' + data.TwgustTM + ' ' + strings.bearing_info + ': ' + data.bearingTM +
                              (isNaN(parseFloat(data.bearingTM)) ? '' : '° (' + getord(+data.bearingTM) + ')');
                        $('#imgtip6_txt').html(tip);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[6] !== null) {
                        $('#imgtip6_img').attr('src', config.imgPathURL + config.tipImgs[6] + cacheDefeat);
                    }
                }

                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(), // End of singleWind()

        singleDir = (function () {
            var instance;
            var ssGauge;
            var cache = {};

            function init() {
                var params = $.extend(true, {}, commonParams);
                cache.valueLatest = 0;
                cache.valueAverage = 0;
                cache.titles = [strings.latest_web, strings.tenminavg_web];
                if ($('#canvas_dir').length) {
                    params.size = Math.ceil($('#canvas_dir').width() * config.gaugeScaling);
                    params.pointerTypeLatest = gaugeGlobals.pointer;
                    params.pointerTypeAverage = gaugeGlobals.dirAvgPointer;
                    params.pointerColorAverage = gaugeGlobals.dirAvgPointerColour;
                    params.degreeScale = true;
                    params.pointSymbols = strings.compass;
                    params.roseVisible = false;
                    params.lcdTitleStrings = cache.titles;
                    params.useColorLabels = false;

                    ssGauge = new steelseries.WindDirection('canvas_dir', params);
                    ssGauge.setValueAverage(+cache.valueAverage);
                    ssGauge.setValueLatest(+cache.valueLatest);
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_dir').css({width: params.size + 'px', height: params.size + 'px'});
                    }
                    if (config.showGaugeShadow) {
                        $('#canvas_dir').css(gaugeShadow(params.size));
                    }
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    return null;
                }

                function update() {
                    var windSpd, windGst, range, tip, i,
                        rosePoints = 0,
                        roseMax = 0,
                        roseSectionAngle = 0,
                        roseAreas = [];

                    cache.valueLatest = extractInteger(data.bearing);
                    cache.valueAverage = extractInteger(data.avgbearing);
                    cache.bearingFrom = extractInteger(data.BearingRangeFrom10);
                    cache.bearingTo = extractInteger(data.BearingRangeTo10);

                    ssGauge.setValueAnimatedAverage(+cache.valueAverage);
                    if (cache.valueAverage === 0) {
                        cache.valueLatest = 0;
                    }
                    ssGauge.setValueAnimatedLatest(+cache.valueLatest);

                    if (config.showWindVariation) {
                        windSpd = +extractDecimal(data.wspeed);
                        windGst = +extractDecimal(data.wgust);
                        switch (data.windunit.toLowerCase()) {
                        case 'mph':
                            cache.avgKnots = 0.868976242 * windSpd;
                            cache.gstKnots = 0.868976242 * windGst;
                            break;
                        case 'kts':
                            cache.avgKnots = windSpd;
                            cache.gstKnots = windGst;
                            break;
                        case 'm/s':
                            cache.avgKnots = 1.94384449 * windSpd;
                            cache.gstKnots = 1.94384449 * windGst;
                            break;
                        case 'km/h':
                            cache.avgKnots = 0.539956803 * windSpd;
                            cache.gstKnots = 0.539956803 * windGst;
                            break;
                        // no default
                        }
                        cache.avgKnots = Math.round(cache.avgKnots);
                        cache.gstKnots = Math.round(cache.gstKnots);
                        if (config.showWindMetar) {
                            ssGauge.VRB = ' - METAR: ' + ('0' + data.avgbearing).slice(-3) + ('0' + cache.avgKnots).slice(-2) +
                                        'G' + ('0' + cache.gstKnots).slice(-2) + 'KT ';
                        } else {
                            ssGauge.VRB = '';
                        }
                        if (windSpd > 0) {
                            range = (+cache.bearingTo < +cache.bearingFrom ? 360 + (+cache.bearingTo) : +cache.bearingTo) - (+cache.bearingFrom);

                            if (cache.avgKnots < 3) {
                                if (config.showRoseOnDirGauge) {
                                    ssGauge.setSection([steelseries.Section(cache.bearingFrom, cache.bearingTo, gaugeGlobals.windVariationSector)]);
                                    ssGauge.setSection([]);
                                } else {
                                    ssGauge.setSection([steelseries.Section(cache.bearingFrom, cache.bearingTo, gaugeGlobals.minMaxArea)]);
                                    ssGauge.setArea([]);
                                }
                            } else if (config.showRoseOnDirGauge) {
                                ssGauge.setSection([steelseries.Section(cache.bearingFrom, cache.bearingTo, gaugeGlobals.windVariationSector)]);
                            } else {
                                ssGauge.setSection([]);
                                ssGauge.setArea([steelseries.Section(cache.bearingFrom, cache.bearingTo, gaugeGlobals.minMaxArea)]);
                            }
                            if (config.showWindMetar) {
                                if ((range < 60 && range > 0) || range === 0 && cache.bearingFrom === cache.valueAverage) {
                                    ssGauge.VRB += ' STDY';
                                } else if (cache.avgKnots < 3) {
                                    ssGauge.VRB += ' VRB';
                                } else {
                                    ssGauge.VRB += ' ' + cache.bearingFrom + 'V' + cache.bearingTo;
                                }
                            }
                        } else {
                            if (config.showWindMetar) {
                                ssGauge.VRB = ' - METAR: 00000KT';
                            }
                            ssGauge.setSection([]);
                            if (!config.showRoseOnDirGauge) {
                                ssGauge.setArea([]);
                            }
                        }
                    } else {
                        ssGauge.VRB = '';
                    }
                    if (config.showRoseOnDirGauge && data.WindRoseData) {
                        rosePoints = data.WindRoseData.length;
                        roseSectionAngle = 360 / rosePoints;
                        for (i = 0; i < rosePoints; i++) {
                            roseMax = Math.max(roseMax, data.WindRoseData[i]);
                        }
                        if (roseMax > 0) {
                            for (i = 0; i < rosePoints; i++) {
                                roseAreas[i] = steelseries.Section(
                                    i * roseSectionAngle - roseSectionAngle / 2,
                                    (i + 1) * roseSectionAngle - roseSectionAngle / 2,
                                    'rgba(' + gradient('2020D0', 'D04040', data.WindRoseData[i] / roseMax) + ',' +
                                        (data.WindRoseData[i] / roseMax).toFixed(2) + ')'
                                );
                            }
                        }
                        ssGauge.setArea(roseAreas);
                    }

                    if (ddimgtooltip.showTips) {
                        tip = strings.latest_title + ' ' + strings.bearing_info + ': ' + cache.valueLatest + '° (' + getord(+cache.valueLatest) + ')' +
                              ssGauge.VRB + '<br>' + strings.tenminavg_web + ' ' + strings.bearing_info + ': ' + cache.valueAverage + '° (' +
                              getord(+cache.valueAverage) + '), ' + strings.dominant_bearing + ': ' + data.domwinddir;
                        if (!config.showRoseGauge) {
                            tip += '<br>' + strings.windruntoday + ': ' + data.windrun + ' ' + displayUnits.windrun;
                        }
                        $('#imgtip7_txt').html(tip);
                    }
                }

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[7] !== null) {
                        $('#imgtip7_img').attr('src', config.imgPathURL + config.tipImgs[7] + cacheDefeat);
                    }
                }

                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            }

            return {
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleRose = (function () {
            var instance;
            var ssGauge;

            var buffers = {};
            var cache = {};
            var ctxRoseCanvas;

            cache.firstRun = true;
            cache.odoDigits = <?php echo $gaugeOdoDigits?>;

            function init() {
                var div, roseCanvas;
                if ($('#canvas_rose').length) {
                    cache.gaugeSize = Math.ceil($('#canvas_rose').width() * config.gaugeScaling);
                    cache.gaugeSize2 = cache.gaugeSize / 2;
                    cache.showOdo = config.showRoseGaugeOdo || false;
                    <?php
                        if(!$gaugeOdo){
                            echo "cache.showOdo = false;";
                        }
                    ?>
                    cache.compassString = strings.compass;
                    div = document.createElement('div');
                    div.style.display = 'none';
                    document.body.appendChild(div);
                    cache.plotSize = Math.floor(cache.gaugeSize * 0.68);
                    cache.plotSize2 = cache.plotSize / 2;
                    buffers.plot = document.createElement('canvas');
                    buffers.plot.width = cache.plotSize;
                    buffers.plot.height = cache.plotSize;
                    buffers.plot.id = 'rosePlot';
                    buffers.ctxPlot = buffers.plot.getContext('2d');
                    div.appendChild(buffers.plot);
                    buffers.frame = document.createElement('canvas');
                    buffers.frame.width = cache.gaugeSize;
                    buffers.frame.height = cache.gaugeSize;
                    buffers.ctxFrame = buffers.frame.getContext('2d');
                    steelseries.drawFrame(buffers.ctxFrame, gaugeGlobals.frameDesign, cache.gaugeSize2, cache.gaugeSize2, cache.gaugeSize, cache.gaugeSize);
                    buffers.background = document.createElement('canvas');
                    buffers.background.width = cache.gaugeSize;
                    buffers.background.height = cache.gaugeSize;
                    buffers.ctxBackground = buffers.background.getContext('2d');
                    steelseries.drawBackground(buffers.ctxBackground, gaugeGlobals.background, cache.gaugeSize2,
                                               cache.gaugeSize2, cache.gaugeSize, cache.gaugeSize);
                    /*
                    if (g_imgSmall !== null) {
                        var drawSize = g_size * 0.831775;
                        var x = (g_size - drawSize) / 2;
                        buffers.ctxBackground.drawImage(g_imgSmall, x, x, cache.gaugeSize, cache.gaugeSize);
                    }
                    */
                    drawCompassPoints(buffers.ctxBackground, cache.gaugeSize);
                    buffers.foreground = document.createElement('canvas');
                    buffers.foreground.width = cache.gaugeSize;
                    buffers.foreground.height = cache.gaugeSize;
                    buffers.ctxForeground = buffers.foreground.getContext('2d');
                    steelseries.drawForeground(buffers.ctxForeground, gaugeGlobals.foreground, cache.gaugeSize, cache.gaugeSize, false);

                    roseCanvas = document.getElementById('canvas_rose');
                    ctxRoseCanvas = roseCanvas.getContext('2d');
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_rose').css({width: cache.gaugeSize + 'px', height: cache.gaugeSize + 'px'});
                    }
                    roseCanvas.width = cache.gaugeSize;
                    roseCanvas.height = cache.gaugeSize;
                    if (config.showGaugeShadow) {
                        $('#canvas_rose').css(gaugeShadow(cache.gaugeSize));
                    }
                    ctxRoseCanvas.drawImage(buffers.frame, 0, 0);
                    ctxRoseCanvas.drawImage(buffers.background, 0, 0);
                    ctxRoseCanvas.drawImage(buffers.foreground, 0, 0);

                    if (cache.showOdo) {
                        cache.odoHeight = Math.ceil(cache.gaugeSize * <?php echo $gaugeOdoSize?>);
                        cache.odoWidth = Math.ceil(Math.floor(cache.odoHeight * 0.68) * cache.odoDigits);
                        buffers.Odo = document.createElement('canvas');
                        $(buffers.Odo).attr({
                            id    : 'canvas_odo',
                            width : cache.odoWidth,
                            height: cache.odoHeight
                        });
                        $(buffers.Odo).css({
                            position: 'absolute',
                            top     : Math.ceil(cache.gaugeSize * 0.7 + $('#canvas_rose').position().top) + 'px',
                            left    : Math.ceil((cache.gaugeSize - cache.odoWidth) / 2 + $('#canvas_rose').position().left) + 'px'
                        });
                        $(buffers.Odo).insertBefore('#canvas_rose');
                        ssGauge = new steelseries.Odometer('canvas_odo', {
                            height  : cache.odoHeight,
                            digits  : cache.odoDigits - 1,
                            valueForeColor: "<?php echo $gaugeOdoForeground?>",
                            valueBackColor: "<?php echo $gaugeOdoBackground?>",
                            decimalForeColor: "<?php echo $gaugeOdoForegroundDecimals?>",
                            decimalBackColor: "<?php echo $gaugeOdoBackgroundDecimals?>",
                            decimals: 1
                        });
                    }
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    return null;
                }

                cache.firstRun = false;

                function update() {
                    var rose, offset;
                    if (ctxRoseCanvas && !cache.firstRun) {
                        ctxRoseCanvas.clearRect(0, 0, cache.gaugeSize, cache.gaugeSize);
                        buffers.ctxPlot.clearRect(0, 0, cache.plotSize, cache.plotSize);
                        rose = new RGraph.Rose('rosePlot', data.WindRoseData);
                        rose.Set('chart.strokestyle', 'black');
                        rose.Set('chart.background.axes.color', 'gray');
                        rose.Set('chart.colors.alpha', 0.5);
                        rose.Set('chart.colors', ['Gradient(#408040:red:#7070A0)']);
                        rose.Set('chart.margin', Math.ceil(40 / data.WindRoseData.length));

                        rose.Set('chart.title', cache.gaugeTitle);
                        rose.Set('chart.title.size', Math.ceil(0.05 * cache.plotSize));
                        rose.Set('chart.title.bold', false);
                        rose.Set('chart.title.color', gaugeGlobals.background.labelColor.getRgbColor());
                        rose.Set('chart.gutter.top', 0.2 * cache.plotSize);
                        rose.Set('chart.gutter.bottom', 0.2 * cache.plotSize);

                        rose.Set('chart.tooltips.effect', 'snap');
                        rose.Set('chart.labels.axes', '');
                        rose.Set('chart.background.circles', true);
                        rose.Set('chart.background.grid.spokes', 16);
                        rose.Set('chart.radius', cache.plotSize2);
                        rose.Draw();
                        ctxRoseCanvas.drawImage(buffers.frame, 0, 0);
                        ctxRoseCanvas.drawImage(buffers.background, 0, 0);
                        offset = Math.floor(cache.gaugeSize2 - cache.plotSize2);
                        ctxRoseCanvas.drawImage(buffers.plot, offset, offset);
                        ctxRoseCanvas.drawImage(buffers.foreground, 0, 0);
                        if (cache.showOdo) {
                            ssGauge.setValueAnimated(extractDecimal(data.windrun));
                        }
                        if (ddimgtooltip.showTips) {
                            $('#imgtip10_txt').html(strings.dominant_bearing + ': ' + data.domwinddir + '<br>' +
                                                    strings.windruntoday + ': ' + data.windrun + ' ' + displayUnits.windrun);
                        }
                    }
                }

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[10] !== null) {
                        $('#imgtip10_img').attr('src', config.imgPathURL + config.tipImgs[10] + cacheDefeat);
                    }
                }
                function drawCompassPoints(ctx, size) {
                    ctx.save();
                    ctx.font = 0.08 * size + 'px serif';
                    ctx.fillStyle = gaugeGlobals.background.labelColor.getRgbColor();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    for (var i = 0; i < 4; i++) {
                        ctx.translate(size / 2, size * 0.125);
                        ctx.fillText(cache.compassString[i * 2], 0, 0, size);
                        ctx.translate(-size / 2, -size * 0.125);
                        ctx.translate(size / 2, size / 2);
                        ctx.rotate(Math.PI / 2);
                        ctx.translate(-size / 2, -size / 2);
                    }
                    ctx.restore();
                }

                function setTitle(newTitle) {
                    cache.gaugeTitle = newTitle;
                }

                function setCompassString(newStr) {
                    cache.compassString = newStr;

                    if (!cache.firstRun) {
                        steelseries.drawBackground(buffers.ctxBackground, gaugeGlobals.background, cache.gaugeSize2, cache.gaugeSize2,
                                                   cache.gaugeSize, cache.gaugeSize);
                        drawCompassPoints(buffers.ctxBackground, cache.gaugeSize);
                    }
                }
                return {
                    update           : update,
                    gauge            : ssGauge,
                    drawCompassPoints: drawCompassPoints,
                    setTitle         : setTitle,
                    setCompassString : setCompassString
                };
            }

            return {
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleUV = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);

                // define UV start values
                cache.value = 0.0001;
                cache.sections = [
                    steelseries.Section(0, 2.9, '#289500'),
                    steelseries.Section(2.9, 5.8, '#f7e400'),
                    steelseries.Section(5.8, 7.8, '#f85900'),
                    steelseries.Section(7.8, 10.9, '#d8001d'),
                    steelseries.Section(10.9, 20, '#6b49c8')
                ];
                // Define value gradient for UV
                cache.gradient = new steelseries.gradientWrapper(0, 16,
                    [0, 0.1, 0.19, 0.31, 0.45, 0.625, 1],
                    [
                        new steelseries.rgbaColor(0, 200, 0, 1),
                        new steelseries.rgbaColor(0, 200, 0, 1),
                        new steelseries.rgbaColor(255, 255, 0, 1),
                        new steelseries.rgbaColor(248, 89, 0, 1),
                        new steelseries.rgbaColor(255, 0, 0, 1),
                        new steelseries.rgbaColor(255, 0, 144, 1),
                        new steelseries.rgbaColor(153, 140, 255, 1)
                    ]
                );
                cache.useSections = false;
                cache.useValueGradient = true;

                // create UV bargraph gauge
                if ($('#canvas_uv').length) {
                    params.size = Math.ceil($('#canvas_uv').width() * config.gaugeScaling);
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeUV?>;
                    params.maxValue = gaugeGlobals.uvScaleDefMax;
                    params.titleString = strings.uv_title;
                    params.niceScale = false;
                    params.section = cache.sections;
                    params.useSectionColors = cache.useSections;
                    params.valueGradient = cache.gradient;
                    params.useValueGradient = cache.useValueGradient;
                    params.lcdDecimals = gaugeGlobals.uvLcdDecimals;

                    ssGauge = new steelseries.RadialBargraph('canvas_uv', params);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_uv').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_uv').css(gaugeShadow(params.size));
                    }

                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    var tip, indx;

                    cache.value = extractDecimal(data.UV);

                    if (+cache.value === 0) {
                        indx = 0;
                    } else if (cache.value < 2.5) {
                        indx = 1;
                    } else if (cache.value < 5.5) {
                        indx = 2;
                    } else if (cache.value < 7.5) {
                        indx = 3;
                    } else if (cache.value < 10.5) {
                        indx = 4;
                    } else {
                        indx = 5;
                    }

                    if (cache.value > ssGauge.getMaxValue()) {
                        ssGauge.setMaxValue(Math.ceil(cache.value) + Math.ceil(cache.value) % 2);
                    }

                    cache.risk = strings.uv_levels[indx];
                    cache.headLine = strings.uv_headlines[indx];
                    cache.detail = strings.uv_details[indx];
                    ssGauge.setUnitString(cache.risk);
                    ssGauge.setValueAnimated(cache.value);

                    if (ddimgtooltip.showTips) {
                        // update tooltip
                        tip = '<b>' + strings.uv_title + ': ' + cache.value + '</b> - <i>' + strings.solar_maxToday + ': ' + data.UVTH + '</i><br>';
                        tip += '<i>' + cache.headLine + '</i><br>';
                        tip += cache.detail;
                        $('#imgtip8_txt').html(tip);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[8] !== null) {
                        $('#imgtip8_img').attr('src', config.imgPathURL + config.tipImgs[8] + cacheDefeat);
                    }
                }

                return {
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleSolar = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);

                // define Solar start values
                cache.value = 0.0001;
                cache.sections = [
                    steelseries.Section(0, 200, 'rgba(47, 47, 47, 0.3)'),
                    steelseries.Section(200, 600, 'rgba(254, 251, 184, 0.3)'),
                    steelseries.Section(600, 800, 'rgba(232, 220, 56, 0.3)'),
                    steelseries.Section(800, 1000, 'rgba(255, 246, 24,0.3)'),
                    steelseries.Section(1000, 1800, 'rgba(255, 246, 24, 0.5)')
                ];

                // create Solar gauge
                if ($('#canvas_solar').length) {
                    params.size = Math.ceil($('#canvas_solar').width() * config.gaugeScaling);
                    params.section = cache.sections;
                    params.maxValue = gaugeGlobals.solarGaugeScaleMax;
                    params.titleString = strings.solar_title;
                    params.gaugeType = steelseries.GaugeType.<?php echo $gaugeTypeS?>;
                    params.unitString = 'Wm\u207B\u00B2';
                    params.niceScale = false;
                    params.thresholdVisible = false;
                    params.lcdDecimals = 0;

                    if (config.showSunshineLed) {
                        params.userLedVisible = true;
                        params.userLedColor = steelseries.LedColor.YELLOW_LED;
                    }

                    ssGauge = new steelseries.Radial('canvas_solar', params);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_solar').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_solar').css(gaugeShadow(params.size));
                    }
                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    var tip, percent;

                    cache.value = +extractInteger(data.SolarRad);
                    cache.maxToday = extractInteger(data.SolarTM);
                    cache.currMaxValue = +extractInteger(data.CurrentSolarMax);
                    percent = (+cache.currMaxValue === 0 ? '--' : Math.round(+cache.value / +cache.currMaxValue * 100));

                    // Set a section (100 units wide) to show current theoretical max value
                    if (data.CurrentSolarMax !== 'N/A') {
                        ssGauge.setArea([steelseries.Section(cache.currMaxValue, Math.min(cache.currMaxValue + 100,
                                        gaugeGlobals.solarGaugeScaleMax), 'rgba(220,0,0,0.5)')]);
                    }

                    // Need to rescale the gauge?
                    cache.maxValue = Math.max(
                        Math.ceil(cache.value / 100) * 100,
                        Math.ceil(cache.currMaxValue / 100) * 100,
                        Math.ceil(cache.maxToday / 100) * 100,
                        gaugeGlobals.solarGaugeScaleMax);
                    if (cache.maxValue !== ssGauge.getMaxValue()) {
                        ssGauge.setMaxValue(cache.maxValue);
                    }

                    // Set the values
                    ssGauge.setMaxMeasuredValue(cache.maxToday);
                    ssGauge.setValueAnimated(cache.value);

                    if (config.showSunshineLed) {
                        ssGauge.setUserLedOnOff(percent !== '--' &&
                                                percent >= gaugeGlobals.sunshineThresholdPct &&
                                                +cache.value >= gaugeGlobals.sunshineThreshold);
                    }

                    if (ddimgtooltip.showTips) {
                        // update tooltip
                        tip = '<b>' + strings.solar_title + ': ' + cache.value + ' W/m²</b>';
                        if (typeof data.SolarTM !== 'undefined') {
                            tip += '<br>' + strings.solar_maxToday + ': ' + cache.maxToday + ' W/m²';
                        }
                        $('#imgtip9_txt').html(tip);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[9] !== null) {
                        $('#imgtip9_img').attr('src', config.imgPathURL + config.tipImgs[9] + cacheDefeat);
                    }
                }

                return {
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        singleCloudBase = (function () {
            var instance;   // Stores a reference to the Singleton
            var ssGauge;    // Stores a reference to the SS Gauge
            var cache = {};      // Stores various config values and parameters

            function init() {
                var params = $.extend(true, {}, commonParams);

                cache.sections = createCloudBaseSections(true);
                cache.value = 0.0001;
                cache.maxValue = gaugeGlobals.cloudScaleDefMaxm;

                // create Cloud base radial gauge
                if ($('#canvas_cloud').length) {
                    params.size = Math.ceil($('#canvas_cloud').width() * config.gaugeScaling);
                    params.section = cache.sections;
                    params.maxValue = cache.maxValue;
                    params.titleString = strings.cloudbase_title;
                    params.unitString = strings.metres;
                    params.thresholdVisible = false;
                    params.lcdDecimals = 0;

                    ssGauge = new steelseries.Radial('canvas_cloud', params);
                    ssGauge.setValue(cache.value);

                    // over-ride CSS applied size?
                    if (config.gaugeScaling !== 1) {
                        $('#canvas_cloud').css({width: params.size + 'px', height: params.size + 'px'});
                    }

                    // add a shadow to the gauge
                    if (config.showGaugeShadow) {
                        $('#canvas_cloud').css(gaugeShadow(params.size));
                    }
                    // subcribe to data updates
                    $.subscribe('gauges.dataUpdated', update);
                    $.subscribe('gauges.graphUpdate', updateGraph);
                } else {
                    // cannot draw gauge, return null
                    return null;
                }

                function update() {
                    cache.value = extractInteger(data.cloudbasevalue);

                    if (data.cloudbaseunit === 'm') {
                        // adjust metre gauge in jumps of 1000 metres, don't downscale during the session
                        cache.maxValue = Math.max(Math.ceil(cache.value / 1000) * 1000, gaugeGlobals.cloudScaleDefMaxm, cache.maxValue);
                        if (cache.value <= 1000 && config.roundCloudbaseVal) {
                            // and round the value to the nearest  10 m
                            cache.value = Math.round(cache.value / 10) * 10;
                        } else if (config.roundCloudbaseVal) {
                            // and round the value to the nearest 50 m
                            cache.value = Math.round(cache.value / 50) * 50;
                        }
                    } else {
                        // adjust feet gauge in jumps of 2000 ft, don't downscale during the session
                        cache.maxValue = Math.max(Math.ceil(cache.value / 2000) * 2000, gaugeGlobals.cloudScaleDefMaxft, cache.maxValue);
                        if (cache.value <= 2000 && config.roundCloudbaseVal) {
                            // and round the value to the nearest 50 ft
                            cache.value = Math.round(cache.value / 50) * 50;
                        } else if (config.roundCloudbaseVal) {
                            // and round the value to the nearest 100 ft
                            cache.value = Math.round(cache.value / 100) * 100;
                        }
                    }

                    if (cache.maxValue !== ssGauge.getMaxValue()) {
                        if (ssGauge.getMaxValue() > cache.maxValue) {
                            // Gauge currently showing more than our max (nice scale effct),
                            // so reset our max to match
                            cache.maxValue = ssGauge.getMaxValue();
                        } else {
                            // Gauge scale is too low, increase it.
                            // First set the pointer back to zero so we get a nice animation
                            ssGauge.setValue(0);
                            // and redraw the gauge with teh new scale
                            ssGauge.setMaxValue(cache.maxValue);
                        }
                    }
                    ssGauge.setValueAnimated(cache.value);

                    if (config.showPopupData) {
                        // static tooltip on cloud gauge
                        $('#imgtip11_txt').html('<strong>' + strings.cloudbase_popup_title + '</strong><br>' + strings.cloudbase_popup_text);
                    }
                } // End of update()

                function updateGraph(evnt, cacheDefeat) {
                    if (config.tipImgs[11] !== null) {
                        $('#imgtip11_img').attr('src', config.imgPathURL + config.tipImgs[11] + cacheDefeat);
                    }
                }

                return {
                    data  : cache,
                    update: update,
                    gauge : ssGauge
                };
            } // End of init()

            return {
                // Get the Singleton instance if one exists
                // or create one if it doesn't
                getInstance: function () {
                    if (!instance) {
                        instance = init();
                    }
                    return instance;
                }
            };
        })(),

        getRealtime = function () {
            var url = config.realTimeURL;
            if ($.active > 0) {
                // kill any outstanding requests
                jqXHR.abort();
            }
            if (config.longPoll) {
                url += '?timestamp=' + timestamp;
            }
            jqXHR = $.ajax({url     : url,
                            cache   : (config.longPoll),
                            dataType: 'json',
                            timeout : config.longPoll ? (Math.min(config.realtimeInterval, 20) + 21) * 1000 : 21000 // 21 second time-out by default
                        }).done(function (data) {
                            checkRtResp(data);
                        }).fail(function (xhr, status, err) {
                            checkRtError(xhr, status, err);
                        });
        },
        checkRtResp = function (response) {
            var delay;
            statusTimer.reset(config.longPoll ? 1 : config.realtimeInterval);
            if (config.longPoll && response.status !== 'OK') {
                checkRtError(null, 'PHP Error', response.status);
            } else {
                if (processData(response)) {
                    delay = ajaxDelay;
                } else {
                    delay = 5;
                }
                if (delay > 0) {
                    downloadTimer = setTimeout(getRealtime, delay * 1000);
                } else {
                    getRealtime();
                }
            }
        },

        //
        // checkRtError() called by the Ajax fetch if an error occurs during the fetching realtimegauges.txt
        //
        checkRtError = function (xhr, status, error) {
            if (xhr == null || xhr.statusText !== 'abort') {
                clearTimeout(downloadTimer);
                ledIndicator.setLedOnOff(false);
                ledIndicator.setTitle(strings.led_title_unknown);
                statusScroller.setText(status + ': ' + error);
                downloadTimer = setTimeout(getRealtime, 5000);
            }
        },

        //
        // processData() massages the data returned in realtimegauges.txt, and posts a gauges.dataUpdated event to update the page
        //
        processData = function (dataObj) {
            var str, dt, tm, today, now, then, tmp, elapsedMins, retVal;
            if (config.longPoll) {
                timestamp = dataObj.timestamp;
                data = dataObj.data;
            } else {
                data = dataObj;
            }
            if (typeof data.ver !== 'undefined' && data.ver >= realtimeVer) {
                try {
                    str = data.LastRainTipISO.split(' ');
                    dt = str[0].replace(/\//g, '-').split('-');
                    tm = str[1].split(':');
                    today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (typeof data.dateFormat === 'undefined') {
                        data.dateFormat = 'y/m/d';
                    } else {
                        data.dateFormat = data.dateFormat.replace('%', '');
                    }
                    if (data.dateFormat === 'y/m/d') {
                        then = new Date(dt[0], dt[1] - 1, dt[2], tm[0], tm[1], 0, 0);
                    } else if (data.dateFormat === 'd/m/y') {
                        then = new Date(dt[2], dt[1] - 1, dt[0], tm[0], tm[1], 0, 0);
                    } else {
                        then = new Date(dt[2], dt[0] - 1, dt[1], tm[0], tm[1], 0, 0);
                    }
                    if (then.getTime() >= today.getTime()) {
                        data.LastRained = strings.LastRainedT_info + ' ' + str[1];
                    } else if (then.getTime() + 86400000 >= today.getTime()) {
                        data.LastRained = strings.LastRainedY_info + ' ' + str[1];
                    } else {
                        data.LastRained = then.getDate().toString() + ' ' + strings.months[then.getMonth()] + ' ' + strings.at + ' ' + str[1];
                    }
                } catch (e) {
                    data.LastRained = data.LastRainTipISO;
                }
                if (data.tempunit.length > 1) {
                    data.tempunit = data.tempunit.replace(/&\S*;/, '°');
                } else {
                    data.tempunit = '°' + data.tempunit;
                }

                $("#gaugeSSTime").html(data.date);

                // Check for station off-line
                now = Date.now();
                tmp = data.timeUTC.split(',');
                sampleDate = Date.UTC(tmp[0], tmp[1] - 1, tmp[2], tmp[3], tmp[4], tmp[5]);
                if (now - sampleDate > config.stationTimeout * 60 * 1000) {
                    elapsedMins = Math.floor((now - sampleDate) / (1000 * 60));
                    ledIndicator.setLedColor(steelseries.LedColor.RED_LED);
                    ledIndicator.setTitle(strings.led_title_offline);
                    ledIndicator.blink(true);
                    if (elapsedMins < 120) {
                        tm = elapsedMins.toString() + ' ' + strings.StatusMinsAgo;
                    } else if (elapsedMins < 2 * 24 * 60) {
                        tm = Math.floor(elapsedMins / 60).toString() + ' ' + strings.StatusHoursAgo;
                    } else {
                        tm = Math.floor(elapsedMins / (60 * 24)).toString() + ' ' + strings.StatusDaysAgo;
                    }
                    data.forecast = strings.led_title_offline + ' ' + strings.StatusLastUpdate + ' ' + tm;
                } else if (+data.SensorContactLost === 1) {
                    ledIndicator.setLedColor(steelseries.LedColor.RED_LED);
                    ledIndicator.setTitle(strings.led_title_lost);
                    ledIndicator.blink(true);
                    data.forecast = strings.led_title_lost;
                } else {
                    ledIndicator.setLedColor(steelseries.LedColor.GREEN_LED);
                    ledIndicator.setTitle(strings.led_title_ok + '. ' + strings.StatusLastUpdate + ': ' + data.date);
                    ledIndicator.blink(false);
                    ledIndicator.setLedOnOff(true);
                    data.forecast = strings.StatusLastUpdate + ': ' + data.date;
                }

                data.forecast = $('<div/>').html(data.forecast).text();
                data.forecast = data.forecast.trim();
                data.pressunit = data.pressunit.trim();
                if (data.pressunit === 'in') {
                    data.pressunit = 'inHg';
                }
                data.windunit = data.windunit.trim();
                data.windunit = data.windunit.toLowerCase();
                if (data.windunit === 'knots') {
                    data.windunit = 'kts';
                }

                if (data.windunit === 'kmh' || data.windunit === 'kph') {
                    data.windunit = 'km/h';
                }

                data.rainunit = data.rainunit.trim();

                try {
                    if (data.cloudbaseunit.toLowerCase() === 'metres') {
                        data.cloudbaseunit = 'm';
                    } else if (data.cloudbaseunit.toLowerCase() === 'feet') {
                        data.cloudbaseunit = 'ft';
                    }
                } catch (e) {
                    data.cloudbaseunit = '';
                }
                if (config.showCloudGauge && (
                        (config.weatherProgram === 4 || config.weatherProgram === 5) ||
                        data.cloudbasevalue === '')) {
                    data.cloudbaseunit = (data.windunit === 'mph' || data.windunit === 'kts') ? 'ft' : 'm';
                    data.cloudbasevalue = calcCloudbase(data.temp, data.tempunit, data.dew, data.cloudbaseunit);
                }
                if (data.tempunit[1] !== displayUnits.temp && userUnitsSet) {
                    if (data.tempunit[1] === 'C') {
                        convTempData(c2f);
                    } else {
                        convTempData(f2c);
                    }
                } else if (firstRun) {
                    displayUnits.temp = data.tempunit[1];
                    setRadioCheck('rad_unitsTemp', displayUnits.temp);
                }
                if (data.rainunit !== displayUnits.rain && userUnitsSet) {
                    convRainData(displayUnits.rain === 'mm' ? in2mm : mm2in);
                } else if (firstRun) {
                    displayUnits.rain = data.rainunit;
                    setRadioCheck('rad_unitsRain', displayUnits.rain);
                }
                if (data.windunit !== displayUnits.wind && userUnitsSet) {
                    convWindData(data.windunit, displayUnits.wind);
                } else if (firstRun) {
                    displayUnits.wind = data.windunit;
                    displayUnits.windrun = getWindrunUnits(data.windunit);
                    setRadioCheck('rad_unitsWind', displayUnits.wind);
                }
                if (data.pressunit !== displayUnits.press && userUnitsSet) {
                    convBaroData(data.pressunit, displayUnits.press);
                } else if (firstRun) {
                    displayUnits.press = data.pressunit;
                    setRadioCheck('rad_unitsPress', displayUnits.press);
                }
                if (data.cloudbaseunit !== displayUnits.cloud && userUnitsSet) {
                    convCloudBaseData(displayUnits.cloud === 'm' ? ft2m : m2ft);
                } else if (firstRun) {
                    displayUnits.cloud = data.cloudbaseunit;
                    setRadioCheck('rad_unitsCloud', displayUnits.cloud);
                }

                statusScroller.setText(data.forecast);
                if (firstRun) {
                    doFirst();
                }
                $.publish('gauges.dataUpdated', {});

                retVal = true;
            } else {
                if (data.ver < realtimeVer) {
                    statusTimer.setValue(0);
                    statusScroller.setText('Your ' + config.realTimeURL.substr(config.realTimeURL.lastIndexOf('/') + 1) + ' file template needs updating!');
                    return false;
                } else {
                    statusScroller.setText(strings.realtimeCorrupt);
                }
                ledIndicator.setLedOnOff(false);
                ledIndicator.setTitle(strings.led_title_unknown);

                retVal = false;
            }
            return retVal;
        },

        //
        // pagetimeout() called once every config.pageUpdateLimit minutes to stop updates and prevent page 'sitters'
        //
        pageTimeout = function () {
            statusScroller.setText(strings.StatusPageLimit);
            ledIndicator.setLedColor(steelseries.LedColor.RED_LED);
            ledIndicator.setTitle(strings.StatusPageLimit);
            ledIndicator.blink(true);
            ledIndicator.setTitle(strings.StatusTimeout);
            clearTimeout(downloadTimer);
            if ($.active > 0) {
                jqXHR.abort();
            }
            clearInterval(tickTockInterval);
            statusTimer.setValue(0);
            $('#canvas_led').click(
                function click() {
                    $('#canvas_led').unbind('click');
                    statusTimer.reset(1);
                    tickTockInterval = setInterval(
                        function tick() {
                            $.publish('gauges.clockTick', null);
                        },
                        1000
                    );
                    setTimeout(pageTimeout, config.pageUpdateLimit * 60 * 1000);
                    getRealtime();
                }
            );
        },

        //
        // doFirst() called by doUpdate() the first time the page is updated to set-up various things that are
        // only known when the realtimegauges.txt data is available
        //
        doFirst = function () {
            var cacheDefeat = '?' + (new Date()).getTime().toString();

            if (data.tempunit[1] === 'F') {
                displayUnits.temp = 'F';
                setRadioCheck('rad_unitsTemp', 'F');
                setTempUnits(false);
            }

            if (data.pressunit !== 'hPa') {
                displayUnits.press = data.pressunit;
                setRadioCheck('rad_unitsPress', data.pressunit);
                setBaroUnits(data.pressunit);
            }

            if (data.windunit !== 'km/h') {
                displayUnits.wind = data.windunit;
                setRadioCheck('rad_unitsWind', data.windunit);
                setWindUnits(data.windunit);
            }

            if (data.rainunit !== 'mm') {
                displayUnits.rain = data.rainunit;
                setRadioCheck('rad_unitsRain', data.rainunit);
                setRainUnits(false);
            }

            if (config.showSolarGauge && typeof data.SolarTM !== 'undefined' && gaugeSolar) {
                gaugeSolar.gauge.setMaxMeasuredValueVisible(true);
            }

            if (config.showCloudGauge && data.cloudbaseunit !== 'm') {
                displayUnits.cloud = data.cloudbaseunit;
                setRadioCheck('rad_unitsCloud', data.cloudbaseunit);
                setCloudBaseUnits(false);
            }
            $('#scriptVer').html(config.scriptVer);
            $('#programVersion').html(data.version);
            $('#programBuild').html(data.build);
            $('#programName').html(programLink[config.weatherProgram]);

            if (config.showPopupData) {
                ddimgtooltip.init('[id^="tip_"]');
                if ($(window).width() < 480) {
                    $('.ddimgtooltip').filter(':hidden').width('200px');
                }
            }

            if (config.showPopupData && config.showPopupGraphs) {}

            firstRun = false;
        },

        //
        // createTempSections() creates an array of gauge sections appropriate for Celsius or Fahrenheit scales
        //
        createTempSections = function (celsius) {
            var section;
            if (celsius) {
                section = [
                    steelseries.Section(-100, -35, 'rgba(0, 23, 128, 0.6)'),
                    steelseries.Section(-35, -30, 'rgba(0, 35, 189, 0.6)'),
                    steelseries.Section(-30, -25, 'rgba(0, 44, 240, 0.6)'),
                    steelseries.Section(-25, -20, 'rgba(15, 59, 255, 0.6)'),
                    steelseries.Section(-20, -15, 'rgba(92, 122, 255, 0.6)'),
                    steelseries.Section(-15, -10, 'rgba(61, 97, 255, 0.6)'),
                    steelseries.Section(-10, -5, 'rgba(102, 130, 255, 0.6)'),
                    steelseries.Section(-5, -1, 'rgba(138, 159, 255, 0.6)'),
                    steelseries.Section(-1, 1, 'rgba(255, 255, 255, 0.6)'),
                    steelseries.Section(1, 5, 'rgba(222, 211, 211, 0.6)'),
                    steelseries.Section(5, 10, 'rgba(227, 206, 206, 0.6)'),
                    steelseries.Section(10, 15, 'rgba(212, 175, 175, 0.6)'),
                    steelseries.Section(15, 20, 'rgba(209, 143, 143, 0.6)'),
                    steelseries.Section(20, 25, 'rgba(233, 83, 83, 0.6)'),
                    steelseries.Section(25, 30, 'rgba(217, 28, 28, 0.6)'),
                    steelseries.Section(30, 40, 'rgba(172, 22, 22, 0.6)'),
                    steelseries.Section(40, 100, 'rgba(113, 15, 15, 0.6)')
                ];
            } else {
                section = [
                    steelseries.Section(-200, -30, 'rgba(0, 23, 128, 0.6)'),
                    steelseries.Section(-30, -25, 'rgba(0, 35, 189, 0.6)'),
                    steelseries.Section(-25, -15, 'rgba(0, 44, 240, 0.6)'),
                    steelseries.Section(-15, -5, 'rgba(15, 59, 255, 0.6)'),
                    steelseries.Section(-5, 5, 'rgba(61, 97, 255, 0.6)'),
                    steelseries.Section(5, 15, 'rgba(102, 130, 255, 0.6)'),
                    steelseries.Section(15, 25, 'rgba(138, 159, 255, 0.6)'),
                    steelseries.Section(25, 30, 'rgba(179, 193, 255, 0.6)'),
                    steelseries.Section(30, 34, 'rgba(255, 255, 255, 0.6)'),
                    steelseries.Section(32, 40, 'rgba(222, 211, 211, 0.6)'),
                    steelseries.Section(40, 50, 'rgba(227, 206, 206, 0.6)'),
                    steelseries.Section(50, 60, 'rgba(212, 175, 175, 0.6)'),
                    steelseries.Section(60, 70, 'rgba(209, 143, 143, 0.6)'),
                    steelseries.Section(70, 80, 'rgba(233, 83, 83, 0.6)'),
                    steelseries.Section(80, 90, 'rgba(217, 28, 28, 0.6)'),
                    steelseries.Section(90, 110, 'rgba(172, 22, 22, 0.6)'),
                    steelseries.Section(110, 200, 'rgba(113, 15, 15, 0.6)')
                ];
            }
            return section;
        },

        //
        // createRainSections() returns an array of section highlights for the Rain Rate gauge
        //
        /*
          Assumes 'standard' descriptive limits from UK met office:
           < 0.25 mm/hr - Very light rain
           0.25mm/hr to 1.0mm/hr - Light rain
           1.0 mm/hr to 4.0 mm/hr - Moderate rain
           4.0 mm/hr to 16.0 mm/hr - Heavy rain
           16.0 mm/hr to 50 mm/hr - Very heavy rain
           > 50.0 mm/hour - Extreme rain

           Roughly translated to the corresponding Inch rates
           < 0.001
           0.001 to 0.05
           0.05 to 0.20
           0.20 to 0.60
           0.60 to 2.0
           > 2.0
        */
        createRainRateSections = function (metric) {
            var factor = metric ? 1 : 1 / 25;
            return [steelseries.Section(0, 0.25 * factor, 'rgba(203, 231, 255, 0.5)'),
                    steelseries.Section(0.25 * factor, 1 * factor, 'rgba(130, 214, 255, 0.5)'),
                    steelseries.Section(1 * factor, 4 * factor, 'rgba(80, 117, 212, 0.5)'),
                    steelseries.Section(4 * factor, 16 * factor, 'rgba(45, 75, 235, 0.68)'),
                    steelseries.Section(16 * factor, 50 * factor, 'rgba(22, 45, 201, 0.8)'),
                    steelseries.Section(50 * factor, 1000 * factor, 'rgba(36, 0, 255, 0.92)')];
        },

        //
        // createRainFallSections()returns an array of section highlights for total rainfall in mm or inches
        //
        createRainfallSections = function (metric) {
            var factor = metric ? 1 : 1 / 25;
            return [steelseries.Section(0, 5 * factor, 'rgba(0, 250, 0, 1)'),
                    steelseries.Section(5 * factor, 10 * factor, 'rgba(0, 250, 117, 1)'),
                    steelseries.Section(10 * factor, 25 * factor, 'rgba(218, 246, 0, 1)'),
                    steelseries.Section(25 * factor, 40 * factor, 'rgba(250, 186, 0, 1)'),
                    steelseries.Section(40 * factor, 50 * factor, 'rgba(250, 95, 0, 1)'),
                    steelseries.Section(50 * factor, 65 * factor, 'rgba(250, 0, 0, 1)'),
                    steelseries.Section(65 * factor, 75 * factor, 'rgba(250, 6, 80, 1)'),
                    steelseries.Section(75 * factor, 100 * factor, 'rgba(205, 18, 158, 1)'),
                    steelseries.Section(100 * factor, 125 * factor, 'rgba(0, 0, 250, 1)'),
                    steelseries.Section(125 * factor, 500 * factor, 'rgba(0, 219, 212, 1)')];
        },

        //
        // createRainfallGradient() returns an array of SS colours for continuous gradient colouring of the total rainfall LED gauge
        //
        createRainfallGradient = function (metric) {
            var grad = new steelseries.gradientWrapper(
                0,
                (metric ? 100 : 4),
                [0, 0.1, 0.62, 1],
                [new steelseries.rgbaColor(15, 148, 0, 1),
                 new steelseries.rgbaColor(213, 213, 0, 1),
                 new steelseries.rgbaColor(213, 0, 25, 1),
                 new steelseries.rgbaColor(250, 0, 0, 1)]
            );
            return grad;
        },

        //
        // createClousBaseSections() returns an array of section highlights for the Cloud Base gauge
        //
        createCloudBaseSections = function (metric) {
            var section;
            if (metric) {
                section = [steelseries.Section(0, 150, 'rgba(245, 86, 59, 0.5)'),
                            steelseries.Section(150, 300, 'rgba(225, 155, 105, 0.5)'),
                            steelseries.Section(300, 750, 'rgba(212, 203, 109, 0.5)'),
                            steelseries.Section(750, 1000, 'rgba(150, 203, 150, 0.5)'),
                            steelseries.Section(1000, 1500, 'rgba(80, 192, 80, 0.5)'),
                            steelseries.Section(1500, 2500, 'rgba(0, 140, 0, 0.5)'),
                            steelseries.Section(2500, 5500, 'rgba(19, 103, 186, 0.5)')];
            } else {
                section = [steelseries.Section(0, 500, 'rgba(245, 86, 59, 0.5)'),
                            steelseries.Section(500, 1000, 'rgba(225, 155, 105, 0.5)'),
                            steelseries.Section(1000, 2500, 'rgba(212, 203, 109, 0.5)'),
                            steelseries.Section(2500, 3500, 'rgba(150, 203, 150, 0.5)'),
                            steelseries.Section(3500, 5500, 'rgba(80, 192, 80, 0.5)'),
                            steelseries.Section(5500, 8500, 'rgba(0, 140, 0, 0.5)'),
                            steelseries.Section(8500, 18000, 'rgba(19, 103, 186, 0.5)')];
            }
            return section;
        },

        //
        //--------------- Helper functions ------------------
        //

        //
        // getord() converts a value in degrees (0-360) into a localised compass point (N, ENE, NE, etc)
        //
        getord = function (deg) {
            if (deg === 0) {
                // Special case, 0=No wind, 360=North
                return strings.calm;
            } else {
                return (strings.coords[Math.floor((deg + 11.25) / 22.5) % 16]);
            }
        },

        //
        // getUrlParam() extracts the named parameter from the current page URL
        //
        getUrlParam = function (paramName) {
            var name, regexS, regex, results;
            name = paramName.replace(/(\[|\])/g, '\\$1');
            regexS = '[\\?&]' + name + '=([^&#]*)';
            regex = new RegExp(regexS);
            results = regex.exec(window.location.href);
            if (results === null) {
                return '';
            } else {
                return results[1];
            }
        },

        //
        // extractDecimal() returns a decimal number from a string, the decimal point can be either a dot or a comma
        // it ignores any text such as pre/appended units
        //
        extractDecimal = function (str, errVal) {
            try {
                return (/[\-+]?[0-9]+\.?[0-9]*/).exec(str.replace(',', '.'))[0];
            } catch (e) {
                // error condition
                return errVal || -9999;
            }
        },

        //
        // extractInteger() returns an integer from a string
        // it ignores any text such as pre/appended units
        //
        extractInteger = function (str, errVal) {
            try {
                return (/[\-+]?[0-9]+/).exec(str)[0];
            } catch (e) {
                // error condition
                return errVal || -9999;
            }
        },

        //
        // tempTrend() converts a temperature trend value into a localised string, or +1, 0, -1 depending on the value of bTxt
        //
        tempTrend = function (trend, units, bTxt) {
            // Scale is over 3 hours, in Celsius
            var val = trend * 3 * (units[1] === 'C' ? 1 : (5 / 9)),
                ret;
            if (trend === -9999) {
                ret = (bTxt ? '--' : trend);
            } else if (val > 5) {
                ret = (bTxt ? strings.RisingVeryRapidly : 1);
            } else if (val > 3) {
                ret = (bTxt ? strings.RisingQuickly : 1);
            } else if (val > 1) {
                ret = (bTxt ? strings.Rising : 1);
            } else if (val > 0.5) {
                ret = (bTxt ? strings.RisingSlowly : 1);
            } else if (val >= -0.5) {
                ret = (bTxt ? strings.Steady : 0);
            } else if (val >= -1) {
                ret = (bTxt ? strings.FallingSlowly : -1);
            } else if (val >= -3) {
                ret = (bTxt ? strings.Falling : -1);
            } else if (val >= -5) {
                ret = (bTxt ? strings.FallingQuickly : -1);
            } else {
                ret = (bTxt ? strings.FallingVeryRapidly : -1);
            }
            return ret;
        },

        //
        // baroTrend() converts a pressure trend value into a localised string, or +1, 0, -1 depending on the value of bTxt
        //
        baroTrend = function (trend, units, bTxt) {
            var val = trend * 3,
                ret;
            if (units === 'inHg') {
                val *= 33.8639;
            } else if (units === 'kPa') {
                val *= 10;
            }
            if (trend === -9999) {
                ret = (bTxt ? '--' : trend);
            } else if (val > 6.0) {
                ret = (bTxt ? strings.RisingVeryRapidly : 1);
            } else if (val > 3.5) {
                ret = (bTxt ? strings.RisingQuickly : 1);
            } else if (val > 1.5) {
                ret = (bTxt ? strings.Rising : 1);
            } else if (val > 0.1) {
                ret = (bTxt ? strings.RisingSlowly : 1);
            } else if (val >= -0.1) {
                ret = (bTxt ? strings.Steady : 0);
            } else if (val >= -1.5) {
                ret = (bTxt ? strings.FallingSlowly : -1);
            } else if (val >= -3.5) {
                ret = (bTxt ? strings.Falling : -1);
            } else if (val >= -6.0) {
                ret = (bTxt ? strings.FallingQuickly : -1);
            } else {
                ret = (bTxt ? strings.FallingVeryRapidly : -1);
            }
            return ret;
        },

        //
        // getMinTemp() returns the lowest temperature today for gauge scaling
        //
        getMinTemp = function (deflt) {
            return Math.min(
                extractDecimal(data.tempTL, deflt),
                extractDecimal(data.dewpointTL, deflt),
                extractDecimal(data.apptempTL, deflt));
        },

        //
        // getMaxTemp() returns the highest temperature today for gauge scaling
        //
        getMaxTemp = function (deflt) {
            return Math.max(
                extractDecimal(data.tempTH, deflt),
                extractDecimal(data.apptempTH, deflt));
        },

        c2f = function c2f(val) {
            return (extractDecimal(val) * 9 / 5 + 32).toFixed(1);
        },
        f2c = function f2c(val) {
            return ((extractDecimal(val) - 32) * 5 / 9).toFixed(1);
        },
        mph2ms = function mph2ms(val) {
            return (extractDecimal(val) * 0.447).toFixed(1);
        },
        kts2ms = function kts2ms(val) {
            return (extractDecimal(val) * 0.515).toFixed(1);
        },
        kmh2ms = function kmh2ms(val) {
            return (extractDecimal(val) * 0.2778).toFixed(1);
        },
        ms2kts = function ms2kts(val) {
            return (extractDecimal(val) * 1.9426).toFixed(1);
        },
        ms2mph = function ms2mph(val) {
            return (extractDecimal(val) * 2.237).toFixed(1);
        },
        ms2kmh = function ms2kmh(val) {
            return (extractDecimal(val) * 3.6).toFixed(1);
        },
        mm2in = function mm2in(val) {
            return (extractDecimal(val) / 25.4).toFixed(2);
        },
        in2mm = function in2mm(val) {
            return (extractDecimal(val) * 25.4).toFixed(1);
        },
        miles2km = function miles2km(val) {
            return (extractDecimal(val) * 1.609344).toFixed(1);
        },
        nmiles2km = function nmiles2km(val) {
            return (extractDecimal(val) * 1.85200).toFixed(1);
        },
        km2miles = function km2miles(val) {
            return (extractDecimal(val) / 1.609344).toFixed(1);
        },
        km2nmiles = function km2nmiles(val) {
            return (extractDecimal(val) / 1.85200).toFixed(1);
        },
        hpa2inhg = function hpa2inhg(val, decimals) {
            return (extractDecimal(val) * 0.029528744).toFixed(decimals || 3);
        },
        inhg2hpa = function inhg2hpa(val) {
            return (extractDecimal(val) / 0.029528744).toFixed(1);
        },
        kpa2hpa = function kpa2hpa(val) {
            return (extractDecimal(val) * 10).toFixed(1);
        },
        hpa2kpa = function hpa2kpa(val, decimals) {
            return (extractDecimal(val) / 10).toFixed(decimals || 2);
        },
        m2ft = function m2ft(val) {
            return (val * 3.2808399).toFixed(0);
        },
        ft2m = function ft2m(val) {
            return (val / 3.2808399).toFixed(0);
        },

        //
        // setCookie() writes the 'obj' in cookie 'name' for persistent storage
        //
        setCookie = function (name, obj) {
            var date = new Date(),
                expires;
            date.setYear(date.getFullYear() + 1);
            expires = '; expires=' + date.toGMTString();
            document.cookie = name + '=' + encodeURIComponent(JSON.stringify(obj)) + expires;
        },

        //
        // getCookie() reads the value of cookie 'name' from persistent storage
        //
        getCookie = function (name) {
            var i, x, y,
                ret = null,
                arrCookies = document.cookie.split(';');

            for (i = arrCookies.length; i--;) {
                x = arrCookies[i].split('=');
                if (x[0].trim() === name) {
                    try {
                        y = decodeURIComponent(x[1]);
                    } catch (e) {
                        y = x[1];
                    }
                    ret = JSON.parse(unescape(y));
                    break;
                }
            }
            return ret;
        },

        //
        // setRadioCheck() sets the desired value of the HTML radio buttons to be selected
        //
        setRadioCheck = function (obj, val) {
            $('input:radio[name="' + obj + '"]').filter('[value="' + val + '"]').prop('checked', true);
        },

        //
        // convTempData() converts all the temperature values using the supplied conversion function
        //
        convTempData = function (convFunc) {
            data.apptemp = convFunc(data.apptemp);
            data.apptempTH = convFunc(data.apptempTH);
            data.apptempTL = convFunc(data.apptempTL);
            data.dew = convFunc(data.dew);
            data.dewpointTH = convFunc(data.dewpointTH);
            data.dewpointTL = convFunc(data.dewpointTL);
            data.heatindex = convFunc(data.heatindex);
            data.heatindexTH = convFunc(data.heatindexTH);
            data.humidex = convFunc(data.humidex);
            data.intemp = convFunc(data.intemp);
            data.temp = convFunc(data.temp);
            data.tempTH = convFunc(data.tempTH);
            data.tempTL = convFunc(data.tempTL);
            data.wchill = convFunc(data.wchill);
            data.wchillTL = convFunc(data.wchillTL);
            if (convFunc === c2f) {
                data.temptrend = (+extractDecimal(data.temptrend) * 9 / 5).toFixed(1);
                data.tempunit = '°F';
            } else {
                data.temptrend = (+extractDecimal(data.temptrend) * 5 / 9).toFixed(1);
                data.tempunit = '°C';
            }
        },

        //
        // convRainData() converts all the rain data units using the supplied conversion function
        //
        convRainData = function (convFunc) {
            data.rfall = convFunc(data.rfall);
            data.rrate = convFunc(data.rrate);
            data.rrateTM = convFunc(data.rrateTM);
            data.hourlyrainTH = convFunc(data.hourlyrainTH);
            data.rainunit = convFunc === mm2in ? 'in' : 'mm';
        },

        //
        // convWindData() converts all the wind values using the supplied conversion function
        //
        convWindData = function (from, to) {
            var fromFunc1, toFunc1,
                fromFunc2, toFunc2,
                dummy = function (val) {
                    return val;
                };

            // convert to m/s & km
            switch (from) {
            case 'mph':
                fromFunc1 = mph2ms;
                fromFunc2 = miles2km;
                break;
            case 'kts':
                fromFunc1 = kts2ms;
                fromFunc2 = nmiles2km;
                break;
            case 'km/h':
                fromFunc1 = kmh2ms;
                fromFunc2 = dummy;
                break;
            case 'm/s':
            // falls through
            default:
                fromFunc1 = dummy;
                fromFunc2 = dummy;
            }
            // conversion function from km to required units
            switch (to) {
            case 'mph':
                toFunc1 = ms2mph;
                toFunc2 = km2miles;
                displayUnits.windrun = 'miles';
                break;
            case 'kts':
                toFunc1 = ms2kts;
                toFunc2 = km2nmiles;
                displayUnits.windrun = 'n.miles';
                break;
            case 'km/h':
                toFunc1 = ms2kmh;
                toFunc2 = dummy;
                displayUnits.windrun = 'km';
                break;
            case 'm/s':
            default:
                toFunc1 = dummy;
                toFunc2 = dummy;
                displayUnits.windrun = 'km';
            }
            data.wgust = toFunc1(fromFunc1(data.wgust));
            data.wgustTM = toFunc1(fromFunc1(data.wgustTM));
            data.windTM = toFunc1(fromFunc1(data.windTM));
            data.windrun = toFunc2(fromFunc2(data.windrun));
            data.wlatest = toFunc1(fromFunc1(data.wlatest));
            data.wspeed = toFunc1(fromFunc1(data.wspeed));
            data.windunit = to;
        },

        //
        // convBaroData() converts all the pressure values using the supplied conversion function
        //
        convBaroData = function (from, to) {
            var fromFunc, toFunc,
                dummy = function (val) {
                    return val;
                };
            switch (from) {
            case 'hPa':
            case 'mb':
                fromFunc = dummy;
                break;
            case 'inHg':
                fromFunc = inhg2hpa;
                break;
            case 'kPa':
                fromFunc = kpa2hpa;
                break;
            }
            switch (to) {
            case 'hPa':
            case 'mb':
                toFunc = dummy;
                break;
            case 'inHg':
                toFunc = hpa2inhg;
                break;
            case 'kPa':
                toFunc = hpa2kpa;
                break;
            }

            data.press = toFunc(fromFunc(data.press));
            data.pressH = toFunc(fromFunc(data.pressH));
            data.pressL = toFunc(fromFunc(data.pressL));
            data.pressTH = toFunc(fromFunc(data.pressTH));
            data.pressTL = toFunc(fromFunc(data.pressTL));
            data.presstrendval = toFunc(fromFunc(data.presstrendval), 3);
            data.pressunit = to;
        },

        //
        // convCloudBaseData() converts all the cloud base data units using the supplied conversion function
        //
        convCloudBaseData = function (convFunc) {
            data.cloudbasevalue = convFunc(data.cloudbasevalue);
            data.cloudbaseunit = convFunc === m2ft ? 'ft' : 'm';
        },

        //
        // setUnits() Main data conversion routine, calls all the setXXXX() sub-routines
        //
        setUnits = function (radio) {
            var sel = radio.value;

            userUnitsSet = true;

            switch (sel) {
            // == Temperature ==
            case 'C':
                displayUnits.temp = sel;
                if (data.tempunit[1] !== sel) {
                    setTempUnits(true);
                    convTempData(f2c);
                    if (gaugeTemp) {gaugeTemp.update();}
                    if (gaugeDew) {gaugeDew.update();}
                }
                break;
            case 'F':
                displayUnits.temp = sel;
                if (data.tempunit[1] !== sel) {
                    setTempUnits(false);
                    convTempData(c2f);
                    if (gaugeTemp) {gaugeTemp.update();}
                    if (gaugeDew) {gaugeDew.update();}
                }
                break;
            // == Rainfall ==
            case 'mm':
                displayUnits.rain = sel;
                if (data.rainunit !== sel) {
                    setRainUnits(true);
                    convRainData(in2mm);
                    if (gaugeRain) {gaugeRain.update();}
                    if (gaugeRRate) {gaugeRRate.update();}
                }
                break;
            case 'in':
                displayUnits.rain = sel;
                if (data.rainunit !== sel) {
                    setRainUnits(false);
                    convRainData(mm2in);
                    if (gaugeRain) {gaugeRain.update();}
                    if (gaugeRRate) {gaugeRRate.update();}
                }
                break;
            // == Pressure ==
            case 'hPa':
            // falls through
            case 'inHg':
            // falls through
            case 'mb':
            // falls through
            case 'kPa':
                displayUnits.press = sel;
                if (data.pressunit !== sel) {
                    convBaroData(data.pressunit, sel);
                    setBaroUnits(sel);
                    if (gaugeBaro) {gaugeBaro.update();}
                }
                break;
            // == Wind speed ==
            case 'mph':
            // falls through
            case 'kts':
            // falls through
            case 'm/s':
            // falls through
            case 'km/h':
                displayUnits.wind = sel;
                if (data.windunit !== sel) {
                    convWindData(data.windunit, sel);
                    setWindUnits(sel);
                    if (gaugeWind) {gaugeWind.update();}
                    if (gaugeDir) {gaugeDir.update();}
                    if (gaugeRose) {gaugeRose.update();}
                }
                break;
            // == CloudBase ==
            case 'm':
                displayUnits.cloud = sel;
                if (data.cloudbaseunit !== sel) {
                    setCloudBaseUnits(true);
                    convCloudBaseData(ft2m);
                    if (gaugeCloud) {gaugeCloud.update();}
                }
                break;
            case 'ft':
                displayUnits.cloud = sel;
                if (data.cloudbaseunit !== sel) {
                    setCloudBaseUnits(false);
                    convCloudBaseData(m2ft);
                    if (gaugeCloud) {gaugeCloud.update();}
                }
                break;
            // no default
            }
            if (config.useCookies) {
                setCookie('units', displayUnits);
            }
        },

        setTempUnits = function (celsius) {
            if (celsius) {
                data.tempunit = '°C';
                if (gaugeTemp) {
                    gaugeTemp.data.sections = createTempSections(true);
                    gaugeTemp.data.minValue = gaugeGlobals.tempScaleDefMinC;
                    gaugeTemp.data.maxValue = gaugeGlobals.tempScaleDefMaxC;
                }
                if (gaugeDew) {
                    gaugeDew.data.sections = createTempSections(true);
                    gaugeDew.data.minValue = gaugeGlobals.tempScaleDefMinC;
                    gaugeDew.data.maxValue = gaugeGlobals.tempScaleDefMaxC;
                }
            } else {
                data.tempunit = '°F';
                if (gaugeTemp) {
                    gaugeTemp.data.sections = createTempSections(false);
                    gaugeTemp.data.minValue = gaugeGlobals.tempScaleDefMinF;
                    gaugeTemp.data.maxValue = gaugeGlobals.tempScaleDefMaxF;
                }
                if (gaugeDew) {
                    gaugeDew.data.sections = createTempSections(false);
                    gaugeDew.data.minValue = gaugeGlobals.tempScaleDefMinF;
                    gaugeDew.data.maxValue = gaugeGlobals.tempScaleDefMaxF;
                }
            }
            if (gaugeTemp) {
                gaugeTemp.gauge.setUnitString(data.tempunit);
                gaugeTemp.gauge.setSection(gaugeTemp.data.sections);
            }
            if (gaugeDew) {
                gaugeDew.gauge.setUnitString(data.tempunit);
                gaugeDew.gauge.setSection(gaugeTemp.data.sections);
            }
        },

        setRainUnits = function (mm) {
            if (mm) {
                data.rainunit = 'mm';
                if (gaugeRain) {
                    gaugeRain.data.lcdDecimals = 1;
                    gaugeRain.data.scaleDecimals = 1;
                    gaugeRain.data.labelNumberFormat = gaugeGlobals.labelFormat;
                    gaugeRain.data.sections = (gaugeGlobals.rainUseSectionColours ? createRainfallSections(true) : []);
                    gaugeRain.data.maxValue = gaugeGlobals.rainScaleDefmm;
                    gaugeRain.data.grad = (gaugeGlobals.rainUseGradientColours ? createRainfallGradient(true) : null);
                }
                if (gaugeRRate) {
                    gaugeRRate.data.lcdDecimals = 1;
                    gaugeRRate.data.scaleDecimals = 0;
                    gaugeRRate.data.labelNumberFormat = gaugeGlobals.labelFormat;
                    gaugeRRate.data.sections = createRainRateSections(true);
                    gaugeRRate.data.maxValue = gaugeGlobals.rainRateScaleDefmm;
                }
            } else {
                data.rainunit = 'in';
                if (gaugeRain) {
                    gaugeRain.data.lcdDecimals = 2;
                    gaugeRain.data.scaleDecimals = gaugeGlobals.rainScaleDefIn < 1 ? 2 : 1;
                    gaugeRain.data.labelNumberFormat = steelseries.LabelNumberFormat.FRACTIONAL;
                    gaugeRain.data.sections = (gaugeGlobals.rainUseSectionColours ? createRainfallSections(false) : []);
                    gaugeRain.data.maxValue = gaugeGlobals.rainScaleDefIn;
                    gaugeRain.data.grad = (gaugeGlobals.rainUseGradientColours ? createRainfallGradient(false) : null);
                }
                if (gaugeRRate) {
                    gaugeRRate.data.lcdDecimals = 2;
                    gaugeRRate.data.scaleDecimals = gaugeGlobals.rainRateScaleDefIn < 1 ? 2 : 1;
                    gaugeRRate.data.labelNumberFormat = steelseries.LabelNumberFormat.FRACTIONAL;
                    gaugeRRate.data.sections = createRainRateSections(false);
                    gaugeRRate.data.maxValue = gaugeGlobals.rainRateScaleDefIn;
                }
            }
            if (gaugeRain) {
                gaugeRain.data.value = 0;
                gaugeRain.gauge.setUnitString(data.rainunit);
                gaugeRain.gauge.setSection(gaugeRain.data.sections);
                gaugeRain.gauge.setGradient(gaugeRain.data.grad);
                gaugeRain.gauge.setFractionalScaleDecimals(gaugeRain.data.scaleDecimals);
                gaugeRain.gauge.setLabelNumberFormat(gaugeRain.data.labelNumberFormat);
                gaugeRain.gauge.setLcdDecimals(gaugeRain.data.lcdDecimals);
            }
            if (gaugeRRate) {
                gaugeRRate.data.value = 0;
                gaugeRRate.gauge.setUnitString(data.rainunit + '/h');
                gaugeRRate.gauge.setSection(gaugeRRate.data.sections);
                gaugeRRate.gauge.setFractionalScaleDecimals(gaugeRRate.data.scaleDecimals);
                gaugeRRate.gauge.setLabelNumberFormat(gaugeRRate.data.labelNumberFormat);
                gaugeRRate.gauge.setLcdDecimals(gaugeRRate.data.lcdDecimals);
            }
        },

        setWindUnits = function (to) {
            var maxVal;
            if (!gaugeWind) {return;}

            // conversion function to required units
            switch (to) {
            case 'mph':
                maxVal = gaugeGlobals.windScaleDefMaxMph;
                break;
            case 'kts':
                maxVal = gaugeGlobals.windScaleDefMaxKts;
                break;
            case 'km/h':
                maxVal = gaugeGlobals.windScaleDefMaxKmh;
                break;
            case 'm/s':
                maxVal = gaugeGlobals.windScaleDefMaxMs;
                break;
            // no default
            }
            // set the gauges
            data.windunit = to;
            gaugeWind.data.maxValue = maxVal;
            gaugeWind.gauge.setUnitString(data.windunit);
            gaugeWind.gauge.setValue(0);
        },

        setBaroUnits = function (to) {
            var minVal, maxVal;

            if (!gaugeBaro) {return;}

            // set to the required units
            switch (to) {
            case 'hPa':
            // falls through
            case 'mb':
                minVal = gaugeGlobals.baroScaleDefMinhPa;
                maxVal = gaugeGlobals.baroScaleDefMaxhPa;
                gaugeBaro.data.lcdDecimals = 1;
                gaugeBaro.data.scaleDecimals = 0;
                gaugeBaro.data.labelNumberFormat = gaugeGlobals.labelFormat;
                break;
            case 'inHg':
                minVal = gaugeGlobals.baroScaleDefMininHg;
                maxVal = gaugeGlobals.baroScaleDefMaxinHg;
                gaugeBaro.data.lcdDecimals = 2;
                gaugeBaro.data.scaleDecimals = 1;
                gaugeBaro.data.labelNumberFormat = steelseries.LabelNumberFormat.FRACTIONAL;
                break;
            case 'kPa':
                minVal = gaugeGlobals.baroScaleDefMinkPa;
                maxVal = gaugeGlobals.baroScaleDefMaxkPa;
                gaugeBaro.data.lcdDecimals = 2;
                gaugeBaro.data.scaleDecimals = 1;
                gaugeBaro.data.labelNumberFormat = steelseries.LabelNumberFormat.FRACTIONAL;
                break;
            // no default
            }

            data.pressunit = to;
            gaugeBaro.gauge.setUnitString(to);
            gaugeBaro.gauge.setLcdDecimals(gaugeBaro.data.lcdDecimals);
            gaugeBaro.gauge.setFractionalScaleDecimals(gaugeBaro.data.scaleDecimals);
            gaugeBaro.gauge.setLabelNumberFormat(gaugeBaro.data.labelNumberFormat);
            gaugeBaro.data.minValue = minVal;
            gaugeBaro.data.maxValue = maxVal;
            gaugeBaro.data.value = gaugeBaro.data.minValue;
        },

        setCloudBaseUnits = function (m) {
            if (!gaugeCloud) {return;}

            if (m) {
                gaugeCloud.data.sections = createCloudBaseSections(true);
                gaugeCloud.data.maxValue = gaugeGlobals.cloudScaleDefMaxm;
            } else {
                gaugeCloud.data.sections = createCloudBaseSections(false);
                gaugeCloud.data.maxValue = gaugeGlobals.cloudScaleDefMaxft;
            }
            gaugeCloud.data.value = 0;
            gaugeCloud.gauge.setUnitString(m ? strings.metres : strings.feet);
            gaugeCloud.gauge.setSection(gaugeCloud.data.sections);
        },

        //
        // setLang() switches the HTML page language set, called by changeLang() in language.js
        //
        setLang = function (newLang) {
            strings = newLang;
            // temperature
            if (gaugeTemp) {
                if ($('#rad_temp1').is(':checked')) {
                    gaugeTemp.data.title = strings.temp_title_out;
                } else {
                    gaugeTemp.data.title = strings.temp_title_in;
                }
                gaugeTemp.gauge.setTitleString(gaugeTemp.data.title);
                if (data.ver) {gaugeTemp.update();}
            }
            if (gaugeDew) {
                switch ($('input[name="rad_dew"]:checked').val()) {
                case 'dew':
                    gaugeDew.data.title = strings.dew_title;
                    break;
                case 'app':
                    gaugeDew.data.title = strings.apptemp_title;
                    break;
                case 'wnd':
                    gaugeDew.data.title = strings.chill_title;
                    break;
                case 'hea':
                    gaugeDew.data.title = strings.heat_title;
                    break;
                case 'hum':
                    gaugeDew.data.title = strings.humdx_title;
                    break;
                // no default
                }
                gaugeDew.gauge.setTitleString(gaugeDew.data.title);
                if (data.ver) {gaugeDew.update();}
            }
            // rain
            if (gaugeRain) {
                gaugeRain.data.title = strings.rain_title;
                gaugeRain.gauge.setTitleString(gaugeRain.data.title);
                if (data.ver) {gaugeRain.update();}
            }
            // rrate
            if (gaugeRRate) {
                gaugeRRate.data.title = strings.rrate_title;
                gaugeRRate.gauge.setTitleString(gaugeRRate.data.title);
                if (data.ver) {gaugeRRate.update();}
            }
            // humidity
            if (gaugeHum) {
                if ($('#rad_hum1').is(':checked')) {
                    gaugeHum.data.title = strings.hum_title_out;
                } else {
                    gaugeHum.data.title = strings.hum_title_in;
                }
                gaugeHum.gauge.setTitleString(gaugeHum.data.title);
                if (data.ver) {gaugeHum.update();}
            }
            // barometer
            if (gaugeBaro) {
                gaugeBaro.data.title = strings.baro_title;
                gaugeBaro.gauge.setTitleString(gaugeBaro.data.title);
                if (data.ver) {gaugeBaro.update();}
            }
            // wind
            if (gaugeWind) {
                gaugeWind.data.title = strings.wind_title;
                gaugeWind.gauge.setTitleString(gaugeWind.data.title);
                if (data.ver) {gaugeWind.update();}
            }
            if (gaugeDir) {
                gaugeDir.gauge.setPointSymbols(strings.compass);
                gaugeDir.data.titles = [strings.latest_web, strings.tenminavg_web];
                gaugeDir.gauge.setLcdTitleStrings(gaugeDir.data.titles);
                if (data.ver) {gaugeDir.update();}
            }
            if (gaugeUV) {
                gaugeUV.gauge.setTitleString(strings.uv_title);
                if (data.ver) {gaugeUV.update();}
            }
            if (gaugeSolar) {
                gaugeSolar.gauge.setTitleString(strings.solar_title);
                if (data.ver) {gaugeSolar.update();}
            }
            if (gaugeRose) {
                gaugeRose.setTitle(strings.windrose);
                gaugeRose.setCompassString(strings.compass);
                if (data.ver) {gaugeRose.update();}
            }
            if (gaugeCloud) {
                // Cloudbase
                gaugeCloud.data.units = data.cloudunit === 'm' ? strings.metres : strings.feet;
                gaugeCloud.gauge.setTitleString(strings.cloudbase_title);
                gaugeCloud.gauge.setUnitString(gaugeCloud.data.units);
                if (data.ver) {gaugeCloud.update();}
            }
        },

        //
        // return windrun units based on the windspeed units
        //
        getWindrunUnits = function (spdUnits) {
            var retVal;
            switch (spdUnits) {
            case 'mph':
                retVal = 'miles';
                break;
            case 'kts':
                retVal = 'n.miles';
                break;
            case 'km/h':
            // falls through
            case 'm/s':
            // falls through
            default:
                retVal = 'km';
                break;
            }
            return retVal;
        },

        //
        // performs a simple cloudbase calculation for those weather programs that don't supply it
        //
        calcCloudbase = function (temp, tempunit, dew, cloudbaseunit) {
            var sprd = temp - dew;
            var cb = sprd * (tempunit[1] === 'C' ? 400 : 227.3); // cloud base in feet
            if (cloudbaseunit === 'm') {
                cb = ft2m(cb);
            }
            return cb;
        },

        //
        // create a shadow effect for the gauge using CSS
        //
        gaugeShadow = function (size) {
            var offset = Math.floor(size * <?php echo $gaugeShadowSize?>);
            return {
                'box-shadow'   : offset + 'px ' + offset + 'px ' + offset + 'px ' + gaugeGlobals.shadowColour,
                'border-radius': Math.floor(size / 2) + 'px'
            };
        },

        //
        // generate a colour gradient based on start and end values
        //
        gradient = function (startCol, endCol, fraction) {
            var redOrigin, grnOrigin, bluOrigin,
                gradientSizeRed, gradientSizeGrn, gradientSizeBlu;

            redOrigin = parseInt(startCol.substr(0, 2), 16);
            grnOrigin = parseInt(startCol.substr(2, 2), 16);
            bluOrigin = parseInt(startCol.substr(4, 2), 16);

            gradientSizeRed = parseInt(endCol.substr(0, 2), 16)  - redOrigin; //Graduation Size Red
            gradientSizeGrn = parseInt(endCol.substr(2, 2), 16)  - grnOrigin;
            gradientSizeBlu = parseInt(endCol.substr(4, 2), 16)  - bluOrigin;

            return (redOrigin + (gradientSizeRed * fraction)).toFixed(0) + ',' +
                    (grnOrigin + (gradientSizeGrn * fraction)).toFixed(0) + ',' +
                    (bluOrigin + (gradientSizeBlu * fraction)).toFixed(0);
        };
        // ########################################################
        // End of gauges() var declarations
        // ########################################################

    //
    // Execution starts here
    //

    if (!document.createElement('canvas').getContext) {
        $('body').html(strings.canvasnosupport);
        setTimeout(function () {
            window.location = config.oldGauges;
        }, 3000);
        return false;
    } else {
        $(document).ready(function () {
            init(config.dashboardMode);
        });
    }

    return {
        setLang    : setLang,
        setUnits   : setUnits,
        processData: processData
    };
}()),

    /*!
    * Image w/ description tooltip v2.0  -  For FF1+ IE6+ Opr8+
    * Created: April 23rd, 2010. This notice must stay intact for usage
    * Author: Dynamic Drive at http://www.dynamicdrive.com/
    * Visit http://www.dynamicdrive.com/ for full source code
    * Modified: M Crossley June 2011, January 2012
    * v2.-
    */
    ddimgtooltip = {
        tiparray: (function () {
            var style = {background: '#FFFFFF', color: 'black', border: '2px ridge darkblue'},
                i = 12,  // set to number of tooltips required
                tooltips = [];
            for (;i--;) {
                tooltips[i] = [null, ' ', style];
            }
            return tooltips;
        }()),

        tooltipoffsets: [20, -55], //additional x and y offset from mouse cursor for tooltips

        tipDelay: <?php echo $gaugeTooltipDelay*10?>,

        delayTimer: 0,

        tipprefix: 'imgtip', //tooltip DOM ID prefixes

        createtip: function ($, tipid, tipinfo) {
            if ($('#' + tipid).length === 0) { //if this tooltip doesn't exist yet
                return $('<div id="' + tipid + '" class="ddimgtooltip" />')
                            .html(
                                ((tipinfo[1]) ? '<div class="tipinfo" id="' + tipid + '_txt">' + tipinfo[1] + '</div>' : '') +
                                (tipinfo[0] !== null ? '<div style="text-align:center"><img class="tipimg" id="' + tipid +
                                '_img" src="' + tipinfo[0] + '" /></div>' : '')
                            )
                            .css(tipinfo[2] || {})
                            .appendTo(document.body);
            }
            return null;
        },

        positiontooltip: function ($, $tooltip, e) {
            var x = e.pageX + this.tooltipoffsets[0],
                y = e.pageY + this.tooltipoffsets[1],
                tipw = $tooltip.outerWidth(),
                tiph = $tooltip.outerHeight(),
                wWidth = $(window).width(),
                wHght = $(window).height(),
                dTop = $(document).scrollTop();

            x = (x + tipw > $(document).scrollLeft() + wWidth) ? x - tipw - (ddimgtooltip.tooltipoffsets[0] * 2) : x;
            y = (y + tiph > dTop + wHght) ? dTop + wHght - tiph - 10 : y;
            x = Math.max(x, 0);
            if (tipw >= wWidth) {
                $($tooltip.attr('id') + '_img').css({width: wWidth - 20});
                $('#' + $tooltip.attr('id') + '_img').css({width: wWidth - 20});
                y = e.pageY + 5;
            }
            $tooltip.css({left: x, top: y});
        },

        delaybox: function ($, $tooltip) {
            if (this.showTips) {
                ddimgtooltip.delayTimer = setTimeout(
                    function () {
                        ddimgtooltip.showbox($tooltip);
                    }, ddimgtooltip.tipDelay);
            }
        },

        showbox: function (tooltip) {
            if (this.showTips) {
                //$(tooltip).show();
                $(tooltip).fadeIn(<?php echo $gaugeTooltipFadeInSpeed?>);
            }
        },

        hidebox: function ($, $tooltip) {
            clearTimeout(ddimgtooltip.delayTimer);
            //$tooltip.hide();
            $tooltip.fadeOut(<?php echo $gaugeTooltipFadeOutSpeed?>);
        },

        showTips: false,

        init: function (targetselector) {
            var tiparray = ddimgtooltip.tiparray,
                $targets = $(targetselector);

            if ($targets.length === 0) {
                return;
            }
            $targets.each(function () {
                var $target = $(this),
                    tipsuffix, tipid,
                    $tooltip;
                var ind = ($target.attr('id').match(/_(\d+)/) || [])[1] || ''; //match d of attribute id='tip_d'
                tipsuffix = parseInt(ind, 10); //get d as integer
                tipid = this.tipid = ddimgtooltip.tipprefix + tipsuffix; //construct this tip's ID value and remember it
                $tooltip = ddimgtooltip.createtip($, tipid, tiparray[tipsuffix]);

                $target.<?php echo $gaugeTooltipsTrigger?>(function (e) {
                    var $tooltip = $('#' + this.tipid);
                    ddimgtooltip.delaybox($, $tooltip, e);
                });
                $target.mouseleave(function () {
                    var $tooltip = $('#' + this.tipid);
                    ddimgtooltip.hidebox($, $tooltip);
                });
                $target.mousemove(function (e) {
                    var $tooltip = $('#' + this.tipid);
                    ddimgtooltip.positiontooltip($, $tooltip, e);
                });
                if ($tooltip) {
                    $tooltip.mouseenter(function () {
                        ddimgtooltip.hidebox($, $(this));
                    });
                }
            });
        }
    };

// if String doesn't offer a .trim() method, add it
String.prototype.trim = String.prototype.trim || function trim() {
    return this.replace(/^\s+|\s+$/g, '');
};
