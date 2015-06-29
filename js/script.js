/*=== lazyLoading ===*/
window.addEventListener('load', function(){
	var bLazy = new Blazy;
}, false);

/*=== Message close ===*/
document.getElementById('closeMessage').addEventListener('click', function(){
	document.getElementById('Message').style.display = 'none';
});