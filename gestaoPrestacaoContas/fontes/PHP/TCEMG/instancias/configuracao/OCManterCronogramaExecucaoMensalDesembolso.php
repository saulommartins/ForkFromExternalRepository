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

/**
  * Oculto de Configuração de Cronograma de Execucao Mensal de Desembolso 
  * Data de Criação   : 29/02/2016

  * @author Analista      Ane Caroline
  * @author Desenvolvedor Lisiane Morais

  * @package URBEM
  * @subpackage

  * $Id:$
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGCronogramaExecucaoMensalDesembolso.class.php';


//Define o nome dos arquivos PHP
$stPrograma = "ManterCronogramaExecucaoMensalDesembolso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');
$saldo_inicial = 0.00;
  
switch ($stCtrl) {
case 'montaDadosUnidade':
    require_once CAM_GF_ORC_COMPONENTES."ISelectUnidade.class.php";
    $obInCodUnidade = new ISelectUnidade;
    $obInCodUnidade->setExercicio( Sessao::getExercicio() );
    $obInCodUnidade->setNumOrgao( $request->get('inCodOrgao') );
    $rsUnidade = $obInCodUnidade->getRecordSet();

    $js = " var arUnidade = $('inCodUnidade').options;";
    $js .= "\n for (var chave in arUnidade) {
        arUnidade[chave] = null;
    }";
    $js .= "\n arUnidade[0] = new Option('Selecione', '', '');";
    $inCount = 1;
    $arNumUnidade = array();
    while ( !$rsUnidade->eof() ) {
        $js .= "\n arUnidade[".$inCount."] = new Option('".$rsUnidade->getCampo('num_unidade')." - " . $rsUnidade->getCampo('nom_unidade') . "', '".$rsUnidade->getCampo('num_unidade')."', '');";
        $inCount++;
        array_push($arNumUnidade, $rsUnidade->getCampo('num_unidade'));
        $rsUnidade->proximo();
    }
    $inCodigoUnidades = implode (",", $arNumUnidade);
    $js .= "\n arUnidade[".$inCount."] = new Option('Todos', '0', '');";
    $js .= "\n f.inCodigosUnidade.value = '".$inCodigoUnidades."';";

    echo $js;
    break;

case 'mudaValor':
    //FILTRO
    $inCodEntidade = $request->get('inCodEntidade');
    $inCodOrgao    = $request->get('inCodOrgao');
    $inCodUnidade  = $request->get('inCodUnidade');
    $stExercicio   = Sessao::getExercicio();
    
    $TTCEMGCronogramaExecucaoMensalDesembolso = new TTCEMGCronogramaExecucaoMensalDesembolso;
    $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('cod_entidade'  , $inCodEntidade      );
    $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('num_orgao'     , $inCodOrgao         );
    $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('exercicio'     , $stExercicio        );
    $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('num_unidade'   , $inCodUnidade       );
    $TTCEMGCronogramaExecucaoMensalDesembolso->recuperaGruposDespesa($rsGruposDespesa, '', $boTransacao);
    $vlSomatorioGrupos = 0.00;
  
    foreach ($rsGruposDespesa->getElementos() as $gruposDespesa) {
        $vlSomatorioPeriodo = 0.00;
        for ($x = 1; $x <= 12; $x++) {
            $vlPeriodo = str_replace(".","",($_REQUEST[$x.'_'.$gruposDespesa['cod_grupo']]));
            $vlPeriodo = str_replace(",",".",$vlPeriodo);
            $vlSomatorioPeriodo = bcadd($vlPeriodo, $vlSomatorioPeriodo, 2);
        }
 
        if($vlSomatorioPeriodo != 0) {
            $js .= 'f.TotalValor_'.$gruposDespesa['cod_grupo'].'.value = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
        } else {
            $js .= 'f.TotalValor_'.$gruposDespesa['cod_grupo'].'.value = " ";';
        }
        
        $vlSomatorioPeriodo = str_replace(",",".",$vlSomatorioPeriodo); 
        $vlSomatorioGrupos = bcadd($vlSomatorioGrupos, $vlSomatorioPeriodo, 2);

        if ($vlSomatorioGrupos > $request->get('hdnVlSaldoTotal') ) {
            $js .= "alertaAviso('Valores Totais Superior ao Saldo Diponível para esta Unidade.','form','erro','".Sessao::getId()."');";
        } 
    }
    echo $js;
    break;
}

function montaSpanGruposDespesa(){
    global $request;  
    //FILTRO
    $inCodEntidade = $request->get('inCodEntidade');
    $inCodOrgao    = $request->get('inCodOrgao');
    $inCodUnidade  = $request->get('inCodUnidade');
    $stExercicio   = Sessao::getExercicio();
    $arIDValor2    = array();
    
    $TTCEMGCronogramaExecucaoMensalDesembolso = new TTCEMGCronogramaExecucaoMensalDesembolso;
    $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('cod_entidade'  , $inCodEntidade      );
    $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('num_orgao'     , $inCodOrgao         );
    $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('exercicio'     , $stExercicio        );
    $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('num_unidade'   , $inCodUnidade       );
    $TTCEMGCronogramaExecucaoMensalDesembolso->recuperaOrgaoUnidade($rsOrgaoUnidade, '', $boTransacao);   
    $TTCEMGCronogramaExecucaoMensalDesembolso->listarPeriodo( $rsListaPeriodo,'', $boTransacao); 
    $TTCEMGCronogramaExecucaoMensalDesembolso->recuperaSaldoInicial( $rsSaldoInicial,'', $boTransacao );
  
    $nom_orgao = $rsOrgaoUnidade->getCampo('nom_orgao');
    $nom_unidade = $rsOrgaoUnidade->getCampo('nom_unidade');
    $saldo_inicial = number_format($rsSaldoInicial->getCampo( 'saldo_inicial' ) == "" ? 0.00 : $rsSaldoInicial->getCampo( 'saldo_inicial' ), 2, ",", ".");
      
    $saldo_total = str_replace(".","",$saldo_inicial);
    $saldo_total = str_replace(",",".",$saldo_total);
  
    if ($rsListaPeriodo->getNumLinhas() < 1) {
        //Insere um registro para cada mes do ano
        foreach($rsOrgaoUnidade->getElementos() AS $registro) {
            for($i=1;$i<=12;$i++){
                $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('cod_entidade',$inCodEntidade);
                $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('periodo'     ,$i );
                $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('exercicio'   ,$stExercicio);
                $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('num_unidade' ,$registro['num_unidade'] );
                $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('num_orgao'   ,"'".$inCodOrgao."'");
                $TTCEMGCronogramaExecucaoMensalDesembolso->insereNovosGruposPeriodo($boTransacao);
            }
        }
        $TTCEMGCronogramaExecucaoMensalDesembolso->listarPeriodo($rsListaPeriodo, '', $boTransacao);
    }
  
    // Função de nomes dos meses para a configuração mesal
    function mes($inValor)
    {
        $stMes = array ( "","Jan.","Fev.","Mar.","Abr.","Maio","Jun.",
                        "Jul.","Ago.","Set.","Out.","Nov.","Dez.");
        return $stMes[$inValor];
    }
  
    while ( !$rsListaPeriodo->eof() ) {
        $arIDValor2[ $rsListaPeriodo->getCampo('cod_grupo') ][ ($rsListaPeriodo->getCampo('periodo') ) ] = $rsListaPeriodo->getCampo( 'valor' );
        $inCodGrupoAnterior = $rsListaPeriodo->getCampo('cod_grupo');
        $rsListaPeriodo->proximo();
    }

    $TTCEMGCronogramaExecucaoMensalDesembolso->recuperaGruposDespesa($rsGruposDespesa, '', $boTransacao);
    $arValorMensal = array();
    $i = 0 ;
    foreach ( $rsGruposDespesa->getElementos() as $arRegistro ) {
        $vlSomatorioPeriodo = 0.00;
        $arValorMensal[$i] = $arRegistro;
        for ($x=1; $x<=12; $x++ ) {
            $vlPeriodo = str_replace(",",".",($arIDValor2[$arRegistro['cod_grupo']][$x])); 
            $vlSomatorioPeriodo = bcadd($vlPeriodo, $vlSomatorioPeriodo, 2);
            $arValorMensal[$i][$x] = number_format($arIDValor2[$arRegistro['cod_grupo']][$x],2, ",", ".");
        }
        $arValorMensal[$i]['valorTotal'] = number_format($vlSomatorioPeriodo,2, ",", ".");
        $arValorMensal[$i]['saldo_inicial'] = number_format($rsSaldoInicial->getCampo( 'saldo_inicial' ) == "" ? 0.00 : $rsSaldoInicial->getCampo( 'saldo_inicial' ), 2, ",", ".");
        $i++;
    }
    
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arValorMensal );
    $obFormulario = new Formulario;
    $obFormulario->addForm ( $obForm );
    
    $obLista = new Lista();
    $obLista->setMostraPaginacao(false);
    $obLista->setTitulo($nom_orgao.' - '.$nom_unidade. ': R$ ' .$saldo_inicial);
    $obLista->setRecordSet($rsTemp);
    
    //Cabeçalhos
    $obLista->addCabecalho('', 1);
    $obLista->addCabecalho('Grupo de Despesa');
    
    //Dados
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('[cod_grupo] - [descricao]');
    $obLista->commitDadoComponente();
    
    for($i=1; $i<=12; $i++) {
        $stMes = mes($i);
        $obVlCronogramaMensal = new Numerico;
        $obVlCronogramaMensal->setRotulo             ( "Valor"                                      );
        $obVlCronogramaMensal->setTitle              ( "Informe o valor"                            );
        $obVlCronogramaMensal->setName               ( "$i"."_"                            );
        $obVlCronogramaMensal->setValue              ( "[$i]"                 );
        $obVlCronogramaMensal->setDecimais           ( 2                                            );      
        $obVlCronogramaMensal->setNull               ( false                                        );
        $obVlCronogramaMensal->setNegativo           ( false                                        );
        $obVlCronogramaMensal->obEvento->setOnChange(" montaParametrosGET('mudaValor','', 'sincrono'); ");
        
        $obLista->addCabecalho($stMes, 5);
        $obLista->addDadoComponente( $obVlCronogramaMensal);
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDadoComponente();
        $somatorio = $arIDValor2['cod_grupo'][$i] ;
    }
    
    $obTxtTotalValor = new Moeda();
    $obTxtTotalValor->setName( "TotalValor_" );
    $obTxtTotalValor->setId  ( "TotalValor_" );
    $obTxtTotalValor->setReadOnly( true );
    $obTxtTotalValor->setValue ( '[valorTotal]' );
  
    $obLista->addCabecalho('Total',5);
    $obLista->addDadoComponente( $obTxtTotalValor );
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->commitDadoComponente();
    
    $obLista->montaInnerHTML();
    $stHTML = $obLista->getHTML();
    
    $stJs .= "f.hdnVlSaldoTotal.value = ".$saldo_total.";    \n";
    $stJs .= "jQuery('#spnGruposDespesa').html('".$stHTML."');";
    $stJs .= "jQuery('#Ok').attr('disabled',false);\n";
    return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {
    case "montaSpanGruposDespesa":
            $stJs = montaSpanGruposDespesa();
   break;
}

if ($stJs) {
    echo $stJs;
}

?>