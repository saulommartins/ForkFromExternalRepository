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

    * Casos de uso: uc-03.01.14
*/

/*
$Log$
Revision 1.16  2006/07/21 11:36:18  fernando
Inclusão do  Ajuda.

Revision 1.15  2006/07/13 20:45:55  fernando
Alteração de hints

Revision 1.14  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.13  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../apolice.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';
setAjuda("UC-03.01.14");
if (!(isset($ctrl))) {
    $ctrl = 0;
}

switch ($ctrl) {
    case 0:
?>
    <script type="text/javascript">
        function Salvar()
        {
            document.frm.submit();
        }
    </script>

    <form name="frm" action="apoliceSeguros.php?<?=$sessao->id?>" method="POST">
        <input type="hidden" name="ctrl" value="1">
    <table width="100%">

    <tr>
        <td class="alt_dados" colspan="2">Dados para Apólice</td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Informe o código da apólice.">Código da Apólice</td>
        <td class="field"><input type="text" name="codApolice" size="20" maxlength="20" onKeyPress="return (isValido(this, event, '0123456789'));" ></td>
    </tr>

    <tr>
        <td class="label" title="Informe o número da apólice.">Número da Apólice</td>
        <td class="field"><input type="text" name="numApolice" size="20" maxlength="20"   ></td>
    </tr>

    <tr>
        <td class="label" title="Selecione a seguradora.">Seguradora</td>
        <td class="field">
<?php

        $bemsegurado = new apolice;
        $bemsegurado->listaComboSeguradoras();
        $bemsegurado->mostraComboSeguradoras();
?>
        </td>
    </tr>

    <tr>
        <td class="label" title="Selecione a ordenação do relatório.">Ordenar Por</td>
        <td class="field">
            <select name="orderby">
                <option value="a.num_apolice" SELECTED>Apólice</option>
                <option value="c.nom_cgm">Seguradora</option>
                <option value="a.cod_apolice">Código da Apólice</option>
                <option value="a.dt_vencimento">Data de Vencimento</option>
                <option value="ab.cod_bem">Código do Bem</option>
                <option value="e.nom_especie">Espécie</option>
                <option value="b.descricao">Descrição</option>
            </select>
        </td>
    </tr>

    <tr>
        <td class="field" colspan="2">
        <?=geraBotaoOk();?>
        </td>
    </tr>

    </table>
    </form>
<?php
    break;

    case 1:
        $sSQLs = "
                SELECT
                    ab.cod_bem,
                    e.nom_especie,
                    bae.descricao,
                    a.cod_apolice,
                    a.num_apolice,
                    a.dt_vencimento,
                    c.nom_cgm
                FROM
                    patrimonio.bem as bae
                    LEFT OUTER JOIN patrimonio.bem_baixado as bb ON
                        bb.cod_bem = bae.cod_bem,
                    patrimonio.especie as e,
                    patrimonio.apolice_bem as ab,
                    patrimonio.apolice as a,
                    sw_cgm as c
                WHERE
                    bb.cod_bem IS NULL                AND
                    ab.cod_bem = bae.cod_bem          AND
                    ab.cod_apolice = a.cod_apolice    AND
                    bae.cod_especie = e.cod_especie   AND
                    bae.cod_grupo = e.cod_grupo       AND
                    bae.cod_natureza = e.cod_natureza AND
                    a.numcgm = c.numcgm
                ";
        if ($codApolice != "") {
            $sSQLs .= " AND a.cod_apolice = $codApolice";
        }

        if ($numApolice != "") {
            $sSQLs .= " AND a.num_apolice = '".$numApolice."'";
        }

        if ($numCgm != "xxx" AND $numCgm != "") {
            $sSQLs .= " AND a.numcgm = $numCgm";
        }
        $sessao->transf  = "";
        $sessao->transf  = $sSQLs;
        $sessao->transf2 = $orderby;

        $sqlPDF  = $sessao->transf;
        $sqlPDF .= " ORDER BY ".$sessao->transf2." ASC";

        $botoesPDF = new botoesPdfLegado;
        $botoesPDF->imprimeBotoes( '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/apoliceSeguros.xml' , $sqlPDF , '' , '' );
    break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
