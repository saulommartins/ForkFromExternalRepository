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
    * Filtro para consultar saldos

    * Data de Criação: 04/07/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * $Id: FLConsultarSaldo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma      = "ConsultarSaldo";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

require $pgJs;

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgForm );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

// Monta combo de Entidades
$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$obCmbEntidade = new Select();
$obCmbEntidade->setRotulo    ( "Entidade"                 );
$obCmbEntidade->setId        ( "inCodEntidade"            );
$obCmbEntidade->setName      ( "inCodEntidade"            );
$obCmbEntidade->setTitle     ( "Selecione a Entidade"     );
$obCmbEntidade->setCampoId   ( "cod_entidade"             );
$obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
$obCmbEntidade->setValue     ( $inCodEntidade             );
$obCmbEntidade->setNull      ( false                       );
$obCmbEntidade->addOption    ( ""            ,"Selecione" );
$obCmbEntidade->obEvento->setOnChange( "montaParametrosGET('mostraSpanContaBanco');" );
$obCmbEntidade->preencheCombo( $rsEntidade                );

// Span onde traz a pesquisa da conta
$obSpanContaBanco = new Span;
$obSpanContaBanco->setId( "spnContaBanco" );

$obDtSaldo = new Data;
$obDtSaldo->setName('dtSaldo');
$obDtSaldo->setId('dtSaldo');
$obDtSaldo->setRotulo('Data do Saldo');
$obDtSaldo->setTitle( 'Código Limite da Conta Contábil.' );
$obDtSaldo->setNull( false );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm               );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addTitulo    ( "Dados para Buscar Valor do Saldo" );
$obFormulario->addComponente( $obCmbEntidade        );
$obFormulario->addSpan      ( $obSpanContaBanco     );
$obFormulario->addComponente( $obDtSaldo            );

$obOk = new Ok;
$obLimpar = new Button;
$obLimpar->setName  ( "Limpar" );
$obLimpar->setValue ( "Limpar" );
$obLimpar->obEvento->setOnClick("limpar();");
$obFormulario->defineBarra( array($obOk, $obLimpar) );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
