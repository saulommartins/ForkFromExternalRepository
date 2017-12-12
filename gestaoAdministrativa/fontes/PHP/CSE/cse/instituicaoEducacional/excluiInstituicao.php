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
* Arquivo de instância para Instituição Educacional
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19055 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 08:44:42 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.87
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");

    if (!(isset($ctrl))) {
        $ctrl = 0;
        unset($sessao->transf2);
    }

    if (isset($codInstituicao)) {
        $ctrl=1;
    }

?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

    switch ($ctrl) {
        case 0:
        $ctrl = 1;
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT
                    cod_instituicao,
                    nom_instituicao
                    FROM
                    cse.instituicao_educacional Where cod_instituicao > 0 " ;
        //echo $select."<br>";

        if (!(isset($sessao->transf2))) {
            $sessao->transf = "";
            $sessao->transf = $select;
            $sessao->transf2 = "nom_instituicao";
            $sessao->transf3["controle"] = 1;
        }

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(".$sessao->transf2.")","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbConfig->abreSelecao($sSQL);

        if ( $pagina > 0 and $dbConfig->eof() ) {
            $pagina--;
            $paginacao->pegaPagina($pagina);
            $paginacao->geraLinks();
            $paginacao->pegaOrder("lower(".$sessao->transf2.")","ASC");
            $sSQL = $paginacao->geraSQL();
            $dbConfig->abreSelecao($sSQL);
        }

        while (!$dbConfig->eof()) {
            $cod = $dbConfig->pegaCampo("cod_instituicao");
            $lista[$cod] = $dbConfig->pegaCampo("nom_instituicao");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
    <table width="100%">
        <tr>
            <td colspan="4" class="alt_dados">
                Instituições Cadastradas
            </td>
        </tr>
        <tr>
            <td class="labelcenter"  width='5%'>
                &nbsp;
            </td>
            <td class="labelcenter" width='12%'>
                Código
            </td>
            <td class="labelcenter" width="80%">
                Instituição
            </td>
            <td class="label">
                &nbsp;
            </td>
<?php
        $iCont = $paginacao->contador();
        if ($lista != "") {
            while (list ($key, $val) = each ($lista)) { //mostra os tipos de processos na tela
                     $parametros = urlencode($val);
?>
        <tr>
            <td class="label">
                <?=$iCont++;?>
            </td>
            <td class="show_dados_right">
                <?=$key;?>
            </td>
            <td class="show_dados">
                <?=$val;?>
            </td>
            <td class="botao">
            <?php echo "

        <a href='#' onClick=\"alertaQuestao('".CAM_CSE."cse/instituicaoEducacional/excluiInstituicao.php?".$sessao->id."','codInstituicao','".$key."','".$parametros."','sn_excluir','$sessao->id');\">
                                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a>";?>
            </td>
<?php
            }
        } else {
?>
        <tr>
            <td class="show_dados_center" colspan="4">
                <b>Nenhum registro encontrado!</b>
            </td>
        </tr>
<?php
        }
        echo "</table>";
        echo "<table width=450 align=center><tr><td align=center><font size=2>";
            $paginacao->mostraLinks();
        echo "</font></tr></td></table>";

    break;
        case 1:
        $excluir = new cse;
        if ($excluir->excluiInstituicao($codInstituicao)) {
            include CAM_FW_LEGADO."auditoriaLegada.class.php";
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $codInstituicao);
            $audicao->insereAuditoria();
            echo '
                <script type="text/javascript">
                alertaAviso("'.$codInstituicao.'","excluir","aviso","'.$sessao->id.'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.$sessao->id.'&pagina='.$pagina.'");
                </script>';
        } else {
            echo '
                <script type="text/javascript">
                alertaAviso("'.$codInstituicao.'","n_excluir","erro","'.$sessao->id.'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.$sessao->id.'");
                </script>';
        }
    break;
    }
    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
