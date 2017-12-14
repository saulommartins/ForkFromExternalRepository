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
    * Pagina de Formulario de Inclusao/Alteracao de VALORES DO INDICADOR ECONOMICO

    * Data de Criacao: 19/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FLManterValor.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.08

*/

/*
$Log$
Revision 1.4  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterValor";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

/***********************************************/

include_once ( $pgJs );

Sessao::write( "link", "" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $stCtrl );

$obLblCodIndicador = new Label;

$obTxtCodIndicador = new TextBox;
$obTxtCodIndicador->setRotulo  ( 'Código');
$obTxtCodIndicador->setTitle   ( 'Código do Indicador');
$obTxtCodIndicador->setName    ( 'inCodIndicador');
$obTxtCodIndicador->setValue   ( $inCodIndicador );
$obTxtCodIndicador->setInteiro ( true );
$obTxtCodIndicador->setSize    ( 10 );
$obTxtCodIndicador->setMaxLength ( 10 );
$obTxtCodIndicador->setNull    ( true );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo  ( 'Descrição');
$obTxtDescricao->setTitle   ( 'Descrição do Indicador Econômico');
$obTxtDescricao->setName    ( 'stDescricao');
$obTxtDescricao->setValue   ( $stDescricao );
$obTxtDescricao->setInteiro ( false );
$obTxtDescricao->setSize    ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull    ( true );

$obTxtAbreviatura = new TextBox;
$obTxtAbreviatura->setRotulo  ( 'Abreviatura ou Símbolo');
$obTxtAbreviatura->setTitle   ( 'Abreviatura ou Símbolo referente ao Indicador Econômico');
$obTxtAbreviatura->setName    ( 'stAbreviatura');
$obTxtAbreviatura->setValue   ( $stAbreviatura );
$obTxtAbreviatura->setInteiro ( false );
$obTxtAbreviatura->setSize    ( 15 );
$obTxtAbreviatura->setMaxLength ( 15 );
$obTxtAbreviatura->setNull    ( true );

$obDtVigencia  = new Data;
$obDtVigencia->setName               ( "dtVigencia"                    );
$obDtVigencia->setValue              ( $dia                            );
$obDtVigencia->setRotulo             ( "Data de Vigência"              );
$obDtVigencia->setTitle              ( "Data de Vigência da fórmula de cálculo" );
$obDtVigencia->setMaxLength          ( 20                              );
$obDtVigencia->setSize               ( 10                              );
$obDtVigencia->setNull               ( true                            );
$obDtVigencia->obEvento->setOnChange ( "validaData1500( this );"       );

//--------------------------------
// DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//------------------------------------------------------
//MONTA FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda ( "UC-05.05.08" );
$obFormulario->addTitulo ('Dados para o Filtro');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addComponente ( $obTxtCodIndicador );
$obFormulario->addComponente ( $obTxtDescricao );

$obFormulario->addComponente ( $obTxtAbreviatura );

$obFormulario->ok();
$obFormulario->show ();

$stJs .= 'f.inCodIndicador.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
