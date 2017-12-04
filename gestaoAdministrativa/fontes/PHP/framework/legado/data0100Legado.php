<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

include 'calendarioLegado.inc.php';

if (is_array($_REQUEST)) {
   while ( list( $key, $val ) = each( $_REQUEST ) ) {
      $variavel = $key;
      $$variavel = $val;
   }
}
if (is_array($_REQUEST)) {
   while ( list( $key, $val ) = each( $_REQUEST ) ) {
      $variavel = $key;
      $$variavel = $val;
   }
}
$noFrame = isset($noFrame) ? $noFrame : null;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
   <title>Calendário</title>
<script src="funcoesJs.js" type="text/javascript"></script>
<script type="text/javascript">

    function xMontaCSS()
    {
      var sLinha;
      var sNavegador = navigator.appName;
      if (sNavegador == "Microsoft Internet Explorer") {
          sLinha = "<link rel=\"STYLESHEET\" type=text/css href=\"stylos_ieLegado.css\">";
      } else {
          sLinha = "<link rel=\"STYLESHEET\" type=text/css href=\"stylos_ieLegado.css\">";
      }
      document.write(sLinha);
    }

    xMontaCSS();

   function MudaCalendario(iMes,iAno)
   {
     var sPag = "data0100Legado.php?sForm=<?=$sForm?>&sCampo=<?=$sCampo?>&iMes=" + iMes + "&iAno=" + iAno;
         document.location.replace(sPag);
   }

    function EncheCampo(sData)
    {
       <?php //Verifica se a página chamadora tem frames ou não
             if (isset($noFrame)) {
     ?>
        window.opener.parent.document.<?=$sForm?>.<?=$sCampo?>.value = sData;
       <?php } else { ?>

        window.opener.parent.frames['telaPrincipal'].document.<?=$sForm?>.<?=$sCampo?>.focus();
        window.opener.parent.frames['telaPrincipal'].document.<?=$sForm?>.<?=$sCampo?>.value = sData;
       <?php } ?>
       window.close();
    }
   </script>
</head>
<body onload="javascript:window.focus();">
<div align="center">
<?php
if ($iMes==0 or $iAno==0) {
   $iMes = (int) date("m",time());
   $iAno = (int) date("Y",time());
}
calendario($iMes,$iAno);
?>
</div>
</body>
</html>
