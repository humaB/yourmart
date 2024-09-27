function formatPrice(price) {
    var string = parseFloat(price).toString();
    return string
      .replace(/,/g, "")
      .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
  };


  export { formatPrice };
