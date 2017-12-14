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
* Página de Formulario de filtro do objeto
* Data de Criação   : 11/10/2006

* @author Analista: Lucas Teixeira Stephanou
* @author Desenvolvedor: Lucas Teixeira Stephanou

  $Id: FLProcurarContrato.php 64256 2015-12-22 16:06:28Z michel $

* Casos de uso :uc-03.04.07, uc-03.04.05,
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'excluir');

Sessao::write('link', '');
Sessao::remove('filtro');

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $request->get('nomForm'));

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum'));

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral;
$obPeriodicidade               = new Periodicidade;
$obPeriodicidade->setExercicio ( Sessao::getExercicio());

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );

$obHdnBoFornecedor = new Hidden;
$obHdnBoFornecedor->setName( "boFornecedor" );
$obHdnBoFornecedor->setValue( $request->get('boFornecedor') );

if($request->get('boFornecedor')){
    $arFiltroBuscaContrato = Sessao::read('arFiltroBuscaContrato');
    $arFiltroBuscaContrato = (is_array($arFiltroBuscaContrato)) ? $arFiltroBuscaContrato : array();
    $inCodEntidade = $arFiltroBuscaContrato['inCodEntidade'];
    $inCodFornecedor = $arFiltroBuscaContrato['inCodFornecedor'];
    $stNomFornecedor = '';

    if(!empty($inCodFornecedor))
        $stNomFornecedor = sistemalegado::pegaDado("nom_cgm","sw_cgm","WHERE numcgm = '".$inCodFornecedor."' ");

    if(!empty($stNomFornecedor))
        $stNomFornecedor = $inCodFornecedor.' - '.$stNomFornecedor;

    $obITextBoxSelectEntidadeGeral->setCodEntidade($inCodEntidade);
    $obITextBoxSelectEntidadeGeral->setLabel(true);
    
    $obHdnCodFornecedor = new Hidden;
    $obHdnCodFornecedor->setName( "inCodFornecedor" );
    $obHdnCodFornecedor->setValue( $inCodFornecedor );
    
    $obLblFornecedor = new Label;
    $obLblFornecedor->setId    ('inCodFornecedor');
    $obLblFornecedor->setRotulo('Fornecedor');
    $obLblFornecedor->setValue ($stNomFornecedor);

    $obTxtExercicio = new TextBox;
    $obTxtExercicio->setName   ('stExercicio');
    $obTxtExercicio->setId     ('stExercicio');
    $obTxtExercicio->setValue  (Sessao::getExercicio());
    $obTxtExercicio->setRotulo ('Exercício');
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                        );
$obFormulario->addHidden     ( $obHdnForm                     );
$obFormulario->addHidden     ( $obHdnCampoNum                 );
$obFormulario->addHidden     ( $obHdnCampoNom                 );
$obFormulario->addHidden     ( $obHdnTipoBusca 	              );
$obFormulario->addHidden     ( $obHdnBoFornecedor             );
$obFormulario->addTitulo     ( "Dados para filtro"            );
$obFormulario->addHidden     ( $obHdnAcao                     );
$obFormulario->addComponente ( $obITextBoxSelectEntidadeGeral );
if($request->get('boFornecedor')){
    $obFormulario->addHidden     ( $obHdnCodFornecedor        );
    $obFormulario->addComponente ( $obLblFornecedor           );
    $obFormulario->addComponente ( $obTxtExercicio            );
}else
    $obFormulario->addComponente ( $obPeriodicidade           );

$obFormulario->OK();
$obFormulario->show();

?>
