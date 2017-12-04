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
 * Página de Filtro Classificação Contábil
 * Data de Criação   : 10/11/2004

 * @author Analista: Diego Victoria
 * @author Desenvolvedor: Leandro André Zis

 * Casos de uso: uc-03.03.06

 $Id: FLManterItem.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaCatalogoClassificacao.class.php";

$stPrograma = "ManterItem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgOcul2 = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaCatalogoClassificacao.php';
$pgJs   = "JS".$stPrograma.".js";

$pgProx = $pgList;

include_once($pgJs );

//***********************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/
Sessao::remove('filtro');
Sessao::remove('link');

$obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem;

$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao;

if ($stAcao == "alterar" || $stAcao == "consultar") {
   $obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNaoPermiteManutencao(true);
}

$obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido   (false);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida (false);

$obSpnListaAtributos = new Span;
$obSpnListaAtributos->setID ('spnListaAtributos');

$obTxtCodItem = new TextBox();
$obTxtCodItem->setRotulo   ( "Código do Item"           );
$obTxtCodItem->setTitle    ( "Informe o código do item.");
$obTxtCodItem->setName     ( "inCodItem"                );
$obTxtCodItem->setValue    ( $inCodItem                 );

$obTxtDescricao = new TextBox();
$obTxtDescricao->setRotulo    ( "Descrição"   );
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setValue     ( $stDescricao  );
$obTxtDescricao->setMaxLength ( 80            );
$obTxtDescricao->setSize      ( 50            );
$obTxtDescricao->setTitle     ( 'Informe a descrição do item');

$obCmpTipoBusca = new TipoBusca( $obTxtDescricao );
$obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->listar( $rsTipo ) ;
$arRdTipo = array();
$inCodTipo = 0;

$obRdTipo = new Radio;
$obRdTipo->setRotulo    ( "Tipo"                               );
$obRdTipo->setTitle     ( "Selecione o tipo de item desejado." );
$obRdTipo->setName      ( "inCodTipo"                          );
$obRdTipo->setLabel     ( "Todos"                              );
$obRdTipo->setValue     ( "0"                                  );
$obRdTipo->setChecked   ( true                                 );
$obRdTipo->setNull      ( false                                );
$arRdTipo[] = $obRdTipo;

for ($i = 0; $i < $rsTipo->getNumLinhas(); $i++) {
   if ($rsTipo->getCampo('cod_tipo') != 0) {
      $obRdTipo = new Radio;
      $obRdTipo->setRotulo  ( "Tipo"                                      );
      $obRdTipo->setName    ( "inCodTipo"                                 );
      $obRdTipo->setLabel   ( $rsTipo->getCampo('descricao')              );
      $obRdTipo->setValue   ( $rsTipo->getCampo('cod_tipo')               );
      $obRdTipo->setChecked ( $inCodTipo == $rsTipo->getCampo('cod_tipo') );
      $obRdTipo->setNull    ( false                                       );
      $arRdTipo[] = $obRdTipo;
      $rsTipo->proximo();
   }
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgList );
$obForm->setTarget ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( "UC-03.03.06");
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addTitulo         ( "Dados para Filtro" );
$obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
$obFormulario->addSpan           ( $obSpnListaAtributos    );
$obFormulario->addTitulo         ( "Dados do Item" );
$obFormulario->addComponente     ( $obTxtCodItem );
$obFormulario->addComponente     ( $obCmpTipoBusca );
$obFormulario->agrupaComponentes ( $arRdTipo );

$obFormulario->OK   ();
$obFormulario->show ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
