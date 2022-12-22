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

const startSpinner = (productId) => {
  const addButton = document.getElementById(`add-to-cart-${productId}`);
  if (addButton) {
    addButton.innerHTML = `<i class="fa fa-spinner fa-spin"></i>`;
  }
  const addIcon = document.getElementById(`add-to-cart-icon-${productId}`);
  if (addIcon) {
    addIcon.innerHTML = `<i class="fa fa-spinner fa-spin"></i>`;
  }
}

const stopSpinner = (productId) => {
  const addButton = document.getElementById(`add-to-cart-${productId}`);
  if (addButton) {
    addButton.innerHTML = "Added";
  }
  const addIcon = document.getElementById(`add-to-cart-icon-${productId}`);
  if (addIcon) {
    addIcon.innerHTML = "add_shopping_cart";
  }
}

const addToCart = async (e) => {
  e.preventDefault();
  try {
    const { add_to_cart, product_id } = e.target;
    startSpinner(product_id.value);
    const baseURL = "/src/api/cart.php";
    const body = {
      add_to_cart: add_to_cart.value,
      product_id: product_id.value,
    };
    await api({ method: "POST", url: baseURL, body });
    const quantity = await api({ url: `${baseURL}?quantity` });
    if (quantity) {
      let productCount = quantity.data;
      productCount = productCount >= 100 ? "99+" : productCount;
      document.getElementById("cart-product-count").innerHTML = productCount;
    }
    stopSpinner(product_id.value);
  } catch (error) {
    location.href = "/error";
  }
}
