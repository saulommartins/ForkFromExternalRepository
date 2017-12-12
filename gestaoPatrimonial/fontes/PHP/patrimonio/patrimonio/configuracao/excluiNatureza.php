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
    * Aqruivo que faz a exclusão das naturezas
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 20522 $
    $Name$
    $Autor: $
    $Date: 2007-02-28 15:49:17 -0300 (Qua, 28 Fev 2007) $

    * Casos de uso: uc-03.01.03
*/

/*
$Log$
Revision 1.20  2007/02/28 18:49:17  bruce
Bug #8325#

Revision 1.19  2006/11/21 16:14:01  larocca
Bug #6906#

Revision 1.18  2006/10/11 16:01:54  larocca
Bug #6906#

Revision 1.17  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.16  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.15  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.03");
if (!(isset($ctrl)))
    $ctrl = 0;

if (isset($codNatureza))
    $ctrl = 1;
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($ctrl) {

    // exibe lista das Naturezas cadastradas
    case 0:

    if (isset($acao)) {
        $sSQLs = "SELECT cod_natureza, nom_natureza FROM patrimonio.natureza";
        $sessao->transf = $sSQLs;
    }

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados($sessao->transf,"10");
    $paginacao->pegaPagina($pagina);
    $paginacao->geraLinks();
    $paginacao->pegaOrder("nom_natureza","ASC");
    $sSQL = $paginacao->geraSQL();

    //print $sSQL;
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();

?>
    <table width="100%">
    <tr>
        <td class="alt_dados" colspan="4">Registros da Natureza</td>
    </tr>
    <tr>
        <td class="labelcenter" width="5%">&nbsp;</td>
        <td class="labelcenter" width="12%">Código</td>
        <td class="labelcenter" width="80%">Descrição</td>
        <td class="labelcenter">&nbsp;</td>
    </tr>
<?php

    $cont = $paginacao->contador();

    while (!$dbEmp->eof()) {
        $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
        $nomNaturezaf  = trim($dbEmp->pegaCampo("nom_natureza"));
        $dbEmp->vaiProximo();

        $nomNaturezaf2 = AddSlashes($nomNaturezaf);

?>
        <tr>
            <td class="labelcenter"><?=$cont++;?></td>
            <td class="show_dados_right">&nbsp;<?=$codNaturezaf;?></td>
            <td class="show_dados">&nbsp;<?=$nomNaturezaf;?></td>
            <td class="botao" title="Excluir">
                <a href='' onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/configuracao/excluiNatureza.php?<?=Sessao::getId();?>','codNatureza','<?=$codNaturezaf;?>','Natureza: <?=$nomNaturezaf2;?>','sn_excluir','<?=Sessao::getId();?>')">
                <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif'  border="0"></a></td>
            </td>
        </tr>
<?php
        echo "\n";
    }
?>
    </table>
<?php

    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();

?>
    <table width="100%" align="center">
    <tr>
        <td align="center">
            <font size=2><?=$paginacao->mostraLinks(); ?></font>
        </td>
    </tr>
    </table>
<?php

    break;

    // exclui a natureza selecionada
    case 1:

        include_once '../configPatrimonio.class.php';

        $nomNatureza = pegaDado("nom_natureza","patrimonio.natureza","where cod_natureza = ".$codNatureza);
        $patrimonio = new configPatrimonio;
        $patrimonio->setaVariaveisNatureza($codNatureza);

        if ($patrimonio->deleteNatureza()) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
            $audicao->insereAuditoria();

            $objeto = "Natureza: ".$nomNatureza;
            echo '
                <script type="text/javascript">
                    alertaAviso("'.$objeto.'","excluir","aviso","'.Sessao::getId().'");
                    window.location = "excluiNatureza.php?'.Sessao::getId().'";
                </script>';
        } else {

            $objeto = "Esta Natureza está sendo utilizada. Natureza: ".$nomNatureza;
            echo '
                <script type="text/javascript">
                     alertaAviso("Esta natureza está sendo utilizada! (Natureza:'.$nomNatureza.')","n_excluir","erro","'.Sessao::getId().'");
                    window.location = "excluiNatureza.php?'.Sessao::getId().'";
                </script>';
        }

    break;
}
?>

<?php
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
