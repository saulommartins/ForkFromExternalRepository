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
    * Aqruivo que faz a exclusão dos Atributos
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper

    * @ignore

    $Revision: 28299 $
    $Name$
    $Autor: $
    $Date: 2008-02-29 14:31:59 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-03.01.02
*/

/*
$Log$
Revision 1.21  2007/02/28 18:41:53  bruce
Bug #8326#

Revision 1.20  2006/10/11 16:01:54  larocca
Bug #6906#

Revision 1.18  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.17  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.16  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.02");
if (!(isset($ctrl)))
    $ctrl = 0;

if (isset($codAtributo))
    $ctrl = 1;
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php
switch ($ctrl) {

// lista atributos cadastrados
case 0:

    if (isset($acao)) {
        $sSQLs = "SELECT cod_atributo, nom_atributo FROM administracao.atributo_dinamico";
        $sessao->transf = $sSQLs;
    }

    // gera lista de atributos com paginacao
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados($sessao->transf,"10");
    $paginacao->pegaPagina($pagina);
    $paginacao->geraLinks();
    $paginacao->pegaOrder("nom_atributo","ASC");
    $sSQL = $paginacao->geraSQL();

    //print $sSQL;
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();

?>
    <table width="100%">
    <tr>
        <td class="alt_dados" colspan="4">Registros de Atributo</td>
    </tr>
    <tr>
        <td class="labelcenter" width="5%">&nbsp;</td>
        <td class="labelcenter" width="12%">Código</td>
        <td class="labelcenter" width="80%">Descrição</td>
        <td class="labelcenter">&nbsp;</td>
    </tr>
<?php
    $cont = $paginacao->contador();

    // lista atributos encontrados
    while (!$dbEmp->eof()) {
        $codAtributof  = trim($dbEmp->pegaCampo("cod_atributo"));
        $nomAtributof  = trim($dbEmp->pegaCampo("nom_atributo"));
        $dbEmp->vaiProximo();
        $nomAtributof = AddSlashes($nomAtributof);

?>
        <tr>
            <td class="labelcenter" width="5%"><?=$cont++;?></td>
            <td class="show_dados_right">&nbsp;<?=$codAtributof;?></td>
            <td class="show_dados">&nbsp;<?=$nomAtributof;?></td>
            <td class="botao" title="Excluir">
                <a href=''
onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/configuracao/excluiAtributo.php?<?=Sessao::getId();?>','codAtributo','<?=$codAtributof;?>','Atributo: <?=$codAtributof;?> - <?=$nomAtributof;?>','sn_excluir','<?=Sessao::getId();?>')">
                <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif'  border="0"></a></td>
            </td>
        </tr>
<?php
    }
?>
    </table>
<?php
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();

    $paginacao->mostraLinks();

    echo "</font></tr></td></table>";

    break;

// executa exclusao do atributo...
case 1:

    include_once '../configPatrimonio.class.php';

    $nomAtributo = pegaDado("nom_atributo","administracao.atributo_dinamico","Where cod_atributo = ".$codAtributo);
    $objeto = "Atributo: ".$nomAtributo;
    $patrimonio = new configPatrimonio;
    $patrimonio->setaVariaveisAtributos($codAtributo);

    // exclui atributo...
    if ($patrimonio->deleteAtributo()) {

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';

        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codAtributo);
        $audicao->insereAuditoria();
        echo '<script type="text/javascript">
        alertaAviso("'.$objeto.'","excluir","aviso","'.Sessao::getId().'");
        window.location = "excluiAtributo.php?'.Sessao::getId().'";
        </script>';

    // se houve erro na exclusao ,exibe msg de erro
    } else {
        $objeto = "Este Atributo está sendo utilizado. Atributo: ".$nomAtributo;
        echo '<script type="text/javascript">
        alertaAviso("'.$objeto.'","n_excluir","erro","'.Sessao::getId().'");
        window.location = "excluiAtributo.php?'.Sessao::getId().'";
        </script>';
    }

    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';

?>
