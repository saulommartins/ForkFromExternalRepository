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
 * Página de Formulario
 * Data de Criação: 25/10/2007

 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @ignore

 $Id:$

 * Casos de uso:

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_PAT_COMPONENTES."ISelectEspecie.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";

# Define o nome dos arquivos PHP
$stPrograma = "ManterInventario";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";

$pgGeraRelatorioHistoricoInventario = "OCGeraRelatorioHistoricoInventario.php";

$stCaminho = CAM_GP_PAT_INSTANCIAS."inventario/";

$stAcao = $request->get('stAcao');

if ($stAcao == 'anular') {
    $stAcao = 'excluir';
}

$obForm = new Form;

if ($_REQUEST['stAcao'] != 'historico') {
    $obForm->setAction ( $pgList );
} else {
    $obForm->setAction ( $stCaminho.$pgGeraRelatorioHistoricoInventario."?".Sessao::getId());
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setId    ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao );

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

$obTxtExercicio = new TextBox;
$obTxtExercicio->setId     ('stExercicio');
$obTxtExercicio->setName   ('stExercicio');
$obTxtExercicio->setRotulo ('Exercicio'  );
$obTxtExercicio->setValue  (Sessao::getExercicio());
$obTxtExercicio->setLabel  (true);

$obTxtInventario = new TextBox;
$obTxtInventario->setId     ('inCodInventario');
$obTxtInventario->setName   ('inCodInventario');
$obTxtInventario->setRotulo ('Código Inventário');

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addHidden     ( $obHdnAcao      );
$obFormulario->addComponente ( $obTxtExercicio );
$obFormulario->addComponente ( $obISelectEntidade);
$obFormulario->addComponente ( $obTxtInventario );

$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
