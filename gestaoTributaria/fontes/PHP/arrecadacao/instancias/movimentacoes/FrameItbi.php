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

    * $Id: FrameItbi.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

Casos de uso: uc-05.00.00
*/

/*
$Log$
Revision 1.3  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/SessaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/Sessao.class.php';
Sessao::setTrataExcecao( false );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<script type="text/javascript">
      window.status = ":::::::: URBEM ::::::::";
</script>
<html>
<head>
    <title>:::::::: URBEM ::::::::</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<frameset rows="*,55,0" border="0" noresize >
    <frame name="telaPrincipal" src="../../../../../../gestaoTributaria/fontes/PHP/cadastroImobiliario/instancias/transferenciaPropriedade/FMManterTransferencia.php?<?=Sessao::getId();?>&boItbi=true&inInscricaoMunicipal=<?=$_REQUEST['inInscricaoMunicipal']?>" marginwidth="0" marginheight="0" scrolling="auto" frameborder="1" noresize >
    <frame name="telaMensagem" src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/index/menu.html" marginwidth="0" marginheight="0" scrolling="yes" frameborder="1" noresize >
    <frame id="oculto" name="oculto" src="" marginwidth="100%" marginheight="100%"      scrolling="yes" frameborder="1" noresize >
</frameset>
</html>
