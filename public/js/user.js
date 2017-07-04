// window.onload = () => { $("#register").modal() }
$('body').ctshop({  
  currency:   '￥',
    paypal:  {
    currency_code:   'CNY'  
  }
})

const p = async() => {
  let data = new Promise((resovle, reject) => {
    $.get('/userApi/listFood', )
  })
}