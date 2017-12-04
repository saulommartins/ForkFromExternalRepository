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
    * Página do Formulario de filtro para estornar baixa manual
    * Data de Criação   : 23/05/2006

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Fernando Piccini Cercato

    * @ignore

    * $Id: FLEstornarBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.7  2007/06/13 14:01:16  cercato
Bug #9387#

Revision 1.6  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "EstornarBaixaManual";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "estornar";
}

Sessao::write( "link", "" );

$obRMONAgencia = new RMONAgencia();
$rsBanco = new recordSet();
$rsAgencia = new recordSet();

$obRMONAgencia->obRMONBanco->listarBanco($rsBanco);

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obTxtLote = new TextBox;
$obTxtLote->setRotulo        ( "Código do Lote"                  );
$obTxtLote->setTitle         ( "Código do lote."                 );
$obTxtLote->setName          ( "inCodLote"                       );
$obTxtLote->setNull          ( true                               );
$obTxtLote->setSize          ( 12                                 );
$obTxtLote->setInteiro       ( true                               );

$obTxtNumeroCarne = new TextBox;
$obTxtNumeroCarne->setRotulo        ( "Número do Carnê"                  );
$obTxtNumeroCarne->setTitle         ( "Número do Carnê."                  );
$obTxtNumeroCarne->setName          ( "inNumCarne"                       );
$obTxtNumeroCarne->setValue         ( $inNumCarne                        );
$obTxtNumeroCarne->setNull          ( true                               );
$obTxtNumeroCarne->setSize          ( 12                                 );
$obTxtNumeroCarne->setMaxLength     ( 17                                 );
$obTxtNumeroCarne->setInteiro       ( true                               );

$obTxtBanco = new TextBox;
$obTxtBanco->setRotulo        ( "Banco"                            );
$obTxtBanco->setTitle         ( "Banco ao qual a agência pertence." );
$obTxtBanco->setName          ( "inNumbanco"                       );
$obTxtBanco->setValue         ( $inNumBanco                        );
$obTxtBanco->setSize          ( 10                                 );
$obTxtBanco->setMaxLength     ( 6                                  );
$obTxtBanco->setNull          ( true                               );
$obTxtBanco->setInteiro       ( true                               );
$obTxtBanco->obEvento->setOnChange ( "preencheAgencia('');" );

$obCmbBanco = new Select;
$obCmbBanco->setName          ( "cmbBanco"                   );
$obCmbBanco->addOption        ( "", "Selecione"              );
$obCmbBanco->setValue         ( $_REQUEST['inNumBanco']      );
$obCmbBanco->setCampoId       ( "num_banco"                  );
$obCmbBanco->setCampoDesc     ( "nom_banco"                  );
$obCmbBanco->preencheCombo    ( $rsBanco                     );
$obCmbBanco->setNull          ( true                         );
$obCmbBanco->setStyle         ( "width: 220px"               );
$obCmbBanco->obEvento->setOnChange ( "preencheAgencia('');"  );

$obTxtAgencia = new TextBox;
$obTxtAgencia->setRotulo        ( "Agência"                                     );
$obTxtAgencia->setTitle         ( "Agência bancária na qual a conta foi aberta." );
$obTxtAgencia->setName          ( "inNumAgencia"                                );
$obTxtAgencia->setValue         ( $inNumAgencia                                 );
$obTxtAgencia->setSize          ( 10                                            );
$obTxtAgencia->setMaxLength     ( 6                                             );
$obTxtAgencia->setNull          ( true                                          );
$obTxtAgencia->setInteiro       ( true                                          );

$obCmbAgencia = new Select;
$obCmbAgencia->setName          ( "cmbAgencia"                   );
$obCmbAgencia->addOption        ( "", "Selecione"                );
$obCmbAgencia->setValue         ( $_REQUEST['inNumAgencia']      );
$obCmbAgencia->setCampoId       ( "num_agencia"                  );
$obCmbAgencia->setCampoDesc     ( "nom_agencia"                  );
$obCmbAgencia->preencheCombo    ( $rsAgencia                     );
$obCmbAgencia->setNull          ( true                           );
$obCmbAgencia->setStyle         ( "width: 220px"                 );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo            ( "Contribuinte"                    );
$obBscCGM->setTitle             ( "Busca Contribuinte por CGM."      );
$obBscCGM->setId                ( "inNomCGM"                        );
$obBscCGM->setNull              ( true                              );
$obBscCGM->obCampoCod->setName  ( "inNumCGM"                        );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinte');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','inNomCGM','todos','".Sessao::getId()."','800','550');" );

$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull                  ( true                         );
$obBscInscricaoMunicipal->setRotulo                ( "Inscrição Imobiliária"      );
$obBscInscricaoMunicipal->setTitle                 ( "Informe a inscrição municipal do imóvel." );
$obBscInscricaoMunicipal->setId                    ( "stEnderecoImovel"           );
$obBscInscricaoMunicipal->obCampoCod->setName      ( "inInscricaoImobiliaria"     );
$obBscInscricaoMunicipal->obCampoCod->setInteiro   ( false                        );
$obBscInscricaoMunicipal->setFuncaoBusca( "abrePopUp( '".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php', 'frm', 'inInscricaoImobiliaria', 'stEnderecoImovel', 'todos', '".Sessao::getId()."', '800', '550' );" );
$obBscInscricaoMunicipal->obCampoCod->obEvento->setOnChange("buscaValor('buscaIM');");

$obBscInscricaoEconomica = new BuscaInner;
$obBscInscricaoEconomica->setId                   ( "stInscricaoEconomica"  );
$obBscInscricaoEconomica->setRotulo               ( "Inscrição Econômica"   );
$obBscInscricaoEconomica->setTitle                ( "Pessoa física ou jurídica cadastrada como inscrição econômica.");
$obBscInscricaoEconomica->obCampoCod->setName     ( "inInscricaoEconomica"  );
$obBscInscricaoEconomica->setNull                 ( true                    );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor('buscaIE');");
$obBscInscricaoEconomica->setFuncaoBusca          ( "abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
//$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addForm  ( $obForm          );
$obFormulario->addHidden( $obHdnCtrl       );
$obFormulario->addHidden( $obHdnAcao       );
$obFormulario->addComponente ( $obTxtLote );
$obFormulario->addComponente ( $obTxtNumeroCarne );
$obFormulario->addComponenteComposto ($obTxtBanco, $obCmbBanco      );
$obFormulario->addComponenteComposto ($obTxtAgencia, $obCmbAgencia  );
$obFormulario->addComponente ( $obBscCGM );
$obFormulario->addComponente ( $obBscInscricaoMunicipal );
$obFormulario->addComponente ( $obBscInscricaoEconomica );

$obFormulario->Ok();
$obFormulario->show();
?>
