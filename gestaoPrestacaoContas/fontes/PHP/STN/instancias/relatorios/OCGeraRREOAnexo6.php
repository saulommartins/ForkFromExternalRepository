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

    * PÃ¡gina de RelatÃ³rio RREO Anexo3
    * Data de CriaÃ§Ã£o   : 14/11/2007

    * @author Tonismar RÃ©gis Bernardo

    * @ignore

    * Casos de uso : uc-06.01.03

    $Id: OCGeraRREOAnexo3.php 29066 2008-04-08 18:41:08Z bruce $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

if ($request->get('stAcao') == 'anexo5novo') {
    $preview = new PreviewBirt(6,36,42);
} else {
    $preview = new PreviewBirt(6,36,24);
}
$preview->setTitulo('Demonstrativo do Resultado Nominal');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel (true );

$obErro = new Erro();

if (!$_REQUEST['cmbMes'] && !$_REQUEST['cmbBimestre']) {
    $stTipoRelatorio = $_REQUEST['stTipoRelatorio'] == "Mes" ? "Mês" : $_REQUEST['stTipoRelatorio'];
    $obErro->setDescricao('É preciso selecionar ao menos um '.$stTipoRelatorio.'.');
}

/**
 * Faz a inserção/update do parametro meta_resultado_nominal_fixada na table administracao.configuracao
 */
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado('exercicio',Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado('cod_modulo',36);
$obTAdministracaoConfiguracao->setDado('parametro','meta_resultado_nominal_fixada');
$obTAdministracaoConfiguracao->setDado('valor', $request->get('inVlMetaFixada') );

$obTAdministracaoConfiguracao->recuperaPorChave($rsAdministracaoConfiguracao);

if ( $rsAdministracaoConfiguracao->getNumLinhas() > 0 ) {
  $obTAdministracaoConfiguracao->alteracao();
} else {
  $obTAdministracaoConfiguracao->inclusao();
}

// Faz as buscas dos dados das entidades selecionadas
$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$request->get('inCodEntidade') ).")" );

// Adiciona o parametro do nome da entidade
if ( count($request->get('inCodEntidade')) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    $inCodEntidadePrefeitura = SistemaLegado::pegaDado('valor','administracao.configuracao'," WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'cod_entidade_prefeitura' ");

    $obTOrcamentoEntidade->recuperaRelacionamento( $rsEntidadePrefeitura, " AND exercicio = '".Sessao::getExercicio()."' AND cod_entidade = ".$inCodEntidadePrefeitura );
    $preview->addParametro( 'nom_entidade', $rsEntidadePrefeitura->getCampo('nom_cgm') );
}

// Faz uma verificação para saber se alguma das entidades é uma entidade RPPS para que possa ser feito a 2° busca do relatório
$inCount=0;
while (!$rsEntidade->eof()) {
    $stValor = SistemaLegado::pegaDado('valor', 'administracao.configuracao', "where exercicio='".Sessao::getExercicio()."' and parametro='cod_entidade_rpps' and valor='".$rsEntidade->getCampo('cod_entidade')."' ");
    if ($stValor != "") {
        $preview->addParametro( 'cod_entidade_rpps', $rsEntidade->getCampo('cod_entidade') );
        unset($_REQUEST['inCodEntidade'][$inCount]);
        break;
    }
    $rsEntidade->proximo();
}

// Faz um implode das entidades que sobraram (caso uma delas seja RPPS, foi retirada desse grupo de entidades para nao entrar na primeira pesquisa do relatorio)
$stCodEntidade = implode(',', $request->get('inCodEntidade') );
$preview->addParametro( 'cod_entidade', $stCodEntidade );

if ( preg_match( "/C[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
} else {
    $preview->addParametro( 'poder' , 'Executivo' );
}

$preview->addParametro( 'tipo_periodo', $request->get('stTipoRelatorio') );
$preview->addParametro( 'exercicio_anterior', Sessao::getExercicio() -1 );
$preview->addParametro( 'inBimestre', $request->get('cmbBimestre') );

if ( $request->get('stTipoRelatorio') == 'Bimestre') {
    switch( $request->get('cmbBimestre') ):
        case 1:
            $preview->addParametro( 'dtInicioBimestre'  , '01/01/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtInicioBimestreAnterior'  , '01/01/'.Sessao::getExercicio() );
            if ( (Sessao::getExercicio() % 4) == 0 ) {
                $preview->addParametro( 'dtFimBimestre', '29/02/'.Sessao::getExercicio() );
                $preview->addParametro( 'dtFimBimestreAnterior', '29/02/'.Sessao::getExercicio() );
            } else {
                $preview->addParametro( 'dtFimBimestre', '28/02/'.Sessao::getExercicio() );
                $preview->addParametro( 'dtFimBimestreAnterior', '28/02/'.Sessao::getExercicio() );
            }
        break;
        case 2:
            $preview->addParametro( 'dtInicioBimestre'  , '01/03/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestre'     , '30/04/'.Sessao::getExercicio() );

            $preview->addParametro( 'dtInicioBimestreAnterior'  , '01/01/'.Sessao::getExercicio() );
            if (( Sessao::getExercicio() % 4) == 0 )
                $preview->addParametro( 'dtFimBimestreAnterior'     , '29/02/'.Sessao::getExercicio() );
            else
               $preview->addParametro( 'dtFimBimestreAnterior'     , '28/02/'.Sessao::getExercicio() );
        break;
        case 3:
            $preview->addParametro( 'dtInicioBimestre' , '01/05/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestre'    , '30/06/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtInicioBimestreAnterior' , '01/03/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestreAnterior'    , '30/04/'.Sessao::getExercicio() );
        break;
        case 4:
            $preview->addParametro( 'dtInicioBimestre' , '01/07/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestre'    , '31/08/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtInicioBimestreAnterior' , '01/05/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestreAnterior'    , '30/06/'.Sessao::getExercicio() );
        break;
        case 5:
            $preview->addParametro( 'dtInicioBimestre', '01/09/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestre'   , '31/10/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtInicioBimestreAnterior' , '01/07/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestreAnterior'    , '31/08/'.Sessao::getExercicio() );
        break;
        case 6:
            $preview->addParametro( 'dtInicioBimestre', '01/11/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestre'   , '31/12/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtInicioBimestreAnterior' , '01/09/'.Sessao::getExercicio() );
            $preview->addParametro( 'dtFimBimestreAnterior'    , '31/10/'.Sessao::getExercicio() );
        break;
    endswitch;
    $preview->addParametro( 'periodo', $request->get('cmbBimestre') );
    $preview->addParametro( 'descricaoPeriodo', $request->get('cmbBimestre')."º Bimestre de ".Sessao::getExercicio() );
        
} elseif ( $request->get('stTipoRelatorio') == 'Quadrimestre' ) {
    
    switch( $request->get('cmbQuadrimestre')):
        case 1:
            $preview->addParametro( 'dt_final_periodo', '30/04/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 4 );
        break;
        case 2:
            $preview->addParametro( 'dt_final_periodo', '31/08/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 8 );
        break;
        case 3:
            $preview->addParametro( 'dt_final_periodo', '31/12/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 12 );
        break;
    endswitch;

    $preview->addParametro( 'periodo', $request->get('cmbQuadrimestre') );
    $preview->addParametro( 'descricaoPeriodo', $request->get('cmbQuadrimestre')."º Quadrimestre de ".Sessao::getExercicio() );
    
} elseif ( $request->get('stTipoRelatorio') == 'Semestre' ) {

    switch( $request->get('cmbSemestre')):
        case 1:
            $preview->addParametro( 'dt_final_periodo', '30/06/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 6 );
        break;
        case 2:
            $preview->addParametro( 'dt_final_periodo', '31/12/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 12 );
        break;
    endswitch;
    
    $preview->addParametro( 'periodo', $request->get('cmbSemestre') );
    $preview->addParametro( 'descricaoPeriodo', $request->get('cmbSemestre')."º Semestre de ".Sessao::getExercicio() );
    
} elseif ( $request->get('stTipoRelatorio') == 'Mes' ) {
    
    $dtInicial = "01/".$_REQUEST["cmbMes"]."/".Sessao::getExercicio();
    
    if($_REQUEST["cmbMes"] == '01'){          
        $dtInicialAnterior = "01/12/".(Sessao::getExercicio()-1);
        $dtFInalAnterior = "31/12/".(Sessao::getExercicio()-1);
    } else {
        $mesAnterior       = (int)($_REQUEST["cmbMes"]) -1;
        $mesAnterior       = $mesAnterior < 10 ? "0".(string)$mesAnterior : (string)$mesAnterior;
        $dtInicialAnterior = "01/".$mesAnterior."/".(Sessao::getExercicio());
        $dtFinalAnterior   = SistemaLegado::retornaUltimoDiaMes($mesAnterior,Sessao::getExercicio());
        
    }
    
    $dtFinal = SistemaLegado::retornaUltimoDiaMes($_REQUEST["cmbMes"],Sessao::getExercicio());

     $preview->addParametro( 'dtInicioMes', $dtInicial );
     $preview->addParametro( 'dtFimMes', $dtFinal );
     $preview->addParametro( 'dtInicioMesAnterior', $dtInicialAnterior );
     $preview->addParametro( 'dtFimMesAnterior', $dtFinalAnterior );
     
     $preview->addParametro( 'mes', $_REQUEST["cmbMes"] );
     $codUF = SistemaLegado::pegaConfiguracao( 'cod_uf', 2, Sessao::getExercicio());
     $preview->addParametro( 'cod_uf', $codUF );
     $preview->addParametro( 'tipo_relatorio', $request->get('stTipoRelatorio')  );
     $preview->addParametro( 'descricaoPeriodo', sistemaLegado::mesExtensoBR(intval($_REQUEST['cmbMes']))." de ".Sessao::getExercicio());
}

#############################Modificações do tce para o novo layout##############################
//adiciona unidade responsável ao relatório
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );
$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario = new TAdministracaoUsuario;
$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);

$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

if ($request->get('stAcao') == 'anexo5novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));

if ( !$obErro->ocorreu() ) {
    $preview->preview();
} else {
    SistemaLegado::alertaAviso("FLModelosRREO.php?'.Sessao::getId().&stAcao=".$_REQUEST['stAcao'], $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
}

?>