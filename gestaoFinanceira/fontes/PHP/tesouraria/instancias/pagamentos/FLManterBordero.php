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
    * Filtro para Tesouraria - Bordero
    * Data de Criação   : 28/01/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.9  2006/07/05 20:39:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL                                                                   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                     );
include_once( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterBordero";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

//include_once( $pgJs );

//sessao->link= "";
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

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
    // DEFINE OBJETOS DAS CLASSES

    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRTesourariaBoletim->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRTesourariaBoletim->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

    $rsBanco = new RecordSet;
    $rsAgencia = new RecordSet;
    $obRMONConta = new RMONContaCorrente;
    $obRMONConta->obRMONAgencia->obRMONBanco->listarBanco($rsBanco);

    $obForm = new Form;
    $obForm->setAction ( $pgList    );
    $obForm->setTarget ( "telaPrincipal"   );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName  ( "stAcao" );
    $obHdnAcao->setValue ( $stAcao  );

    $obApplet = new IAppletTerminal( $obForm );

    $obCmbEntidades = new SelectMultiplo();
    $obCmbEntidades->setName   ('inCodEntidade');
    $obCmbEntidades->setRotulo ( "Entidades" );
    $obCmbEntidades->setTitle  ( "Selecione a Entidade" );
    $obCmbEntidades->setNull   ( false );

    // Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
    $rsRecordSet = new RecordSet;
    if ($rsEntidade->getNumLinhas()==1) {
           $rsRecordSet = $rsEntidade;
           $rsEntidade = new RecordSet;
    }

    // lista de atributos disponiveis
    $obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
    $obCmbEntidades->setCampoId1   ( 'cod_entidade' );
    $obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
    $obCmbEntidades->SetRecord1    ( $rsEntidade );

    // lista de atributos selecionados
    $obCmbEntidades->SetNomeLista2 ('inCodEntidade');
    $obCmbEntidades->setCampoId2   ('cod_entidade');
    $obCmbEntidades->setCampoDesc2 ('nom_cgm');
    $obCmbEntidades->SetRecord2    ( $rsRecordSet );

    //Define Objeto Text para o Exercicio
    $obTxtExercicio = new TextBox;
    $obTxtExercicio->setName      ( "stExercicio"         );
    $obTxtExercicio->setValue     ( Sessao::getExercicio()    );
    $obTxtExercicio->setRotulo    ( "Exercício"           );
    $obTxtExercicio->setTitle     ( "Exercício"           );
    $obTxtExercicio->setNull      ( false                 );
    $obTxtExercicio->setMaxLength ( 4                     );
    $obTxtExercicio->setSize      ( 5                     );

    //Define o objeto INNER para armazenar a Conta Banco
    $obBscConta = new BuscaInner;
    $obBscConta->setRotulo( "Conta" );
    $obBscConta->setTitle( "Informe Conta" );
    $obBscConta->setNull( true  );
    $obBscConta->setId( "stConta" );
    $obBscConta->setValue( '' );
    $obBscConta->obCampoCod->setName("inCodConta");
    $obBscConta->obCampoCod->setValue( "" );
    $obBscConta->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stConta','banco','".Sessao::getId()."','800','550');" );
    $obBscConta->setValoresBusca( CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(), $obForm->getName(), 'banco' );

    //Define o objeto TEXT para Codigo do Borderô Inicial
    $obTxtCodigoBorderoInicial = new TextBox;
    $obTxtCodigoBorderoInicial->setName     ( "inCodigoBorderoInicial" );
    $obTxtCodigoBorderoInicial->setValue    ( $inCodigoBorderoInicial  );
    $obTxtCodigoBorderoInicial->setRotulo   ( "Borderô"                );
    $obTxtCodigoBorderoInicial->setInteiro  ( true                     );
    $obTxtCodigoBorderoInicial->setNull     ( true                     );

    //Define objeto Label
    $obLblBordero = new Label;
    $obLblBordero->setValue( "&nbsp;a&nbsp;" );

    //Define o objeto TEXT para Codigo da Ordem de Pagamento final
    $obTxtCodigoBorderoFinal = new TextBox;
    $obTxtCodigoBorderoFinal->setName       ( "inCodigoBorderoFinal" );
    $obTxtCodigoBorderoFinal->setValue      ( $inCodigoBorderoFinal  );
    $obTxtCodigoBorderoFinal->setRotulo     ( ""                     );
    $obTxtCodigoBorderoFinal->setInteiro    ( true                   );
    $obTxtCodigoBorderoFinal->setNull       ( true                   );

    // Define objeto Data para Vencimento
    $obDataVencimento = new Data;
    $obDataVencimento->setName     ( "dtDataVencimento" );
    $obDataVencimento->setRotulo   ( "Vencimento" );
    $obDataVencimento->setTitle    ( '' );
    $obDataVencimento->setNull     ( true );

    $obDataInicial = new Data;
    $obDataInicial->setName     ( "stDtInicial"         );
    $obDataInicial->setRotulo   ( "Data"                );
    $obDataInicial->setTitle    ( ''                    );
    $obDataInicial->setNull     ( true                  );

    //Define objeto Label
    $obLblData = new Label;
    $obLblData->setValue( "&nbsp;a&nbsp;" );

    //Define o objeto TEXT para Codigo do Empenho final
    $obDataFinal = new Data;
    $obDataFinal->setName       ( "stDtFinal"         );
    $obDataFinal->setRotulo     ( ""                  );
    $obDataFinal->setTitle      ( ''                  );
    $obDataFinal->setNull       ( true                );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( "Dados para Filtro"      );
    $obFormulario->addForm       ( $obForm                  );
    $obFormulario->addHidden     ( $obHdnAcao               );
    $obFormulario->addHidden     ( $obApplet                );
    $obFormulario->addComponente ( $obCmbEntidades          );
    $obFormulario->addComponente ( $obTxtExercicio          );
    $obFormulario->addComponente ( $obBscConta              );
    $obFormulario->agrupaComponentes( array( $obTxtCodigoBorderoInicial, $obLblBordero, $obTxtCodigoBorderoFinal ));
    $obFormulario->agrupaComponentes( array( $obDataInicial, $obLblData, $obDataFinal ));

    $obFormulario->Ok();
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
