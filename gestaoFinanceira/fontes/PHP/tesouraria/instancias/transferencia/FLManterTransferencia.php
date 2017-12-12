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
    * Filtro para funcionalidade Manter Transferencia
    * Data de Criação   : 09/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.09
*/

/*
$Log$
Revision 1.10  2006/07/05 20:40:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETTERMINAL );
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"          );
SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma      = "ManterTransferencia";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$rsEntidades = new RecordSet;

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->addTransferencia();
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget ( "oculto" );
$obForm->setTarget( "telaPrincipal");

$obIApplet = new IAppletTerminal( $obForm );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao );

// DEFINE OBJETOS DO FORMULARIO
// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione a(s) Entidade(s) a pesquisar" );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );
// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordSet );

//Define Objeto Text para Nr. do Terminal
$obTxtDataBoletim = new Data;
$obTxtDataBoletim->setName      ( "stDataBoletim"                              );
$obTxtDataBoletim->setValue     ( $stDataBoletim                               );
$obTxtDataBoletim->setRotulo    ( "Data do Boletim"                            );
$obTxtDataBoletim->setTitle     ( "Informe a Data do Boletim"                  );

//Define Objeto Text para Nr. do Terminal
$obTxtNroBoletim = new TextBox;
$obTxtNroBoletim->setName      ( "inNumeroBoletim"                              );
$obTxtNroBoletim->setValue     ( $inNumeroBoletim                               );
$obTxtNroBoletim->setRotulo    ( "Número Boletim"                               );
$obTxtNroBoletim->setTitle     ( "Informe o Número do Boletim"                  );
$obTxtNroBoletim->setMaxLength ( 3                                              );
$obTxtNroBoletim->setSize      ( 4                                              );

$obBscContaCredito = new BuscaInner;
$obBscContaCredito->setRotulo ( "Conta a Crédito" );
$obBscContaCredito->setTitle  ( "Informe a Conta a Crédito" );
$obBscContaCredito->setId     ( "stNomContaCredito"  );
$obBscContaCredito->setValue  ( ""                   );
$obBscContaCredito->obCampoCod->setName     ( "inCodPlanoCredito" );
$obBscContaCredito->obCampoCod->setSize     ( 10           );
$obBscContaCredito->obCampoCod->setMaxLength( 8            );
$obBscContaCredito->obCampoCod->setValue    ( ""           );
$obBscContaCredito->obCampoCod->setAlign    ( "left"       );
$obBscContaCredito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlanoCredito','stNomContaCredito','banco','".Sessao::getId()."','800','550');");
$obBscContaCredito->setValoresBusca(CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(),'frm', 'banco');

$obBscContaDebito = new BuscaInner;
$obBscContaDebito->setRotulo ( "Conta a Débito" );
$obBscContaDebito->setTitle  ( "Informe a Conta a Crédito" );
$obBscContaDebito->setId     ( "stNomContaDebito"  );
$obBscContaDebito->setValue  ( ""   );
$obBscContaDebito->obCampoCod->setName     ( "inCodPlanoDebito" );
$obBscContaDebito->obCampoCod->setSize     ( 10           );
$obBscContaDebito->obCampoCod->setMaxLength( 8            );
$obBscContaDebito->obCampoCod->setValue    ( ""  );
$obBscContaDebito->obCampoCod->setAlign    ( "left"       );
$obBscContaDebito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlanoDebito','stNomContaDebito','banco','".Sessao::getId()."','800','550');");
$obBscContaDebito->setValoresBusca(CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(),'frm', 'banco');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                   );
$obFormulario->addHidden    ( $obHdnCtrl                );
$obFormulario->addHidden    ( $obHdnAcao                );
$obFormulario->addHidden    ( $obIApplet                );
$obFormulario->addTitulo    ( "Dados para Filtro"       );
$obFormulario->addComponente( $obCmbEntidades           );
$obFormulario->addComponente( $obTxtDataBoletim         );
$obFormulario->addComponente( $obTxtNroBoletim          );
$obFormulario->addComponente( $obBscContaCredito        );
$obFormulario->addComponente( $obBscContaDebito         );

$obFormulario->Ok();

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
