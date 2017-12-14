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
    * Data de Criação: 08/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GT_FIS_NEGOCIO."RFISProrrogarRecebimentoDocumentos.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISProrrogarRecebimentoDocumentos.class.php" );

//Instanciando a Classe de Controle e de Visao
$obController = new RFISProrrogarRecebimentoDocumentos;
$obVisao = new VFISProrrogarRecebimentoDocumentos( $obController );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Define o nome dos arquivos PHP
$stPrograma = "ProrrogarRecebimentoDocumentos";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

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

//Valores da Regra de Negócio
$stTipoFiscalizacao = $obRsProcesso->arElementos[0]['cod_tipo']. " - " . $obRsProcesso->arElementos[0]['descricao'];
$inProcessoFiscal = $obRsProcesso->arElementos[0]['cod_processo'];

if ($obRsProcesso->arElementos[0]['dt_prorrogada'] != "") {
    $stPrazoEntrega = $obRsProcesso->arElementos[0]['dt_prorrogada'];
} else {
    $stPrazoEntrega = $obRsProcesso->arElementos[0]['prazo_entrega'];
}

//Cria um novo formulario
$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
//$obForm->setTarget( "tela_principal" );

#### Campos Hidden ####

//stAcao
$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//stCtrl
$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "prorrogarRecebimentoDocumentos" );

//Cod. Tipo Fiscalização
$obHdnInTipoFiscalizacao = new Hidden();
$obHdnInTipoFiscalizacao->setName( "inTipoFiscalizacao" );
$obHdnInTipoFiscalizacao->setValue( $_REQUEST["inTipoFiscalizacao"] );

//Cod. Processo Fiscal
$obHdnInCodProcesso = new Hidden();
$obHdnInCodProcesso->setName( "inCodProcesso" );
$obHdnInCodProcesso->setValue( $_REQUEST["inCodProcesso"] );

//Cod. Fiscal
$obHdnInCodFiscal = new Hidden();
$obHdnInCodFiscal->setName( "inCodFiscal" );
$obHdnInCodFiscal->setValue( $_REQUEST["inCodFiscal"] );

//Data Anterior
$obHdnDataAnterior = new Hidden();
$obHdnDataAnterior->setName( "inDtAnterior" );
$obHdnDataAnterior->setValue( $stPrazoEntrega );

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

//Prazo de Entrega
$obPrazoEntrega = new Label();
$obPrazoEntrega->setRotulo( "Prazo para Entrega" );
$obPrazoEntrega->setName( "stPrazoEntrega" );
$obPrazoEntrega->setValue( $stPrazoEntrega );

//Inscricao Economica
$obInscricaoEconomica = new Label();
$obInscricaoEconomica->setRotulo( "Inscrição Econômica" );
$obInscricaoEconomica->setName( "stInscricaoEconomica" );
$obInscricaoEconomica->setValue( $inInscricaoEconomica );

//Inscricao Municipal
$obInscricaoMunicipal = new Label();
$obInscricaoMunicipal->setRotulo( "Inscrição Imobiliária" );
$obInscricaoMunicipal->setName( "stInscricaoMunicipal" );
$obInscricaoMunicipal->setValue( $inInscricaoMunicipal );

//Prorragação de Entrega
$obProrrogacaoEntrega = new Data;
$obProrrogacaoEntrega->setName( "dtProrrogacaoEntrega" );
$obProrrogacaoEntrega->setId( "dtProrrogacaoEntrega" );
$obProrrogacaoEntrega->setSize( "8" );
$obProrrogacaoEntrega->setRotulo( "Prorragação de Entrega" );
$obProrrogacaoEntrega->setTitle( "Informe a Data para Prorragação de Entrega." );
$obProrrogacaoEntrega->setNull( false );

//Monta o formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnInTipoFiscalizacao );
$obFormulario->addHidden( $obHdnInCodProcesso );
$obFormulario->addHidden( $obHdnInCodFiscal );
$obFormulario->addHidden( $obHdnDataAnterior );

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

$obFormulario->addTitulo( "Dados para Prorrogação" );
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

$obFormulario->addComponente( $obPrazoEntrega );
$obFormulario->addComponente( $obProrrogacaoEntrega );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
