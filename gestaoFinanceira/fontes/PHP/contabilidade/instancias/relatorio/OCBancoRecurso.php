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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desencolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: OCBancoRecurso.php 60613 2014-11-03 20:41:50Z jean $

    * Casos de uso: uc-02.02.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioBancoRecurso.class.php"  );
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";

$stCtrl = $request->get('stCtrl');

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );

switch ($stCtrl) {
 case "MontaAgencia":
        if ($request->get("inNumBanco") != '') {
            $stSelecionado = $request->get('inNumAgencia');
            $stJs .= "limpaSelect(f.stNomeAgencia,0); \n";
            $stJs .= "jq('#inNumAgencia').val(''); \n";
            $stJs .= "jq('#stNomeAgencia').html('<option value=\"0\">Selecione</option>');\n";
            
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $request->get("inNumBanco") );
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );
            
            $stJs .= "jq('#inCodBanco').val('".$rsBanco->getCampo('cod_banco')."');\n";
            
            $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);
            
            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_agencia");
                $stDesc = $rsCombo->getCampo("nom_agencia"); 
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                    
                $stJs .= "jq('#stNomeAgencia').append('<option value=\"".$inId."\" Select=\"".$stSelected."\">".$stDesc."</option>');\n";
                $rsCombo->proximo();
            }
            
            $stJs .= "limpaSelect(f.stContaCorrente, 1);";
        } else {
            $stJs .= "jq('#inCodBanco').val('');\n";
            $stJs .= "limpaSelect(f.stNomeAgencia,0); \n";
            $stJs .= "jq('#inNumAgencia').val(''); \n";
            $stJs .= "jq('#stNomeAgencia').append('<option value=\"0\">Selecione</option>');\n";
            $stJs .= "limpaSelect(f.stContaCorrente,0);";
            $stJs .= "jq('#stContaCorrente').append('<option value=\"0\">Selecione</option>');\n";
        }
        echo $stJs;
    break;

    case "MontaContaCorrente":
        if ($request->get("inNumAgencia") != '') {
            
            $obRContabilidadePlanoBanco->setCodConta( $request->get('inCodConta') );
            $obRContabilidadePlanoBanco->setCodPlano( $request->get('inCodPlano') );
            
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setCodBanco( $request->get("inCodBanco") );
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $request->get("inNumBanco") );
            $obRContabilidadePlanoBanco->obRMONAgencia->setNumAgencia( $request->get("inNumAgencia") );
            $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);
            $stJs .= "jq('#inCodAgencia').val('".$rsCombo->getCampo('cod_agencia')."');\n";
            
            $obRContabilidadePlanoBanco->consultar();
            
            $stCombo  = "stContaCorrente";
            $stSelecionado = $request->get('stContaCorrente');
            $stJs .= "limpaSelect(f.".$stCombo.",0); \n";
            $stJs .= "jq('#".$stCombo."').html('<option value=\"0\">\Selecione</option>');\n";
            
            include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
            $obRMONContaCorrente = new RMONContaCorrente();
            $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $request->get('inCodBanco') );
            $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $request->get('inNumAgencia') );
            
            $rsCCorrente = new RecordSet();
            $obRMONContaCorrente->listarContaCorrente( $rsCCorrente, $obTransacao );
            
            $inCount - 0;
            while ( !$rsCCorrente->eof() ) {
                $inCount++;
                $inId = $rsCCorrente->getCampo("num_conta_corrente");
                $stDesc = $rsCCorrente->getCampo("num_conta_corrente");
                if ($stSelecionado == $inId) {
                    $stSelected = 'selected';
                } else {
                    $stSelected = '';
                }
                $stJs .= "jq('#".$stCombo."').append('<option value=\"".$inId."\" Select=\"".$stSelected."\">".$stDesc."</option>');\n";
                $rsCCorrente->proximo();
            }
        } else {
            $stJs .= "jq('#inCodAgencia').val('');\n";
            $stJs .= "limpaSelect(f.stContaCorrente,0);";
            $stJs .= "jq('#stContaCorrente').html('<option value=\"0\">Selecione</option>');\n";
        }
        echo $stJs;
    break;

    case "limpaCombo2":
        $stJs .= "limpaSelect('f.stNomeAgencia',0);\n";
        SistemaLegado::executaFrameOculto($stJs);
    break;
   
    case "BuscaContaCorrente":
            if ($request->get("stContaCorrente") != '' && $request->get("stContaCorrente") != 0) {
                //$obRContabilidadePlanoBanco->setCodConta( $_REQUEST['inCodConta'] );
                include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php";
                $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;
                $obTContabilidadePlanoBanco->setDado( 'cod_banco'	 	, $_REQUEST['inCodBanco']);
                $obTContabilidadePlanoBanco->setDado( 'cod_agencia'	 	, $_REQUEST['inCodAgencia']);
                $obTContabilidadePlanoBanco->setDado( 'conta_corrente'	        , $_REQUEST['stContaCorrente']);
                $obTContabilidadePlanoBanco->setDado( 'exercicio'	 	, Sessao::getExercicio());

                if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.1' ) {
                    $stFiltro = " AND ( cod_estrutural like '1.1.1.1.2%' or cod_estrutural like '1.1.1.1.3%' ) ";
                }

                if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.2' ) {
                    $stFiltro = " AND ( cod_estrutural like '1.1.1.1.1%' or cod_estrutural like '1.1.1.1.2%' ) ";
                }
                
                if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.3' ) {
                    $stFiltro = " AND ( cod_estrutural like '1.1.1.1.1%' or cod_estrutural like '1.1.1.1.3%' ) ";
                }
                
                $obTContabilidadePlanoBanco->listarPorEstrutural( $rsPlanoBanco , $stFiltro );
                
                //if ( $rsPlanoBanco->eof() ) { retirada validação para aceitar contas correntes repetidas para o mesmo banco e agencia, para atender necessidade do Tribunal de contas do estado de Goias
                
                    $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );
                    
                    include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
                    $obRMONContaCorrente = new RMONContaCorrente();
                    $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
                    $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );
                    $obRMONContaCorrente->obRMONAgencia->setCodAgencia( $_REQUEST['inCodAgencia'] );
                    $obRMONContaCorrente->setNumeroConta( $_REQUEST['stContaCorrente']);
                    $obRMONContaCorrente->consultarContaCorrente( $rsCCorrente, $obTransacao );

                    $stJs .= "f.inContaCorrente.value = ".$rsCCorrente->getCampo( 'cod_conta_corrente')."; \n";
                    $stJs .= "alertaAviso('','','','".Sessao::getId()."'); \n";
            } else {
                    //$stJs .= "alertaAviso('Já existe a <i><b>conta plano: ".$rsPlanoBanco->getCampo('cod_plano')."</b></i> cadastrada para a <i><b>conta corrente ".$_REQUEST['stContaCorrente']."</b></i> informada.','form','erro','".Sessao::getId()."');";
                    //$stJs .= "f.stContaCorrente.selectedIndex = 0;";
                    $stJs .= "jq('#inContaCorrente').val(''); \n";
            }
           echo $stJs;
    break;

}

if ($stCtrl == '') {
 
    $obRRelatorio  = new RRelatorio;
    $obRegra       = new RContabilidadeRelatorioBancoRecurso;
    
    $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
    $obRContabilidadePlanoContaAnalitica->setExercicio ( Sessao::getExercicio() );
    $obRContabilidadePlanoContaAnalitica->recuperaMascaraConta( $stMascara );
    
    //seta elementos do filtro
    $stFiltro = "";
    
    $arFiltro = Sessao::read('filtroRelatorio');
    
    //seta elementos do filtro para ENTIDADE
    if ($arFiltro['inCodEntidade'] != "") {
        $stEntidades = "";
        foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
            $stEntidades .= $valor." , ";
        }
        $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 2 ) . "";
    } else {
        $stEntidades .= $arFiltro['stTodasEntidades'];
    }
    
    foreach ($arFiltro as $key => $valor) {
        if (substr($key, 0, 6) == "grupo_") {
            $stGrupos .= substr($key,6,99).", ";
            $boGrupo = true;
        }
        if (substr($key, 0, 8) == "sistema_") {
            $stSistemas .= substr($key,8,99).", ";
            $boSistema = true;
        }
    }
    
    if ($boGrupo)   $stFiltro .= "\n substr(cod_estrutural,1,1)::integer in (".substr($stGrupos,0,strlen($stGrupos)-2).") AND ";
    if ($boSistema) $stFiltro .= "\n cod_sistema in (".substr($stSistemas,0,strlen($stSistemas)-2).") AND ";
    
    if ($arFiltro['stCodEstruturalInicial'] or $arFiltro['stCodEstruturalFinal']) {
        if (!$arFiltro['stCodEstruturalInicial']) {
            $stCodEstruturalInicial = str_replace(9,'0',$stMascara);
        } else $stCodEstruturalInicial = $arFiltro['stCodEstruturalInicial'];
    
        if ($arFiltro['stCodEstruturalFinal']) {
            $arCodEstruturalFinal = explode( '.' ,$arFiltro['stCodEstruturalFinal'] );
            $inSize = sizeof($arCodEstruturalFinal);
            for ($inSize -1; $inSize >= 0 ; $inSize--) {
                if ($arCodEstruturalFinal[$inSize-1] == 0) {
                    $arCodEstruturalFinal[$inSize-1] = str_pad(9,strlen($arCodEstruturalFinal[$inSize-1]),'9',STR_PAD_LEFT);
                } else {
                    break;
                }
            }
            $stCodEstruturalFinal = implode('.',$arCodEstruturalFinal);
        } else {
            $stCodEstruturalFinal = $stMascara;
        }
        
        $arCodEstrutural = array();
        $arCodEstrutural[] = $stCodEstruturalInicial;
        $arCodEstrutural[] = $stCodEstruturalFinal;
    }
    
    $arCodReduzido = array ();
    if ($arFiltro['inCodPlanoInicial'] != "") {
        $arCodPlano[] = $arFiltro['inCodPlanoInicial'];
    }
    if ($arFiltro['inCodPlanoFinal'] != "") {
        $arCodPlano[] = $arFiltro['inCodPlanoFinal'];
    } else {
        $arCodPlano[] = '';
    }
    
    //seta elementos do filtro
    $obRegra->setExercicio      ( Sessao::getExercicio() );
    $obRegra->setEntidades      ( $stEntidades );
    $obRegra->setCodEstrutural  ( $arCodEstrutural );
    $obRegra->setCodPlano       ( $arCodPlano );
    $obRegra->setCodRecurso     ( $arFiltro['inCodRecurso'] );
    $obRegra->setDescricao      ( $arFiltro['stDescricao'] );
    $obRegra->setCodBanco       ( $arFiltro['inCodBanco'] );
    $obRegra->setCodAgencia     ( $arFiltro['inCodAgencia'] );
    $obRegra->setContaCorrente  ( $arFiltro['inContaCorrente'] );
    $obRegra->setOrdenacao      ( $arFiltro['inOrdenacao'] );
    
    $obRegra->geraRecordSet ( $rsBancoRecurso );
    Sessao::write('rsBancoRecurso', $rsBancoRecurso);
    
    $obRRelatorio->executaFrameOculto( "OCGeraRelatorioBancoRecurso.php" );
}
?>
