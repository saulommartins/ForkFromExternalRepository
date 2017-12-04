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
    * Página de Relatório RREO Anexo5
    * Data de Criação   : 10/06/2008

    * @author Analista Alexandre Melo
    * @author Desenvolvedor Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso : uc-06.01.04

    $Id: OCGeraRREOAnexo5.php 28716 2008-03-27 15:28:33Z lbbarreiro $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Bimestre.class.php';
require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

$stAcao = $request->get('stAcao');
if ($stAcao == 'anexo5') {
    $preview = new PreviewBirt(6,36,57);
    $preview->setTitulo('Relatório do Birt');
    $preview->setTitulo('Dem. Receitas  e Despesas  Previdenciárias do RPPS');
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setExportaExcel(true);
} else {
    if (Sessao::getExercicio() >= '2015') {
        $preview = new PreviewBirt(6,36,61);
        $preview->setTitulo('Relatório do Birt');
        $preview->setTitulo('Dem. Receitas  e Despesas  Previdenciárias do RPPS');
        $preview->setVersaoBirt( '2.5.0' );
        $preview->setExportaExcel(true);    
    }else{
        $preview = new PreviewBirt(6,36,28);
        $preview->setTitulo('Relatório do Birt');
        $preview->setTitulo('Dem. Receitas  e Despesas  Previdenciárias do RPPS');
        $preview->setVersaoBirt( '2.5.0' );
        $preview->setExportaExcel(true);    
    }
}

if (is_array($request->get('inCodEntidade'))) {
    $stCodEntidade = implode(',',$request->get('inCodEntidade'));
}

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".$stCodEntidade.")" );
$inCount=0;
while (!$rsEntidade->eof()) {
    $stValor = SistemaLegado::pegaDado('valor', 'administracao.configuracao', "where exercicio='".Sessao::getExercicio()."' and parametro='cod_entidade_rpps' and valor='".$rsEntidade->getCampo('cod_entidade')."' ");
    if ($stValor == "") {
        SistemaLegado::alertaAviso("FLModelosRREO.php?".Sessao::getId()."&stAcao=".$stAcao, $rsEntidade->getCampo('nom_cgm').' não é uma entidade RPPS',"","aviso", Sessao::getId(), "../");
    }
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
    $rsEntidade->proximo();
    $inCount++;
}

$preview->addParametro( 'entidade', $stCodEntidade );

if ($inCount > 1) {
    $preview->addParametro( 'nom_entidade', '' );
}

if ( preg_match( "/C[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'LEGISLATIVO' );
} else {
    $preview->addParametro( 'poder' , 'EXECUTIVO' );
}

$preview->addParametro( 'exercicio', Sessao::getExercicio() );
$preview->addParametro( 'exercicio_anterior', (Sessao::getExercicio() - 1) );

switch ($request->get('stTipoRelatorio')) {
    case 'Bimestre':
        $preview->addParametro( 'bimestre', $request->get('cmbBimestre') );
        $preview->addParametro( 'periodicidade', "bimestre" );
        $preview->addParametro( 'tipo_periodicidade', "bimestre" );
        $preview->addParametro( 'periodo_referencia', $request->get('cmbBimestre')."º BIMESTRE DE ".Sessao::getExercicio() );
        
        $stDataInicial = Bimestre::getDataInicial($request->get('cmbBimestre'), Sessao::getExercicio());
        $stDataFinal = Bimestre::getDataFinal($request->get('cmbBimestre'), Sessao::getExercicio());
        
        $inMesAnterior = (($request->get('cmbBimestre') * 2) -1);
        switch ($inMesAnterior) {
            case 1:  $stMesAnteriorDescricao = 'JANEIRO';   break;
            case 3:  $stMesAnteriorDescricao = 'MARÇO';     break;
            case 5:  $stMesAnteriorDescricao = 'MAIO';      break;
            case 7:  $stMesAnteriorDescricao = 'JULHO';     break;
            case 9:  $stMesAnteriorDescricao = 'SETEMBRO';  break;
            case 11: $stMesAnteriorDescricao = 'NOVEMBRO';  break;
        }
        
    break;

    case 'Mes':
        $preview->addParametro( 'periodicidade', (integer)$request->get('cmbMensal') );
        $preview->addParametro( 'tipo_periodicidade', "mes" );
        $stDataInicial = "01/".$request->get('cmbMes')."/".Sessao::getExercicio();
        $stDataFinal = SistemaLegado::retornaUltimoDiaMes($request->get('cmbMes'), Sessao::getExercicio());
        
        switch ($request->get('cmbMes')) {
            case '01':
                $preview->addParametro( 'periodo_referencia', "JANEIRO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "DEZEMBRO";
            break;
            case '02':
                $preview->addParametro( 'periodo_referencia', "FEVEREIRO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "JANEIRO";
            break;
            case '03':
                $preview->addParametro( 'periodo_referencia', "MARÇO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "FEVEREIRO";
            break;
            case '04':
                $preview->addParametro( 'periodo_referencia', "ABRIL DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "MARÇO";
            break;
            case '05':
                $preview->addParametro( 'periodo_referencia', "MAIO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "ABRIL";
            break;
            case '06':
                $preview->addParametro( 'periodo_referencia', "JUNHO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "MAIO";
            break;
            case '07':
                $preview->addParametro( 'periodo_referencia', "JULHO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "JUNHO";
            break;
            case '08':
                $preview->addParametro( 'periodo_referencia', "AGOSTO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "JULHO";
            break;
            case '09':
                $preview->addParametro( 'periodo_referencia', "SETEMBRO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "AGOSTO";
            break;
            case '10':
                $preview->addParametro( 'periodo_referencia', "OUTUBRO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "SETEMBRO";
            break;
            case '11':
                $preview->addParametro( 'periodo_referencia', "NOVEMBRO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "OUTUBRO";
            break;
            case '12':
                $preview->addParametro( 'periodo_referencia', "DEZEMBRO DE ".Sessao::getExercicio());
                $stMesAnteriorDescricao = "NOVEMBRO";
            break;
        }
        
    break;
}

$preview->addParametro( 'dt_inicial', $stDataInicial );
$preview->addParametro( 'dt_final', $stDataFinal );

$preview->addParametro( 'mes_anterior', $stMesAnteriorDescricao);

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
//necessário codificar os caracteres especias em ascii para o birt interpretar corretamente
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

if ($_REQUEST['stAcao'] == 'anexo4novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
