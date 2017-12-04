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
    * Página de Oculto de Manter Configuração e-Sfinge
    * Data de Criação: 28/02/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id:$

    * Casos de uso: uc-02.08.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function configuracoesIniciais()
{
    include_once( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCESCMotivoLicencaEsfingeSw.class.php"  );

    $arConfiguracoesMotivosLicencas = array();
    $inId = 0;

    $obTExportacaoTCESCMotivoLicencaEsfingeSw = new TExportacaoTCESCMotivoLicencaEsfingeSw;
    $obTExportacaoTCESCMotivoLicencaEsfingeSw->recuperaRelacionamento( $rsMotivosLicencas );
    if ( $rsMotivosLicencas->getNumLinhas() > 0 ) {
    foreach ($rsMotivosLicencas->arElementos as $item) {
            $arConfiguracoesMotivosLicencas[$inId]['inId'] = $inId;
            $arConfiguracoesMotivosLicencas[$inId]['inMotivoLicensaEsfinge'] = $item['cod_motivo_licenca_esfinge'];
            $arConfiguracoesMotivosLicencas[$inId]['stMotivoLicensaEsfinge'] = $item['descricao_motivo_esfinge'];
            $arConfiguracoesMotivosLicencas[$inId]['inMotivoLicensaUrbem'] = $item['cod_assentamento'];
            $arConfiguracoesMotivosLicencas[$inId]['stMotivoLicensaUrbem'] = $item['descricao_motivo_urbem'];
            $inId++;
        }
    }

    Sessao::write('arConfiguracoesMotivosLicencas', $arConfiguracoesMotivosLicencas);
}

function montaSpnListaMotivosLicencas()
{
    $rsConfiguracoesMotivosLicencas = new RecordSet;
    $rsConfiguracoesMotivosLicencas->preenche( Sessao::read('configuracoesMotivosLicencas') );

    $obLista = new Lista;
    $obLista->setTitulo( "Lista de Motivos Licenças" );
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsConfiguracoesMotivosLicencas );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Cód. do e-Sfinge");
    $obLista->ultimoCabecalho->setWidth( 14 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Motivo da Licença do e-Sfinge");
    $obLista->ultimoCabecalho->setWidth( 31 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Cód. do Urbem");
    $obLista->ultimoCabecalho->setWidth( 14 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Motivo da Licença do Urbem");
    $obLista->ultimoCabecalho->setWidth( 31 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inMotivoLicensaEsfinge" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stMotivoLicensaEsfinge" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inMotivoLicensaUrbem" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stMotivoLicensaUrbem" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax( 'excluirConfiguracaoMotivosLicencas' );" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "document.getElementById('spnListaMotivosLicensas').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function incluirConfiguracaoMotivosLicencas()
{
    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php"  );
    include_once( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCESCMotivoLicencaEsfinge.class.php"  );

    $obTExportacaoTCESCMotivoLicencaEsfinge = new TExportacaoTCESCMotivoLicencaEsfinge;
    $obTExportacaoTCESCMotivoLicencaEsfinge->setDado( 'cod_motivo_licenca_esfinge', $_GET['inMotivoLicensaEsfinge'] );
    $obTExportacaoTCESCMotivoLicencaEsfinge->recuperaPorChave( $rsMotivoLicencaEsfinge );
    $stMotivoLicencaEsfinge = $rsMotivoLicencaEsfinge->getCampo('descricao');

    $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento;
    $obTPessoalAssentamentoAssentamento->setDado( 'cod_assentamento', $_GET['inMotivoLicensaUrbem'] );
    $obTPessoalAssentamentoAssentamento->recuperaPorChave( $rsMotivoLicencaUrbem );
    $stMotivoLicencaoUrbem = $rsMotivoLicencaUrbem->getCampo('descricao');

    $boValida = true;

    //Valida a inclusão da configuração de certidão
    $arConfiguracoesMotivosLicencas = Sessao::read('configuracoesMotivosLicencas');
    foreach ($arConfiguracoesMotivosLicencas as $MotivoLicenca) {
        //valida se já existe um igual
        if ($MotivoLicenca['inMotivoLicensaEsfinge'] == $_GET['inMotivoLicensaEsfinge'] && $MotivoLicenca['inMotivoLicensaUrbem'] == $_GET['inMotivoLicensaUrbem']) {
            $boValida = false;
            $stJs .= "alertaAviso('Já existe esta configuração de motivo da licença. ( \'$stMotivoLicencaEsfinge\'  e \'$stMotivoLicencaoUrbem\' )','form','erro','".Sessao::getId()."');\n";
        }
    }

    if ($boValida) {

        $inId = count( $arConfiguracoesMotivosLicencas );

        $arConfiguracoesMotivosLicencas[$inId]['inId']                   = $inId;
        $arConfiguracoesMotivosLicencas[$inId]['inMotivoLicensaEsfinge'] = $_GET['inMotivoLicensaEsfinge'];
        $arConfiguracoesMotivosLicencas[$inId]['stMotivoLicensaEsfinge'] = trim($stMotivoLicencaEsfinge);
        $arConfiguracoesMotivosLicencas[$inId]['inMotivoLicensaUrbem'] = $_GET['inMotivoLicensaUrbem'];
        $arConfiguracoesMotivosLicencas[$inId]['stMotivoLicensaUrbem'] = trim($stMotivoLicencaoUrbem);

        $stJs .= "document.getElementById('inMotivoLicensaEsfinge').value = '';";
        $stJs .= "document.getElementById('inMotivoLicensaUrbem').value = '';";
    }

    Sessao::write('arConfiguracoesMotivosLicencas', $arConfiguracoesMotivosLicencas);

    return $stJs;
}

function excluirConfiguracaoMotivosLicencas($inIdExcluir)
{
    $arNovoMotivoLicenca = array();

    $inId = 0;
    $arConfiguracoesMotivosLicencas = Sessao::read('configuracoesMotivosLicencas');
    foreach ($arConfiguracoesMotivosLicencas as $MotivoLicenca) {
        if ($MotivoLicenca['inId'] != $inIdExcluir) {
            $arNovoMotivoLicenca[$inId]['inId'] = $inId;
            $arNovoMotivoLicenca[$inId]['inMotivoLicensaEsfinge'] = $MotivoLicenca['inMotivoLicensaEsfinge'];
            $arNovoMotivoLicenca[$inId]['stMotivoLicensaEsfinge'] = $MotivoLicenca['stMotivoLicensaEsfinge'];
            $arNovoMotivoLicenca[$inId]['inMotivoLicensaUrbem'] = $MotivoLicenca['inMotivoLicensaUrbem'];
            $arNovoMotivoLicenca[$inId]['stMotivoLicensaUrbem'] = $MotivoLicenca['stMotivoLicensaUrbem'];

            $inId++;
        }
    }

    Sessao::write('configuracoesMotivosLicencas', $arNovoMotivoLicenca);
    $stJs = montaSpnListaMotivosLicencas();

    return $stJs;
}

switch ($stCtrl) {
    case "configuracoesIniciais":
        configuracoesIniciais();
        $js = montaSpnListaMotivosLicencas();
    break;
    case "incluirConfiguracaoMotivosLicencas":
        $js  = incluirConfiguracaoMotivosLicencas();
        $js .= montaSpnListaMotivosLicencas();
    break;
    case "excluirConfiguracaoMotivosLicencas":
        $js  = excluirConfiguracaoMotivosLicencas( $_GET['inId'] );
    break;
}

if ($js) {
    echo $js;
}
?>
