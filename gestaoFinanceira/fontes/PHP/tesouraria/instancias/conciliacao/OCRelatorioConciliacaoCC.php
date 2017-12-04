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
    * Página de Processamento de Configuração do módulo Tesouraria
    * Data de Criação   : 22/08/2014

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    * $Id: OCRelatorioConciliacaoCC.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaRelatorioConciliacao.class.php";

$obRRelatorio  = new RRelatorio;
$obRegra = new RTesourariaRelatorioConciliacao;
$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroForm = Sessao::read('filtroGeraRel');
$arCodPlano = $arFiltroForm['arCodPlano'];

//Função que verifica se Descrição é array e monta nova Lista array() de acordo 
function array_separa_descricao($arListaEnt = array())
{
    $arListaSaida = array();
    for($i=0;$i<count($arListaEnt);$i++){
        $inCount = count($arListaSaida);
        
        if(!is_array($arListaEnt[$i]['descricao'])){
            $arListaSaida[$inCount] = $arListaEnt[$i];    
        }else{
            $arListaSaida[$inCount]['cod_plano']    = $arListaEnt[$i]['cod_plano'];
            $arListaSaida[$inCount]['ordem']        = $arListaEnt[$i]['ordem'];
            $arListaSaida[$inCount]['movimentacao'] = $arListaEnt[$i]['movimentacao'];
            $arListaSaida[$inCount]['valor']        = '';
            $arListaSaida[$inCount]['descricao']    = $arListaEnt[$i]['descricao'][0];
    
            for($c=1;$c<count($arListaEnt[$i]['descricao']);$c++){
                $inCount++;
                if(($c+1)==count($arListaEnt[$i]['descricao'])){
                    $arListaSaida[$inCount]['valor']        = $arListaEnt[$i]['valor'];
                    $arListaSaida[$inCount]['descricao']    = $arListaEnt[$i]['descricao'][$c];
                }else{
                    $arListaSaida[$inCount]['valor']        = '';
                    $arListaSaida[$inCount]['descricao']    = $arListaEnt[$i]['descricao'][$c];
                }
            }
        }
    }
    
    return $arListaSaida;
}

//Função que ordena uma lista de array por campo 
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    $args = array_separa_descricao(array_pop($args));
    return $args;
}

if ($arFiltro['stExercicio']) {
    $obRegra->obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio($arFiltro['stExercicio']);
} else {
    $obRegra->obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
}

$obRegra->obRTesourariaConciliacao->setMes(intval($arFiltro['inMes']));
$obRegra->obRTesourariaConciliacao->setDataInicial($arFiltroForm['filtro']['stDataInicial']);
$obRegra->obRTesourariaConciliacao->setDataFinal($arFiltroForm['stDtExtrato']);
$obRegra->obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($arFiltro['inCodEntidade']);
$obRegra->setSaldoTesouraria($arFiltro['nuSaldoTesouraria']);

$arCount  = array(2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
$arLista2 = array();
$arLista3 = array();
$arLista4 = array();
$arLista5 = array();

for($i=0;$i<count($arCodPlano);$i++){
    $obRegra->obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano( $arCodPlano[$i]['inCodPlano'] );
    $obRegra->setCC(true);
    
    $obRegra->geraRecordSet($arRecordSet);
    
    for($c=0;$c<count($arRecordSet[2]->arElementos);$c++){
        if($arRecordSet[2]->arElementos[$c]['dt_conciliacao']==''){
            $arLista2[$arCount[2]] = $arRecordSet[2]->arElementos[$c];
            $vlSoma = str_replace('.', '', $arRecordSet[2]->arElementos[$c]['valor']);
            $vlSoma = str_replace(',', '.', $vlSoma);
            $vlTotal = $vlTotal+$vlSoma;
            
            $arCount[2]++;
        }
    }    
    for($c=0;$c<count($arRecordSet[3]->arElementos);$c++){
        if($arRecordSet[3]->arElementos[$c]['dt_conciliacao']==''){
            $arLista3[$arCount[3]]=$arRecordSet[3]->arElementos[$c];
            $vlSoma = str_replace('.', '', $arRecordSet[3]->arElementos[$c]['valor']);
            $vlSoma = str_replace(',', '.', $vlSoma);
            $vlTotal = $vlTotal+$vlSoma;

            $arCount[3]++;
        }
    }
    
    for($c=0;$c<count($arRecordSet[4]->arElementos);$c++){
        if($arRecordSet[4]->arElementos[$c]['conciliado']==false){
            $arLista4[$arCount[4]]=$arRecordSet[4]->arElementos[$c];
            $vlSoma = str_replace('.', '', $arRecordSet[4]->arElementos[$c]['valor']);
            $vlSoma = str_replace(',', '.', $vlSoma);
        
            if($arRecordSet[4]->arElementos[$c]['manual']==true)
                $vlTotal = $vlTotal-($vlSoma);
            else
                $vlTotal = $vlTotal+($vlSoma);
                
            $arCount[4]++;
        }
    }
    
    for($c=0;$c<count($arRecordSet[5]->arElementos);$c++){
        if($arRecordSet[5]->arElementos[$c]['conciliado']==false){
            $arLista5[$arCount[5]]=$arRecordSet[5]->arElementos[$c];
            $vlSoma = str_replace('.', '', $arRecordSet[5]->arElementos[$c]['valor']);
            $vlSoma = str_replace(',', '.', $vlSoma);
            
            if($arRecordSet[5]->arElementos[$c]['manual']==true)
                $vlTotal = $vlTotal-($vlSoma);
            else
                $vlTotal = $vlTotal+($vlSoma);
                
            $arCount[5]++;
        }
    }
    
    $countLista0=0;
    for($c=0;$c<count($arRecordSet[0]->arElementos);$c++){
        $arLista0[$countLista0]=$arRecordSet[0]->arElementos[$c];
        if($countLista0==1){
            $arLista0[$countLista0]['descricao'] = 'Banco';
            $arLista0[$countLista0]['valor']     = $arFiltroForm['stNomeBanco'];
            $countLista0++;
            
            $arLista0[$countLista0]['descricao'] = 'Agência';
            $arLista0[$countLista0]['valor']     = $arFiltroForm['stNomeAgencia'];
            $countLista0++;
            
            $arLista0[$countLista0]['descricao'] = 'Conta Corrente';
            $arLista0[$countLista0]['valor']     = $arFiltroForm['inNumeroConta'];
            
            for($b=0;$b<count($arCodPlano);$b++){
                $countLista0++;
                
                if($b==0)
                    $arLista0[$countLista0]['descricao'] = 'Conta Banco';    
                else
                    $arLista0[$countLista0]['descricao'] = '';

                $arLista0[$countLista0]['valor'] = $arCodPlano[$b]['inCodPlano']." - ".$arCodPlano[$b]['stNomConta'];
            }
        }
        $countLista0++;
    }    
}

$vlTotal = $vlTotal - $arFiltro['nuSaldoTesouraria'];
$vlTotal = number_format($vlTotal*(-1), 2, ',', '.');

$arLista6[0]['descricao']   = 'Saldo Conciliado';
$arLista6[0]['valor']       = $vlTotal;

$rsNewRecord = new RecordSet;
$rsNewRecord->preenche( array_orderby($arLista2, 'movimentacao' , SORT_ASC) );
$arRecordSet[2] = $rsNewRecord;

$rsNewRecord = new RecordSet;
$rsNewRecord->preenche( array_orderby($arLista3, 'movimentacao' , SORT_ASC) );
$arRecordSet[3] = $rsNewRecord;

$rsNewRecord = new RecordSet;
$rsNewRecord->preenche( array_orderby($arLista4, 'movimentacao' , SORT_ASC) );
$arRecordSet[4] = $rsNewRecord;

$rsNewRecord = new RecordSet;
$rsNewRecord->preenche( array_orderby($arLista5, 'movimentacao' , SORT_ASC) );
$arRecordSet[5] = $rsNewRecord;

$rsNewRecord = new RecordSet;
$rsNewRecord->preenche( $arLista6 );
$arRecordSet[6] = $rsNewRecord;

$rsNewRecord = new RecordSet;
$rsNewRecord->preenche( $arLista0 );
$arRecordSet[0] = $rsNewRecord;

Sessao::write('arDados', $arRecordSet);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioConciliacaoCC.php" );
?>
