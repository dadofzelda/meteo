var radial1,
        radial2,
        radial3,
        radial4,
        radial5,
        radial6,
        radial7,
        radial8,
        //radial9,
        vertical1,
        vertical2,
        vertical3,
        odoValue = 99998.2;


    function init() {
        // Define some sections
        var sections = [steelseries.Section(0, 25, 'rgba(0, 0, 220, 0.3)'),
                        steelseries.Section(25, 50, 'rgba(0, 220, 0, 0.3)'),
                        steelseries.Section(50, 75, 'rgba(220, 220, 0, 0.3)') ],

            // Define one area
            areas = [steelseries.Section(75, 100, 'rgba(220, 0, 0, 0.3)')],

            // Define value gradient for bargraph
            valGrad = new steelseries.gradientWrapper(  0,
                                                        100,
                                                        [ 0, 0.33, 0.66, 0.85, 1],
                                                        [ new steelseries.rgbaColor(0, 0, 200, 1),
                                                          new steelseries.rgbaColor(0, 200, 0, 1),
                                                          new steelseries.rgbaColor(200, 200, 0, 1),
                                                          new steelseries.rgbaColor(200, 0, 0, 1),
                                                          new steelseries.rgbaColor(200, 0, 0, 1) ]);

        // Initialzing gauges
        radial1 = new steelseries.Radial('canvasRadial1', {
                            gaugeType: steelseries.GaugeType.TYPE1,
                            size: 201,
                            section: sections,
                            area: areas,
                            titleString: 'Title',
                            unitString: 'Type 1',
                            threshold: 50,
                            lcdVisible: true
                        });

        radial2 = new steelseries.Radial('canvasRadial2', {
                            gaugeType: steelseries.GaugeType.TYPE2,
                            size: 201,
                            section: sections,
                            area: areas,
                            titleString: 'Title',
                            unitString: 'Type 2',
                            threshold: 50,
                            lcdVisible: true
                        });

        radial3 = new steelseries.Radial('canvasRadial3', {
                            gaugeType: steelseries.GaugeType.TYPE3,
                            size: 201,
                            section: sections,
                            area: areas,
                            titleString: "Title",
                            unitString: "Type 3",
                            lcdVisible: true
                        });

        radial4 = new steelseries.Radial('canvasRadial4', {
                            gaugeType: steelseries.GaugeType.TYPE4,
                            size: 201,
                            section: sections,
                            area: areas,
                            titleString: "Title",
                            unitString: "Type 4",
                            threshold: 50,
                            lcdVisible: true
                        });

        radial5 = new steelseries.RadialBargraph('canvasRadial5', {
                            gaugeType: steelseries.GaugeType.TYPE1,
                            size: 201,
                            titleString: 'Title',
                            unitString: 'Type 5',
                            lcdVisible: true
                        });

        radial6 = new steelseries.RadialBargraph('canvasRadial6', {
                            gaugeType: steelseries.GaugeType.TYPE2,
                            size: 201,
                            titleString: 'Title',
                            unitString: 'Type 6',
                            lcdVisible: true
                        });

        radial7 = new steelseries.RadialBargraph('canvasRadial7', {
                            gaugeType: steelseries.GaugeType.TYPE3,
                            size: 201,
                            section: sections,
                            useSectionColors: true,
                            titleString: 'Title',
                            unitString: 'Type 7',
                            lcdVisible: true
                        });

        radial8 = new steelseries.RadialBargraph('canvasRadial8', {
                            gaugeType: steelseries.GaugeType.TYPE4,
                            size: 201,
                            valueGradient: valGrad,
                            useValueGradient: true,
                            titleString: 'Title',
                            unitString: 'Type 8',
                            lcdVisible: true
                        });



        // Start the random update
        //radial9.setOdoValue(odoValue);
        setInterval(function(){ setRandomValue(radial1, 100); }, 4200);
        setInterval(function(){ setRandomValue(radial2, 100); }, 4300);
        setInterval(function(){ setRandomValue(radial3, 100); }, 4400);
        setInterval(function(){ setRandomValue(radial4, 100); }, 4500);
        setInterval(function(){ setRandomValue(radial5, 100); }, 4600);
        setInterval(function(){ setRandomValue(radial6, 100); }, 4700);
        setInterval(function(){ setRandomValue(radial7, 100); }, 4800);
        setInterval(function(){ setRandomValue(radial8, 100); }, 4900);
    }

    function setRandomValue(gauge, range) {
        gauge.setValueAnimated(Math.random() * range);
    }

    function setDirRandomValue(gauge, range) {
        gauge.setValueAnimatedLatest(Math.random() * range);
        gauge.setValueAnimatedAverage(Math.random() * range);
    }

    function setHorRandomValue(gauge) {
        var pitch = Math.random() * 100 - 50,
            roll = Math.random() * 120 - 60;
        gauge.setPitchAnimated(pitch);
        gauge.setRollAnimated(roll);
    }

    function setOdometer(gauge) {
        odoValue += 0.02
        gauge.setOdoValue(odoValue);
    }

    function setFrameDesign(sel) {
        switch (sel.options[sel.selectedIndex].value) {
        case "BLACK_METAL":
            radial1.setFrameDesign(steelseries.FrameDesign.BLACK_METAL);
            radial2.setFrameDesign(steelseries.FrameDesign.BLACK_METAL);
            radial3.setFrameDesign(steelseries.FrameDesign.BLACK_METAL);
            radial4.setFrameDesign(steelseries.FrameDesign.BLACK_METAL);
            radial5.setFrameDesign(steelseries.FrameDesign.BLACK_METAL);
            radial6.setFrameDesign(steelseries.FrameDesign.BLACK_METAL);
            radial7.setFrameDesign(steelseries.FrameDesign.BLACK_METAL);
            radial8.setFrameDesign(steelseries.FrameDesign.BLACK_METAL);

            break;
        case "METAL":
            radial1.setFrameDesign(steelseries.FrameDesign.METAL);
            radial2.setFrameDesign(steelseries.FrameDesign.METAL);
            radial3.setFrameDesign(steelseries.FrameDesign.METAL);
            radial4.setFrameDesign(steelseries.FrameDesign.METAL);
            radial5.setFrameDesign(steelseries.FrameDesign.METAL);
            radial6.setFrameDesign(steelseries.FrameDesign.METAL);
            radial7.setFrameDesign(steelseries.FrameDesign.METAL);
            radial8.setFrameDesign(steelseries.FrameDesign.METAL);
            //radial9.setFrameDesign(steelseries.FrameDesign.METAL);
            break;
        case "SHINY_METAL":
            radial1.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            radial2.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            radial3.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            radial4.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            radial5.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            radial6.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            radial7.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            radial8.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            //radial9.setFrameDesign(steelseries.FrameDesign.SHINY_METAL);
            break;
        case "BRASS":
            radial1.setFrameDesign(steelseries.FrameDesign.BRASS);
            radial2.setFrameDesign(steelseries.FrameDesign.BRASS);
            radial3.setFrameDesign(steelseries.FrameDesign.BRASS);
            radial4.setFrameDesign(steelseries.FrameDesign.BRASS);
            radial5.setFrameDesign(steelseries.FrameDesign.BRASS);
            radial6.setFrameDesign(steelseries.FrameDesign.BRASS);
            radial7.setFrameDesign(steelseries.FrameDesign.BRASS);
            radial8.setFrameDesign(steelseries.FrameDesign.BRASS);
            //radial9.setFrameDesign(steelseries.FrameDesign.BRASS);
            break;
        case "STEEL":
            radial1.setFrameDesign(steelseries.FrameDesign.STEEL);
            radial2.setFrameDesign(steelseries.FrameDesign.STEEL);
            radial3.setFrameDesign(steelseries.FrameDesign.STEEL);
            radial4.setFrameDesign(steelseries.FrameDesign.STEEL);
            radial5.setFrameDesign(steelseries.FrameDesign.STEEL);
            radial6.setFrameDesign(steelseries.FrameDesign.STEEL);
            radial7.setFrameDesign(steelseries.FrameDesign.STEEL);
            radial8.setFrameDesign(steelseries.FrameDesign.STEEL);
            //radial9.setFrameDesign(steelseries.FrameDesign.STEEL);
            break;
        case "CHROME":
            radial1.setFrameDesign(steelseries.FrameDesign.CHROME);
            radial2.setFrameDesign(steelseries.FrameDesign.CHROME);
            radial3.setFrameDesign(steelseries.FrameDesign.CHROME);
            radial4.setFrameDesign(steelseries.FrameDesign.CHROME);
            radial5.setFrameDesign(steelseries.FrameDesign.CHROME);
            radial6.setFrameDesign(steelseries.FrameDesign.CHROME);
            radial7.setFrameDesign(steelseries.FrameDesign.CHROME);
            radial8.setFrameDesign(steelseries.FrameDesign.CHROME);
            //radial9.setFrameDesign(steelseries.FrameDesign.CHROME);
            break;
        case "GOLD":
            radial1.setFrameDesign(steelseries.FrameDesign.GOLD);
            radial2.setFrameDesign(steelseries.FrameDesign.GOLD);
            radial3.setFrameDesign(steelseries.FrameDesign.GOLD);
            radial4.setFrameDesign(steelseries.FrameDesign.GOLD);
            radial5.setFrameDesign(steelseries.FrameDesign.GOLD);
            radial6.setFrameDesign(steelseries.FrameDesign.GOLD);
            radial7.setFrameDesign(steelseries.FrameDesign.GOLD);
            radial8.setFrameDesign(steelseries.FrameDesign.GOLD);
            //radial9.setFrameDesign(steelseries.FrameDesign.GOLD);
            break;
        case "ANTHRACITE":
            radial1.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            radial2.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            radial3.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            radial4.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            radial5.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            radial6.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            radial7.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            radial8.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            //radial9.setFrameDesign(steelseries.FrameDesign.ANTHRACITE);
            break;
        case "TILTED_GRAY":
            radial1.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            radial2.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            radial3.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            radial4.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            radial5.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            radial6.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            radial7.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            radial8.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            //radial9.setFrameDesign(steelseries.FrameDesign.TILTED_GRAY);
            break;
        case "TILTED_BLACK":
            radial1.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            radial2.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            radial3.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            radial4.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            radial5.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            radial6.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            radial7.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            radial8.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            //radial9.setFrameDesign(steelseries.FrameDesign.TILTED_BLACK);
            break;
        case "GLOSSY_METAL":
            radial1.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            radial2.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            radial3.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            radial4.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            radial5.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            radial6.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            radial7.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            radial8.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            //radial9.setFrameDesign(steelseries.FrameDesign.GLOSSY_METAL);
            break;
        }
    }

    function setBackgroundColor(sel) {
        switch (sel.options[sel.selectedIndex].value) {
        case "DARK_GRAY":
            radial1.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            radial2.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            radial3.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            radial4.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            radial5.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            radial6.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            radial7.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            radial8.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.DARK_GRAY);
            break;
        case "SATIN_GRAY":
            radial1.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            radial2.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            radial3.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            radial4.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            radial5.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            radial6.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            radial7.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            radial8.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.SATIN_GRAY);
            break;
        case "LIGHT_GRAY":
            radial1.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            radial2.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            radial3.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            radial4.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            radial5.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            radial6.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            radial7.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            radial8.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.LIGHT_GRAY);
            break;
        case "WHITE":
            radial1.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            radial2.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            radial3.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            radial4.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            radial5.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            radial6.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            radial7.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            radial8.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.WHITE);
            break;
        case "BLACK":
            radial1.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            radial2.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            radial3.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            radial4.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            radial5.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            radial6.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            radial7.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            radial8.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.BLACK);
            break;
        case "BEIGE":
            radial1.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            radial2.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            radial3.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            radial4.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            radial5.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            radial6.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            radial7.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            radial8.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.BEIGE);
            break;
        case "BROWN":
            radial1.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            radial2.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            radial3.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            radial4.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            radial5.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            radial6.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            radial7.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            radial8.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.BROWN);
            break;
        case "RED":
            radial1.setBackgroundColor(steelseries.BackgroundColor.RED);
            radial2.setBackgroundColor(steelseries.BackgroundColor.RED);
            radial3.setBackgroundColor(steelseries.BackgroundColor.RED);
            radial4.setBackgroundColor(steelseries.BackgroundColor.RED);
            radial5.setBackgroundColor(steelseries.BackgroundColor.RED);
            radial6.setBackgroundColor(steelseries.BackgroundColor.RED);
            radial7.setBackgroundColor(steelseries.BackgroundColor.RED);
            radial8.setBackgroundColor(steelseries.BackgroundColor.RED);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.RED);
            break;
        case "GREEN":
            radial1.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            radial2.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            radial3.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            radial4.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            radial5.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            radial6.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            radial7.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            radial8.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.GREEN);
            break;
        case "BLUE":
            radial1.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            radial2.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            radial3.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            radial4.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            radial5.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            radial6.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            radial7.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            radial8.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.BLUE);
            break;
        case "ANTHRACITE":
            radial1.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            radial2.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            radial3.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            radial4.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            radial5.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            radial6.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            radial7.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            radial8.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.ANTHRACITE);
            break;
        case "MUD":
            radial1.setBackgroundColor(steelseries.BackgroundColor.MUD);
            radial2.setBackgroundColor(steelseries.BackgroundColor.MUD);
            radial3.setBackgroundColor(steelseries.BackgroundColor.MUD);
            radial4.setBackgroundColor(steelseries.BackgroundColor.MUD);
            radial5.setBackgroundColor(steelseries.BackgroundColor.MUD);
            radial6.setBackgroundColor(steelseries.BackgroundColor.MUD);
            radial7.setBackgroundColor(steelseries.BackgroundColor.MUD);
            radial8.setBackgroundColor(steelseries.BackgroundColor.MUD);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.MUD);
            break;
        case "PUNCHED_SHEET":
            radial1.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            radial2.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            radial3.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            radial4.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            radial5.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            radial6.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            radial7.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            radial8.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.PUNCHED_SHEET);
            break;
        case "CARBON":
            radial1.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            radial2.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            radial3.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            radial4.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            radial5.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            radial6.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            radial7.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            radial8.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.CARBON);
            break;
        case "STAINLESS":
            radial1.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            radial2.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            radial3.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            radial4.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            radial5.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            radial6.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            radial7.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            radial8.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.STAINLESS);
            break;
        case "BRUSHED_METAL":
            radial1.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            radial2.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            radial3.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            radial4.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            radial5.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            radial6.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            radial7.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            radial8.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_METAL);
            break;
        case "BRUSHED_STAINLESS":
            radial1.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            radial2.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            radial3.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            radial4.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            radial5.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            radial6.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            radial7.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            radial8.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.BRUSHED_STAINLESS);
            break;
        case "TURNED":
            radial1.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            radial2.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            radial3.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            radial4.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            radial5.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            radial6.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            radial7.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            radial8.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            //radial9.setBackgroundColor(steelseries.BackgroundColor.TURNED);
            break;
        }
    }

    function setForegroundType(sel) {
        switch (sel.options[sel.selectedIndex].value) {
        case "TYPE1":
            radial1.setForegroundType(steelseries.ForegroundType.TYPE1);
            radial2.setForegroundType(steelseries.ForegroundType.TYPE1);
            radial3.setForegroundType(steelseries.ForegroundType.TYPE1);
            radial4.setForegroundType(steelseries.ForegroundType.TYPE1);
            radial5.setForegroundType(steelseries.ForegroundType.TYPE1);
            radial6.setForegroundType(steelseries.ForegroundType.TYPE1);
            radial7.setForegroundType(steelseries.ForegroundType.TYPE1);
            radial8.setForegroundType(steelseries.ForegroundType.TYPE1);
            //radial9.setForegroundType(steelseries.ForegroundType.TYPE1);

            break;
        case "TYPE2":
            radial1.setForegroundType(steelseries.ForegroundType.TYPE2);
            radial2.setForegroundType(steelseries.ForegroundType.TYPE2);
            radial3.setForegroundType(steelseries.ForegroundType.TYPE2);
            radial4.setForegroundType(steelseries.ForegroundType.TYPE2);
            radial5.setForegroundType(steelseries.ForegroundType.TYPE2);
            radial6.setForegroundType(steelseries.ForegroundType.TYPE2);
            radial7.setForegroundType(steelseries.ForegroundType.TYPE2);
            radial8.setForegroundType(steelseries.ForegroundType.TYPE2);
            //radial9.setForegroundType(steelseries.ForegroundType.TYPE2);

            break;
        case "TYPE3":
            radial1.setForegroundType(steelseries.ForegroundType.TYPE3);
            radial2.setForegroundType(steelseries.ForegroundType.TYPE3);
            radial3.setForegroundType(steelseries.ForegroundType.TYPE3);
            radial4.setForegroundType(steelseries.ForegroundType.TYPE3);
            radial5.setForegroundType(steelseries.ForegroundType.TYPE3);
            radial6.setForegroundType(steelseries.ForegroundType.TYPE3);
            radial7.setForegroundType(steelseries.ForegroundType.TYPE3);
            radial8.setForegroundType(steelseries.ForegroundType.TYPE3);
            //radial9.setForegroundType(steelseries.ForegroundType.TYPE3);

            break;
        case "TYPE4":
            radial1.setForegroundType(steelseries.ForegroundType.TYPE4);
            radial2.setForegroundType(steelseries.ForegroundType.TYPE4);
            radial3.setForegroundType(steelseries.ForegroundType.TYPE4);
            radial4.setForegroundType(steelseries.ForegroundType.TYPE4);
            radial5.setForegroundType(steelseries.ForegroundType.TYPE4);
            radial6.setForegroundType(steelseries.ForegroundType.TYPE4);
            radial7.setForegroundType(steelseries.ForegroundType.TYPE4);
            radial8.setForegroundType(steelseries.ForegroundType.TYPE4);
            //radial9.setForegroundType(steelseries.ForegroundType.TYPE4);

            break;
        case "TYPE5":
            radial1.setForegroundType(steelseries.ForegroundType.TYPE5);
            radial2.setForegroundType(steelseries.ForegroundType.TYPE5);
            radial3.setForegroundType(steelseries.ForegroundType.TYPE5);
            radial4.setForegroundType(steelseries.ForegroundType.TYPE5);
            radial5.setForegroundType(steelseries.ForegroundType.TYPE5);
            radial6.setForegroundType(steelseries.ForegroundType.TYPE5);
            radial7.setForegroundType(steelseries.ForegroundType.TYPE5);
            radial8.setForegroundType(steelseries.ForegroundType.TYPE5);
            //radial9.setForegroundType(steelseries.ForegroundType.TYPE5);

            break;
        }
    }

    function setPointerColor(sel) {
        switch (sel.options[sel.selectedIndex].value) {
        case "RED":
            radial1.setPointerColor(steelseries.ColorDef.RED);
            radial2.setPointerColor(steelseries.ColorDef.RED);
            radial3.setPointerColor(steelseries.ColorDef.RED);
            radial4.setPointerColor(steelseries.ColorDef.RED);
            radial5.setValueColor(steelseries.ColorDef.RED);
            radial6.setValueColor(steelseries.ColorDef.RED);
            radial7.setValueColor(steelseries.ColorDef.RED);
            radial8.setValueColor(steelseries.ColorDef.RED);
            //radial9.setPointerColor(steelseries.ColorDef.RED);

            break;
        case "GREEN":
            radial1.setPointerColor(steelseries.ColorDef.GREEN);
            radial2.setPointerColor(steelseries.ColorDef.GREEN);
            radial3.setPointerColor(steelseries.ColorDef.GREEN);
            radial4.setPointerColor(steelseries.ColorDef.GREEN);
            radial5.setValueColor(steelseries.ColorDef.GREEN);
            radial6.setValueColor(steelseries.ColorDef.GREEN);
            radial7.setValueColor(steelseries.ColorDef.GREEN);
            radial8.setValueColor(steelseries.ColorDef.GREEN);
            //radial9.setPointerColor(steelseries.ColorDef.GREEN);

            break;
        case "BLUE":
            radial1.setPointerColor(steelseries.ColorDef.BLUE);
            radial2.setPointerColor(steelseries.ColorDef.BLUE);
            radial3.setPointerColor(steelseries.ColorDef.BLUE);
            radial4.setPointerColor(steelseries.ColorDef.BLUE);
            radial5.setValueColor(steelseries.ColorDef.BLUE);
            radial6.setValueColor(steelseries.ColorDef.BLUE);
            radial7.setValueColor(steelseries.ColorDef.BLUE);
            radial8.setValueColor(steelseries.ColorDef.BLUE);
            //radial9.setPointerColor(steelseries.ColorDef.BLUE);

            break;
        case "ORANGE":
            radial1.setPointerColor(steelseries.ColorDef.ORANGE);
            radial2.setPointerColor(steelseries.ColorDef.ORANGE);
            radial3.setPointerColor(steelseries.ColorDef.ORANGE);
            radial4.setPointerColor(steelseries.ColorDef.ORANGE);
            radial5.setValueColor(steelseries.ColorDef.ORANGE);
            radial6.setValueColor(steelseries.ColorDef.ORANGE);
            radial7.setValueColor(steelseries.ColorDef.ORANGE);
            radial8.setValueColor(steelseries.ColorDef.ORANGE);
            //radial9.setPointerColor(steelseries.ColorDef.ORANGE);

            break;
        case "YELLOW":
            radial1.setPointerColor(steelseries.ColorDef.YELLOW);
            radial2.setPointerColor(steelseries.ColorDef.YELLOW);
            radial3.setPointerColor(steelseries.ColorDef.YELLOW);
            radial4.setPointerColor(steelseries.ColorDef.YELLOW);
            radial5.setValueColor(steelseries.ColorDef.YELLOW);
            radial6.setValueColor(steelseries.ColorDef.YELLOW);
            radial7.setValueColor(steelseries.ColorDef.YELLOW);
            radial8.setValueColor(steelseries.ColorDef.YELLOW);
            //radial9.setPointerColor(steelseries.ColorDef.YELLOW);

            break;
        case "CYAN":
            radial1.setPointerColor(steelseries.ColorDef.CYAN);
            radial2.setPointerColor(steelseries.ColorDef.CYAN);
            radial3.setPointerColor(steelseries.ColorDef.CYAN);
            radial4.setPointerColor(steelseries.ColorDef.CYAN);
            radial5.setValueColor(steelseries.ColorDef.CYAN);
            radial6.setValueColor(steelseries.ColorDef.CYAN);
            radial7.setValueColor(steelseries.ColorDef.CYAN);
            radial8.setValueColor(steelseries.ColorDef.CYAN);
            //radial9.setPointerColor(steelseries.ColorDef.CYAN);

            break;
        case "MAGENTA":
            radial1.setPointerColor(steelseries.ColorDef.MAGENTA);
            radial2.setPointerColor(steelseries.ColorDef.MAGENTA);
            radial3.setPointerColor(steelseries.ColorDef.MAGENTA);
            radial4.setPointerColor(steelseries.ColorDef.MAGENTA);
            radial5.setValueColor(steelseries.ColorDef.MAGENTA);
            radial6.setValueColor(steelseries.ColorDef.MAGENTA);
            radial7.setValueColor(steelseries.ColorDef.MAGENTA);
            radial8.setValueColor(steelseries.ColorDef.MAGENTA);
            //radial9.setPointerColor(steelseries.ColorDef.MAGENTA);

            break;
        case "WHITE":
            radial1.setPointerColor(steelseries.ColorDef.WHITE);
            radial2.setPointerColor(steelseries.ColorDef.WHITE);
            radial3.setPointerColor(steelseries.ColorDef.WHITE);
            radial4.setPointerColor(steelseries.ColorDef.WHITE);
            radial5.setValueColor(steelseries.ColorDef.WHITE);
            radial6.setValueColor(steelseries.ColorDef.WHITE);
            radial7.setValueColor(steelseries.ColorDef.WHITE);
            radial8.setValueColor(steelseries.ColorDef.WHITE);
            //radial9.setPointerColor(steelseries.ColorDef.WHITE);

            break;
        case "GRAY":
            radial1.setPointerColor(steelseries.ColorDef.GRAY);
            radial2.setPointerColor(steelseries.ColorDef.GRAY);
            radial3.setPointerColor(steelseries.ColorDef.GRAY);
            radial4.setPointerColor(steelseries.ColorDef.GRAY);
            radial5.setValueColor(steelseries.ColorDef.GRAY);
            radial6.setValueColor(steelseries.ColorDef.GRAY);
            radial7.setValueColor(steelseries.ColorDef.GRAY);
            radial8.setValueColor(steelseries.ColorDef.GRAY);
            //radial9.setPointerColor(steelseries.ColorDef.GRAY);

            break;
        case "BLACK":
            radial1.setPointerColor(steelseries.ColorDef.BLACK);
            radial2.setPointerColor(steelseries.ColorDef.BLACK);
            radial3.setPointerColor(steelseries.ColorDef.BLACK);
            radial4.setPointerColor(steelseries.ColorDef.BLACK);
            radial5.setValueColor(steelseries.ColorDef.BLACK);
            radial6.setValueColor(steelseries.ColorDef.BLACK);
            radial7.setValueColor(steelseries.ColorDef.BLACK);
            radial8.setValueColor(steelseries.ColorDef.BLACK);
            //radial9.setPointerColor(steelseries.ColorDef.BLACK);

            break;
        case "RAITH":
            radial1.setPointerColor(steelseries.ColorDef.RAITH);
            radial2.setPointerColor(steelseries.ColorDef.RAITH);
            radial3.setPointerColor(steelseries.ColorDef.RAITH);
            radial4.setPointerColor(steelseries.ColorDef.RAITH);
            radial5.setValueColor(steelseries.ColorDef.RAITH);
            radial6.setValueColor(steelseries.ColorDef.RAITH);
            radial7.setValueColor(steelseries.ColorDef.RAITH);
            radial8.setValueColor(steelseries.ColorDef.RAITH);
            //radial9.setPointerColor(steelseries.ColorDef.RAITH);

            break;
        case "GREEN_LCD":
            radial1.setPointerColor(steelseries.ColorDef.GREEN_LCD);
            radial2.setPointerColor(steelseries.ColorDef.GREEN_LCD);
            radial3.setPointerColor(steelseries.ColorDef.GREEN_LCD);
            radial4.setPointerColor(steelseries.ColorDef.GREEN_LCD);
            radial5.setValueColor(steelseries.ColorDef.GREEN_LCD);
            radial6.setValueColor(steelseries.ColorDef.GREEN_LCD);
            radial7.setValueColor(steelseries.ColorDef.GREEN_LCD);
            radial8.setValueColor(steelseries.ColorDef.GREEN_LCD);
            //radial9.setPointerColor(steelseries.ColorDef.GREEN_LCD);

            break;
        case "JUG_GREEN":
            radial1.setPointerColor(steelseries.ColorDef.JUG_GREEN);
            radial2.setPointerColor(steelseries.ColorDef.JUG_GREEN);
            radial3.setPointerColor(steelseries.ColorDef.JUG_GREEN);
            radial4.setPointerColor(steelseries.ColorDef.JUG_GREEN);
            radial5.setValueColor(steelseries.ColorDef.JUG_GREEN);
            radial6.setValueColor(steelseries.ColorDef.JUG_GREEN);
            radial7.setValueColor(steelseries.ColorDef.JUG_GREEN);
            radial8.setValueColor(steelseries.ColorDef.JUG_GREEN);
            //radial9.setPointerColor(steelseries.ColorDef.JUG_GREEN);

            break;
        }
    }

    function setPointerType(sel) {
        switch (sel.options[sel.selectedIndex].value) {
        case "TYPE1":
            radial1.setPointerType(steelseries.PointerType.TYPE1);
            radial2.setPointerType(steelseries.PointerType.TYPE1);
            radial3.setPointerType(steelseries.PointerType.TYPE1);
            radial4.setPointerType(steelseries.PointerType.TYPE1);
            //radial9.setPointerType(steelseries.PointerType.TYPE1);

            break;
        case "TYPE2":
            radial1.setPointerType(steelseries.PointerType.TYPE2);
            radial2.setPointerType(steelseries.PointerType.TYPE2);
            radial3.setPointerType(steelseries.PointerType.TYPE2);
            radial4.setPointerType(steelseries.PointerType.TYPE2);
            //radial9.setPointerType(steelseries.PointerType.TYPE2);

            break;
        case "TYPE3":
            radial1.setPointerType(steelseries.PointerType.TYPE3);
            radial2.setPointerType(steelseries.PointerType.TYPE3);
            radial3.setPointerType(steelseries.PointerType.TYPE3);
            radial4.setPointerType(steelseries.PointerType.TYPE3);
            //radial9.setPointerType(steelseries.PointerType.TYPE3);

            break;
        case "TYPE4":
            radial1.setPointerType(steelseries.PointerType.TYPE4);
            radial2.setPointerType(steelseries.PointerType.TYPE4);
            radial3.setPointerType(steelseries.PointerType.TYPE4);
            radial4.setPointerType(steelseries.PointerType.TYPE4);
            //radial9.setPointerType(steelseries.PointerType.TYPE4);

            break;
        case "TYPE5":
            radial1.setPointerType(steelseries.PointerType.TYPE5);
            radial2.setPointerType(steelseries.PointerType.TYPE5);
            radial3.setPointerType(steelseries.PointerType.TYPE5);
            radial4.setPointerType(steelseries.PointerType.TYPE5);
            //radial9.setPointerType(steelseries.PointerType.TYPE5);

            break;
        case "TYPE6":
            radial1.setPointerType(steelseries.PointerType.TYPE6);
            radial2.setPointerType(steelseries.PointerType.TYPE6);
            radial3.setPointerType(steelseries.PointerType.TYPE6);
            radial4.setPointerType(steelseries.PointerType.TYPE6);
            //radial9.setPointerType(steelseries.PointerType.TYPE6);

            break;
        case "TYPE7":
            radial1.setPointerType(steelseries.PointerType.TYPE7);
            radial2.setPointerType(steelseries.PointerType.TYPE7);
            radial3.setPointerType(steelseries.PointerType.TYPE7);
            radial4.setPointerType(steelseries.PointerType.TYPE7);
            //radial9.setPointerType(steelseries.PointerType.TYPE7);

            break;
        case "TYPE8":
            radial1.setPointerType(steelseries.PointerType.TYPE8);
            radial2.setPointerType(steelseries.PointerType.TYPE8);
            radial3.setPointerType(steelseries.PointerType.TYPE8);
            radial4.setPointerType(steelseries.PointerType.TYPE8);
            //radial9.setPointerType(steelseries.PointerType.TYPE8);

            break;
        case "TYPE9":
            radial1.setPointerType(steelseries.PointerType.TYPE9);
            radial2.setPointerType(steelseries.PointerType.TYPE9);
            radial3.setPointerType(steelseries.PointerType.TYPE9);
            radial4.setPointerType(steelseries.PointerType.TYPE9);
            //radial9.setPointerType(steelseries.PointerType.TYPE9);

            break;
        case "TYPE10":
            radial1.setPointerType(steelseries.PointerType.TYPE10);
            radial2.setPointerType(steelseries.PointerType.TYPE10);
            radial3.setPointerType(steelseries.PointerType.TYPE10);
            radial4.setPointerType(steelseries.PointerType.TYPE10);
            //radial9.setPointerType(steelseries.PointerType.TYPE10);

            break;
        case "TYPE11":
            radial1.setPointerType(steelseries.PointerType.TYPE11);
            radial2.setPointerType(steelseries.PointerType.TYPE11);
            radial3.setPointerType(steelseries.PointerType.TYPE11);
            radial4.setPointerType(steelseries.PointerType.TYPE11);
            //radial9.setPointerType(steelseries.PointerType.TYPE11);

            break;
        case "TYPE12":
            radial1.setPointerType(steelseries.PointerType.TYPE12);
            radial2.setPointerType(steelseries.PointerType.TYPE12);
            radial3.setPointerType(steelseries.PointerType.TYPE12);
            radial4.setPointerType(steelseries.PointerType.TYPE12);
            //radial9.setPointerType(steelseries.PointerType.TYPE12);

            break;
        case "TYPE13":
            radial1.setPointerType(steelseries.PointerType.TYPE13);
            radial2.setPointerType(steelseries.PointerType.TYPE13);
            radial3.setPointerType(steelseries.PointerType.TYPE13);
            radial4.setPointerType(steelseries.PointerType.TYPE13);
            //radial9.setPointerType(steelseries.PointerType.TYPE13);

            break;
        case "TYPE14":
            radial1.setPointerType(steelseries.PointerType.TYPE14);
            radial2.setPointerType(steelseries.PointerType.TYPE14);
            radial3.setPointerType(steelseries.PointerType.TYPE14);
            radial4.setPointerType(steelseries.PointerType.TYPE14);
            //radial9.setPointerType(steelseries.PointerType.TYPE14);

            break;
        case "TYPE15":
            radial1.setPointerType(steelseries.PointerType.TYPE15);
            radial2.setPointerType(steelseries.PointerType.TYPE15);
            radial3.setPointerType(steelseries.PointerType.TYPE15);
            radial4.setPointerType(steelseries.PointerType.TYPE15);
            //radial9.setPointerType(steelseries.PointerType.TYPE15);

            break;
        case "TYPE16":
            radial1.setPointerType(steelseries.PointerType.TYPE16);
            radial2.setPointerType(steelseries.PointerType.TYPE16);
            radial3.setPointerType(steelseries.PointerType.TYPE16);
            radial4.setPointerType(steelseries.PointerType.TYPE16);
            //radial9.setPointerType(steelseries.PointerType.TYPE16);

            break;
        }
    }

    function setLcdColor(sel) {
        switch (sel.options[sel.selectedIndex].value) {
        case "BEIGE":
            radial1.setLcdColor(steelseries.LcdColor.BEIGE);
            radial2.setLcdColor(steelseries.LcdColor.BEIGE);
            radial3.setLcdColor(steelseries.LcdColor.BEIGE);
            radial4.setLcdColor(steelseries.LcdColor.BEIGE);
            radial5.setLcdColor(steelseries.LcdColor.BEIGE);
            radial6.setLcdColor(steelseries.LcdColor.BEIGE);
            radial7.setLcdColor(steelseries.LcdColor.BEIGE);
            radial8.setLcdColor(steelseries.LcdColor.BEIGE);
            break;
        case "BLUE":
            radial1.setLcdColor(steelseries.LcdColor.BLUE);
            radial2.setLcdColor(steelseries.LcdColor.BLUE);
            radial3.setLcdColor(steelseries.LcdColor.BLUE);
            radial4.setLcdColor(steelseries.LcdColor.BLUE);
            radial5.setLcdColor(steelseries.LcdColor.BLUE);
            radial6.setLcdColor(steelseries.LcdColor.BLUE);
            radial7.setLcdColor(steelseries.LcdColor.BLUE);
            radial8.setLcdColor(steelseries.LcdColor.BLUE);
            break;
        case "ORANGE":
            radial1.setLcdColor(steelseries.LcdColor.ORANGE);
            radial2.setLcdColor(steelseries.LcdColor.ORANGE);
            radial3.setLcdColor(steelseries.LcdColor.ORANGE);
            radial4.setLcdColor(steelseries.LcdColor.ORANGE);
            radial5.setLcdColor(steelseries.LcdColor.ORANGE);
            radial6.setLcdColor(steelseries.LcdColor.ORANGE);
            radial7.setLcdColor(steelseries.LcdColor.ORANGE);
            radial8.setLcdColor(steelseries.LcdColor.ORANGE);
            break;
        case "RED":
            radial1.setLcdColor(steelseries.LcdColor.RED);
            radial2.setLcdColor(steelseries.LcdColor.RED);
            radial3.setLcdColor(steelseries.LcdColor.RED);
            radial4.setLcdColor(steelseries.LcdColor.RED);
            radial5.setLcdColor(steelseries.LcdColor.RED);
            radial6.setLcdColor(steelseries.LcdColor.RED);
            radial7.setLcdColor(steelseries.LcdColor.RED);
            radial8.setLcdColor(steelseries.LcdColor.RED);
            break;
        case "YELLOW":
            radial1.setLcdColor(steelseries.LcdColor.YELLOW);
            radial2.setLcdColor(steelseries.LcdColor.YELLOW);
            radial3.setLcdColor(steelseries.LcdColor.YELLOW);
            radial4.setLcdColor(steelseries.LcdColor.YELLOW);
            radial5.setLcdColor(steelseries.LcdColor.YELLOW);
            radial6.setLcdColor(steelseries.LcdColor.YELLOW);
            radial7.setLcdColor(steelseries.LcdColor.YELLOW);
            radial8.setLcdColor(steelseries.LcdColor.YELLOW);
            break;
        case "WHITE":
            radial1.setLcdColor(steelseries.LcdColor.WHITE);
            radial2.setLcdColor(steelseries.LcdColor.WHITE);
            radial3.setLcdColor(steelseries.LcdColor.WHITE);
            radial4.setLcdColor(steelseries.LcdColor.WHITE);
            radial5.setLcdColor(steelseries.LcdColor.WHITE);
            radial6.setLcdColor(steelseries.LcdColor.WHITE);
            radial7.setLcdColor(steelseries.LcdColor.WHITE);
            radial8.setLcdColor(steelseries.LcdColor.WHITE);
            break;
        case "GRAY":
            radial1.setLcdColor(steelseries.LcdColor.GRAY);
            radial2.setLcdColor(steelseries.LcdColor.GRAY);
            radial3.setLcdColor(steelseries.LcdColor.GRAY);
            radial4.setLcdColor(steelseries.LcdColor.GRAY);
            radial5.setLcdColor(steelseries.LcdColor.GRAY);
            radial6.setLcdColor(steelseries.LcdColor.GRAY);
            radial7.setLcdColor(steelseries.LcdColor.GRAY);
            radial8.setLcdColor(steelseries.LcdColor.GRAY);
            break;
        case "BLACK":
            radial1.setLcdColor(steelseries.LcdColor.BLACK);
            radial2.setLcdColor(steelseries.LcdColor.BLACK);
            radial3.setLcdColor(steelseries.LcdColor.BLACK);
            radial4.setLcdColor(steelseries.LcdColor.BLACK);
            radial5.setLcdColor(steelseries.LcdColor.BLACK);
            radial6.setLcdColor(steelseries.LcdColor.BLACK);
            radial7.setLcdColor(steelseries.LcdColor.BLACK);
            radial8.setLcdColor(steelseries.LcdColor.BLACK);
            break;
        case "GREEN":
            radial1.setLcdColor(steelseries.LcdColor.GREEN);
            radial2.setLcdColor(steelseries.LcdColor.GREEN);
            radial3.setLcdColor(steelseries.LcdColor.GREEN);
            radial4.setLcdColor(steelseries.LcdColor.GREEN);
            radial5.setLcdColor(steelseries.LcdColor.GREEN);
            radial6.setLcdColor(steelseries.LcdColor.GREEN);
            radial7.setLcdColor(steelseries.LcdColor.GREEN);
            radial8.setLcdColor(steelseries.LcdColor.GREEN);
            break;
        case "BLUE2":
            radial1.setLcdColor(steelseries.LcdColor.BLUE2);
            radial2.setLcdColor(steelseries.LcdColor.BLUE2);
            radial3.setLcdColor(steelseries.LcdColor.BLUE2);
            radial4.setLcdColor(steelseries.LcdColor.BLUE2);
            radial5.setLcdColor(steelseries.LcdColor.BLUE2);
            radial6.setLcdColor(steelseries.LcdColor.BLUE2);
            radial7.setLcdColor(steelseries.LcdColor.BLUE2);
            radial8.setLcdColor(steelseries.LcdColor.BLUE2);
            break;
        case "BLUE_BLACK":
            radial1.setLcdColor(steelseries.LcdColor.BLUE_BLACK);
            radial2.setLcdColor(steelseries.LcdColor.BLUE_BLACK);
            radial3.setLcdColor(steelseries.LcdColor.BLUE_BLACK);
            radial4.setLcdColor(steelseries.LcdColor.BLUE_BLACK);
            radial5.setLcdColor(steelseries.LcdColor.BLUE_BLACK);
            radial6.setLcdColor(steelseries.LcdColor.BLUE_BLACK);
            radial7.setLcdColor(steelseries.LcdColor.BLUE_BLACK);
            radial8.setLcdColor(steelseries.LcdColor.BLUE_BLACK);
            break;
        case "BLUE_DARKBLUE":
            radial1.setLcdColor(steelseries.LcdColor.BLUE_DARKBLUE);
            radial2.setLcdColor(steelseries.LcdColor.BLUE_DARKBLUE);
            radial3.setLcdColor(steelseries.LcdColor.BLUE_DARKBLUE);
            radial4.setLcdColor(steelseries.LcdColor.BLUE_DARKBLUE);
            radial5.setLcdColor(steelseries.LcdColor.BLUE_DARKBLUE);
            radial6.setLcdColor(steelseries.LcdColor.BLUE_DARKBLUE);
            radial7.setLcdColor(steelseries.LcdColor.BLUE_DARKBLUE);
            radial8.setLcdColor(steelseries.LcdColor.BLUE_DARKBLUE);
            break;
        case "BLUE_GRAY":
            radial1.setLcdColor(steelseries.LcdColor.BLUE_GRAY);
            radial2.setLcdColor(steelseries.LcdColor.BLUE_GRAY);
            radial3.setLcdColor(steelseries.LcdColor.BLUE_GRAY);
            radial4.setLcdColor(steelseries.LcdColor.BLUE_GRAY);
            radial5.setLcdColor(steelseries.LcdColor.BLUE_GRAY);
            radial6.setLcdColor(steelseries.LcdColor.BLUE_GRAY);
            radial7.setLcdColor(steelseries.LcdColor.BLUE_GRAY);
            radial8.setLcdColor(steelseries.LcdColor.BLUE_GRAY);
            break;
        case "STANDARD":
            radial1.setLcdColor(steelseries.LcdColor.STANDARD);
            radial2.setLcdColor(steelseries.LcdColor.STANDARD);
            radial3.setLcdColor(steelseries.LcdColor.STANDARD);
            radial4.setLcdColor(steelseries.LcdColor.STANDARD);
            radial5.setLcdColor(steelseries.LcdColor.STANDARD);
            radial6.setLcdColor(steelseries.LcdColor.STANDARD);
            radial7.setLcdColor(steelseries.LcdColor.STANDARD);
            radial8.setLcdColor(steelseries.LcdColor.STANDARD);
            break;
        case "STANDARD_GREEN":
            radial1.setLcdColor(steelseries.LcdColor.STANDARD_GREEN);
            radial2.setLcdColor(steelseries.LcdColor.STANDARD_GREEN);
            radial3.setLcdColor(steelseries.LcdColor.STANDARD_GREEN);
            radial4.setLcdColor(steelseries.LcdColor.STANDARD_GREEN);
            radial5.setLcdColor(steelseries.LcdColor.STANDARD_GREEN);
            radial6.setLcdColor(steelseries.LcdColor.STANDARD_GREEN);
            radial7.setLcdColor(steelseries.LcdColor.STANDARD_GREEN);
            radial8.setLcdColor(steelseries.LcdColor.STANDARD_GREEN);
            break;
        case "BLUE_BLUE":
            radial1.setLcdColor(steelseries.LcdColor.BLUE_BLUE);
            radial2.setLcdColor(steelseries.LcdColor.BLUE_BLUE);
            radial3.setLcdColor(steelseries.LcdColor.BLUE_BLUE);
            radial4.setLcdColor(steelseries.LcdColor.BLUE_BLUE);
            radial5.setLcdColor(steelseries.LcdColor.BLUE_BLUE);
            radial6.setLcdColor(steelseries.LcdColor.BLUE_BLUE);
            radial7.setLcdColor(steelseries.LcdColor.BLUE_BLUE);
            radial8.setLcdColor(steelseries.LcdColor.BLUE_BLUE);
            break;
        case "RED_DARKRED":
            radial1.setLcdColor(steelseries.LcdColor.RED_DARKRED);
            radial2.setLcdColor(steelseries.LcdColor.RED_DARKRED);
            radial3.setLcdColor(steelseries.LcdColor.RED_DARKRED);
            radial4.setLcdColor(steelseries.LcdColor.RED_DARKRED);
            radial5.setLcdColor(steelseries.LcdColor.RED_DARKRED);
            radial6.setLcdColor(steelseries.LcdColor.RED_DARKRED);
            radial7.setLcdColor(steelseries.LcdColor.RED_DARKRED);
            radial8.setLcdColor(steelseries.LcdColor.RED_DARKRED);
            break;
        case "DARKBLUE":
            radial1.setLcdColor(steelseries.LcdColor.DARKBLUE);
            radial2.setLcdColor(steelseries.LcdColor.DARKBLUE);
            radial3.setLcdColor(steelseries.LcdColor.DARKBLUE);
            radial4.setLcdColor(steelseries.LcdColor.DARKBLUE);
            radial5.setLcdColor(steelseries.LcdColor.DARKBLUE);
            radial6.setLcdColor(steelseries.LcdColor.DARKBLUE);
            radial7.setLcdColor(steelseries.LcdColor.DARKBLUE);
            radial8.setLcdColor(steelseries.LcdColor.DARKBLUE);
            break;
        case "LILA":
            radial1.setLcdColor(steelseries.LcdColor.LILA);
            radial2.setLcdColor(steelseries.LcdColor.LILA);
            radial3.setLcdColor(steelseries.LcdColor.LILA);
            radial4.setLcdColor(steelseries.LcdColor.LILA);
            radial5.setLcdColor(steelseries.LcdColor.LILA);
            radial6.setLcdColor(steelseries.LcdColor.LILA);
            radial7.setLcdColor(steelseries.LcdColor.LILA);
            radial8.setLcdColor(steelseries.LcdColor.LILA);
            break;
        case "BLACKRED":
            radial1.setLcdColor(steelseries.LcdColor.BLACKRED);
            radial2.setLcdColor(steelseries.LcdColor.BLACKRED);
            radial3.setLcdColor(steelseries.LcdColor.BLACKRED);
            radial4.setLcdColor(steelseries.LcdColor.BLACKRED);
            radial5.setLcdColor(steelseries.LcdColor.BLACKRED);
            radial6.setLcdColor(steelseries.LcdColor.BLACKRED);
            radial7.setLcdColor(steelseries.LcdColor.BLACKRED);
            radial8.setLcdColor(steelseries.LcdColor.BLACKRED);
            break;
        case "DARKGREEN":
            radial1.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial2.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial3.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial4.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial5.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial6.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial7.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial8.setLcdColor(steelseries.LcdColor.DARKGREEN);
            break;
        case "DARKGREEN":
            radial1.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial2.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial3.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial4.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial5.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial6.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial7.setLcdColor(steelseries.LcdColor.DARKGREEN);
            radial8.setLcdColor(steelseries.LcdColor.DARKGREEN);
            break;
        case "AMBER":
            radial1.setLcdColor(steelseries.LcdColor.AMBER);
            radial2.setLcdColor(steelseries.LcdColor.AMBER);
            radial3.setLcdColor(steelseries.LcdColor.AMBER);
            radial4.setLcdColor(steelseries.LcdColor.AMBER);
            radial5.setLcdColor(steelseries.LcdColor.AMBER);
            radial6.setLcdColor(steelseries.LcdColor.AMBER);
            radial7.setLcdColor(steelseries.LcdColor.AMBER);
            radial8.setLcdColor(steelseries.LcdColor.AMBER);
            break;
        case "LIGHTBLUE":
            radial1.setLcdColor(steelseries.LcdColor.LIGHTBLUE);
            radial2.setLcdColor(steelseries.LcdColor.LIGHTBLUE);
            radial3.setLcdColor(steelseries.LcdColor.LIGHTBLUE);
            radial4.setLcdColor(steelseries.LcdColor.LIGHTBLUE);
            radial5.setLcdColor(steelseries.LcdColor.LIGHTBLUE);
            radial6.setLcdColor(steelseries.LcdColor.LIGHTBLUE);
            radial7.setLcdColor(steelseries.LcdColor.LIGHTBLUE);
            radial8.setLcdColor(steelseries.LcdColor.LIGHTBLUE);
            break;
        }
    }

    function setLedColor(sel) {
        switch (sel.options[sel.selectedIndex].value) {
        case "RED_LED":
            radial1.setLedColor(steelseries.LedColor.RED_LED);
            radial2.setLedColor(steelseries.LedColor.RED_LED);
            radial3.setLedColor(steelseries.LedColor.RED_LED);
            radial4.setLedColor(steelseries.LedColor.RED_LED);
            radial5.setLedColor(steelseries.LedColor.RED_LED);
            radial6.setLedColor(steelseries.LedColor.RED_LED);
            radial7.setLedColor(steelseries.LedColor.RED_LED);
            radial8.setLedColor(steelseries.LedColor.RED_LED);
            //radial9.setLedColor(steelseries.LedColor.RED_LED);

            break;
        case "GREEN_LED":
            radial1.setLedColor(steelseries.LedColor.GREEN_LED);
            radial2.setLedColor(steelseries.LedColor.GREEN_LED);
            radial3.setLedColor(steelseries.LedColor.GREEN_LED);
            radial4.setLedColor(steelseries.LedColor.GREEN_LED);
            radial5.setLedColor(steelseries.LedColor.GREEN_LED);
            radial6.setLedColor(steelseries.LedColor.GREEN_LED);
            radial7.setLedColor(steelseries.LedColor.GREEN_LED);
            radial8.setLedColor(steelseries.LedColor.GREEN_LED);
            //radial9.setLedColor(steelseries.LedColor.GREEN_LED);

            break;
        case "BLUE_LED":
            radial1.setLedColor(steelseries.LedColor.BLUE_LED);
            radial2.setLedColor(steelseries.LedColor.BLUE_LED);
            radial3.setLedColor(steelseries.LedColor.BLUE_LED);
            radial4.setLedColor(steelseries.LedColor.BLUE_LED);
            radial5.setLedColor(steelseries.LedColor.BLUE_LED);
            radial6.setLedColor(steelseries.LedColor.BLUE_LED);
            radial7.setLedColor(steelseries.LedColor.BLUE_LED);
            radial8.setLedColor(steelseries.LedColor.BLUE_LED);
            //radial9.setLedColor(steelseries.LedColor.BLUE_LED);

            break;
        case "ORANGE_LED":
            radial1.setLedColor(steelseries.LedColor.ORANGE_LED);
            radial2.setLedColor(steelseries.LedColor.ORANGE_LED);
            radial3.setLedColor(steelseries.LedColor.ORANGE_LED);
            radial4.setLedColor(steelseries.LedColor.ORANGE_LED);
            radial5.setLedColor(steelseries.LedColor.ORANGE_LED);
            radial6.setLedColor(steelseries.LedColor.ORANGE_LED);
            radial7.setLedColor(steelseries.LedColor.ORANGE_LED);
            radial8.setLedColor(steelseries.LedColor.ORANGE_LED);
            //radial9.setLedColor(steelseries.LedColor.ORANGE_LED);

            break;
        case "YELLOW_LED":
            radial1.setLedColor(steelseries.LedColor.YELLOW_LED);
            radial2.setLedColor(steelseries.LedColor.YELLOW_LED);
            radial3.setLedColor(steelseries.LedColor.YELLOW_LED);
            radial4.setLedColor(steelseries.LedColor.YELLOW_LED);
            radial5.setLedColor(steelseries.LedColor.YELLOW_LED);
            radial6.setLedColor(steelseries.LedColor.YELLOW_LED);
            radial7.setLedColor(steelseries.LedColor.YELLOW_LED);
            radial8.setLedColor(steelseries.LedColor.YELLOW_LED);
            //radial9.setLedColor(steelseries.LedColor.YELLOW_LED);

            break;
        case "CYAN_LED":
            radial1.setLedColor(steelseries.LedColor.CYAN_LED);
            radial2.setLedColor(steelseries.LedColor.CYAN_LED);
            radial3.setLedColor(steelseries.LedColor.CYAN_LED);
            radial4.setLedColor(steelseries.LedColor.CYAN_LED);
            radial5.setLedColor(steelseries.LedColor.CYAN_LED);
            radial6.setLedColor(steelseries.LedColor.CYAN_LED);
            radial7.setLedColor(steelseries.LedColor.CYAN_LED);
            radial8.setLedColor(steelseries.LedColor.CYAN_LED);
            //radial9.setLedColor(steelseries.LedColor.CYAN_LED);

            break;
        case "MAGENTA_LED":
            radial1.setLedColor(steelseries.LedColor.MAGENTA_LED);
            radial2.setLedColor(steelseries.LedColor.MAGENTA_LED);
            radial3.setLedColor(steelseries.LedColor.MAGENTA_LED);
            radial4.setLedColor(steelseries.LedColor.MAGENTA_LED);
            radial5.setLedColor(steelseries.LedColor.MAGENTA_LED);
            radial6.setLedColor(steelseries.LedColor.MAGENTA_LED);
            radial7.setLedColor(steelseries.LedColor.MAGENTA_LED);
            radial8.setLedColor(steelseries.LedColor.MAGENTA_LED);
            //radial9.setLedColor(steelseries.LedColor.MAGENTA_LED);

            break;
        }
    }
