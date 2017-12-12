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
* Arquivo de instância para Questões
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19067 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 09:33:57 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.94
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");

    if (!(isset($ctrl))) {
        $ctrl = 0;
        unset($sessao->transf2);
    }

    if (isset($codQuestao)) {
        $ctrl = 1;
    }
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

    switch ($ctrl) {
        case 0:
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT
                    cod_questao,
                    nom_questao,
                    valor_padrao,
                    tipo
                    FROM
                    cse.questao_censo";
        //echo $select."<br>";
        if (!(isset($sessao->transf2['questao']))) {
            $sessao->transf['questao'] = "";
            $sessao->transf['questao'] = $select;
            $sessao->transf2['questao'] = "cod_questao";
        }

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf['questao'],"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(".$sessao->transf2['questao'].")","ASC");
        $sSQL = $paginacao->geraSQL();
        //echo $sSQL."<br>";
        $dbConfig->abreSelecao($sSQL);
        //VOLTA UMA PAGINA CASO O ULTIMO ELEMENTO DA PAGINA CORRENTE TENHA SIDO EXCLUIDO
        if ( $pagina > 0 and $dbConfig->eof() ) {
            $pagina--;
            $paginacao->pegaPagina($pagina);
            $paginacao->geraLinks();
            $paginacao->pegaOrder("lower(".$sessao->transf2['questao'].")","ASC");
            $sSQL = $paginacao->geraSQL();
            //echo $sSQL."<br>";
            $dbConfig->abreSelecao($sSQL);
        }
        while (!$dbConfig->eof()) {
            $lista[] = $dbConfig->pegaCampo("cod_questao")."/".$dbConfig->pegaCampo("nom_questao")."/".
            $dbConfig->pegaCampo("valor_padrao")."/".$dbConfig->pegaCampo("tipo");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
<table width="100%">
    <tr>
        <td colspan="5" class="alt_dados">
            Questões Cadastradas
        </td>
    <tr>
        <td class="label" width="5%">
            &nbsp;
        </td>
        <td class="labelcenter" width="12%">
            Código
        </td>
        <td class="labelcenter" width="30%">
            Questão
        </td>
        <td class="labelcenter" width="50%">
            Valor Padrão
        </td>
        <td class="label">
            &nbsp;
        </td>
    </tr>
<?php
    $iCont = $paginacao->contador();
    if ($lista != "") {
        while (list ($cod, $val) = each ($lista)) { //mostra os tipos de processos na tela
                $fim = explode("/", $val);
?>
    <tr>
        <td class="label">
            <?=$iCont++?>&nbsp;
        </td>
        <td class="show_dados_right">
            <?=$fim[0];?>&nbsp;
        </td>
        <td class="show_dados">
            <?=$fim[1];?>&nbsp;
        </td>
        <td class="show_dados">
<?php
    if ($fim[3] == "l" or $fim[3] == "m") {
        $arTipo = explode("\n", $fim[2] );
        echo "            ".$arTipo[0]."...";
    } else {
        echo $fim[2];
    }
?>&nbsp;
        </td>
        <td class="botao">

            <?php echo "
        <a href='#'
onClick=\"alertaQuestao('".CAM_CSE."cse/questoes/excluiQuestao.php?".$sessao->id."&stDescQuestao=".$fim[1]."','codQuestao','".$fim[0]."','".$fim[1]."','sn_excluir','$sessao->id');\">
                                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a>";?>

        </td>
    </tr>
<?php
        }
    } else {
?>
    <tr>
        <td class="show_dados_center" colspan="5">
            <b>Nenhum registro encontrado!</b>
        </td>
    </tr>
<?php
    }
?>
</table>
<table width="450" align="center">
    <tr>
        <td align="center">
            <font size=2>
            <?=$paginacao->mostraLinks();?>
            </font>
        </td>
    </tr>
</table>
<?php
    break;
    case 1:
        $excluir = new cse;
        if ($excluir->excluiQuestao($codQuestao)) {
            include(CAM_FW_LEGADO."auditoriaLegada.class.php");
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $codQuestao);
            $audicao->insereAuditoria();
            echo '<script type="text/javascript">
                alertaAviso("'.$codQuestao.'","excluir","aviso","'.$sessao->id.'");
                window.location = "excluiQuestao.php?'.$sessao->id.'&pagina='.$pagina.'";
                </script>';
        } else {
            echo '<script type="text/javascript">
                alertaAviso("'.$codQuestao.'","n_excluir","erro","'.$sessao->id.'");
                window.location = "excluiQuestao.php?'.$sessao->id.'&pagina='.$pagina.'";
                </script>';
        }
    break;
}
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
