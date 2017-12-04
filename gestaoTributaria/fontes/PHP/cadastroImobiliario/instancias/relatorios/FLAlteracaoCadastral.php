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
 * Página de filtro para o relatório de alteração cadastral
 * Data de Criação   : 06/04/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo Boezio Paulino

 * @ignore

 * $Id: FLAlteracaoCadastral.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.25
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

while ( !$rsRecordSet->eof() ) {
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

Sessao::remove('sessao_transf5');

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."relatorios/OCAlteracaoCadastral.php" );

$obCodInicioLocalizacao = new TextBox;
$obCodInicioLocalizacao->setName  ( "inCodInicioLocalizacao" );
$obCodInicioLocalizacao->setRotulo( "Localização" );
$obCodInicioLocalizacao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLocalizacao."', this, event);" );
$obCodInicioLocalizacao->setSize( strlen($stMascaraLocalizacao)+2);
$obCodInicioLocalizacao->setMaxLength( strlen($stMascaraLocalizacao)+2 );
$obCodInicioLocalizacao->setTitle ( "Informe um período" ) ;

$obLblPeriodoLocalizacao = new Label;
$obLblPeriodoLocalizacao->setValue( " até " );

$obCodTerminoLocalizacao = new TextBox;
$obCodTerminoLocalizacao->setName     ( "inCodTerminoLocalizacao" );
$obCodTerminoLocalizacao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLocalizacao."', this, event);" );
$obCodTerminoLocalizacao->setSize( strlen($stMascaraLocalizacao)+2);
$obCodTerminoLocalizacao->setMaxLength( strlen($stMascaraLocalizacao)+2 );
$obCodTerminoLocalizacao->setRotulo   ( "Localização" );
$obCodTerminoLocalizacao->setTitle    ( "Informe um período" );

$obCodInicioLote = new TextBox;
$obCodInicioLote->setName  ( "inCodInicioLote" );
$obCodInicioLote->setRotulo( "Lote" );
$obCodInicioLote->setTitle ( "Informe um período" ) ;
$obCodInicioLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );

$obLblPeriodoLote = new Label;
$obLblPeriodoLote->setValue( " até " );

$obCodTerminoLote = new TextBox;
$obCodTerminoLote->setName     ( "inCodTerminoLote" );
$obCodTerminoLote->setRotulo   ( "Lote" );
$obCodTerminoLote->setTitle    ( "Informe um período" );
$obCodTerminoLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );

$obCodInicioInscricao = new TextBox;
$obCodInicioInscricao->setName  ( "inCodInicioInscricao" );
$obCodInicioInscricao->setRotulo( "Inscrição Imobiliária" );
$obCodInicioInscricao->setTitle ( "Informe um período" ) ;
$obCodInicioInscricao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraInscricao."', this, event);");

$obLblPeriodoInscricao = new Label;
$obLblPeriodoInscricao->setValue( " até " );

$obCodTerminoInscricao = new TextBox;
$obCodTerminoInscricao->setName     ( "inCodTerminoInscricao" );
$obCodTerminoInscricao->setRotulo   ( "Código do Logradouro" );
$obCodTerminoInscricao->setTitle    ( "Informe um período" );
$obCodTerminoInscricao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraInscricao."', this, event);");

$obCodInicio = new TextBox;
$obCodInicio->setName  ( "inCodInicioLogradouro" );
$obCodInicio->setRotulo( "Código do Logradouro" );
$obCodInicio->setTitle ( "Informe um período" ) ;

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCodTermino = new TextBox;
$obCodTermino->setName     ( "inCodTerminoLogradouro" );
$obCodTermino->setRotulo   ( "Código do Logradouro" );
$obCodTermino->setTitle    ( "Informe um período" );

$obCodInicioBairro = new TextBox;
$obCodInicioBairro->setName  ( "inCodInicioBairro" );
$obCodInicioBairro->setRotulo( "Código do Bairro" );
$obCodInicioBairro->setTitle ( "Informe um período" ) ;

$obLblPeriodoBairro = new Label;
$obLblPeriodoBairro->setValue( " até " );

$obCodTerminoBairro = new TextBox;
$obCodTerminoBairro->setName     ( "inCodTerminoBairro" );
$obCodTerminoBairro->setRotulo   ( "Código do Bairro" );
$obCodTerminoBairro->setTitle    ( "Informe um período" );

$nomFiltroSessao['tipo_relatorio']['analitico'] = "Analítico";
$nomFiltroSessao['tipo_relatorio']['sintetico'] = "Sintético";

$obCmbTipo = new Select;
$obCmbTipo->setName      ( "stTipoRelatorio"             );
$obCmbTipo->setRotulo    ( "Tipo de Relatório"           );
$obCmbTipo->setTitle     ( "Selecione o tipo de relatório" );
$obCmbTipo->addOption    ( ""          , "Selecione"     );
$obCmbTipo->addOption    ( "analitico" , "Analítico"     );
$obCmbTipo->addOption    ( "sintetico" , "Sintético"     );
$obCmbTipo->setCampoDesc ( "stTipo"                      );
$obCmbTipo->setNull      ( false                         );
$obCmbTipo->setStyle     ( "width: 200px"                );
$obCmbTipo->obEvento->setOnChange( "disableAtributos(this.value)" );

while ( !$rsAtributo->eof() ) {
    $nomFiltroSessao['atributo'][$rsAtributo->getCampo( 'cod_atributo' )] = $rsAtributo->getCampo( 'nom_atributo' );
    $rsAtributo->proximo();
}

$rsAtributo->setPrimeiroElemento();

$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName  ('inCodAtributos');
$obCmbAtributos->setRotulo( "Atributos" );
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

$nomFiltroSessao['ordenacao']['inscricao']   = "Inscrição Imobiliária";
$nomFiltroSessao['ordenacao']['localizacao'] = "Localização";
$nomFiltroSessao['ordenacao']['lote']        = "Lote";
$nomFiltroSessao['ordenacao']['logradouro']  = "Logradouro";
$nomFiltroSessao['ordenacao']['bairro']      = "Bairro";
$nomFiltroSessao['ordenacao']['cep']         = "CEP";
Sessao::write( "nomFiltro", $nomFiltroSessao );

$obCmbOrder = new Select;
$obCmbOrder->setName      ( "stOrder"        );
$obCmbOrder->setRotulo    ( "Ordenação"      );
$obCmbOrder->setTitle     ( "Escolha a ordenação do relatório"      );
$obCmbOrder->addOption    ( "", "Selecione"                         );
$obCmbOrder->addOption    ( "inscricao"   , "Inscrição Imobiliária" );
$obCmbOrder->addOption    ( "localizacao" , "Localização"           );
$obCmbOrder->addOption    ( "lote"        , "Lote"                  );
$obCmbOrder->addOption    ( "logradouro"  , "Logradouro"            );
$obCmbOrder->addOption    ( "bairro"      , "Bairro"                );
$obCmbOrder->addOption    ( "cep"         , "CEP"                   );
$obCmbOrder->setCampoDesc ( "stOrder"        );
$obCmbOrder->setNull      ( false            );
$obCmbOrder->setStyle     ( "width: 200px"   );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.25" );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->agrupaComponentes( array( $obCodInicioLocalizacao, $obLblPeriodoLocalizacao ,$obCodTerminoLocalizacao) );
$obFormulario->agrupaComponentes( array( $obCodInicioLote, $obLblPeriodoLote ,$obCodTerminoLote) );
$obFormulario->agrupaComponentes( array( $obCodInicioInscricao, $obLblPeriodoInscricao ,$obCodTerminoInscricao) );
$obFormulario->agrupaComponentes( array( $obCodInicio, $obLblPeriodo ,$obCodTermino) );
$obFormulario->agrupaComponentes( array( $obCodInicioBairro, $obLblPeriodoBairro ,$obCodTerminoBairro ) );
$obFormulario->addComponente( $obCmbTipo  );
$obFormulario->addComponente( $obCmbAtributos );
$obFormulario->addComponente( $obCmbOrder );
$obFormulario->OK();
$obFormulario->show();

include_once 'JSCadastroImobiliario.js';

?>
