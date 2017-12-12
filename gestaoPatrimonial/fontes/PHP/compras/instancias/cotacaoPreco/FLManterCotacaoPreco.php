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
    * Página de Filtro de Cotação de Preços
    * Data de Criação   : 18/09/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    * Casos de uso: uc-03.04.04
*/

/*
$Log$
Revision 1.1  2006/09/18 14:41:56  cleisson
Inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

include_once( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php"         );
include_once( CAM_GP_COM_COMPONENTES."IPopUpFornecedor.class.php"   );
include_once( CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php"        );
include_once( CAM_GP_COM_COMPONENTES."IMontaSolicitacao.class.php"  );

$stLink = "";

//Define o nome dos arquivos PHP
$stPrograma = "ManterCotacaoPreco";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
        $stAcao = "alterar";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obForm = new Form;
$obForm->setAction ( $pgList  );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio( true              );
$obPeriodicidade->setNull           ( false             );
$obPeriodicidade->setValue          ( 4                 );

$obBscItem = new IPopUpItem($obForm);
$obBscItem->setNull( true );
$obBscItem->setRetornaUnidade( false );

$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setNull(true);

$obSolicitacao      = new IMontaSolicitacao($obForm);
$obSolicitacao->obExercicio->setRotulo("Exercício da Solicitação");
$obSolicitacao->obITextBoxSelectEntidade->setRotulo("Entidade da Solicitação");

//Define componente para selecionar o Mapa de compras
/*
INCLUIR COMPONENTE
*/

//Define componente para Fornecedor
$obFornecedor = new IPopUpFornecedor($obForm);
$obFornecedor->setId ( "stNomFornecedor" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm                     );
$obFormulario->setAjuda("UC-03.04.04"               );
$obFormulario->addHidden( $obHdnAcao                );
$obFormulario->addHidden( $obHdnCtrl                );
$obFormulario->addTitulo( "Dados para Filtro"       );
$obFormulario->addComponente( $obPeriodicidade      );
$obFormulario->addComponente( $obBscItem            );
$obFormulario->addComponente( $obBscMarca           );
$obSolicitacao->geraFormulario($obFormulario        );
//$obFormulario->addComponente  ( $obMontaMapa      );
$obFormulario->addComponente( $obFornecedor         );
$obFormulario->Ok();
$obFormulario->Show();
