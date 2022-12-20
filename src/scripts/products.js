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

const addToCart = async (e) => {
  e.preventDefault();
  try {
    const { add_to_cart, product_id } = e.target;
    const url = "/src/api/cart.php";
    const body = {
      add_to_cart: add_to_cart.value,
      product_id: product_id.value,
    };
    const response = await api({ method: "POST", url, body });
    if (response) {
      let productCount = response.number_of_products;
      productCount = productCount >= 100 ? "99+" : productCount;
      document.getElementById("cart-product-count").innerHTML = productCount;
    }
  } catch (error) {
    location.href = "/error";
  }
}
