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
    * Formulario para Modalidade de Lançamento
    * Data de Criação   : 04/01/2005

    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FLManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.13

*/

/*
$Log$
Revision 1.7  2006/09/15 14:33:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeLancamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterModalidade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ( empty( $boVinculoModalidade ) ) {
    $boVinculoModalidade = "atividade";
}

Sessao::write( "link", "" );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$boVinculo = $_REQUEST["boVinculo"];
// DEFINE OBJETOS DO FILTRO ATIVIDADE/INSCRICAO
$obRadioVinculoAtividade = new Radio;
$obRadioVinculoAtividade->setName    ( "boVinculoModalidade"   );
$obRadioVinculoAtividade->setTitle   ( "Vínculo da Modalidade" );
$obRadioVinculoAtividade->setRotulo  ( "Vínculo"               );
$obRadioVinculoAtividade->setValue   ( "atividade"             );
$obRadioVinculoAtividade->setLabel   ( "Atividade"             );
$obRadioVinculoAtividade->setNull    ( false                   );
$obRadioVinculoAtividade->setChecked ( !$boVinculo             );
$obRadioVinculoAtividade->obEvento->setOnChange( "atualizaFormularioFiltro();" );

$obRadioVinculoInscricao = new Radio;
$obRadioVinculoInscricao->setName    ( "boVinculoModalidade"   );
$obRadioVinculoInscricao->setValue   ( "inscricao"             );
$obRadioVinculoInscricao->setLabel   ( "Inscrição Econômica"   );
$obRadioVinculoInscricao->setNull    ( false                   );
$obRadioVinculoInscricao->setChecked ( $boVinculo              );
$obRadioVinculoInscricao->obEvento->setOnChange( "atualizaFormularioFiltro();" );

$obSpnBusca = new Span;
$obSpnBusca->setID("spnBusca");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgList                  );
$obForm->setTarget           ( "telaPrincipal"          );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
$obFormulario->setAjuda      ( "UC-05.02.13");
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );

$obFormulario->addTitulo     ( "Dados para filtro"      );
$obFormulario->addComponenteComposto ( $obRadioVinculoAtividade , $obRadioVinculoInscricao );
$obFormulario->addSpan       ( $obSpnBusca              );

$obFormulario->Ok();
$obFormulario->show();

sistemaLegado::executaFrameOculto("atualizaFormularioFiltro();");

?>
