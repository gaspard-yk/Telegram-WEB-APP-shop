const fetch = import('node-fetch');
const sqlite3 = require('sqlite3').verbose();
const { open } = require('sqlite');
const moment = require('moment');
require('dotenv').config();


console.log('Server running');


const { Telegraf, Markup } = require('telegraf');
const bot = new Telegraf(process.env.bot_token); //сюда помещается токен, который дал botFather
bot.use(Telegraf.log())
const keyboard = Markup.inlineKeyboard([
  Markup.button.webApp('Biq Zur Store', process.env.bot_url),
]);
// const keyboard = Markup.keyboard([
//   Markup.button.webApp('Biq Zur Store', process.env.bot_url),
// ]).resize();

// bot.use(Telegraf.log())
const urlbutton = {//меню бота 
  type: "web_app",
  text: "Магазин",
  web_app: {
    url: process.env.bot_url
  }
};
bot.telegram.setChatMenuButton({ menuButton: urlbutton });

// async function db_work(id,send,itn){
// let invoice_id = await createinvoiveid(id);
// console.log(invoice_id)
// console.log(typeof invoice_id)
// insert_db(invoice_id,send,itn)
// return invoice_id;
// }


// async function createinvoiveid(id){
// const  db = await open({
//   filename: 'test.db',
// driver: sqlite3.Database
// })
// console.log(id)
// id=String(id)
// result = await db.get(`SELECT COUNT() FROM invoices WHERE id LIKE '${id}%'`)
// invoice_id=result['COUNT()'];
// console.log(invoice_id)

//   if (invoice_id==0){
//     invoice_id=id+'0001';
//   }
//   else if(invoice_id<9){
//     invoice_id=id+'000' + (invoice_id+1);
//   }
//   else if(invoice_id<99){//rows['COUNT()']>=10 && 
//     invoice_id=id+'00' + (invoice_id+1);
//   }
//   else if(invoice_id<999){//rows['COUNT()']>=100 && 
//     invoice_id=id+'0' + (invoice_id+1);
//   }
//   else{
//     invoice_id=id + (invoice_id+1);
//   }
//   db.close();
//   return invoice_id;
// }


// async function insert_db(invoice_id,send,itn) {
//   const  db = await open({
//     filename: 'test.db',
//   driver: sqlite3.Database
//   })
//   // var formattedDate = moment().format('YYYYMMDD');
// const today = new Date();
// let YYYY = today.getFullYear();
// let MM = today.getMonth() + 1; // Months start at 0!
// if (MM < 10) {MM = '0' + MM};
// let DD = today.getDate();
// if (DD < 10) {DD = '0' + DD};
// let hh = today.getHours();
// if (hh < 10) {hh = '0' + hh};
// let mm = today.getUTCMinutes();
// if (mm < 10) {mm = '0' + mm};
// let ss = today.getSeconds();
// if (ss < 10) {ss = '0' + ss};
// let formattedToday = YYYY + '-' + MM + '-' + DD + 'T' + hh + ':' + mm + ':' + ss;
//   await db.exec(`INSERT INTO invoices (id,goods,itn,date) VALUES ('${invoice_id}','${send}',${itn},'${formattedToday}')`)//,'${date}'
//   // insert=`INSERT INTO invoices VALUES (${invoice_id},${send},${itn}`;
//   // db.run(insert)
//   db.close();
// }


// async function check_db(invoice_id){
//   const  db = await open({
//     filename: 'test.db',
//   driver: sqlite3.Database
//   })
//   // result = await db.get(`SELECT price FROM invoices WHERE id = '${id}'`)
//   console.log(id)

//   price = await db.get(`SELECT goods FROM invoices WHERE id = '${invoice_id}'`)
//   if(price===undefined){
//       result=[false,'Заказ не найден, возможен сбой в системе']
//       return result;
//   }
//   invoice_price = JSON.parse(price.goods);
//   console.log(invoice_price);

//   for(i=0;i<invoice_price.length;i++){
//     check_price = await db.get(`SELECT p_name,p_price,p_quantity FROM products WHERE p_code = '${invoice_price[i]['code']}'`)
//     console.log(check_price)

//     if(invoice_price[i]['count']>check_price['p_quantity']){
//       return [false,`Товар ${check_price['p_name']} отсутствует в количестве ${invoice_price[i]['count']} шт.`]
//     }
//     else if(invoice_price[i]['cost']!=check_price['p_price']*invoice_price[i]['count']*100){
//       return [false,`Цена товара ${check_price['p_name']} изменилась`]
//     }
//   }
//   return [true,'']
// // console.log(result)
// // return result;
// }


// function getInvoice (id,arr_price,invoice_id) {
// const  invoice = {
//     chat_id: id, // Уникальный идентификатор целевого чата или имя пользователя целевого канала
//     provider_token: '1744374395:TEST:84cb62c5d7529d1aac1b',
//     // provider_token: '401643678:TEST:5fef0975-0446-4a13-9afe-ee739ba7bbdf', // токен выданный через бот @SberbankPaymentBot 
//     start_parameter: 'get_access', //Уникальный параметр глубинных ссылок. Если оставить поле пустым, переадресованные копии отправленного сообщения будут иметь кнопку «Оплатить», позволяющую нескольким пользователям производить оплату непосредственно из пересылаемого сообщения, используя один и тот же счет. Если не пусто, перенаправленные копии отправленного сообщения будут иметь кнопку URL с глубокой ссылкой на бота (вместо кнопки оплаты) со значением, используемым в качестве начального параметра.

//     title: 'Заказ №'+ invoice_id, // Название продукта, 1-32 символа
//     description: 'Any_Description', // Описание продукта, 1-255 знаков
//     currency: 'RUB', // Трехбуквенный код валюты ISO 4217
//     prices: arr_price,//[{ label: 'Invoice Title', amount: 100 * 100 }], // Разбивка цен, сериализованный список компонентов в формате JSON 100 копеек * 100 = 100 рублей
//     payload: { 
//       // Полезные данные счета-фактуры, определенные ботом, 1–128 байт. Это не будет отображаться пользователю, используйте его для своих внутренних процессов.
//       invoice_id: invoice_id,
//       // provider_token: '401643678:TEST:5fef0975-0446-4a13-9afe-ee739ba7bbdf',
//       // array:send//массив в строку переделать !!!!!!!!!!!!!!!!

//     },
//     need_email: true,
//     send_email_to_provider:true 
//    }
//   return invoice
// }


async function post_request(invoice_id) {//,email
  // import fetch from "node-fetch";
  const db = await open({
    filename: '../test.db',
    driver: sqlite3.Database
  })
  // await db.exec(`UPDATE invoices SET email ='${email}' WHERE id = '${invoice_id}'`)
  price = await db.get(`SELECT * FROM invoices WHERE id = '${invoice_id}'`)
  // price['telegram']=true;
  // price.goods=JSON.parse(price.goods)

  // for(i=0;i<price.goods.length;i++){
  //   quantity = await db.get(`SELECT p_quantity FROM products WHERE p_code = '${price.goods[i].code}'`) 
  //   quantity.p_quantity = quantity.p_quantity-price.goods[i].count
  //   await db.exec(`UPDATE products SET p_quantity ='${quantity.p_quantity}' WHERE p_code = '${price.goods[i].code}'`)
  // }
  // console.log(price)
  arr_price = [];
  arr_price[0] = price;
  // console.log(JSON.stringify(arr_price))
  // try{


  let response = await fetch(`${process.env.bot_send_url}`, { //itaurum.ru:8443 //dbs
    method: 'POST',
    headers: {
      'Authorization': 'Basic ' + btoa(`${process.env.bot_username}` + `:` + `${process.env.bot_password}`),//encodeURI 
      'Content-Type': 'application/json;'//;charset=utf-8
    },
    body: JSON.stringify(arr_price),
    mode: 'cors',// mode:"no-cors",
    //credentials: "include",
    cache: "default",
  });

  // let data =  response.json();
  // console.log(data)
  // return data;

  if (response.ok) {

    // let text = await res.text();
    // return text;

    let ret = await response.json();
    return ret;

  } else {
    console.log(response.status)
    return `HTTP error: ${response.status}`;
  }
  // }
  // catch(err){
  // return(err);
  // }
}


// bot.on('pre_checkout_query', async (ctx) => {
//   id = JSON.parse(ctx.preCheckoutQuery.invoice_payload).invoice_id;
//   result = await check_db(id)
//   if (result[0]==true){
//     ctx.answerPreCheckoutQuery(true)
//   }
//   else{
//     ctx.answerPreCheckoutQuery(false,result[1])
//   }
// }) // ответ на предварительный запрос по оплате


// bot.on('successful_payment', async (ctx, next) => { // ответ в случае положительной оплаты
//   invoice_id = JSON.parse(ctx.SuccessfulPayment.invoice_payload).invoice_id;
//   email=ctx.SuccessfulPayment.order_info.email;
//   result = await post_request(invoice_id,email)
//   console.log(result);

//   msg='Successful Payment\n'
//   for (i=0;i<result.length;i++){
//     msg+=result[i].code+' '+result[i].sn+'\n'
//   }
//   ctx.reply(msg)
//   // result = await post_request('21294816870005','uuu@mail.ru')
//   // console.log(result);
//   // result=JSON.parse(result);
//   // await ctx.reply('SuccessfulPayment. We send code to your email')
// })

bot.start((ctx) => {
  ctx.reply('Приветсвуем вас в магазине Biq Zur Store' +
    "\nЧтобы увидеть все команды бота отправьте команду /help", keyboard)
});
bot.help((ctx) => ctx.reply('Вот мой список команд:' +
  '\n /store- check my store' +
  '\n /my_invoices- посмотреть мои заказы' +
  '\n /activation получить инструкцию для активации'));


bot.hears('/my_invoices', async (ctx) => {
  let mi_keyboard = [];
  const db = await open({
    filename: '../test.db',
    driver: sqlite3.Database
  })
  invoices = await db.each(`SELECT id FROM invoices WHERE user_id = '${ctx.from.id}' AND payment_status='0' `, (error, row) => {// AND payment_status='successfully_paid'
    /*gets called for every row our query returns*/
    mi_keyboard.push([{ text: `${row.id}`, callback_data: `{"id":"${row.id}"}` }])//id:'${row.id}'
  });
  if (mi_keyboard.length > 0) {
    ctx.reply(`Ваши заказы`, {
      reply_markup: {
        inline_keyboard: mi_keyboard
      }
    })
  }
  else {
    ctx.reply(`У вас нет оплаченных заказов`)
  }
  db.close()
});

bot.hears('/activation', async (ctx) => {
  ctx.reply(`Выберите оператора для активации`, {
    reply_markup: {
      inline_keyboard: [[{ text: `Оператор 1`, callback_data: `{"operator":"operator 1"}` }],
      [{ text: `Оператор 2`, callback_data: `{"operator":"operator 2"}` }],
      [{ text: `Оператор 3`, callback_data: `{"operator":"operator 3"}` }]]
    }
  })
});

bot.on('callback_query', async (ctx) => {
  let json = JSON.parse(ctx.callbackQuery.data);
  if ('id' in json) {
    ctx.answerCbQuery();//id, `aaaaaaa ${ctx.data}`
    result = await post_request(json.id)//,email
    msg = `Заказа № ${json.id}\n`
    for (i = 0; i < result.length; i++) {
      msg += result[i].code + ' ' + result[i].sn + '\n'
    }
    ctx.reply(msg)
    return
  }



  if ('operator' in json) {
    ctx.answerCbQuery();
    ctx.reply(`Инструкция по активации от оператора ${json.operator} `)
    return
  }
})


// bot.hears('i', async (ctx) => {
//   ctx.reply('msg')
//   result = await post_request('21294816870008','uuu@mail.ru')
//   console.log(result);
//   msg='Successful Payment\n'
//   for (i=0;i<result.length;i++){
//     msg+=result[i].code+' '+result[i].sn+'\n'
//   }

//   ctx.reply(msg)
// });
bot.hears('/store', (ctx) => {
  ctx.reply('my store', keyboard)
});

// bot.hears('/privacy_policy', (ctx) => {
//   ctx.replyWithDocument({source:'Соглашение_об_обработке_персональных_данных.pdf'});
// });

bot.on('web_app_data', async (ctx) => {
  ctx.reply('Спасибо за покупку. Тут будут коды активации:')
  // var arr = JSON.parse(ctx.message.web_app_data.data);
  // arr_price=[];
  // for(i=0;i<arr.length-1;i++){
  //   arr_price.push({})
  //   arr_price[i].label=arr[i]['name'];
  //   arr_price[i].amount=arr[i]['count']*arr[i]['cost']*100;
  //   delete arr[i]['name'];
  //   arr[i]['cost']=arr_price[i].amount;
  // }
  // itn = arr[arr.length-1]
  // arr.pop();
  // send = JSON.stringify(arr)
  // invoice_id= await db_work(ctx.from.id,send,itn);

  // ctx.replyWithInvoice(getInvoice(ctx.from.id,arr_price,invoice_id)) //  метод replyWithInvoice для выставления счета 
  // // ctx.reply(ctx.message.web_app_data.data);

  // console.log(getInvoice(ctx.from.id,arr_price,invoice_id));
  console.log(ctx.message.web_app_data);
})

bot.launch(
  {
    webhook: {
      domain: process.env.bot_url + ':8443',
      port: 8080,
      //tlsOptions: tlsOptions,
      hookPath: "/",
      maxConnections: 100,
    }
  }
)
