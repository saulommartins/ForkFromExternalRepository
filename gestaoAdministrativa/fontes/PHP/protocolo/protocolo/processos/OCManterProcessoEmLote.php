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
    * Página de Listagem para Arquivar Processo em Lote.
    * Data de Criação: 23/04/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.98

    $Id: LSManterProcessoEmLote.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php";
include_once CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcessoEmLote";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

$stJs = "";

function montaLista ()
{
    $rsListaProcesso = new RecordSet();
    $arListaProcesso = array();

    if (Sessao::read('arListaProcesso') != "") {
        $arListaProcesso = Sessao::read('arListaProcesso');
        $rsListaProcesso->preenche($arListaProcesso);

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Listagem de Processos" );
        $obLista->setRecordSet( $rsListaProcesso );
        //--------------------------------------
        // CABEÇALHO ---------------------------
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth (3);
        $obLista->commitCabecalho();
        //---------------------------
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo ("Código");
        $obLista->ultimoCabecalho->setWidth (10);
        $obLista->commitCabecalho();
        //---------------------------
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo ("Interessado");
        $obLista->ultimoCabecalho->setWidth (22);
        $obLista->commitCabecalho();
        //---------------------------
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo ("Classificação");
        $obLista->ultimoCabecalho->setWidth (22);
        $obLista->commitCabecalho();
        //---------------------------
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo ("Assunto");
        $obLista->ultimoCabecalho->setWidth (32);
        $obLista->commitCabecalho();
        //---------------------------
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo ("Ação");
        $obLista->ultimoCabecalho->setWidth (4);
        $obLista->commitCabecalho();
        //-------------------------------
        // Monta os dados----------------
        $obLista->addDado();
        $obLista->ultimoDado->setCampo ("[cod_processo]"."/"."[ano_exercicio]");
        $obLista->ultimoDado->setAlinhamento ("ESQUERDA");
        $obLista->commitDado();
        //---------------------------
        $obLista->addDado();
        $obLista->ultimoDado->setCampo ("nom_cgm");
        $obLista->ultimoDado->setAlinhamento ("ESQUERDA");
        $obLista->commitDado();
        //---------------------------
        $obLista->addDado();
        $obLista->ultimoDado->setCampo ("nom_classificacao");
        $obLista->ultimoDado->setAlinhamento ("ESQUERDA");
        $obLista->commitDado();
        //---------------------------
        $obLista->addDado();
        $obLista->ultimoDado->setCampo ("nom_assunto");
        $obLista->ultimoDado->setAlinhamento ("ESQUERDA");
        $obLista->commitDado();
        //---------------------------
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('removerProcesso'); " );
        $obLista->ultimaAcao->addCampo("" , "&id=[inId]");
        $obLista->commitAcao();
        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    
        $stJs .= "jq('#spnLancamentos').html('".$stHtml."');\n";
    } else {
        $stJs .= "jq('#spnLancamentos').html('&nbsp;');\n";
    }

    return $stJs;
}

function validaProcesso (Request $request)
{
    $stMensagem = '';

    $arListaProcesso = array();
    $arListaProcesso = Sessao::read("arListaProcesso");
    $inCount = count($arListaProcesso);

    $arProcesso = explode("/",$request->get("stChaveProcesso"));

    if ($inCount > 0 ) {
        foreach ($arListaProcesso as $arAux) {
            if ( ($arAux['cod_processo'] == $arProcesso[0]) && ($arAux['ano_exercicio'] == $arProcesso[1]) ) {
                $stMensagem = 'Este processo já consta na lista!';
                die;
            }
        }
    } 

    if ($request->get("stChaveProcesso") == "") {
        $stMensagem = 'Deve ser informado o processo para poder incluí-lo na lista!';
    }

    if ($stMensagem == "") {
        $obTProcesso = new TProcesso();
        $stFiltro = " AND SW_PROCESSO.cod_processo = ".$arProcesso[0]." \n";
        $stFiltro .= " AND SW_PROCESSO.ano_exercicio = '".$arProcesso[1]."' \n";
        $obTProcesso->recuperaProcessoAlteracao($rsProcessos, $stFiltro, $stOrdem, "");

        if ($rsProcessos->getNumLinhas() < 1) {
            $stMensagem = "Este processo não pode ser arquivado!";
        } else {
            $arLista = array();

            $arLista['inId']              = $inCount+1;
            $arLista['cod_processo']      = $rsProcessos->getCampo('cod_processo');
            $arLista['ano_exercicio']     = $rsProcessos->getCampo('ano_exercicio');
            $arLista['nom_cgm']           = $rsProcessos->getCampo('nom_cgm');
            $arLista['nom_classificacao'] = $rsProcessos->getCampo('nom_classificacao');
            $arLista['nom_assunto']       = $rsProcessos->getCampo('nom_assunto');

            $arListaProcesso[] = $arLista;
            Sessao::write("arListaProcesso", $arListaProcesso);
        }
    }

    return $stMensagem;
}

switch ($stCtrl) {
    case 'incluirProcesso':
        $arListaProcesso = Sessao::read("arListaProcesso");

        $stMensagem = validaProcesso($request);

        if ($stMensagem == "") {            
            $stJs .= montaLista();
        } else {
            $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
        }

        $stJs .= "jq('#stChaveProcesso').val('');\n";
    break;

    case 'removerProcesso':
        $arListaProcesso = Sessao::read("arListaProcesso");
        $arLista = array();
        $indice = 0;

        foreach ($arListaProcesso as $i => $dado) {
            if ( $dado["inId"] != $request->get("id") ) {
                $arLista[$indice]["inId"]              = $dado["inId"];
                $arLista[$indice]["cod_processo"]      = $dado["cod_processo"];
                $arLista[$indice]["ano_exercicio"]     = $dado["ano_exercicio"];
                $arLista[$indice]["nom_cgm"]           = $dado["nom_cgm"];
                $arLista[$indice]["nom_classificacao"] = $dado["nom_classificacao"];
                $arLista[$indice]["nom_assunto"]       = $dado["nom_assunto"];
                $indice++;
            }
        }

        Sessao::write("arListaProcesso", $arLista);

        $stJs .= montaLista();
    break;
}

echo $stJs;

?>
