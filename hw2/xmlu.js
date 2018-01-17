function submit(){
	
	var xmlu = document.getElementsByName("xmlurl")[0].value;
	console.log(xmlu);
	if (xmlu.search(".xml")!= -1){
		removetag("input");
		removetag("h1");
		removetag("a");
		removetag("p");
		removetag("br");
		removetag("style")
		var x = new XMLHttpRequest();
		x.open("GET", xmlu, true);
		x.onreadystatechange = function () {
		  if (x.readyState == 4 && x.status == 200)
		  {
			var doc = x.responseXML;
			if (xmlu.search("train")!= -1){
				trainxml(doc);
			}else{
				mlbxml(doc);
			}
		  }
		};
		x.send(null);
	}else{
		alert( "Input url isn't xml !!" );
	}
}

function removetag(tagname){
	var element = document.getElementsByTagName(tagname),index;
	for (index = element.length-1; index >=0;index--){
		element[index].parentNode.removeChild(element[index]);
	}
}
function trainxml(xml){
	console.log(xml.getElementsByTagName("train"));
	//css
	var linktag = document.createElement("link");
	linktag.rel = "stylesheet";
	linktag.type = "text/css";
	linktag.href = "train.css";
	document.getElementsByTagName("head")[0].appendChild(linktag);
	
	//title
	var titletag = document.createElement("title");
	titletag.innerHTML = "Train";
	document.getElementsByTagName("head")[0].appendChild(titletag);
	
	//innerdiv
	var innerdivtag = document.createElement("div");
	innerdivtag.className = "train_table";
	document.getElementsByTagName("body")[0].appendChild(innerdivtag);
	
	//h1
	var h1tag = document.createElement("h1");
	h1tag.innerHTML = "Train";
	var train_tb = document.getElementsByClassName("train_table")[0];
	train_tb.appendChild(h1tag);
	for(var x = 0;x <xml.getElementsByTagName("train").length;x++){
		//spandiv
		var train_data = xml.getElementsByTagName("train")[x];
		var divtag = document.createElement("div");
		divtag.className = "train_id";
		var spantag = document.createElement("span");
		spantag.innerHTML = train_data.childNodes[3].innerHTML+ '-' +train_data.childNodes[1].innerHTML;
		divtag.appendChild(spantag);
		train_tb.appendChild(divtag);
		
		//table
		var tabletag = document.createElement("table");
		var trtag = document.createElement("tr");
		for(var n = 0; n < 2; n ++){
			var trtag = document.createElement("tr");
			tabletag.appendChild(trtag);
		}
		//tr position - time
		for(var p = 5,t = 7; p < train_data.childNodes.length;p+=4,t+=4){
			var tdpostag = document.createElement("td");
			tdpostag.innerHTML = (train_data.childNodes.length-4 <= p)?train_data.childNodes[p].innerHTML:train_data.childNodes[p].innerHTML + "--->";
			var tdtimetag = document.createElement("td");
			tdtimetag.innerHTML = train_data.childNodes[t].innerHTML;
			tabletag.getElementsByTagName("tr")[0].appendChild(tdpostag);
			tabletag.getElementsByTagName("tr")[1].appendChild(tdtimetag);
		}
		train_tb.appendChild(tabletag);
	}
}
function mlbxml(xml){
	
	//css
	var linktag = document.createElement("link");
	linktag.rel = "stylesheet";
	linktag.type = "text/css";
	linktag.href = "mlb.css";
	document.getElementsByTagName("head")[0].appendChild(linktag);
	
	
	//title
	var titletag = document.createElement("title");
	titletag.innerHTML = "Baseball";
	document.getElementsByTagName("head")[0].appendChild(titletag);
	
	//h1
	var h1tag = document.createElement("h1");
	h1tag.innerHTML = "Baseball";
	var basebody = document.getElementsByTagName("body")[0];
	basebody.appendChild(h1tag);
	
	//basetable
	for (var x = 0; x < xml.getElementsByTagName("baseball").length;x++){
		//baseball data
		var baseball_data = xml.getElementsByTagName("baseball")[x];
		
		//table
		var tabletag = document.createElement("table");
		tabletag.className = "table_" + (x+1);
		tabletag.border = "1";
		tabletag.style.color = baseball_data.getElementsByTagName("Color")[0].innerHTML;
		tabletag.style.background = baseball_data.getElementsByTagName("BColor")[0].innerHTML;
		var tbodytag = document.createElement("tbody");
		
		
		//table_top
		var trtag = document.createElement("tr");
		trtag.className = "table_top"
		var thtag = document.createElement("th");
		thtag.colSpan = "4";
		thtag.innerHTML = baseball_data.getElementsByTagName("Team")[0].innerHTML;
		//append table_top components
		trtag.appendChild(thtag);
		tbodytag.appendChild(trtag);
		
		
		//twotrtag
		var trtag = document.createElement("tr");
		tbodytag.appendChild(trtag);
		
		var trtag = document.createElement("tr");
		trtag.className = "table_bottom";
		tbodytag.appendChild(trtag);
		
		//teamimage
		var imagetag = document.createElement("img");
		imagetag.src = baseball_data.getElementsByTagName("Image")[0].innerHTML;
		var tdnametag = document.createElement("td");
		tdnametag.innerHTML = "Image";
		var tdtexttag = document.createElement("td");
		tdtexttag.appendChild(imagetag);
		
		
		tbodytag.getElementsByTagName("tr")[1].appendChild(tdnametag);
		tbodytag.getElementsByTagName("tr")[2].appendChild(tdtexttag);
		
		var name = ["star","Coach","League"];
		for(var y = 0; y < name.length; y++){
			var tdnametag = document.createElement("td");
			tdnametag.innerHTML = name[y];
			var tdtexttag = document.createElement("td");
			if (y == 0){
				var innertable = document.createElement("table");
				innertable.className = "innertable";	
				innertable.border = "3";
				var innertbody = document.createElement("tbody");
				innertable.appendChild(innertbody);
				
				for(n = 0; n <baseball_data.getElementsByTagName(name[y])[0].children.length;n++){
					var tdtag = document.createElement("td");
					if(n==1){
						tdtag.innerHTML = "Birth : ";
					}
					tdtag.innerHTML += baseball_data.getElementsByTagName(name[y])[0].children[n].innerHTML;
					innertbody.appendChild(tdtag);
				}
				tdtexttag.appendChild(innertable);
			}else{
				tdtexttag.innerHTML = baseball_data.getElementsByTagName(name[y])[0].innerHTML;		
			}
			tbodytag.getElementsByTagName("tr")[1].appendChild(tdnametag);
			tbodytag.getElementsByTagName("tr")[2].appendChild(tdtexttag);
		
		}
		
		//vedio
		var trtag = document.createElement("tr");
		var tdtag = document.createElement("td");
		tdtag.colSpan = "4";
		var iframetag = document.createElement("iframe");
		iframetag.src = baseball_data.getElementsByTagName("Video")[0].innerHTML;
		
		//append vedio components
		tdtag.appendChild(iframetag);
		trtag.appendChild(tdtag);
		tbodytag.appendChild(trtag);
		tabletag.appendChild(tbodytag);
		//append table ;
		basebody.appendChild(tabletag);
	}
	
}
