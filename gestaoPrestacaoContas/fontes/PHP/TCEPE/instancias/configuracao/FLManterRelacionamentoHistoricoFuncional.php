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
     * 
    * Data de Criação   : 26/09/2014

    * @author Analista:
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: FMManterOrcamento.php 60054 2014-09-26 14:55:37Z lisiane $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once ( CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php" );
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

$stPrograma = "ManterRelacionamentoHistoricoFuncional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$obForm = new Form;
$obForm->setAction ( $pgForm );
$obForm->setTarget ( "telaPrincipal" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
$obROrcamentoEntidade->listarEntidades($rsEntidades);

$obCmbEntidades = new Select  ();
$obCmbEntidades->setRotulo    ('Entidade');
$obCmbEntidades->setId        ('inCodEntidade');
$obCmbEntidades->setName      ('inCodEntidade');
$obCmbEntidades->setCampoId   ('cod_entidade');
$obCmbEntidades->setCampoDesc ('[nom_cgm]');
$obCmbEntidades->addOption    ('', 'Selecione');
$obCmbEntidades->preencheCombo($rsEntidades);

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo('Configurações do Orçamento');
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';