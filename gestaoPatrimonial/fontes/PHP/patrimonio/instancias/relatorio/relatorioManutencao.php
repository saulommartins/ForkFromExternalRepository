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
    * Relatório Manutenção
    * Data de Criação   : 03/04/2003

    * @author Desenvolvedor  Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 18445 $
    $Name$
    $Autor: $
    $Date: 2006-12-01 13:55:29 -0200 (Sex, 01 Dez 2006) $

    * Casos de uso: uc-03.01.16
*/

/*
$Log$
Revision 1.28  2006/12/01 15:55:23  hboaventura
bug #7716#

Revision 1.27  2006/09/19 17:59:46  leandro.zis
Bug #6748#

Revision 1.26  2006/07/21 11:36:18  fernando
Inclusão do  Ajuda.

Revision 1.25  2006/07/13 20:47:47  fernando
Alteração de hints

Revision 1.24  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.23  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../relatorio.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';
setAjuda("UC-03.01.16");
$relatorio = new relatorio;

if (!(isset($ctrl))) {
    $ctrl=0;
}
if (isset($pagina)) {
    $ctrl=1;
}

switch ($ctrl) {

case 0:
    unset($sessao->transf2);
?>
<script>

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.dataInicial.value.length;
            if (campo == 0) {
            mensagem += "@O campo Data Inicial é obrigatório.";
            erro = true;
            }

        campo = document.frm.dataFinal.value.length;
            if (campo == 0) {
            mensagem += "@O campo Data Final é obrigatório.";
            erro = true;
            }

            if (!verificaData(document.frm.dataInicial)) {
              mensagem += "@O campo Data Inicial é inválida.";
              document.form.dataInicial.value = '';
              erro = true;
            }

            if (!verificaData(document.frm.dataFinal)) {
              mensagem += "@O campo Data Final é inválida.";
              erro = true;
            }

       if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id?>');
            return !(erro);
     }

</script>
    <form action="relatorioManutencao.php?<?=$sessao->id?>&ctrl=1" method="POST" name="frm" onsubmit="return Valida();" >
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="2">Informe os Dados para Consulta</td>
<?php
   geraCampoData2("*Data Inicial", "dataInicial", "01/03/2003", false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');this.value=''};\"","Informe a data inicial","Buscar data inicial" );
   geraCampoData2("*Data Final", "dataFinal", hoje(), false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');this.value='';};\"","Informe a data final","Buscar data final" );

?>

        <tr>
            <td class="label" title="Selecione a situação">Situação</td>
            <td class="field">
                <select name="situacao">
                    <option value="xxx">Todas</option>
                    <option value="realizada">Realizadas</option>
                    <option value="nao">Não Realizadas</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="field" colspan=2><input type="submit" name="enviar" value="OK" style="width: 60px">
            <input type="reset" value="Limpar" style="width: 60px;" tabindex="1" name="limpar"/></td>
        </tr>
        </table>
    </form>
<?php
break;

case 1:
        $ArrData     = explode("/", $dataInicial);
        $dataInicial = $ArrData[2] . "-" . $ArrData[1] . "-" . $ArrData[0];
        $ArrData     = explode("/", $dataFinal);
        $dataFinal   = $ArrData[2] . "-" . $ArrData[1] . "-" . $ArrData[0];

        if ($situacao == 'realizada') {
            $stFiltro = " AND manut.dt_realizacao IS NOT NULL";
        } elseif ($situacao == 'nao') {
            $stFiltro = " AND manut.dt_realizacao IS NULL";
        } elseif ($situacao == 'xxx') {
            $stFiltro = "";
        }
        /*$sqlEmp =" (SELECT
                      emp.vl_saldo_anterior, manut_paga.cod_bem,
                      manut_paga.dt_agendamento, manut_paga.cod_empenho
                   FROM
                      patrimonio.manutencao_paga as manut_paga,
                      empenho.empenho as emp
                   WHERE
                      emp.cod_entidade = manut_paga.cod_entidade AND
                      emp.exercicio = manut_paga.exercicio AND
                      emp.cod_empenho = manut_paga.cod_empenho) ";*/
        $sqlEmp =" (SELECT
                      emp.vl_saldo_anterior, manut_paga.cod_bem,
                      manut_paga.dt_agendamento, manut_paga.cod_empenho
                   FROM
                      patrimonio.manutencao_paga as manut_paga
                   LEFT JOIN
                      empenho.empenho as emp
                   ON
                      emp.exercicio = manut_paga.exercicio AND
                      emp.cod_empenho = manut_paga.cod_empenho AND
                      emp.cod_entidade = manut_paga.cod_entidade)";

//        $sqlPDF ="
//            SELECT
//                manut.cod_bem,
//                bem.descricao,
//                manut.observacao,
//                manut.dt_agendamento,
//                manut.dt_realizacao,
//                manut.dt_garantia,
//                manut.numcgm,
//--                manut_emp.vl_saldo_anterior as valor,
//--                manut_emp.cod_empenho,
//                manut_paga.valor,
//                manut_paga.cod_empenho,
//                bem.cod_natureza|| '.' ||bem.cod_grupo|| '.' ||bem.cod_especie|| '.' ||manut.cod_bem as codigo
//            FROM
//                patrimonio.bem             as bem,
//                patrimonio.manutencao      as manut,
//                patrimonio.manutencao_paga as manut_paga
//                left outer join ".$sqlEmp." as manut_emp on (
//                manut_paga.cod_bem         = manut_emp.cod_bem AND
//                manut_paga.dt_agendamento  = manut_emp.dt_agendamento)
//            WHERE
//                manut.cod_bem = manut_paga.cod_bem AND
//                manut.dt_agendamento = manut_paga.cod_bem AND
//                manut.cod_bem         = bem.cod_bem AND
//                manut.dt_agendamento >= '$dataInicial'     AND
//                manut.dt_agendamento <= '$dataFinal'".$stFiltro;
//echo("<pre><hr>");print_r($sqlPDF);echo("</pre><hr>");die();

        $sqlPDF ="
                    SELECT
                         manut.cod_bem
                        ,bem.descricao
                        ,manut.observacao
                        ,manut.dt_agendamento
                        ,manut.dt_realizacao
                        ,manut.dt_garantia
                        ,manut.numcgm
                        ,manut_emp.valor
                        ,manut_emp.cod_empenho
                        ,bem.cod_natureza|| '.' ||bem.cod_grupo|| '.' ||bem.cod_especie|| '.' ||manut.cod_bem as codigo
                    FROM
                         patrimonio.bem as bem
                        ,patrimonio.manutencao as manut
                        left join (select manut_paga.valor, manut_paga.cod_bem, manut_paga.dt_agendamento, empenho.cod_empenho from patrimonio.manutencao_paga as manut_paga
                        inner join  empenho.empenho on manut_paga.cod_entidade = empenho.cod_entidade AND manut_paga.cod_empenho = empenho.cod_empenho AND manut_paga.exercicio = empenho.exercicio  ) as manut_emp
                     ON
                            manut.cod_bem = manut_emp.cod_bem
                        AND manut.dt_agendamento = manut_emp.dt_agendamento
                    WHERE
                            manut.cod_bem         = bem.cod_bem
                        AND manut.dt_agendamento between '$dataInicial'     AND  '$dataFinal' ".$stFiltro;

        $botoesPdf = new botoesPdfLegado;
        $botoesPdf->imprimeBotoes( '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/relatorioManutencao.xml',$sqlPDF,'Relatório de Manutenção' );
break;
}
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
