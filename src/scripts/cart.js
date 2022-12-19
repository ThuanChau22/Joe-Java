const updateToCart = (element) => {
  const [update_to_cart, product_id, old_quantity, new_quantity] = element;
  const updateToCart = `${update_to_cart.name}=${update_to_cart.value}`;
  const productId = `${product_id.name}=${product_id.value}`;
  const oldQuantity = `${old_quantity.name}=${old_quantity.value}`;
  const newQuantity = `${new_quantity.name}=${new_quantity.value}`;
  const request = new XMLHttpRequest();
  request.onreadystatechange = () => {
    if (request.readyState == 4 && request.status == 200) {
      let { html, number_of_products } = JSON.parse(request.responseText);
      number_of_products = number_of_products >= 100 ? "99+" : number_of_products;
      document.getElementById("cart-product-count").innerHTML = number_of_products;
      document.getElementById("cart-content").innerHTML = html;
    }
  };
  request.open("POST", "/src/scripts/cart.php", true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(`${updateToCart}&${productId}&${oldQuantity}&${newQuantity}`);
  return false;
}

const removeFromCart = (element) => {
  const [remove_from_cart, product_id] = element;
  const removeFromCart = `${remove_from_cart.name}=${remove_from_cart.value}`;
  const productId = `${product_id.name}=${product_id.value}`;
  const request = new XMLHttpRequest();
  request.onreadystatechange = () => {
    if (request.readyState == 4 && request.status == 200) {
      let { html, number_of_products } = JSON.parse(request.responseText);
      number_of_products = number_of_products >= 100 ? "99+" : number_of_products;
      document.getElementById("cart-product-count").innerHTML = number_of_products;
      document.getElementById("cart-content").innerHTML = html;
    }
  };
  request.open("POST", "/src/scripts/cart.php", true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(`${removeFromCart}&${productId}`);
  return false;
}
