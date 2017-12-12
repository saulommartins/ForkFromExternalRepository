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
    * Arquivo que exclui as baixas de bens
    * Data de Criação   : 20/05/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 18992 $
    $Name$
    $Autor: $
    $Date: 2006-12-26 15:35:24 -0200 (Ter, 26 Dez 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.16  2006/12/26 17:35:24  hboaventura
Bug #7517#

Revision 1.15  2006/07/21 11:35:07  fernando
Inclusão do  Ajuda.

Revision 1.14  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.13  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../bens.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.06");
$exclui = new bens;

if ( !(isset($ctrl)) )
    $ctrl = 0;
if ( isset($codBem) )
    $ctrl = 1;
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($ctrl) {
    case 0:
//		$listaBem = $exclui->listaBensBaixados();
         $sSQLs = "
                SELECT
                    cod_bem,
                    dt_baixa,
                    motivo
                FROM   patrimonio.bem_baixado";

        if (!isset($pagina)) {
            $sessao->transf = $sSQLs;
        }

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento="&ctrl=0";
        $paginacao->geraLinks();
        $paginacao->pegaOrder("dt_baixa","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($sSQL);

        if ( $pagina > 0 and $dbConfig->eof() ) {
            $pagina--;
            $paginacao->pegaPagina($pagina);
            $paginacao->complemento="&ctrl=1";
            $paginacao->geraLinks();
            $paginacao->pegaOrder("dt_baixa","ASC");
            $sSQL = $paginacao->geraSQL();
            $conn->abreSelecao($sSQL);
        }
        $dbConfig->vaiPrimeiro();
        $sessao->transf2 = $pagina;

?>
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="5">Registros de Bens Baixados</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%">&nbsp;</td>
            <td class="labelcenter" width="15%">Código do Bem</td>
            <td class="labelcenter" width="15%">Data da Baixa</td>
            <td class="labelcenter" width="60%">Motivo</td>
            <td class="labelcenter" width="5%">&nbsp;</td>
        </tr>
<?php
        $cont = $paginacao->contador();

        while (!$dbConfig->eof()) {
            $cod = $dbConfig->pegaCampo("cod_bem");
            $lista[$cod] = $dbConfig->pegaCampo("dt_baixa")."/".$dbConfig->pegaCampo("motivo");
            $dbConfig->vaiProximo();
        }
        if ($lista!="") {
            $cont = 1;
            while (list ($key, $val) = each ($lista)) {
                $res = explode("/", $val);
                $arrData = explode("-", $res[0]);
                $res[0] = $arrData[2]."/".$arrData[1]."/".$arrData[0];
?>
                <tr>
                    <td class="labelcenter"><?=$cont++;?></td>
                    <td class="show_dados_right"><?=$key;?></td>
                    <td class="show_dados_right"><?=$res[0];?></td>
                    <td class="show_dados"><?=$res[1];?></td>
                    <td class="botao">
                    <a href='' onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/bens/excluiBaixa.php?<?=Sessao::getId();?>','codBem','<?=$key;?>','<?=$key." - ".trim($res[1]);?>','sn_excluir', '<?=Sessao::getId();?>');">
                    <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif' title="Excluir" border='0'></a>
                </td>
<?php
            }
        } else {
?>
            <tr><td class="show_dados_center" colspan="5">Nenhum registro encontrado.</td></tr>
<?php
        }
?>
        </table>
<?php
 $dbConfig->limpaSelecao();
        $dbConfig->fechaBD();

        echo "<table width=100% align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";

    break;
    case 1:
        if ( $exclui->excluiBaixa($codBem) ) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codBem); //registra os passos no auditoria
            $audicao->insereAuditoria();
//Exibe mensagem e redireciona
            alertaAviso($PHP_SELF,"Bem: $codBem","excluir","aviso","");

        } else {
            alertaAviso($PHP_SELF,"Bem: $codBem","n_excluir","erro","");
        }
    break;

}

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
