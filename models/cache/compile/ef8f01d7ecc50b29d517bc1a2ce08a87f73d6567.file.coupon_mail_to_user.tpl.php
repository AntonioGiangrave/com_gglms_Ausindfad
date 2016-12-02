<?php /* Smarty version Smarty-3.1.5, created on 2016-12-02 16:56:31
         compiled from "components/com_gglms/models/templates/coupon_mail_to_user.tpl" */ ?>
<?php /*%%SmartyHeaderCode:319805841842013db82-92704966%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ef8f01d7ecc50b29d517bc1a2ce08a87f73d6567' => 
    array (
      0 => 'components/com_gglms/models/templates/coupon_mail_to_user.tpl',
      1 => 1480694183,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '319805841842013db82-92704966',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_584184202a6be',
  'variables' => 
  array (
    'ausind' => 0,
    'coursename' => 0,
    'coupon' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_584184202a6be')) {function content_584184202a6be($_smarty_tpl) {?>
<html>
<head>
    <title>Coupon</title>
</head>
<body>
<h1>Coupon formazione</h1>

<p>Spett.le <?php echo $_smarty_tpl->tpl_vars['ausind']->value['receiver_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['ausind']->value['receiver_surname'];?>
,</p>
<p>
    <?php echo $_smarty_tpl->tpl_vars['ausind']->value['associazione_name'];?>
 ha provveduto a fornirle un coupon per usufruire di un corso di e-learning.
</p>
<p>
    Per usufruirne deve prima registrarsi sul portale <a href="<?php echo $_smarty_tpl->tpl_vars['ausind']->value['associazione_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['ausind']->value['associazione_name'];?>
</a>:
    <br />
    Le credenziali così create saranno valide anche per gli eventuali futuri corsi e-learning.
    <br />
    Una volta registrato e loggato sul portale, inserisca il coupon seguente nel form che troverà sotto la voce "coupon" del menu principale.
</p>
Ecco il <?php echo $_smarty_tpl->tpl_vars['ausind']->value['coupon_number'];?>
 coupon a Lei riservato.
</p>

<h3><?php echo $_smarty_tpl->tpl_vars['coursename']->value;?>
</h3>

<div style="font-family: monospace; font-size: 24px;">
        <b><?php echo $_smarty_tpl->tpl_vars['coupon']->value;?>
</b>
</div>

<p>
    <b>Per una migliore fruizione del corso consigliamo fortemente di usare browser quali Firefox (versione 4 o superiore), Google Chrome (versione 6 o superiore), Explorer (dalla versione 9)</b>
<p>

<p>
    Cordiali saluti<br />
    Lo staff <?php echo $_smarty_tpl->tpl_vars['ausind']->value['associazione_name'];?>

</p>
<p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

</body>
</html>
<?php }} ?>