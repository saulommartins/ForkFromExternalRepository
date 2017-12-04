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
    * Página de Processamento do frame Oculto
    * Data de Criação  : 15/05/2008

    * @author Analista Gelson W. Golçalves
    * @author Desenvolvedor Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * $Id: OCManterRecurso.php 66353 2016-08-16 20:04:08Z michel $

    * Casos de uso : uc-06.01.09

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_ORC_COMPONENTES."ISelectMultiploRecurso.class.php";
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once TORC."TOrcamentoEntidade.class.php";
require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "JS".$stPrograma.".js";


function montaListaRecurso($stAcao)
{
    $arRecursos = Sessao::read("arRecursos");
    if(!is_array($arRecursos))
        $arRecursos = array();

    $arRecursosFundeb60 = array();
    $arRecursosFundeb40 = array();

    foreach($arRecursos AS $key => $recurso){
        #inTipoRecurso: 1 => Recursos de Pagamento de Profissionais Magistério - Fundeb 60%
        #inTipoRecurso: 2 => Recursos de Outras Despesas - Fundeb 40%
        if($recurso['inTipoRecurso']==1 || $stAcao > 1){
            $arRecursosFundeb60[] = $recurso;
        }elseif($recurso['inTipoRecurso']==2){
            $arRecursosFundeb40[] = $recurso;
        }
    }

    $rsRecursos60 = new RecordSet();
    $rsRecursos60->preenche( $arRecursosFundeb60 );
    $rsRecursos60->ordena('inCodRecurso');
    $rsRecursos60->ordena('inCodUnidade');
    $rsRecursos60->ordena('inCodOrgao');
    $rsRecursos60->ordena('inCodEntidade');

    $obTable = new Table();
    $obTable->setRecordSet( $rsRecursos60 );

    if($stAcao == 1)
        $obTable->setSummary('Lista de Recursos de Pagamento de Profissionais Magistério');
    else
        $obTable->setSummary('Lista de Recursos');

    $obTable->Head->addCabecalho( 'Entidade'               , 5  );
    $obTable->Head->addCabecalho( 'Órgao'                  , 18 );
    $obTable->Head->addCabecalho( 'Unidade'                , 24 );
    $obTable->Head->addCabecalho( 'Recurso'                , 30 );
    $obTable->Head->addCabecalho( 'Ação'                   , 15 );
    $obTable->Head->addCabecalho( 'Educação Infantil'      , 8  );

    $obTable->Body->addCampo( 'inCodEntidade'                         , 'C' );
    $obTable->Body->addCampo( '[inCodOrgao] - [stNomOrgao]'           , 'E' );
    $obTable->Body->addCampo( '[inCodUnidade] - [stNomUnidade]'       , 'E' );
    $obTable->Body->addCampo( '[inCodRecurso] - [stDescricaoRecurso]' , 'E' );
    $obTable->Body->addCampo( 'stDescricaoAcao'                       , 'E' );
    $obTable->Body->addCampo( 'stTipoEducacaoInfantil'                , 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirRecurso(%s,%s,%s,%s,%s,%s)', array( 'inCodEntidade','inCodOrgao','inCodUnidade','inCodRecurso','inTipoRecurso','inCodAcao' ) );

    $obTable->montaHTML();
    $stHTML60 = $obTable->getHtml();
    $stHTML60 = str_replace( "\n" ,   "",$stHTML60 );
    $stHTML60 = str_replace( "  " ,   "",$stHTML60 );
    $stHTML60 = str_replace( "'"  ,"\\'",$stHTML60 );

    $stHTML40 = "";

    if($stAcao == 1){
        $rsRecursos40 = new RecordSet();
        $rsRecursos40->preenche( $arRecursosFundeb40 );
        $rsRecursos40->ordena('inCodRecurso');
        $rsRecursos40->ordena('inCodUnidade');
        $rsRecursos40->ordena('inCodOrgao');
        $rsRecursos40->ordena('inCodEntidade');

        $obTable = new Table();
        $obTable->setRecordSet( $rsRecursos40 );

        $obTable->setSummary('Lista de Recursos de Outras Despesas');
        $obTable->Head->addCabecalho( 'Entidade'               , 5  );
        $obTable->Head->addCabecalho( 'Órgao'                  , 18 );
        $obTable->Head->addCabecalho( 'Unidade'                , 24 );
        $obTable->Head->addCabecalho( 'Recurso'                , 30 );
        $obTable->Head->addCabecalho( 'Ação'                   , 15 );
        $obTable->Head->addCabecalho( 'Educação Infantil'      , 8  );

        $obTable->Body->addCampo( 'inCodEntidade'                         , 'C' );
        $obTable->Body->addCampo( '[inCodOrgao] - [stNomOrgao]'           , 'E' );
        $obTable->Body->addCampo( '[inCodUnidade] - [stNomUnidade]'       , 'E' );
        $obTable->Body->addCampo( '[inCodRecurso] - [stDescricaoRecurso]' , 'E' );
        $obTable->Body->addCampo( 'stDescricaoAcao'                       , 'E' );
        $obTable->Body->addCampo( 'stTipoEducacaoInfantil'                , 'E' );

        $obTable->Body->addAcao( 'excluir' ,  'excluirRecurso(%s,%s,%s,%s,%s,%s)', array( 'inCodEntidade','inCodOrgao','inCodUnidade','inCodRecurso','inTipoRecurso','inCodAcao' ) );
        $obTable->montaHTML();
        $stHTML40 = $obTable->getHtml();
        $stHTML40 = str_replace( "\n" ,   "",$stHTML40 );
        $stHTML40 = str_replace( "  " ,   "",$stHTML40 );
        $stHTML40 = str_replace( "'"  ,"\\'",$stHTML40 );
    }

    $stHTML = $stHTML60.$stHTML40;
    $stJs = "d.getElementById('spnListaRecurso').innerHTML = '".$stHTML."';";

    return $stJs;
}

function limparRecurso($boParcial = TRUE, $stAcao = 1)
{
    if(!$boParcial){
        $stJs .= "f.inCodEntidade.value = '';       \n";
        $stJs .= "f.stNomEntidade.value = '';       \n";
        $stJs .= "f.inCodOrgao.value = '';          \n";
        $stJs .= "jq('#inCodUnidade').empty().append(new Option('Selecione','')); \n";
        $stJs .= "jq('#inCodRecurso').empty().append(new Option('Selecione','')); \n";
        $stJs .= "jq('#inCodAcao').empty().append(new Option('Selecione',''));    \n";
    }

    if($stAcao==1)
        $stJs .= "f.inTipoRecurso.value = '';       \n";
    $stJs .= "f.inTipoEducacaoInfantil.value = '';  \n";
    $stJs .= "f.inCodRecurso.value = '';            \n";
    $stJs .= "f.inCodAcao.value = '';            \n";
    $stJs .= liberaTipoEducacao();

    return $stJs;
}

function montaAcaoRecurso(){
    $js = "jq('#inCodAcao').empty().append(new Option('Selecione','')); \n";

    if ($_REQUEST['inCodEntidade'] != "" && $_REQUEST['inCodOrgao'] != "" && $_REQUEST['inCodUnidade'] != "" && $_REQUEST['inCodRecurso'] != "") {
        list($inCodRecurso, $stDescricaoRecurso) = explode('-', $_REQUEST['inCodRecurso']);

        $obTOrcamentoRecurso = new TOrcamentoRecurso();
        $obTOrcamentoRecurso->setDado('exercicio', Sessao::getExercicio());
        $obTOrcamentoRecurso->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTOrcamentoRecurso->setDado('num_unidade', $_REQUEST['inCodUnidade']);
        $obTOrcamentoRecurso->setDado('num_orgao', $_REQUEST['inCodOrgao']);
        $obTOrcamentoRecurso->setDado('cod_recurso', $inCodRecurso);
        $obTOrcamentoRecurso->recuperaRecursoAcao( $rsRecursoAcao );

        while (!$rsRecursoAcao->eof()) {
            $stOption  = "'";
            $stOption .= $rsRecursoAcao->getCampo('num_acao');
            $stOption .= " - ";
            $stOption .= $rsRecursoAcao->getCampo('titulo');
            $stOption .= "', '";
            $stOption .= $rsRecursoAcao->getCampo('num_acao');
            $stOption .= "-";
            $stOption .= $rsRecursoAcao->getCampo('cod_acao');
            $stOption .= "-";
            $stOption .= $rsRecursoAcao->getCampo('titulo');
            $stOption .= "', ''";

            $js .= "jq('#inCodAcao').append(new Option(".$stOption.")); \n";

            $rsRecursoAcao->proximo();
        }
    }
    
    $js .= liberaTipoEducacao();

    return $js;
}

function liberaTipoEducacao(){
    $stJs   = "var acao = f.inCodAcao.value;                                \n";
    $stJs  .= "if(acao == ''){                                              \n";
    $stJs  .= "  f.inTipoEducacaoInfantil.value = '';                       \n";
    $stJs  .= "  jq('#inTipoEducacaoInfantil').attr('disabled','disabled'); \n";
    $stJs  .= "}else{                                                       \n";
    $stJs  .= "  jq('#inTipoEducacaoInfantil').removeAttr('disabled');      \n";
    $stJs  .= "}                                                            \n";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
case 'montaDadosUnidade':
    require_once CAM_GF_ORC_COMPONENTES."ISelectUnidade.class.php";
    $obInCodUnidade = new ISelectUnidade;
    $obInCodUnidade->setExercicio( Sessao::getExercicio() );
    $obInCodUnidade->setNumOrgao( $_REQUEST['inCodOrgao'] );
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

case "montaDadosRecurso":
    $js = " var arDisponivel = $('inCodRecursoDisponivel').options;";
    $js .= "\n for (var chave in arDisponivel) {
        arDisponivel[chave] = null;
    }";
    $js .= "\n var arSelecionado = $('inCodRecursoSelecionado').options;";
    $js .= "\n for (var chave in arSelecionado) {
        arSelecionado[chave] = null;
    }";

    if ($_REQUEST['stAcao'] == '1') {
        $js .= "\n var arDisponivel2 = $('inCodRecursoDisponivel2').options;";
        $js .= "\n for (var chave in arDisponivel2) {
            arDisponivel2[chave] = null;
        }";
        $js .= "\n var arSelecionado2 = $('inCodRecursoSelecionado2').options;";
        $js .= "\n for (var chave in arSelecionado2) {
            arSelecionado2[chave] = null;
        }";
    }

    if ($_REQUEST['inCodEntidade'] != "" && $_REQUEST['inCodOrgao'] != "" && $_REQUEST['inCodUnidade'] != "") {
        $pos = strpos($_REQUEST['inCodUnidade'], ",");

        if ($pos === false) {
            $stFiltro = " AND NOT EXISTS( SELECT 1
                                            FROM   stn.vinculo_recurso, stn.vinculo_stn_recurso
                                           WHERE  recurso.exercicio = vinculo_recurso.exercicio
                                             AND  recurso.cod_recurso = vinculo_recurso.cod_recurso
                                             AND  vinculo_recurso.cod_entidade = " . $_REQUEST['inCodEntidade'] . "
                                             AND  vinculo_recurso.num_unidade  = " . $_REQUEST['inCodUnidade'] . "
                                             AND  vinculo_recurso.num_orgao    = " . $_REQUEST['inCodOrgao'] . "
                                             AND  vinculo_recurso.cod_vinculo  = " . $_REQUEST['stAcao'] . "
                                             AND  vinculo_recurso.cod_vinculo  = vinculo_stn_recurso.cod_vinculo
                                             AND  vinculo_recurso.cod_tipo = 2)";

            if ($_REQUEST['stAcao'] == '1') {
                $stFiltro2 = substr_replace($stFiltro, "1)", -2);
            }

            $stFiltro .= " AND EXISTS ( SELECT 1
                                         FROM orcamento.despesa
                                        WHERE despesa.cod_entidade = " . $_REQUEST['inCodEntidade'] . "
                                          AND despesa.num_unidade  = " . $_REQUEST['inCodUnidade'] . "
                                          AND despesa.num_orgao    = " . $_REQUEST['inCodOrgao'] . "
                                          AND despesa.cod_recurso = recurso.cod_recurso
                                          AND despesa.exercicio   = recurso.exercicio)";

            if ($_REQUEST['stAcao'] == '1') {
                $stFiltro2 .= " AND EXISTS ( SELECT 1
                                         FROM orcamento.despesa
                                        WHERE despesa.cod_entidade = " . $_REQUEST['inCodEntidade'] . "
                                          AND despesa.num_unidade  = " . $_REQUEST['inCodUnidade'] . "
                                          AND despesa.num_orgao    = " . $_REQUEST['inCodOrgao'] . "
                                          AND despesa.cod_recurso = recurso.cod_recurso
                                          AND despesa.exercicio   = recurso.exercicio)";
            }
        } else {
            $stFiltro = " TRUE ";

            if ($_REQUEST['stAcao'] == '1') {
                $stFiltro2 = $stFiltro;
            }
        }

        // Para quando for FUNDEB
        if ($_REQUEST['stAcao'] == '1') {
            $obISelectMultiplRecurso2 = new ISelectMultiploRecurso;
            $obISelectMultiplRecurso2->setName("inCodRecurso2");
            $obISelectMultiplRecurso2->setExercicio( Sessao::getExercicio() );
            $obISelectMultiplRecurso2->setFiltro( $stFiltro2 );
            $obISelectMultiplRecurso2->montaRecordSet();

            if ($obISelectMultiplRecurso2->getRecordsetLista1()->getNumLinhas() < 1) {
                $rsRecursoDisponivel2 = $obISelectMultiplRecurso2->getRecordsetLista2();
            } else {
                $rsRecursoDisponivel2 = $obISelectMultiplRecurso2->getRecordsetLista1();
            }

            $inCount = 0;
            while ( !$rsRecursoDisponivel2->eof() ) {
                $js .= "\n arDisponivel2[".$inCount."] = new Option('".$rsRecursoDisponivel2->getCampo('cod_recurso')." - ".$rsRecursoDisponivel2->getCampo('nom_recurso')."', '".$rsRecursoDisponivel2->getCampo('cod_recurso')."', '');";
                $inCount++;
                $rsRecursoDisponivel2->proximo();
            }
        }
        //-------------
        $obISelectMultiplRecurso = new ISelectMultiploRecurso;
        $obISelectMultiplRecurso->setExercicio( Sessao::getExercicio() );
        $obISelectMultiplRecurso->setFiltro( $stFiltro );
        $obISelectMultiplRecurso->montaRecordSet();

        if ($obISelectMultiplRecurso->getRecordsetLista1()->getNumLinhas() < 1) {
            $rsRecursoDisponivel = $obISelectMultiplRecurso->getRecordsetLista2();
        } else {
            $rsRecursoDisponivel = $obISelectMultiplRecurso->getRecordsetLista1();
        }

        $inCount = 0;
        while ( !$rsRecursoDisponivel->eof() ) {
            $js .= "\n arDisponivel[".$inCount."] = new Option('".$rsRecursoDisponivel->getCampo('cod_recurso')." - ".$rsRecursoDisponivel->getCampo('nom_recurso')."', '".$rsRecursoDisponivel->getCampo('cod_recurso')."', '');";
            $inCount++;
            $rsRecursoDisponivel->proximo();
        }

        require_once CAM_GPC_STN_MAPEAMENTO."TSTNVinculoRecurso.class.php";

        //Para quando for FUNDEB
        if ($_REQUEST['stAcao'] == '1') {
            $obTSTNVinculoRecurso2 = new TSTNVinculoRecurso;
            $obTSTNVinculoRecurso2->setDado('exercicio'   , Sessao::getExercicio()        );
            $obTSTNVinculoRecurso2->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTSTNVinculoRecurso2->setDado('num_orgao'   , $_REQUEST['inCodOrgao']   );
            $obTSTNVinculoRecurso2->setDado('num_unidade' , $_REQUEST['inCodUnidade'] );

            $obTSTNVinculoRecurso2->recuperaVinculoRecurso( $rsRecursoSelecionado2, 'AND vinculo_recurso.cod_vinculo='.$_REQUEST['stAcao'].' AND vinculo_recurso.cod_tipo=1');

            $inCount = 0;
            while ( !$rsRecursoSelecionado2->eof() ) {
                $js .= "\n arSelecionado2[".$inCount."] = new Option('".$rsRecursoSelecionado2->getCampo('cod_recurso')." - " . $rsRecursoSelecionado2->getCampo('nom_recurso') . "', '".$rsRecursoSelecionado2->getCampo('cod_recurso')."', '');";
                $inCount++;
                $rsRecursoSelecionado2->proximo();
            }
        }
        //---------------
        $obTSTNVinculoRecurso = new TSTNVinculoRecurso;
        $obTSTNVinculoRecurso->setDado('exercicio'   , Sessao::getExercicio()        );
        $obTSTNVinculoRecurso->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTSTNVinculoRecurso->setDado('num_orgao'   , $_REQUEST['inCodOrgao']   );
        $obTSTNVinculoRecurso->setDado('num_unidade' , $_REQUEST['inCodUnidade'] );

        $obTSTNVinculoRecurso->recuperaVinculoRecurso( $rsRecursoSelecionado, 'AND vinculo_recurso.cod_vinculo='.$_REQUEST['stAcao'].' AND vinculo_recurso.cod_tipo=2');

        $inCount = 0;
        while ( !$rsRecursoSelecionado->eof() ) {
            $js .= "\n arSelecionado[".$inCount."] = new Option('".$rsRecursoSelecionado->getCampo('cod_recurso')." - " . $rsRecursoSelecionado->getCampo('nom_recurso') . "', '".$rsRecursoSelecionado->getCampo('cod_recurso')."', '');";
            $inCount++;
            $rsRecursoSelecionado->proximo();
        }
    }

    echo $js;

    break;

    case 'montaDadosRecursoFundebMDE':
        $js = "jq('#inCodRecurso').empty().append(new Option('Selecione','')); \n";

        if ($_REQUEST['inCodEntidade'] != "" && $_REQUEST['inCodOrgao'] != "" && $_REQUEST['inCodUnidade'] != "") {
            require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";

            $stFiltro = " 
                          AND EXISTS ( SELECT 1
                                         FROM orcamento.despesa
                                        WHERE despesa.cod_entidade = ".$_REQUEST['inCodEntidade']."
                                          AND despesa.num_unidade  = ".$_REQUEST['inCodUnidade']."
                                          AND despesa.num_orgao    = ".$_REQUEST['inCodOrgao']."
                                          AND despesa.cod_recurso = recurso.cod_recurso
                                          AND despesa.exercicio   = recurso.exercicio
                                     )
            ";

            $obTOrcamentoRecurso = new TOrcamentoRecurso();
            $obTOrcamentoRecurso->setDado('exercicio', Sessao::getExercicio());
            $obTOrcamentoRecurso->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTOrcamentoRecurso->setDado('num_unidade', $_REQUEST['inCodUnidade']);
            $obTOrcamentoRecurso->setDado('num_orgao', $_REQUEST['inCodOrgao']);
            $obTOrcamentoRecurso->recuperaRelacionamento( $rsRecurso , $stFiltro, 'recurso.cod_recurso' );

            while (!$rsRecurso->eof()) {
                $stOption  = "'";
                $stOption .= $rsRecurso->getCampo('cod_recurso');
                $stOption .= " - ";
                $stOption .= $rsRecurso->getCampo('nom_recurso');
                $stOption .= "', '";
                $stOption .= $rsRecurso->getCampo('cod_recurso');
                $stOption .= "-";
                $stOption .= $rsRecurso->getCampo('nom_recurso');
                $stOption .= "', ''";

                $js .= "jq('#inCodRecurso').append(new Option(".$stOption.")); \n";

                $rsRecurso->proximo();
            }
        }

        $js .= montaAcaoRecurso();

        echo $js;
    break;

    case 'montaAcaoRecurso':
        $js = montaAcaoRecurso();

        echo $js;
    break;

    case "incluirRecurso":
        $stJs   = "";
        $stErro = "";

        $arRecursos = Sessao::read("arRecursos");
        if(!is_array($arRecursos))
            $arRecursos = array();

        $inCodEntidade          = $request->get('inCodEntidade');
        $inCodOrgao             = $request->get('inCodOrgao');
        $inCodUnidade           = $request->get('inCodUnidade');
        $inTipoRecurso          = $request->get('inTipoRecurso');
        $inTipoEducacaoInfantil = $request->get('inTipoEducacaoInfantil');
        list($inCodRecurso, $stDescricaoRecurso) = explode('-', $request->get('inCodRecurso'));
        list($inNumAcao, $inCodAcao, $stDescricaoAcao) = explode('-', $request->get('inCodAcao'));

        if(empty($inCodEntidade))
            $stErro .= '@Informe o campo Entidade!';
        if(empty($inCodOrgao))
            $stErro .= '@Informe o campo Órgao!';
        if(empty($inCodUnidade))
            $stErro .= '@Informe o campo Unidade!';
        if(empty($inTipoRecurso) && $request->get('stAcao') != 1)
            $inTipoRecurso = 2;
        if(empty($inTipoRecurso))
            $stErro .= '@Informe o campo Tipo de Recurso!';
        if(empty($inCodRecurso))
            $stErro .= '@Informe o campo Recurso!';
        if(!empty($inCodAcao) && empty($inTipoEducacaoInfantil))
            $stErro .= '@Informe o campo Tipo Educação Infantil!';
        
        if(empty($stErro)){
            $stChaveIncluir  = $inCodEntidade;
            $stChaveIncluir .= '.'.$inCodOrgao;
            $stChaveIncluir .= '.'.$inCodUnidade;
            $stChaveIncluir .= '.'.$inCodRecurso;
            $stChaveIncluir .= '.'.$inTipoRecurso;
            $stChaveIncluir .= '.'.$inCodAcao;

            foreach($arRecursos AS $key => $recurso){
                $stChaveRecurso  = $recurso['inCodEntidade'];
                $stChaveRecurso .= '.'.$recurso['inCodOrgao'];
                $stChaveRecurso .= '.'.$recurso['inCodUnidade'];
                $stChaveRecurso .= '.'.$recurso['inCodRecurso'];
                $stChaveRecurso .= '.'.$recurso['inTipoRecurso'];
                $stChaveRecurso .= '.'.$recurso['inCodAcao'];

                if($stChaveIncluir == $stChaveRecurso){
                    $stErro .= '@Erro ao Incluir (recurso '.$inCodRecurso.' já vinculado)!';
                    break;
                }
            }
        }

        if(empty($stErro)){
            $arStTipoRecurso[1] = "Recursos de Pagamento de Profissionais Magistério";
            $arStTipoRecurso[2] = "Recursos de Outras Despesas";

            $stTipoEducacaoInfantil = "";
            $arStTipoEducacaoInfantil[1] = "Creche";
            $arStTipoEducacaoInfantil[2] = "Pré-Escola";
            if(!empty($inTipoEducacaoInfantil))
                $stTipoEducacaoInfantil = $arStTipoEducacaoInfantil[$inTipoEducacaoInfantil];

            $obTEntidade = new TOrcamentoEntidade();
            $obTEntidade->setDado('exercicio'    , Sessao::getExercicio());
            $obTEntidade->setDado('cod_entidade' , $inCodEntidade        );
            $obTEntidade->recuperaRelacionamentoNomes( $rsEntidadesGeral );

            $obTOrcamentoUnidade = new TOrcamentoUnidade;
            $stFiltro  = "      AND  unidade.exercicio = '".Sessao::getExercicio()."'";
            $stFiltro .= "\n    AND  unidade.num_orgao = ".$request->get('inCodOrgao');
            $stFiltro .= "\n    AND  unidade.num_unidade = ".$request->get('inCodUnidade');
            $obTOrcamentoUnidade->recuperaRelacionamento( $rsUnidade, $stFiltro, 'orcamento.unidade.num_unidade' );

            $arTemp = array();
            $arTemp['inCodEntidade']          = $inCodEntidade;
            $arTemp['stNomEntidade']          = $rsEntidadesGeral->getCampo('entidade');
            $arTemp['inCodOrgao']             = $inCodOrgao;
            $arTemp['stNomOrgao']             = $rsUnidade->getCampo('nom_orgao');
            $arTemp['inCodUnidade']           = $inCodUnidade;
            $arTemp['stNomUnidade']           = $rsUnidade->getCampo('nom_unidade');
            $arTemp['inTipoRecurso']          = $inTipoRecurso;
            $arTemp['stTipoRecurso']          = $arStTipoRecurso[$inTipoRecurso];
            $arTemp['inTipoEducacaoInfantil'] = $inTipoEducacaoInfantil;
            $arTemp['stTipoEducacaoInfantil'] = $stTipoEducacaoInfantil;
            $arTemp['inCodRecurso']           = $inCodRecurso;
            $arTemp['stDescricaoRecurso']     = $stDescricaoRecurso;
            $arTemp['inCodAcao']              = $inCodAcao;
            $arTemp['stDescricaoAcao']        = (!empty($inCodAcao)) ? $inNumAcao.' - '.$stDescricaoAcao : '';

            $arRecursos[] = $arTemp;
        }

        if(!empty($stErro))
            $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');";
        else{
            Sessao::write("arRecursos", $arRecursos);

            $stJs .= "alertaAviso('Recurso(".$inCodRecurso.") incluído na Lista de Recursos!','form','erro','".Sessao::getId()."');";
            $stJs .= montaListaRecurso($request->get('stAcao'));
            $stJs .= limparRecurso(TRUE, $request->get('stAcao'));
        }

        echo $stJs;
    break;

    case 'excluirRecurso':
        $arRecursos = Sessao::read("arRecursos");
        $arTemp = array();

        $inAcao = $request->get('inAcao');
        $inAcao = ($inAcao == 'inCodAcao') ? '' : $inAcao;

        $stChaveExcluir  = $request->get('inEntidade');
        $stChaveExcluir .= '.'.$request->get('inOrgao');
        $stChaveExcluir .= '.'.$request->get('inUnidade');
        $stChaveExcluir .= '.'.$request->get('inRecurso');
        $stChaveExcluir .= '.'.$request->get('inTipo');
        $stChaveExcluir .= '.'.$inAcao;

        foreach($arRecursos AS $key => $recurso){
            $stChaveRecurso  = $recurso['inCodEntidade'];
            $stChaveRecurso .= '.'.$recurso['inCodOrgao'];
            $stChaveRecurso .= '.'.$recurso['inCodUnidade'];
            $stChaveRecurso .= '.'.$recurso['inCodRecurso'];
            $stChaveRecurso .= '.'.$recurso['inTipoRecurso'];
            $stChaveRecurso .= '.'.$recurso['inCodAcao'];

            if($stChaveExcluir != $stChaveRecurso)
                $arTemp[] = $recurso;
        }

        Sessao::write("arRecursos", $arTemp);
        $stJs = montaListaRecurso($request->get('stAcao'));

        SistemaLegado::executaFrameOculto($stJs);
    break;

    case 'montaListaRecurso':
        $stJs .= montaListaRecurso($request->get('stAcao'));

        echo $stJs;
    break;

    case 'limparRecurso':
        $stJs  = limparRecurso(FALSE,$request->get('stAcao'));

        echo $stJs;
    break;

    case 'limpar':
        $stJs = 'document.getElementById("frm").reset();';

        Sessao::remove("arRecursos");

        echo $stJs;
    break;

    case 'liberaTipoEducacao':
        $stJs = liberaTipoEducacao();

        echo $stJs;
    break;
}

?>
