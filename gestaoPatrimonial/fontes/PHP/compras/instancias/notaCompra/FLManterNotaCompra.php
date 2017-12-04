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
    * Página de Filtro de nota de compra
    * Data de Criação   : 08/12/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Thiago La Delfa Cabelleira

    * @ignore

    * Casos de uso: uc-03.04.29
*/
/*
$Log$
Revision 1.3  2006/12/20 11:54:31  thiago
 UC 03.04.29

Revision 1.2  2006/12/13 17:16:39  thiago
 nota de compra

Revision 1.1  2006/12/11 10:45:18  thiago
arquivos para nota de compra

Revision 1.1  2006/12/11 10:20:48  thiago
filtro para nota de compra

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");
include_once ( CAM_GF_EMP_COMPONENTES."IPopUpEmpenho.class.php" );
include_once ( CAM_GP_COM_COMPONENTES."IPopUpOrdemCompra.class.php" );

$stPrograma = "ManterNotaCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//include ($pgJs);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obForm = new Form;
$obForm->setAction ( $pgList );

$obEmpenho = new IPopUpEmpenho( $obForm );
$obEmpenho->obCampoCod->setDisabled ( false );
$stTmp = $obEmpenho->obCampoCod->obEvento->getOnChange();
$obEmpenho->obCampoCod->obEvento->onChange = "";
$obEmpenho->obCampoCod->obEvento->setOnChange( "executaFuncaoAjax('BuscaPreEmpenho');");
$obEmpenho->obITextBoxSelectEntidadeUsuario->setNull(true);

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio( true              );
$obPeriodicidade->setNull           ( true              );
$obPeriodicidade->setValue          ( 4                 );

//Número da Nota de Compra
$obTxtNumNotaCompra = new TextBox;
$obTxtNumNotaCompra->setName  ( "num_nota" );
$obTxtNumNotaCompra->setId  ( "num_nota" );
$obTxtNumNotaCompra->setRotulo( "Número da Nota de Compra" );

$obPopCodOrdem = new IPopUpOrdemCompra($obForm);
$obPopCodOrdem->setTipo('notaFiscal');
$obPopCodOrdem->obTxtExercicioOrdemCompra->setNull(true);
$obPopCodOrdem->obTxtEntidade->setNull(true);
$obPopCodOrdem->setNull(true);
//------------------------------------------

//Definição do formulário
$obFormulario = new Formulario;
//carrega a lista por padrao
$obFormulario->addForm       ( $obForm             );
//Define o caminho de ajuda do Caso de uso (padrão no Framework)
$obFormulario->setAjuda      ("UC-03.05.29"        );
$obFormulario->addHidden     ( $obHdnCtrl          );
$obFormulario->addHidden     ( $obHdnAcao          );
//$obFormulario->addHidden     ( $obHdnEntidadeEmpenho );
$obFormulario->addTitulo     ( "Dados para Filtro:" );
//$obFormulario->addComponente ( $obTxtNumEmpenho    );
$obEmpenho->geraFormulario($obFormulario);
//$obFormulario->addComponente ( $obDataNotaCompra   );
$obFormulario->addComponente ( $obPeriodicidade    );
$obFormulario->addComponente ( $obTxtNumNotaCompra );

$obPopCodOrdem->geraFormulario($obFormulario);

$obFormulario->Ok();
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
