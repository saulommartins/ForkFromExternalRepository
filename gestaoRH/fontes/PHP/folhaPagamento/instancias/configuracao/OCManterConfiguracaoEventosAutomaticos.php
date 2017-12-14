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
/*
    * Página de Oculto da Configuração dos Eventos Automáticos
    * Data de Criação: 06/11/2015

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Jean da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: OCManterConfiguracaoBeneficio.php 62044 2015-03-26 20:00:36Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php" );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoEventosAutomaticos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $request->get('stCtrl');

function listaEventos() {

    $obLista = new Lista;
    $rsEventos = new RecordSet;
    $rsEventos->preenche ( Sessao::read('arEventos') );

    $obLista->setMostraPaginacao(false);
    $obLista->setRecordset( $rsEventos );
    $obLista->setTitulo ( 'Lista de Eventos para Lançamento Automático' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[codigo] - [descricao]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('deletarEvento');" );
    $obLista->ultimaAcao->addCampo("","&codigo=[codigo]&cod_evento=[cod_evento]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "jq('#spnLista').html('');\n";
    $stJs .= "jq('#spnLista').html('".$html."');\n";
    
    $stJs .= "jq('#inCodigoEvento').val('');\n";
    $stJs .= "jq('#stEvento').html('&nbsp;');\n";
    $stJs .= "jq('#stTextoComplementar').html('&nbsp;');\n";

    return $stJs;
}

$arEventos = Sessao::read('arEventos');
$arListaEventos = array();
$stJs = '';

switch ($stCtrl) {
    case 'incluirEvento':
        $stMensagem = '';
        if(is_array($arEventos)) {
            foreach($arEventos as $registro) {
                if($registro['codigo'] == $request->get('inCodigoEvento')) {
                    $stMensagem = 'Este evento já foi inserido';
                    break;
                }
            }
        }

        if ($stMensagem != '') {
            $stJs.= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');\n";
        } else {
            $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
            $obRFolhaPagamentoEvento->setCodigo($request->get('inCodigoEvento'));
            $obRFolhaPagamentoEvento->listarEvento($rsEventos);

            $arListaEventos['cod_evento'] = $rsEventos->getCampo('cod_evento');
            $arListaEventos['codigo'] = $rsEventos->getCampo('codigo');
            $arListaEventos['descricao'] = $rsEventos->getCampo('descricao');
            $arEventos[] = $arListaEventos;

            Sessao::write('arEventos', $arEventos);
            $stJs = listaEventos();
        }
    break;

    case 'carregaEventos':
        $rsEventosAutomaticos = new RecordSet;
        $arEventosAutomaticos = array();
        $arRegistro = array();
        $arRegistroSessao = array();

        $stResultado = SistemaLegado::pegaConfiguracao("evento_automatico", 27, Sessao::getExercicio());

        if ($stResultado != "") {
            $arEventosAutomaticos = explode(",", $stResultado);

            $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();

            foreach ($arEventosAutomaticos as $registro) {
                $obRFolhaPagamentoEvento->setCodEvento($registro);
                $obRFolhaPagamentoEvento->listarEvento($rsRegistro);
                $arRegistro['codigo'] = $rsRegistro->getCampo('codigo');
                $arRegistro['cod_evento'] = $rsRegistro->getCampo('cod_evento');
                $arRegistro['descricao'] = $rsRegistro->getCampo('descricao');
                $arRegistroSessao[] = $arRegistro;
            }

            Sessao::write("arEventos", $arRegistroSessao);
        }
        
        $stJs = listaEventos();
        
    break;
    
    case 'deletarEvento':
        foreach ($arEventos as $registro) {
            if ($registro['codigo'].$registro['cod_evento'] != $request->get('codigo').$request->get('cod_evento')) {
                $arTempEvento[] = $registro;
            }
        }
        
        Sessao::write('arEventos', $arTempEvento);
        $stJs = listaEventos();
    break;
    
}

if ($stJs) {
    echo $stJs;
}
