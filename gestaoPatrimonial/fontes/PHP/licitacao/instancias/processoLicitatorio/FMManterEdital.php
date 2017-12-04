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
    * Pagina de formulário para Incluir Edital
    * Data de Criação   : 19/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: FMManterEdital.php 64262 2015-12-23 12:48:09Z jean $

    * Casos de uso: uc-03.05.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacao.class.php" );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( TLIC."TLicitacaoEdital.class.php" );
include_once ( TLIC."TLicitacaoLicitacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEdital";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;

$obTLicitacaoEdital = new TLicitacaoEdital();

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form();
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

if ($_REQUEST['inNumEdital'] && $_REQUEST['stExercicio']) {
    $exercicioLicitacao = $_REQUEST['stExercicio'];
    $obTLicitacaoEdital->setDado( 'exercicio' , $exercicioLicitacao );
    $obTLicitacaoEdital->setDado( 'num_edital', $_REQUEST['inNumEdital'] );
    $obTLicitacaoEdital->recuperaEdital( $rsEdital );

    $obHdnNumEdital = new Hidden();
    $obHdnNumEdital->setName( 'inNumEdital' );
    $obHdnNumEdital->setValue( $rsEdital->getCampo( 'num_edital' ) );

    $obHdnProcesso = new Hidden();
    $obHdnProcesso->setName( 'inNumProcesso' );
    $obHdnProcesso->setValue( $rsEdital->getCampo('cod_processo').'/'.$rsEdital->getCampo('exercicio_processo') );

    $inNumEdital           = $rsEdital->getCampo( 'num_edital' );
    $stExercicioLicitacao  = $rsEdital->getCampo( 'exercicio' );
    $inCodEntidade         = $rsEdital->getCampo( 'cod_entidade' );
    $stNomEntidade         = $rsEdital->getCampo( 'nom_entidade' );
    $inCodModalidade       = $rsEdital->getCampo( 'cod_modalidade' );
    $stNomModalidade       = $rsEdital->getCampo( 'nom_modalidade' );
    $inCodLicitacao        = $rsEdital->getCampo( 'cod_licitacao'  );
    $inResponsavelJuridico = $rsEdital->getCampo( 'responsavel_juridico' );
    $stHoraAbertura        = $rsEdital->getCampo( 'hora_abertura_propostas' );
    $stLocalMaterial       = $rsEdital->getCampo( 'local_entrega_material' );
    $inCodProcesso         = $rsEdital->getCampo( 'cod_processo' );
    $stExercicioProcesso   = $rsEdital->getCampo( 'exercicio_processo' );
    $dtAprovacao           = $rsEdital->getCampo( 'dt_aprovacao_juridico' );
    $stLocalEntrega        = $rsEdital->getCampo( 'local_entrega_propostas' );
    $dtEntrega             = $rsEdital->getCampo( 'dt_entrega_propostas'   );
    $dtEntregaFinal        = $rsEdital->getCampo( 'dt_final_entrega_propostas'   );
    $stHoraEntrega         = $rsEdital->getCampo( 'hora_entrega_propostas' );
    $stHoraEntregaFinal    = $rsEdital->getCampo( 'hora_final_entrega_propostas' );
    $stLocalAbertura       = $rsEdital->getCampo( 'local_abertura_propostas' );
    $dtAbertura            = $rsEdital->getCampo( 'dt_abertura_propostas' );
    $dtValidade            = $rsEdital->getCampo( 'dt_validade_proposta' );
    $txtValidade           = $rsEdital->getCampo( 'observacao_validade_proposta' );
    $txtCondPagamento      = $rsEdital->getCampo( 'condicoes_pagamento' );
    $stCodDocumento        = $rsEdital->getCampo( 'cod_documento' );
    $stNomCGM              = $rsEdital->getCampo( 'nom_cgm' ) ;

    $obHdnCodEntidade = new Hidden();
    $obHdnCodEntidade->setName('inCodEntidade');
    $obHdnCodEntidade->setValue( $inCodEntidade );

    $obHdnNomEntidade = new Hidden();
    $obHdnNomEntidade->setName('stNomEntidade');
    $obHdnNomEntidade->setValue( $stNomEntidade );

    $obHdnCodModalidade = new Hidden();
    $obHdnCodModalidade->setName(inCodModalidade);
    $obHdnCodModalidade->setValue($inCodModalidade);

    $obHdnCodLicitacao = new Hidden();
    $obHdnCodLicitacao->setName('inCodLicitacao');
    $obHdnCodLicitacao->setValue($inCodLicitacao);

    $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
    $obTLicitacaoLicitacao->setDado('cod_licitacao', $rsEdital->getCampo('cod_licitacao'));
    $obTLicitacaoLicitacao->setDado('exercicio', $rsEdital->getCampo('exercicio'));
    $obTLicitacaoLicitacao->setDado('cod_modalidade', $rsEdital->getCampo('cod_modalidade'));
    $obTLicitacaoLicitacao->setDado('cod_entidade', $rsEdital->getCampo('cod_entidade'));
    $obTLicitacaoLicitacao->recuperaValorLicitacao($rsValorLicitacao);

    $inValorLicitacao = number_format($rsValorLicitacao->getCampo('valor_total'),2,',','.');

    $jsOnload = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumEdital=". $rsEdital->getCampo( 'num_edital' ) ."&stNomCGM=". $rsEdital->getCampo( 'nom_cgm' ) ."&inCodProcesso=". $rsEdital->getCampo( 'cod_processo' ) ."&stExercicioProcesso=". $rsEdital->getCampo( 'exercicio_processo' ) ."&inCodEntidade=". $rsEdital->getCampo( 'cod_entidade' )."&stNomEntidade=". $rsEdital->getCampo( 'nom_entidade' ) ."&inCodModalidade=". $rsEdital->getCampo( 'cod_modalidade' ) ."&inCodLicitacao=". $rsEdital->getCampo( 'cod_licitacao' ) ."','preencheAlteracao');\n";
} else {

    include_once CAM_GP_COM_MAPEAMENTO."TComprasConfiguracao.class.php";

    $obTConfiguracao = new TComprasConfiguracao;
    $obTConfiguracao->setDado( "parametro" , "numeracao_automatica_licitacao" );
    $obTConfiguracao->recuperaPorChave($rsConfiguracao);
    $boIdLicitacaoAutomatica = $rsConfiguracao->getCampo('valor');

    // Caso o parâmetro for false, constroi o campo para o usuário informar o cód. da licitação.
    if ($boIdLicitacaoAutomatica == "f") {
        $obTxtNumEdital = new Inteiro();
        $obTxtNumEdital->setId    ( 'inCodEdital' );
        $obTxtNumEdital->setName  ( 'inCodEdital' );
        $obTxtNumEdital->setRotulo( 'Código do Edital' );
        $obTxtNumEdital->setTitle ( 'Informe o código do Edital.' );
        $obTxtNumEdital->setNull  ( false );
    }
}

$obMontaLicitacao = new IMontaNumeroLicitacao($obForm,true,'',true);
$obMontaLicitacao->setSelecionaAutomaticamenteLicitacao(true);
$obMontaLicitacao->setEntidadeUsuario( true );

if ($stAcao == 'alterar') {
    $obExercicio = new Exercicio();
    $obExercicio->setName( 'stExercicioLicitacao' );

    # Define label Entidade.
    $obLblEntidade = new Label;
    $obLblEntidade->setRotulo('Entidade');
    $obLblEntidade->setValue($inCodEntidade." - ".$stNomEntidade  );

    # Define o Label de Modalidade
    $obLblModalidade = new Label;
    $obLblModalidade->setRotulo('Modalidade');
    $obLblModalidade->setValue($inCodModalidade." - ".$stNomModalidade);

    # Define o Label Licitacao
    $obLblLicitacao = new Label;
    $obLblLicitacao->setRotulo('Número da Licitação');
    $obLblLicitacao->setValue($inCodLicitacao);

    $obProcessoLicitatorio = new Label();
    $obProcessoLicitatorio->setId( 'stProcesso' );
    $obProcessoLicitatorio->setValue( $inCodProcesso."/".$stExercicioProcesso);
    $obProcessoLicitatorio->setRotulo( 'Processo Administrativo' );
}

$obPopUpCGM = new IPopUpCGM($obForm);
$obPopUpCGM->setRotulo( 'CGM do Responsável Jurídico' );
$obPopUpCGM->setTitle ( 'Informe o CGM do responsável jurídico.' );
$obPopUpCGM->obCampoCod->setName( 'inResponsavelJuridico' );
$obPopUpCGM->obCampoCod->setValue( $inResponsavelJuridico );

$obDataAprovacao = new Data;
$obDataAprovacao->setName  ( "dtAprovacao" );
$obDataAprovacao->setRotulo( "Data de Aprovação do Jurídico" );
$obDataAprovacao->setTitle ( "Informe a data de aprovação do departamento jurídico." );
$obDataAprovacao->setValue ( $dtAprovacao  );
$obDataAprovacao->setNull  ( false );

$obLocalEntrega = new TextBox;
$obLocalEntrega->setName( 'stLocalEntrega' );
$obLocalEntrega->setRotulo( 'Local da Entrega das Propostas' );
$obLocalEntrega->setTitle( 'Informe o local onde os participantes devem efetuar a entrega das propostas.' );
$obLocalEntrega->setSize ( 90 );
$obLocalEntrega->setMaxLength( 100 );
$obLocalEntrega->setValue( $stLocalEntrega );
$obLocalEntrega->setNull ( false );

$obDataEntrega = new Data;
$obDataEntrega->setName  ( "dtEntrega" );
$obDataEntrega->setRotulo( "Data da Entrega" );
$obDataEntrega->setTitle ( "Informe a data inicial para entrega das propostas." );
$obDataEntrega->setValue ( $dtEntrega  );
$obDataEntrega->setNull  ( false );

$obHoraEntrega = new Hora;
$obHoraEntrega->setName ( "stHoraEntrega" );
$obHoraEntrega->setRotulo( "Hora da Entrega" );
$obHoraEntrega->setTitle( "Informe a hora inicial para entrega das propostas." );
$obHoraEntrega->setValue( $stHoraEntrega  );
$obHoraEntrega->setNull ( false );

$obDataEntregaFinal = new Data;
$obDataEntregaFinal->setName  ( "dtEntregaFinal" );
$obDataEntregaFinal->setRotulo( "Data Final da Entrega" );
$obDataEntregaFinal->setTitle ( "Informe a data limite para entrega das propostas." );
$obDataEntregaFinal->setValue ( $dtEntregaFinal  );
$obDataEntregaFinal->setNull  ( true );

$obHoraEntregaFinal = new Hora;
$obHoraEntregaFinal->setName ( "stHoraEntregaFinal" );
$obHoraEntregaFinal->setRotulo( "Hora Final da Entrega" );
$obHoraEntregaFinal->setTitle( "Informe a hora limite para entrega das propostas." );
$obHoraEntregaFinal->setValue( $stHoraEntregaFinal  );
$obHoraEntregaFinal->setNull ( true );

$obLocalAbertura = new TextBox;
$obLocalAbertura->setName( 'stLocalAbertura' );
$obLocalAbertura->setRotulo( 'Local de Abertura' );
$obLocalAbertura->setTitle( 'Informe o local onde ocorrerá a abertura das propostas.' );
$obLocalAbertura->setSize ( 90 );
$obLocalAbertura->setMaxLength( 100 );
$obLocalAbertura->setValue( $stLocalAbertura );
$obLocalAbertura->setNull ( false );

$obDataAbertura = new Data;
$obDataAbertura->setName  ( "dtAbertura" );
$obDataAbertura->setRotulo( "Data da Abertura" );
$obDataAbertura->setTitle ( "Informe a data da abertura das propostas." );
$obDataAbertura->setValue ( $dtAbertura  );
$obDataAbertura->setNull ( false );

$obHoraAbertura = new Hora;
$obHoraAbertura->setName ( "stHoraAbertura" );
$obHoraAbertura->setRotulo( "Hora da Abertura" );
$obHoraAbertura->setTitle( "Informe a hora da abertura das propostas." );
$obHoraAbertura->setValue( $stHoraAbertura );
$obHoraAbertura->setNull ( false );

$obLocalMaterial = new TextBox;
$obLocalMaterial->setName( 'stLocalMaterial' );
$obLocalMaterial->setRotulo( 'Local de Entrega do Material' );
$obLocalMaterial->setTitle( 'Informe o local da entrega do material.' );
$obLocalMaterial->setSize ( 90 );
$obLocalMaterial->setMaxLength( 100 );
$obLocalMaterial->setValue( $stLocalMaterial );
$obLocalMaterial->setNull ( false );

$obDtValidade = new Data();
$obDtValidade->setName('dtValidade');
$obDtValidade->setRotulo( 'Validade das Propostas' );
$obDtValidade->setTitle( 'Informe a Validade das Propostas.' );

$obDtValidade->setValue( $dtValidade );
$obDtValidade->setNull( false );

$obTxtValidade = new TextArea;
$obTxtValidade->setName( 'txtValidade' );
$obTxtValidade->setRotulo( 'Observação da Validade' );
$obTxtValidade->setTitle( 'Informe Observação da validade das propostas.' );
$obTxtValidade->setValue( $txtValidade );
$obTxtValidade->setCols( 30 );
$obTxtValidade->setRows( 5  );
$obTxtValidade->setNull ( true );

$obTxtCondPagamento = new TextBox;
$obTxtCondPagamento->setName( 'txtCodPagamento' );
$obTxtCondPagamento->setRotulo( 'Condições de Pagamento' );
$obTxtCondPagamento->setTitle( 'Informe as condições de pagamento.' );
$obTxtCondPagamento->setValue( $txtCondPagamento );
$obTxtCondPagamento->setMaxLength( 80 );
$obTxtCondPagamento->setSize( 90 );
$obTxtCondPagamento->setNull ( false );

$obLblValorLicitacao = new Label;
$obLblValorLicitacao->setRotulo('Valor da Licitação');
$obLblValorLicitacao->setId('valorLicitacao');
$obLblValorLicitacao->setValue($inValorLicitacao);

$obRadioDocumento = new SimNao();
$obRadioDocumento->setRotulo( 'Gerar Documento' );
$obRadioDocumento->setTitle ( 'Gerar documento para o edital.' );
$obRadioDocumento->setName  ( 'boGerarDocumento');

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addTitulo( 'Dados do Edital' );
if ($boIdLicitacaoAutomatica == "f") {
    $obFormulario->addComponente( $obTxtNumEdital );
}

if ($stAcao == 'alterar') {
    $obFormulario->addComponente    ( $obExercicio              );
    $obFormulario->addComponente    ( $obLblEntidade            );
    $obFormulario->addComponente    ( $obLblModalidade          );
    $obFormulario->addComponente    ( $obLblLicitacao           );
    $obFormulario->addComponente    ( $obProcessoLicitatorio    );
    $obFormulario->addHidden        ( $obHdnNumEdital           );
    $obFormulario->addHidden        ( $obHdnProcesso            );
    $obFormulario->addHidden        ( $obHdnCodEntidade         );
    $obFormulario->addHidden        ( $obHdnNomEntidade         );
    $obFormulario->addHidden        ( $obHdnCodModalidade       );
    $obFormulario->addHidden        ( $obHdnCodLicitacao        );

} else {
    $obMontaLicitacao->geraFormulario( $obFormulario );
}

$obFormulario->addComponente( $obLblValorLicitacao );

$obFormulario->addTitulo( 'Aprovação Jurídica' );
$obFormulario->addComponente( $obPopUpCGM );
$obFormulario->addComponente( $obDataAprovacao );
$obFormulario->addTitulo( 'Sobre as Propostas' );
$obFormulario->addComponente( $obLocalEntrega );
$obFormulario->addComponente( $obDataEntrega );
$obFormulario->addComponente( $obDataEntregaFinal );
$obFormulario->addComponente( $obHoraEntrega );
$obFormulario->addComponente( $obHoraEntregaFinal );
$obFormulario->addComponente( $obLocalAbertura );
$obFormulario->addComponente( $obDataAbertura );
$obFormulario->addComponente( $obHoraAbertura );
$obFormulario->addComponente( $obDtValidade );
$obFormulario->addComponente( $obTxtValidade );
$obFormulario->addTitulo( 'Outras Informações' );
$obFormulario->addComponente( $obLocalMaterial );
$obFormulario->addComponente( $obTxtCondPagamento );
$obFormulario->addComponente( $obRadioDocumento );
if ($stAcao != 'alterar') {
    $obFormulario->Ok();
} else {
    $obFormulario->Cancelar( $pgList."?".Sessao::getId()."&stAcao=".$stAcao.$stFiltro );
}
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
