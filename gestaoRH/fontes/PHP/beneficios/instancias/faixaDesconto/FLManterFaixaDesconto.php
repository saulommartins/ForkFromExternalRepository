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
* Página de Formulario de filtro para Faixa de descontos
* Data de Criação   : 11/07/2005

* @author Analista: Vandré Ramos
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 30880 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioVigencia.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterFaixaDesconto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write('stOrigem', "FL");

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

$obRdoTipoA = new Radio;
$obRdoTipoA->setName    ( "stTipo" );
$obRdoTipoA->setTitle   ( "Informe o tipo do benefício" );
$obRdoTipoA->setRotulo  ( "*Tipo" );
$obRdoTipoA->setLabel   ( "Vale-Transporte" );
$obRdoTipoA->setValue   ( "v" );
$obRdoTipoA->setChecked ( true );

$obRdoTipoV = new Radio;
$obRdoTipoV->setName    ( "stTipo" );
$obRdoTipoV->setRotulo  ( "*Tipo" );
$obRdoTipoV->setLabel   ( "Auxílio Refeição" );
$obRdoTipoV->setValue   ( "a");
$obRdoTipoV->setDisabled( true );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
$obFormulario->addTitulo     ( "Filtro para Seleção"   );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addComponenteComposto ( $obRdoTipoA, $obRdoTipoV );
$obFormulario->OK();
$obFormulario->show();

include_once( $pgJS );
?>
