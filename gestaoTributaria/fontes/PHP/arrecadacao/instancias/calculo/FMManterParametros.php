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
  * Página de Formulario para DEFINIR PARAMETROS - MODULO ARRECADACAO
  * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: FMManterParametros.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.11  2006/09/15 11:50:26  fabio
corrigidas tags de caso de uso

Revision 1.10  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterParametros";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PRManterCalculo.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write( "link", "" );

// instancia objeto
$obRARRParametroCalculo = new RARRParametroCalculo;
// pegar mascara de credito
$obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

// DEFINE OBJETOS DO FORMULARIO
$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo    ( "Crédito"        );
$obBscCredito->setTitle     ( "Busca crédito."   );
$obBscCredito->setId        ( "stCredito"       );
$obBscCredito->setNULL      ( false );
$obBscCredito->obCampoCod->setName      ("inCodCredito"             );
$obBscCredito->obCampoCod->setValue     ( $_REQUEST["inCodCredito"] );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('BuscaDoCredito');");
$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obBscFormulaCalculo = new BuscaInner;
$obBscFormulaCalculo->setRotulo ( "Fórmula de Cálculo"                             );
$obBscFormulaCalculo->setNULL   ( false );
$obBscFormulaCalculo->setTitle  ( "Fórmula que executara o cálculo para o crédito."  );
$obBscFormulaCalculo->setId     ( "stFormula"                                       );
$obBscFormulaCalculo->obCampoCod->setName   ( "inCodFuncao" );
$obBscFormulaCalculo->obCampoCod->setValue  ( $_REQUEST["inCodFuncao"]  );
$obBscFormulaCalculo->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
$obBscFormulaCalculo->obCampoCod->setMascara   ( "999.99.99" );
$obBscFormulaCalculo->setFuncaoBusca    ( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFormula','','".Sessao::getId()."&stCodModulo=12,14,25&stCodBiblioteca=2,1&','800','550');" );

$arVenal = array(
                    array ("codigo"=>"venal_predial", "valor"=>"Venal Predial"),
                    array ("codigo"=>"venal_terreno", "valor"=>"Venal Terreno"),
                );

$rsVenal = new RecordSet;
$rsVenal->preenche ($arVenal);

$obCmbVenal = new Select;
$obCmbVenal->setName        ("stVenal"      );
$obCmbVenal->setId          ("stVenal"      );
$obCmbVenal->setRotulo      ("Valor Correspondente");
$obCmbVenal->setTitle       ("Valor venal correspondente para o cálculo.");
$obCmbVenal->setCampoId     ("codigo"       );
$obCmbVenal->setCampoDesc   ("valor"        );
$obCmbVenal->addOption      ("","Selecione" );
$obCmbVenal->preencheCombo  ($rsVenal       );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgProc           );
$obForm->setTarget           ( "oculto"          );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addTitulo     ( "Dados para Parâmetros"  );
$obFormulario->addComponente ( $obBscCredito            );
$obFormulario->addComponente ( $obBscFormulaCalculo     );
$obFormulario->addComponente ( $obCmbVenal              );
$obFormulario->Ok();
$obFormulario->show();
