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

    $Revision: 59612 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-03-05 15:47:13 -0300 (Seg, 05 Mar 2007) $

    * Casos de uso: uc-02.08.17
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function configuracoesIniciais()
{
    include_once( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCESCTipoCertidaoEsfinge.class.php"  );
    global $sessao;

    $sessao->transf['configuracoesCertidoes'] = array();
    $inId = 0;

    $obTExportacaoTCESCTipoCertidaoEsfinge = new TExportacaoTCESCTipoCertidaoEsfinge;
    $obTExportacaoTCESCTipoCertidaoEsfinge->recuperaRelacionamento( $rsCertidoes );
    if ( $rsCertidoes->getNumLinhas() > 0 ) {
        foreach ($rsCertidoes->arElementos as $item) {
            $sessao->transf['configuracoesCertidoes'][$inId]['inId'] = $inId;
            $sessao->transf['configuracoesCertidoes'][$inId]['inTipoEsfinge'] = $item['cod_tipo_certidao'];
            $sessao->transf['configuracoesCertidoes'][$inId]['stTipoEsfinge'] = $item['descricao'];
            $sessao->transf['configuracoesCertidoes'][$inId]['inTipoUrbem'] = $item['cod_documento'];
            $sessao->transf['configuracoesCertidoes'][$inId]['stTipoUrbem'] = $item['nom_documento'];

            $inId++;
        }
    }

}

function montaSpnListaCertidoes()
{
    global $sessao;

    $rsConfiguracoesCertidoes = new RecordSet;
    $rsConfiguracoesCertidoes->preenche( $sessao->transf['configuracoesCertidoes'] );

    $obLista = new Lista;
    $obLista->setTitulo( "Lista de Certidões" );
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsConfiguracoesCertidoes );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Cód. do e-Sfinge");
    $obLista->ultimoCabecalho->setWidth( 14 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Certidão do e-Sfinge");
    $obLista->ultimoCabecalho->setWidth( 31 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Cód. do Urbem");
    $obLista->ultimoCabecalho->setWidth( 14 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Certidão do Urbem");
    $obLista->ultimoCabecalho->setWidth( 31 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inTipoEsfinge" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stTipoEsfinge" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inTipoUrbem" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stTipoUrbem" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax( 'excluirConfiguracaoCertidao' );" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "document.getElementById('spnListaCertidoes').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function incluirConfiguracaoCertidao()
{
    include_once( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCESCTipoCertidao.class.php"  );
    include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumento.class.php"  );
    global $sessao;

    $obTExportacaoTCESCTipoCertidao = new TExportacaoTCESCTipoCertidao;
    $obTExportacaoTCESCTipoCertidao->setDado( 'cod_tipo_certidao', $_GET['inTipoEsfinge'] );
    $obTExportacaoTCESCTipoCertidao->recuperaPorChave( $rsTipoCertidaoEsfinge );
    $stTipoEsfinge = $rsTipoCertidaoEsfinge->getCampo('descricao');

    $obTLicitacaoDocumento = new TLicitacaoDocumento;
    $obTLicitacaoDocumento->setDado( 'cod_documento', $_GET['inTipoUrbem'] );
    $obTLicitacaoDocumento->recuperaPorChave( $rsTipoCertidaoUrbem );
    $stTipoUrbem = $rsTipoCertidaoUrbem->getCampo('nom_documento');

    $boValida = true;

    //Valida a inclusão da configuração de certidão
    foreach ($sessao->transf['configuracoesCertidoes'] as $Certidao) {
        //valida se já existe um igual
        if ($Certidao['inTipoEsfinge'] == $_GET['inTipoEsfinge'] && $Certidao['inTipoUrbem'] = $_GET['inTipoUrbem']) {
            $boValida = false;
            $stJs .= "alertaAviso('Já existe esta configuração de tipo de certidão. ( \'$stTipoEsfinge\'  e \'$stTipoUrbem\' )','form','erro','".$sessao->id."');\n";
        }
    }

    if ($boValida) {

        $inId = count( $sessao->transf['configuracoesCertidoes'] );

        $sessao->transf['configuracoesCertidoes'][$inId]['inId']          = $inId;
        $sessao->transf['configuracoesCertidoes'][$inId]['inTipoEsfinge'] = $_GET['inTipoEsfinge'];
        $sessao->transf['configuracoesCertidoes'][$inId]['stTipoEsfinge'] = trim($stTipoEsfinge);
        $sessao->transf['configuracoesCertidoes'][$inId]['inTipoUrbem'] = $_GET['inTipoUrbem'];
        $sessao->transf['configuracoesCertidoes'][$inId]['stTipoUrbem'] = trim($stTipoUrbem);

        $stJs .= "document.getElementById('inTipoEsfinge').value = '';";
        $stJs .= "document.getElementById('inTipoUrbem').value = '';";
    }

    return $stJs;
}

function excluirConfiguracaoCertidao($inIdExcluir)
{
    global $sessao;

    $arNovoCertidoes = array();

    $inId = 0;

    foreach ($sessao->transf['configuracoesCertidoes'] as $Certidao) {
    if ($Certidao['inId'] != $inIdExcluir) {
            $arNovoCertidoes[$inId]['inId'] = $inId;
            $arNovoCertidoes[$inId]['inTipoEsfinge'] = $Certidao['inTipoEsfinge'];
            $arNovoCertidoes[$inId]['stTipoEsfinge'] = $Certidao['stTipoEsfinge'];
            $arNovoCertidoes[$inId]['inTipoUrbem'] = $Certidao['inTipoUrbem'];
            $arNovoCertidoes[$inId]['stTipoUrbem'] = $Certidao['stTipoUrbem'];

            $inId++;
        }
    }

    unset( $sessao->transf['configuracoesCertidoes'] );
    $sessao->transf['configuracoesCertidoes'] = $arNovoCertidoes;
    unset( $arNovoCertidoes );

    $stJs = montaSpnListaCertidoes();

    return $stJs;
}

switch ($stCtrl) {
    case "configuracoesIniciais":
        configuracoesIniciais();
        $js = montaSpnListaCertidoes();
    break;
    case "incluirConfiguracaoCertidao":
        $js  = incluirConfiguracaoCertidao();
        $js .= montaSpnListaCertidoes();
    break;
    case "excluirConfiguracaoCertidao":
        $js  = excluirConfiguracaoCertidao( $_GET['inId'] );
    break;
}

if ($js) {
    echo $js;
}
?>
