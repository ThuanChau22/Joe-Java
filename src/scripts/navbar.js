window.onload = window.onresize = () => {
  const height = document.getElementById("navbar").offsetHeight;
  document.getElementById("navbar-padding").style.paddingTop = height + 'px';
};