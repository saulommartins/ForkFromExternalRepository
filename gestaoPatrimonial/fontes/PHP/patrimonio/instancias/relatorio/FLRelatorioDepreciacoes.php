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
  * Página de Filtro para emissão do relatório de depreciação de bens
  * Data de criação : 10/12/2009

  * @copyright CNM Confederação Nacional dos Municípios.
  * @link http://www.cnm.org.br CNM Confederação Nacional dos Municípios.

  * @author Desenvolvedor: Cassiano Ferreira

*
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";

 //Define o nome dos arquivos PHP
$stPrograma = "RelatorioDepreciacoes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCGera".$stPrograma.".php";

$obForm = new Form;
$obForm->setAction( $pgOcul );

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setName('stExercicio');
$obTxtExercicio->setValue(date('Y'));
$obTxtExercicio->setNull(false);

$obIMontaClassificacao = new IMontaClassificacao( $obForm );
$obIMontaClassificacao->setNull(true);

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setName('stTipoRelatorio');
$obCmbTipoRelatorio->setRotulo('Tipo Relatório');
$obCmbTipoRelatorio->addOption('analitico', 'Analítico');
$obCmbTipoRelatorio->addOption('sintetico', 'Sintético');

$arCompetenciaDepreciacao = array(  1=>false,
                                    2=>false,
                                    3=>false,
                                    4=>false,
                                    6=>false,
                                    12=>false);

$inCompetenciaDepreciacao   = SistemaLegado::pegaConfiguracao( 'competencia_depreciacao',6, Sessao::getExercicio() );
$arCompetenciaDepreciacao[$inCompetenciaDepreciacao ? $inCompetenciaDepreciacao : 1] = true;
$obCmbCompetenciaDepreciacao = new Select;
$obCmbCompetenciaDepreciacao->setName   ('inCompetenciaDepreciacao');
$obCmbCompetenciaDepreciacao->setRotulo ('Competência Depreciação');
$obCmbCompetenciaDepreciacao->setNull   (false);
$obCmbCompetenciaDepreciacao->addOption ('1',  'Mensal'          , $arCompetenciaDepreciacao[1] );
$obCmbCompetenciaDepreciacao->addOption ('2',  'Bimestral'       , $arCompetenciaDepreciacao[2] );
$obCmbCompetenciaDepreciacao->addOption ('3',  'Trimestral'      , $arCompetenciaDepreciacao[3] );
$obCmbCompetenciaDepreciacao->addOption ('4',  'Quadrimestral'   , $arCompetenciaDepreciacao[4] );
$obCmbCompetenciaDepreciacao->addOption ('6',  'Semestral'       , $arCompetenciaDepreciacao[6] );
$obCmbCompetenciaDepreciacao->addOption ('12', 'Anual'           , $arCompetenciaDepreciacao[12]);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo    ( 'Classificação' );
$obFormulario->addComponente($obTxtExercicio);
$obFormulario->addComponente( $obISelectEntidade      );
$obIMontaClassificacao->geraFormulario( $obFormulario );
$obFormulario->addComponente($obCmbTipoRelatorio);
$obFormulario->addComponente($obCmbCompetenciaDepreciacao);
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
