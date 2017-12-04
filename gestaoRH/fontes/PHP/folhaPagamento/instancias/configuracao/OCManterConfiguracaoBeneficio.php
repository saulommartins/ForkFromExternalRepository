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
    * Página de Oculto
    * Data de Criação   : 16/04/2007

    * @author Analista      Só Deus sabe
    * @author Desenvolvedor Yemanjá

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: OCManterConfiguracaoBeneficio.php 62044 2015-03-26 20:00:36Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoBeneficio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

function listaPlanos() {

    $obLista = new Lista;
    $rsPlanos = new RecordSet;
    $rsPlanos->preenche ( Sessao::read('arPlanos') );
    
    $obLista->setMostraPaginacao(false);
    $obLista->setRecordset( $rsPlanos );
    $obLista->setTitulo ( 'Lista de Planos de Saúde' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Fornecedor");
    $obLista->ultimoCabecalho->setWidth( 15 );
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
    $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[codigo] - [descricao]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delPlano');" );
    $obLista->ultimaAcao->addCampo("","&numcgm=[numcgm]&codigo=[codigo]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";
    
    $stJs .= "d.getElementById('inCGMFornecedor').value     = '';\n";
    $stJs .= "d.getElementById('stCGMFornecedor').innerHTML = '&nbsp;';\n";
    $stJs .= "d.getElementById('inCodigoEventoSaude').value = '';\n";
    $stJs .= "d.getElementById('stEventoSaude').innerHTML   = '&nbsp;';\n";

    return $stJs;
}

function carregaPlanos()
{

}

$arPlanos = Sessao::read('arPlanos');
$stJs = '';

switch ($stCtrl) {
    case 'incluirPlano':
        $stMensagem = '';
    
        if(is_array($arPlanos)) {
            foreach($arPlanos as $registro) {
                if($registro['codigo'] == $_REQUEST['inCodigoEventoSaude']) {
                    $stMensagem = 'Este evento já foi inserido';
                }
            }
        }
        
        if ($stMensagem == '') {
            $obTCGM = new TCGM();
            $obTCGM->setDado('numcgm', $_REQUEST['inCGMFornecedor']);
            $obTCGM->recuperaPorChave($rsCgm);
            
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
            $obTFolhaPagamentoEvento->recuperaEventos($rsEvento, " AND FPE.codigo = '".$_REQUEST['inCodigoEventoSaude']."'");

            $arRegistro['numcgm']     = $rsCgm->getCampo('numcgm');
            $arRegistro['nom_cgm' ]   = $rsCgm->getCampo('nom_cgm');
            $arRegistro['codigo'] = $rsEvento->getCampo('codigo');
            $arRegistro['descricao']  = $rsEvento->getCampo('descricao');

            $arPlanos[] = $arRegistro ;

            Sessao::write('arPlanos', $arPlanos);
            $stJs = listaPlanos();
        } else {
            $stJs.= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');\n";
        }

    break;

    case 'carregaPlanos':
        $rsPlanos = new RecordSet;
        $arPlanos = array();
        $arRegistro = array();
        $rsRegistro = new RecordSet;
        
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBeneficioEvento.class.php";
        $obTFolhaPagamentoBeneficioEvento = new TFolhaPagamentoBeneficioEvento;
        $obTFolhaPagamentoBeneficioEvento->recuperaRelacionamento($rsBeneficioEvento);
        
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoBeneficioFornecedor.class.php";
        $obTFolhaPagamentoConfiguracaoBeneficioFornecedor = new TFolhaPagamentoConfiguracaoBeneficioFornecedor;
        if ($rsBeneficioEvento->getNumLinhas() > 0) {
            $obTFolhaPagamentoConfiguracaoBeneficioFornecedor->recuperaRelacionamento($rsRegistro, " AND configuracao_beneficio.timestamp = '".$rsBeneficioEvento->getCampo('timestamp')."'");
        }

        while (!$rsRegistro->eof()) {
            $registro['numcgm']    = $rsRegistro->getCampo ( 'numcgm' );
            $registro['nom_cgm']   = $rsRegistro->getCampo ( 'nom_cgm' );
            $registro['codigo']    = $rsRegistro->getCampo ( 'codigo' );
            $registro['descricao'] = $rsRegistro->getCampo ( 'descricao' );
            $arPlanos[] = $registro ;
    
            $rsRegistro->proximo();
        }
        
        Sessao::write('arPlanos', $arPlanos);
        $stJs = listaPlanos();
    break;

    case 'delPlano':
        foreach ($arPlanos as $registro) {
            if ($registro['numcgm'].$registro['codigo'] != $_GET['numcgm'].$_GET['codigo']) {
                $arTempPlano[] = $registro;
            }
        }
        
        Sessao::write('arPlanos', $arTempPlano);
        $stJs = listaPlanos();
    break;
}

if ($stJs) {
    echo $stJs;
}
