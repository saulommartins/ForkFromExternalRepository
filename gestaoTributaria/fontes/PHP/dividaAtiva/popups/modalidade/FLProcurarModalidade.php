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
    * Página de Formulario de Filtro para PopUp de Modalidade

    * Data de Criação   : 26/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLProcurarModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.07

*/

/*
$Log$
Revision 1.2  2007/06/21 20:58:48  cercato
adicionado filtro por tipo de modalidade no componente.

Revision 1.1  2006/09/26 10:01:40  cercato
popup de busca modalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarModalidade";
$pgList        = "LS".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('link');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $request->get("stAcao")  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get("stCtrl")  );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

$obHdnTipoModalidade = new Hidden;
$obHdnTipoModalidade->setName( "tipoModalidade" );
$obHdnTipoModalidade->setValue( $request->get("tipoBusca") );

//Codigo
$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo ( "Código" );
$obTxtCodigo->setTitle ( "Informe o código da modalidade." );
$obTxtCodigo->setName ( "inCodigo" );
$obTxtCodigo->setSize ( 20 );
$obTxtCodigo->setMaxLength ( 20 );
$obTxtCodigo->setNull ( true );
$obTxtCodigo->setInteiro ( true );

//Descricao
$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo ( "Descrição" );
$obTxtDescricao->setTitle ( "Informe a descrição da modalidade." );
$obTxtDescricao->setName ( "stDescricao" );
$obTxtDescricao->setSize ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull ( true );
$obTxtDescricao->setInteiro ( false );

$obLabelIntervalo = new Label;
$obLabelIntervalo->setValue ( "até" );

//Data de vigencia
$obDtVigenciaInicio  = new Data;
$obDtVigenciaInicio->setName ( "dtVigenciaInicio" );
$obDtVigenciaInicio->setRotulo ( "Vigência" );
$obDtVigenciaInicio->setTitle ( "Informe a vigência da modalidade." );
$obDtVigenciaInicio->setMaxLength ( 20 );
$obDtVigenciaInicio->setSize ( 10 );
$obDtVigenciaInicio->setNull ( true );
$obDtVigenciaInicio->obEvento->setOnChange ( "validaData1500( this );" );

$obDtVigenciaFim  = new Data;
$obDtVigenciaFim->setName ( "dtVigenciaFim" );
$obDtVigenciaFim->setRotulo ( "Vigência" );
$obDtVigenciaFim->setTitle ( "Informe a vigência da modalidade." );
$obDtVigenciaFim->setMaxLength ( 20 );
$obDtVigenciaFim->setSize ( 10 );
$obDtVigenciaFim->setNull ( true );
$obDtVigenciaFim->obEvento->setOnChange ( "validaData1500( this );" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.07" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCampoNom );
$obFormulario->addHidden     ( $obHdnCampoNum );
$obFormulario->addHidden     ( $obHdnTipoModalidade );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addComponente ( $obTxtCodigo );
$obFormulario->addComponente ( $obTxtDescricao );
$obFormulario->agrupaComponentes ( array($obDtVigenciaInicio, $obLabelIntervalo, $obDtVigenciaFim) );
$obFormulario->Ok ();

$obFormulario->show();
