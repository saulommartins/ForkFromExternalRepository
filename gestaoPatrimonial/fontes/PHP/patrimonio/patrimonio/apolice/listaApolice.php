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
    * Script que lista as apólices
    * Data de Criação   : 24/03/2003

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.15  2006/07/21 11:34:42  fernando
Inclusão do  Ajuda.

Revision 1.14  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.13  2006/07/06 12:11:27  diego

*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
    include_once '../apolice.class.php';
    setAjuda("UC-03.01.08");
    $sScript = "alteraApolice.php";
    $sBotao  = "btneditar.gif";

    // atribui o valor da variaval de sessao
    $acao = $sessao->acao;

    // excluir, caso a acao do item de menu seja 74
    if ($acao=='74') {
        $sScript = "excluiApolice.php";
        $sBotao  = "btnexcluir.gif";

    }

    $altera = new apolice;
    $aApolice = $altera->listaApolice();

?>
    <table width="100%">
    <tr>
        <td colspan="7" class="alt_dados">Registros das Apólices de Seguro</td>
    </tr>
    <tr>
        <td class="labelcenter" width="5%">&nbsp;</td>
        <td class="labelcenter" width="12%">Código</td>
        <td class="labelcenter">Número</td>
        <td class="labelcenter">Seguradora</td>
        <td class="labelcenter">Vencimento</td>
        <td class="labelcenter" width="1%">&nbsp;</td>
    </tr>
<?php

    // consulta apolices...
    $sSQLs = "
            select
                a.cod_apolice, a.numcgm, c.nom_cgm, a.num_apolice, to_char(a.dt_vencimento,'dd/mm/yyyy') as dt_vencimento, a.contato
            from
                patrimonio.apolice a, sw_cgm c
            where
                c.numcgm = a.numcgm
            ";

    $sessao->transf = $sSQLs;

    // gera lista de apolices com paginacao
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados($sessao->transf,"10");
    $paginacao->pegaPagina($pagina);
    $paginacao->geraLinks();
    $paginacao->pegaOrder("c.nom_cgm, a.num_apolice","ASC");
    $sSQL = $paginacao->geraSQL();

    //Pega os dados encontrados em uma query
    $conn = new dataBaseLegado;
    $conn->abreBD();
    $conn->abreSelecao($sSQL);

    if ( $pagina > 0 and $conn->eof() ) {
        $pagina--;
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("c.nom_cgm, a.num_apolice","ASC");
        $sSQL = $paginacao->geraSQL();
        $conn->abreSelecao($sSQL);
    }

    $conn->vaiPrimeiro();
    $cont = $paginacao->contador();

    $sessao->transf2 = $pagina;

    // lista apolices encontrados
    while (!$conn->eof()) {

        $codApolice  = trim($conn->pegaCampo("cod_apolice"));
        $numcgm  = trim($conn->pegaCampo("numcgm"));
        $nom_cgm  = trim($conn->pegaCampo("nom_cgm"));
        $num_apolice  = trim($conn->pegaCampo("num_apolice"));
        $dt_vencimento  = trim($conn->pegaCampo("dt_vencimento"));
        // parametros para monstar link de 'exclusao' ou 'alteracao' de apolice
        // acao == 74 -> acao de exclusao
        if ($acao=='74') {

            $title = "Excluir";
            $sessao->seguradora = $nom_cgm;
            $onClick =
"onClick=\"alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/apolice/excluiApolice.php?".Sessao::getId()."&codigo=".$codApolice."&nom_cgm=".$nom_cgm."',"."'codigo','".$codApolice."','Apólice ".$num_apolice." - ".$nom_cgm."','sn_excluir', '".Sessao::getId()."');\"";

        // acao de alteracao
        } else {
            $title = "Alterar";
            $sPagAlt = $sScript."?".Sessao::getId()."&codigo=$codApolice&pagina=$pagina";
        }

        $conn->vaiProximo();
?>
            <tr>
                <td class="labelcenter"><?=$cont++;?></td>
                <td class="show_dados_right"><?=$codApolice;?></td>
                <td class="show_dados"><?=$num_apolice; ?>&nbsp;</td>
                <td class="show_dados"><?=$nom_cgm; ?>&nbsp;</td>
                <td class="show_dados_center"><?=$dt_vencimento;?>&nbsp;</td>
                <td class="botao" title="<?=$title;?>">
                    <a href='<?=$sPagAlt;?>' <?=$onClick;?>>
                    <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/<?=$sBotao;?>' title ="Alterar" border='0'></a>
                </td>
            </tr>
<?php
        }

    $conn->limpaSelecao();
    $conn->fechaBD();

    echo "</table>";

    echo "<table width=100% align=center><tr><td align=center><font size=2>";

    $paginacao->mostraLinks();

    echo "</font></tr></td></table>";

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
