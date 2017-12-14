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
    * Página de Formulário para configuração de Relatório de MODELOS
    * Data de Criação   : 22/05/2006

    * @author Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso : uc-06.02.02
*/

/*
$Log$
Revision 1.8  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:42:06  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GP_PAT_NEGOCIO."RPatrimonioNatureza.class.php"                                     );
include_once (CAM_GP_PAT_COMPONENTES."ISelectNatureza.class.php" );
include_once (CAM_GP_PAT_COMPONENTES."ISelectGrupo.class.php" );
include_once (CAM_GP_PAT_COMPONENTES."ISelectEspecie.class.php" );

$sID    =  '?'.Sessao::getId();
$stPrograma = "ManterClassificacaoGrupos";
$pgFilt = "FL".$stPrograma.".php$sID";
$pgList = "LS".$stPrograma.".php$sID";
$pgForm = "FM".$stPrograma.".php$sID";
$pgProc = "PR".$stPrograma.".php$sID";
$pgOcul = "OC".$stPrograma.".php$sID";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtSiglaClassificacao = new TextBox;
$obTxtSiglaClassificacao->setRotulo   ( "Sigla da Classificação"            );
$obTxtSiglaClassificacao->setName     ( "stSiglaClassificacao"              );
$obTxtSiglaClassificacao->setSize     ( 3                                   );
$obTxtSiglaClassificacao->setMaxLength( 3                                   );
$obTxtSiglaClassificacao->setTitle    ( "Informe a sigla da classificação." );
$obTxtSiglaClassificacao->setInteiro  ( false                               );
$obTxtSiglaClassificacao->setNull     ( false                               );
$obTxtSiglaClassificacao->obEvento->setOnKeyUp ("this.value = this.value.toUpperCase()");

$obIComp = new ISelectGrupo($obForm);
//$obIComp = new ISelectNatureza($obForm);
//$obIComp = new ISelectEspecie($obForm);
//$obIComp->obSelectGrupo->setNull (true);
//$obIComp->obISelectNatureza->setNull(true);
$obIComp->obSelectGrupo->obEvento->setOnChange ("buscaDados('BuscaSigla','".$pgOcul."','".$obForm->getAction()."','".$obForm->getTarget()."');");

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden        ( $obHdnAcao                    );
$obFormulario->addHidden        ( $obHdnCtrl                    );
$obFormulario->addTitulo        ( "Parâmetros do Módulo TCE-RJ" );
//$obFormulario->addComponente    ( $obCmbNatureza                );
//$obFormulario->addComponente    ( $obCmbGrupo                   );

//$obISelectGrupo->geraFormulario( $obFormulario );
$obIComp->geraFormulario( $obFormulario );

$obFormulario->addComponente    ( $obTxtSiglaClassificacao      );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
