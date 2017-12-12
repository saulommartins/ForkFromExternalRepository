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
    * Página de formulário para alteração de construção
    * Data de Criação   : 10/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterConstrucaoAlteracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.7  2006/09/18 10:30:16  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"             );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConstrucao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obRCIMConstrucao = new RCIMConstrucaoOutros;

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obRCIMConstrucao->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
$obRCIMConstrucao->consultarConstrucao();

if ( $obRCIMConstrucao->obRProcesso->getCodigoProcesso() ) {
    $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $stMascaraProcesso );
    $stProcesso = str_pad( $obRCIMConstrucao->obRProcesso->getCodigoProcesso() , strlen( $arProcesso[0] ), "0", STR_PAD_LEFT );
    $stSeparador = preg_replace( "/[a-zA-Z0-9]/","", $stMascaraProcesso );
    $stProcesso .= $stSeparador.$obRCIMConstrucao->obRProcesso->getExercicio();
}
//DEFINICAO DOS ATRIBUTOS DE LOTE
$arChaveAtributoConstrucao =  array( "cod_construcao" => $_REQUEST["inCodigoConstrucao"] );
$obRCIMConstrucao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucao );
$obRCIMConstrucao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setLabel      ( true         );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

// recebe varaiveis de timestamp do processo(se haver) para comparação
// se timestamp do processo for igual a timestamp da construção,
// este processo é o de inclusao, se nao for igual
// liberar campo de processo e inseri-lo com o mesmo timestamp do imovel
$rsProcessos = new Recordset;
$obRCIMConstrucao->listarProcessos($rsProcessos);
$tmTimestampProcesso    = $rsProcessos->getCampo("timestamp")  ;
$tmTimestampConstrucao  = $obRCIMConstrucao->getTimestampConstrucao()  ;
$boTimestampIgual       = $tmTimestampProcesso == $tmTimestampConstrucao;
// fim das variaveis de timestamp

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCodigoConstrucao = new Hidden;
$obHdnCodigoConstrucao->setName  ( "inCodigoConstrucao" );
$obHdnCodigoConstrucao->setValue ( $obRCIMConstrucao->getCodigoConstrucao() );

$obHdnNumeroInscricao = new Hidden;
$obHdnNumeroInscricao->setName  ( "inNumeroInscricao" );
$obHdnNumeroInscricao->setValue ( $_REQUEST["inNumeroInscricao"] );

$obHdnCodigoCondominio = new Hidden;
$obHdnCodigoCondominio->setName  ( "inCodigoCondominio" );
$obHdnCodigoCondominio->setValue ( $_REQUEST["inCodigoCondominio"] );

$obHdnTimestampConstrucao = new Hidden;
$obHdnTimestampConstrucao->setName  ( "hdnTimestamp"            );
$obHdnTimestampConstrucao->setValue ( $tmTimestampConstrucao    );

$obHdnProcesso = new Hidden;
$obHdnProcesso->setName  ( "inProcesso" );
$obHdnProcesso->setValue ( $stProcesso  );

$obHdnVinculo = new Hidden;
$obHdnVinculo->setName  ( "stTipoVinculo" );
$obHdnVinculo->setValue ( $stTipoVinculo  );

$obLblCodigoConstrucao = new Label;
$obLblCodigoConstrucao->setName     ( "inCodigoConstrucao" );
$obLblCodigoConstrucao->setRotulo   ( "Código" );
$obLblCodigoConstrucao->setValue    ( $obRCIMConstrucao->getCodigoConstrucao() );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setName      ( "inNumeroInscricao" );
$obLblNumeroInscricao->setRotulo    ( "Inscrição Imobiliária" );
$obLblNumeroInscricao->setTitle     ( "Inscrição imobiliária com a qual a construção está vinculada" );
$obLblNumeroInscricao->setValue     ( $_REQUEST["inNumeroInscricao"] );

$obDtDataConstrucao= new data;
$obDtDataConstrucao->setRotulo        ( "Data da Construção"          );
$obDtDataConstrucao->setName          ( "stDtConstrucao"              );
$obDtDataConstrucao->setId            ( "stDtConstrucao"              );
$obDtDataConstrucao->setValue         ( $_REQUEST["stDtConstrucao"]   );
$obDtDataConstrucao->setNull          ( false                         );
$obDtDataConstrucao->obEvento->setOnChange( "validaDataConstrucao();" );

$obLblNomeCondominio = new Label;
$obLblNomeCondominio->setName      ( "inNomeCondominio" );
$obLblNomeCondominio->setRotulo    ( "Condomínio" );
$obLblNomeCondominio->setTitle     ( "Nome do Condomínio a qual a construção esta vinculada" );
$obLblNomeCondominio->setValue     ( $_REQUEST["stNomeCond"] );

$obTxtDescricaoConstrucao = new TextBox;
$obTxtDescricaoConstrucao->setName      ( "stDescricaoConstrucao" );
$obTxtDescricaoConstrucao->setId        ( "stDescricaoConstrucao" );
$obTxtDescricaoConstrucao->setRotulo    ( "Descrição" );
$obTxtDescricaoConstrucao->setSize      ( 80 );
$obTxtDescricaoConstrucao->setMaxLength ( 160 );
$obTxtDescricaoConstrucao->setNull      ( false );
$obTxtDescricaoConstrucao->setValue     ( $obRCIMConstrucao->getDescricao() );

$obTxtAreaConstrucao = new Numerico;
$obTxtAreaConstrucao->setName      ( "flAreaConstrucao" );
$obTxtAreaConstrucao->setRotulo    ( "Área" );
$obTxtAreaConstrucao->setMaxValue  ( 999999999999.99    );
$obTxtAreaConstrucao->setSize      ( 18 );
$obTxtAreaConstrucao->setMaxLength ( 18 );
$obTxtAreaConstrucao->setNull      ( false );
$obTxtAreaConstrucao->setNegativo  ( false );
$obTxtAreaConstrucao->setNaoZero   ( true );
$obTxtAreaConstrucao->setTitle     ( "Área construída em metros quadrados" );
$obTxtAreaConstrucao->setValue     ( $obRCIMConstrucao->getAreaConstruida() );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obLblProcesso = new Label;
$obLblProcesso->setName     ( "stProcesso"  );
$obLblProcesso->setRotulo   ( "Processo"    );
$obLblProcesso->setValue    ( $stProcesso   );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm                   );
$obFormulario->setAjuda             ( "UC-05.01.12" );
$obFormulario->addHidden          ( $obHdnCtrl                );
$obFormulario->addHidden          ( $obHdnAcao                );
$obFormulario->addHidden          ( $obHdnCodigoConstrucao    );
$obFormulario->addHidden          ( $obHdnNumeroInscricao     );
$obFormulario->addHidden          ( $obHdnCodigoCondominio    );
$obFormulario->addHidden          ( $obHdnVinculo             );
$obFormulario->addHidden          ( $obHdnTimestampConstrucao );
$obFormulario->addAba             ( "Construção"              );
$obFormulario->addTitulo          ( "Dados para construção"   );
$obFormulario->addComponente      ( $obLblCodigoConstrucao    );
if ($_REQUEST["stTipoVinculo"]== "dependente") {
    $obFormulario->addComponente      ( $obLblNumeroInscricao     );
} else {
    $obFormulario->addComponente      ( $obLblNomeCondominio      );
}
$obFormulario->addComponente      ( $obDtDataConstrucao       );
$obFormulario->addComponente      ( $obTxtDescricaoConstrucao );
$obFormulario->addComponente      ( $obTxtAreaConstrucao      );
if ( $obRCIMConstrucao->obRProcesso->getCodigoProcesso() && !$boTimestampIgual ) {
    $obFormulario->addComponente  ( $obLblProcesso            );
    $obFormulario->addHidden      ( $obHdnProcesso            );
} else {
    $obFormulario->addComponente  ( $obBscProcesso            );
}
$obFormulario->addAba             ( "Características"         );
$obMontaAtributos->geraFormulario ( $obFormulario );
$obFormulario->Cancelar( $pgList );
$obFormulario->setFormFocus( $obTxtDescricaoConstrucao->getId() );
$obFormulario->show();
?>
