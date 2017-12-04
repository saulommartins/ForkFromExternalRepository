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
  * Página de
  * Data de criação : 28/10/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 28271 $
    $Name$
    $Author: luiz $
    $Date: 2008-02-28 08:43:31 -0300 (Qui, 28 Fev 2008) $

    Caso de uso: uc-03.01.18
**/

/*
$Log$
Revision 1.11  2006/07/21 11:34:29  fernando
Inclusão do  Ajuda.

Revision 1.10  2006/07/06 14:07:05  diego
Retirada tag de log com erro.

Revision 1.9  2006/07/06 12:11:27  diego

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_GP_PAT_NEGOCIO."RPatrimonioAtributoPatrimonio.class.php");
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioBaixa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

require_once $pgJS;

$atributosPatrimonio = new RPatrimonioAtributoPatrimonio;

$atributosPatrimonio->listar($atributo);

$obForm = new Form;
$obForm->setAction( $pgOcul );
$obForm->setTarget( 'telaPrincipal' );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GP_PAT_INSTANCIAS."relatorio/OCRelatorioBaixa.php" );

$obHdnCtrl   = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

//Define o objeto de periodicidade para o formulário
$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setExercicio( Sessao::getExercicio() );
$obDtPeriodicidade->setNull     ( false );

//Define o objeto SelectMultiplo para armazenar a natureza
$obCmbAtributo = new Select();
$obCmbAtributo->setName         ( 'inCodAtributo'                    );
$obCmbAtributo->setRotulo       ( "Atributo"                         );
$obCmbAtributo->setTitle        ( "Selecione o atributo para o filtro." );
$obCmbAtributo->addOption       ( "", "Selecione"                    );
$obCmbAtributo->setCampoId      ( "cod_atributo"                     );
$obCmbAtributo->setCampoDesc    ( "nom_atributo"                     );
$obCmbAtributo->preencheCombo   ( $atributo                          );

$obCmbOrdem = new Select;
$obCmbOrdem->setRotulo              ( "Ordem"               );
$obCmbOrdem->setName                ( "inCodOrdem"          );
$obCmbOrdem->setStyle               ( "width: 200px"        );
$obCmbOrdem->addOption              ( "", "Selecione"       );
$obCmbOrdem->addOption              ( "1", "Código"         );
$obCmbOrdem->addOption              ( "2", "Descrição"      );
$obCmbOrdem->addOption              ( "3", "Data de baixa"  );
$obCmbOrdem->setNull                ( false                 );
$obCmbOrdem->setTitle               ( 'Selecione a ordenação do relatório.' );

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                          );
$obFormulario->setAjuda         ("UC-03.01.18");
$obFormulario->addHidden        ( $obHdnCaminho                    );
$obFormulario->addHidden        ( $obHdnCtrl                       );
$obFormulario->addTitulo        ( "Insira os Dados para Procura"   );
$obFormulario->addComponente    ( $obISelectEntidade               );
$obFormulario->addComponente    ( $obDtPeriodicidade               );
$obFormulario->addComponente    ( $obCmbOrdem                      );

if ($atributo->getNumlinhas() > 0) {
    $obFormulario->addComponente    ( $obCmbAtributo                   );
}

$obFormulario->OK();
$obFormulario->show();
