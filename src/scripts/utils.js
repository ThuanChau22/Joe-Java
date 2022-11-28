const navigateTo = (elementId) => {
  const element = document.getElementById(elementId);
  element.scrollIntoView({ behavior: "smooth" });
}

const submitForm = (elementId) => {
  document.getElementById(elementId).submit();
}