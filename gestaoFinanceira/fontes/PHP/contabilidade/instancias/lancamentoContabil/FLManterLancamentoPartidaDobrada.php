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
/*
    * Filtro de Alteração de Partida Dobrada
    * Data de Criação   : 05/08/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: FLManterLancamentoPartidaDobrada.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php"                        );

$stPrograma = "ManterLancamentoPartidaDobrada";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    Sessao::remove('filtro');

    $obForm = new Form;
    $obForm->setAction( $pgList  );
    $obForm->setTarget( "telaPrincipal" );

    $obHdnAcao =  new Hidden;
    $obHdnAcao->setName  ( "stAcao" );
    $obHdnAcao->setValue ( $_REQUEST['stAcao'] );

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName  ( "stCtrl" );
    $obHdnCtrl->setValue ( $stCtrl  );

    $obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
    $obISelectEntidade->obTextBox->setNull(false);
    $obISelectEntidade->setNull(false);

    $obTxtCodLote = new TextBox;
    $obTxtCodLote->setName            ( "inCodLote"             );
    $obTxtCodLote->setId              ( "inCodLote"             );
    $obTxtCodLote->setValue           ( $inCodLote              );
    $obTxtCodLote->setRotulo          ( "Nr. do Lote"           );
    $obTxtCodLote->setTitle           ( "Informe o Nro do Lote" );
    $obTxtCodLote->setInteiro         ( true                    );
    $obTxtCodLote->setNull            ( true                    );
    $obTxtCodLote->obEvento->setOnBlur("");

    $obTxtNomLote = new TextBox;
    $obTxtNomLote->setName     ( "stNomLote"    );
    $obTxtNomLote->setId       ( "stNomLote"    );
    $obTxtNomLote->setValue    ( $stNomLote     );
    $obTxtNomLote->setRotulo   ( "Nome do Lote" );
    $obTxtNomLote->setTitle    ( "Informe o Nome do Lote"  );
    $obTxtNomLote->setNull     ( true           );
    $obTxtNomLote->setSize     ( 80             );
    $obTxtNomLote->setMaxLength( 80             );

    $obDtLote  = new Data();
    $obDtLote->setName   ( "stDtLote" );
    $obDtLote->setId     ( "stDtLote" );
    $obDtLote->setValue  ( $stDtLote  );
    $obDtLote->setRotulo ( "Data" );
    $obDtLote->setTitle  ( "Informe a Data" );
    $obDtLote->setNull   ( true );

    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );
    $obFormulario->addHidden( $obHdnAcao );
    $obFormulario->addHidden( $obHdnCtrl );
    $obFormulario->addTitulo( "Dados para Filtro" );
    $obFormulario->addComponente( $obISelectEntidade );
    $obFormulario->addComponente( $obTxtCodLote );
    $obFormulario->addComponente( $obTxtNomLote );
    $obFormulario->addComponente( $obDtLote     );
    $obFormulario->Ok();
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
