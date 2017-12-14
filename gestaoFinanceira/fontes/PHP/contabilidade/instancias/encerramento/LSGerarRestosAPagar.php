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
    * Página de Listagem de Saldo Financeiro + Restos a pagar - empenhos
    * Data de Criação   : 18/11/2008

    * @ignore

    * $Id: $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
include_once CAM_GF_CONT_MAPEAMENTO . 'FContabilidadeRelatorioInsuficiencia.class.php';
include_once CAM_GF_CONT_MAPEAMENTO . 'FContabilidadeRelatorioInsuficienciaDestinacaoRecurso.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "GerarRestosAPagar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgRel  = "OCGeraRelatorioInsuficiencia.php";
$pgJs   = 'JS'.$stPrograma.".js";

include_once($pgJs);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ( Sessao::read('exercicio') >= '2013') {
    $obHdnEntidade = new Hidden;
    $obHdnEntidade->setName ( "inCodEntidade" );
    $obHdnEntidade->setValue( $_REQUEST['inCodEntidadeCredito'] );
} else {
    $rsEntidades = new RecordSet;

    $obTOrcamentoEntidade = New TOrcamentoEntidade();
    $obTOrcamentoEntidade->setDado('exercicio', Sessao::read('exercicio'));
    $obTOrcamentoEntidade->recuperaEntidades($rsEntidades);

    while ( !$rsEntidades->eof() ) {
        $stEntidades .= $rsEntidades->getCampo('cod_entidade').',';
        $rsEntidades->proximo();
    }

    $stEntidades = substr($stEntidades, 0 , strlen($stEntidades)-1);
}

SistemaLegado::BloqueiaFrames(true,true);
flush();

$rsRestos = new RecordSet();

$boDestinacaoRecurso = sistemaLegado::pegaConfiguracao('recurso_destinacao',8,Sessao::getExercicio());

if ($boDestinacaoRecurso == 'true') {
    $obFContabilidadeRelatorioInsuficienciaDestinacaoRecurso = new FContabilidadeRelatorioInsuficienciaDestinacaoRecurso();
    $obFContabilidadeRelatorioInsuficienciaDestinacaoRecurso->setDado('exercicio'   , Sessao::read('exercicio') );
    $obFContabilidadeRelatorioInsuficienciaDestinacaoRecurso->setDado('cod_entidade', $_REQUEST['inCodEntidadeCredito']);
    $obFContabilidadeRelatorioInsuficienciaDestinacaoRecurso->setDado('dt_final', '31/12/' . Sessao::read('exercicio'));
    $obFContabilidadeRelatorioInsuficienciaDestinacaoRecurso->recuperaTodos($rsRestos);
} else {
    $obFContabilidadeRelatorioInsuficiencia = new FContabilidadeRelatorioInsuficiencia();
    $obFContabilidadeRelatorioInsuficiencia->setDado('exercicio'   , Sessao::read('exercicio') );
    $obFContabilidadeRelatorioInsuficiencia->setDado('cod_entidade', $_REQUEST['inCodEntidadeCredito']);
    $obFContabilidadeRelatorioInsuficiencia->setDado('dt_final', '31/12/' . Sessao::read('exercicio'));
    $obFContabilidadeRelatorioInsuficiencia->recuperaTodos($rsRestos);
}

$rsRestos->addFormatacao('saldo','NUMERIC_BR');
$rsRestos->addFormatacao('restos_processados','NUMERIC_BR');
$rsRestos->addFormatacao('restos_nao_processados','NUMERIC_BR');
$rsRestos->addFormatacao('liquidado_a_pagar','NUMERIC_BR');
$rsRestos->addFormatacao('a_liquidar','NUMERIC_BR');
$rsRestos->addFormatacao('saldo_inscrito','NUMERIC_BR');

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTotalizaMultiplo(array('saldo,&nbsp; ,r,3',
                                    'restos_processados,&nbsp; ,r,4',
                                    'restos_nao_processados,&nbsp; ,r,5',
                                    'a_liquidar,&nbsp; ,r,6',
                                    'liquidado_a_pagar,&nbsp; ,r,7',
                                    'saldo_inscrito,&nbsp; ,r,8'));
$obLista->setRecordSet( $rsRestos );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if ($boDestinacaoRecurso == 'true') {
    $obLista->ultimoCabecalho->addConteudo("Destinação Recurso");
} else {
    $obLista->ultimoCabecalho->addConteudo("Recurso");
}
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Saldo");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Restos Processados");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Restos Não Processados");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenhos Liquidados a Pagar");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenhos a Liquidar");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Total");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_entidade]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
if ($boDestinacaoRecurso == 'true') {
    $obLista->ultimoDado->setCampo( "[num_recurso] - [tipo]" );
} else {
    $obLista->ultimoDado->setCampo( "[cod_recurso] - [tipo]" );
}
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[saldo]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[restos_processados]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[restos_nao_processados]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[a_liquidar]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[liquidado_a_pagar]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[saldo_inscrito]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

//***************************************/
//Monta FORMULARIO
//***************************************/

$btnRelatorio = new Button;
$btnRelatorio->setValue ( 'Relatório' );
$btnRelatorio->setName  ( 'btnRelatorio');
$btnRelatorio->obEvento->setOnClick("parent.frames['oculto'].location = '".$pgRel."?stEntidades=" . $_REQUEST['inCodEntidadeCredito'] . "';");

//$obHdnEval = new HiddenEval();

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden ( $obHdnCtrl     );
$obFormulario->addHidden ( $obHdnAcao     );

if (Sessao::read('exercicio') >= '2013') {
    $obFormulario->addHidden ( $obHdnEntidade );
}

$obFormulario->AddLista($obLista);
$obFormulario->addComponente( $btnRelatorio);
$obFormulario->Cancelar( $stLocation );
$obFormulario->show();

$jsOnload="LiberaFrames(true,true);";
SistemaLegado::LiberaFrames(true,true);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
