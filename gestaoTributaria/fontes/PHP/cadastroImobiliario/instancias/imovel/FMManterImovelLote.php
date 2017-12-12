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
    * Página de Formulário para o cadastro de imóvel
    * Data de Criação   : 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterImovelLote.php 66542 2016-09-16 13:32:13Z evandro $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.25  2007/10/08 13:19:43  vitor
Ticket#10302#

Revision 1.24  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stLink = Sessao::read('stLink');
$link   = Sessao::read('link');

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgOcul );

$stAcao = $request->get('stAcao','incluir');

$arProprietariosSessao = array();
$arPromitentesSessao   = array();
$arEnderecoSessao      = array();//endereco_entrega

include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();

$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obRCIMImovel->roRCIMLote->setCodigoLote( $request->get("inCodigoLote") );
$obRCIMImovel->roRCIMLote->consultarLote();

//RECUPERA A LISTA DE ENDEREÇOS
$obRCIMConfrontacaoTrecho = new RCIMConfrontacaoTrecho( $obRCIMImovel->roRCIMLote );
$obRCIMConfrontacaoTrecho->setPrincipal( "t" );
$obRCIMConfrontacaoTrecho->listarConfrontacoes( $rsListaConfrontacoes );
$rsListaConfrontacoes->ordena("cod_confrontacao");

$rsListaConfrontacoes->setPrimeiroElemento();

if ($request->get('stAcao') == "incluir") {
   $obRCIMImovel->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosImovel );
} else {
    $obRCIMImovel->setNumeroInscricao( $request->get("inInscricaoMunicipal") );
    $obErro = $obRCIMImovel->consultarImovelAlteracao();

    //RECUPERA OS CEPS DO LOGRADOURO
    $obRCIMImovel->obRCIMConfrontacaoTrecho->setCodigoConfrontacao( $obRCIMImovel->obRCIMConfrontacaoTrecho->getCodigoConfrontacao() );
    $obRCIMImovel->roRCIMLote->setCodigoLote( $request->get('inCodigoLote') );
    $boTransacao = false;
    $obRCIMImovel->obRCIMConfrontacaoTrecho->consultarConfrontacao( $boTransacao );

    $obRCIMImovel->obRCIMLogradouro->setCodigoLogradouro( $obRCIMImovel->obRCIMConfrontacaoTrecho->obRCIMTrecho->getCodigoLogradouro() );
    $obRCIMImovel->obRCIMLogradouro->listarCEP( $rsCep );
    $cont=0;
    foreach ($obRCIMImovel->arRCIMProprietario as $obRCIMProprietario) {
        $arProprietariosSessao[] = array( "inNumCGM"  => $obRCIMProprietario->getNumeroCGM(),
                                          "stNomeCGM" => $obRCIMProprietario->obRCGM->getNomCGM(),
                                          "flQuota"   => number_format( $obRCIMProprietario->getCota(),2,",","."),
                                          "ordem"     => $obRCIMProprietario->getOrdem(),
                                          "inLinha"   => $cont);
        $cont++;
    }
    $rsProprietarios = new RecordSet;
    $rsProprietarios->preenche( $arProprietariosSessao );
    $stJs = montaListaProprietario( $rsProprietarios  );
    $cont=0;
    foreach ($obRCIMImovel->arRCIMProprietarioPromitente as $obRCIMProprietarioPromitente) {
        $arPromitentesSessao[] = array( "inNumCGM"  => $obRCIMProprietarioPromitente->getNumeroCGM(),
                                        "stNomeCGM" => $obRCIMProprietarioPromitente->obRCGM->getNomCGM(),
                                        "flQuota"   => number_format( $obRCIMProprietarioPromitente->getCota(),2,",", "." ),
                                        "ordem"     => $obRCIMProprietarioPromitente->getOrdem(),
                                        "inLinha"   => $cont);
        $cont++;
    }
    $rsPromitentes = new RecordSet;
    $rsPromitentes->preenche( $arPromitentesSessao );
    $stJs .= montaListaPromitente( $rsPromitentes  );

    //Monta array com os dados do ENDERECO DE ENTREGA
    if ( $obRCIMImovel->obRCIMLogradouroEntrega->getCodigoLogradouro() ) {
        $arEnderecoSessao = array();
        $arEnderecoSessao['cep'           ] = $obRCIMImovel->getCEPEntrega();
        $arEnderecoSessao['nom_logradouro'] = $obRCIMImovel->obRCIMLogradouroEntrega->getNomeLogradouro();
        $arEnderecoSessao['cod_logradouro'] = $obRCIMImovel->obRCIMLogradouroEntrega->getCodigoLogradouro();
        $arEnderecoSessao['nom_bairro'    ] = $obRCIMImovel->obRCIMBairro->getNomeBairro();
        $arEnderecoSessao['cod_bairro'    ] = $obRCIMImovel->obRCIMBairro->getCodigoBairro();
        $arEnderecoSessao['caixa_postal'  ] = $obRCIMImovel->getCaixaPostal();
        $arEnderecoSessao['numero'        ] = $obRCIMImovel->getNumeroEntrega();
        $arEnderecoSessao['complemento'   ] = $obRCIMImovel->getComplementoEntrega();
        $stJs .= montaFormEnderecoEntrega( true , Sessao::getId(), $arEnderecoSessao );
    }

    //DEFINICAO DOS ATRIBUTOS DE IMOVEL
    $arChaveAtributoImovel =  array( "inscricao_municipal" => $request->get("inInscricaoMunicipal") );
    $obRCIMImovel->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoImovel );
    $obRCIMImovel->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosImovel );
    Sessao::write('proprietarios'   , $arProprietariosSessao );
    Sessao::write('promitentes'     , $arPromitentesSessao   );
    Sessao::write('endereco_entrega', $arEnderecoSessao      );
}
if ($request->get('stAcao') == "alterar") {
    $inCodigoCondominio = $obRCIMImovel->obRCIMCondominio->getCodigoCondominio();

    $obLblCreci = new Label;
    $obLblCreci->setRotulo ( "Creci" );
    $obLblCreci->setId     ( "inCreci" );
    $obLblCreci->setName   ( "inCreci" );
    $obLblCreci->setValue  ( $request->get('stCreciResponsavel') ." - ". $request->get('stNomeCreci') );
}

$obMontaAtributosImovel = new MontaAtributos;
$obMontaAtributosImovel->setTitulo     ( "Atributos"        );
$obMontaAtributosImovel->setName       ( "Atributo_"  );
if ($request->get('stAcao') == "alterar") {
    $obMontaAtributosImovel->setLabel  ( true );
}
$obMontaAtributosImovel->setRecordSet  ( $rsAtributosImovel );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $request->get('stAcao') );

$obHdnLote = new Hidden;
$obHdnLote->setName  ( "inCodigoLote" );
$obHdnLote->setValue ( $request->get("inCodigoLote") );

$obHdnSubLote = new Hidden;
$obHdnSubLote->setName  ( "inCodigoSubLote" );
$obHdnSubLote->setValue ( $request->get("inCodigoSubLote") );

$obHdnTimestampImovel = new Hidden;
$obHdnTimestampImovel->setName  ( "hdnTimestampImovel" );
$tmTimestampImovel = (isset($tmTimestampImovel)) ? $tmTimestampImovel : '';
$obHdnTimestampImovel->setValue ( $tmTimestampImovel );

$obHdnCodigoUF = new Hidden;
$obHdnCodigoUF->setName ( "inCodigoUF" );

$obHdnCodigoMunicipio = new Hidden;
$obHdnCodigoMunicipio->setName( "inCodigoMunicipio" );

//COMPONENTES PARA A ABA INSCRICAO IMOBILIARIA
$obLblNumeroLote = new Label;
$obLblNumeroLote->setRotulo ( "Lote" );
$obLblNumeroLote->setValue  ( $obRCIMImovel->roRCIMLote->getNumeroLote()." - Lote ".$request->get("stTipoLote") );

$obLblBairroLote = new Label;
$obLblBairroLote->setRotulo ( "Bairro" );
$obLblBairroLote->setValue  ( $obRCIMImovel->roRCIMLote->obRCIMBairro->getNomeBairro() );

$obTxtNumeroInscricao = new TextBox;
$obTxtNumeroInscricao->setName      ( "inNumeroInscricao" );
$obTxtNumeroInscricao->setId        ( "inNumeroInscricao" );
$obTxtNumeroInscricao->setRotulo    ( "Número da Inscrição" );
$obTxtNumeroInscricao->setInteiro   ( true );
$obTxtNumeroInscricao->setSize      ( strlen($stMascaraIM) );
$obTxtNumeroInscricao->setMaxLength ( strlen($stMascaraIM) );
$obTxtNumeroInscricao->setNull      ( false );
$obTxtNumeroInscricao->setTitle     ( "Número da inscrição imobiliária" );
$obTxtNumeroInscricao->setValue     ( $obRCIMImovel->getNumeroInscricao() );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setRotulo    ( "Número da Inscrição" );
$obLblNumeroInscricao->setTitle     ( "Número da inscrição imobiliária" );
$obLblNumeroInscricao->setValue     ( $obRCIMImovel->getNumeroInscricao() );

$obHdnNumeroInscricao = new Hidden;
$obHdnNumeroInscricao->setName      ( "inNumeroInscricao"                 );
$obHdnNumeroInscricao->setValue     ( $obRCIMImovel->getNumeroInscricao() );

$dtdiaHOJE = date ("d/m/Y");

$obTxtDataInscricaoImovel = new Data;
$obTxtDataInscricaoImovel->setName   ( "dtDataInscricaoImovel" );
$obTxtDataInscricaoImovel->setId     ( "dtDataInscricaoImovel" );
$obTxtDataInscricaoImovel->setTitle  ( "Data de inscrição do imóvel" );
$obTxtDataInscricaoImovel->setNull   ( false );
$obTxtDataInscricaoImovel->setRotulo ( "Data da Inscrição" );
if ( $obRCIMImovel->getDataInscricao() )
    $obTxtDataInscricaoImovel->setValue  ( $obRCIMImovel->getDataInscricao() );
else
    $obTxtDataInscricaoImovel->setValue  ( $dtdiaHOJE );

$obTxtDataInscricaoImovel->obEvento->setOnChange  ( "javascript: buscaValor( 'validaDataInscricaoLote' );"  );

$boIsRS = SistemaLegado::isRS();

$obTxtMatricula = new TextBox;
$obTxtMatricula->setName      ( "stMatriculaRegistroImoveis" );
$obTxtMatricula->setRotulo    ( "Matrícula no Registro de Imóveis" );
$obTxtMatricula->setSize      ( 20 );
$obTxtMatricula->setMaxLength ( 20 );
//é obrigatório somente para municípios do rio grande do sul
if ($boIsRS) {
    $obTxtMatricula->setNull      ( false );
}
$obTxtMatricula->setInteiro   ( true );
$obTxtMatricula->setValue     ( $obRCIMImovel->getMatriculaRegistroImoveis() );

$obTxtZona = new TextBox;
$obTxtZona->setName      ( "stMatriculaZona" );
$obTxtZona->setRotulo    ( "Zona" );
$obTxtZona->setSize      ( 10 );
$obTxtZona->setMaxLength ( 10 );
//é obrigatório somente para municípios do rio grande do sul
if ($boIsRS) {
    $obTxtZona->setNull      ( false );
}
$obTxtZona->setInteiro   ( true );
$obTxtZona->setValue     ( $obRCIMImovel->getZona() );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $request->get('inProcesso') );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obBscCreci = new BuscaInner;
$obBscCreci->setRotulo                ( "CRECI"                                          );
$obBscCreci->setTitle                 ( "CRECI do corretor responsável pelo imóvel"      );
$obBscCreci->setNull                  ( true                                             );
$obBscCreci->setId                    ( "stNomeResponsavel"                              );
$obBscCreci->obCampoCod->setName      ( "stCreciResponsavel"                             );
$obBscCreci->obCampoCod->setInteiro   ( false                                            );
$obBscCreci->obCampoCod->setSize      ( 10                                               );
$obBscCreci->obCampoCod->setMaxLength ( 10                                               );
$obBscCreci->obCampoCod->setValue     ( $request->get("stCreciResponsavel")              );
$obBscCreci->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreci');"               );
$obBscCreci->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."corretagem/FLProcurarCorretagem.php','frm','stCreciResponsavel','stNomeResponsavel','todos','".Sessao::getId()."','800','550')" );

$obCmbEndereco = new Select;
$obCmbEndereco->setRotulo     ( "Logradouro"           );
$obCmbEndereco->setName       ( "inCodigoConfrontacao" );
$obCmbEndereco->setTitle      ( "Trecho que indica o endereço do imóvel" );
$obCmbEndereco->setStyle      ( "width: 250px"         );
$obCmbEndereco->setRotulo     ( "Endereço"             );
$obCmbEndereco->addOption     ( "", "Selecione"        );
$obCmbEndereco->setNull       ( false                  );
$obCmbEndereco->setCampoID    ( "cod_confrontacao"     );
$obCmbEndereco->setCampoDesc  ( "nom_completo"         );
$obCmbEndereco->preencheCombo ( $rsListaConfrontacoes  );
$obCmbEndereco->setValue      ( $obRCIMImovel->obRCIMConfrontacaoTrecho->getCodigoConfrontacao()  );
$obCmbEndereco->obEvento->setOnChange("buscaValor('buscaCepLogradouro');");

if ($request->get('stAcao') == "incluir") {
    $obCmbEndereco->setValue  ( $rsListaConfrontacoes->getCampo("cod_confrontacao") );
}

$obCmbCEP = new Select;
$obCmbCEP->setRotulo     ( "CEP"           );
$obCmbCEP->setName       ( "inCEP"         );
$obCmbCEP->setId         ( "inCEP"         );
$obCmbCEP->setStyle      ( "width:100px"   );
$obCmbCEP->setRotulo     ( "CEP"           );
$obCmbCEP->addOption     ( "", "Selecione" );
$obCmbCEP->setNull       ( false           );
$obCmbCEP->setValue      ( ""              );
$obCmbCEP->setCampoID    ( "cep"           );
$obCmbCEP->setCampoDesc  ( "cep"           );

if ($request->get('stAcao') == "incluir") {
    //RECUPERA OS CEPS DO LOGRADOURO
    $obRCIMImovel1 = new RCIMImovel( new RCIMLote );

    $rsListaConfrontacoes->setPrimeiroElemento();

    $obRCIMImovel1->obRCIMConfrontacaoTrecho->setCodigoConfrontacao( $rsListaConfrontacoes->getCampo("cod_confrontacao") );
    $obRCIMImovel1->roRCIMLote->setCodigoLote( $rsListaConfrontacoes->getCampo("cod_lote") );
    $boTransacao = false;
    $obRCIMImovel1->obRCIMConfrontacaoTrecho->consultarConfrontacao( $boTransacao );

    //RECUPERA OS CEPS DO LOGRADOURO
    $obRCIMImovel1->obRCIMLogradouro->setCodigoLogradouro( $obRCIMImovel1->obRCIMConfrontacaoTrecho->obRCIMTrecho->getCodigoLogradouro() );
    $obRCIMImovel1->obRCIMLogradouro->listarCEP( $rsCep );

    $obCmbCEP->preencheCombo ( $rsCep  );
    if ( $rsCep->getNumLinhas() == 1 ) {
        $obCmbCEP->setValue ( $rsCep->getCampo("cep") );
    }
}else
if ($request->get('stAcao') == "alterar") {
    $obCmbCEP->preencheCombo ( $rsCep  );
    $obCmbCEP->setValue      ( $obRCIMImovel->getCepImovel() );
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//PROPRIETARIOS
$obBscCGM = new IPopUpCGM( $obForm );
$obBscCGM->setNull ( true );
$obBscCGM->setId( "inNomCGM" );
$obBscCGM->obCampoCod->setName("inNumCGM");
$obBscCGM->obCampoCod->setValue( $inNumCGM );
$obBscCGM->setRotulo ( "*CGM" );
$obBscCGM->setTitle ( "Procura por um CGM para adicionar como proprietário" );

$obTxtQuota = new Numerico;
$obTxtQuota->setName      ( "flQuota" );
$obTxtQuota->setRotulo    ( "*Quota" );
$obTxtQuota->setSize      ( 6 );
$obTxtQuota->setMaxLength ( 6 );
$obTxtQuota->setValue     ( "100,00" );
$obTxtQuota->setNegativo  ( false );

$obRdoSituacaoProprietario = new Radio;
$obRdoSituacaoProprietario->setName    ( "boProprietario" );
$obRdoSituacaoProprietario->setRotulo  ( "Situação" );
$obRdoSituacaoProprietario->setLabel   ( "Proprietário" );
$obRdoSituacaoProprietario->setValue   ( "true" );
$obRdoSituacaoProprietario->setChecked ( true);

$obRdoSituacaoPromitente = new Radio;
$obRdoSituacaoPromitente->setName    ( "boProprietario" );
$obRdoSituacaoPromitente->setRotulo  ( "Situação" );
$obRdoSituacaoPromitente->setLabel   ( "Promitente" );
$obRdoSituacaoPromitente->setValue   ( "false");
$obRdoSituacaoPromitente->setChecked ( false );

$obBtnIncluirProprietario = new Button;
$obBtnIncluirProprietario->setName( "stIncluirProprietario" );
$obBtnIncluirProprietario->setValue( "Incluir" );
$obBtnIncluirProprietario->obEvento->setOnClick( "incluirProprietario();" );

$obBtnLimparProprietario= new Button;
$obBtnLimparProprietario->setName( "stLimparProprietario" );
$obBtnLimparProprietario->setValue( "Limpar" );
$obBtnLimparProprietario->obEvento->setOnClick( "limparProprietario();" );

$obSpnListaProprietario = new Span;
$obSpnListaProprietario->setId( "lsListaProprietarios" );

$obSpnListaPromitentes = new Span;
$obSpnListaPromitentes->setId( "lsListaPromitentes" );

//ENDERECO DE ENTREGA
$obChkEnderecoEntrega = new checkbox;
$obChkEnderecoEntrega->setName  ( "boEnderecoEntrega"                    );
$obChkEnderecoEntrega->setRotulo( " &nbsp "                              );
$obChkEnderecoEntrega->setLabel ( "Habilitar endereço de entrega de correspondências" );
if ( $request->get('stAcao') == "alterar" AND $obRCIMImovel->obRCIMLogradouroEntrega->getCodigoLogradouro() ) {
    $obChkEnderecoEntrega->setChecked( true );
}
$obChkEnderecoEntrega->obEvento->setOnClick( "montaFormEnderecoEntrega();" );

$obSpnEnderecoEntrega = new Span;
$obSpnEnderecoEntrega->setId( "spnEnderecoEntrega" );

$obTxtCep = new CEP;
$obTxtCep->setName   ( "stCEPEntrega" );
$obTxtCep->setRotulo ( "CEP" );
$obTxtCep->setValue  ( $obRCIMImovel->getCEPEntrega() );

$obHdnNomeLogradouro = new Hidden;
$obHdnNomeLogradouro->setName ( "stNomeLogradouro" );
$obHdnNomeLogradouro->setValue( $request->get("stNomeLogradouro") );

$obBscLogradouro = new BuscaInner;
$obBscLogradouro->setRotulo ( "Logradouro"                               );
$obBscLogradouro->setId     ( "campoInnerLogr"                           );
$obBscLogradouro->obCampoCod->setName  ( "inNumLogradouro"               );
$obBscLogradouro->obCampoCod->obEvento->setOnChange ( "buscaLogradouro();" );
$stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInnerLogr',''";
$stBusca .= " ,'".Sessao::getId()."','800','550')";
$obBscLogradouro->setFuncaoBusca ( $stBusca );
if ( is_object( $obRCIMImovel->obRCIMLogradouroEntrega ) ) {
    $obBscLogradouro->setValue( $obRCIMImovel->obRCIMLogradouroEntrega->getNomeLogradouro() );
    $obBscLogradouro->obCampoCod->setValue ( $obRCIMImovel->obRCIMLogradouroEntrega->getCodigoLogradouro() );
}

$obBscBairroEntrega = new BuscaInner;
$obBscBairroEntrega->setRotulo ( "Bairro"                 );
$obBscBairroEntrega->setId     ( "innerBairroEntrega"     );
$obBscBairroEntrega->obCampoCod->setName  ( "inCodigoBairroEntrega" );
$obBscBairroEntrega->obCampoCod->obEvento->setOnChange ( "buscaBairro();" );
$stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."bairroSistema/FLProcurarBairro.php','frm','inCodigoBairroEntrega','innerBairroEntrega',''";
$stBusca .= " ,'".Sessao::getId()."','800','550')";
$obBscBairroEntrega->setFuncaoBusca ( $stBusca );
if ( is_object( $obRCIMImovel->obRCIMLogradouroEntrega ) ) {
    $obBscBairroEntrega->setValue( $obRCIMImovel->obRCIMBairro->getNomeBairro() );
    $obBscBairroEntrega->obCampoCod->setValue ( $obRCIMImovel->obRCIMBairro->getCodigoBairro() );
}

$obTxtNumero = new TextBox;
$obTxtNumero->setName      ( "stNumero" );
$obTxtNumero->setRotulo    ( "Número"   );
$obTxtNumero->setsize      ( 6          );
$obTxtNumero->setMaxLength ( 6          );
$obTxtNumero->setValue     ( $obRCIMImovel->getNumeroEntrega() );

$obTxtComplemento = new TextBox;
$obTxtComplemento->setName     ( "stComplemento" );
$obTxtComplemento->setRotulo   ( "Complemento"   );
$obTxtComplemento->setSize     ( 53              );
$obTxtComplemento->setMaxLength( 50             );
$obTxtComplemento->setValue    ( $obRCIMImovel->getComplementoEntrega() );

$obTxtNumeroImovel = new TextBox;
$obTxtNumeroImovel->setName      ( "stNumeroImovel" );
$obTxtNumeroImovel->setRotulo    ( "Número"   );
$obTxtNumeroImovel->setTitle     ( "Número do imóvel" );
$obTxtNumeroImovel->setsize      ( 6          );
$obTxtNumeroImovel->setMaxLength ( 6          );
$obTxtNumeroImovel->setValue     ( $obRCIMImovel->getNumeroImovel() );

$obTxtComplementoImovel = new TextBox;
$obTxtComplementoImovel->setName     ( "stComplementoImovel" );
$obTxtComplementoImovel->setRotulo   ( "Complemento"   );
$obTxtComplementoImovel->setTitle    ( "Complemento do endereço do imóvel" );
$obTxtComplementoImovel->setSize     ( 53              );
$obTxtComplementoImovel->setMaxLength( 50             );
$obTxtComplementoImovel->setValue    ( $obRCIMImovel->getComplementoImovel() );

if ( $obRCIMImovel->obRCIMConfiguracao->getNavegacaoAutomatico() == "ativo" )
    $boSeguir = true;
else
    $boSeguir = false;

$obChkSeguir = new checkbox;
$obChkSeguir->setName       ( "boSeguir"                                );
$obChkSeguir->setRotulo     ( " &nbsp "                                 );
$obChkSeguir->setLabel      ( "Seguir para o cadastro de edificação"    );
$obChkSeguir->setChecked    ( $boSeguir );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm      ( $obForm                   );
$obFormulario->setAjuda     ( "UC-05.01.09" );
$obFormulario->addHidden    ( $obHdnCtrl                );
$obFormulario->addHidden    ( $obHdnNomeLogradouro      );
$obFormulario->addHidden    ( $obHdnLote                );
$obFormulario->addHidden    ( $obHdnSubLote             );
$obFormulario->addHidden    ( $obHdnAcao                );
$obFormulario->addHidden    ( $obHdnCodigoUF            );
$obFormulario->addHidden    ( $obHdnCodigoMunicipio     );
$obFormulario->addHidden    ( $obHdnTimestampImovel     );
$obFormulario->addAba       ( "Inscrição Imobiliária"   );
$obFormulario->addFuncaoAba ( "atualizaComponente();" );
$obFormulario->addTitulo    ( "Dados para imóvel"       );
$obFormulario->addComponente( $obLblNumeroLote          );
$obFormulario->addComponente( $obLblBairroLote          );
if ( $obRCIMImovel->obRCIMConfiguracao->getNumeroIM() == 'false' and $request->get('stAcao') == "incluir") {
    $obFormulario->addComponente( $obTxtNumeroInscricao     );
} elseif ($request->get('stAcao') != "incluir") {
    $obFormulario->addComponente( $obLblNumeroInscricao );
    $obFormulario->addHidden    ( $obHdnNumeroInscricao );
}
$obFormulario->addComponente( $obTxtDataInscricaoImovel );
$obFormulario->addComponente( $obTxtMatricula           );
$obFormulario->addComponente( $obTxtZona                );
$obFormulario->addComponente( $obBscProcesso );
if ( ( $request->get('stAcao') == "alterar" ) && ( $request->get('stCreciResponsavel') != "" ) ) {
    $obFormulario->addComponente( $obLblCreci               );
} else {
    $obFormulario->addComponente( $obBscCreci               );
}
$obFormulario->addComponente( $obCmbEndereco            );
$obFormulario->addComponente( $obTxtNumeroImovel        );
$obFormulario->addComponente( $obTxtComplementoImovel   );
$obFormulario->addComponente( $obCmbCEP                 );
$obFormulario->addComponente( $obChkEnderecoEntrega     );
$obFormulario->addSpan      ( $obSpnEnderecoEntrega     );
$obFormulario->addAba       ( "Proprietários"           );
$obFormulario->addFuncaoAba ( "atualizaComponente();" );
$obFormulario->addTitulo    ( "Proprietários"           );
$obFormulario->addComponente( $obBscCGM                 );
$obFormulario->addComponente( $obTxtQuota               );
$obFormulario->agrupaComponentes( array( $obRdoSituacaoProprietario, $obRdoSituacaoPromitente) );
$obFormulario->defineBarraAba   ( array( $obBtnIncluirProprietario, $obBtnLimparProprietario ),"","" );
$obFormulario->addspan      ( $obSpnListaProprietario   );
$obFormulario->addspan      ( $obSpnListaPromitentes    );
$obFormulario->addAba       ( "Características"         );
$obFormulario->addFuncaoAba ( "atualizaComponente();" );
$obMontaAtributosImovel->geraFormulario ( $obFormulario );
if ($request->get('stAcao') == "incluir") {
    $obFormulario->addDiv( 4, "componente" );

    //verificar se existe permissao
    $rsPermissao = new RecordSet();
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
    $obTAdministracaoAcao = NEW TAdministracaoAcao();
    $obTAdministracaoAcao->setDado("cod_acao", 751);
    $obTAdministracaoAcao->recuperaPermissao($rsPermissao );
    if ( !$rsPermissao->eof() ) {
        $obFormulario->addComponente      ( $obChkSeguir            );
    }
    $obFormulario->fechaDiv();
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar( $pgList );
}
if ($request->get('stAcao') == "incluir") {
    $obFormulario->setFormFocus( $obTxtNumeroInscricao->getId() );
} elseif ($request->get('stAcao') == "alterar") {
    $obFormulario->setFormFocus( $obTxtDataInscricaoImovel->getId() );
}
$obFormulario->show();
include_once( $pgJs );
if (isset($stJs)) {
    SistemaLegado::executaFrameOculto( $stJs );
}
?>
