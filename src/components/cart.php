<?php
require_once("../utils/utils.php");

function cart($cart)
{
  function productList($cart)
  {
    $productList = "";
    foreach ($cart as $product) {
      $productId = $product["id"];
      $productImage = $product["image"];
      $productName = $product["name"];
      $productPrice = $product["price"];
      $productQuantity = $product["quantity"];
      $productList .= <<<HTML
      <div class="card py-1 mb-2">
        <div class="row g-0">
          <div class="col-lg-2 col-md-4 col-sm-3 col-4">
          <a href=/products/$productId>
            <img class="img-fluid rounded-start" src=$productImage>
          </a>
          </div>
          <div class="col-lg-10 col-md-8 col-sm-9 col-8 d-flex flex-column">
            <a class="text-decoration-none" href=/products/$productId>
              <p class="cart-product-title my-1">$productName</p>
            </a>
            <div class="row g-0 mt-auto">
              <div class="col-lg-3 col-md-6 col-sm-5 col-8">
                <form class="d-inline" method="post" action="cart" onsubmit="updateToCart(event)">
                  <input type="hidden" name="product_id" value="$productId">
                  <input type="hidden" name="old_quantity" value="$productQuantity">
                  <input class="cart-product-input form-control w-50 py-1" type="text" autocomplete="off" maxlength="3" name="new_quantity" value="$productQuantity">
                  <input class="cart-product-btn btn btn-link px-1 pt-0" name="update_to_cart" type="submit" value="Update">
                </form>
                <form class="d-inline" method="post" action="cart" onsubmit="removeFromCart(event)">
                  <input type="hidden" name="product_id" value="$productId">
                  <input class="cart-product-btn btn btn-link px-1 pt-0" name="remove_from_cart" type="submit" value="Remove">
                </form>
              </div>
              <div class="col-md-9 col-md-6 col-sm-7 col-4">
                <div class="cart-product-price">$$productPrice</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      HTML;
    }
    return <<<HTML
    $productList
    HTML;
  }
  $productList = productList($cart);

  function orderSummary($cart)
  {
    $productList = "";
    $total = 0;
    foreach ($cart as $product) {
      $productName = $product["name"];
      $productQuantity = $product["quantity"];
      $productCost = $product["price"] * $productQuantity;
      $productList .= <<<HTML
      <li class="cart-summary-product list-group-item px-2">
        <div class="row m-0">
          <div class="col-9 p-0">
            <p><b>$productName</b>(x$productQuantity)</p>
          </div>
          <div class="col-3 p-0">
            <p class="float-end"><i>$$productCost</i></p>
          </div>
        </div>
      </li>
      HTML;
      $total += $productCost;
    }
    $total = number_format($total, 2);
    return <<<HTML
    <div class="card">
      <div class="card-header">
        <p class="cart-summary-title">Order Summary</p>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush">$productList</ul>
        <hr>
        <span class="cart-summary-total-label">Total:</span>
        <span class="cart-summary-total-cost">$$total</span>
        <form class="text-center pt-4" method="post" action="cart">
          <input class="cart-checkout-btn" name="checkout" type="submit" value="Checkout">
        </form>
      </div>
    </div>
    HTML;
  }
  $orderSummary = orderSummary($cart);

  return count($cart) > 0
    ? <<<HTML
    <div class="row">
      <div class="col-lg-8 col-md-7 mb-3">
        $productList
      </div>
      <div class="col-lg-4 col-md-5">
        $orderSummary
      </div>
    </div>
    HTML
    : <<<HTML
    <div class="cart-empty">
      <p >Your Cart is empty</p>
    </div>
    HTML;
}
