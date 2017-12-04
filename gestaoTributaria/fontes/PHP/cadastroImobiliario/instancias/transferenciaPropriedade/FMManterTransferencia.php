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
    * Página de formulário para o cadastro de transferência de proipriedade
    * Data de Criação   : 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini
                             Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.24  2006/10/03 14:54:36  cercato
comentando echo que exibia uma consulta.

Revision 1.23  2006/10/02 09:06:00  domluc
#7030#

Revision 1.22  2006/09/18 10:31:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNaturezaTransferencia.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php"         );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMProprietario.class.php"         );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMAdquirente.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"          );
include_once( CAM_GT_CIM_NEGOCIO . "RCIMImovel.class.php"        );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php"              );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );
include_once ( $pgOcul );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = 'incluir';
}

$obRCIMNaturezaTransferencia = new RCIMNaturezaTransferencia;
$obRCIMTransferencia         = new RCIMTransferencia;
$obRCIMConfiguracao          = new RCIMConfiguracao;
$obRCIMImovel                = new RCIMImovel (new RCIMLote);
$obRCIMProprietario          = new RCIMProprietario ( $obRCIMImovel );
$obRCGM                      = new RCGM;
$obRCIMAdquirente            = new RCIMAdquirente;
$rsDescricaoNatureza         = new Recordset;

if ($_REQUEST['inInscricaoMunicipal']) {
    $inIM =$_REQUEST['inInscricaoMunicipal'];
    $obRCIMTransferencia->inInscricaoMunicipal = $inIM;
    $obRCIMTransferencia->listarTransferencia($rsListaTransf);
    if ( $rsListaTransf->getNumLinhas() == 1 ) {
        $inCodTransf = $rsListaTransf->getCampo('cod_transferencia');
        $stAcao = 'alterar';
    }
}

// veriicar permiessao nao acao alterar imovel
$sql = " select * from administracao.acao inner join administracao.permissao on permissao.cod_acao = acao.cod_acao
          where acao.cod_acao= 739 and numcgm = ".Sessao::read('numCgm')." and ano_exercicio = '".Sessao::getExercicio()."'" ;

$obConexao   = new Conexao;
$rsRecordSet = new RecordSet;
$obConexao->executaSQL( $rsRecordSet, $sql, $boTransacao );

Sessao::remove('Documentos');
Sessao::remove('Adquirentes');

$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$arProcesso = preg_split( "/[^a-zA-Z0-9]/", $stMascaraProcesso );
if ($_REQUEST["inProcesso"] != "") {

    $stProcesso = str_pad( $_REQUEST["inProcesso"], strlen( $arProcesso[0] ), "0", STR_PAD_LEFT );
    $stSeparador = preg_replace( "/[a-zA-Z0-9]/","", $stMascaraProcesso );
    $stProcesso .= $stSeparador.$_REQUEST["inExercicioProc"];
}

$obRCIMNaturezaTransferencia->listarNaturezaTransferencia( $rsDescricaoNatureza );

/* Caso nao seja incluir */
if ($stAcao != "incluir" && !$_REQUEST['boItbi']) {
    $inCodigoTransferencia = $_REQUEST["inCodigoTransferencia"] ? $_REQUEST["inCodigoTransferencia"] : $inCodTransf;
    $obRCIMTransferencia->setCodigoTransferencia( $inCodigoTransferencia );
    $obRCIMTransferencia->consultarTransferencia();

    $inInscricaoImobiliaria = $obRCIMTransferencia->getInscricaoMunicipal();
    $inInscricaoMascara     = $obRCIMTransferencia->getInscricaoMunicipal();
    $inCodigoNatureza       = $obRCIMTransferencia->getCodigoNatureza();
    $inProcesso             = $obRCIMTransferencia->getProcesso();
    $stExercicioProcesso    = $obRCIMTransferencia->getExercicioProcesso();
    $_REQUEST['stCreci']    = $obRCIMTransferencia->obRCIMCorretagem->getRegistroCreci();
    $_REQUEST['stNomeCreci'] = $obRCIMTransferencia->obRCIMCorretagem->getNomCgmCreci();
    $stCodigoMatricula      = $obRCIMTransferencia->getMatriculaRegImov();

    $obRCIMNaturezaTransferencia->setCodigoNatureza( $inCodigoNatureza );
    $obRCIMNaturezaTransferencia->consultarNaturezaTransferencia();
    $stNaturezaTransferencia = $obRCIMNaturezaTransferencia->getDescricaoNatureza();

    $obLblTransferencia = new Label;
    $obLblTransferencia->setRotulo ( "Código" );
    $obLblTransferencia->setValue  ( $inCodigoTransferencia );
    SistemaLegado::BloqueiaFrames(true,false);
    SistemaLegado::executaFramePrincipal( "buscaValor('MontarListasBlok');" );
}else
if ($stAcao != "incluir") {
    $inCodigoTransferencia = $_REQUEST["inCodigoTransferencia"] ? $_REQUEST["inCodigoTransferencia"] : $inCodTransf;
    $obRCIMTransferencia->setCodigoTransferencia( $inCodigoTransferencia );
    $obRCIMTransferencia->consultarTransferencia();

    if ( !$obRCIMTransferencia->getInscricaoMunicipal() ) {
        $stAcao = "incluir";

        $obRCIMImovel->setNumeroInscricao( $_REQUEST['inInscricaoMunicipal']);
        $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );
        $arProprietarios = array();
        $inCont = 0;
        while (!$rsProrprietarios->eof()) {
            $inNumCgm   = $rsProrprietarios->getCampo("numcgm"   );
            $flQuota    = $rsProrprietarios->getCampo("cota"     );
            $obRCGM->setNumCGM  ($inNumCgm  );
            $obRCGM->consultar  ( $rsCGM    );
            $arProprietarios[$inCont][ 'inSeq'   ] = $inCont     ;
            $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm   ;
            $arProprietarios[$inCont][ 'nome'    ] = $obRCGM->getNomCGM();
            $arProprietarios[$inCont][ 'quota'   ] = $flQuota;
            $rsProrprietarios->proximo();
            $inCont++;

            }
        $rsProprietarios = new Recordset;
        $rsProprietarios->preenche($arProprietarios);
        $stJs .=  ListaProprietarios        ( $rsProprietarios , false     );

        SistemaLegado::executaFrameOculto($stJs);

    } else {
        $inCodigoTransferencia = $_REQUEST["inCodigoTransferencia"] ? $_REQUEST["inCodigoTransferencia"] : $inCodTransf;
        $obRCIMTransferencia->setCodigoTransferencia( $inCodigoTransferencia );
        $obRCIMTransferencia->consultarTransferencia();

        $inInscricaoImobiliaria = $obRCIMTransferencia->getInscricaoMunicipal();
        $inInscricaoMascara     = $obRCIMTransferencia->getInscricaoMunicipal();
        $inCodigoNatureza       = $obRCIMTransferencia->getCodigoNatureza();
        $inProcesso             = $obRCIMTransferencia->getProcesso();
        $stExercicioProcesso    = $obRCIMTransferencia->getExercicioProcesso();
        $stCodigoMatricula      = $obRCIMTransferencia->getMatriculaRegImov();

        $_REQUEST['stCreci']    = $obRCIMTransferencia->obRCIMCorretagem->getRegistroCreci();
        $_REQUEST['stNomeCreci'] = $obRCIMTransferencia->obRCIMCorretagem->getNomCgmCreci();
        $obRCIMNaturezaTransferencia->setCodigoNatureza( $inCodigoNatureza );
        $obRCIMNaturezaTransferencia->consultarNaturezaTransferencia();
        $stNaturezaTransferencia = $obRCIMNaturezaTransferencia->getDescricaoNatureza();

        $obLblTransferencia = new Label;
        $obLblTransferencia->setRotulo ( "Código" );
        $obLblTransferencia->setValue  ( $inCodigoTransferencia );
        SistemaLegado::BloqueiaFrames(true,false);
        SistemaLegado::executaFramePrincipal( "buscaValor('MontarListasBlok');" );
    }
    $inInscricaoMascara = $_REQUEST["inInscricaoMunicipal"];
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                          ( "stAcao"                                    );
$obHdnAcao->setValue                         ( $_REQUEST['stAcao']                         );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                          ( "stCtrl"                                    );
$obHdnCtrl->setValue                         ( $_REQUEST['stCtrl']                         );

$obHdnItbi = new Hidden;
$obHdnItbi->setName                          ( "boItbi"                                    );
$obHdnItbi->setValue                         ( $_REQUEST['boItbi']                         );

$obHdnCodigoTransferencia =  new Hidden;
$obHdnCodigoTransferencia->setName           ( "inCodigoTransferencia"                     );
$obHdnCodigoTransferencia->setValue          ( $inCodigoTransferencia                      );

$obHdnCodigoMatricula = new Hidden;
$obHdnCodigoMatricula->setName               ( "stCodigoMatricula"                         );
$obHdnCodigoMatricula->setValue              ( $inCodigoMatricula                          );

$obHdnInscricaoImobiliaria =  new Hidden;
$obHdnInscricaoImobiliaria->setName          ( "inInscricaoImobiliaria"                    );
$obHdnInscricaoImobiliaria->setValue         ( $inInscricaoImobiliaria                     );

$obHdnDataCadastro =  new Hidden;
$obHdnDataCadastro->setName                  ( "hdnDataCadastro"                           );
$obHdnDataCadastro->setValue                 ( $hdnDataCadastro                            );

$obLblInscricaoImobiliaria = new Label;
$obLblInscricaoImobiliaria->setRotulo        ( "Inscrição Imobiliária"                     );
$obLblInscricaoImobiliaria->setValue         ( $inInscricaoMascara                         );

$obHdnInscricaoMascara = new Hidden;
$obHdnInscricaoMascara->setName        ( "inInscricaoMascara"                     );
$obHdnInscricaoMascara->setValue         ( $inInscricaoMascara                         );

$obHdnCodigoNatureza =  new Hidden;
$obHdnCodigoNatureza->setName                ( "inCodigoNatureza"                          );
$obHdnCodigoNatureza->setValue               ( $inCodigoNatureza                           );

$obHdnCampoNumDom = new Hidden;
$obHdnCampoNumDom->setName( "stNumeroDomicilio" );
$obHdnCampoNumDom->setID  ( "stNumeroDomicilio" );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName("campoNum");
$obHdnCampoNum->setValue($_REQUEST['campoNum']);

$obLblNatureza = new Label;
$obLblNatureza->setRotulo                    ( "Natureza da Transferência"                 );
$obLblNatureza->setValue                     ( $stNaturezaTransferencia                    );

$obLblProcesso = new Label;
$obLblProcesso->setRotulo                    ( "Processo"                                  );
$obLblProcesso->setValue                     ( $stProcesso                     );

$obLblCreci = new Label;
$obLblCreci->setRotulo                       ( "Creci"                                     );
$obLblCreci->setValue                        ( $_REQUEST['stCreci']." - ".$_REQUEST['stNomeCreci']                 );

$obLblMatricula = new Label;
$obLblMatricula->setRotulo                   ( "Matrícula no Registro de Imóveis"          );
$obLblMatricula->setValue                    ( $stCodigoMatricula                          );

$obTxtData = new Data;
$obTxtData->setRotulo                        ( "Data da Efetivação"                        );
$obTxtData->setName                          ( "stDataEfetivacao"                          );
$obTxtData->setId                            ( "stDataEfetivacao"                          );
$obTxtData->setValue                         ( $hdnDataCadastro                            );
$obTxtData->setSize                          ( 10                                          );
$obTxtData->setMaxLength                     ( 10                                          );
$obTxtData->setNull                          ( false                                       );
$obTxtData->obEvento->setOnChange            ( "javascript: buscaValor( 'validaData' );"   );

$obTxtJustificativa = new TextArea;
$obTxtJustificativa->setRotulo               ( "Observações"                               );
$obTxtJustificativa->setTitle                ( "Observações"                               );
$obTxtJustificativa->setName                 ( "stJustificativa"                           );
$obTxtJustificativa->setNull                 ( true                                        );

$obHdnExercicioProcesso =  new Hidden;
$obHdnExercicioProcesso->setName             ( "stExercicioProcesso"                       );
$obHdnExercicioProcesso->setValue            ( $stExercicioProcesso                        );

$obHdnNumCGMCreci =  new Hidden;
$obHdnNumCGMCreci->setName                   ( "inNumCGMCreci"                             );
$obHdnNumCGMCreci->setValue                  ( $inNumCGMCreci                              );

$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull            ( false                                       );
$obBscInscricaoMunicipal->setRotulo          ( "Inscrição Imobiliária"                     );
$obBscInscricaoMunicipal->setTitle           ( "Inscrição imobiliária que será transferida");
$obBscInscricaoMunicipal->obCampoCod->setName( "inInscricaoImobiliaria"                    );
$obBscInscricaoMunicipal->obCampoCod->setId  ( "inInscricaoImobiliaria"                    );
$obBscInscricaoMunicipal->obCampoCod->setSize ( strlen($stMascaraInscricao)                 );
$obBscInscricaoMunicipal->obCampoCod->setValue( $inInscricaoMascara                 );
$obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)           );
$obBscInscricaoMunicipal->obCampoCod->obEvento->setOnChange ( "BloqueiaFrames(true,false);buscaValor('buscaInscricao');" );
//$obBscInscricaoMunicipal->obCampoCod->obEvento->setOnBlur   ( "buscaValor('buscaInscricao');" );
$obBscInscricaoMunicipal->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inInscricaoImobiliaria','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');");

$obTxtCodigoNatureza = new TextBox;
$obTxtCodigoNatureza->setRotulo              ( "Natureza da Transferência"                  );
$obTxtCodigoNatureza->setName                ( "inCodigoNatureza"                          );
$obTxtCodigoNatureza->setValue               ( $inCodigoNatureza                           );
$obTxtCodigoNatureza->setSize                ( 10                                          );
$obTxtCodigoNatureza->setMaxLength           ( 10                                          );
$obTxtCodigoNatureza->setInteiro             ( true                                        );
$obTxtCodigoNatureza->setNull                ( false                                       );
$obTxtCodigoNatureza->obEvento->setOnChange  ( "buscaValor('ListaDocumentos');"            );

$obCmbCodigoNatureza = new Select;
$obCmbCodigoNatureza->setName                ( "inCodigoNaturezaTxt"                       );
$obCmbCodigoNatureza->setValue               ( $inCodigoNatureza                           );
$obCmbCodigoNatureza->setStyle               ( "width: 340px"                              );
$obCmbCodigoNatureza->setCampoID             ( "cod_natureza"                              );
$obCmbCodigoNatureza->setCampoDesc           ( "descricao"                                 );
$obCmbCodigoNatureza->addOption              ( "", "Selecione"                             );
$obCmbCodigoNatureza->setNull                ( false                                       );
$obCmbCodigoNatureza->preencheCombo          ( $rsDescricaoNatureza                        );
$obCmbCodigoNatureza->obEvento->setOnChange  ( "buscaValor('ListaDocumentos');"            );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo do protocolo referente ao pedido de transferência" );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
if ($stAcao != "incluir") {
    $obBscProcesso->obCampoCod->setValue( $stProcesso );
    $obBscProcesso->obCampoCod->setPreencheComZeros( true );
}
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setNull ( false );
$obBscProcesso->obCampoCod->setInteiro ( false );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->obCampoCod->obEvento->setOnChange("buscaValor('buscaProcesso');" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obBscCreci = new BuscaInner;
$obBscCreci->setRotulo                       ( "CRECI"                                     );
$obBscCreci->setTitle                        ( "CRECI do corretor responsável pela transferência" );
$obBscCreci->setNull                         ( true                                        );
$obBscCreci->setId                           ( "stNomeCreci"                               );
$obBscCreci->setValue                        ( $_REQUEST['stNomeCreci']                       );
$obBscCreci->obCampoCod->setName             ( "stCreci"                                   );
$obBscCreci->obCampoCod->setInteiro          ( false                                       );
$obBscCreci->obCampoCod->setSize             ( 10                                          );
$obBscCreci->obCampoCod->setMaxLength        ( 10                                          );
$obBscCreci->obCampoCod->setValue            ( $_REQUEST["stCreci"]                        );
$obBscCreci->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreci');"                 );
$obBscCreci->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."corretagem/FLProcurarCorretagem.php','frm','stCreci'
                             ,'stNomeCreci','todos','".Sessao::getId()."','800','550')"        );

$obSpnDocumentos = new Span;
$obSpnDocumentos->setId                      ( "spnDocumentosNatureza"                     );

if ($stAcao == "incluir" or $stAcao == "alterar") {
    $obBscCGM = new BuscaInner;
    $obBscCGM->setRotulo                     ( "*CGM"                                      );
    $obBscCGM->setTitle                      ( "Procura por um CGM para adicionar como adquirente" );
    $obBscCGM->setNull                       ( true                                        );
    $obBscCGM->setId                         ( "campoInner"                                );
    $obBscCGM->obCampoCod->setName           ( "inNumCGM"                                  );
    $obBscCGM->obCampoCod->setValue          ( $inNumCGM                                   );
    $obBscCGM->obCampoCod->obEvento->setOnBlur( "buscaValor('buscaCGM');"                  );
    $obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','geral','".Sessao::getId()."','800','550')" );

    $obTxtQuota = new Numerico;
    $obTxtQuota->setName                     ( "nuQuota"                                   );
    $obTxtQuota->setSize                     ( 6                                           );
    $obTxtQuota->setMaxLength                ( 6                                           );
    $obTxtQuota->setInteiro                  ( true                                        );
    $obTxtQuota->setNull                     ( true                                        );
    $obTxtQuota->setRotulo                   ( "*Quota"                                    );
    $obTxtQuota->setValue                    ( $nuQuota                                    );
    $obTxtQuota->setTitle                    ( "Percentagem de participação do adquirente" );
    $obTxtQuota->obEvento->setOnKeyUp        ( "mascaraDinamico('999.99', this, event);"   );

    $obLblPercentual = new Label;
    $obLblPercentual->setValue               ( '%'                                                          );

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName                   ( "btnIncluirAdquirentes"                     );
    $obBtnIncluir->setValue                  ( "Incluir"                                   );
    $obBtnIncluir->obEvento->setOnClick      ( "return incluirAdquirentes();");

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName                    ( "btnLimparAdquirentes"                                       );
    $obBtnLimpar->setValue                   ( "Limpar"                                    );
    $obBtnLimpar->obEvento->setOnClick       ( "limparAdquirentes();"                      );
}
/* Lucas Stephanou - 23/03/2005*/

/* Adiciona Span para lista de Atuais Adquirintes */

$obSpnProprietarios = new Span;
$obSpnProprietarios->setId                   ( "spnProprietarios"                          );

$obSpnAdquirentes = new Span;
$obSpnAdquirentes->setId                     ( "spnAdquirentes"                            );

$obSpnCheckAvalia = new Span;
$obSpnCheckAvalia->setId                     ( "spnCheckAvalia"                            );

/* Lucas Stephanou - 11/07/2005 */

/* Checkbox para efetuar mudança de endereço do imovel*/

$obChkSeguir = new Checkbox;
$obChkSeguir->setName   ( "boSeguir"    );
$obChkSeguir->setRotulo ( " &nbsp;"     );
$obChkSeguir->setLabel  ( "Seguir para formulário de alteração de imóvel" );

$obBtnOk = new Ok;

$obBtnLimparFormulario = new Button;
$obBtnLimparFormulario->setName              ( "btnLimparFormulario"                       );
$obBtnLimparFormulario->setValue             ( "Limpar"                                    );
$obBtnLimparFormulario->obEvento->setOnClick ( "limparFormulario();"                       );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFomulario = new Formulario;
$obFomulario->addForm                   ( $obForm                                          );
$obFomulario->setAjuda ( "UC-05.01.17" );
$obFomulario->addTitulo                 ( "Dados para transferência"                       );
$obFomulario->addHidden                 ( $obHdnAcao                                       );
$obFomulario->addHidden                 ( $obHdnCtrl                                       );
$obFomulario->addHidden                 ( $obHdnItbi                                       );
$obFomulario->addHidden                 ( $obHdnCodigoTransferencia                        );
$obFomulario->addHidden                 ( $obHdnCodigoMatricula                            );
$obFomulario->addHidden                 ( $obHdnCampoNumDom );
$obFomulario->addHidden                 ( $obHdnCampoNum );

if ($stAcao == "alterar") {
    $obFomulario->addHidden             ( $obHdnInscricaoImobiliaria                       );
    $obFomulario->addHidden             ( $obHdnInscricaoMascara                           );
    $obFomulario->addHidden             ( $obHdnCodigoNatureza                             );
    $obFomulario->addHidden             ( $obHdnExercicioProcesso                          );
    $obFomulario->addHidden             ( $obHdnNumCGMCreci                                );
    $obFomulario->addHidden             ( $obHdnDataCadastro                               );
    $obFomulario->addComponente         ( $obLblInscricaoImobiliaria                       );
    $obFomulario->addComponente         ( $obLblNatureza                                   );
    $obFomulario->addComponente         ( $obBscProcesso                                   );
    $obFomulario->addComponente         ( $obBscCreci                                      );
} elseif ($stAcao == "efetivar" or $stAcao == "cancelar") {
    $obFomulario->addHidden             ( $obHdnInscricaoImobiliaria                       );
    $obFomulario->addHidden             ( $obHdnCodigoNatureza                             );
    $obFomulario->addHidden             ( $obHdnExercicioProcesso                          );
    $obFomulario->addHidden             ( $obHdnDataCadastro                               );
    $obFomulario->addComponente         ( $obLblInscricaoImobiliaria                       );
    $obFomulario->addComponente         ( $obLblNatureza                                   );
    $obFomulario->addComponente         ( $obLblProcesso                                   );
    $obFomulario->addComponente         ( $obLblCreci                                      );
    $obFomulario->addComponente         ( $obLblMatricula                                  );
    $obFomulario->addComponente         ( $obTxtData                                       );
    $obFomulario->addComponente         ( $obTxtJustificativa                              );
} else {
    $obFomulario->addHidden             ( $obHdnExercicioProcesso                          );
    $obFomulario->addHidden             ( $obHdnNumCGMCreci                                );
    $obFomulario->addComponente         ( $obBscInscricaoMunicipal                         );
    $obFomulario->addComponenteComposto ( $obTxtCodigoNatureza, $obCmbCodigoNatureza       );
    $obFomulario->addComponente         ( $obBscProcesso                                   );
    $obFomulario->addComponente         ( $obBscCreci                                      );
}

$obFomulario->addSpan                   ( $obSpnProprietarios                               );
$obFomulario->addSpan                   ( $obSpnDocumentos                                );
if ($stAcao == "incluir" or $stAcao == "alterar") {
    $obFomulario->addTitulo             ( "Adquirentes"                                    );
    $obFomulario->addComponente         ( $obBscCGM                                        );
    $obFomulario->agrupaComponentes     ( array( $obTxtQuota, $obLblPercentual )           );
    $obFomulario->defineBarraAba           ( array( $obBtnIncluir, $obBtnLimpar ), "left", "" );
}

$obFomulario->addSpan                   (  $obSpnAdquirentes                                );
if ( $stAcao == "efetivar"  &&  verificaPermissaoUsuario() ) {
    if ( $rsRecordSet->getNumLinhas() > 0)
        $obFomulario->addComponente      ( $obChkSeguir           );
}

if ($stAcao == "incluir") {
    $obFomulario->defineBarra           ( array( $obBtnOk, $obBtnLimparFormulario )        );
} else {
//    if ($stAcao == 'alterar') {
  //      $obFomulario->addSpan               (  $obSpnCheckAvalia                                );
//    }
    $obFomulario->Cancelar();
}

if ($stAcao == "incluir") {
    $obFomulario->setFormFocus( $obBscInscricaoMunicipal->obCampoCod->getid() );
} elseif ($stAcao == "alterar") {
    $obFomulario->setFormFocus( $obBscProcesso->obCampoCod->getid() );
} elseif ($stAcao == "efetivar") {
    $obFomulario->setFormFocus( $obTxtData->getid() );
}

$obFomulario->show();

?>
