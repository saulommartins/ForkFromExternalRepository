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


include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;

include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioBalanceteVerificacao.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioBalanceteReceita.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteDespesa.class.php";

//-----------------------------
// PEGANDO AS ENTIDADES
$stEntidade = implode(',',$request->get('inCodEntidade'));

$stFiltro = " WHERE cod_estrutural like '1.0.0.0.00.00.00.00.00' OR cod_estrutural like '1.1.0.0.00.00.00.00.00' OR cod_estrutural like '1.2.0.0.00.00.00.00.00'
                 OR cod_estrutural like '1.3.0.0.00.00.00.00.00' OR cod_estrutural like '1.4.0.0.00.00.00.00.00' OR cod_estrutural like '1.5.0.0.00.00.00.00.00'
                 OR cod_estrutural like '1.6.0.0.00.00.00.00.00' OR cod_estrutural like '1.7.0.0.00.00.00.00.00' OR cod_estrutural like '1.9.0.0.00.00.00.00.00'
                 OR cod_estrutural like '2.4.0.0.00.00.00.00.00' OR cod_estrutural like '1.7.2.1.34%'            OR cod_estrutural like '1.7.2.1.35%'
                 OR cod_estrutural like '1.7.2.1.33%'            OR cod_estrutural like '1.7.6.0.00%'";

$obROrcamentoBalanceteReceita = new ROrcamentoRelatorioBalanceteReceita;
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("exercicio"              , Sessao::getExercicio()         );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stFiltro"               , ''                             );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stEntidade"             , $stEntidade                    );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stCodEstruturalInicial" , ''                             );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stCodEstruturalFinal"   , ''                             );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stCodReduzidoInicial"   , ''                             );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stCodReduzidoFinal"     , ''                             );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stDataInicial"          , $request->get('stDataInicial') );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stDataFinal"            , $request->get('stDataFinal')   );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("inCodRecurso"           , ''                             );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("stDestinacaoRecurso"    , ''                             );
$obROrcamentoBalanceteReceita->obFBalanceteReceita->setDado("inCodDetalhamento"      , ''                             );
$stOrder = "cod_estrutural";
$obROrcamentoBalanceteReceita->obFBalanceteReceita->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

//--------------------------------------------------------------------
// IV - RETENÇÕES DO PASEP NA FONTE TOTAL DOS VALORES RETIDOS (IV)

$stFiltroDespesa = "AND od.cod_entidade IN (".$stEntidade.") AND cod_estrutural ilike ''%3.3.9.0.47.00%''";
$obFBalanceteDespesa = new FOrcamentoBalanceteDespesa();
$obFBalanceteDespesa->setDado("exercicio"              , Sessao::getExercicio()         );
$obFBalanceteDespesa->setDado("stFiltro"               , $stFiltroDespesa               );
$obFBalanceteDespesa->setDado("stEntidade"             , $stEntidade                    );
$obFBalanceteDespesa->setDado("stCodEstruturalInicial" , ''                             );
$obFBalanceteDespesa->setDado("stCodEstruturalFinal"   , ''                             );
$obFBalanceteDespesa->setDado("stCodReduzidoInicial"   , ''                             );
$obFBalanceteDespesa->setDado("stCodReduzidoFinal"     , ''                             );
$obFBalanceteDespesa->setDado("stDataInicial"          , $request->get('stDataInicial') );
$obFBalanceteDespesa->setDado("stDataFinal"            , $request->get('stDataFinal')   );
$obFBalanceteDespesa->setDado("stControleDetalhado"    , ''                             );
$obFBalanceteDespesa->setDado("inNumOrgao"             , ''                             );
$obFBalanceteDespesa->setDado("inNumUnidade"           , ''                             );
$obErro = $obFBalanceteDespesa->recuperaTodos( $rsDespesas, " WHERE pago_per > 0", " ORDER BY classificacao" );

//----------------------
//Separando cada grupo de receitas para poder colocar em cada grupo do relatório

$arDados = array();

$rsReceitasCorrentes = new RecordSet;
$rsReceitasCorrentes->setPrimeiroElemento();

$rsReceitasCapital = new RecordSet;
$rsReceitasCapital->setPrimeiroElemento();

$rsDeducoes = new RecordSet;
$rsDeducoes->setPrimeiroElemento();

$arTemp = array();

$arDeducoesTransConvenios = array();
$arDeducoesTransConvenios['cod_estrutural'] = '01';
$arDeducoesTransConvenios['descricao'] = 'Transferências de Convênios (§7º, do rt. 2º, da Lei n.º 9.715/98)';
$arDeducoesTransConvenios['arrecadacao_periodo'] = 0.00;

$vlTotalCorrentes = 0.00;
$vlTotalCapital = 0.00;
$vlTotalDeducao = 0.00;

//------------------
// Laço para separar os tipos de receitas e calcular seus totais

foreach ($rsRecordSet->getElementos() as $i => $value){
    if (substr($value['cod_estrutural'],0,4) == '1.1.' || substr($value['cod_estrutural'],0,4) == '1.2.' || substr($value['cod_estrutural'],0,4) == '1.3.' ||
         substr($value['cod_estrutural'],0,4) == '1.4.' || substr($value['cod_estrutural'],0,4) == '1.5.' || substr($value['cod_estrutural'],0,4) == '1.6.' ||
         substr($value['cod_estrutural'],0,8) == '1.7.0.0.' || substr($value['cod_estrutural'],0,4) == '1.9.'){
        //------------------------
        // Receitas Correntes
        $arTemp = $value;
        
        if ($arTemp['arrecadado_periodo'] < 0){
            $arTemp['arrecadado_periodo'] = $value['arrecadado_periodo']*-1;
        }
        
        $arTemp['cod_estrutural'] = str_replace('.','',substr($value['cod_estrutural'],0,7)).''.substr($value['cod_estrutural'],7,9);
        
        $vlTotalCorrentes = $vlTotalCorrentes+$arTemp['arrecadado_periodo'];
        
        $arTemp['arrecadado_periodo'] = number_format($arTemp['arrecadado_periodo'],2,',','.');
        
        $rsReceitasCorrentes->add($arTemp);
        
    } else if (substr($value['cod_estrutural'],0,4) == '2.4.'){
        //------------------------
        // Receitas de Capital
        $arTemp = $value;
        
        if ($arTemp['arrecadado_periodo'] < 0){
            $arTemp['arrecadado_periodo'] = $value['arrecadado_periodo']*-1;
        }
        
        $arTemp['cod_estrutural'] = str_replace('.','',substr($value['cod_estrutural'],0,7)).''.substr($value['cod_estrutural'],7,9);
        
        $vlTotalCapital = $arTemp['arrecadado_periodo'];
        
        $arTemp['arrecadado_periodo'] = number_format($arTemp['arrecadado_periodo'],2,',','.');
        
        $rsReceitasCapital->add($arTemp);
        
    } else if (substr($value['cod_estrutural'],0,11) == '1.7.2.1.34.' || substr($value['cod_estrutural'],0,11) == '1.7.2.1.35.'
               || substr($value['cod_estrutural'],0,11) == '1.7.2.1.33.' || substr($value['cod_estrutural'],0,11) == '1.7.6.0.00.'){
        //-----------------------------------------------------
        // Deduções da Receita -> Transferência de Convênios
        $arTemp = $value;
        
        if ($value['arrecadado_periodo'] < 0){
            $arTemp['arrecadado_periodo'] = $value['arrecadado_periodo']*-1;
        }
        
        $arTemp['cod_estrutural'] = str_replace('.','',substr($value['cod_estrutural'],0,7)).''.substr($value['cod_estrutural'],7,9);
        
        $arDeducoesTransConvenios['arrecadado_periodo'] = $arDeducoesTransConvenios['arrecadado_periodo'] + $arTemp['arrecadado_periodo'];
    }
}

//----------------------------
// Cálculo dos totais das Deduções
$vlTotalDeducao = $arDeducoesTransConvenios['arrecadado_periodo']; //serão somadas as outras deduções quando estiverem disponíveis
$arDeducoesTransConvenios['arrecadado_periodo'] = number_format($arDeducoesTransConvenios['arrecadado_periodo'],2,',','.');

$rsDeducoes->add($arDeducoesTransConvenios);

// Calculo do total de retencoes do PASEP (IV)
foreach ($rsDespesas->getElementos() as $value) {
    $vlTotalRetencoes += $value['pago_per'];
}

//---------------------
// Preparando array com todos os dados para o relatório

$arDados['correntes'] = $rsReceitasCorrentes;
$arDados['capital']   = $rsReceitasCapital;
$arDados['deducoes']  = $rsDeducoes;
$arDados['retencoes'] = $rsDespesas;

$arDados['total_correntes'] = $vlTotalCorrentes;
$arDados['total_capital']   = $vlTotalCapital;
$arDados['total_deducoes']  = $vlTotalDeducao;
$arDados['total_retencoes'] = $vlTotalRetencoes;

$arDados['total_geral'] = ($vlTotalCorrentes + $vlTotalCapital) - $vlTotalDeducao;

$arDados['pasep'] = $arDados['total_geral'] * 0.01;

$arDados['data_inicial'] = $request->get('stDataInicial');
$arDados['data_final'] = $request->get('stDataFinal');

Sessao::write('arDados', $arDados);
Sessao::write('cod_entidade', $request->get('inCodEntidade'));
Sessao::write('data_inicial', $request->get('stDataInicial'));
Sessao::write('data_final'  , $request->get('stDataFinal'));

Sessao::write('retorno', CAM_GF_CONT_INSTANCIAS."relatorio/FLDemonstrativoPASEP.php");

SistemaLegado::LiberaFrames(true,true);

$stCaminho = CAM_GF_CONT_INSTANCIAS."relatorio/OCRelatorioDemostrativoPASEP.php";

SistemaLegado::mudaFramePrincipal($stCaminho);

?>