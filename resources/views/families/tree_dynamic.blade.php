@extends('adminlte::page')

@section('title', $family->name . ' - Interactive Tree')

@section('content_header')
    <h1>
        <i class="fas fa-project-diagram"></i> {{ $family->name }} - Interactive Tree
        <small>Dynamic Visualization</small>
    </h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-sitemap"></i> Family Hierarchy
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="tree-container" style="width: 100%; height: 800px; overflow: hidden; background-color: #f4f6f9;"></div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-center">
                    <span class="badge badge-primary mr-2"><i class="fas fa-male"></i> Male</span>
                    <span class="badge badge-danger mr-2"><i class="fas fa-female"></i> Female</span>
                    <span class="badge badge-secondary"><i class="fas fa-cross"></i> Deceased</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const treeData = @json($treeData);
            
            if (!treeData || Object.keys(treeData).length === 0) {
                document.getElementById('tree-container').innerHTML = '<div class="alert alert-warning m-3">No family tree data available. Add family members to see the tree.</div>';
                return;
            }

            const container = document.getElementById('tree-container');
            const width = container.clientWidth;
            const height = container.clientHeight;

            // Zoom behavior
            const zoom = d3.zoom()
                .scaleExtent([0.1, 3])
                .on('zoom', (event) => {
                    svgGroup.attr('transform', event.transform);
                });

            const svg = d3.select('#tree-container').append('svg')
                .attr('width', width)
                .attr('height', height)
                .call(zoom)
                .append('g');
            
            const svgGroup = svg.append('g')
                .attr('transform', `translate(${width / 2}, 50)`);

            let i = 0;
            const duration = 750;
            let root;

            // Declares a tree layout and assigns the size
            const treemap = d3.tree().nodeSize([180, 100]);

            // Assigns parent, children, height, depth
            root = d3.hierarchy(treeData, function(d) { return d.children; });
            root.x0 = 0;
            root.y0 = 0;

            // Collapse after the second level
            // root.children.forEach(collapse);

            update(root);

            function collapse(d) {
                if(d.children) {
                    d._children = d.children;
                    d._children.forEach(collapse);
                    d.children = null;
                }
            }

            function update(source) {
                const treeData = treemap(root);

                // Compute the new tree layout
                const nodes = treeData.descendants(),
                      links = treeData.descendants().slice(1);

                // Normalize for fixed-depth
                nodes.forEach(function(d){ d.y = d.depth * 100});

                // ****************** Nodes section ******************

                // Update the nodes...
                const node = svgGroup.selectAll('g.node')
                    .data(nodes, function(d) {return d.id || (d.id = ++i); });

                // Enter any new modes at the parent's previous position.
                const nodeEnter = node.enter().append('g')
                    .attr('class', 'node')
                    .attr('transform', function(d) {
                        return "translate(" + source.x0 + "," + source.y0 + ")";
                    })
                    .on('click', click);

                // Add Circle for the nodes
                nodeEnter.append('circle')
                    .attr('class', 'node')
                    .attr('r', 1e-6)
                    .style("fill", function(d) {
                        return d._children ? "lightsteelblue" : "#fff";
                    })
                    .style("stroke", function(d) {
                        return d.data.attributes.gender === 'male' ? '#007bff' : '#dc3545';
                    })
                    .style("stroke-width", "3px");

                // Add labels for the nodes
                nodeEnter.append('text')
                    .attr("dy", ".35em")
                    .attr("y", function(d) { return d.children || d._children ? -25 : 25; })
                    .attr("text-anchor", "middle")
                    .text(function(d) { return d.data.name; })
                    .style("font-weight", "bold")
                    .style("fill-opacity", 1e-6)
                    .style("font-size", "14px");
                
                // Add spouse label if exists
                nodeEnter.append('text')
                    .attr("dy", "1.5em")
                    .attr("y", function(d) { return d.children || d._children ? -25 : 25; })
                    .attr("text-anchor", "middle")
                    .text(function(d) { return d.data.attributes.spouse ? '& ' + d.data.attributes.spouse : ''; })
                    .style("font-size", "12px")
                    .style("fill", "#666")
                    .style("fill-opacity", 1e-6);

                // UPDATE
                const nodeUpdate = nodeEnter.merge(node);

                // Transition to the proper position for the node
                nodeUpdate.transition()
                    .duration(duration)
                    .attr("transform", function(d) { 
                        return "translate(" + d.x + "," + d.y + ")";
                    });

                // Update the node attributes and style
                nodeUpdate.select('circle.node')
                    .attr('r', 15)
                    .style("fill", function(d) {
                        if (d.data.attributes.status === 'deceased') return '#ccc';
                        return d._children ? "lightsteelblue" : "#fff";
                    })
                    .attr('cursor', 'pointer');
                
                nodeUpdate.selectAll('text')
                    .style("fill-opacity", 1);

                // Remove any exiting nodes
                const nodeExit = node.exit().transition()
                    .duration(duration)
                    .attr("transform", function(d) {
                        return "translate(" + source.x + "," + source.y + ")";
                    })
                    .remove();

                nodeExit.select('circle')
                    .attr('r', 1e-6);

                nodeExit.select('text')
                    .style("fill-opacity", 1e-6);

                // ****************** Links section ******************

                // Update the links...
                const link = svgGroup.selectAll('path.link')
                    .data(links, function(d) { return d.id; });

                // Enter any new links at the parent's previous position.
                const linkEnter = link.enter().insert('path', "g")
                    .attr("class", "link")
                    .attr('d', function(d){
                        const o = {x: source.x0, y: source.y0}
                        return diagonal(o, o)
                    })
                    .style("fill", "none")
                    .style("stroke", "#ccc")
                    .style("stroke-width", "2px");

                // UPDATE
                const linkUpdate = linkEnter.merge(link);

                // Transition back to the parent element position
                linkUpdate.transition()
                    .duration(duration)
                    .attr('d', function(d){ return diagonal(d, d.parent) });

                // Remove any exiting links
                const linkExit = link.exit().transition()
                    .duration(duration)
                    .attr('d', function(d) {
                        const o = {x: source.x, y: source.y}
                        return diagonal(o, o)
                    })
                    .remove();

                // Store the old positions for transition.
                nodes.forEach(function(d){
                    d.x0 = d.x;
                    d.y0 = d.y;
                });

                // Creates a curved (diagonal) path from parent to the child nodes
                function diagonal(s, d) {
                    path = `M ${s.x} ${s.y}
                            C ${s.x} ${(s.y + d.y) / 2},
                              ${d.x} ${(s.y + d.y) / 2},
                              ${d.x} ${d.y}`
                    return path
                }

                // Toggle children on click.
                function click(event, d) {
                    if (d.children) {
                        d._children = d.children;
                        d.children = null;
                    } else {
                        d.children = d._children;
                        d._children = null;
                    }
                    update(d);
                }
            }
        });
    </script>
@stop
