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
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/ExpReg/ExpRegData.class.php';

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "PopupAtribuicaoFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RFuncao;
$obErro  = new Erro;
$obRegExpData = new ExpRegData( '', "br");

/////// Parte comun a inclusão e alteração
$inCount = $inCountAux = 0;
$obRegra->setNomeFuncao( $_POST['stFuncao'] );
$obRegra->listar( $rsFuncao );

$obRegra->obRVariavel->setCodFuncao( $rsFuncao->getCampo('cod_funcao') );
$obRegra->obRVariavel->setCodModulo( $rsFuncao->getCampo('cod_modulo') );
$obRegra->obRVariavel->setCodBiblioteca( $rsFuncao->getCampo('cod_biblioteca') );
$obRegra->obRVariavel->setParametro( true );
$obRegra->obRVariavel->listar( $rsParametros );

$stFuncao = $_POST['stVariavelInicial']. ' <- '.$_POST['stFuncao']. '( ';
while ( !$rsParametros->eof() ) {
    $rsVariavel     = new RecordSet;
    $arVariaveis    = array();
    $stTipoVariavel = $rsParametros->getCampo('nom_tipo');
    $stNomVariavel  = $rsParametros->getCampo('nom_variavel');
    $stValor        = 'stValor_'    . $stNomVariavel;
    $$stValor       = $_POST[ $stValor ];
    $stVariavel     = 'stVariavel_' . $stNomVariavel;
    $$stVariavel    = str_replace('-','#',$_POST[ $stVariavel ]);
    $stParametro    = ($$stValor != "") ? $$stValor : $$stVariavel;

    $stFuncao .= ' ';
    if ( ($stTipoVariavel == 'TEXTO') && (trim($$stValor) != "")) {
        $stFuncao .= '"'. $stParametro .'"';
    } else {
        $stFuncao .= $stParametro;
    }
    $stFuncao .= ' ,';
    $rsParametros->proximo();
}
if ( substr($stFuncao,strlen($stFuncao)-1,strlen($stFuncao)) == ',') {
    $stFuncao = substr($stFuncao,0,strlen($stFuncao)-1);
}
$stFuncao .= ' ); ';
///////////////////////

switch ($stAcao) {

    case "incluir":
        $arPosicao  = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível
        $arFuncao = Sessao::read('Funcao');
        $inCountCorpo = count($arFuncao['Corpo']);
        for ($inCount=$arPosicao[0]; $inCount<$inCountCorpo; $inCount++) {
            $arAux[] = $arFuncao['Corpo'][$inCount];
        }

        $arTmp['Nivel']    = $arPosicao[1];
        $arTmp['Conteudo'] = $stFuncao;
        $stCondicao = $arTmp['Conteudo'];

        $obRegExpData->setContexto( $stCondicao );
        $stCondicao = $obRegExpData->br2us();

        $arTmp['Conteudo'] = $stCondicao;

        $arCorpoAnterior = $arFuncao['Corpo'];
        $arFuncao['Corpo'][ $arPosicao[0] ] = $arTmp;
        for ($inCount=($arPosicao[0]+1); $inCount<=$inCountCorpo; $inCount++) {
            $arFuncao['Corpo'][$inCount] = $arAux[$inCountAux++];
        }
        $stCorpoLN = $obRegra->montaCorpoFuncao();
        $stCorpoPL = $obRegra->ln2pl();
        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);
        SistemaLegado::executaWindowOpener("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");

    break;
    case "alterar":
        $arPosicao = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível

        $stCondicao = $stFuncao;

        $obRegExpData->setContexto( $stCondicao );
        $stCondicao = $obRegExpData->br2us();

        $stFuncao = $stCondicao;
        $arFuncao = Sessao::read('Funcao');
        $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'] = $stFuncao;
        Sessao::write('Funcao',$arFuncao);
        $stCorpoLN = $obRegra->montaCorpoFuncao();
        $stCorpoPL = $obRegra->ln2pl();
        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);
        SistemaLegado::executaWindowOpener("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");
    break;
}

?>
