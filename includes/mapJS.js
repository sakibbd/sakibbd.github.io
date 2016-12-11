function mapCreation(){
  var mapContainerId = document.getElementById("map");
  console.log(mapContainerId);
 // initialize the map
  var map = L.map(mapContainerId).setView([23.6850, 90.3563], 1);

  // load a tile layer
  L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
      //attribution: 'Tiles by <a href="http://mapc.org">MAPC</a>, Data by <a href="http://mass.gov/mgis">MassGIS</a>',
      attribution: 'Tiles by <a href="http://mapc.org">MAPC</a>, Data by <a href="http://mass.gov/mgis">MassGIS</a>',
      maxZoom: 17,
      minZoom: 0
    }).addTo(map);
	
	
		//window.map = map;

/**$(document).ready(function(){
    $(window).scroll(function(){
        window.map.invalidateSize(false);
    })
})
**/


	
	//Add another layer of the number of rats from other sources
	$.getJSON("neighborhoods.geojson",function(hoodData){
		L.geoJson( hoodData, {
			style: function(feature){
			  var fillColor,
				  density = feature.properties.density;
			  if ( density > 80 ) fillColor = "#006837";
			  else if ( density > 40 ) fillColor = "#FF0000";
			  else if ( density > 20 ) fillColor = "#78c679";
			  else if ( density > 10 ) fillColor = "#c2e699";
			  else if ( density > 0 ) fillColor = "#ffffcc";
			  else fillColor = "#f7f7f7";  // no data
			  return { color: "#999", weight: 1, fillColor: fillColor, fillOpacity: .6 };
			},
			onEachFeature: function( feature, layer ){
			  layer.bindPopup( "<strong>" + feature.properties.Name + "</strong><br/>" + feature.properties.density + " rats per square mile" )
			}
		  }).addTo(map);
		}); 
		
// load GeoJSON from an external file
  $.getJSON("rodents.geo.json",function(data){

  //create custom icon
	var ratIcon = L.icon({
		iconUrl: 'images/icon.png',
		iconSize: [60,50]
	});
 var rodents = L.geoJson(data,{
      pointToLayer: function(feature,latlng){
	    //var marker =  L.marker(latlng,{icon: ratIcon, draggable: true}); // This is customized icon
	    var marker =  L.marker(latlng,{ draggable: true});
		marker.bindPopup(feature.properties.Location + '<br/>' + feature.properties.OPEN_DT);
		//marker.bindPopup(console.log(feature.properties.Location));
		marker.on('click', function(a){
			callD3_circle(marker);
		});
		
		return marker;
      }
    });
/**	
	//Testing with the search option in the map
	// load GeoJSON from an external file
  $.getJSON("rodents.geo.json",function(data){
  
   //create custom icon
	var ratIcon = L.icon({
		iconUrl: 'images/icon.png',
		iconSize: [60,50]
	});
	
	
	 var itemToSearch = L.geoJson(data,{
      pointToLayer: function(feature,latlng){
	    var marker =  L.marker(latlng,{icon: ratIcon, draggable: true}); // This is customized icon
	    var marker =  L.marker(latlng,{ draggable: true});
		return marker;
      }
    });
	
	var markersLayer = new L.LayerGroup();	//layer contain searched elements
	//map.addLayer(markersLayer);
	
	map.addControl( new L.Control.Search({
		layer: itemToSearch,
		initial: false,
		collapsed: false,
		textPlaceholder: 'Color...',
		markerLocation: true,
		
	}) );
	
	
	////////////populate map with markers from sample data
	for(i in data) {
		var title = data[i].LOCATION_STREET_NAME,	//value searched
			loc = data[i].loc,		//position found
			marker = new L.Marker(new L.latLng(loc), {title: title} );//se property searched
		marker.bindPopup('title: '+ title );
		markersLayer.addLayer(marker);
	}

});

var fuse = new Fuse(restaurant500.features, {
		keys: ['properties.name', 'properties.cuisine']
	});

	L.control.search({
		layer: restLayers,
		propertyName: 'name',
		filterData: function(text, records) {
			var jsons = fuse.search(text),
				ret = {}, key;
			
			for(var i in jsons) {
				key = jsons[i].properties.name;
				ret[ key ]= records[key];
			}

			console.log(jsons,ret);
			return ret;
		}
	})
	.on('search_locationfound', function(e) {
		e.layer.openPopup();
	})
	.addTo(map);
	**/

	
	//This is for the clustering
    var clusters = L.markerClusterGroup({
		animateAddingMarkers: true,
		//disableClusteringAtZoom: true,
		//chunkProgress:true,
	});
		clusters.addLayer(rodents);
		map.addLayer(clusters);
		
	clusters.on('clusterclick',function(a){
		console.log(clusters);
	});
	
   }); 
   
   
   function callD3_circle(marker){
       
var margin = {top: 20, right: 100, bottom: 30, left: 100},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;
	

						
var dataset = [
   { "cx": 100, "cy": 20, "radius": 10, "color" : "green" },
   { "cx": 97, "cy": 10, "radius": 20, "color" : "black" },
   { "cx": 50, "cy": 20, "radius": 10, "color" : "black" },
   { "cx": 40, "cy": 35, "radius": 33, "color" : "black" },
   { "cx": 30, "cy": 40, "radius": 45, "color" : "black" }];
   
var dataset1 = [
                [5, 20], [480, 90], [250, 50], [100, 33], [330, 95],
                [410, 12], [475, 44], [25, 67], [85, 21], [220, 88]
              ];

			  
var padding = 20;
var xaxis = 50;
var color = d3.scale.category10();

var xScale = d3.scale.linear()
					 .domain([0, d3.max(dataset,function(d){return marker._leaflet_id})])
					 .range([padding, 500-padding]);

var yScale = d3.scale.linear()
					 //.domain([0, d3.max(dataset, function(d){return d.cy})])
					 .domain([0, d3.max(dataset, function(d){return marker._leaflet_id})])
					 .range([500-padding, 	padding]);
					 
var rScale = d3.scale.linear()
					//.domain([0, d3.max(dataset, function(d){return d.radius})])
					.domain([0, d3.max(dataset, function(d){return marker._leaflet_id})])
					.range([0, 50]);
					
//This is added for zooming facility
	var zoom = d3.behavior.zoom()
    .x(xScale)
    .y(yScale)
    .scaleExtent([1, 10])
    .on("zoom", zoomed);

	function zoomed() {
  svg.select(".x.axis").call(xAxis);
  svg.select(".y.axis").call(yAxis);
}

function reset() {
  svg.call(zoom
      .x(x.domain([-width / 2, width / 2]))
      .y(y.domain([-height / 2, height / 2]))
      .event);
}

d3.select("button").on("click", reset);
//end of zooming facility

/** This is zooming the axis only			
var svg = d3.select("#chartContainer").append("svg") //Here instead of d3.select("body") I used d3.select("#chartContainer") to append the chart in #chartContainer div
						   .attr("width",500)
						   .attr("height",500)
						   .attr("class", "chartHolder")
						   .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
						   .call(zoom)//call the zoom facility
						   ;
**/
//This is zooming the whole chart
  var svg = d3.select("#chartContainer").append("svg") //Here instead of d3.select("body") I used d3.select("#chartContainer") to append the chart in #chartContainer div
						   .attr("width",500)
						   .attr("height",500)
						   .attr("class", "chartHolder")
						   .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
						   .call(d3.behavior.zoom().on("zoom", function () {
    svg.attr("transform", "translate(" + d3.event.translate + ")" + " scale(" + d3.event.scale + ")")
  }))
							.append("g")
						   ;



var circles = svg.selectAll("circle")
				 .data(dataset)
				 .enter()
				 .append("circle");

var circleAttributes = circles
				 .attr("cx",function(d){return xScale(d.cx);})
				 .attr("cy",function(d){return xScale(d.cy);})
				 .attr("r",function(d){return rScale(marker._leaflet_id);})
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
/**				
xAxis = d3.svg.axis() // generate an axis
    .scale(xScale) // set the range of the axis
    .tickSize(5) // height of the ticks
	.orient("bottom")
    .tickSubdivide(true); // display ticks between text labels

**/
var xAxis = d3.svg.axis()
    .scale(xScale)
    .orient("top")
    .innerTickSize(-height)
    .outerTickSize(0)
    .tickPadding(10);


yAxis = d3.svg.axis() // generate an axis
	.scale(yScale) // set the range of the axis
	.tickSize(5) // width of the ticks
	.orient("left") // have the text labels on the left hand side
	.innerTickSize(-width)
	.tickSubdivide(true); // display ticks between text labels
        //.tickSubdivide(false);

/**
var yAxis = d3.svg.axis()
    .scale(yScale)
    .orient("left")
    .innerTickSize(-width)
    .outerTickSize(0)
    .tickPadding(10);	
**/	
svg.append("svg")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + xaxis + ")")
      .call(xAxis);	
	  
	  
 svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text(marker._leaflet_id);
	  

	
function getColor(d){
  r = 120;
  b = r + (d * 7) % (256-r);
    
  return 'rgb(' + r + ',' + r + ',' + b + ')' ;
}
}

function toggleMap(){
	 //document.getElementById("map").style.display= "inline" ;
	 var test = document.getElementById('map').style.display.value;
	 console.log(test);
	 if(document.getElementById('map').style.display == 'none'){
		document.getElementById('map').style.display= 'inline' ;
	 }else
	 document.getElementById('map').style.display= 'none' ;
}

}