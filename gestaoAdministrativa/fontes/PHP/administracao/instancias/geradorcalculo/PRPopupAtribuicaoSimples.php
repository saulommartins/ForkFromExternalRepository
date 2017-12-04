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

$Revision: 5624 $
$Name$
$Author: lizandro $
$Date: 2006-01-26 17:48:55 -0200 (Qui, 26 Jan 2006) $

Casos de uso: uc-01.03.95
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "PopupAtribuicaoSimples";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RFuncao;
$obErro  = new Erro;

switch ($stAcao) {

    case "incluir":

        $stVariavelInicial = $_REQUEST['stVariavelInicial'];

        $stCondicao = $_POST['hdnCondicao'];
        $inCount = $inCountAux = 0;
        if ($_POST['stTipoVariavel']=="DATA") {
            $stdata = explode ("/", $stCondicao);
            if ($stdata[2] != '') {
                $stCondicao = $stdata[2]."-".$stdata[1]."-".$stdata[0];
            }
        }
        if ($_POST['stTipoVariavel']=="TEXTO") {
            if ($stCondicao != 'VAZIO') {
                $stCondicao = '"'.$stCondicao.'"';
            }
        }
        $stAtribuicao = str_replace('#','$',$stVariavelInicial).' = '.str_replace('#','$',$stCondicao).';';
        $stAtribuicao = str_replace('&nbsp;',' ',$stAtribuicao);
        $stAtribuicao = str_replace("\'","'",$stAtribuicao);

        $arFuncao = Sessao::read('Funcao');
        foreach ($arFuncao as $inChave=>$stValor) {
            $stAtribuicao = str_replace($stValor,'',$stAtribuicao);
        }

        //está sendo verificado a sintaxe com eval pois a função exec não funciona corretamente no stack
        $inError = eval($stAtribuicao);
        // exec("php -r '$stAtribuicao' ",$arOut,$inError);
        if ($inError === false) {
            $obErro->setDescricao("Erro de Sintaxe. Revise a documentação.");
        }
        unset($$stVariavelInicial);
        if (preg_match("/".$stCondicao."/", '$')) {
            unset($$stCondicao);
        }

        if ( !$obErro->ocorreu() ) {
            $arPosicao  = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível

            $stCondicao = str_replace("\'","'",$stCondicao);
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

            Sessao::write('Funcao',$arFuncao);
            $stCorpoLN = $obRegra->montaCorpoFuncao();
            $stCorpoLN = str_replace("\\\'","\'",$stCorpoLN);
            $stCorpoLN = str_replace("''","\\'\\'",$stCorpoLN);
            $stCorpoPL = $obRegra->ln2pl();
            $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
            $stCorpoPL = str_replace('\"','"',$stCorpoPL);

            $stCorpoPL = str_replace("''","\\'\\'",$stCorpoPL);
            SistemaLegado::executaWindowOpener("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $stCondicao = $_POST['hdnCondicao'];
        $inCount = $inCountAux = 0;

        $stVariavelInicial = $_REQUEST['stVariavelInicial'];
         if ($_POST['stTipoVariavel']=="DATA") {
            $stdata = explode ("/", $stCondicao);
            if ($stdata[2] != '') {
                $stCondicao = $stdata[2]."-".$stdata[1]."-".$stdata[0];
            }
        }
        if ($_POST['stTipoVariavel']=="TEXTO") {
            if ($stCondicao != 'VAZIO') {
                $stCondicao = '"'.$stCondicao.'"';
            }
        }
        $stAtribuicao = str_replace('#','$',$stVariavelInicial).' = '.str_replace('#','$',$stCondicao).';';
        $stAtribuicao = str_replace('&nbsp;',' ',$stAtribuicao);
        $stAtribuicao = str_replace("\'","'",$stAtribuicao);

        //está sendo verificado a sintaxe com eval pois a função exec não funciona corretamente no stack
        $inError = eval($stAtribuicao);
        // exec("php -r '$stAtribuicao' ",$arOut,$inError);
        if ($inError === false) {
            $obErro->setDescricao("Erro de Sintaxe. Revise a documentação.");
        }
        unset($$stVariavelInicial);
        if (preg_match("/".$stCondicao."/", '$')) {
            unset($$stCondicao);
        }

        if ( !$obErro->ocorreu() ) {
            $arPosicao = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível
            $arFuncao = Sessao::read('Funcao');
            $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'] = $stVariavelInicial.' <- '.$stCondicao.';';
            Sessao::write('Funcao',$arFuncao);

            $stCorpoLN = $obRegra->montaCorpoFuncao();
            $stCorpoPL = $obRegra->ln2pl();

            $stCorpoLN = str_replace("\\\'","\'",$stCorpoLN);
            $stCorpoLN = str_replace("''","\\'\\'",$stCorpoLN);
            $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
            $stCorpoPL = str_replace('\"','"',$stCorpoPL);
            $stCorpoPL = str_replace("''","\\'\\'",$stCorpoPL);

            SistemaLegado::executaWindowOpener("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}

?>
