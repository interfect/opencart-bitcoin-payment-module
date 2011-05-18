<form action="https://www.mybitcoin.com/sci/paypage.php?t=<?php echo $enc_token; ?>" method="post" id="checkout" name="btc_checkout">
</form>

<?php if (isset($main_currency)): ?>
<div class="buttons">
  <table>
    <tr>

     <td align="right">
	 	 <img src="<?php echo HTTPS_SERVER; ?>admin/view/image/payment/bitcoin.png" style="float:right; margin-left:10px; margin-right:3px;">
	 <div style="float:right;">
		<?php echo $main_currency; ?> <?php echo $text_mbc_exchange_rate; ?> <?php echo $btc_rate; ?><br />
		<?php echo $text_mbc_total_bitcoins; ?> <strong><?php echo $total_btc; ?></strong>
	 </div>

	 </td>
    </tr>
  </table>
</div>
<? endif; ?>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location = '<?php echo str_replace('&', '&amp;', $back); ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
     <td align="right"><a onclick="confirmSubmit();" class="button"><span><?php echo $button_confirm; ?></span></a></td>
    </tr>
  </table>
</div>
<script type="text/javascript"><!--
function confirmSubmit() {
	$.ajax({
		type: 'GET',
		url: 'index.php?route=payment/btc_mybitcoin/confirm',
		success: function() {
			$('#checkout').submit();
		}
	});
}
//--></script>