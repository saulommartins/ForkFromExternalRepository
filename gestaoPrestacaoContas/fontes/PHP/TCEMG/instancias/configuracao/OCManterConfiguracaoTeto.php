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
  * Página Oculta do Formulario de Configurar Teto Remuneratório
  * Data de Criação: 01/04/2016

  * @author Analista:      Dagiane Vieira
  * @author Desenvolvedor: Michel Teixeira
  *
  * @ignore
  * $Id: OCManterConfiguracaoTeto.php 65298 2016-05-10 18:53:52Z jean $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaTeto() {
    $rsRecordSet = new RecordSet();

    if (Sessao::read('arListaTetos') != '') {
        $rsRecordSet->preenche(Sessao::read('arListaTetos'));
        $rsRecordSet->ordena('data_ordena');
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Tetos Remuneratórios" );

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Vigência" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor Teto Remuneratório" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Justificativa" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ações");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vigencia" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "teto" );
    $obLista->ultimoDado->setAlinhamento('DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "justificativa" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarTeto');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirTeto');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n",   "",$stHtml);
    $stHtml = str_replace("  ",   "",$stHtml);
    $stHtml = str_replace("'" ,"\\'",$stHtml);
    $stJs .= "d.getElementById('spnListaTetos').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function incluirTeto(Request $request) {
    $obErro  = new Erro();

    $inId = $request->get('inIdTeto');
    $vlTeto = $request->get('vlTeto');
    $dtVigencia = $request->get('dtVigencia');
    $stJustificativa = $request->get('stJustificativa');
    $inCodigoEvento = $request->get('inCodigoEvento');

    if ( empty($dtVigencia) )
        $obErro->setDescricao("Informe o campo Vigência!");
        
    if ( empty($vlTeto) || $vlTeto == 0)
        $obErro->setDescricao("Informe o campo Teto Remuneratório!");

    if (!$obErro->ocorreu()) {
        $arListaTetos = Sessao::read('arListaTetos');

        foreach ($arListaTetos as $teto) {
            if ($teto['vigencia'] == $dtVigencia && $teto['inId'] != $inId) {
                $obErro->setDescricao("Esta Vigência já está na lista!");
                break;
            }
        }

        if (!$obErro->ocorreu()) {
            include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php';

            //INCLUIR
            if($inId == ''){
                $stTipo = 'incluído';
                $arTeto = array();
                $arTeto['exercicio']     = Sessao::getExercicio();
                $arTeto['cod_entidade']  = $request->get('hdnCodEntidade');
                $arTeto['vigencia']      = $dtVigencia;
                $arTeto['teto']          = $vlTeto;
                $arTeto['justificativa'] = $stJustificativa;
                $arTeto['cod_evento']    = $inCodigoEvento;

                $stDataOrdena = explode('/', $dtVigencia);
                $arTeto['data_ordena']   = $stDataOrdena[2].$stDataOrdena[1].$stDataOrdena[0];
                $arTeto['inId']          = count($arListaTetos);

                $stDescricao = "&nbsp;";
                if ($inCodigoEvento != "") {
                    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
                    $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, " WHERE codigo::INTEGER = ".$inCodigoEvento);

                    if ($rsEvento->getNumLinhas() > 0)
                     $stDescricao = $rsEvento->getCampo("descricao");
                }
                $arTeto['nom_evento']    = $stDescricao;

                $arListaTetos[] = $arTeto;
            }else{
                $stTipo = 'alterado';
                foreach ($arListaTetos as $key => $teto) {
                    if ($teto['inId'] == $inId) {
                        $arListaTetos[$key]['exercicio']     = Sessao::getExercicio();
                        $arListaTetos[$key]['vigencia']      = $dtVigencia;
                        $arListaTetos[$key]['teto']          = $vlTeto;
                        $arListaTetos[$key]['justificativa'] = $stJustificativa;
                        $arListaTetos[$key]['cod_evento']    = $inCodigoEvento;

                        $stDataOrdena = explode('/', $dtVigencia);
                        $arListaTetos[$key]['data_ordena']   = $stDataOrdena[2].$stDataOrdena[1].$stDataOrdena[0];

                        $stDescricao = "&nbsp;";
                        if ($inCodigoEvento != "") {
                            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
                            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, " WHERE codigo::INTEGER = ".$inCodigoEvento);

                            if ($rsEvento->getNumLinhas() > 0)
                             $stDescricao = $rsEvento->getCampo("descricao");
                        }
                        $arListaTetos[$key]['nom_evento']    = $stDescricao;

                        break;
                    }
                }
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        Sessao::write('arListaTetos', $arListaTetos);

        $stJs .= "alertaAviso('Teto Remuneratório ".$stTipo.".','form','erro','".Sessao::getId()."');\n";
        $stJs .= montaTeto();
        $stJs .= limparFormTeto();
    }

    return $stJs;
}

function alterarTeto(Request $request) {
    $arListaTetos = Sessao::read('arListaTetos');

    foreach ($arListaTetos as $key => $teto) {
        if ($teto['inId'] == $request->get('inId')) {
            $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                \n";

            $stJs .= "jQuery('#btnIncluir').val('Alterar');                                     \n";

            $stJs .= "jQuery('#inIdTeto').val('".$teto['inId']."');                             \n";
            $stJs .= "jQuery('#vlTeto').val('".$teto['teto']."');                               \n";
            $stJs .= "jQuery('#dtVigencia').val('".$teto['vigencia']."');                       \n";
            $stJs .= "jQuery('#stJustificativa').val('".$teto['justificativa']."');             \n";
            $stJs .= "jQuery('#inCodigoEvento').val('".$teto['cod_evento']."');                 \n";
            $stJs .= "jQuery('#stEvento').html('".$teto['nom_evento']."');                      \n";
            $stJs .= "jQuery('#HdninCodigoEvento').val('".$teto['cod_evento']."');              \n";
            $stJs .= "jQuery('#hdnDescEvento').val('".$teto['nom_evento']."');                  \n";
            $stJs .= "jQuery('#vlTeto').focus();                                                \n";
            $stJs .= "jQuery('#dtVigencia').attr('readonly', true);                             \n";

            break;
        }
    }

    SistemaLegado::executaFrameOculto($stJs);
}

function excluirTeto(Request $request) {
    $arTemp = array();
    $arDelete = array();
    $arListaTetos = Sessao::read('arListaTetos');

    foreach ($arListaTetos as $key => $teto) {
        if ($teto['inId'] != $request->get('inId')) {
            $arTemp[] = $teto;
        } else {
            $arDelete[] = $teto;
        }
    }

    Sessao::write('arListaTetos', $arTemp);
    Sessao::write('arListaTetosDelete', $arDelete);

    $stJs  = "alertaAviso('Teto Remuneratório Removido.','form','erro','".Sessao::getId()."');\n";
    $stJs .= montaTeto();
    $stJs .= limparFormTeto();

    SistemaLegado::executaFrameOculto($stJs);
}

function limparFormTeto() {
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery; \n";

    $stJs .= "jQuery('#btnIncluir').val('Incluir');          \n";
    $stJs .= "jQuery('#inIdTeto').val('');                   \n";
    $stJs .= "jQuery('#vlTeto').val('0,00');                 \n";
    $stJs .= "jQuery('#dtVigencia').val('');                 \n";
    $stJs .= "jQuery('#stJustificativa').val('');            \n";
    $stJs .= "jQuery('#inCodigoEvento').val('');             \n";
    $stJs .= "jQuery('#stEvento').html('&nbsp;');            \n";
    $stJs .= "jQuery('#HdninCodigoEvento').val('');          \n";
    $stJs .= "jQuery('#hdnDescEvento').val('');              \n";
    $stJs .= "jQuery('#dtVigencia').attr('readonly', false); \n";

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case 'carregaDados':
        include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTetoRemuneratorio.class.php";
        include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php';

        $obTTCEMGTetoRemuneratorio = new TTCEMGTetoRemuneratorio();
        $obTTCEMGTetoRemuneratorio->setDado("cod_entidade", $request->get('inCodEntidade'));
        $obTTCEMGTetoRemuneratorio->recuperaPorChave($rsTeto);

        $arListaTetos = array();

        foreach($rsTeto->getElementos() as $teto) {
            $stDataOrdena = explode('/', $teto['vigencia']);
            $teto['data_ordena'] = $stDataOrdena[2].$stDataOrdena[1].$stDataOrdena[0];
            $teto['teto'] = number_format ( $teto['teto'], 2, ",", "." );
            $teto['inId'] = count($arListaTetos);

            $stDescricao = "&nbsp;";
            if ($teto['cod_evento'] != "") {
                $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
                $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, " WHERE codigo::INTEGER = ".$teto['cod_evento']);

                if ($rsEvento->getNumLinhas() > 0)
                    $stDescricao = $rsEvento->getCampo("descricao");
            }
            $teto['nom_evento'] = $stDescricao;

            $arListaTetos[] = $teto;
        }

        Sessao::write('arListaTetos', $arListaTetos);

        $stJs = montaTeto();
    break;

    case 'incluirTeto':
        $stJs .= incluirTeto($request);
    break;

    case 'alterarTeto':
        $stJs .= alterarTeto($request);
    break;

    case 'excluirTeto':
        $stJs .= excluirTeto($request);
    break;

    case 'limparFormTeto':
        $stJs .= limparFormTeto();
    break;
}

if (isset($stJs))
   echo $stJs;
