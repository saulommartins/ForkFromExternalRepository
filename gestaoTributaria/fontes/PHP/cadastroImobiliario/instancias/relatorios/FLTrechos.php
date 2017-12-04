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
 * Página de filtro para o relatório de trechos
 * Data de Criação   : 31/03/2004

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo Boezio Paulino

 * @ignore

 * $Id: FLTrechos.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.22
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";

$obRRegra = new RCadastroDinamico;
$rsAtributo = $rsAtributos = new RecordSet;
$obRRegra->setCodCadastro('7');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributo );

Sessao::remove('sessao_transf5');

//Define COMPONENTES DO FORMULARIO
//****************************************/

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."relatorios/OCTrechos.php" );

$obCodInicio = new TextBox;
$obCodInicio->setName  ( "inCodInicio" );
$obCodInicio->setId    ( "inCodInicio" );
$obCodInicio->setRotulo( "Sequência" );
$obCodInicio->setTitle ( "Informe um período" ) ;
$obCodInicio->setValidaCaracteres( true, "0123456789." );

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCodTermino = new TextBox;
$obCodTermino->setName     ( "inCodTermino" );
$obCodTermino->setRotulo   ( "Sequência" );
$obCodTermino->setTitle    ( "Informe um período" );
$obCodTermino->setValidaCaracteres( true, "0123456789." );

$obCodInicioLogradouro = new TextBox;
$obCodInicioLogradouro ->setName  ( "inCodInicioLogradouro" );
$obCodInicioLogradouro ->setRotulo( "Código do Logradouro" );
$obCodInicioLogradouro ->setTitle ( "Informe um período" ) ;
$obCodInicioLogradouro->setInteiro( true );

$obLblPeriodoLogradouro = new Label;
$obLblPeriodoLogradouro->setValue( " até " );

$obCodTerminoLogradouro = new TextBox;
$obCodTerminoLogradouro->setName     ( "inCodTerminoLogradouro" );
$obCodTerminoLogradouro->setRotulo   ( "Código do Logradouro" );
$obCodTerminoLogradouro->setTitle    ( "Informe um período" );
$obCodTerminoLogradouro->setInteiro  ( true );

$arFiltro['nomFiltro']['tipo_relatorio']['analitico'] = "Analítico";
$arFiltro['nomFiltro']['tipo_relatorio']['sintetico'] = "Sintético";

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
    $arFiltro['nomFiltro']['atributo'][$rsAtributo->getCampo( 'cod_atributo' )] = $rsAtributo->getCampo( 'nom_atributo' );
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

$arFiltro['nomFiltro']['ordenacao']['codLogradouro'] = "Código do logradouro";
$arFiltro['nomFiltro']['ordenacao']['nomLogradouro'] = "Nome do logradouro";

Sessao::write('arFiltro', $arFiltro);

$obCmbOrder = new Select;
$obCmbOrder->setName      ( "stOrder"          );
$obCmbOrder->setRotulo    ( "Ordenação"        );
$obCmbOrder->setTitle     ( "Selecione a ordenação do relatório"     );
$obCmbOrder->addOption    ( ""              , "Selecione"            );
$obCmbOrder->addOption    ( "codLogradouro" , "Código do logradouro" );
$obCmbOrder->addOption    ( "nomLogradouro" , "Nome do logradouro"   );
$obCmbOrder->setCampoDesc ( "stOrder"          );
$obCmbOrder->setNull      ( false              );
$obCmbOrder->setStyle     ( "width: 200px"     );

//Monta FORMULARIO
//****************************************/

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.22" );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->agrupaComponentes( array( $obCodInicioLogradouro, $obLblPeriodoLogradouro ,$obCodTerminoLogradouro) );
$obFormulario->agrupaComponentes( array( $obCodInicio, $obLblPeriodo ,$obCodTermino) );
$obFormulario->addComponente( $obCmbTipo      );
$obFormulario->addComponente( $obCmbAtributos );
$obFormulario->addComponente( $obCmbOrder     );
$obFormulario->OK();
$obFormulario->setFormFocus( $obCodInicio->getId() );
$obFormulario->show();

include_once 'JSTrechos.js';
?>
