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
    * Pagina de processamento para Grupos de Credito
    * Data de Criação   : 25/05/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * $Id: FMManterTipoSuspensao.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.03.07
*/

/*
$Log$
Revision 1.6  2006/09/15 11:23:59  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRTipoSuspensao.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoSuspensao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

// instancia regra
//$obRARRTipoSuspensao = new RARRTipoSuspensao;
//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName( "inCodigoTipoSuspensao" );
$obHdnCodigoTipo->setValue( $_REQUEST["inCodigoTipoSuspensao"] );

$obLblCodigoTipo = new Label;
$obLblCodigoTipo->setRotulo ( "Código"                          );
$obLblCodigoTipo->setTitle  ( "Código do Tipo de Suspensão"     );
$obLblCodigoTipo->setName   ( "stCodigoTipoSuspensao"           );
$obLblCodigoTipo->setId     ( "stCodigoTipoSuspensao"           );
$obLblCodigoTipo->setValue  ( $_REQUEST["inCodigoTipoSuspensao"]);

$obTxtDescricao = new TextBox ;
$obTxtDescricao->setName        ( "stDescricao"             );
$obTxtDescricao->setId          ( "stDescricao"             );
$obTxtDescricao->setMaxLength   ( 80                        );
$obTxtDescricao->setStyle       ( "width:200px;"            );
$obTxtDescricao->setRotulo      ( "Descrição"               );
$obTxtDescricao->setNull        ( false                     );
$obTxtDescricao->setValue       ( $_REQUEST["stDescricao"]  );

$obRdbEmitirSim = new Radio;
$obRdbEmitirSim->setRotulo     ( "Emitir Documentos"                                                      );
$obRdbEmitirSim->setName       ( "boEmitir"                                                               );
$obRdbEmitirSim->setLabel      ( "Sim"                                                                    );
$obRdbEmitirSim->setValue      ( "1"                                                                      );
$obRdbEmitirSim->setChecked    (  ($_REQUEST["boEmitir"] == "t" || $_REQUEST["boEmitir"] == "")          );
$obRdbEmitirSim->setTitle      ( "Emitir Documentos"                                                      );
$obRdbEmitirSim->setNull       ( false                                                                    );

$obRdbEmitirNao = new Radio;
$obRdbEmitirNao->setRotulo     ( "Emitir Documentos"                                                      );
$obRdbEmitirNao->setName       ( "boEmitir"                                                               );
$obRdbEmitirNao->setLabel      ( "Não"                                                                    );
$obRdbEmitirNao->setValue      ( "0"                                                                      );
$obRdbEmitirNao->setChecked    ( ($_REQUEST["boEmitir"] == "f")                                           );
$obRdbEmitirNao->setTitle      ( "Emitir Documentos"                                                      );
$obRdbEmitirNao->setNull       ( false                                                                    );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc     );
$obForm->setTarget( "oculto"    );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnCodigoTipo              );
$obFormulario->addTitulo            ( "Dados para Tipo de Baixa"    );
if ($stAcao == "alterar") {
    $obFormulario->addComponente    ( $obLblCodigoTipo     );
}
$obFormulario->addComponente        ( $obTxtDescricao      );
$obFormulario->addComponenteComposto( $obRdbEmitirSim,$obRdbEmitirNao);
$obFormulario->Ok();
$obFormulario->setFormFocus( $obTxtDescricao->getId() );
$obFormulario->show();

?>
