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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25762 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:20:03 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeOrcamentaria.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeOrcamentariaResponsavel.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNNaturezaJuridica.class.php");
include_once( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNFuncaoGestor.class.php" );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function preencheNorma()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma($_REQUEST['hdnCodNorma']);
    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->listar($rsNorma);
    include_once(CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php");
    $obTTipoNorma = new TTipoNorma();
    $stFiltro = " WHERE cod_tipo_norma = ".$rsNorma->getCampo("cod_tipo_norma");
    $obTTipoNorma->recuperaTodos($rsTipoNorma,$stFiltro);
    if ($rsTipoNorma->getCampo('cod_tipo_norma') != 0) {
        $stJs .= "document.getElementById('stCodNorma').value = '".trim($rsNorma->getCampo("num_norma_exercicio"))."';\n";
        $stJs .= "document.getElementById('stNorma').innerHTML= '".trim($rsTipoNorma->getCampo("nom_tipo_norma"))." ".$rsNorma->getCampo("num_norma")."/".$rsNorma->getCampo("exercicio")." - ".trim($rsNorma->getCampo("nom_norma"))."';\n";
    }

    return $stJs;
}

function montaLista($arResponsavel)
{
    $rsResponsavel = new RecordSet();
    $rsResponsavel->preenche( $arResponsavel );

    $obTable = new Table();
    $obTable->setRecordSet( $rsResponsavel );
    $obTable->setSummary('Responsáveis da Unidade');

    //$obTable->setConditional( true , "#efefef" );

    $obTable->Head->addCabecalho( 'Responsável' , 25);
    $obTable->Head->addCabecalho( 'Cargo' , 30 );
    $obTable->Head->addCabecalho( 'Função' , 35 );
    $obTable->Head->addCabecalho( 'Data de Início' , 10 );
    $obTable->Head->addCabecalho( 'Data de Término' , 10 );

    $obTable->Body->addCampo( '[inNumCGM] - [stNomCGM]', 'E' );
    $obTable->Body->addCampo( 'stCargo', 'E' );
    $obTable->Body->addCampo( '[stFuncao] - [stDescricao]', 'E' );
    $obTable->Body->addCampo( 'stDtInicio', 'E' );
    $obTable->Body->addCampo( 'stDtFim', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s)', array( 'id' ) );
    $obTable->Body->addAcao( 'alterar' ,  'montaAlteracaoLista(%s)' , array( 'id' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "document.getElementById('spnResponsavel').innerHTML = '".$stHTML."';";
    $stJs.= "limparResponsavel();";

    return $stJs;
}

function retornaData($stData)
{
    return implode('',array_reverse(explode('/',$stData)));
}

switch ($stCtrl) {

    case 'preencheDados' :
        echo preencheNorma();

        $obTResponsavel = new TTCERNUnidadeOrcamentariaResponsavel();
        $obTUnidade = new TTCERNUnidadeOrcamentaria();
        $stFiltro = " WHERE unidade.num_unidade = ".$_REQUEST['hdnNumUnidade']." AND unidade.num_orgao = ".$_REQUEST['hdnNumOrgao']." AND unidade.exercicio = '".Sessao::getExercicio()."'";
        $obTUnidade->recuperaRelacionamento($rsOrcamentaria, $stFiltro);

        if ($rsOrcamentaria->getNumLinhas() > 0) {
            $stFiltro = " AND unidade_orcamentaria.cod_institucional = ".$rsOrcamentaria->getCampo('cod_institucional')."";
            $obTResponsavel->recuperaRelacionamento( $rsResponsavel, $stFiltro);

            $inCount = 0;
            while ( !$rsResponsavel->eof() ) {
                $arResponsavel[$inCount]['id'] = $inCount;
                $arResponsavel[$inCount]['inNumCGM'] = $rsResponsavel->getCampo('numcgm');
                $arResponsavel[$inCount]['stNomCGM'] = $rsResponsavel->getCampo('nom_cgm');
                $arResponsavel[$inCount]['stCargo'] = $rsResponsavel->getCampo('cargo');
                $arResponsavel[$inCount]['stFuncao'] = $rsResponsavel->getCampo('cod_funcao');
                $arResponsavel[$inCount]['stDescricao'] = $rsResponsavel->getCampo('descricao');
                $arResponsavel[$inCount]['stDtInicio'] = $rsResponsavel->getCampo('dt_inicio');
                $arResponsavel[$inCount]['stDtFim'] = $rsResponsavel->getCampo('dt_fim');
                $inCount++;
                $rsResponsavel->proximo();
            }
            Sessao::write('arResponsavel', $arResponsavel);
            echo montaLista( $arResponsavel );
        }

    break;

    $arResponsavel = Sessao::read('arResponsavel');

    case 'incluiResponsavel' :
        if ($_REQUEST['inNumCGM'] == '') {
            $stMensagem = 'Responsável inválido ou campo vazio';
        } elseif ($_REQUEST['stCargo'] == '') {
            $stMensagem = 'Cargo não informado';
        } elseif ($_REQUEST['stFuncao'] == '') {
            $stMensagem = 'Função não informada';
        } elseif ($_REQUEST['stDtInicio'] == '') {
            $stMensagem = 'Data de início inválida ou campo vazio';
        } elseif ($_REQUEST['stDtFim'] == '') {
            $stMensagem = 'Data de término inválida ou campo vazio';
        } elseif ( retornaData($_REQUEST['stDtInicio']) >= retornaData($_REQUEST['stDtFim']) ) {
            $stMensagem  = 'A data de término deve ser superior a data de início';
        }
        if ( is_array($arResponsavel) ) {
            foreach ($arResponsavel as $arResponsavel) {
                if ( !((retornaData($_REQUEST['stDtInicio']) >= retornaData($arResponsavel['stDtFim'])) OR (retornaData($_REQUEST['stDtFim']) <= retornaData($arResponsavel['stDtInicio']))) ) {
                    $stMensagem = 'Já existe um responsável cadastrado neste período';
                }
            }
        }

        $arResponsavel = Sessao::read('arResponsavel');

        if ($_REQUEST['stFuncao']) {
            $obFuncao = new TTCERNFuncaoGestor;
            $obFuncao->recuperaTodos($rsFuncao," WHERE cod_funcao = ".$_REQUEST['stFuncao']."");
        }

        $arElementos = array();
        if (!$stMensagem) {
            $arElementos['id']          = count($arResponsavel);
            $arElementos['inNumCGM']    = $_REQUEST['inNumCGM'];
            $arElementos['stNomCGM']    = $_REQUEST['stNomCGM'];
            $arElementos['stCargo']     = $_REQUEST['stCargo'];
            $arElementos['stFuncao']    = $rsFuncao->getCampo('cod_funcao');
            $arElementos['stDescricao'] = $rsFuncao->getCampo('descricao');
            $arElementos['stDtInicio']  = $_REQUEST['stDtInicio'];
            $arElementos['stDtFim']     = $_REQUEST['stDtFim'];
            $arResponsavel[] = $arElementos;

            Sessao::write('arResponsavel', $arResponsavel);
            echo montaLista( $arResponsavel );
        } else {
            echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }
    break;

    case 'buscaCodInstitucional':
        $rsCodInstitucional = '';

        if ($_REQUEST['stInstitucional']) {
            $obTUnidade = new TTCERNUnidadeOrcamentaria();
            $stFiltro = " AND unidade_orcamentaria.cod_institucional = ".$_REQUEST['stInstitucional']."";
            $obTUnidade->recuperaRelacionamento($rsCodInstitucional, $stFiltro);
            if ($rsCodInstitucional->getNumLinhas() > 0) {
                $stJs .= "$('stInstitucional').value = '';";
                $stJs .= "alertaAviso('Já existe este código institucional.','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= "alertaAviso('','form','erro','".Sessao::getId()."');";
            }
        }

    break;

    case 'excluirListaItens':
        $arTemp = array();
        $inCount = 0;
        $arResponsavel = Sessao::read('arResponsavel');
        foreach ($arResponsavel as $arValue) {
            if ($arValue['id'] != $_REQUEST['id']) {
                $arTemp[$inCount]['id']            = $arValue['id'];
                $arTemp[$inCount]['inNumCGM']      = $arValue['inNumCGM'];
                $arTemp[$inCount]['stNomCGM']      = $arValue['stNomCGM'];
                $arTemp[$inCount]['stCargo']       = $arValue['stCargo'];
                $arTemp[$inCount]['stFuncao']      = $arValue['stFuncao'];
                $arTemp[$inCount]['stDescricao']   = $arValue['stDescricao'];
                $arTemp[$inCount]['stDtInicio']    = $arValue['stDtInicio'];
                $arTemp[$inCount]['stDtFim']       = $arValue['stDtFim'];
                $inCount++;
            }
        }

        Sessao::write('arResponsavel',$arTemp);
        echo montaLista( $arTemp );
    break;

    case 'montaAlteracaoLista':
        $inId = $_REQUEST['id']+1;

        $obResponsavel = new TTCERNUnidadeOrcamentariaResponsavel();
        $obResponsavel->recuperaRelacionamento($rsResponsavel, " AND unidade_orcamentaria_responsavel.id = ".$inId."");

        $stJs.= "d.getElementById('hdnId').value = '".($rsResponsavel->getCampo('id')-1)."';";
        $stJs.= "d.getElementById('inNumCGM').value = '".$rsResponsavel->getCampo('numcgm')."';";
        $stJs.= "d.getElementById('stNomCGM').innerHTML = '".$rsResponsavel->getCampo('nom_cgm')."';";
        $stJs.= "d.getElementById('stNomCGM').value = '".$rsResponsavel->getCampo('nom_cgm')."';";
        $stJs.= "d.getElementById('stCargo').value = '".$rsResponsavel->getCampo('cargo')."';";
        $stJs.= "d.getElementById('stFuncao').value = '".$rsResponsavel->getCampo('cod_funcao')."';";
        $stJs.= "d.getElementById('stDtInicio').value = '".$rsResponsavel->getCampo('dt_inicio')."';";
        $stJs.= "d.getElementById('stDtFim').value = '".$rsResponsavel->getCampo('dt_fim')."';";
        $stJs.= "d.getElementById('btIncluir').value = 'Alterar';";
        $stJs.= "d.getElementById('btIncluir').setAttribute('onClick','montaParametrosGET( \'alterarListaItens\', \'hdnId,inNumCGM,stNomCGM,stCargo,stFuncao,stDtInicio,stDtFim\' );');";

        $arResponsavel = Sessao::read('arResponsavel');
        Sessao::write('arResponsavel',$arResponsavel);
    break;

    case 'alterarListaItens':
        $arResponsavel = Sessao::read('arResponsavel');

        if ($_REQUEST['inNumCGM'] == '') {
            $stMensagem = 'Responsável inválido ou campo vazio';
        } elseif ($_REQUEST['stCargo'] == '') {
            $stMensagem = 'Cargo não informado';
        } elseif ($_REQUEST['stFuncao'] == '') {
            $stMensagem = 'Função não informada';
        } elseif ($_REQUEST['stDtInicio'] == '') {
            $stMensagem = 'Data de início inválida ou campo vazio';
        } elseif ($_REQUEST['stDtFim'] == '') {
            $stMensagem = 'Data de término inválida ou campo vazio';
        } elseif ( retornaData($_REQUEST['stDtInicio']) >= retornaData($_REQUEST['stDtFim']) ) {
            $stMensagem  = 'A data de término deve ser superior a data de início';
        }
        if ( is_array($arResponsavel) ) {
            foreach ($arResponsavel as $arResponsavel) {
                if ( !((retornaData($_REQUEST['stDtInicio']) >= retornaData($arResponsavel['stDtFim'])) OR (retornaData($_REQUEST['stDtFim']) <= retornaData($arResponsavel['stDtInicio']))) AND ( $_REQUEST['hdnId'] != $arResponsavel['id']) ) {
                    $stMensagem = 'Já existe um responsável cadastrado neste período';
                }
            }
        }

        $arResponsavel = Sessao::read('arResponsavel');
        $inCount = 0;

        if ($_REQUEST['stFuncao']) {
            $obFuncao = new TTCERNFuncaoGestor;
            $obFuncao->recuperaTodos($rsFuncao," WHERE cod_funcao = ".$_REQUEST['stFuncao']."");
        }

        if (!$stMensagem) {
            foreach ($arResponsavel as $key => $value) {
                if ($_REQUEST['hdnId'] == $value['id']) {
                    $arResponsavel[$inCount]['id']          = $_REQUEST['hdnId'];
                    $arResponsavel[$inCount]['inNumCGM']    = $_REQUEST['inNumCGM'];
                    $arResponsavel[$inCount]['stNomCG']     = $_REQUEST['stNomCGM'];
                    $arResponsavel[$inCount]['stCargo']     = $_REQUEST['stCargo'];
                    $arResponsavel[$inCount]['stFuncao']    = $rsFuncao->getCampo('cod_funcao');
                    $arResponsavel[$inCount]['stDescricao'] = $rsFuncao->getCampo('descricao');
                    $arResponsavel[$inCount]['stDtInicio']  = $_REQUEST['stDtInicio'];
                    $arResponsavel[$inCount]['stDtFim']     = $_REQUEST['stDtFim'];
                }
                $inCount++;
            }
            Sessao::write('arResponsavel',$arResponsavel);
            echo 'limparResponsavel();';
            echo montaLista( $arResponsavel );
        } else {
            echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }
    break;

    case 'bloquear':
        $stJs .= " jq('#Ok').prop('disabled',true);\n";
        $stJs .= " jq('#limpar').prop('disabled',true);\n";
    break;
}

echo $stJs;

?>
