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
    * Página de Formulário para efetuar Resgates
    * Data de Criação   : 31/08/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30835 $
    $Name$
    $Author: cako $
    $Date: 2007-11-01 11:22:24 -0200 (Qui, 01 Nov 2007) $

    * Casos de uso: uc-02.04.28

*/
/*
$Log$
Revision 1.11  2007/09/17 15:06:17  luciano
Ticket#10187#

Revision 1.10  2007/07/17 14:50:51  rodrigo_sr
Bug#9585#

Revision 1.9  2007/07/06 18:59:20  rodrigo_sr
Bug#9585#

Revision 1.8  2007/04/05 15:54:36  cako
Bug #8910#

Revision 1.7  2007/01/03 11:08:28  cako
Bug #7792#

Revision 1.6  2006/10/23 19:55:26  cako
Bug #7282#

Revision 1.5  2006/10/23 16:27:48  domluc
Add opção para multiplos boletins

Revision 1.4  2006/09/18 11:07:21  cako
implementação do uc-02.04.28

Revision 1.3  2006/09/14 10:27:24  cako
adição do tipo de transferencia

Revision 1.2  2006/09/01 10:29:45  cako
Implementação do uc-02.04.28

Revision 1.1  2006/08/31 12:14:09  cako
Implementação do uc-02.04.28

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETTERMINAL );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                       );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterResgate";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PRManterTransferencia.php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include 'JSManterDepositoRetirada.js';

$stAcao = "incluir";

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
$obRTesourariaBoletim->addTransferencia();

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnCodBoletim = new Hidden;
$obHdnCodBoletim->setId   ( "HdnCodBoletim" );
$obHdnCodBoletim->setName ( "HdnCodBoletim" );
$obHdnCodBoletim->setValue( $inCodBoletim );

$stHdnValor = "
    var erro = false;
    if (!document.getElementById('inCodBoletim')) {
        erro = true;
        mensagem += '@Não existe boletim para esta entidade! Você deverá abrir ou reabrir um boletim!';
    } else {
        if (document.getElementById('inCodBoletim').value == '') {
            erro = true;
            mensagem = '@Selecione um boletim!';
        }
    }
    if (document.frm.inCodPlanoCredito.value == '') {
         erro = true;
         mensagem += '@Deve ser informada uma conta para contrapartida!';
    }
    if (document.frm.inCodPlanoDebito.value == '') {
         erro = true;
         mensagem += '@Deve ser informada uma conta para resgate!';
    }
    var stValor;
    stValor = document.frm.nuValor.value;
    while (stValor.indexOf('.')>0) {
        stValor = stValor.replace('.','');
    }
    stValor = stValor.replace(',','.');
    if (stValor <= 0) {
         erro = true;
         mensagem += '@Campo Valor deve ser maior que 0,00!';
    }
    var stVlTransferencia;
    var stVlTransf;
    stVlTransferencia = document.frm.nuValor.value;
    while (stVlTransferencia.indexOf('.')>0) {
        stVlTransferencia = stVlTransferencia.replace('.','');
    }
    stVlTransf = stVlTransferencia.replace(',','.');
    if (erro == false) {
        if ( parseFloat(stVlTransf) > parseFloat(document.frm.nuSaldoContaAnalitica.value) ) { ;
            if ( confirm( 'O saldo da conta informada não é suficiente para realizar o resgate.\\n (Saldo da conta: R$ '+document.frm.nuSaldoContaAnaliticaBR.value+')\\n Se efetuar este resgate, o saldo da conta ficará negativo. Deseja continuar?')) {
                erro = false
            } else erro = true;
        }
    } ";

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $stHdnValor );

// Define Objeto Select para Entidade
$obIEntidade = new ITextBoxSelectEntidadeUsuario();
if($inCodEntidade)
    $obIEntidade->setCodEntidade( $inCodEntidade );

$obIEntidade->setNull(false);
$obIEntidade->obTextBox->obEvento->setOnChange( "if (this.value == '') { \n
                                                        frm.reset();\n
                                                }\n
                                                else { buscaDado( 'buscaBoletim');\n
                                                       montaParametrosGET( 'montaSpanContas' , 'inCodEntidade');
                                                       jQuery('#stExercicioEmpenho').removeProp('disabled');\n
                                                       montaParametrosGET('liberaCampoEmpenho'); }\n" );

$jsOnload = "if (document.getElementById('inCodEntidade').value != '') {
                buscaDado( 'buscaBoletim');\n
                montaParametrosGET( 'montaSpanContas' , 'inCodEntidade');
             }\n ";

$obIEntidade->obSelect->obEvento->setOnChange( "if (this.value == '') { \n
                                                        frm.reset();\n
                                                }\n
                                                else { buscaDado( 'buscaBoletim');\n
                                                       montaParametrosGET( 'montaSpanContas' , 'inCodEntidade');\n
                                                       jQuery('#stExercicioEmpenho').removeProp('disabled');\n
                                                       montaParametrosGET('liberaCampoEmpenho'); }\n" );
$obIEntidade->setTitle ( "Selecione a entidade." );

$obHdnDtBoletimAberto = new Hidden;
$obHdnDtBoletimAberto->setName ( "stDtBoletim" );
$obHdnDtBoletimAberto->setId   ( "stDtBoletim" );

$obSpanBoletim = new Span;
$obSpanBoletim->setId( "spnBoletim" );

$obSpanContas = new Span;
$obSpanContas->setId( "spnContas" );

$obHdnVlSaldoContaAnalitica = new Hidden;
$obHdnVlSaldoContaAnalitica->setName (   "nuSaldoContaAnalitica" );
$obHdnVlSaldoContaAnalitica->setId   (   "nuSaldoContaAnalitica" );
$obHdnVlSaldoContaAnalitica->setValue(   $nuSaldoContaAnalitica  );

$obHdnVlSaldoContaAnaliticaBR = new Hidden;
$obHdnVlSaldoContaAnaliticaBR->setName ( "nuSaldoContaAnaliticaBR" );
$obHdnVlSaldoContaAnaliticaBR->setId   ( "nuSaldoContaAnaliticaBR" );
$obHdnVlSaldoContaAnaliticaBR->setValue( $nuSaldoContaAnaliticaBR  );

// Define Objeto para busca do histórico
$obBscHistorico = new BuscaInner();
$obBscHistorico->setRotulo                 ( "Histórico Padrão"           );
$obBscHistorico->setTitle                  ( "Informe o histórico padrão.");
$obBscHistorico->setId                     ( "stNomHistorico"             );
$obBscHistorico->setValue                  ( $stNomHistorico              );
$obBscHistorico->setNull                   ( false                        );
$obBscHistorico->obCampoCod->setName       ( "inCodHistorico"             );
$obBscHistorico->obCampoCod->setSize       ( 10                           );
$obBscHistorico->obCampoCod->setMaxLength  ( 5                            );
$obBscHistorico->obCampoCod->setValue      ( $inCodHistorico              );
$obBscHistorico->obCampoCod->setAlign      ( "left"                       );
$obBscHistorico->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistorico','stNomHistorico','','".Sessao::getId()."','800','550');");
$obBscHistorico->setValoresBusca           ( CAM_GF_CONT_POPUPS.'historicoPadrao/OCHistoricoPadrao.php?'.Sessao::getId(), $obForm->getName() );

// Define objeto para o valor da aplicaçao
$obTxtValor = new Numerico;
$obTxtValor->setName     ( "nuValor"   );
$obTxtValor->setId       ( "nuValor"   );
$obTxtValor->setValue    ( $nuValor    );
$obTxtValor->setRotulo   ( "Valor"     );
$obTxtValor->setTitle    ( "Informe o Valor do Resgate." );
$obTxtValor->setDecimais ( 2                );
$obTxtValor->setNegativo ( false            );
$obTxtValor->setNull     ( false             );
$obTxtValor->setSize     ( 23               );
$obTxtValor->setMaxLength( 23               );
$obTxtValor->setMinValue ( 1                );

//Busca cod_uf para verificar se é o estado de Tocantins 27
$inCodUf = SistemaLegado::pegaConfiguracao("cod_uf", 2, Sessao::getExercicio(), $boTransacao);    
//Disponibilizar na tela deDepósitos/Retiradas na Tesouraria o campo Tipo de Transferência para atender exigências do Tribunal de Tocantins.
    if ( $inCodUf == 27 ) {
        include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOTipoTransferencia.class.php";
       $obTTCETOTipoTransferencia = new TTCETOTipoTransferencia();
        $obTTCETOTipoTransferencia->recuperaTodos($rsTipoTransferencia,"","",$boTransacao);
        // Define o objeto para o tipo de pagamento

        $obTipoTransferencia = new Select;
        $obTipoTransferencia->setRotulo         ( "Tipo de Trânsferência"       );
        $obTipoTransferencia->setName           ( "inCodTipoTransferenciaTO"    );
        $obTipoTransferencia->setCampoId        ( 'cod_tipo'                    );
        $obTipoTransferencia->setCampoDesc      ( '[cod_tipo] - [descricao]'    );
        $obTipoTransferencia->addOption         ( "", "Selecione"               );
        $obTipoTransferencia->setNull           ( false                         );
        $obTipoTransferencia->setStyle          ( "width: 220px"                );        
        $obTipoTransferencia->preencheCombo     ($rsTipoTransferencia           );
        
        $obTxtExercicioEmpenho = new Exercicio;
        $obTxtExercicioEmpenho->setRotulo       ( 'Exercício do Empenho'        );
        $obTxtExercicioEmpenho->setName         ( 'stExercicioEmpenho'          );
        $obTxtExercicioEmpenho->setid           ( 'stExercicioEmpenho'          );
        $obTxtExercicioEmpenho->setNull         ( true                          );
        $obTxtExercicioEmpenho->setValue        ( null                          );
        $obTxtExercicioEmpenho->obEvento->setOnChange( " montaParametrosGET('liberaCampoEmpenho'); " );
        
        // Define objeto BuscaInner para descrição e codigo do empenho
        $obBscEmpenho = new BuscaInner;
        $obBscEmpenho->setTitle                 ( "Informe o número do empenho.");
        $obBscEmpenho->setRotulo                ( "Número do Empenho"           );
        $obBscEmpenho->setId                    ( "stDescEmpenho"               );
        if (isset($stDescEmpenho)) {
            $obBscEmpenho->setValue             ( $stDescEmpenho                );
        }
        $obBscEmpenho->setNull                  ( true                          );
        $obBscEmpenho->obCampoCod->setName      ( "inCodigoEmpenho"             );
        $obBscEmpenho->obCampoCod->setId        ( "inCodigoEmpenho"             );
        $obBscEmpenho->obCampoCod->setValue     ( $request->get('inCodEmpenho') );
        $obBscEmpenho->obCampoCod->setSize      ( 8                             );
        $obBscEmpenho->obCampoCod->setMaxLength ( 8                             );
        $obBscEmpenho->obCampoCod->setInteiro   ( true                          );
        $obBscEmpenho->obCampoCod->setNull      ( true                          );
        $obBscEmpenho->obImagem->setId          ( "stLinkBusca" );
        $obBscEmpenho->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLEmpenho.php','frm','inCodigoEmpenho','stDescEmpenho','buscaTodosEmpenhos&inCodEntidade=
                                            '+document.frm.inCodEntidade.value+'&stCampoExercicio=stExercicioEmpenho&stExercicioEmpenho='+document.frm.stExercicioEmpenho.value,
                                            '".Sessao::getId()."','800','550');");
        $obBscEmpenho->obCampoCod->obEvento->setOnChange(" montaParametrosGET('buscaEmpenho'); ");

        $jsOnload .= "  jQuery('#inCodigoEmpenho').prop('disabled','true'); ";
        $jsOnload .= "  jQuery('#stExercicioEmpenho').prop('disabled','true'); ";
        $jsOnload .= "  jQuery('#stLinkBusca').hide(); ";
    }

// Define Objeto TextArea para observações
$obTxtObs = new TextArea;
$obTxtObs->setName   ( "stObservacoes" );
$obTxtObs->setId     ( "stObservacoes" );
$obTxtObs->setValue  ( $stObservacoes  );
$obTxtObs->setRotulo ( "Observações"   );
$obTxtObs->setTitle  ( "Informe as observações do Resgate." );
$obTxtObs->setNull   ( true            );
$obTxtObs->setRows   ( 2               );
$obTxtObs->setCols   ( 100             );
$obTxtObs->setMaxCaracteres    ( 170 );

// define objeto com o tipo de transferencia
$obHdnTipoTransf = new Hidden;
$obHdnTipoTransf->setName ( 'inTipoTransferencia' );
$obHdnTipoTransf->setValue ( 4 );

$obHdnPrograma = new Hidden;
$obHdnPrograma->setName ('stPrograma');
$obHdnPrograma->setValue ( $stPrograma );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obIAppletTerminal = new IAppletTerminal( $obForm );

$obFormulario->addTitulo    ( "Dados para Resgates"               );
$obFormulario->addHidden    ( $obHdnAcao                          );
$obFormulario->addHidden    ( $obHdnCtrl                          );
$obFormulario->addHidden    ( $obIAppletTerminal                  );
$obFormulario->addHidden    ( $obHdnCodBoletim                    );
$obFormulario->addComponente( $obIEntidade                        );
$obFormulario->addSpan      ( $obSpanBoletim                      );
$obFormulario->addHidden    ( $obHdnDtBoletimAberto               );
$obFormulario->addSpan      ( $obSpanContas                       );
$obFormulario->addComponente( $obBscHistorico                     );
$obFormulario->addComponente( $obTxtValor                         );
if( $inCodUf == 27 ) {
    $obFormulario->addComponente  ( $obTipoTransferencia               );
    $obFormulario->addComponente  ( $obTxtExercicioEmpenho           );
    $obFormulario->addComponente  ( $obBscEmpenho                    );
}
$obFormulario->addHidden    ( $obHdnVlSaldoContaAnalitica         );
$obFormulario->addHidden    ( $obHdnVlSaldoContaAnaliticaBR       );
$obFormulario->addHidden    ( $obHdnEval, true                    );
$obFormulario->addComponente( $obTxtObs                           );
$obFormulario->addHidden    ( $obHdnTipoTransf                    );
$obFormulario->addHidden    ( $obHdnPrograma                      );

Sessao::write('obIEntidade', $obIEntidade);

$obOk  = new Ok;
$obOk->setId ("Ok");
$obOk->obEvento->setOnClick("Salvar(); document.frm.Ok.disabled = false;");

$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "frm.reset(); frm.inCodEntidade.focus(); document.frm.Ok.disabled = false;" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
