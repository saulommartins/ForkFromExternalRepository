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
 * Filtro para Compra Direta
 * Data de Criação   : 29/01/2007

 * @author Analista: Gelson
 * @author Desenvolvedor: Lucas Teixeira Stephanou

 * @ignore

 * Casos de uso : uc-03.05.21

 $Id: FLManterAutorizacao.php 63841 2015-10-22 19:14:30Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GP_COM_COMPONENTES."ISelectModalidade.class.php";
require_once CAM_GP_COM_COMPONENTES."ISelectTipoObjeto.class.php";
require_once CAM_GP_COM_COMPONENTES."IPopUpEditObjeto.class.php";
require_once CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php";
require_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasModalidade.class.php";

$stAcao = $request->get('stAcao');
$stProgramaParcial = ($stAcao=='parcial') ? 'Parcial' : '';

# Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao".$stProgramaParcial;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o Hidden de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obEntidadeUsuario->setNull( true );

$obComprasModalidade = new TComprasModalidade();
$rsRecordSet = new RecordSet;
$stFiltro = " WHERE cod_modalidade NOT IN(4,5,10,11) ";
$obComprasModalidade->recuperaTodos($rsRecordSet,$stFiltro," ORDER BY cod_modalidade ");

$obISelectModalidadeLicitacao = new Select();
$obISelectModalidadeLicitacao->setRotulo    ( "Modalidade"                      );
$obISelectModalidadeLicitacao->setTitle     ( "Selecione a modalidade."         );
$obISelectModalidadeLicitacao->setName      ( "inCodModalidade"                 );
$obISelectModalidadeLicitacao->setNull      ( true                              );
$obISelectModalidadeLicitacao->setCampoID   ( "cod_modalidade"                  );
$obISelectModalidadeLicitacao->addOption    ( "","Selecione"                    );
$obISelectModalidadeLicitacao->setCampoDesc ( "[cod_modalidade] - [descricao]"  );
$obISelectModalidadeLicitacao->preencheCombo( $rsRecordSet                      );

$obTxtLicitacao = new TextBox();
$obTxtLicitacao->setName    ( "inCodigoLicitacao"               );
$obTxtLicitacao->setId      ( "inCodigoLicitacao"               );
$obTxtLicitacao->setRotulo  ( "Código Licitação"                );
$obTxtLicitacao->setTitle   ( "Informe o código da licitação."  );
$obTxtLicitacao->setInteiro ( true                              );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValue          ( 4                     );
$obPeriodicidade->setValidaExercicio( true                  );
$obPeriodicidade->obDataInicial->setName( "stDtInicial"     );
$obPeriodicidade->obDataFinal->setName  ( "stDtFinal"       );

$obMapa = new TextBox();
$obMapa->setRotulo ( 'Mapa'                         );
$obMapa->setTitle  ( 'Código do mapa de compras.'   );
$obMapa->setId     ( 'inCodMapa'                    );
$obMapa->setName   ( 'inCodMapa'                    );
$obMapa->setInteiro( true                           );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addComponente ( $obEntidadeUsuario            );
$obFormulario->addComponente ( $obISelectModalidadeLicitacao );
$obFormulario->addComponente ( $obTxtLicitacao               );
$obFormulario->addComponente ( $obPeriodicidade              );
$obFormulario->addComponente ( $obMapa                       );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
