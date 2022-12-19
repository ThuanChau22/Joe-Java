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

const addToCart = (element) => {
  const [add_to_cart, product_id] = element;
  const addToCart = `${add_to_cart.name}=${add_to_cart.value}`;
  const productId = `${product_id.name}=${product_id.value}`;
  const request = new XMLHttpRequest();
  request.onreadystatechange = () => {
    if (request.readyState == 4 && request.status == 200) {
      let { number_of_products } = JSON.parse(request.responseText);
      number_of_products = number_of_products >= 100 ? "99+" : number_of_products;
      document.getElementById("cart-product-count").innerHTML = number_of_products;
    }
  };
  request.open("POST", "/src/scripts/cart.php", true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(`${addToCart}&${productId}`);
  return false;
}
