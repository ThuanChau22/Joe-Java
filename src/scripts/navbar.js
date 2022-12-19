const changeNavbarPadding = () => {
  const height = document.getElementById("navbar").offsetHeight;
  document.getElementById("navbar-padding").style.paddingTop = height + 'px';
};
window.addEventListener("load", changeNavbarPadding);
window.addEventListener("resize", changeNavbarPadding);
