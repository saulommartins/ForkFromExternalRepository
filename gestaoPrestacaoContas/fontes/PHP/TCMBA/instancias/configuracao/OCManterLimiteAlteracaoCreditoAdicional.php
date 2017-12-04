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

    * Pacote de configuração do TCMBA - Limites Para Alteração de Créditos Adicionais
    * Data de Criação   : 11/09/2015
    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    * 
    * $id:$
    
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO ."TTCMBALimiteAlteracaoCredito.class.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO ."TTCMBATipoAlteracaoOrcamentaria.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLimiteAlteracaoCreditoAdicional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

function montaListaDados()
{
    $rsRecordSet = new RecordSet();
    if (Sessao::read('arDados') != "") {
        $rsRecordSet->preenche(Sessao::read('arDados'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Limites para Alteração de Créditos" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nº");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Entidade" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nº Lei Autorizativa" );
    $obLista->ultimoCabecalho->setWidth( 28 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo de alteração orçamentária" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor da alteração" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao_entidade" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao_lei" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao_alteracao" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor_alteracao" );
    $obLista->ultimoDado->setAlinhamento('DIREITA');
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( " JavaScript:modificaDado('alterarLista'); " );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( " JavaScript:modificaDado('excluirLista'); " );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaInnerHTML();
    $stHtml = $obLista->getHTML();
    
    $stJs = " jq('#spnLista').html('".$stHtml."'); \n";

    return $stJs;
}

function carregaDadosTipoAlteracao()
{
    $obTTCMBATipoAlteracaoOrcamentaria = new TTCMBATipoAlteracaoOrcamentaria();
    $obTTCMBATipoAlteracaoOrcamentaria->recuperaTipoLimiteAlteracaoCredito($rsTiposAlteracoes, "", "ORDER BY cod_tipo", $boTransacao );

    foreach ($rsTiposAlteracoes->getElementos() as $value) {
        $stJs .= " jq('#stTipoAlteracaoOrcamentaria').append(new Option('".$value['cod_tipo']." - ".$value['descricao']."','".$value['cod_tipo']."') ); \n";
    }
    
    return $stJs;
}

function carregaDadosLista()
{
    $obTTCMBALimiteAlteracaoCredito = new TTCMBALimiteAlteracaoCredito();
    $obTTCMBALimiteAlteracaoCredito->recuperaLimiteAlteracao($rsLimiteAlteracao,"","",$boTransacao);
    $arTmp = array();

    foreach ($rsLimiteAlteracao->getElementos() as $key => $value) {
        $arTmp[$key]['inId']                = $key;
        $arTmp[$key]['descricao_entidade']  = $value['descricao_entidade'];
        $arTmp[$key]['descricao_alteracao'] = $value['descricao_alteracao'];
        $arTmp[$key]['descricao_lei']       = $value['descricao_lei'];
        $arTmp[$key]['exercicio']           = $value['exercicio'];
        $arTmp[$key]['cod_entidade']        = $value['cod_entidade'];
        $arTmp[$key]['cod_norma']           = $value['cod_norma'];
        $arTmp[$key]['cod_tipo_alteracao']  = $value['cod_tipo_alteracao'];
        $arTmp[$key]['valor_alteracao']     = number_format($value['valor_alteracao'],2,',','.');
    }
    
    Sessao::write('arDados', $arTmp ); 
    unset($arTmp);
}

function validaInclusaoLista($arDados)
{
    foreach ($arDados as $key => $value) {
        if ( $_REQUEST['inCodEntidade'] == $value['cod_entidade'] &&
             $_REQUEST['inCodNormaAutorizativa'] == $value['cod_norma'] &&
             $_REQUEST['stTipoAlteracaoOrcamentaria'] == $value['cod_tipo_alteracao']             
            ) {
                return false;
        }
    }
    return true;
}

function incluirLista()
{
    $arDados = Sessao::read('arDados');

    $boValida = validaInclusaoLista($arDados);
    if ($boValida == true) {
        $obTTCMBATipoAlteracaoOrcamentaria = new TTCMBATipoAlteracaoOrcamentaria();
        $obTTCMBATipoAlteracaoOrcamentaria->recuperaTipoLimiteAlteracaoCredito($rsTipos,"WHERE cod_tipo = ".$_REQUEST['stTipoAlteracaoOrcamentaria']." ","",$boTransacao);
        
        include_once ( TORC."TOrcamentoEntidade.class.php"    );
        $obTEntidade = new TOrcamentoEntidade();
        $obTEntidade->recuperaRelacionamento( $rsEntidade,"AND e.exercicio = '".Sessao::getExercicio()."' AND e.cod_entidade = ".$_REQUEST['inCodEntidade']."","",$boTransacao );
        $stNomeEntidade = $rsEntidade->getCampo('nom_cgm');
        
        $inProx = count($arDados);
        $stDescricaoLei           = $_REQUEST['inCodNormaAutorizativa'].' - '.$_REQUEST['stDescricaoNorma'];
        $stDescricaoEntidade      = $_REQUEST['inCodEntidade'].' - '.$stNomeEntidade;
        $stDescricaoTipoAlteracao = $_REQUEST['stTipoAlteracaoOrcamentaria'].' - '.$rsTipos->getCampo('descricao');

        $arDados[$inProx]['inId']                = $inProx;   
        $arDados[$inProx]['descricao_entidade']  = $stDescricaoEntidade;
        $arDados[$inProx]['descricao_alteracao'] = $stDescricaoTipoAlteracao;
        $arDados[$inProx]['descricao_lei']       = $stDescricaoLei;
        $arDados[$inProx]['exercicio']           = Sessao::getExercicio();
        $arDados[$inProx]['cod_entidade']        = $_REQUEST['inCodEntidade'];
        $arDados[$inProx]['cod_norma']           = $_REQUEST['inCodNormaAutorizativa'];
        $arDados[$inProx]['cod_tipo_alteracao']  = $_REQUEST['stTipoAlteracaoOrcamentaria'];
        $arDados[$inProx]['valor_alteracao']     = $_REQUEST['nuValorAlteracao'];
        
        Sessao::write('arDados',$arDados);
        
        $stJs .= montaListaDados();
        $stJs .= limparListaIncluisao();
    
    }else{
        $stJs = " alertaAviso('Não foi possível incluir porque o registro já existe na lista!','erro','erro','".Sessao::getId()."'); ";

    }
    
    return $stJs;
}

function alterarLista()
{
    $arDados = Sessao::read('arDados');
    
    foreach ($arDados as $value) {
        if ($value['inId'] == $_REQUEST['inId']) {
            $stJs  = " jq('#inCodEntidade').val('".$value['cod_entidade']."');        \n";
            $stJs .= " jq('#stNomEntidade').val('".$value['cod_entidade']."'); \n";
            $stJs .= " jq('#inCodNormaAutorizativa').val('".$value['cod_norma']."');  \n";
            $stJs .= " jq('#inCodNormaAutorizativa').blur(); \n";
            $stJs .= " jq('#stTipoAlteracaoOrcamentaria').val('".$value['cod_tipo_alteracao']."'); \n";
            $stJs .= " jq('#nuValorAlteracao').val('".$value['valor_alteracao']."');  \n";
            $stJs .= " jq('#btIncluir').val('Alterar');  \n";
            $stJs .= " jq('#btIncluir').attr('onClick','JavaScript: if ( validaCamposLista() ){ montaParametrosGET(\'alteraItemLista\'); }');  \n";
        }
    }

    return $stJs;
}

function alteraItemLista()
{
    $arDados = Sessao::read('arDados');
    
    $boValida = validaInclusaoLista($arDados);
    if ($boValida) {
        $obTTCMBATipoAlteracaoOrcamentaria = new TTCMBATipoAlteracaoOrcamentaria();
        $obTTCMBATipoAlteracaoOrcamentaria->recuperaTipoLimiteAlteracaoCredito($rsTipos,"WHERE cod_tipo = ".$_REQUEST['stTipoAlteracaoOrcamentaria']." ","",$boTransacao);
        
        include_once ( TORC."TOrcamentoEntidade.class.php"    );
        $obTEntidade = new TOrcamentoEntidade();
        $obTEntidade->recuperaRelacionamento( $rsEntidade,"AND e.exercicio = '".Sessao::getExercicio()."' AND e.cod_entidade = ".$_REQUEST['inCodEntidade']."","",$boTransacao );
        $stNomeEntidade = $rsEntidade->getCampo('nom_cgm');
        
        $inProx = count($arDados);
        $stDescricaoEntidade      = $_REQUEST['inCodEntidade'].' - '.$stNomeEntidade;
        $stDescricaoTipoAlteracao = $_REQUEST['stTipoAlteracaoOrcamentaria'].' - '.$rsTipos->getCampo('descricao');

        foreach ($arDados as $key => $value) {
            if ($_REQUEST['inId'] == $value['inId'] ) {
                $arDados[$key]['inId']                = $value['inId'];
                $arDados[$key]['descricao_entidade']  = $stDescricaoEntidade;
                $arDados[$key]['descricao_alteracao'] = $stDescricaoTipoAlteracao;
                $arDados[$key]['descricao_lei']       = $_REQUEST['stDescricaoNorma'];
                $arDados[$key]['cod_entidade']        = $_REQUEST['inCodEntidade'];
                $arDados[$key]['cod_norma']           = $_REQUEST['inCodNormaAutorizativa'];
                $arDados[$key]['cod_tipo_alteracao']  = $_REQUEST['stTipoAlteracaoOrcamentaria'];
                $arDados[$key]['valor_alteracao']     = $_REQUEST['nuValorAlteracao'];
            }
        }

        Sessao::write('arDados',$arDados);
        
        $stJs .= " jq('#btIncluir').val('Incluir');  \n";
        $stJs .= " jq('#btIncluir').attr('onClick','JavaScript: if ( validaCamposLista() ){ montaParametrosGET(\'incluirLista\'); }');  \n";
    }else{
        $stJs = " alertaAviso('Registro já existe na lista!','erro','erro','".Sessao::getId()."'); ";
    }
    
    $stJs .= montaListaDados();
    $stJs .= limparListaIncluisao();
    
    return $stJs;
}

function excluirLista()
{
    $arDados = Sessao::read('arDados');
       
    foreach ($arDados as $key => $value) {
        if ($_REQUEST['inId'] != $value['inId'] ) {
            $arTmp[] = $value;                  
        }
    }

    Sessao::write('arDados',$arTmp);
    $stJs .= montaListaDados();

    return $stJs;
}

function limparListaIncluisao()
{
    $stJs  = " jq('#inCodEntidade').val('');                \n";
    $stJs .= " jq('#stNomEntidade').val('');                \n";
    $stJs .= " jq('#inCodNormaAutorizativa').val('');       \n";
    $stJs .= " jq('#stNorma').html('&nbsp;');               \n";
    $stJs .= " jq('#stTipoAlteracaoOrcamentaria').val('');  \n";
    $stJs .= " jq('#nuValorAlteracao').val('0,00');         \n";
    
    return $stJs;
}

switch ($stCtrl) {
    case 'carregaListaDados':
        //Carrega dados já cadastrados e atribui no array da sessao
        carregaDadosLista();
        //Carrega dados da combo de tipo de alteracao e lista
        $stJs  = carregaDadosTipoAlteracao();
        $stJs .= montaListaDados();
    break;
    
    case 'incluirLista':
        $stJs = incluirLista();
    break;

    case 'alterarLista':
        $stJs = alterarLista();
    break;

    case 'alteraItemLista':
        $stJs = alteraItemLista();
    break;

    case 'excluirLista':
        $stJs = excluirLista();
    break;

    case 'limparLista':
        $stJs = limparListaIncluisao();
    break;
}

if ($stJs) {
    echo $stJs;
}

?>