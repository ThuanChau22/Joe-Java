const navigateTo = (elementId) => {
  const element = document.getElementById(elementId);
  element.scrollIntoView({ behavior: "smooth" });
}

const submitForm = (elementId) => {
  document.getElementById(elementId).submit();
}

const api = ({ method = "GET", url = "", body = {} }) => {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.responseType = "json";
    xhr.open(method, url, true);
    xhr.setRequestHeader("Content-type", "application/json");
    xhr.send(JSON.stringify(body));
    xhr.onload = () => {
      if (xhr.status === 200) {
        resolve(xhr.response);
      }
      if (xhr.status >= 400) {
        reject(xhr.response);
      }
    }
  });
}