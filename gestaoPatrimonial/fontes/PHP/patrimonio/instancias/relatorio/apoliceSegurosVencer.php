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
    * Apolice Seguros
    * Data de Criação   : 03/04/2003

    * @author Desenvolvedor Leonardo Tremper

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.15
*/

/*
$Log$
Revision 1.20  2006/07/21 11:36:18  fernando
Inclusão do  Ajuda.

Revision 1.19  2006/07/13 20:46:11  fernando
Alteração de hints

Revision 1.18  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.17  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.15");
if (!(isset($ctrl))) {
    $ctrl = 0;
}

if (isset($pagina)) {
    $ctrl = 1;
}

switch ($ctrl) {

case 0:
?>
    <script type="text/javascript">

        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;
            campo = document.frm.datainicial.value.length;
            if (campo == 0) {
                mensagem += "@Data inicial é obrigatória.";
                erro = true;
            }
            campo = document.frm.datafinal.value.length;
            if (campo == 0) {
                mensagem += "@Data Final é obrigatória.";
                erro = true;
            }

            if (!verificaData(document.frm.datainicial)) {
              mensagem += "@O campo Data Inicial é inválida.";
              erro = true;
            }

            if (!verificaData(document.frm.datafinal)) {
              mensagem += "@O campo Data Final é inválida.";
              erro = true;
            }

            if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id?>');
            return !(erro);
        }

        function Salvar()
        {
            if (Valida()) {
                document.frm.submit();
            }
        }
    </script>

    <form name="frm" action="apoliceSegurosVencer.php?<?=$sessao->id?>" method="POST">
    <table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">Selecione as Datas para Procura</td>
    </tr>
<?php
   geraCampoData2("*Data Inicial", "datainicial", $datainicial, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"","Informe  data inicial","Buscar data inicial" );
   geraCampoDataHidden("*Data Final", "datafinal", $datafinal, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"","Informe a data final","Buscar data final" );
?>
    <tr>
    <td class="label" title="Selecione a ordenação do relatório.">Ordenar Por</td>
        <td class="field">
            <select name="orderby">
                <option value="a.num_apolice" SELECTED>Apólice</option>
                <option value="c.nom_cgm">Seguradora</option>
                <option value="a.cod_apolice">Código da Apólice</option>
                <option value="a.dt_vencimento">Data de Vencimento</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2"><input type="button" value="OK" style="width: 60px" onclick="Salvar();">
        <input type="reset" value="Limpar" style="width: 60px;" tabindex="1" name="limpar"/></td>
    </tr>

    </table>
    </form>
<?php
break;

case 1:
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';

    if (isset($datainicial)) {

        $dtInicial = dataToSql($datainicial);
        $dtFinal = dataToSql($datafinal);
        if ($dtFinal < $dtInicial) {
?>
            <script type="text/javascript">
                alertaMensagem("A Data Inicial deve ser menor que a data final","unica","aviso","<?=$sessao->id;?>");
                window.location = "apoliceSegurosVencer.php?<?=$sessao->id;?>";
            </script>
<?php
        } else {
            $sSQLs = "SELECT DISTINCT
                        a.cod_apolice, c.nom_cgm, a.num_apolice, a.dt_vencimento
                    FROM
                        patrimonio.apolice as a, sw_cgm as c
                    WHERE
                        dt_vencimento BETWEEN '".$dtInicial."' AND '".$dtFinal."'
                    AND
                        a.numcgm = c.numcgm";
            $sessao->transf = "";
            $sessao->transf = $sSQLs;
            $sessao->transf2 = $orderby;
        }
    }

    $botoesPDF = new botoesPdfLegado;
    $sqlPDF = $sessao->transf;
    $sqlPDF .= " ORDER BY $sessao->transf2 DESC";
    $botoesPDF->imprimeBotoes( '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/apoliceSegurosVencer.xml' , $sqlPDF , '' , '' );
break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
