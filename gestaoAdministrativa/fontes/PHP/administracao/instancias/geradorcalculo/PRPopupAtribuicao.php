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
    * Arquivo de instância para manutenção de funções
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: PRPopupAtribuicao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.03.95
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "PopupAtribuicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RFuncao;
$obErro  = new Erro;

switch ($stAcao) {

    case "incluir":
        $stCondicao = $_POST['hdnCondicao'];
        $inCount = $inCountAux = 0;

        if ($_POST['stTipoVariavel']=="TEXTO") {
            $stCondicao = '"'.$stCondicao.'"';
        }
        $stAtribuicao = str_replace('#','$',$stVariavelInicial).' = '.str_replace('#','$',$stCondicao).';';
        $stAtribuicao = str_replace('&nbsp;',' ',$stAtribuicao);
        $stAtribuicao = str_replace("\'","'",$stAtribuicao);
        $arFuncao = Sessao::read('Funcao');
        foreach ($arFuncao as $inChave=>$stValor) {
            $stAtribuicao = str_replace($stValor,'',$stAtribuicao);
        }
        // for ($inC=0; $inC<count($stAtribuicao); $inC++) {
        //     echo $stAtribuicao[$inC]." - ".ord($stAtribuicao[$inC])." <br>";
        // }
        exec("php -r '$stAtribuicao' ",$arOut,$inError);
        if ($inError!=0) {
            $obErro->setDescricao("Erro de Sintaxe. Revise a documentação.");
            echo "php -r '$stAtribuicao' ";
        }

        if ( !$obErro->ocorreu() ) {
            $arPosicao  = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível
            $stCondicao = str_replace("\'","'",$stCondicao);
            $arFuncao = Sessao::read('Funcao');
            $inCountCorpo = count($arFuncao['Corpo']);
            for ($inCount=$arPosicao[0]; $inCount<$inCountCorpo; $inCount++) {
                $arAux[] = $arFuncao['Corpo'][$inCount];
            }
            $arTmp['Nivel']    = $arPosicao[1];
            $arTmp['Conteudo'] = $stVariavelInicial.' <- '.$stCondicao.';';
            $arCorpoAnterior = $arFuncao['Corpo'];
            $arFuncao['Corpo'][ $arPosicao[0] ] = $arTmp;
            for ($inCount=($arPosicao[0]+1); $inCount<=$inCountCorpo; $inCount++) {
                $arFuncao['Corpo'][$inCount] = $arAux[$inCountAux++];
            }

            $stCorpoLN = $obRegra->montaCorpoFuncao();
            $stCorpoLN = str_replace("\\\'","\'",$stCorpoLN);
            $stCorpoLN = str_replace("''","\\'\\'",$stCorpoLN);
            $stCorpoPL = $obRegra->ln2pl();
/*            $stCorpoPL = str_replace("(''","(\\'\\'||\\'\\'",$stCorpoPL);
            $stCorpoPL = str_replace(",''",",\\'\\'||\\'\\'",$stCorpoPL);
            $stCorpoPL = str_replace("'')","\\'\\'||\\'\\')",$stCorpoPL);
            $stCorpoPL = str_replace("'',","\\'\\'||\\'\\',",$stCorpoPL);*/
            $stCorpoPL = str_replace("''","\\'\\'",$stCorpoPL);

            Sessao::write('Funcao',$arFuncao);
            SistemaLegado::executaWindowOpener("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $stCondicao = $_POST['hdnCondicao'];
        $inCount = $inCountAux = 0;

        if ($_POST['stTipoVariavel']=="TEXTO") {
            $stCondicao = '"'.$stCondicao.'"';
        }
        $stAtribuicao = str_replace('#','$',$stVariavelInicial).' = '.str_replace('#','$',$stCondicao).';';
        $stAtribuicao = str_replace('&nbsp;',' ',$stAtribuicao);
        exec("php -r '$stAtribuicao' ",$arOut,$inError);
        if ($inError!=0) {
            $obErro->setDescricao("Erro de Sintaxe. Revise a documentação.");
        }

        if ( !$obErro->ocorreu() ) {
            $arPosicao = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível

            $arFuncao = Sessao::read('Funcao');
            $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'] = $stVariavelInicial.' <- '.$stCondicao.';';
            Sessao::write('Funcao',$arFuncao);
            $stCorpoLN = $obRegra->montaCorpoFuncao();
            $stCorpoPL = $obRegra->ln2pl();
            SistemaLegado::executaWindowOpener("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}

?>
