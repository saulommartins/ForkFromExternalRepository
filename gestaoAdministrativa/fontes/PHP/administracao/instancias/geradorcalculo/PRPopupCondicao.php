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

    $Id: PRPopupCondicao.php 63829 2015-10-22 12:06:07Z franver $

    Casos de uso: uc-01.03.95
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/ExpReg/ExpRegData.class.php';

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "PopupCondicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RFuncao;
$obErro  = new Erro;
$obRegExpData = new ExpRegData( '', "br");
$arFuncao = Sessao::read('Funcao');

switch ($stAcao) {

    case "incluir":
        $stCondicao = $_POST['hdnCondicao'];
        $inCount = $inCountAux = 0;
        $arFuncao = Sessao::read('Funcao');
        $inQuantLinhas = $_REQUEST['rdbCondicao'];
        $stCondicao = SistemaLegado::unhtmlentities($stCondicao);
        $stCondicao = str_replace('&nbsp;',' ',$stCondicao);
        $stCondicaoVerifica = str_replace('#','$',$stCondicao);
        $stCondicaoVerifica = str_replace(' =',' ==',$stCondicaoVerifica);
        $stCondicaoVerifica = str_replace('\"','"',$stCondicaoVerifica);
        $stCondicaoVerifica = str_replace('OU','OR',$stCondicaoVerifica);
        $stCondicaoVerifica = str_replace('E','AND',$stCondicaoVerifica);
        $stCondicaoVerifica = str_replace('NULO','0',$stCondicaoVerifica);

        $obRegExpData->setContexto( $stCondicaoVerifica );
        $stCondicaoVerifica = $obRegExpData->br2us();

        exec("php -r '".$stCondicaoVerifica.";' ",$arOut,$inError);

        $obRegExpData->setContexto( $stCondicao );
        $stCondicao = $obRegExpData->br2us();

        if ($inError!=0) {
            $obErro->setDescricao("Erro de Sintaxe. Revise a documentação.".$stCondicaoVerifica );
        }

        if ( !$obErro->ocorreu() ) {
            $arPosicao = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível

            $arFuncao = Sessao::read('Funcao');

            $inCountCorpo = count($arFuncao['Corpo']);
            for ($inCountElementos=0; $inCountElementos<$inQuantLinhas; $inCountElementos++) {
                for ($inCount=($arPosicao[0]+$inCountElementos); $inCount<$inCountCorpo; $inCount++) {
                    $arAux[] = $arFuncao['Corpo'][$inCount];
                }
            }
            $arTmp['Nivel']    = $arPosicao[1]+1;
            $stCondicao = str_replace('\"','"',$stCondicao );

            $arTmp['Conteudo'] = 'SE  '.$stCondicao.' ENTAO';
            $arFuncao['Corpo'][ $arPosicao[0] ] = $arTmp;
            if ($_REQUEST['rdbCondicao']==3) {
                $arTmp['Nivel']    = $arPosicao[1]+1;
                $arTmp['Conteudo'] = 'SENAO';
                $arFuncao['Corpo'][ ($arPosicao[0]+1) ] = $arTmp;
            }
            $arTmp['Nivel']    = $arPosicao[1];
            $arTmp['Conteudo'] = 'FIMSE';
            $arFuncao['Corpo'][ ($arPosicao[0])+($inQuantLinhas-1) ] = $arTmp;

            for ($inCount=($arPosicao[0]+$inQuantLinhas); $inCount<($inCountCorpo+$inQuantLinhas); $inCount++) {
                //for ($inCount=($arPosicao[0]+1); $inCount<=$inCountCorpo; $inCount++) {
                $arFuncao['Corpo'][$inCount] = $arAux[$inCountAux++];
            }

            Sessao::write('Funcao',$arFuncao);
            $stCorpoLN = $obRegra->montaCorpoFuncao();
            $stCorpoPL = $obRegra->ln2pl();
            $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
            $stCorpoPL = str_replace('\"','"',$stCorpoPL);

            SistemaLegado::executaWindowOpener("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $stCondicao = $_POST['hdnCondicao'];
        $inCount = $inCountAux = 0;
        $inQuantLinhas = $_REQUEST['rdbCondicao'];

        $arFuncao = Sessao::read('Funcao');
        $stCondicao = SistemaLegado::unhtmlentities($stCondicao);
        $stCondicao = str_replace('&nbsp;',' ',$stCondicao);
        $stCondicaoVerifica = str_replace('#','$',$stCondicao);
        $stCondicaoVerifica = str_replace(' =',' ==',$stCondicaoVerifica);
        $stCondicaoVerifica = str_replace('\"','"',$stCondicaoVerifica);
        $stCondicaoVerifica = str_replace('OU','OR',$stCondicaoVerifica);
        $stCondicaoVerifica = str_replace('E','AND',$stCondicaoVerifica);

        $obRegExpData->setContexto( $stCondicaoVerifica );
        $stCondicaoVerifica = $obRegExpData->br2us();

        exec("php -r '".$stCondicaoVerifica.";' ",$arOut,$inError);

        //------------------------
        $obRegExpData->setContexto( $stCondicao );
        $stCondicao = $obRegExpData->br2us();

        if ($inError!=0) {
            $obErro->setDescricao("Erro de Sintaxe. Revise a documentação.");
        }

        if ( !$obErro->ocorreu() ) {         
             $arPosicao = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível
            $arFuncao = Sessao::read('Funcao');
            $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'] = 'SE  '.$stCondicao.' ENTAO';
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
