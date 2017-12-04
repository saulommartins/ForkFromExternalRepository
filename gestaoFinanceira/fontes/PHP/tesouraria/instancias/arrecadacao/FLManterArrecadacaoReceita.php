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
    * Filtro para funcionalidade Manter Arrecadacao
    * Data de Criação   : 21/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30692 $
    $Name$
    $Autor:$
    $Date: 2008-03-11 15:05:26 -0300 (Ter, 11 Mar 2008) $

    * Casos de uso: uc-02.04.04
*/

/*
$Log$
Revision 1.10  2007/07/25 15:11:39  cako
Bug#9567#

Revision 1.9  2006/12/21 11:52:43  cako
Bug #7796#

Revision 1.8  2006/11/21 22:01:20  cleisson
Bug #7544#

Revision 1.7  2006/09/01 16:56:54  jose.eduardo
uc-02.04.04

Revision 1.6  2006/07/05 20:38:50  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETTERMINAL );
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php"          );
SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma      = "ManterArrecadacaoReceita";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    // DEFINICAO DOS COMPONENTES
    $obForm = new Form;
    $obForm->setAction( $pgList );
    $obForm->setTarget ( "oculto" );
    $obForm->setTarget( "telaPrincipal");

    $stCtrl = isset($_REQUEST["stCtrl"]) ? $_REQUEST["stCtrl"] : '';

    // OBJETOS HIDDEN
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName  ( "stCtrl" );
    $obHdnCtrl->setValue (  $stCtrl );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName  ( "stAcao" );
    $obHdnAcao->setValue ( $stAcao );

    $obApplet = new IAppletTerminal( $obForm );

    // DEFINE OBJETOS DO FORMULARIO
    // Define SELECT multiplo para codigo da entidade
    $obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario;

    $obPeriodicidade = new Periodicidade();
    $obPeriodicidade->setExercicio      ( Sessao::getExercicio());
    $obPeriodicidade->setValue          ( 4                 );

    //Define Objeto TextBox para codigo do boletim
    $obTxtCodBoletim = new TextBox();
    $obTxtCodBoletim->setId      ( "inCodBoletim"       );
    $obTxtCodBoletim->setName    ( "inCodBoletim"       );
    $obTxtCodBoletim->setRotulo  ( "Número Boletim"     );
    $obTxtCodBoletim->setTitle   ( "Número do Boletim"  );
    $obTxtCodBoletim->setInteiro ( true                 );

    //Define Objeto Data para data do boletim
    $obTxtDtBoletim = new Data();
    $obTxtDtBoletim->setId        ( "stDtBoletim"     );
    $obTxtDtBoletim->setName      ( "stDtBoletim"     );
    $obTxtDtBoletim->setRotulo    ( "Data do Boletim" );
    $obTxtDtBoletim->setTitle     ( "Data do Boletim" );

    //Define Objeto Label
    $obLabel = new Label();
    $obLabel->setValue   ( ' até ' );

    // Define Objeto BuscaInner para conta
    $obBscConta = new BuscaInner;
    $obBscConta->setRotulo ( "Conta"       );
    $obBscConta->setTitle  ( "Informe a Conta Banco que deseja pesquisar" );
    $obBscConta->setId     ( "stNomConta"  );
    $obBscConta->setNull   ( true          );
    $obBscConta->obCampoCod->setName     ( "inCodPlano" );
    $obBscConta->obCampoCod->setSize     ( 10           );
    $obBscConta->obCampoCod->setNull     ( true         );
    $obBscConta->obCampoCod->setMaxLength( 8            );
    $obBscConta->obCampoCod->setAlign    ( "left"       );
    $obBscConta->setFuncaoBusca("abrePopUpContas('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlano','stNomConta','tes_arrec','".Sessao::getId()."','800','550');");
    $obBscConta->setValoresBusca(CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(),$obForm->getName(),'tes_arrec');

    // Define Objeto BuscaInner para conta
    $obBscReceita = new BuscaInner;
    $obBscReceita->setRotulo ( "Cód. Receita"       );
    $obBscReceita->setTitle  ( "Digite o Reduzido da Receita a Arrecadar" );
    $obBscReceita->setId     ( "stNomReceita" );
    $obBscReceita->setNull   ( true           );
    $obBscReceita->obCampoCod->setName     ( "inCodReceita" );
    $obBscReceita->obCampoCod->setSize     ( 10             );
    $obBscReceita->obCampoCod->setNull     ( true           );
    $obBscReceita->obCampoCod->setMaxLength( 8              );
    $obBscReceita->obCampoCod->setAlign    ( "left"         );
    $obBscReceita->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."receita/LSReceita.php','frm','inCodReceita','stNomReceita','receitaArrec','".Sessao::getId()."','800','550');");
    $obBscReceita->setValoresBusca(CAM_GF_CONT_POPUPS.'receita/OCReceita.php?'.Sessao::getId(),$obForm->getName(), 'receitaArrec' );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm      ( $obForm               );
    $obFormulario->addHidden    ( $obHdnCtrl            );
    $obFormulario->addHidden    ( $obHdnAcao            );
    $obFormulario->addHidden    ( $obApplet             );
    $obFormulario->addTitulo    ( "Dados para Filtro"   );
    $obFormulario->addComponente( $obISelectMultiploEntidadeUsuario );
    $obFormulario->addComponente( $obPeriodicidade );
    $obFormulario->addComponente( $obTxtCodBoletim      );
    $obFormulario->addComponente( $obTxtDtBoletim       );
    $obFormulario->addComponente( $obBscConta           );
    $obFormulario->addComponente( $obBscReceita         );

    $obFormulario->Ok();

    $obFormulario->show();
}
SistemaLegado::LiberaFrames();
include_once( $pgJs );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
