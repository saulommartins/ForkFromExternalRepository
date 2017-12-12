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
 * Página de Relatório RGF Anexo1
 * Data de Criação   : 08/10/2007

 * @author Tonismar Régis Bernardo

 * @ignore

 * $Id: OCGeraRGFAnexo1.php 57656 2014-03-25 18:47:28Z eduardoschitz $

 * Casos de uso : uc-06.01.20
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioAnexo4.class.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CLA_MPDF;

$obRelatorioAnexo4 = new TTCEMGRelatorioAnexo4;
$obErro = new Erro();

$stAno = Sessao::getExercicio();
$inPeriodo = $request->get('cmbPeriodo');

if (!$request->get('cmbBimestre') && !$request->get('cmbQuadrimestre') && !$request->get('cmbSemestre')) {
    $obErro->setDescricao('É preciso selecionar ao menos um '.$request->get('stTipoRelatorio').'.');
}

// verifica se a entidade configurada como Consorcio foi selecionada sozinha ou se há outra entidade junto.
//$stEntidade = SistemaLegado::pegaDado("valor","administracao.configuracao","WHERE parametro = 'cod_entidade_consorcio' AND exercicio = '".Sessao::getExercicio()."'");
// TALVEZ PRECISE USAR PARA OS RESTOS


if ($request->get('stPeriodicidade') == 'Bimestre'){
    $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*2),2,'0',STR_PAD_LEFT),$stAno);
    $data_ini = "01/".str_pad((string)(($inPeriodo*2)-1),2,'0',STR_PAD_LEFT)."/".$stAno;
} elseif ($request->get('stPeriodicidade') == 'Trimestre') {
    $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*3),2,'0',STR_PAD_LEFT),$stAno);
    $data_ini = "01/".str_pad((string)(($inPeriodo*3)-2),2,'0',STR_PAD_LEFT)."/".$stAno;
} else {
    $data_fim = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*6),2,'0',STR_PAD_LEFT),$stAno);
    $data_ini = "01/".str_pad((string)(($inPeriodo*6)-5),2,'0',STR_PAD_LEFT)."/".$stAno;
}

if( Sessao::getExercicio() == '2014' ) {
    $arDozeUltimoMes = array();
    $inAno = (int)substr($data_fim, -4 );
    $inMes = (int)substr($data_fim, 4, 2 ) ; 

    $inExercicio = $inAno;
    
    $i = 1;
    while ($i <= 12) {
        if ( $inAno < $inExercicio ) {
            $stExercicioAnterior = $inAno;
            $arDozeUltimoMes[$i] = $inMes;
        }
        
        $i = $i +1;
        $inMes = $inMes -1;
        if ( $inMes == 0 ) {
            $inAno = $inAno -1;
            $inMes = 12;
        }
    }
    
    $obRelatorioAnexo4->setDado('exercicio_anterior',$stExercicioAnterior);
    $obRelatorioAnexo4->setDado('meses_exercicio_anterior', implode(',',$arDozeUltimoMes));
}

if (($request->get("boRestos") == 't') && (Sessao::getExercicio() <= 2014)){
    $data_fim_resto = $data_fim;
    $data_ini_resto = SistemaLegado::somaOuSubtraiData($data_fim_resto,false,12,'month');
    
    $obRelatorioAnexo4->setDado('stDataIniResto',$data_ini_resto);
    $obRelatorioAnexo4->setDado('stDataFimResto',$data_fim_resto);
}
$obRelatorioAnexo4->setDado('stTipoRelatorio',$request->get("stTipoRelatorio"));
$obRelatorioAnexo4->setDado('stDataInicial', $data_ini);
$obRelatorioAnexo4->setDado('stDataFinal', $data_fim);
$obRelatorioAnexo4->setDado('exercicio', substr($data_fim,6));
$obRelatorioAnexo4->setDado('stRestos', $request->get("boRestos"));


$obRelatorioAnexo4->recuperaReceita($rsReceita);
$obRelatorioAnexo4->recuperaDespesa($rsDespesa);
$obRelatorioAnexo4->recuperaDespesaComPessoal($rsDespesaPessoal);
$obRelatorioAnexo4->recuperaReceitaLiquida($rsReceitaLiquida);


$flTotalDespesaPref = 0.00;
$flTotalDespesaCam = 0.00;
$flTotalDespesaAdm = 0.00;
$flTotalDespesaTotais = 0.00;
$flTotalDespesaTodos = 0.00;
$flTotalReceita = 0.00;
$flTotalDespesaNoAno = 0.00;
$flTotalReceitaNoAno = 0.00;
//------------------------
// Temporário para a consulta das despesas
$rsTemp_pref = new RecordSet;
$rsTemp_pref->setPrimeiroElemento();

$rsTemp_cam = new RecordSet;
$rsTemp_cam->setPrimeiroElemento();

$rsTemp_adm = new RecordSet;
$rsTemp_adm->setPrimeiroElemento();

$rsTemp_ttl = new RecordSet;
$rsTemp_ttl->setPrimeiroElemento();

$arTemp = array();
//------------------------------- 

foreach ($rsReceita->getElementos() AS $arReceita) {
    if($arReceita['receita_ate_per'] <= 0.00 ) {
        $flTotalReceitaNoAno = $flTotalReceitaNoAno + $arReceita['receita_ate_per'];
    } else {
        $flTotalReceitaNoAno = $flTotalReceitaNoAno - $arReceita['receita_ate_per'];
    }
    if( $arReceita['cod_estrutural'] == '01') {
        $flTotalReceitaNoAno = $arReceita['receita_ate_per'];
    }
}

foreach ($rsDespesa->getElementos() as $i => $value){
    $arTemp = $value;
    switch ($request->get('stTipoRelatorio')){
        case 1:
            if ($value['tipo'] == 1){
                $flTotalDespesaPref = $flTotalDespesaPref + $value['empenhado'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else if ($value['tipo'] == 2){
                $flTotalDespesaCam = $flTotalDespesaCam + $value['empenhado'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else if ($value['tipo'] == 3){
                $flTotalDespesaAdm = $flTotalDespesaAdm + $value['empenhado'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else {
                $flTotalDespesaTotais = $flTotalDespesaTotais + $value['empenhado'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno - $value['empenhado_ate_periodo'];
            }
            $arTemp['valor_def'] = $value['empenhado'];
        break;
        
        case 2:
            if ($value['tipo'] == 1){
                $flTotalDespesaPref = $flTotalDespesaPref + $value['liquidado'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else if ($value['tipo'] == 2){
                $flTotalDespesaCam = $flTotalDespesaCam + $value['liquidado'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else if ($value['tipo'] == 3){
                $flTotalDespesaAdm = $flTotalDespesaAdm + $value['liquidado'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else {
                $flTotalDespesaTotais = $flTotalDespesaTotais + $value['liquidado'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno - $value['empenhado_ate_periodo'];
            }
            $arTemp['valor_def'] = $value['liquidado'];
        break;
        
        case 3:
            if ($value['tipo'] == 1){
                $flTotalDespesaPref = $flTotalDespesaPref + $value['pago'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else if ($value['tipo'] == 2){
                $flTotalDespesaCam = $flTotalDespesaCam + $value['pago'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else if ($value['tipo'] == 3){
                $flTotalDespesaAdm = $flTotalDespesaAdm + $value['pago'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno + $value['empenhado_ate_periodo'];
            } else {
                $flTotalDespesaTotais = $flTotalDespesaTotais + $value['pago'];
                $flTotalDespesaNoAno = $flTotalDespesaNoAno - $value['empenhado_ate_periodo'];
            }
            $arTemp['valor_def'] = $value['pago'];
        break;
    }
    
    switch($value['tipo']){
        case 1:
            $rsTemp_pref->add($arTemp);
        break;
        case 2:
            $rsTemp_cam->add($arTemp);
        break;
        case 3:
            $rsTemp_adm->add($arTemp);
        break;
        case 4:
            $rsTemp_ttl->add($arTemp);
        break;
    }
}

foreach ($rsReceita->getElementos() as $i => $value){
    $flTotalReceita = $flTotalReceita + $value['total'];
}

$arRestos = array();

$arCamara['descricao'] = 'restos';
$arCamara['nivel'] = 0;
$arCamara['valor'] = 0.00;
$arCamara['entidade'] = 1;

$arPrefeitura['descricao'] = 'restos';
$arPrefeitura['nivel'] = 0;
$arPrefeitura['valor'] = 0.00;
$arPrefeitura['entidade'] = 2;

$arAdm['descricao'] = 'restos';
$arAdm['nivel'] = 0;
$arAdm['valor'] = 0.00;
$arAdm['entidade'] = 3;

//---------------------
// Preparando array com todos os dados para o relatório

$arDados['data_inicial'] = $request->get('stDataInicial');
$arDados['data_final'] = $request->get('stDataFinal');
$arDados['tipo_despesa'] = $request->get('stTipoRelatorio');

$arDados['despesa_pref'] = $rsTemp_pref;
$arDados['despesa_cam'] = $rsTemp_cam;
$arDados['despesa_adm'] = $rsTemp_adm;
$arDados['despesa_ttl'] = $rsTemp_ttl;

$arDados['total_despesa_pessoal'] = $rsDespesaPessoal->getCampo('valor');
$arDados['total_despesa_pref'] = $flTotalDespesaPref;
$arDados['total_despesa_cam'] = $flTotalDespesaCam;
$arDados['total_despesa_adm'] = $flTotalDespesaAdm;
$arDados['total_despesa_ttl'] = $flTotalDespesaTotais;

$arDados['receitas'] = $rsReceita;
$arDados['total_receita'] = $flTotalReceita;
$arDados['total_receita_liquida'] = $rsReceitaLiquida->getCampo('valor')+$flTotalReceitaNoAno;

//-----------------------
// FORMANTANDO OS VALORES PARA MOEDA

$arDados['aplicacao'] = $arDados['aplicacao'];
$arDados['percent_despesa'] = $arDados['percent_despesa'];
$arDados['lei_complementar'] = $arDados['lei_complementar'];

$arDados['excedente'] = $arDados['excedente'];
$arDados['percent_excedente'] = $arDados['percent_excedente'];

//-------------------------------
// Preparando a chamada para o layout do relatório

$obMPDF = new FrameWorkMPDF(6,55,5);
$obMPDF->setDataInicio($request->get("stDataInicial"));
$obMPDF->setDataFinal($request->get("stDataFinal"));
$obMPDF->setNomeRelatorio("Anexo IV: Demonstrativo dos Gastos com Pessoal");

$obMPDF->setConteudo($arDados);
//die();
$obMPDF->gerarRelatorio();