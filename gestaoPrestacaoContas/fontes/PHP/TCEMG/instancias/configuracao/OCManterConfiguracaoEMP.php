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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OCManterConfiguracaoEMP.php 61800 2015-03-04 20:16:20Z arthur $
  * $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
  * $Author: gelson $
  * $Rev: 59612 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoEMP.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasModalidade.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php';


function incluirEmpenho() {
    $obErro  = new Erro();

    if ( $_REQUEST['stExercicio'] != '' &&
         $_REQUEST['inCodEntidade'] != '' &&
         $_REQUEST['inCodEmpenho'] != '' &&
         $_REQUEST['stExercicioLicitacao'] != '' &&
         $_REQUEST['inCodLicitacao'] != '' &&
         $_REQUEST['inCodModalidade'] != '' ) {
        
        $arListaEmpenho = Sessao::read('arListaEmpenho');
        
        foreach ($arListaEmpenho as $arItem) {
            if ($arItem['inCodEmpenho']  == $_REQUEST['inCodEmpenho'] &&
                $arItem['inCodEntidade'] == $_REQUEST['inCodEntidade'] &&
                $arItem['stExercicio']   == $_REQUEST['stExercicio']) {
                
                $obErro->setDescricao("Este Empenho já está na lista!");
            }
        }
        
        $obComprasModalidade = new TComprasModalidade();
        $obComprasModalidade->setDado('cod_modalidade', $_REQUEST['inCodModalidade']);
        $obComprasModalidade->recuperaPorChave($rsModalidade);
        
        $obTOrcamentoEntidade = new TOrcamentoEntidade;
        $obTOrcamentoEntidade->recuperaRelacionamento($rsEntidade, " AND E.cod_entidade = ".$_REQUEST['inCodEntidade']." AND E.exercicio = '".Sessao::getExercicio()."'");
        
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $obTEmpenhoEmpenho->setDado('exercicio'   , $_REQUEST['stExercicio']);
        $obTEmpenhoEmpenho->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTEmpenhoEmpenho->setDado('cod_empenho' , $_REQUEST['inCodEmpenho']);
        $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho ($rsEmpenho);
        
        $arEmpenho = array();
        $arEmpenho['stExercicio']           = $_REQUEST['stExercicio'];
        $arEmpenho['inCodEntidade']         = $_REQUEST['inCodEntidade'];
        $arEmpenho['stNomEntidade']         = $rsEntidade->getCampo('nom_cgm');
        $arEmpenho['inCodEmpenho']          = $_REQUEST['inCodEmpenho'];
        $arEmpenho['stDescEmpenho']         = $rsEmpenho->getCampo('credor');
        $arEmpenho['stExercicioLicitacao']  = $_REQUEST['stExercicioLicitacao'];
        $arEmpenho['inCodLicitacao']        = $_REQUEST['inCodLicitacao'];
        $arEmpenho['inCodModalidade']       = $_REQUEST['inCodModalidade'];
        $arEmpenho['stDescricaoModalidade'] = $rsModalidade->getCampo('descricao');
        $arEmpenho['inId']                  = md5(date('YmdHis').rand());
       
    } else {
       $obErro->setDescricao("Informe Todos os campos!");
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arListaEmpenho[] = $arEmpenho;
        
        Sessao::write('arListaEmpenho', $arListaEmpenho);
        
        $stJs .= "alertaAviso('Empenho incluido.','form','erro','".Sessao::getId()."');\n";
        $stJs .= montaEmpenho();
        $stJs .= limparFormEmpenho();
    }
    
    return $stJs;
}

function alterarEmpenho() {
    $obErro  = new Erro();

    $arListaEmpenho = Sessao::read('arListaEmpenho');
   
    foreach ($arListaEmpenho as $arItem) {
        if ($arItem['inCodEmpenho'] == $_REQUEST['inCodEmpenho'] &&
            $arItem['inCodEntidade'] == $_REQUEST['inCodEntidade'] &&
            $arItem['stExercicio'] == $_REQUEST['stExercicio'] &&
            $arItem['inId'] != $_REQUEST['inId']) {
            
            $obErro->setDescricao("Este Empenho já está na lista!");
        }
    }
   
    foreach ($arListaEmpenho as $key => $arEmpenho) {
        if ($arEmpenho['inId'] == $_REQUEST['inId']) {
            $obComprasModalidade = new TComprasModalidade();
            $obComprasModalidade->setDado('cod_modalidade', $_REQUEST['inCodModalidade']);
            $obComprasModalidade->recuperaPorChave($rsModalidade);
            
            $obTOrcamentoEntidade = new TOrcamentoEntidade;
            $obTOrcamentoEntidade->recuperaRelacionamento($rsEntidade, " AND E.cod_entidade = ".$_REQUEST['inCodEntidade']." AND E.exercicio = '".Sessao::getExercicio()."'");
            
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado('exercicio'   , $_REQUEST['stExercicio']);
            $obTEmpenhoEmpenho->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTEmpenhoEmpenho->setDado('cod_empenho' , $_REQUEST['inCodEmpenho']);
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho ($rsEmpenho);
            
            $arListaEmpenho[$key]['stExercicio']           = $_REQUEST['stExercicio'];
            $arListaEmpenho[$key]['inCodEntidade']         = $_REQUEST['inCodEntidade'];
            $arListaEmpenho[$key]['stNomEntidade']         = $rsEntidade->getCampo('nom_cgm');
            $arListaEmpenho[$key]['inCodEmpenho']          = $_REQUEST['inCodEmpenho'];
            $arListaEmpenho[$key]['stDescEmpenho']         = $rsEmpenho->getCampo('credor');
            $arListaEmpenho[$key]['stExercicioLicitacao']  = $_REQUEST['stExercicioLicitacao'];
            $arListaEmpenho[$key]['inCodLicitacao']        = $_REQUEST['inCodLicitacao'];
            $arListaEmpenho[$key]['inCodModalidade']       = $_REQUEST['inCodModalidade'];
            $arListaEmpenho[$key]['stDescricaoModalidade'] = $rsModalidade->getCampo('descricao');
        
            break;
        }
    }
    
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {

        Sessao::write('arListaEmpenho', $arListaEmpenho);
        
        $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery;";
        $stJs .= "jQuery('#btnIncluir').val('Incluir Empenho'); \n";
        $stJs .= "jQuery('#btnIncluir').attr('onclick', 'return montaParametrosGET(\'incluirEmpenho\',\'inId, stExercicio, inCodEntidade, inCodEmpenho, stExercicioLicitacao, inCodLicitacao, inCodModalidade\')'); \n";
        
        $stJs .= "alertaAviso('Empenho alterado.','form','erro','".Sessao::getId()."');\n";
        $stJs .= montaEmpenho();
        $stJs .= limparFormEmpenho();
    }
    
    SistemaLegado::executaFrameOculto($stJs);
}


function excluirEmpenho() {
    $arTemp = $arTempRemovido = array();

    $arListaEmpenho = Sessao::read('arListaEmpenho');

    foreach ($arListaEmpenho as $arEmpenho) {
        if ($arEmpenho['inId'] == $_GET['inId']) {
        } else {
            $arTemp[] = $arEmpenho;
        }
    }

    Sessao::write('arListaEmpenho', $arTemp);
    
    $stJs .= "alertaAviso('Empenho Removido.','form','erro','".Sessao::getId()."');\n";
    $stJs .= montaEmpenho();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function montaEmpenho() {
    $rsRecordSet = new RecordSet();
    
    if (Sessao::read('arListaEmpenho') != '') {
        $rsRecordSet->preenche(Sessao::read('arListaEmpenho'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Empenhos" );

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Exercício" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Entidade" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "N° Empenho" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Exercício Proc. Licitatório" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "N° Proc. Licitatório" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Modalidade" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ações");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stExercicio" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodEntidade] - [stNomEntidade]" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodEmpenho] - [stDescEmpenho]" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stExercicioLicitacao" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCodLicitacao" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodModalidade] - [stDescricaoModalidade]" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarItem');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();
  
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirEmpenho');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnListaEmpenho').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function validaEmpenho() {
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
    $obTEmpenhoEmpenho->setDado('exercicio'   , $_REQUEST['stExercicio']);
    $obTEmpenhoEmpenho->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTEmpenhoEmpenho->setDado('cod_empenho' , $_REQUEST['inCodEmpenho']);
    $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho ($rsEmpenho);
    
    $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery;";
    
    if($rsEmpenho->inNumLinhas < 1) {
        $stJs .= "alertaAviso('Empenho não encontrado.','form','erro','".Sessao::getId()."');\n";
        $stJs .= "jQuery('#stDescEmpenho').html('&nbsp;'); \n";
    } else {
        $stJs .= "jQuery('#stDescEmpenho').html('".$rsEmpenho->getCampo('credor')."'); \n";    
    }

    return $stJs;
}

function limparFormEmpenho() {
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;";

    $stJs .= "jQuery('#inCodEntidade').val('');        \n";
    $stJs .= "jQuery('#stNomEntidade').val('');        \n";
    $stJs .= "jQuery('#inCodEmpenho').val('');         \n";
    $stJs .= "jQuery('#stDescEmpenho').html('&nbsp;'); \n";
    $stJs .= "jQuery('#stExercicioLicitacao').val(''); \n";
    $stJs .= "jQuery('#inCodLicitacao').val('');       \n";
    $stJs .= "jQuery('#inCodModalidade').val('')       \n";

    return $stJs;
}

function limparFormEmpenhoEntidade() {
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;";

    $stJs .= "jQuery('#inCodEmpenho').val('');         \n";
    $stJs .= "jQuery('#stDescEmpenho').html('&nbsp;'); \n";
    $stJs .= "jQuery('#stExercicioLicitacao').val(''); \n";
    $stJs .= "jQuery('#inCodLicitacao').val('');       \n";
    $stJs .= "jQuery('#inCodModalidade').val('')       \n";

    return $stJs;
}

function alterarItem() {
    $arListaEmpenho = Sessao::read('arListaEmpenho');
        
    foreach ($arListaEmpenho as $arEmpenho) {
        
        if ($arEmpenho['inId'] == $_GET['inId']) {
            $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery;";

            $stJs .= "jQuery('#btnIncluir').val('Alterar Empenho'); \n";
            $stJs .= "jQuery('#btnIncluir').attr('onclick', 'return alterarEmpenho();');             \n";
                                                                                                     
            $stJs .= "jQuery('#inId').val('".$arEmpenho['inId']."');                                 \n";
            $stJs .= "jQuery('#stExercicio').val('".$arEmpenho['stExercicio']."');                   \n";
            $stJs .= "jQuery('#inCodEntidade').val('".$arEmpenho['inCodEntidade']."');               \n";
            $stJs .= "jQuery('#stNomEntidade').val('".$arEmpenho['inCodEntidade']."');               \n";
            $stJs .= "jQuery('#inCodEmpenho').val('".$arEmpenho['inCodEmpenho']."');                 \n";
            $stJs .= "jQuery('#stDescEmpenho').html('".$arEmpenho['stDescEmpenho']."');              \n";
            $stJs .= "jQuery('#stExercicioLicitacao').val('".$arEmpenho['stExercicioLicitacao']."'); \n";
            $stJs .= "jQuery('#inCodLicitacao').val('".$arEmpenho['inCodLicitacao']."');             \n";
            $stJs .= "jQuery('#inCodModalidade').val('".$arEmpenho['inCodModalidade']."');           \n";
            $stJs .= "jQuery('#stExercicio').focus();                                                \n";
            
            $obTTCEMGConfiguracaoEMP = new TTCEMGConfiguracaoEMP;
            $obTTCEMGConfiguracaoEMP->setDado('exercicio_licitacao' , $arEmpenho['stExercicioLicitacao']);
            $obTTCEMGConfiguracaoEMP->setDado('cod_licitacao'       , $arEmpenho['inCodLicitacao']);
            $obTTCEMGConfiguracaoEMP->setDado('cod_modalidade'      , $arEmpenho['inCodModalidade']);
            $obTTCEMGConfiguracaoEMP->setDado('cod_empenho'         , $arEmpenho['inCodEmpenho']);
            $obTTCEMGConfiguracaoEMP->recuperaComprasLicitacao($rComprasLicitacao);
            
            // Verifica se determinada licitação nao está na consulta devido a compras, caso nao esteja desabilita, nao permitindo o usuario alterar.
            if ($rComprasLicitacao->getNumLinhas() <= 0){
                $stJs .= "jQuery('#stExercicioLicitacao').attr('readonly', true); \n";
                $stJs .= "jQuery('#inCodLicitacao').attr('readonly', true); \n";
            } else {
                $stJs .= "jQuery('#stExercicioLicitacao').attr('readonly', false); \n";
                $stJs .= "jQuery('#inCodLicitacao').attr('readonly', false); \n";
            }

            break;
        }
    }
    
    SistemaLegado::executaFrameOculto($stJs);
}

switch ($request->get('stCtrl')) {
    case 'incluirEmpenho':
        $stJs .= incluirEmpenho();
    break;

    case 'alterarEmpenho':
        $stJs .= alterarEmpenho();
    break;    
    
    case "excluirEmpenho":
        $stJs .= excluirEmpenho();
    break;

    case 'alterarItem':
        $stJs .= alterarItem();
    break;

    case 'validaEmpenho':
        $stJs .= validaEmpenho();
    break;

    case 'limparFormEmpenho':
        $stJs .= limparFormEmpenho();
    break;

    case 'limparFormEmpenhoEntidade':
        $stJs .= limparFormEmpenhoEntidade();
    break;
    

    case 'carregaDados':
        $arListaEmpenho = array();
        $obTTCEMGConfiguracaoEMP = new TTCEMGConfiguracaoEMP;
        $obTTCEMGConfiguracaoEMP->recuperaTodos($rsConfigEmpenho);
        
        foreach($rsConfigEmpenho->getElementos() as $empenho) {

            $obComprasModalidade = new TComprasModalidade();
            $obComprasModalidade->setDado('cod_modalidade', $empenho['cod_modalidade']);
            $obComprasModalidade->recuperaPorChave($rsModalidade);
            
            $obTOrcamentoEntidade = new TOrcamentoEntidade;
            $obTOrcamentoEntidade->recuperaRelacionamento($rsEntidade, " AND E.cod_entidade = ".$empenho['cod_entidade']." AND E.exercicio = '".$empenho['exercicio']."'");
            
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado('exercicio'   , $empenho['exercicio']);
            $obTEmpenhoEmpenho->setDado('cod_entidade', $empenho['cod_entidade']);
            $obTEmpenhoEmpenho->setDado('cod_empenho' , $empenho['cod_empenho']);
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho ($rsEmpenho);
            
            $arEmpenho['inId']                  = md5(date('YmdHis').rand());
            $arEmpenho['stExercicio']           = $empenho['exercicio'];
            $arEmpenho['inCodEntidade']         = $empenho['cod_entidade'];
            $arEmpenho['stNomEntidade']         = $rsEntidade->getCampo('nom_cgm');
            $arEmpenho['inCodEmpenho']          = $empenho['cod_empenho'];
            $arEmpenho['stDescEmpenho']         = $rsEmpenho->getCampo('credor');
            $arEmpenho['stExercicioLicitacao']  = $empenho['exercicio_licitacao'];
            $arEmpenho['inCodLicitacao']        = $empenho['cod_licitacao'];
            $arEmpenho['inCodModalidade']       = $empenho['cod_modalidade'];
            $arEmpenho['stDescricaoModalidade'] = $rsModalidade->getCampo('descricao');
            
            $arListaEmpenho[] = $arEmpenho;
        }
        
        Sessao::write('arListaEmpenho', $arListaEmpenho);
        
        $stJs .= montaEmpenho();
    break;
}

if (isset($stJs)) {
   echo $stJs;
}
