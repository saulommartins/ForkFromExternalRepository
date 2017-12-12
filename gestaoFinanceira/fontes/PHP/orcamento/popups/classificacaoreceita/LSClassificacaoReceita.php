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
    * Página de Listagem de Itens
    * Data de Criação   : 03/08/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    $Id: LSClassificacaoReceita.php 64311 2016-01-14 17:50:11Z jean $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ClassificacaoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stFncJavaScript .= " function insereClassReceita(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obROrcamentoClassificacaoReceita = new ROrcamentoClassificacaoReceita;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

//Monta sessao com os valores do filtro
$arFiltro = Sessao::read('filtroPopUp');
if ( is_array($arFiltro) ) {
    $_REQUEST = $arFiltro;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arFiltro[$key] = $valor;
    }
    Sessao::write('filtroPopUp',$arFiltro);
}

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}
if ($_REQUEST['inCodClassificacao']) {
    $arClassificacao = explode( "." , $_REQUEST['inCodClassificacao'] );
    $inCount         = count( $arClassificacao );
    //busca o codigo da Classificacao que sera inserido
    //o codigo sera o ultimo do array que nao possua valor igual a zero
    for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
        if ($arClassificacao[$inPosicao] != 0) {
            break;
        }
    }
    //remonta a Classificacao de Receita, colocanco '0' na ultima casa com valor
    for ($inPosicaoNew = 0; $inPosicaoNew <= $inPosicao; $inPosicaoNew++) {
            $stVerificaClassReceita .= $arClassificacao[$inPosicaoNew].".";
    }
    $stVerificaClassReceita = substr( $stVerificaClassReceita, 0, strlen( $stVerificaClassReceita ) - 1 );

    $obROrcamentoClassificacaoReceita->setMascClassificacao( $stVerificaClassReceita );
    $stLink .= '&inCodClassificacao='.$_REQUEST['inCodClassificacao'];
}

if ($_REQUEST['stDescricao']) {
    $obROrcamentoClassificacaoReceita->setDescricao( $_REQUEST['stDescricao'] );
    $stLink .= '&stDescricao='.$_REQUEST['stDescricao'];
}

if (trim($_REQUEST['inExercicio']) != "") {
    $obROrcamentoClassificacaoReceita->setExercicio( $_REQUEST['inExercicio'] );
    $stLink .= '&inExercicio='.$_REQUEST['inExercicio'];
}

$stLink .= "&stAcao=".$stAcao;
$obROrcamentoClassificacaoReceita->setListarAnaliticas('true');

$rsLista = new RecordSet();

if ($_REQUEST['tipoBusca'] == 'receitaDedutora') {
    $obROrcamentoClassificacaoReceita->setDedutora (true);

    $obROrcamentoClassificacaoReceita->listar( $rsLista, " ORDER BY mascara_classificacao" );

} else if ($_REQUEST['tipoBusca'] == 'receitaIRRF') {
    include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );
    $obROrcamentoReceita = new ROrcamentoReceita;
    
    if ($_REQUEST['inExercicio'] != "") {
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setExercicio ( $_REQUEST['inExercicio']        );
    }
    
    if ($_REQUEST['inCodReceitaIRRF'] != "") {
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setCodReceita ( $_REQUEST['inCodReceitaIRRF']   );  
    }
    
    if ($_REQUEST['inCodClassificacao'] != ""){
        $stEstrutural = str_replace(".", "", $_REQUEST['inCodClassificacao']);                
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setCodEstrutural ( $stEstrutural );  
    }

    if( $_REQUEST['stDescricao'] != "") {
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao ( $_REQUEST['stDescricao'] );  
    }

    $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaListaIRRF ( $rsContaIRRF ,$obTransacao     );

    if ( $rsContaIRRF->getNumLinhas() > 0 ) {
        foreach ($rsContaIRRF->getElementos() as $value) {
            
            $arTemp['codigo_receita']        = $value['cod_receita_irrf'];
            $arTemp['mascara_classificacao'] = $value['cod_estrutural'];
            $arTemp['descricao']             = $value['descricao'];
                
            $arAux[] = $arTemp;
            unset($arTemp);
        }
        $rsLista->preenche($arAux);
    }
    
} else {
    $obROrcamentoClassificacaoReceita->listar( $rsLista, " ORDER BY mascara_classificacao" );
}

if ($_REQUEST['tipoBusca'] != 'receitaIRRF') {
    $obLista = new Lista;
    $obLista->obPaginacao->setFiltro("&stLink=".$stLink );

    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição ");
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "mascara_classificacao" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "SELECIONAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:insereClassReceita();" );
    $obLista->ultimaAcao->addCampo("1","mascara_classificacao");
    $obLista->ultimaAcao->addCampo("2","descricao");
    $obLista->commitAcao();

}else{
    $obLista = new Lista;
    $obLista->obPaginacao->setFiltro("&stLink=".$stLink );

    $obLista->setRecordSet( $rsLista );
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código da Receita");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Classificação");
    $obLista->ultimoCabecalho->setWidth( 22 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição ");
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "codigo_receita" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "mascara_classificacao" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "SELECIONAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:insereClassReceita();" );
    $obLista->ultimaAcao->addCampo("1","codigo_receita");
    $obLista->ultimaAcao->addCampo("2","descricao");
    $obLista->commitAcao();

}

$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
