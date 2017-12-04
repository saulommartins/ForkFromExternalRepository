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
* Página de Frames da Consultar Registros de Eventos.
* Data de Criação: 28/09/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Vandré Miguel Ramos

* @ignore

$Revision: 32866 $
$Name$
$Author: rgarbin $
$Date: 2008-01-28 12:57:53 -0200 (Seg, 28 Jan 2008) $

   * Casos de uso: uc-04.05.09
*/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<script type="text/javascript">
      window.status = ":::::::: URBEM ::::::::";
</script>
<html>
<head>
    <title>URBEM :: Consultar Registros de Eventos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<frameset rows="*,0" border="0" noresize >
    <?php include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php"     ); 
    // Foram colocados as variaveis CodMes e InAno para poder ser usadas como a competência na tela seguinte, pois antes possuía uma
    // referência para uma Sessão inexistente e não há necessidade de criar uma sessão para ambas variavéis, apenas passar por request
    ?>
    <frame name="telaPrincipal" src="./<?=$request->get("sUrlConsulta")."&iURLRandomica=".$request->get("iURLRandomica")."&inCodContrato=".$request->get("inCodContrato")."&inRegistro=".$request->get("inRegistro")."&inCodConfiguracao=".$request->get('inCodConfiguracao')."&nom_cgm=".$request->get("nom_cgm")."&numcgm=".$request->get("numcgm")."&inCodComplementar=".$request->get("inCodComplementar")."&inCodPeriodoMovimentacao=".$request->get("inCodPeriodoMovimentacao")."&inCodMes=".$request->get("inCodMes")."&inAno=".$request->get("inAno")?>" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0" noresize >
    <frame name="oculto" id="oculto" src="" marginwidth="100%" marginheight="100%" scrolling="yes" frameborder="1" noresize >
</frameset>
</html>
