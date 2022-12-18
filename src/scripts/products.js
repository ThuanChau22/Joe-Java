const hoverOverLongProductName = () => {
  const className = "products-card-name-content";
  const productCardNames = document.getElementsByClassName(className);
  const canvasContext = document.createElement("canvas").getContext("2d");
  for (const productName of productCardNames) {
    const elementWidth = productName.clientWidth;
    const textWidth = canvasContext.measureText(productName.innerHTML).width;
    const x = elementWidth - textWidth;
    if (x < 0) {
      productName.onmouseenter = () => {
        productName.style.transform = `translateX(${x}px)`;
      }
      productName.onmouseleave = () => {
        productName.style.transform = `translateX(0px)`;
      }
    } else {
      productName.onmouseenter = undefined;
      productName.onmouseleave = undefined;
    }
  }
};
window.addEventListener("load", hoverOverLongProductName);
window.addEventListener("resize", hoverOverLongProductName);

const addProductToCart = (element) => {
  const [add_to_cart, product_id] = element;
  const input1 = `${add_to_cart.name}=${add_to_cart.value}`;
  const input2 = `${product_id.name}=${product_id.value}`;
  const xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = () => {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      const navBarItemCount = document.getElementById("cart-item-count");
      navBarItemCount.innerHTML = xhttp.responseText;
    }
  };
  xhttp.open("POST", "/src/scripts/products.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send(`${input1}&${input2}`);
  return false;
}