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
 * Arquivo paga Filtro do relatorio de Cadastro de Imoveis
 * Data de Criação: 28/04/2005

 * @author Analista: Fabio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo B. Paulino

 * @ignore

 * $Id: FLCadastroImobiliario.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.23
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php";

$pgOcul = "OCCadastroImobiliario.php";

// MONTA MASCARA DE LOCALIZACAO
$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigencia );
$obMontaLocalizacao->obRCIMLocalizacao->setCodigoVigencia( $rsVigencia->getCampo( 'cod_vigencia' ));
$obMontaLocalizacao->obRCIMLocalizacao->listarNiveis( $rsRecordSet );

while (!$rsRecordSet->eof()) {
    $obMontaLocalizacao->stMascara .= $rsRecordSet->getCampo("mascara").".";
    $rsRecordSet->proximo();
}
$stMascaraLocalizacao = substr( $obMontaLocalizacao->getMascara(), 0 , strlen($obMontaLocalizacao->getMascara()) - 1 );

//MASCARA LOTE
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

//MASCARA INSCRICAO
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();

$obRRegra = new RCadastroDinamico;
$rsAtributo = $rsAtributos = new RecordSet;
$obRRegra->setCodCadastro('4');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributo );

$obRRegra->setCodCadastro('2');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoLot2 );

$obRRegra->setCodCadastro('3');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoLot3 );

Sessao::remove('sessao_transf5');
Sessao::remove('filtroRelatorio' );

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."relatorios/OCCadastroImobiliario.php" );

$obHdnNome = new Hidden;
$obHdnNome->setName ( "stNomeCGM" );
$obHdnNome->setID ( "stNomeCGM" );

$obCodInicioLocalizacao = new TextBox;
$obCodInicioLocalizacao->setName  ( "inCodInicioLocalizacao" );
$obCodInicioLocalizacao->setId    ( "inCodInicioLocalizacao" );
$obCodInicioLocalizacao->setRotulo( "Localização" );
$obCodInicioLocalizacao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLocalizacao."', this, event);" );
$obCodInicioLocalizacao->setSize( strlen($stMascaraLocalizacao)+2);
$obCodInicioLocalizacao->setMaxLength( strlen($stMascaraLocalizacao)+2 );
$obCodInicioLocalizacao->setTitle ( "Informe um período" ) ;

$obLblPeriodoLocalizacao = new Label;
$obLblPeriodoLocalizacao->setValue( " até " );

$obCodTerminoLocalizacao = new TextBox;
$obCodTerminoLocalizacao->setName     ( "inCodTerminoLocalizacao" );
$obCodTerminoLocalizacao->setRotulo   ( "Localização" );
$obCodTerminoLocalizacao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLocalizacao."', this, event);" );
$obCodTerminoLocalizacao->setSize( strlen($stMascaraLocalizacao)+2);
$obCodTerminoLocalizacao->setMaxLength( strlen($stMascaraLocalizacao)+2 );
$obCodTerminoLocalizacao->setTitle    ( "Informe um período" );

$obCodInicioLote = new TextBox;
$obCodInicioLote->setName  ( "inCodInicioLote" );
$obCodInicioLote->setRotulo( "Lote" );
$obCodInicioLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );
$obCodInicioLote->setTitle ( "Informe um período" ) ;

$obLblPeriodoLote = new Label;
$obLblPeriodoLote->setValue( " até " );

$obCodTerminoLote = new TextBox;
$obCodTerminoLote->setName     ( "inCodTerminoLote" );
$obCodTerminoLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);");
$obCodTerminoLote->setRotulo   ( "Lote" );
$obCodTerminoLote->setTitle    ( "Informe um período" );

$obCodInicioInscricao = new TextBox;
$obCodInicioInscricao->setName  ( "inCodInicioInscricao" );
$obCodInicioInscricao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraInscricao."', this, event);");
$obCodInicioInscricao->setRotulo( "Inscrição Imobiliária" );
$obCodInicioInscricao->setTitle ( "Informe um período" ) ;

$obLblPeriodoInscricao = new Label;
$obLblPeriodoInscricao->setValue( " até " );

$obCodTerminoInscricao = new TextBox;
$obCodTerminoInscricao->setName     ( "inCodTerminoInscricao" );
$obCodTerminoInscricao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraInscricao."', this, event);");
$obCodTerminoInscricao->setRotulo   ( "Código do Logradouro" );
$obCodTerminoInscricao->setTitle    ( "Informe um período" );

$obCodInicio = new TextBox;
$obCodInicio->setName  ( "inCodInicioLogradouro" );
$obCodInicio->setInteiro( true );
$obCodInicio->setRotulo( "Código do Logradouro" );
$obCodInicio->setTitle ( "Informe um período" ) ;

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCodTermino = new TextBox;
$obCodTermino->setName     ( "inCodTerminoLogradouro" );
$obCodTermino->setInteiro  ( true );
$obCodTermino->setRotulo   ( "Código do Logradouro" );
$obCodTermino->setTitle    ( "Informe um período" );

$obCodInicioBairro = new TextBox;
$obCodInicioBairro->setName  ( "inCodInicioBairro" );
$obCodInicioBairro->setInteiro( true );
$obCodInicioBairro->setRotulo( "Código do Bairro" );
$obCodInicioBairro->setTitle ( "Informe um período" ) ;

$obLblPeriodoBairro = new Label;
$obLblPeriodoBairro->setValue( " até " );

$obCodTerminoBairro = new TextBox;
$obCodTerminoBairro->setName     ( "inCodTerminoBairro" );
$obCodTerminoBairro->setInteiro  ( true );
$obCodTerminoBairro->setRotulo   ( "Código do Bairro" );
$obCodTerminoBairro->setTitle    ( "Informe um período" );

$obCmbEdificacao = new Select;
$obCmbEdificacao->setName      ( "stImoEd" );
$obCmbEdificacao->setRotulo    ( "Imóvel"  );
$obCmbEdificacao->setTitle     ( "Selecione o tipo de filtro." );
$obCmbEdificacao->addOption    ( ""          , "Selecione"     );
$obCmbEdificacao->addOption    ( "0" , "Sem edificação"  );
$obCmbEdificacao->addOption    ( "1" , "Com edificação"  );
$obCmbEdificacao->addOption    ( "2" , "Todos"     );
$obCmbEdificacao->setValue     ( 2 );
$obCmbEdificacao->setCampoDesc ( "stTipoEd"     );
$obCmbEdificacao->setNull      ( false          );
$obCmbEdificacao->setStyle     ( "width: 200px" );

$obCmbSituacao = new Select;
$obCmbSituacao->setName        ("stTipoSituacao");
$obCmbSituacao->setRotulo      ("Situação");
$obCmbSituacao->setTitle       ("Selecione a situação dos imóveis");
$obCmbSituacao->addOption      ("", "Selecione");
$obCmbSituacao->addOption      ("todos", "Todos");
$obCmbSituacao->addOption      ("Ativo", "Ativos");
$obCmbSituacao->addOption      ("Baixado", "Baixados");
$obCmbSituacao->setValue       ( "todos" );
$obCmbSituacao->setCampoDesc   ( "stTipoSituacao" );
$obCmbSituacao->setNull        ( false            );
$obCmbSituacao->setStyle       ( "width: 200px"   );

$sessao->nomFiltro['tipo_relatorio']['analitico'] = "Analítico";
$sessao->nomFiltro['tipo_relatorio']['sintetico'] = "Sintético";

$obCmbTipo = new Select;
$obCmbTipo->setName      ( "stTipoRelatorio"             );
$obCmbTipo->setRotulo    ( "Tipo de Relatório"           );
$obCmbTipo->setTitle     ( "Selecione o tipo de relatório" );
$obCmbTipo->addOption    ( ""          , "Selecione"     );
$obCmbTipo->addOption    ( "analitico" , "Analítico"     );
$obCmbTipo->addOption    ( "sintetico" , "Sintético"     );
$obCmbTipo->setCampoDesc ( "stSituacao"                      );
$obCmbTipo->setNull      ( false                         );
$obCmbTipo->setStyle     ( "width: 200px"                );
$obCmbTipo->obEvento->setOnChange( "disableAtributos(this.value)" );

while ( !$rsAtributo->eof() ) {
    $sessao->nomFiltro['atributo'][$rsAtributo->getCampo( 'cod_atributo' )] = $rsAtributo->getCampo( 'nom_atributo' );
    $rsAtributo->proximo();
}
$rsAtributo->setPrimeiroElemento();

$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName  ('inCodAtributos');
$obCmbAtributos->setRotulo( "Atributos Imóvel" );
$obCmbAtributos->setNull  ( true );
$obCmbAtributos->setTitle ( "Selecione os atributos a serem exibidos no relatório" );

// lista de atributos disponiveis
$obCmbAtributos->SetNomeLista1('inCodAtributosDisponiveis');
$obCmbAtributos->setCampoId1  ('cod_atributo');
$obCmbAtributos->setCampoDesc1('nom_atributo');
$obCmbAtributos->SetRecord1   ( $rsAtributo );

// lista de atributos selecionados
$obCmbAtributos->SetNomeLista2('inCodAtributosSelecionados');
$obCmbAtributos->setCampoId2  ('cod_atributo');
$obCmbAtributos->setCampoDesc2('nom_atributo');
$obCmbAtributos->SetRecord2   ( $rsAtributos );

while ( !$rsAtributoLot2->eof() ) {
    $sessao->nomFiltro['atributoLote2'][$rsAtributoLot2->getCampo( 'cod_atributo' )] = $rsAtributoLot2->getCampo( 'nom_atributo' );
    $rsAtributoLot2->proximo();
}

$rsAtributoLot2->setPrimeiroElemento();

$obCmbAtributosLote2 = new SelectMultiplo();
$obCmbAtributosLote2->setName  ('inCodAtributosLote2');
$obCmbAtributosLote2->setRotulo( "Atributos Lote Urbano" );
$obCmbAtributosLote2->setNull  ( true );
$obCmbAtributosLote2->setTitle ( "Selecione os atributos a serem exibidos no relatório" );

// lista de atributos disponiveis
$obCmbAtributosLote2->SetNomeLista1('inCodAtributosLote2Disponiveis');
$obCmbAtributosLote2->setCampoId1  ('cod_atributo');
$obCmbAtributosLote2->setCampoDesc1('nom_atributo');
$obCmbAtributosLote2->SetRecord1   ( $rsAtributoLot2 );

// lista de atributos selecionados
$obCmbAtributosLote2->SetNomeLista2('inCodAtributosLote2Selecionados');
$obCmbAtributosLote2->setCampoId2  ('cod_atributo');
$obCmbAtributosLote2->setCampoDesc2('nom_atributo');
$obCmbAtributosLote2->SetRecord2   ( $rsAtributos );

while ( !$rsAtributoLot3->eof() ) {
    $sessao->nomFiltro['atributoLote3'][$rsAtributoLot3->getCampo( 'cod_atributo' )] = $rsAtributoLot3->getCampo( 'nom_atributo' );
    $rsAtributoLot3->proximo();
}

$rsAtributoLot3->setPrimeiroElemento();

$obCmbAtributosLote3 = new SelectMultiplo();
$obCmbAtributosLote3->setName  ('inCodAtributosLote3');
$obCmbAtributosLote3->setRotulo( "Atributos Lote Rural" );
$obCmbAtributosLote3->setNull  ( true );
$obCmbAtributosLote3->setTitle ( "Selecione os atributos a serem exibidos no relatório" );

// lista de atributos disponiveis
$obCmbAtributosLote3->SetNomeLista1('inCodAtributosLote3Disponiveis');
$obCmbAtributosLote3->setCampoId1  ('cod_atributo');
$obCmbAtributosLote3->setCampoDesc1('nom_atributo');
$obCmbAtributosLote3->SetRecord1   ( $rsAtributoLot3 );

// lista de atributos selecionados
$obCmbAtributosLote3->SetNomeLista2('inCodAtributosLote3Selecionados');
$obCmbAtributosLote3->setCampoId2  ('cod_atributo');
$obCmbAtributosLote3->setCampoDesc2('nom_atributo');
$obCmbAtributosLote3->SetRecord2   ( $rsAtributos );

$sessao->nomFiltro['ordenacao']['inscricao']   = "Inscrição Imobiliária";
$sessao->nomFiltro['ordenacao']['localizacao'] = "Localização";
$sessao->nomFiltro['ordenacao']['lote']        = "Lote";
$sessao->nomFiltro['ordenacao']['logradouro']  = "Logradouro";
$sessao->nomFiltro['ordenacao']['bairro']      = "Bairro";
$sessao->nomFiltro['ordenacao']['cep']         = "CEP";

$obCmbOrder = new Select;
$obCmbOrder->setName      ( "stOrder"   );
$obCmbOrder->setRotulo    ( "Ordenação" );
$obCmbOrder->setTitle     ( "Escolha a ordenação do relatório"      );
$obCmbOrder->addOption    ( "", "Selecione"  );
$obCmbOrder->addOption    ( "inscricao"   , "Inscrição Imobiliária" );
$obCmbOrder->addOption    ( "localizacao" , "Localização" );
$obCmbOrder->addOption    ( "lote"        , "Lote"        );
$obCmbOrder->addOption    ( "logradouro"  , "Logradouro"  );
$obCmbOrder->addOption    ( "bairro"      , "Bairro"      );
$obCmbOrder->addOption    ( "cep"         , "CEP"         );
$obCmbOrder->setCampoDesc ( "stOrder"                     );
$obCmbOrder->setNull      ( false            );
$obCmbOrder->setStyle     ( "width: 200px"   );

$obBscProprietario = new BuscaInnerIntervalo;
$obBscProprietario->setRotulo( "Proprietário" );
$obBscProprietario->obLabelIntervalo->setValue( "até" );
$obBscProprietario->obCampoCod->setName( "inCodProprietarioInicial" );
$obBscProprietario->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodProprietarioInicial','stNomeCGM','','".Sessao::getId()."','800','450');" ) );
$obBscProprietario->obCampoCod2->setName( "inCodProprietarioFinal" );
$obBscProprietario->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodProprietarioFinal','stNomeCGM','','".Sessao::getId()."','800','450');" ) );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.23" );
$obFormulario->addHidden( $obHdnNome    );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->agrupaComponentes( array( $obCodInicioLocalizacao, $obLblPeriodoLocalizacao ,$obCodTerminoLocalizacao) );
$obFormulario->agrupaComponentes( array( $obCodInicioLote, $obLblPeriodoLote ,$obCodTerminoLote) );
$obFormulario->agrupaComponentes( array( $obCodInicioInscricao, $obLblPeriodoInscricao ,$obCodTerminoInscricao) );
$obFormulario->agrupaComponentes( array( $obCodInicio, $obLblPeriodo ,$obCodTermino) );
$obFormulario->agrupaComponentes( array( $obCodInicioBairro, $obLblPeriodoBairro ,$obCodTerminoBairro ) );
$obFormulario->addComponente( $obBscProprietario );
$obFormulario->addComponente( $obCmbEdificacao );

$obFormulario->addComponente( $obCmbTipo  );
$obFormulario->addComponente( $obCmbAtributos );
$obFormulario->addComponente( $obCmbAtributosLote2 );
$obFormulario->addComponente( $obCmbAtributosLote3 );
$obFormulario->addComponente( $obCmbSituacao );
$obFormulario->addComponente( $obCmbOrder );
$obFormulario->OK();
$obFormulario->setFormFocus( $obCodInicioLocalizacao->getid() );
$obFormulario->show();

include_once 'JSCadastroImobiliario.js';

?>
