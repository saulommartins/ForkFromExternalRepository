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
    * Página de formulário para o Lançamento do Imposto de Transferência
    * Data de Criação   : 03/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FMLancarTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Caso de uso: uc-05.03.21
*/

/*
$Log$
Revision 1.7  2007/09/06 12:42:42  cercato
Ticket#10058#

Revision 1.6  2007/09/04 13:15:47  vitor
Ticket#10058#

Revision 1.5  2007/04/16 18:06:22  cercato
Bug #9132#

Revision 1.4  2007/04/11 14:43:18  rodrigo
Bug #8968#

Revision 1.3  2006/10/18 10:34:01  cercato
correcoes para o itbi.

Revision 1.2  2006/10/16 11:59:42  cercato
utilizando funcao para retornar valores calculados nao nulos por ordem de timestamp.

Revision 1.1  2006/10/10 15:17:57  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNaturezaTransferencia.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRLancamentoTransferencia.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMProprietario.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );

;
//Define o nome dos arquivos PHP
$stPrograma = "LancarTransferencia";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = 'incluir';
}

$obRARRLancamentoTransferencia = new RARRLancamentoTransferencia;
$obRCIMNaturezaTransferencia = new RCIMNaturezaTransferencia;
$obRCIMTransferencia         = new RCIMTransferencia;
$obRCIMConfiguracao          = new RCIMConfiguracao;
$obRCIMImovel        = new RCIMImovel (new RCIMLote);
$obRCIMProprietario  = new RCIMProprietario ( $obRCIMImovel );
$obRCGM              = new RCGM;

Sessao::write( 'Documentos', array() );
Sessao::write( 'Adquirentes', array() );

if ($_REQUEST['inInscricaoMunicipal']) {
    $inInscricao = $_REQUEST['inInscricaoMunicipal'];
}

//recuperando o codigo de transferencia do imovel
$obRCIMTransferencia->inInscricaoMunicipal = $inInscricao;
//traz a transferência somente se ela não foi efetivada ou cancelada
$obRCIMTransferencia->setEfetivacao('t');
$obRCIMTransferencia->listarTransferencia($rsListaTransf);

if ( $rsListaTransf->getNumLinhas() == 1 ) {
    $inCodTransf = $rsListaTransf->getCampo('cod_transferencia');
    $stNomeCreci = $rsListaTransf->getCampo('creci');
    $stCreci = $rsListaTransf->getCampo('numcgm_creci');
    $inCodigoNatureza = $rsListaTransf->getCampo('cod_natureza');
    $inProcesso = $rsListaTransf->getCampo('cod_processo');
    $stExercicioProcesso = $rsListaTransf->getCampo('exercicio_proc');
}

//listando avaliacao imobiliaria do imovel escolhido
$obRARRAvaliacaoImobiliaria = new RARRAvaliacaoImobiliaria;
$obRARRAvaliacaoImobiliaria->obRCIMImovel->setNumeroInscricao( $inInscricao );
$obRARRAvaliacaoImobiliaria->setExercicio( Sessao::getExercicio() );
$obRARRAvaliacaoImobiliaria->listarAvaliacoes( $rsAvaliacao );
$obRARRAvaliacaoImobiliaria->listarAvaliacaoCalculadoNaoNulo( $rsAvaliacaoCalculada );

$boPossuiFinanciamento = false;

if ( $rsAvaliacao->getNumLinhas() > 0 ) {
    $flTerritorialCalculado = number_format( $rsAvaliacaoCalculada->getCampo( 'venal_territorial_calculado' ), 2, ',', '.' );
    $flPredialCalculado     = number_format( $rsAvaliacaoCalculada->getCampo( 'venal_predial_calculado'), 2, ',', '.' );
    $flTotalCalculado       = number_format( $rsAvaliacaoCalculada->getCampo( 'venal_total_calculado'), 2, ',', '.' );

    $flTerritorialDeclarado = number_format( $rsAvaliacao->getCampo( 'venal_territorial_declarado' ), 2, ',', '.' );
    $flPredialDeclarado     = number_format( $rsAvaliacao->getCampo( 'venal_predial_declarado'), 2, ',', '.' );
    $flTotalDeclarado       = number_format( $rsAvaliacao->getCampo( 'venal_total_declarado'), 2, ',', '.' );

    $flTerritorialAvaliado = number_format( $rsAvaliacao->getCampo( 'venal_territorial_avaliado' ), 2, ',', '.' );
    $flPredialAvaliado     = number_format( $rsAvaliacao->getCampo( 'venal_predial_avaliado'), 2, ',', '.' );
    $flTotalAvaliado       = $rsAvaliacao->getCampo( 'venal_total_avaliado');

    $flTotalAliquota = $rsAvaliacao->getCampo( 'aliquota_valor_avaliado');
    $flFinanciadoAliquota = $rsAvaliacao->getCampo( 'aliquota_valor_financiado');
    $flValorFinanciado = $rsAvaliacao->getCampo( 'valor_financiado');

    $flTotalAvaliado1 = number_format($flTotalAvaliado, 2, ',', '.' );
    $flTotalAvaliado -= $flValorFinanciado;
    $flTotalValorImposto = $flTotalAvaliado * $flTotalAliquota;
    if ( $flTotalValorImposto )
        $flTotalValorImposto /= 100;

    $flFinanciadoImposto = $flFinanciadoAliquota * $flValorFinanciado;

    if ( $flFinanciadoImposto )
        $flFinanciadoImposto /= 100;

    if ( $rsAvaliacao->getCampo( 'valor_financiado') > 0 )
        $boPossuiFinanciamento = true;

    $flTotalCobranca = $flTotalValorImposto + $flFinanciadoImposto;

    $flTotalAliquota = number_format($flTotalAliquota, 2, ',', '.' );
    ($flTotalAliquota=="0,00")?$flTotalAliquota="0":$flTotalAliquota=$flTotalAliquota;

    $flFinanciadoAliquota = number_format($flFinanciadoAliquota, 2, ',', '.' );
    $flValorFinanciado = number_format($flValorFinanciado, 2, ',', '.' );

    $flTotalAvaliado = number_format($flTotalAvaliado, 2, ',', '.' );
    $flTotalValorImposto = number_format($flTotalValorImposto, 2, ',', '.' );
    $flFinanciadoImposto = number_format($flFinanciadoImposto, 2, ',', '.' );
    $flTotalCobranca = number_format($flTotalCobranca, 2, ',', '.' );

    $flTotalAvaliado2 = $flTotalAvaliado;
}

$obHdnCodigoTransferencia =  new Hidden;
$obHdnCodigoTransferencia->setName ( "inCodigoTransferencia" );
if (!empty($inCodTransf)) {
    $obHdnCodigoTransferencia->setValue ( $inCodTransf );
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obHdnInscricaoImobiliaria =  new Hidden;
$obHdnInscricaoImobiliaria->setName ( "inInscricaoImobiliaria" );
$obHdnInscricaoImobiliaria->setValue ( $inInscricao  );

$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setRotulo    ( "Inscrição Imobiliária" );
$obLblNumeroInscricao->setTitle     ( "Número da inscrição imobiliária" );
$obLblNumeroInscricao->setValue     ( $inInscricao );

$obTxtCodigoNatureza = new TextBox;
$obTxtCodigoNatureza->setTitle               ( "Informe a natureza da transferência." );
$obTxtCodigoNatureza->setRotulo              ( "Natureza da Transferência"                  );
$obTxtCodigoNatureza->setName                ( "inCodigoNatureza"                          );
if (!empty($inCodigoNatureza )) {
    $obTxtCodigoNatureza->setValue               ( $inCodigoNatureza                           );
}
$obTxtCodigoNatureza->setSize                ( 10                                          );
$obTxtCodigoNatureza->setMaxLength           ( 10                                          );
$obTxtCodigoNatureza->setInteiro             ( true                                        );
$obTxtCodigoNatureza->setNull                ( false                                       );
$obTxtCodigoNatureza->obEvento->setOnChange  ( "buscaValor('ListaDocumentos');"            );

$obRCIMNaturezaTransferencia->listarNaturezaTransferencia( $rsDescricaoNatureza );

$obCmbCodigoNatureza = new Select;
$obCmbCodigoNatureza->setTitle               ( "Informe a natureza da transferência." );
$obCmbCodigoNatureza->setName                ( "inCodigoNaturezaTxt"                       );
if (!empty($inCodigoNatureza )) {
    $obCmbCodigoNatureza->setValue               ( $inCodigoNatureza                           );
}
$obCmbCodigoNatureza->setStyle               ( "width: 340px"                              );
$obCmbCodigoNatureza->setCampoID             ( "cod_natureza"                              );
$obCmbCodigoNatureza->setCampoDesc           ( "descricao"                                 );
$obCmbCodigoNatureza->addOption              ( "", "Selecione"                             );
$obCmbCodigoNatureza->setNull                ( false                                       );
$obCmbCodigoNatureza->preencheCombo          ( $rsDescricaoNatureza                        );
$obCmbCodigoNatureza->obEvento->setOnChange  ( "buscaValor('ListaDocumentos');"            );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo do protocolo referente ao pedido de transferência." );
$obBscProcesso->obCampoCod->setName ("inProcesso");
if (!empty($inProcesso) && !empty($stExercicioProcesso)) {
    $obBscProcesso->obCampoCod->setValue( $inProcesso."/".$stExercicioProcesso );
}
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obBscCreci = new BuscaInner;
$obBscCreci->setRotulo                ( "CRECI"                                          );
$obBscCreci->setTitle                 ( "CRECI do corretor responsável pela transferência."      );
$obBscCreci->setNull                  ( true                                             );
$obBscCreci->setId                    ( "stNomeCreci"                              );
if (!empty($stNomeCreci)) {
    $obBscCreci->setValue                 ( $stNomeCreci );
}
if (!empty($stCreci )) {
    $obBscCreci->obCampoCod->setValue     ( $stCreci );
}
$obBscCreci->obCampoCod->setName      ( "stCreci"                             );
$obBscCreci->obCampoCod->setInteiro   ( false                                            );
$obBscCreci->obCampoCod->setSize      ( 10                                               );
$obBscCreci->obCampoCod->setMaxLength ( 10                                               );
$obBscCreci->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreci');"                );
$obBscCreci->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."corretagem/FLProcurarCorretagem.php','frm','stCreci','stNomeCreci','todos','".Sessao::getId()."','800','550')" );

$obSpnListaDocumentos = new Span;
$obSpnListaDocumentos->setId( "spnLstDoc" );

$obSpnListaProprietario = new Span;
$obSpnListaProprietario->setId( "spnProprietarios" );

$inNumCGM = isset($inNumCGM) ? $inNumCGM  : "";
$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo                     ( "*CGM" );
$obBscCGM->setTitle                      ( "Procura por um CGM para adicionar como adquirente" );
$obBscCGM->setNull                       ( true  );
$obBscCGM->setId                         ( "campoInner"  );
$obBscCGM->obCampoCod->setName           ( "inNumCGM" );
$obBscCGM->obCampoCod->setValue          ( $inNumCGM   );
$obBscCGM->obCampoCod->obEvento->setOnBlur( "buscaValor('buscaCGM');" );
$obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','geral','".Sessao::getId()."','800','550')" );

$nuQuota = isset($nuQuota) ? $nuQuota : "";
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

$obBtnIncluir = new Button;
$obBtnIncluir->setName                   ( "btnIncluirAdquirentes"                     );
$obBtnIncluir->setValue                  ( "Incluir"                                   );
$obBtnIncluir->obEvento->setOnClick      ( "return incluirAdquirentes();");

$obBtnLimpar = new Button;
$obBtnLimpar->setName                    ( "btnLimparAdquirentes"                      );
$obBtnLimpar->setValue                   ( "Limpar"                                    );
$obBtnLimpar->obEvento->setOnClick       ( "limparAdquirentes();"                      );

$obSpnAdquirentes = new Span;
$obSpnAdquirentes->setId                     ( "spnAdquirentes"                            );

$obLblEspaco = new Label;
$obLblEspaco->setRotulo( "&nbsp;" );
$obLblEspaco->setName  ( "stEspaco" );
$obLblEspaco->setValue ( "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" );

$obLblCalculados = new Label;
$obLblCalculados->setRotulo( "&nbsp;"             );
$obLblCalculados->setName  ( "stCalculados"       );
$obLblCalculados->setValue ( "Valores Calculados" );

$obLblDeclarados = new Label;
$obLblDeclarados->setRotulo( "&nbsp;"             );
$obLblDeclarados->setName  ( "stDeclarados"     );
$obLblDeclarados->setValue ( "Valores Declarados" );

$obLblAvaliados = new Label;
$obLblAvaliados->setRotulo( "&nbsp;"             );
$obLblAvaliados->setName  ( "stAvaliados"     );
$obLblAvaliados->setValue ( "Valores Avaliados" );

$obTxtTerritorialDeclarado = new Moeda;
$obTxtTerritorialDeclarado->setTitle              ( "Informe os valores venais territoriais do imóvel." );
$obTxtTerritorialDeclarado->setName               ( "flTerritorialDeclarado" );
$obTxtTerritorialDeclarado->setRotulo             ( "Valor Venal Territorial" );
$obTxtTerritorialDeclarado->setMaxLength          ( 15                );
$obTxtTerritorialDeclarado->setSize               ( 15                );
$obTxtTerritorialDeclarado->setValue              ( $flTerritorialDeclarado );
$obTxtTerritorialDeclarado->obEvento->setOnChange ( "calcularTotalDeclarado()" );

$obTxtTerritorialAvaliado = new Moeda;
$obTxtTerritorialAvaliado->setTitle              ( "Informe os valores venais territoriais do imóvel." );
$obTxtTerritorialAvaliado->setName               ( "flTerritorialAvaliado" );
$obTxtTerritorialAvaliado->setRotulo             ( "Valor Venal Territorial" );
$obTxtTerritorialAvaliado->setMaxLength          ( 15                );
$obTxtTerritorialAvaliado->setSize               ( 15                );
$obTxtTerritorialAvaliado->setValue              ( $flTerritorialAvaliado );
$obTxtTerritorialAvaliado->obEvento->setOnChange ( "buscaValor('calculaTotalAvaliado')" );

$obTxtTerritorial = new Moeda;
$obTxtTerritorial->setTitle              ( "Informe os valores venais territoriais do imóvel." );
$obTxtTerritorial->setName               ( "flTerritorial" );
$obTxtTerritorial->setRotulo             ( "Valor Venal Territorial" );
$obTxtTerritorial->setMaxLength          ( 15                );
$obTxtTerritorial->setSize               ( 15                );
$obTxtTerritorial->setValue              ( $flTerritorialCalculado );
$obTxtTerritorial->setReadOnly           ( true );
$obTxtTerritorial->setNull               ( false );

$obTxtPredialDeclarado = new Moeda;
$obTxtPredialDeclarado->setName               ( "flPredialDeclarado" );
$obTxtPredialDeclarado->setTitle              ( "Informe os valores venais prediais do imóvel." );
$obTxtPredialDeclarado->setRotulo             ( "Valor Venal Predial" );
$obTxtPredialDeclarado->setMaxLength          ( 15                );
$obTxtPredialDeclarado->setSize               ( 15                );
$obTxtPredialDeclarado->setValue              ( $flPredialDeclarado );
$obTxtPredialDeclarado->obEvento->setOnChange ( "calcularTotalDeclarado()" );

$obTxtPredialAvaliado = new Moeda;
$obTxtPredialAvaliado->setName               ( "flPredialAvaliado" );
$obTxtPredialAvaliado->setTitle              ( "Informe os valores venais prediais do imóvel." );
$obTxtPredialAvaliado->setRotulo             ( "Valor Venal Predial" );
$obTxtPredialAvaliado->setMaxLength          ( 15                );
$obTxtPredialAvaliado->setSize               ( 15                );
$obTxtPredialAvaliado->setValue              ( $flPredialAvaliado );
$obTxtPredialAvaliado->obEvento->setOnChange ( "buscaValor('calculaTotalAvaliado')" );

$obTxtPredial = new Moeda;
$obTxtPredial->setName               ( "flPredial" );
$obTxtPredial->setTitle              ( "Informe os valores venais prediais do imóvel." );
$obTxtPredial->setRotulo             ( "Valor Venal Predial" );
$obTxtPredial->setMaxLength          ( 15                );
$obTxtPredial->setSize               ( 15                );
$obTxtPredial->setValue              ( $flPredialCalculado );
$obTxtPredial->setReadOnly           ( true );
$obTxtPredial->setNull               ( false );

$obTxtTotalDeclarado = new Moeda;
$obTxtTotalDeclarado->setName               ( "flTotalDeclarado" );
$obTxtTotalDeclarado->setRotulo             ( "Valor Venal Total" );
$obTxtTotalDeclarado->setMaxLength          ( 15                );
$obTxtTotalDeclarado->setSize               ( 15                );
$obTxtTotalDeclarado->setValue              ( $flTotalDeclarado );
$obTxtTotalDeclarado->setReadOnly           ( true );

$obTxtTotalAvaliado = new Moeda;
$obTxtTotalAvaliado->setName               ( "flTotalAvaliado" );
$obTxtTotalAvaliado->setRotulo             ( "Valor Venal Total" );
$obTxtTotalAvaliado->setMaxLength          ( 15                );
$obTxtTotalAvaliado->setSize               ( 15                );
$obTxtTotalAvaliado->setValue              ( $flTotalAvaliado1 );
$obTxtTotalAvaliado->setReadOnly           ( true );

$obTxtTotal = new Moeda;
$obTxtTotal->setName               ( "flTotal" );
$obTxtTotal->setRotulo             ( "Valor Venal Total" );
$obTxtTotal->setMaxLength          ( 15                );
$obTxtTotal->setSize               ( 15                );
$obTxtTotal->setValue              ( $flTotalCalculado );
$obTxtTotal->setReadOnly           ( true );

$obRdbFinanciamentoSim = new Radio;
$obRdbFinanciamentoSim->setRotulo   ( "Possui Financiamento" );
$obRdbFinanciamentoSim->setName     ( "boFinanciamento" );
$obRdbFinanciamentoSim->setValue    ( "sim" );
$obRdbFinanciamentoSim->setLabel    ( "Sim" );
$obRdbFinanciamentoSim->setNull     ( false );
$obRdbFinanciamentoSim->setChecked  ( $boPossuiFinanciamento );
$obRdbFinanciamentoSim->setTitle    ( "Informe se o imóvel possui financiamento ou não." );
$obRdbFinanciamentoSim->obEvento->setOnChange ( "habilitaFinanciamento('true');" );

$obRdbFinanciamentoNao = new Radio;
$obRdbFinanciamentoNao->setRotulo       ( "Possui Financiamento" );
$obRdbFinanciamentoNao->setName         ( "boFinanciamento" );
$obRdbFinanciamentoNao->setValue        ( "nao" );
$obRdbFinanciamentoNao->setLabel        ( "Não" );
$obRdbFinanciamentoNao->setNull         ( false   );
$obRdbFinanciamentoNao->setChecked      ( !$boPossuiFinanciamento );
$obRdbFinanciamentoNao->setTitle        ( "Informe se o imóvel possui financiamento ou não." );
$obRdbFinanciamentoNao->obEvento->setOnChange ( "habilitaFinanciamento('false');");

$obLblValor = new Label;
$obLblValor->setRotulo( "Tipo de Valor" );
$obLblValor->setName  ( "stValor" );
$obLblValor->setValue ( "Valor (R$) " );

$obLblAliquota = new Label;
$obLblAliquota->setRotulo( "Tipo de Valor" );
$obLblAliquota->setName  ( "stAliquota" );
$obLblAliquota->setValue ( "Alíquota (%)" );

$obLblValorImposto = new Label;
$obLblValorImposto->setRotulo( "Tipo de Valor" );
$obLblValorImposto->setName  ( "stValorImposto" );
$obLblValorImposto->setValue ( "Valor do Imposto (R$)" );

$obTxtTotalValor = new Moeda;
$obTxtTotalValor->setName               ( "flTotalValor" );
$obTxtTotalValor->setRotulo             ( "Valor Venal Total" );
$obTxtTotalValor->setMaxLength          ( 15                );
$obTxtTotalValor->setSize               ( 15                );
$obTxtTotalValor->setValue              ( $flTotalAvaliado2 );
$obTxtTotalValor->setReadOnly           ( true );

$obTxtTotalAliquota = new TextBox;
$obTxtTotalAliquota->setName                 ( "flTotalAliquota"                                                       );
$obTxtTotalAliquota->setRotulo               ( "Valor Venal Total"                                                     );
$obTxtTotalAliquota->setMaxLength            ( 15                                                                      );
$obTxtTotalAliquota->setSize                 ( 15                                                                      );
$obTxtTotalAliquota->setValue                ( $flTotalAliquota                                                        );
$obTxtTotalAliquota->obEvento->setOnChange   ( "buscaValor('calculaTotalAliquota')"                                    );
//$obTxtTotalAliquota->obEvento->setOnBlur   ( "floatDecimal(this, '2', event );"                                      );
//$obTxtTotalAliquota->obEvento->setOnKeyUp  ( "mascaraMoeda(this, 2, event, false);"                                  )
$obTxtTotalAliquota->obEvento->setOnKeyPress ("validaCharMoeda( this, event );return tfloat( this, event );");

$obTxtTotalValorImposto = new Moeda;
$obTxtTotalValorImposto->setName               ( "flTotalValorImposto" );
$obTxtTotalValorImposto->setRotulo             ( "Valor Venal Total" );
$obTxtTotalValorImposto->setMaxLength          ( 15                );
$obTxtTotalValorImposto->setSize               ( 15                );
$obTxtTotalValorImposto->setValue              ( $flTotalValorImposto );
$obTxtTotalValorImposto->setReadOnly           ( true );

$obTxtValorFinanciado = new Moeda;
$obTxtValorFinanciado->setName               ( "flValorFinanciado" );
$obTxtValorFinanciado->setRotulo             ( "Valor Financiado" );
$obTxtValorFinanciado->setMaxLength          ( 15                );
$obTxtValorFinanciado->setSize               ( 15                );
$obTxtValorFinanciado->setValue              ( $flValorFinanciado );
$obTxtValorFinanciado->obEvento->setOnChange ( "buscaValor('calculaTotalFinanciado')" );
$obTxtValorFinanciado->setDisabled           ( !$boPossuiFinanciamento );

$obTxtFinanciadoAliquota = new Moeda;
$obTxtFinanciadoAliquota->setName               ( "flFinanciadoAliquota" );
$obTxtFinanciadoAliquota->setRotulo             ( "Valor Financiado" );
$obTxtFinanciadoAliquota->setMaxLength          ( 15                );
$obTxtFinanciadoAliquota->setSize               ( 15                );
$obTxtFinanciadoAliquota->setValue              ( $flFinanciadoAliquota );
$obTxtFinanciadoAliquota->obEvento->setOnChange ( "buscaValor('calculaTotalFinanciado')" );
$obTxtFinanciadoAliquota->setDisabled           ( !$boPossuiFinanciamento );

$obTxtFinanciadoImposto = new Moeda;
$obTxtFinanciadoImposto->setName               ( "flFinanciadoImposto" );
$obTxtFinanciadoImposto->setRotulo             ( "Valor Financiado" );
$obTxtFinanciadoImposto->setMaxLength          ( 15                );
$obTxtFinanciadoImposto->setSize               ( 15                );
$obTxtFinanciadoImposto->setValue              ( $flFinanciadoImposto );
$obTxtFinanciadoImposto->setReadOnly           ( true );

$obTxtValorTotalCobranca = new Moeda;
$obTxtValorTotalCobranca->setName               ( "flTotalCobranca" );
$obTxtValorTotalCobranca->setRotulo             ( "&nbsp;" );
$obTxtValorTotalCobranca->setMaxLength          ( 15                );
$obTxtValorTotalCobranca->setSize               ( 15                );
$obTxtValorTotalCobranca->setValue              ( $flTotalCobranca );
$obTxtValorTotalCobranca->setReadOnly           ( true );

$obLblEspacoValorTotal = new Label;
$obLblEspacoValorTotal->setRotulo( "&nbsp;" );
$obLblEspacoValorTotal->setName  ( "stEspacoVT" );
$obLblEspacoValorTotal->setValue ( "Total:&nbsp;&nbsp;&nbsp;&nbsp;" );

$obTxtDataVencimento = new Data;
$obTxtDataVencimento->setName   ( "dtVencimento" );
$obTxtDataVencimento->setId     ( "dtVencimento" );
$obTxtDataVencimento->setTitle  ( "Informe a data de vencimento da cobrança." );
$obTxtDataVencimento->setNull   ( false );
$obTxtDataVencimento->setRotulo ( "Data de Vencimento" );

$obRdbEmissaoCarneNao = new Radio;
$obRdbEmissaoCarneNao->setRotulo   ( "Emissão de Carnês" );
$obRdbEmissaoCarneNao->setName     ( "boEmissaoCarne" );
$obRdbEmissaoCarneNao->setValue    ( "nao" );
$obRdbEmissaoCarneNao->setLabel    ( "Não Emitir" );
$obRdbEmissaoCarneNao->setNull     ( false );
$obRdbEmissaoCarneNao->setChecked  ( false );
$obRdbEmissaoCarneNao->setTitle    ( "Selecione se será emitido o carnê de cobrança ou não." );
$obRdbEmissaoCarneNao->obEvento->setOnChange ( "buscaValor('buscaModeloCarne');" );

$obRdbEmissaoCarneSim = new Radio;
$obRdbEmissaoCarneSim->setRotulo       ( "Emissão de Carnês" );
$obRdbEmissaoCarneSim->setName         ( "boEmissaoCarne" );
$obRdbEmissaoCarneSim->setValue        ( "sim" );
$obRdbEmissaoCarneSim->setLabel        ( "Impressão Local" );
$obRdbEmissaoCarneSim->setNull         ( false   );
$obRdbEmissaoCarneSim->setChecked      ( true );
$obRdbEmissaoCarneSim->setTitle        ( "Selecione se será emitido o carnê de cobrança ou não." );
$obRdbEmissaoCarneSim->obEvento->setOnChange ( "buscaValor('buscaModeloCarne');" );

// Define Objeto Span Para Modelo de Carnê
$obSpanModeloCarne = new Span;
$obSpanModeloCarne->setId( 'spnModeloCarne' );

$rsAtributos = new RecordSet;

//DEFINICAO DOS ATRIBUTOS
$arChaveAtributo = array( "inscricao_municipal" => $inInscricao );

$obRARRLancamentoTransferencia->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obRARRLancamentoTransferencia->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm );
$obFormulario->setAjuda ( "UC-05.01.17" );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnInscricaoImobiliaria );
$obFormulario->addHidden ( $obHdnCodigoTransferencia );

$obFormulario->addAba ( "Transferência" );
$obFormulario->addTitulo ( "Dados para Avaliação de Transferência" );
$obFormulario->addComponente ( $obLblNumeroInscricao );
$obFormulario->addComponenteComposto ( $obTxtCodigoNatureza, $obCmbCodigoNatureza       );
$obFormulario->addComponente ( $obBscProcesso );
$obFormulario->addComponente ( $obBscCreci );
$obFormulario->addSpan ( $obSpnListaDocumentos );
$obFormulario->addSpan ( $obSpnListaProprietario );
$obFormulario->addTitulo ( "Adquirentes" );
$obFormulario->addComponente ( $obBscCGM );
$obFormulario->addComponente ( $obTxtQuota );
$obFormulario->defineBarraAba ( array( $obBtnIncluir, $obBtnLimpar ), "left", "" );
$obFormulario->addSpan ( $obSpnAdquirentes );

$obFormulario->addAba ( "Imposto" );

if ($_REQUEST["boLoteRural"] == 't') {
    $obTxtTerritorial->setDisabled( true );
    $obTxtPredial->setDisabled( true );
    $obTxtTotal->setDisabled( true );
}

$obFormulario->addTitulo ( "Dados para o Cálculo do Imposto" );
$obFormulario->agrupaComponentes( array( $obLblCalculados, $obLblEspaco, $obLblDeclarados, $obLblEspaco, $obLblAvaliados ) );
$obFormulario->agrupaComponentes( array( $obTxtTerritorial, $obLblEspaco, $obTxtTerritorialDeclarado, $obLblEspaco, $obTxtTerritorialAvaliado ) );
$obFormulario->agrupaComponentes( array( $obTxtPredial, $obLblEspaco, $obTxtPredialDeclarado, $obLblEspaco, $obTxtPredialAvaliado ) );
$obFormulario->agrupaComponentes( array( $obTxtTotal, $obLblEspaco, $obTxtTotalDeclarado, $obLblEspaco, $obTxtTotalAvaliado ) );
$obFormulario->addTitulo ( "Forma de Cobrança" );
$obFormulario->addComponenteComposto ( $obRdbFinanciamentoSim, $obRdbFinanciamentoNao );
$obFormulario->agrupaComponentes( array( $obLblValor, $obLblEspaco, $obLblEspaco, $obLblAliquota, $obLblEspaco, $obLblEspaco, $obLblValorImposto ) );
$obFormulario->agrupaComponentes( array( $obTxtTotalValor, $obLblEspaco, $obTxtTotalAliquota, $obLblEspaco, $obTxtTotalValorImposto ) );
$obFormulario->agrupaComponentes( array( $obTxtValorFinanciado, $obLblEspaco, $obTxtFinanciadoAliquota, $obLblEspaco, $obTxtFinanciadoImposto ) );
$obLblEspaco->setRotulo( "&nbsp;" );
$obFormulario->agrupaComponentes( array( $obLblEspaco, $obLblEspaco, $obLblEspaco, $obLblEspaco, $obLblEspaco, $obLblEspaco, $obLblEspacoValorTotal, $obTxtValorTotalCobranca ) );
$obFormulario->addComponente ( $obTxtDataVencimento );
$obFormulario->addComponenteComposto ( $obRdbEmissaoCarneNao, $obRdbEmissaoCarneSim );
$obFormulario->addSpan       ( $obSpanModeloCarne );
$obMontaAtributos->geraFormulario( $obFormulario );

$obFormulario->Ok();
$obFormulario->show();

SistemaLegado::executaFrameOculto( "buscaValor('MontarListas&Carnes');" );

?>
