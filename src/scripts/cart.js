const updateToCart = async (e) => {
  e.preventDefault();
  try {
    const { update_to_cart, product_id, old_quantity, new_quantity } = e.target;
    const baseURL = "/src/api/cart.php";
    const body = {
      update_to_cart: update_to_cart.value,
      product_id: product_id.value,
      new_quantity: new_quantity.value,
      old_quantity: old_quantity.value,
    }
    await api({ method: "POST", url: baseURL, body });
    const [cart, quantity] = await Promise.all([
      api({ url: `${baseURL}?html` }),
      api({ url: `${baseURL}?quantity` }),
    ]);
    if (cart) {
      document.getElementById("cart-content").innerHTML = cart.data;
    }
    if (quantity) {
      let productCount = quantity.data;
      productCount = productCount >= 100 ? "99+" : productCount;
      document.getElementById("cart-product-count").innerHTML = productCount;
    }
  } catch (error) {
    location.href = "/error";
  }
}

const removeFromCart = async (e) => {
  e.preventDefault();
  try {
    const { remove_from_cart, product_id } = e.target;
    const baseURL = "/src/api/cart.php";
    const body = {
      remove_from_cart: remove_from_cart.value,
      product_id: product_id.value,
    }
    await api({ method: "POST", url: baseURL, body });
    const [cart, quantity] = await Promise.all([
      api({ url: `${baseURL}?html` }),
      api({ url: `${baseURL}?quantity` }),
    ]);
    if (cart) {
      document.getElementById("cart-content").innerHTML = cart.data;
    }
    if (quantity) {
      let productCount = quantity.data;
      productCount = productCount >= 100 ? "99+" : productCount;
      document.getElementById("cart-product-count").innerHTML = productCount;
    }
  } catch (error) {
    location.href = "/error";
  }
}
