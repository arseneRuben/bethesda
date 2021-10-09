   function graph() {
               //Width and height
			
			var max=0;
           
			var dataset = [
                                    {'language':'Java',
                                    'value': 34},
                                   {'language':'C++',
                                    'value': 10},
                                    {'language':'C#',
                                    'value': 26},
                                    {'language':'sql',
                                    'value': 4},
                                    {'language':'Php',
                                    'value': 42},
                                    {'language':'D3',
                                    'value': 15},
                                   {'language':'Plotly',
                                    'value': 20},
                                    {'language':'Pascal',
                                    'value': 16},
                                    {'language':'.Net',
                                    'value': 18},
                                    {'language':'Angular',
                                    'value': 22},
                ];
			
			var xScale = d3.scale.ordinal()
							.domain(d3.range(dataset.length))
							.rangeRoundBands([0, w], 0.05);
             dataset.forEach(function (d1) {
                     max = (max < d1.value)? d1.value: max;
                
                  })
			var yScale = d3.scale.linear()
							.domain([0, max])
							.range([0, h]);
			
			

            
            //Create SVG element
			var svg = d3.select("p")
						.append("svg")
						.attr("width", w)
						.attr("height", h)
                        ;
            ///////////////////////
            // Axis
            var xAxis = d3.svg.axis()
                .scale(xScale)
                .orient("bottom");

             var yAxis = d3.svg.axis()
                            .scale(yScale)
                            .orient("left")
                            .ticks(5);
             svg.append("svg")
                .attr("class", "y axis")
                .call(yAxis); 
            

            

            
			//Create bars
			svg.selectAll("rect")
			   .data(dataset)
			   .enter()
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
					return "rgb(10, 10, 10)";
			   });
    
			//Create Courses labels
			svg.selectAll("text")
			   .data(dataset)
			   .enter()
			   .append("text")
			   .text(function(d) {
			   		return d.language;
			   })
			   .attr("text-anchor", "middle")
			   .attr("x", function(d, i) {
			   		return xScale(i) + xScale.rangeBand() / 2;
			   })
			   .attr("y", function(d) {
			   		return h  - 14;
			   })
			   .attr("font-family", "sans-serif")
			   .attr("font-size", "8px")
               
			   .attr("fill", "white");
                 ;

                 svg.append('svg')
                .attr('class', 'grid')
                .attr('transform', `translate(0, ${ yScale(h)})`)
                .call(xAxis
                    .tickSize(-h, 0, 0)
                    .tickFormat(''));

           
             /*    svg.append('svg')
                .attr('class', 'grid')
                .call(yAxis
                    .tickSize(-xScale(w), 0, 0)
                    .tickFormat(''));

                

           svg.append("g")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + yScale(h) + ")")
                .call(xAxis);*/

            
            
            
            }