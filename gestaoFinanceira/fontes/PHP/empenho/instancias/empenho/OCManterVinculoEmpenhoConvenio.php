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
    * Arquivo oculto do vínculo Convênio-Empenho.
    * Data de Criação: 17/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.38

    $Id: OCManterVinculoEmpenhoConvenio.php 63231 2015-08-05 21:10:01Z carlos.silva $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" 									 );
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoConvenio.class.php" 							  );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

switch ($stCtrl) {
    case "consultaEmpenhoConvenio":

        $arElementos = array();

        Sessao::remove('elementos');
        Sessao::remove('elementos_excluidos');
        $arConvenio = array();
        $arConvenio = Sessao::read('convenio');
        $numConvenio = $arConvenio['num_convenio'];
        $stExercicio = $arConvenio['exercicio'];

        include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoConvenio.class.php" );
        $obTEmpenhoEmpenhoConvenio = new TEmpenhoEmpenhoConvenio();
        $stFiltro .= "   AND ec.exercicio    = '".$stExercicio."'";
        $stFiltro .= "   AND ec.num_convenio =  ".$numConvenio;
        $stOrder   = " ORDER BY nom_credor ";
        $obTEmpenhoEmpenhoConvenio->recuperaConvenioEmpenhoItem($rsRecordset, $stFiltro, $stOrder);

        if ( $rsRecordset->getNumLinhas() > 0 ) {
            $arTmpElementos = array();
            while ( !$rsRecordset->eof() ) {
                $arElementos['inId']		 = $rsRecordset->getCampo('cod_empenho');
                $arElementos['cod_empenho']  = $rsRecordset->getCampo('cod_empenho');
                $arElementos['cod_entidade'] = $rsRecordset->getCampo('cod_entidade');
                $arElementos['exercicio']    = $rsRecordset->getCampo('exercicio');
                $arElementos['dt_empenho']   = $rsRecordset->getCampo('dt_empenho');
                $arElementos['nom_credor']   = $rsRecordset->getCampo('nom_credor');
                $arElementos['vl_total']  	 = number_format($rsRecordset->getCampo('vl_total'), 2,',','.');

                $arTmpElementos[] = $arElementos;
                $rsRecordset->proximo();
            }
            Sessao::write('elementos', $arTmpElementos);
            $stJs .= listarEmpenho();
        }
    echo $stJs;
    break;

case "incluirEmpenho":

    $rsEmpenhos  = new RecordSet;
    $arElementos = array();

    $arEmpenho = explode('/', $request->get('inCodEmpenho'));
    $inCodEmpenho = $arEmpenho[0];
    $stExercicioEmpenho = $arEmpenho[1];
    $inCodEntidade = $request->get('inCodEntidade');
    if ($inCodEmpenho != "") {

        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $stFiltro .= " AND e.cod_entidade = ".$inCodEntidade;
        $stFiltro .= " AND e.cod_empenho = ".$inCodEmpenho;
        $stFiltro .= " AND e.exercicio = '".$stExercicioEmpenho."'";
        $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordSet, $stFiltro);

        if ($rsRecordSet->getNumLinhas() > 0) {

            $boInserir = false;

            // verifica se o item inserido ja não esta nos elementos excluidos, caso esteja, quer dizer que o convenio ja possuia o item, porem foi excluido e
            // que a pessoa quer inseri-lo novamente
            $arElementosExcluidos = Sessao::read('elementos_excluidos');
            if (is_array($arElementosExcluidos)) {
                foreach ($arElementosExcluidos as $arExcluidos) {
                    if ($arExcluidos['cod_empenho'] ==  $inCodEmpenho &&
                        $arExcluidos['exercicio']	==  $request->get('stExercicio') &&
                        $arExcluidos['cod_entidade']==  $inCodEntidade) {
                        
                        $boInserir = true;
                        break;
                    }
                }
            }
            // se o elemento não esta no array de excluidos, então procura ele no banco de dados, se não achar quer dizer que pode inseri-lo na listagem
            if (!$boInserir) {
                $obEmpenhoEmpenhoConvenio = new TEmpenhoEmpenhoConvenio;
                $obEmpenhoEmpenhoConvenio->setDado('exercicio', $request->get('stExercicio'));
                $obEmpenhoEmpenhoConvenio->setDado('cod_entidade', $inCodEntidade);
                $obEmpenhoEmpenhoConvenio->setDado('cod_empenho', $inCodEmpenho);
                $obEmpenhoEmpenhoConvenio->recuperaPorChave($rsEmpenhoConvenio);

                if ($rsEmpenhoConvenio->getNumLinhas() < 1 ) {
                    $boInserir = true;
                }
            }

            if ($boInserir) {
                $arElementosSessao = Sessao::read('elementos');
                if ($arElementosSessao != "") {
                    $rsEmpenhos->preenche($arElementosSessao);
                    while (!$rsEmpenhos->eof()) {
                        $cod_empenho = $rsRecordSet->getCampo('cod_empenho');
                        if ( $rsRecordSet->getCampo('cod_empenho') == $rsEmpenhos->getCampo('cod_empenho') &&
                             $rsRecordSet->getCampo('exercicio') == $rsEmpenhos->getCampo('exercicio') &&
                             $rsRecordSet->getCampo('cod_entidade') == $rsEmpenhos->getCampo('cod_entidade')) {
                            
                            $boExecuta = true;
                            $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                        }
                        $rsEmpenhos->proximo();
                    }
                }
                
                if (!$boExecuta) {
                    $arElementosTmp = array();
                    while ( !$rsRecordSet->eof() ) {
                        $arElementos['inId']				= $rsRecordSet->getCampo('cod_empenho');
                        $arElementos['cod_empenho'] 		= $rsRecordSet->getCampo('cod_empenho');
                        $arElementos['exercicio']   		= $rsRecordSet->getCampo('exercicio');
                        $arElementos['cod_entidade']		= $rsRecordSet->getCampo('cod_entidade');
                        $arElementos['dt_empenho']  		= $rsRecordSet->getCampo('dt_empenho');
                        $arElementos['nom_credor']			= $rsRecordSet->getCampo('credor');
                        $arElementos['vl_total']  			= number_format($rsRecordSet->getCampo('vl_saldo_anterior'), 2,',','.');
                        $arElementosSessao[] = $arElementos;

                        $rsRecordSet->proximo();
                    }
                    Sessao::write('elementos', $arElementosSessao);
                }
                $stJs .= listarEmpenho();
            } else {
                $stJs .= "alertaAviso('O empenho ".$rsEmpenhoConvenio->getCampo('cod_empenho')."/".$rsEmpenhoConvenio->getCampo('exercicio')." já está vinculado ao convênio ".$rsEmpenhoConvenio->getCampo('num_convenio')."/".$rsEmpenhoConvenio->getCampo('exercicio')."','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('Empenho inexistente! ','form','erro','".Sessao::getId()."');";
        }
    } else {
        $stJs .= "alertaAviso('Informe o código de empenho.','form','erro','".Sessao::getId()."');";
    }

    $stJs .= "$('inCodEmpenho').value = '';";

    echo $stJs;
    break;

case "excluirEmpenho":

    $arElementos = array();
    $arExcluidos = array();

    $inCount  = 0;
    $inCount2 = 0;
    $arElementosSessao = Sessao::read('elementos');

    foreach ($arElementosSessao as $key => $value) {
        if ($value["exercicio"] == $request->get('exercicio') &&
            $value["cod_entidade"] == $request->get('cod_entidade') &&
            $value["cod_empenho"] == $request->get('cod_empenho')) {
            
            $arExcluidos[$inCount2]['inId']         = $value["cod_empenho"];
            $arExcluidos[$inCount2]['cod_empenho']  = $value["cod_empenho"];
            $arExcluidos[$inCount2]['exercicio']	= $value["exercicio"];
            $arExcluidos[$inCount2]['cod_entidade'] = $value["cod_entidade"];
            $inCount2++;

        } else {
            $arElementos[$inCount]['inId']         = $value["cod_empenho"];
            $arElementos[$inCount]['cod_empenho']  = $value["cod_empenho"];
            $arElementos[$inCount]['exercicio']	   = $value["exercicio"];
            $arElementos[$inCount]['cod_entidade'] = $value["cod_entidade"];
            $arElementos[$inCount]['dt_empenho']   = $value["dt_empenho"];
            $arElementos[$inCount]['vl_total']	   = $value["vl_total"];
            $arElementos[$inCount]['nom_credor']   = $value["nom_credor"];
            $inCount++;
        }
    }

    Sessao::write('elementos', $arElementos);
    Sessao::write('elementos_excluidos', $arExcluidos);

    $stJs .= listarEmpenho();
    echo $stJs;
    break;

case "detalharConvenio":

    include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoParticipanteConvenio.class.php" );
    $obTLicitacaoParticipanteConvenio = new TLicitacaoParticipanteConvenio();
    $obTLicitacaoParticipanteConvenio->setDado('num_convenio', $request->get('num_convenio'));
    $obTLicitacaoParticipanteConvenio->setDado('exercicio', $request->get('exercicio'));
    $stOrder   = " ORDER BY sw_cgm.nom_cgm ";
    $obTLicitacaoParticipanteConvenio->recuperaParticipanteConvenio($rsRecordset, "", $stOrder);

    $table = new Table();
    $table->setRecordset($rsRecordset);
    //$table->setConditional( true , "#efefef" );
    $table->Head->addCabecalho( 'Participantes' , 100  );
    $table->Body->addCampo( '[cgm_fornecedor] - [nom_cgm]' , 'E');
    $table->Head->addCabecalho( '' , 100  );
    $table->montaHTML();

    echo $table->getHTML();

    break;

case "limpar":
    $stJs .= "$('inCodEmpenho').value = '';";

    echo $stJs;
    break;

}

function listarEmpenho()
{
    $rsRecordSet = new Recordset;

    if ( Sessao::read('elementos') != "" ) {
        $rsRecordSet->preenche(Sessao::read('elementos'));
    }

    if ( $rsRecordSet->getNumLinhas() > 0 ) {

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Empenhos do Convênio" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Empenho" );
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Emissão" );
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Credor" );
        $obLista->ultimoCabecalho->setWidth( 68 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
        $obLista->ultimoDado->setAlinhamento('DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[dt_empenho]" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[nom_credor]" );
        $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vl_total]" );
        $obLista->ultimoDado->setAlinhamento('DIREITA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "Excluir" );
        $obLista->ultimaAcao->setFuncaoAjax( true );

        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirEmpenho');" );
        $obLista->ultimaAcao->addCampo("1","exercicio");
        $obLista->ultimaAcao->addCampo("2","cod_entidade");
        $obLista->ultimaAcao->addCampo("3","cod_empenho");
        $obLista->commitAcao();

        $obLista->montaInnerHtml();
        $stHtml = $obLista->getHTML();

    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnListaEmpenhos').innerHTML = '".$stHtml."';";
    echo $stJs;
}

?>
