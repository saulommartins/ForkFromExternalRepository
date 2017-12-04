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
    * Página de Filtro para Manter Justificativas
    * Data de Criação: 29/09/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.10.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma    = "ManterJustificativa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::remove('link');

$stAcao = $request->get('stAcao');

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName('stCtrl');

$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

$obTxtCodigo = new Inteiro();
$obTxtCodigo->setRotulo("Código");
$obTxtCodigo->setSize(5);
$obTxtCodigo->setMascara("999");
$obTxtCodigo->setInteiro(true);
$obTxtCodigo->setName("inCodJustificativa");
$obTxtCodigo->setId("inCodJustificativa");
$obTxtCodigo->setTitle("Informe o código da justificativa.");

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo   ( "Descrição"   );
$obTxtDescricao->setTitle    ( "Informe a descrição da justificativa (Faltas Justificadas, Atestados, Licença Médica, Férias)" );
$obTxtDescricao->setName     ( "stDescricaoJustificativa" );
$obTxtDescricao->setId       ( "stDescricaoJustificativa" );
$obTxtDescricao->setMaxLength( 80            );
$obTxtDescricao->setSize     ( 80            );

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo			( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addTitulo            ( "Dados para Filtro"           );
$obFormulario->addComponente        ( $obTxtCodigo                  );
$obFormulario->addComponente        ( $obTxtDescricao               );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
