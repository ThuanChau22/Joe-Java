const updateToCart = async (e) => {
  e.preventDefault();
  try {
    const { update_to_cart, product_id, old_quantity, new_quantity } = e.target;
    const url = "/src/api/cart.php";
    const body = {
      update_to_cart: update_to_cart.value,
      product_id: product_id.value,
      new_quantity: new_quantity.value,
      old_quantity: old_quantity.value,
    }
    const response = await api({ method: "POST", url, body });
    if (response) {
      let productCount = response.number_of_products;
      productCount = productCount >= 100 ? "99+" : productCount;
      document.getElementById("cart-product-count").innerHTML = productCount;
      document.getElementById("cart-content").innerHTML = response.html;
    }
  } catch (error) {
    location.href = "/error";
  }
}

const removeFromCart = async (e) => {
  e.preventDefault();
  try {
    const { remove_from_cart, product_id } = e.target;
    const url = "/src/api/cart.php";
    const body = {
      remove_from_cart: remove_from_cart.value,
      product_id: product_id.value,
    }
    const response = await api({ method: "POST", url, body });
    if (response) {
      let productCount = response.number_of_products;
      productCount = productCount >= 100 ? "99+" : productCount;
      document.getElementById("cart-product-count").innerHTML = productCount;
      document.getElementById("cart-content").innerHTML = response.html;
    }
  } catch (error) {
    location.href = "/error";
  }
}
