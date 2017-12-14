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
    * Página de filtro
    * Data de Criação: 27/03/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage

    * @ignore

    * Caso de uso: uc-03.03.32

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNatureza.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ReemitirSaida";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$stAcao = $request->get('stAcao');

Sessao::write('link' , '');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction   ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

//Recupera as Saídas
$obTAlmoxarifadoNatureza = new TAlmoxarifadoNatureza();
$stFiltro  = " WHERE tipo_natureza = 'S'                                                                              \n";
$stFiltro .= " AND natureza.cod_natureza not in (5,11)                                                                \n";
$stFiltro .= " AND EXISTS (SELECT 1                                                                                   \n";
$stFiltro .= "               FROM almoxarifado.natureza_lancamento                                                    \n";
$stFiltro .= "                  , almoxarifado.lancamento_material                                                    \n";
$stFiltro .= "              WHERE natureza_lancamento.cod_natureza         = natureza.cod_natureza                    \n";
$stFiltro .= "                AND natureza_lancamento.tipo_natureza        = natureza.tipo_natureza                   \n";
$stFiltro .= "                AND lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento \n";
$stFiltro .= "                AND lancamento_material.num_lancamento       = natureza_lancamento.num_lancamento       \n";
$stFiltro .= "                AND lancamento_material.cod_natureza         = natureza_lancamento.cod_natureza         \n";
$stFiltro .= "                AND lancamento_material.tipo_natureza        = natureza_lancamento.tipo_natureza        \n";
$stFiltro .= "            )                                                                                           \n";
$stOrdem   = " ORDER BY natureza.descricao                                                                            \n";
$obTAlmoxarifadoNatureza->recuperaTodos( $rsNaturezaSaida, $stFiltro, $stOrdem );

//Instancia um select multiplo para as saídas
$obISelectMultiploNatureza = new SelectMultiplo();
$obISelectMultiploNatureza->setName   ('stNatureza');
$obISelectMultiploNatureza->setRotulo ( "Natureza de Saída" );
$obISelectMultiploNatureza->setNull   ( false );
$obISelectMultiploNatureza->setTitle  ( "Selecione a(s) Natureza(s) de Saída." );

//Seta as naturezas de saída disponiveis
$obISelectMultiploNatureza->SetNomeLista1 ('inCodNatureza');
$obISelectMultiploNatureza->setCampoId1   ('cod_natureza');
$obISelectMultiploNatureza->setCampoDesc1 ('descricao');
$obISelectMultiploNatureza->SetRecord1    ( $rsNaturezaSaida );

//Seta as naturezas de saída selecionadas
$obISelectMultiploNatureza->SetNomeLista2 ('inCodNaturezaSelecionados');
$obISelectMultiploNatureza->setCampoId2   ('cod_natureza');
$obISelectMultiploNatureza->setCampoDesc2 ('descricao');
$obISelectMultiploNatureza->SetRecord2    ( new RecordSet() );

//Periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setRotulo ( "Periodicidade" );
$obPeriodicidade->setTitle  ( "Informe a periodicidade do lançamento." );
$obPeriodicidade->setName   ( "dtNatureza"                 );
$obPeriodicidade->setNull   ( false                        );
$obPeriodicidade->setExercicio( Sessao::getExercicio() );

//Número de saída
$obTxtNumSaida = new TextBox;
$obTxtNumSaida->setName      ( "inNumSaida"                 );
$obTxtNumSaida->setValue     ( ""                           );
$obTxtNumSaida->setRotulo    ( "Número da saída"            );
$obTxtNumSaida->setTitle     ( "Informe o número da saída." );
$obTxtNumSaida->setNull      ( true                         );
$obTxtNumSaida->setMaxLength ( 4                            );
$obTxtNumSaida->setSize      ( 5                            );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda("UC-03.03.32");
$obFormulario->addHidden        ( $obHdnCtrl                    );
$obFormulario->addHidden        ( $obHdnAcao                    );
$obFormulario->addTitulo        ( "Dados para filtro"           );
$obFormulario->addComponente    ( $obISelectMultiploNatureza    );
$obFormulario->addComponente    ( $obPeriodicidade              );
$obFormulario->addComponente    ( $obTxtNumSaida                );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
