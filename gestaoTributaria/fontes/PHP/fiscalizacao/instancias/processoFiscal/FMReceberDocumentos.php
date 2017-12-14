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
    * Formulário que Prorrogar Recebimento de Documentos
    * Data de Criação: 13/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Zainer Cruz dos Santos Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GT_FIS_NEGOCIO."RFISReceberDocumentos.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISReceberDocumentos.class.php" );

//Instanciando a Classe de Controle e de Visao
$obController = new RFISReceberDocumentos;
$obVisao = new VFISReceberDocumentos( $obController );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Define o nome dos arquivos PHP
$stPrograma = "ReceberDocumentos";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

include_once($pgJs);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Switch que monta a pesquisa de acordo com o Tipo de Fiscalização
switch ($_REQUEST['inTipoFiscalizacao']) {
    case 1:
        $_REQUEST['inInscricaoEconomica'] = $_REQUEST['inInscricao'];
        //Filtros da pesquisa.
        $where = $obVisao->filtrosDocumentos( $_REQUEST );
        $obRsProcesso = $obVisao->iniciarInicioFiscalizacaoEconomica( $where );
        $inInscricaoEconomica = $obRsProcesso->arElementos[0]['inscricao_economica'];
    break;

    case 2:
        $_REQUEST['inCodImovel'] = $_REQUEST['inInscricao'];
        //Filtros da pesquisa.
        $where = $obVisao->filtrosDocumentos( $_REQUEST );
        $obRsProcesso = $obVisao->iniciarInicioFiscalizacaoObra( $where );
        $inInscricaoMunicipal = $obRsProcesso->arElementos[0]['inscricao_municipal'];
    break;
}

//Valores da Regra de Negócios
$stTipoFiscalizacao = $obRsProcesso->arElementos[0]['cod_tipo']. " - " . $obRsProcesso->arElementos[0]['descricao'];
$inProcessoFiscal = $obRsProcesso->arElementos[0]['cod_processo'];
$stDataInicio = $obRsProcesso->arElementos[0]['dt_inicio'];
$stPeriodoFiscalizacao = $obRsProcesso->arElementos[0]['periodo_inicio']. " - " . $obRsProcesso->arElementos[0]['periodo_termino'];
$stPrevisaoEncerramento = $obRsProcesso->arElementos[0]['previsao_termino'];
$inSequencia = $obRsProcesso->arElementos[0]['sequencia'];
$inCodFiscal = $obRsProcesso->arElementos[0]['cod_fiscal'];

#ticket #14193 removendo data entrega
if ($obRsProcesso->arElementos[0]['dt_prorrogada'] != "") {
    $stPrazoEntrega = $obRsProcesso->arElementos[0]['dt_prorrogada'];
} else {
    $stPrazoEntrega = $obRsProcesso->arElementos[0]['prazo_entrega'];
}

//Cria um novo formulario
$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

#### Campos Hidden ####

//stAcao
$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//stCtrlfif.cod_documento
$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "receberDocumentosFiscal" );

//stDescricao
$obHdnStDescricao = new Hidden();
$obHdnStDescricao->setName( "stDescricao" );
$obHdnStDescricao->setValue( $_REQUEST["inCodigo"] );

//Cod. Tipo Fiscalização
$obHdnInTipoFiscalizacao = new Hidden();
$obHdnInTipoFiscalizacao->setName( "inTipoFiscalizacao" );
$obHdnInTipoFiscalizacao->setValue( $_REQUEST["inTipoFiscalizacao"] );

//Cod. Processo Fiscal
$obHdnInCodProcesso = new Hidden();
$obHdnInCodProcesso->setName( "inCodProcesso" );
$obHdnInCodProcesso->setValue( $_REQUEST["inCodProcesso"] );

//Cod. Fiscalacao
$obHdnInCodFiscal = new Hidden();
$obHdnInCodFiscal->setName( "inCodFiscal" );
$obHdnInCodFiscal->setValue( $inCodFiscal );

//Data Entrega
$obHdnDataEntrega = new Hidden();
$obHdnDataEntrega->setName( "inDtEntrega" );
$obHdnDataEntrega->setValue( $stPrazoEntrega );

//Sequencia
$obHdnInSequencia = new Hidden();
$obHdnInSequencia->setName( "inSequencia" );
$obHdnInSequencia->setValue( $inSequencia );

//Tipo Fiscalização
$obTipoFiscalizacao = new Label();
$obTipoFiscalizacao->setRotulo( "Tipo de Fiscalização" );
$obTipoFiscalizacao->setName( "stTipoFiscalizacao" );
$obTipoFiscalizacao->setValue( $stTipoFiscalizacao );

//Processo Fiscal
$obProcessoFiscal = new Label();
$obProcessoFiscal->setRotulo( "Processo Fiscal" );
$obProcessoFiscal->setName( "inProcessoFiscal" );
$obProcessoFiscal->setValue( $inProcessoFiscal  );

//Inscricao Municipal
$obInscricaoMunicipal = new Label();
$obInscricaoMunicipal->setRotulo( "Inscrição Municipal" );
$obInscricaoMunicipal->setName( "stInscricaoMunicipal" );
$obInscricaoMunicipal->setValue( $inInscricaoMunicipal );

//Inscricao Economica
$obInscricaoEconomica = new Label();
$obInscricaoEconomica->setRotulo( "Inscrição Econômica" );
$obInscricaoEconomica->setName( "stInscricaoEconomica" );
$obInscricaoEconomica->setValue( $inInscricaoEconomica );

//Período de Fiscalização
$obPeriodoFiscalizacao = new Label();
$obPeriodoFiscalizacao->setRotulo( "Período de Fiscalização" );
$obPeriodoFiscalizacao->setName( "stPeriodoFiscalizacao" );
$obPeriodoFiscalizacao->setValue( $stPeriodoFiscalizacao );

//Data de Início
$obDataInicio = new Label();
$obDataInicio->setRotulo( "Data de Início" );
$obDataInicio->setName( "stDataInicio" );
$obDataInicio->setValue( $stDataInicio );

//Previsão de Encerramentoacao
$obPrevisaoEncerramento = new Label();
$obPrevisaoEncerramento->setRotulo( "Previsão de Encerramento" );
$obPrevisaoEncerramento->setName( "stPrevisaoEncerramento" );
$obPrevisaoEncerramento->setValue( $stPrevisaoEncerramento );

//Prazo de Entrega
$obPrazoEntrega = new Label();
$obPrazoEntrega->setRotulo( "Data Limite para Entrega" );
$obPrazoEntrega->setName( "stPrazoEntrega" );
$obPrazoEntrega->setValue( $stPrazoEntrega );

//Observações
$obObservacao = new Textarea;
$obObservacao->setRotulo( "Observações" );
$obObservacao->setName( "stObservacao" );
$obObservacao->setId( "stObservacao" );
$obObservacao->setTitle( "Informe as Observações." );
$obObservacao->setNull( false );

//Termo de Entrega
$obTermoEntrega = new ITextBoxSelectDocumento;
$obTermoEntrega->setCodAcao(Sessao::read('acao'));
$obTermoEntrega->obTextBoxSelectDocumento->setNull( false );
$obTermoEntrega->obTextBoxSelectDocumento->setRotulo( "Termo de Entrega" );
$obTermoEntrega->obTextBoxSelectDocumento->setName( "stCodDocumento" );
$obTermoEntrega->obTextBoxSelectDocumento->setTitle( "Selecione o Termo de Entrega." );
$obTermoEntrega->obTextBoxSelectDocumento->obTextBox->setSize( 10 );
$obTermoEntrega->obTextBoxSelectDocumento->obSelect->setStyle( "width: 261px;" );

//Monta o formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnStDescricao );
$obFormulario->addHidden( $obHdnInTipoFiscalizacao );
$obFormulario->addHidden( $obHdnInCodProcesso );
$obFormulario->addHidden( $obHdnInCodFiscal );
$obFormulario->addHidden( $obHdnDataEntrega );
$obFormulario->addHidden( $obHdnInSequencia );

//Inscrição
switch ($_REQUEST['inTipoFiscalizacao']) {
    case "1"://Econômica
        $obHdnInInscricaoEconomica = new Hidden();
        $obHdnInInscricaoEconomica->setName( "inIncricaoEconomica" );
        $obHdnInInscricaoEconomica->setValue( $_REQUEST["inInscricao"] );
        $obFormulario->addHidden( $obHdnInInscricaoEconomica );

    break;

    case "2"://Municipal
        $obHdnInInscricaoMunicipal = new Hidden();
        $obHdnInInscricaoMunicipal->setName( "inInscricaoMunicipal" );
        $obHdnInInscricaoMunicipal->setValue( $_REQUEST["inInscricao"] );
        $obFormulario->addHidden( $obHdnInInscricaoMunicipal );
    break;
}

//Monta formulário
$obFormulario->addTitulo( "Dados para Recepção de Documentos" );
$obFormulario->addComponente( $obTipoFiscalizacao );
$obFormulario->addComponente( $obProcessoFiscal );

//Inscrição
switch ($_REQUEST['inTipoFiscalizacao']) {
    case "1"://Econômica
        $obFormulario->addComponente( $obInscricaoEconomica );
    break;

    case "2"://Municipal

        $obFormulario->addComponente( $obInscricaoMunicipal );
    break;
}

$listaDocumentos = $obVisao->getListaDocumentos( $_REQUEST['inCodProcesso'] ) ;

//Monta Lista de Documentos

$tableListaDocumentos = new Table();
$tableListaDocumentos->setSummary( "Lista de Documentos" );
$tableListaDocumentos->setRecordset( $listaDocumentos );
//$tableListaDocumentos->setConditional( true , "#ddd" );
$tableListaDocumentos->Head->addCabecalho( 'Documento', 100, '' );
$tableListaDocumentos->Head->addCabecalho( 'Entregues', 10, '' );
$tableListaDocumentos->Body->addCampo( 'nom_documento' , 'E','' );
$tableListaDocumentos->Body->addCampo( 'check', 'C', '');
$tableListaDocumentos->montaHTML();

$obListaDocumentos = $tableListaDocumentos->getHTML();

$obListaDocumentos = str_replace( "\n", "", $obListaDocumentos );
$obListaDocumentos = str_replace( "  ", "", $obListaDocumentos );
$obListaDocumentos = str_replace( "'", "\\'", $obListaDocumentos);

//Span
$obSpanListaDocumentos = new Span;
$obSpanListaDocumentos->setValue( $obListaDocumentos );

$obFormulario->addComponente( $obPeriodoFiscalizacao );
$obFormulario->addComponente( $obDataInicio );
$obFormulario->addComponente( $obPrevisaoEncerramento );
$obFormulario->addComponente( $obPrazoEntrega );
$obFormulario->addComponente( $obObservacao );
$obTermoEntrega->geraFormulario( $obFormulario );
$obFormulario->addSpan( $obSpanListaDocumentos );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
