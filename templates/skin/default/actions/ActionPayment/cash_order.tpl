{include file='header.tpl'}
<h2 class="payment-title mb-30">{$aLang.plugin.minimarket.action_payment_init_title_order}</h2>
{$aLang.plugin.minimarket.action_payment_init_text_cash_order|replace:'%%order_number%%':$oPayment->getObjectPaymentId()}
{include file='footer.tpl'}