<html xmlns:http="http://www.w3.org/1999/xhtml">
<head>
    <title>A Leaflet map!</title>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css"/>
    <link rel="stylesheet" href="includes/MarkerCluster.css" />
    <script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
    <script src="includes/jquery-3.0.0.min.js"></script>
    <script src="includes/leaflet.markercluster.js"></script>

    <script  type="text/javascript" src="includes/d3/d3.min.js"> </script>
    <script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
    <script  type="text/javascript" src="chart.js"> </script>
    <!--   <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script> -->
    <style>
        html, body, #map {
            width: 100%;
            height: 100%;
        }
        #left{
            width: 50%;
            float: left;
        }
        #left_top{
            height:50%;

        }
        #left_bottom{
            height:50%;
        }
        #right{
            width: 49%;
            float: right;
        }
        #map{
            width: 50%;
            height: 50%;
            position: fixed;
            margin: 0 auto;
        }

        #button{
            position: fixed;
            left: 30%;
            rigth: 10%;
            top: 2%;
            visibility: hidden;
        }
        rect:hover{
            fill: orange;
        }
        #chartContainer{
            -webkit-box-shadow: 0px -2px 30px 0px rgba(0,0,0,0.75);
            -moz-box-shadow: 0px -2px 30px 0px rgba(0,0,0,0.75);
            box-shadow: 0px -2px 30px 0px rgba(0,0,0,0.75);
        }


/*
        .axis line {
            fill: none;
            stroke: #ccc;
            stroke-dasharray: 2px 3px;
            shape-rendering: crispEdges;
            stroke-width: 1px;
        }

        .axis text {
            font-family: 'Proxima-Nova', sans-serif;
            font-size: 13px;
            pointer-events: none;
            fill: #7e7e7e;
        }

        .y.axis text {
            text-anchor: end !important;
            font-size:14px;
            fill: #7e7e7e;
        }

        .x.axis line {
            display: none;
        }

        .x.axis tick:nth-child(even) {
            display: none;*/
        }
    </style>
</head>
<body>
    <div id="left">
        <div id="left_top">
            <div id="map-container">
                <div id="map">
                </div>
            </div>
        </div>

        <div id="button">
            <button onclick="toggleMap();">Toggle Map</button>
        </div>
        <button id="sort" onclick="sortBars()">Sort</button>
        <button id="reset" onclick="reset()">Reset</button>
        <div id="left_bottom">
            <div id="chartContainer">
            </div>
        </div>
    </div>

    <div id="right">fdfffgffg</div>


<?php include 'returnResult1.php'; ?>
<script>

    function toggleMap(){
        //document.getElementById("map").style.display= "inline" ;
        var test = document.getElementById('map').style.display.value;
        console.log(test);
        if(document.getElementById('map').style.display == 'none'){
            document.getElementById('map').style.display= 'inline' ;
        }else
            document.getElementById('map').style.display= 'none' ;
    }
    var colorRange = ['red','yellow','blue'];
    var colorScale = d3.scale.linear().domain([0,2,4]).range(colorRange);
    console.log(colorScale);
    colors = d3.scale.category20()

    // initialize the map
    var map = L.map('map').setView([23.7762, 90.07402],3);

    // load a tile layer
    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: 'Tiles by <a href="http://mapc.org">MAPC</a>, Data by <a href="http://mass.gov/mgis">MassGIS</a>',
                maxZoom: 17,
                minZoom: 0
            }).addTo(map);


$('document').ready(function(){
    createMarker(result); //Without ajax call. To make this working, included php file which is retruning data
function createMarker(response){
    console.log(response);
    /*
     This is working by giving ajax call
     * */
    /* $.ajax({
     type: "GET",
     url: "returnResult.php",
     dataType: "json",
     success: function(response) {
     createMarker(result);
     },
     error: function(response) {
     console.log("error");
     }
     }).error(function(response) { console.log("rrrr") });*/

    /*function onEachFeature(feature, latlng) {
     latlng.bindPopup(feature.properties.popupContent);
     }*/
    //var marker;
    var clusters = L.markerClusterGroup({
        // animateAddingMarkers: true,
        //disableClusteringAtZoom: true,
        showCoverageOnHover: false,
        maxClusterRadius: 50,

    });
    var points = L.geoJson(response,{
        pointToLayer: function(feature,latlng){
            //var marker =  L.marker(latlng,{icon: ratIcon, draggable: true}); // This is customized icon
              marker =   L.marker(latlng,{ draggable: true});
            var radius = getCircleRadius(feature.properties.patient_no);
            var circleStyle = {
                "radius": getRadius(feature.properties.patient_no),
                "color":getColor(feature.properties.score_report),
                "opacity":1,
                "fillColor":getColor(feature.properties.score_report)
            };
           // var circle = L.Marker(latlng,circleStyle);
           // var circle = L.circleMarker(latlng,circleStyle);
            var circle = L.circle(latlng,radius,circleStyle);
            circle.bindPopup('<b>Site Name:</b> '+feature.properties.name + '<br/><b>Patient:</b> ' + feature.properties.patient_no + '<br/><b>Score: </b>' + feature.properties.score_report);
            circle.on('mouseover',function(e){
                this.openPopup();
            });
           /* circle.on('mouseout',function(e){
                this.closePopup();
            });*/
            circle.on('click', function(e){
               // callD3BarChart(feature,circle);
                var clickedCircel = e.target.feature.properties;
                //console.log(e.target);
              // map.removeLayer(d3.select(this));
              // map.removeLayer(clickedCircel);
                //clickToCreateCircel(e,radius);
                callD3BarChartWithSorting2(feature,circle);
               // callD3Popup(feature,circle);
            });
            //circle.colorScale().range(colorRange);
            //clusters.addLayer(circle);

            //callD3Popup(feature,circle);

            return circle;
        }
    });
/*    map.on('click',function(e){
        clickedCircle = L.circle(e.latlng,10,{
            color: 'red',
            fillOpacity: 0,
            opacity: 0.5
        }).addTo(map);
    });*/
    clusters.addLayer(points);
    map.addLayer(clusters);
    map.addLayer(points);
    window.addEventListener('resize', callD3BarChartWithSorting2);//Update chart when window is resized
function clickToCreateCircel(e,radius){
    clickCircle = L.circle(e.latlng, radius, {
        color: '#f07300',
        fillOpacity: 0,
        opacity: 0.5
    }).addTo(map);
}
    function callD3BarChartWithSorting2(feature,circle){
        var temp = {};
        var dataset = [
            { key: 34, value: 34 }];

        $.ajax({
            type: "GET",
            url: "returnSiteDetails.php",
            dataType: "json",
            async: false,
            success: function(response) {
                var formatedResult = response.features;
                console.log("Formated Result: ",formatedResult)
                formatedResult.forEach(function(data){
                    var temp = {};
                    temp.key = data.properties.site_id;
                    temp.value = data.properties.score_report;
                    temp.site_name = data.properties.name;
                   // console.log('temp',data.properties.site_id);
                    dataset.push(temp);
                });
                console.log('here',dataset);
            },
            error: function(response) {
                console.log('Error',response);
            }
        });
        console.log("dataset",dataset);

        var w = (window.innerWidth/2)-10;
        var h = (window.innerHeight/2);

      /*  var formatedResult = result.features;
        formatedResult.forEach(function (data){
            var temp = {};
           temp.key = data.properties.site_id;
           temp.value = data.properties.score_report;
            dataset.push(temp);
            console.log("he",data,temp);
        });

        console.log(dataset);
*/

        var xScale = d3.scale.ordinal()
            .domain(d3.range(dataset.length))
            .rangeRoundBands([0, w], 0.05);

        var yScale = d3.scale.linear()
            .domain([0, d3.max(dataset, function(d) {return d.value;})])
            .range([0, h]);

        var key = function(d) {
            return d.key;
        };

//Create SVG element
       // var svg = d3.select("body")
        $("#chartContainer").html("");
        var svg = d3.select("#chartContainer")
            .append("svg")
            .attr("width", w)
            .attr("height", h);
        /*

//Sets min and max on xScale
        var xMin = d3.min(dataset, function(d) { return d.patient_no;});
        var xMax = d3.max(dataset, function(d) { return d.patient_no;});
//Defines the y axis styles
        var yAxis = d3.svg.axis()
            .scale(yScale)
            .tickSize(10)
            .tickPadding(8)
            .orient("left");

//Defines the x axis styles
        var xAxis = d3.svg.axis()
            .scale(xScale)
            .tickSize(10)
            .tickPadding(15)
            .orient("bottom")
           // .tickValues([xMin, xMax])
            //.tickFormat(d3.time.format("%b %Y"));
//Appends the y axis
        var yAxisGroup = svg.append("svg")
            .attr("class", "y axis")
            .call(yAxis)
            .selectAll("svg")
            .classed("g-baseline", function(d) { return d == 0 });

//Appends the x axis
        var xAxisGroup = svg.append("svg")
            .attr("class", "x axis")
            .call(xAxis);
*/

//Create bars
    var bars = svg.selectAll("rect")
            .data(dataset, key)
            .enter()
            //.append("g")
            .append("rect")
            .attr("x", function(d, i) {
                return xScale(i);
            })
            .attr("y", function(d) {
                return h - yScale(d.value);
            })
            .attr("width", xScale.rangeBand())
            .attr("height", function(d) {
                return yScale(d.value);
            })
            .attr("fill", function(d) {
                return "rgb(65, 65, " + (d.value * 10) + ")";
            });

//Create labels
        svg.selectAll("text")
            .data(dataset, key)
            .enter()
            .append("text")
            .text(function(d) {
                return d.value;
            })
            .attr("text-anchor", "middle")
            .attr("x", function(d, i) {
                return xScale(i) + xScale.rangeBand() / 2;
            })
            .attr("y", function(d) {
                return h - yScale(d.value) + 14;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "11px")
            .attr("fill", "white");

        var sortOrder = false;
        var sortBars = function () {
            sortOrder = !sortOrder;

            sortItems = function (a, b) {
                if (sortOrder) {
                    return a.value - b.value;
                }
                return b.value - a.value;
            };

            svg.selectAll("rect")
                .sort(sortItems)
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .attr("x", function (d, i) {
                    return xScale(i);
                });

            svg.selectAll('text')
                .sort(sortItems)
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .text(function (d) {
                    return d.value;
                })
                .attr("text-anchor", "middle")
                .attr("x", function (d, i) {
                    return xScale(i) + xScale.rangeBand() / 2;
                })
                .attr("y", function (d) {
                    return h - yScale(d.value) + 14;
                });
        };
// Add the onclick callback to the button
        d3.select("#sort").on("click", sortBars);
        d3.select("#reset").on("click", reset);
        function randomSort() {


            svg.selectAll("rect")
                .sort(sortItems)
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .attr("x", function (d, i) {
                    return xScale(i);
                });

            svg.selectAll('text')
                .sort(sortItems)
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .text(function (d) {
                    return d.value;
                })
                .attr("text-anchor", "middle")
                .attr("x", function (d, i) {
                    return xScale(i) + xScale.rangeBand() / 2;
                })
                .attr("y", function (d) {
                    return h - yScale(d.value) + 14;
                });
        }
        function reset() {
            svg.selectAll("rect")
                .sort(function(a, b){
                    return a.key - b.key;
                })
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .attr("x", function (d, i) {
                    return xScale(i);
                });

            svg.selectAll('text')
                .sort(function(a, b){
                    return a.key - b.key;
                })
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .text(function (d) {
                    return d.value;
                })
                .attr("text-anchor", "middle")
                .attr("x", function (d, i) {
                    return xScale(i) + xScale.rangeBand() / 2;
                })
                .attr("y", function (d) {
                    return h - yScale(d.value) + 14;
                });
        };

        //Tooltip
        var tip = d3.tip()
            .attr('class', 'd3-tip')
            .offset([-10, 0])
            .html(function(d) {
                return "<strong>Score:</strong> <span style='color:red'>" + d.value + "</span>";
            });
        svg.call(tip);
        bars.on("mouseover",tip.show);
        bars.on("mouseout",tip.hide);

        /*        var rect = svg.selectAll("rect")
         .on("mouseover",function(d){

         //Get this bar's x/y values, then augment for the tooltip
         var xPosition = parseFloat(d3.select(this).attr("x")) + xScale.rangeBand() / 2;
         var yPosition = parseFloat(d3.select(this).attr("y")) + 14;

         //Update Tooltip Position & value
         d3.select("#tooltip")
         .style("left", xPosition + "px")
         .style("top", yPosition + "px")
         .select("#value")
         .text(d.value);
         d3.select("#tooltip").classed("hidden", false);
         console.log("ToolTips",xPosition);
         });*/

    }
    function callD3BarChartWithSorting(feature,circle){
        var w = 600;
        var h = 250;

        var dataset1 = [
            { key: 0, value: 5 },
            { key: 1, value: 10 },
            { key: 2, value: 13 },
            { key: 3, value: 19 },
            { key: 4, value: 21 },
            { key: 5, value: 25 },
            { key: 6, value: 22 },
            { key: 7, value: 18 },
            { key: 8, value: 15 },
            { key: 9, value: 13 },
            { key: 10, value: 11 },
            { key: 11, value: 12 },
            { key: 12, value: 15 },
            { key: 13, value: 20 },
            { key: 14, value: 18 },
            { key: 15, value: 17 },
            { key: 16, value: 16 },
            { key: 17, value: 18 },
            { key: 18, value: 23 },
            { key: 19, value: 25 } ];
        var dataset = JSON.stringify(feature.properties);
        //var dataset = (feature.properties);
       // var dataset =  JSON.stringify(result);
        for(var key in dataset){
            console.log(dataset);
        }
        console.log(dataset);
        var xScale = d3.scale.ordinal()
            .domain(d3.range(dataset.length))
            .rangeRoundBands([0, w], 0.05);
        console.log(xScale);
        var yScale = d3.scale.linear()
            .domain([0, d3.max(dataset, function(d) {return d.patient_no;})])
            .range([0, h]);

        var key = function(d) {
            return d.patient_no;
        };

//Create SVG element
        var svg = d3.select("body")
            .append("svg")
            .attr("width", w)
            .attr("height", h);
        console.log("1");
//Create bars
        svg.selectAll("rect")
            .data(dataset, key)
            .enter()
            .append("rect")
            .attr("x", function(d, i) {
                return xScale(i);
            })
            .attr("y", function(d) {
                return h - yScale(d.patient_no);
            })
            .attr("width", xScale.rangeBand())
            .attr("height", function(d) {
                return yScale(d.patient_no);
            })
            .attr("fill", function(d) {
                return "rgb(0, 0, " + (d.patient_no * 10) + ")";
            })

            //Tooltip
            .on("mouseover", function(d) {
                //Get this bar's x/y values, then augment for the tooltip
                var xPosition = parseFloat(d3.select(this).attr("x")) + xScale.rangeBand() / 2;
                var yPosition = parseFloat(d3.select(this).attr("y")) + 14;

                //Update Tooltip Position & value
                d3.select("#tooltip")
                    .style("left", xPosition + "px")
                    .style("top", yPosition + "px")
                    .select("#value")
                    .text(d.patient_no);
                d3.select("#tooltip").classed("hidden", false)
            })
            .on("mouseout", function() {
                //Remove the tooltip
                d3.select("#tooltip").classed("hidden", true);
            })	;
        console.log("2");
//Create labels
        svg.selectAll("text")
            .data(dataset, key)
            .enter()
            .append("text")
            .text(function(d) {
                return d.value;
            })
            .attr("text-anchor", "middle")
            .attr("x", function(d, i) {
                return xScale(i) + xScale.rangeBand() / 2;
            })
            .attr("y", function(d) {
                return h - yScale(d.value) + 14;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "11px")
            .attr("fill", "white");

        var sortOrder = false;
        var sortBars = function () {
            sortOrder = !sortOrder;

            sortItems = function (a, b) {
                if (sortOrder) {
                    return a.value - b.value;
                }
                return b.value - a.value;
            };

            svg.selectAll("rect")
                .sort(sortItems)
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .attr("x", function (d, i) {
                    return xScale(i);
                });

            svg.selectAll('text')
                .sort(sortItems)
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .text(function (d) {
                    return d.value;
                })
                .attr("text-anchor", "middle")
                .attr("x", function (d, i) {
                    return xScale(i) + xScale.rangeBand() / 2;
                })
                .attr("y", function (d) {
                    return h - yScale(d.value) + 14;
                });
        };
// Add the onclick callback to the button
        d3.select("#sort").on("click", sortBars);
        d3.select("#reset").on("click", reset);
        function randomSort() {


            svg.selectAll("rect")
                .sort(sortItems)
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .attr("x", function (d, i) {
                    return xScale(i);
                });

            svg.selectAll('text')
                .sort(sortItems)
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .text(function (d) {
                    return d.value;
                })
                .attr("text-anchor", "middle")
                .attr("x", function (d, i) {
                    return xScale(i) + xScale.rangeBand() / 2;
                })
                .attr("y", function (d) {
                    return h - yScale(d.value) + 14;
                });
        }
        function reset() {
            svg.selectAll("rect")
                .sort(function(a, b){
                    return a.key - b.key;
                })
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .attr("x", function (d, i) {
                    return xScale(i);
                });

            svg.selectAll('text')
                .sort(function(a, b){
                    return a.key - b.key;
                })
                .transition()
                .delay(function (d, i) {
                    return i * 50;
                })
                .duration(1000)
                .text(function (d) {
                    return d.value;
                })
                .attr("text-anchor", "middle")
                .attr("x", function (d, i) {
                    return xScale(i) + xScale.rangeBand() / 2;
                })
                .attr("y", function (d) {
                    return h - yScale(d.value) + 14;
                });
        };
    }
    function callD3BarChart(feature, circle){
        data = JSON.stringify(feature.properties);
        console.log(JSON.stringify(data));
        var margin = {
                top: 20,
                right: 90,
                bottom: 80,
                left: 30
            },
            width = 600 - margin.left - margin.right,
            height = 300 - margin.top - margin.bottom,
            barWidth = width / data.length;

        var x = d3.scale.linear()
            .domain([0, data.length])
            .range([0, width]);
        // console.log('x: '+ x);
        var xAxis = d3.svg.axis()
            .scale(x)
            .tickSize(5)
            .orient("bottom")
            .tickFormat(function(d) {
                return "";
            })

        var y = d3.scale.linear()
            .domain([0, 1])
            .range([height, 0]);

        var yAxis = d3.svg.axis()
            .scale(y)
            .orient("left")
            .ticks(5);

        var svg = d3.select("#chartContainer").append("svg") //Here instead of d3.select("body") I used d3.select("#chartContainer") to append the chart in #chartContainer div
            .attr("width",300)
            .attr("height",300)
            .attr("class", "chartHolder")
            ;

        var g = svg.append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

       // x.domain(data.map(function(d) { return d.LATITUDE; }));
        x.domain([0, d3.max(data, function(d) { return d.LONGITUDE; })]);
        y.domain([0, d3.max(data, function(d) { return d.LONGITUDE; })]);


        var padding = 20;
        var xaxis = 50;
        var color = d3.scale.category10();

        var xScale = d3.scale.linear()
            // .domain([0, d3.max(dataset,function(d){return marker._leaflet_id})])
            .domain([0, d3.max(data,function(d){return d.LONGITUDE})])
            .range([padding, 500-padding]);
        console.log(xScale);
        var yScale = d3.scale.linear()
            .domain([0, d3.max(data, function(d){return d.LONGITUDE})])
            //.domain([0, d3.max(dataset, function(d){return marker._leaflet_id})])
            .range([500-padding, 	padding]);

        xAxis = d3.svg.axis() // generate an axis
            .scale(xScale) // set the range of the axis
            .tickSize(5) // height of the ticks
            .orient("bottom")
            .tickSubdivide(true), // display ticks between text labels

            yAxis = d3.svg.axis() // generate an axis
                .scale(yScale) // set the range of the axis
                .tickSize(5) // width of the ticks
                .orient("right") // have the text labels on the left hand side
                .tickSubdivide(true); // display ticks between text labels

        svg.append("svg")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + xaxis + ")")
            .call(xAxis);

        svg.append("svg")
            .attr("class", "y axis")
            .attr("transform", "translate(0," + xaxis + ")")
            .call(yAxis);


/*        g.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x));

        g.append("g")
            .attr("class", "axis axis--y")
            .call(d3.axisLeft(y).ticks(10, "%"))
            .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", "0.71em")
            .attr("text-anchor", "end")
            .text("Frequency");*/

        g.selectAll(".bar")
            .data(data)
            .enter().append("rect")
            .attr("class", "bar")
            .attr("x", function(d) { return 10; })
            .attr("y", function(d) { return 100; })
            .attr("width", x.LONGITUDE)
            .attr("height", function(d) { return height - 10; });

       /* var svg = d3.select("#chartContainer").append("svg") //Here instead of d3.select("body") I used d3.select("#chartContainer") to append the chart in #chartContainer div
            .attr("width",300)
            .attr("height",300)
            .attr("class", "chartHolder")
            ;

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis);

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis);

        var bar = svg.selectAll("g.bar")
            .data(data)
            .enter()
            .append("g")
            .attr("transform", function(d, i) {
                return "translate(" + (i * barWidth + 5) + ",0)";
            });

        bar.append("rect")
            .attr("y", function(d) {
                if (!isNaN(d.value)) {
                    return y(d.value);
                } else {
                    return 0;
                }
            })
            .attr("width", barWidth - 10)
            .attr("height", function(d) {
                if (!isNaN(d.value)) {
                    return height - y(d.value);
                } else {
                    return 0;
                }
            })
            .attr("fill",function(d,i){return colors(i)})


        bar.append("text")
            .attr("x", function(d) {
                return -height - 70;
            })
            .attr("y", barWidth / 2)
            .attr("transform", "rotate(270)")
            .text(function(d) {
                return d.name;
            });*/
    }

    function callD3Popup(feature,circle){

        var div = $('<div id="chart"><h3>Ethnic Group Distribution</h3><svg/><h4>Additional details:</h4>Extra stuff here</div>')[0];
        var popup = L.popup({
            minWidth: 500,
            minHeight: 350
        }).setContent(div);
        circle.bindPopup(popup);

        data = feature;
        console.log('data'+data);
        var margin = {
                top: 20,
                right: 90,
                bottom: 80,
                left: 30
            },
            width = 600 - margin.left - margin.right,
            height = 300 - margin.top - margin.bottom,
            barWidth = width / data.length;

        var x = d3.scale.linear()
            .domain([0, data.length])
            .range([0, width]);
       // console.log('x: '+ x);
        var xAxis = d3.svg.axis()
            .scale(x)
            .orient("bottom")
            .tickFormat(function(d) {
                return "";
            })

        var y = d3.scale.linear()
            .domain([0, 1])
            .range([height, 0]);

        var yAxis = d3.svg.axis()
            .scale(y)
            .orient("left")
            .ticks(5)

        var svg = d3.select('#chartContainer')
            .select("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
            .classed("chart", true);

        /*var svg = d3.select("#chartContainer").append("svg") //Here instead of d3.select("body") I used d3.select("#chartContainer") to append the chart in #chartContainer div
            .attr("width",300)
            .attr("height",300)
            .attr("class", "chartHolder")
            ;*/

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis);

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis);

        var bar = svg.selectAll("g.bar")
            .data(data)
            .enter()
            .append("g")
            .attr("transform", function(d, i) {
                return "translate(" + (i * barWidth + 5) + ",0)";
            });

        bar.append("rect")
            .attr("y", function(d) {
                if (!isNaN(d.value)) {
                    return y(d.value);
                } else {
                    return 0;
                }
            })
            .attr("width", barWidth - 10)
            .attr("height", function(d) {
                if (!isNaN(d.value)) {
                    return height - y(d.value);
                } else {
                    return 0;
                }
            })
            .attr("fill",function(d,i){return colors(i)})


        bar.append("text")
            .attr("x", function(d) {
                return -height - 70;
            })
            .attr("y", barWidth / 2)
            .attr("transform", "rotate(270)")
            .text(function(d) {
                return d.name;
            });

    }

    function getCircleRadius(x) {
        return x < 10 ? 10000 :
            x > 50 ? 10000 :
                x > 100 ? 20000 :
                    x > 150 ? 30000 :
                        x > 200 ? 40000 :
                            x > 250 ? 50000 :
                                x > 300 ? 60000 :
                                    100000;
    }
    function getRadius(x) {
        return x > 10 ? 1000 :
            x > 50 ? 1000 :
                x > 100 ? 2000 :
                    x > 150 ? 3000 :
                        x > 200 ? 4000 :
                            x > 250 ? 5000 :
                                x > 300 ? 6000 :
                                    10000;
    }
    function getColor(x) {
        return x > 7 ? "red" :
            x > 6.5 ? "#BD0026" :
                x > 6 ? "#E31A1C" :
                    x > 5.5 ? "#FC4E2A" :
                        x > 5 ? "#FD8D3C" :
                            x > 4.5 ? "#FEB24C" :
                                x > 4 ? "#FED976" :
                                    "#FFEDA0";
    }

}
   /* // load GeoJSON from an external file
    $.getJSON("returnResult.php",function(data){
        console.log("Hee");
        //create custom icon
        var ratIcon = L.icon({
            iconUrl: 'images/icon.png',
            iconSize: [60,50]
        });
        var rodents = L.geoJson(data,{
            pointToLayer: function(feature,latlng){
                //var marker =  L.marker(latlng,{icon: ratIcon, draggable: true}); // This is customized icon
                var marker =  L.marker(latlng,{ draggable: true});
                var radius = 500;
                var circleStyle = {
                    "color":"red",
                    "opacity":.9,
                    "fillColor":"green"
                };

                /!*if(feature.properties.source="Constituent Call"){
                    radius = 100;
                    color = 'red';
                }else {
                    radius = 50;
                    color = 'green';
                }*!/

                var circle = L.circle(latlng,radius,circleStyle).addTo(map);
                marker.bindPopup(feature.properties.Location + '<br/>' + feature.properties.OPEN_DT);
                //marker.bindPopup(console.log(feature.properties.Location));
                marker.on('click', function(a){
                    callD3(marker);
                });

                return circle;
            }
        });
        var clusters = L.markerClusterGroup({
            animateAddingMarkers: true,
            //disableClusteringAtZoom: true,
        });
        map.addLayer(rodents);
        clusters.addLayer(rodents);
        map.addLayer(clusters);


        clusters.on('clusterclick',function(a){
           // console.log(clusters);
        });
    });*/
});
console.log("Theere");
    function callD3(marker){
        console.log('here');
        console.log(marker);
        var dataset = [ 5, 10, 13, 19, 21, 25, 22, 18, 15, 13,
            11, 12, 15, 20, 18, 17, 16, 18, 23, 25 ];



        var dataset = [
            { "cx": 100, "cy": 20, "radius": 10, "color" : "green" },
            { "cx": 97, "cy": 10, "radius": 20, "color" : "black" },
            { "cx": 50, "cy": 20, "radius": 10, "color" : "black" },
            { "cx": 40, "cy": 35, "radius": 33, "color" : "black" },
            { "cx": 30, "cy": 40, "radius": 45, "color" : "black" },
            { "cx": 63, "cy": 45, "radius": 20, "color" : "black" },
            { "cx": 70, "cy": 49, "radius": 87, "color" : "black" },
            { "cx": 88, "cy": 55, "radius": 67, "color" : "black" },
            { "cx": 80, "cy": 60, "radius": 15, "color" : "black" },
            { "cx": 90, "cy": 61, "radius": 74, "color" : "black" },
            { "cx": 85, "cy": 66, "radius": 25, "color" : "black" },
            { "cx": 70, "cy": 70, "radius": 50, "color" : "purple" }];

        var dataset1 = [
            [5, 20], [480, 90], [250, 50], [100, 33], [330, 95],
            [410, 12], [475, 44], [25, 67], [85, 21], [220, 88]
        ];

        var padding = 20;
        var xaxis = 50;
        var color = d3.scale.category10();

        var xScale = d3.scale.linear()
               // .domain([0, d3.max(dataset,function(d){return marker._leaflet_id})])
                .domain([0, d3.max(dataset,function(d){return d.cx})])
                .range([padding, 500-padding]);
        console.log(xScale);
        var yScale = d3.scale.linear()
                .domain([0, d3.max(dataset, function(d){return d.cy})])
                //.domain([0, d3.max(dataset, function(d){return marker._leaflet_id})])
                .range([500-padding, 	padding]);

        var rScale = d3.scale.linear()
                .domain([0, d3.max(dataset, function(d){return d.radius})])
                //.domain([0, d3.max(dataset, function(d){return marker._leaflet_id})])
                .range([0, 50]);



        var svg = d3.select("#chartContainer").append("svg") //Here instead of d3.select("body") I used d3.select("#chartContainer") to append the chart in #chartContainer div
                .attr("width",300)
                .attr("height",300)
                .attr("class", "chartHolder")
                ;


        var circles = svg.selectAll("circle")
                .data(dataset)
                .enter()
                .append("circle");
       // var bar =

        var circleAttributes = circles
                .attr("cx",function(d){return xScale(d.cx);})
                .attr("cy",function(d){return xScale(d.cy);})
               // .attr("r",function(d){return rScale(marker._leaflet_id);})
                .attr("r",function(d){return rScale(d.radius);})
                .attr("stroke", "black")
                .style("fill", function (d,i) { return color(i); })
                ;

        var text = svg.selectAll("text")
                .data(dataset)
                .enter()
                .append("text")
                ;
        /**
         var labelText = text
         .attr("x", function(d){ return xScale(d.cx)})
         .attr("y", function(d){ return yScale(d.cy)})
         .text(function(d){ return d.cx+","+d.cy})
         //.attr("font-family", "sans-serif")
         .attr("font-size", "20px")
         .attr('fill', function(d){return getColor(d)});
         ;

         **/

        xAxis = d3.svg.axis() // generate an axis
                .scale(xScale) // set the range of the axis
                .tickSize(5) // height of the ticks
                .orient("bottom")
                .tickSubdivide(true), // display ticks between text labels

        yAxis = d3.svg.axis() // generate an axis
                .scale(yScale) // set the range of the axis
                .tickSize(5) // width of the ticks
                .orient("left") // have the text labels on the left hand side
                .tickSubdivide(true); // display ticks between text labels

        svg.append("svg")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + xaxis + ")")
                .call(xAxis);

        svg.append("svg")
            .attr("class", "y axis")
            .attr("transform", "translate(0," + xaxis + ")")
            .call(yAxis);

        svg.append("g")
                .attr("class", "y axis")
                .call(yAxis)
                .append("text")
                .attr("transform", "rotate(-90)")
                .attr("y", 6)
                .attr("dy", ".71em")
                .style("text-anchor", "end")
                .text(marker.properties.name);

        function getColor(d){
            r = 120;
            b = r + (d * 7) % (256-r);

            return 'rgb(' + r + ',' + r + ',' + b + ')' ;
        }
    }

</script>
</body>
</html>