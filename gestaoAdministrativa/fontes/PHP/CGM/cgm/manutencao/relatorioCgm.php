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
* Arquivo de instância para manutenção de CGM
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 24717 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:48:01 -0300 (Seg, 13 Ago 2007) $

* Casos de uso: uc-01.02.92, uc-01.02.93
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php");
setAjuda('uc-01.02.93')
?>

<script type="text/javascript">
            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
                return !(erro);
            }
            function Salvar()
            {
                if (Valida()) {
                    document.frm.submit();
                }
            }
</script>

<form action="relatorioCgmMostra.php?<?=Sessao::getId();?>&<?print "iAgora=".agora(true,true);?>" method="POST" name="frm">
  <table width="100%">
<tr>
    <td class="alt_dados" colspan=2>Dados para filtro</td>
</tr>
<tr>
    <td class="label" width="30%" title="Selecione o(s) tipo(s) de CGM que deseja listar.">Tipo</td>
    <td class="field">
        <input type="checkbox" name="sTipoFis" value="1">&nbsp;Pessoa Física<br>
    <input type="checkbox" name="sTipoJur" value="2">&nbsp;Pessoa Jurídica<br>
    <input type="checkbox" name="sTipoInt" value="4">&nbsp;CGM Interno
    </td>
</tr>
<tr>
    <td class="label" title="Informe o período de inclusão do CGM.">Período</td>
    <td class=field>
        <?php geraCampoData("sDataIni", '', false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');};\"" );?>
         a
         <?php geraCampoData("sDataFim", '', false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');};\"" );?>
    </td>
</tr>
<tr>
    <td class="label" title="Selecione o campo que deseja usar para ordernar a lista.">Ordenar por</td>
    <td class=field>
         <select name="sOrderBy">
             <option value="numcgm" SELECTED>CGM</option>
             <option value="nom_cgm">Nome</option>
         </select>
    </td>
</tr>
<tr>
<tr>
    <td class=label>Mostrar Endereço no Relatório</td><td class=field>
        <input type="radio" name="endereco" value="S">&nbsp;Sim
        <input type="radio" name="endereco" value="N" checked="checked">&nbsp;Não<br></td>
</tr>
    <td class="field" colspan="2">
       <?php geraBotaoOk(1,1,0,0);?>
    </td>
</tr>
</tr>
</table>
</form>
<?php include("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php"); ?>
