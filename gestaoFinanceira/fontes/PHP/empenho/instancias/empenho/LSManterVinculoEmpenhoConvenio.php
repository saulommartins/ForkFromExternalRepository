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
    * Arquivo de lista de Convênios.
    * Data de Criação: 17/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.38

    $Id: LSManterVinculoEmpenhoConvenio.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

//PESQUISA
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoConvenio.class.php" );
$obTLicitacaoConvenio = new TLicitacaoConvenio();
$stFiltro  = " GROUP BY num_convenio			 	\n";
$stFiltro .= " 		  , exercicio					\n";
$stFiltro .= "		  , cgm_responsavel				\n";
$stFiltro .= "		  , cod_objeto         			\n";
$stFiltro .= "		  , cod_tipo_convenio  			\n";
$stFiltro .= "		  , cod_tipo_documento 			\n";
$stFiltro .= "		  , cod_documento				\n";
$stFiltro .= "		  , observacao 					\n";
$stFiltro .= "		  , dt_assinatura				\n";
$stFiltro .= "		  , dt_vigencia					\n";
$stFiltro .= "		  , valor						\n";
$stFiltro .= "		  , inicio_execucao				\n";
$stFiltro .= "		  , fundamentacao 				\n";
$stOrder  .= " ORDER BY num_convenio 				\n";
$obTLicitacaoConvenio->recuperaTodos( $rsRecordset, $stFiltro, $stOrder );

//LISTA DE CONVENIOS
$table = new TableTree();
$table->setRecordset( $rsRecordset );
$table->setArquivo( 'OCManterVinculoEmpenhoConvenio.php');
$table->setParametros( array( "num_convenio" , "exercicio", "dt_assinatura", "dt_vigencia") );
$table->setComplementoParametros( "stCtrl=detalharConvenio");
$table->setSummary( 'Convênios' );
$table->Head->addCabecalho ( 'Convênio'      , 11  					 );
$table->Head->addCabecalho ( 'Fundamentação' , 74  					 );
$table->Head->addCabecalho ( 'Vigência'      , 15  					 );
$table->Body->addCampo	   ( '[num_convenio]/[exercicio]' , 'C' 	 );
$table->Body->addCampo	   ( '[fundamentacao]'			  , 'E' 	 );
$table->Body->addCampo	   ( '[dt_vigencia]'	   		  , 'C'   	 );
$table->Body->addAcao	   ( "selecionar" , "javascript:location.href='".$pgForm."?".Sessao::getId()."&stAcao=".$stAcao."&numConvenio=%s&exercicio=%s&dt_assinatura=%s&dt_vigencia=%s'", array( 'num_convenio' , 'exercicio', 'dt_assinatura', 'dt_vigencia' ) );

$table->montaHTML();
echo $table->getHtml();

//FORMULARIO
$obForm = new Form;
$obForm->setAction ( $pgForm );
$obForm->setTarget ( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obSpnListaDetalhes = new Span();
$obSpnListaDetalhes->setId( "spnListaDetalhes" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm    ( $obForm              );
$obFormulario->addHidden  ( $obHdnCtrl           );
$obFormulario->addSpan	  ( $obSpnListaDetalhes  );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
