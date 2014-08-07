// JavaScript Document
             //http://hi.baidu.com/liuhao8640/item/69012834ed4fd849033edcad
             var iscreatr=false;
             function initialize() {
              var map = new BMap.Map("bdmap_container",{minZoom:5,maxZoom:30});
              map.centerAndZoom("淮安",15);
              map.enableScrollWheelZoom(true);
              map.addEventListener("click", function(e){
                   if(iscreatr==true)return;
                   iscreatr=true;
                    var point = new BMap.Point(e.point.lng ,e.point.lat); //get bdmap_coordinates from event listener
                    var marker = new BMap.Marker(point);  //create coordinate
                    var label = new BMap.Label("Drag this to where you want it",{offset:new BMap.Size(20,-10)}); //add and set label
                    marker.setLabel(label)
                    map.addOverlay(marker); //add to map
                    //document
                    document.getElementById("bdmap_coordinates").value = e.point.lng + "," + e.point.lat;
                    marker.enableDragging();
                    marker.addEventListener("dragend",function(e){ 
                    document.getElementById("bdmap_coordinates").value = e.point.lng + "," + e.point.lat;
                    });
              });
             }
             initialize();