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

    * Casos de uso: uc-02.08.18

    $Id: OCManterAssinaturas.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

function configuracoesIniciais()
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinatura.class.php"  );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinaturaModulo.class.php"  );

    $arSessaoAssinaturas = array();
    $inId = 0;

    $obTAssinatura = new TAdministracaoAssinatura;
    $obTAssinatura->recuperaRelacionamento($rsAssinaturas, '', 'cod_entidade, nom_cgm');
    while ( !$rsAssinaturas->eof() ) {
       $arSessaoAssinaturas[$inId]['inId'] = $inId;
       $arSessaoAssinaturas[$inId]['inCodEntidade'] = $rsAssinaturas->getCampo('cod_entidade');
       $arSessaoAssinaturas[$inId]['inCGM'] = $rsAssinaturas->getCampo('numcgm');
       $arSessaoAssinaturas[$inId]['stNomCGM'] = $rsAssinaturas->getCampo('nom_cgm');
       $arSessaoAssinaturas[$inId]['stCargo'] = $rsAssinaturas->getCampo('cargo');
       $arSessaoAssinaturas[$inId]['stCRC'] = $rsAssinaturas->getCampo('insc_crc');
       $arCodModulos = array();
       $obTAssinaturaModulo = new TAdministracaoAssinaturaModulo;
       $obTAssinaturaModulo->setDado ('cod_entidade', $rsAssinaturas->getCampo('cod_entidade') );
       $obTAssinaturaModulo->setDado ('numcgm', $rsAssinaturas->getCampo('numcgm') );
       $obTAssinaturaModulo->setDado ('timestamp', $rsAssinaturas->getCampo('timestamp') );
       $obTAssinaturaModulo->recuperaModulosPorAssinatura( $rsAssinaturaModulos );
       while ( !$rsAssinaturaModulos->eof() ) {
          $arCodModulos[] = $rsAssinaturaModulos->getCampo('cod_modulo');
          $rsAssinaturaModulos->proximo();
       }
       $arSessaoAssinaturas[$inId]['arCodModulos'] = $arCodModulos;
       $rsAssinaturas->proximo();
       $inId++;
    }

    Sessao::write('assinaturas',$arSessaoAssinaturas);
}

function montaSpnListaAssinaturas()
{
    $arAssinaturas = Sessao::read('assinaturas');

    $rsAssinaturas = new RecordSet;
    $rsAssinaturas->preenche($arAssinaturas);

    $obLista = new Lista;
    $obLista->setTitulo( "Lista de Assinaturas" );
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsAssinaturas );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Entidade");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Cargo");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCodEntidade" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomCGM" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stCargo" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax( 'carregaAssinatura' );" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax( 'excluirAssinatura' );" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "document.getElementById('spnListaAssinaturas').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function incluirAssinaturas()
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinatura.class.php"  );

    $boValida = true;
    $arSessaoAssinaturas = Sessao::read('assinaturas');

    //Valida a inclusão da configuração de certidão
    foreach ($arSessaoAssinaturas as $arAssinatura) {
        //valida se já existe um igual
        if ($arAssinatura['inCGM'] == $_GET['inCGM'] && $arAssinatura['inCodEntidade'] == $_GET['inCodEntidade']) {
            $boValida = false;
            $stJs .= "alertaAviso('Já existe esta assinatura. ( \'".$_GET['stNomCGM']."\')','form','erro','".Sessao::getId()."');\n";
        }
    }

    if ($boValida) {

        $inId = count($arSessaoAssinaturas);

        $arSessaoAssinaturas[$inId]['inId']          = $inId;
        $arSessaoAssinaturas[$inId]['inCodEntidade'] = $_GET['inCodEntidade'];
        $arSessaoAssinaturas[$inId]['inCGM']         = $_GET['inCGM'];
        $arSessaoAssinaturas[$inId]['stNomCGM']      = $_GET['stNomCGM'];
        $arSessaoAssinaturas[$inId]['stCargo']       = $_GET['stCargo'];
        $arSessaoAssinaturas[$inId]['stCRC']         = $_GET['stCRC'];
        $arSessaoAssinaturas[$inId]['arCodModulos']  = $_GET['inCodModulosSelecionados'];

        $stJs .= "limpaFormularioAssinaturas();";
    }

    Sessao::write('assinaturas',$arSessaoAssinaturas);

    return $stJs;
}

function carregaAssinatura($inIdCarregar)
{
    $arSessaoAssinaturas = Sessao::read('assinaturas');

    $inId          = $arSessaoAssinaturas[$inIdCarregar]['inId'];
    $inCodEntidade = $arSessaoAssinaturas[$inIdCarregar]['inCodEntidade'];
    $inCGM         = $arSessaoAssinaturas[$inIdCarregar]['inCGM'];
    $stNomCGM      = $arSessaoAssinaturas[$inIdCarregar]['stNomCGM'];
    $stCargo       = $arSessaoAssinaturas[$inIdCarregar]['stCargo'];
    $stCRC         = $arSessaoAssinaturas[$inIdCarregar]['stCRC'];
    $arCodModulo   = $arSessaoAssinaturas[$inIdCarregar]['arCodModulos'];

    foreach ($arCodModulo as $chave =>$dado) {
        if ($arStringModulo != '') {
            $arStringModulo.= ',';
        }
        $arStringModulo.= "'".$dado."'";
    }

    $stJs = "f.inId.value = ".$inId.";";
    $stJs.= "f.inCodEntidade.value = ".$inCodEntidade.";";
    $stJs.= "f.stNomEntidade.value = ".$inCodEntidade.";";
    $stJs.= "f.inCGM.value = ".$inCGM.";";
    $stJs.= "d.getElementById('stNomCGM').innerHTML = '".$stNomCGM."';";
    $stJs.= "f.stCargo.value = '".$stCargo."';";
    $stJs.= "f.stCRC.value = '".$stCRC."';";
    $stJs.= "
            passaItem( f.inCodModulosSelecionados, f.inCodModulosDisponiveis, 'tudo');

            objDe = f.inCodModulosDisponiveis;
            objPara = f.inCodModulosSelecionados;
            arModulosSelecionados = new Array(".$arStringModulo.");

            for (i = 0; i<arModulosSelecionados.length; i++) {

                chaveArray = array_search(arModulosSelecionados[i], objDe.options);

                if (chaveArray > -1) {
                    valor = objDe.options[chaveArray].value;
                    texto = objDe.options[chaveArray].text;
                    var temp = new Option(texto,valor);
                    destino = objPara.length;
                    objPara.options[destino] = temp;
                    objDe.options[chaveArray] = null;
                }
            }";

    $stJs .= "document.frm.btIncluirAssinaturas.disabled = true;";
    $stJs .= "document.frm.btAlterarAssinaturas.disabled = false;";

    return $stJs;
}

function alterarAssinatura($inIdAlterar)
{
    $arAssinaturas = array();
    $arAssinaturasSessao = Sessao::read('assinaturas');
    Sessao::remove('assinaturas');

    $inId = 0;

    foreach ($arAssinaturasSessao as $arAssinatura => $dados) {
        if ($dados['inId'] == $inIdAlterar) {
            $arAssinaturasSessao[$arAssinatura]['inId'] = $dados['inId'];
            $arAssinaturasSessao[$arAssinatura]['inCGM'] = $_GET['inCGM'];
            $arAssinaturasSessao[$arAssinatura]['inCodEntidade'] = $_GET['inCodEntidade'];
            $arAssinaturasSessao[$arAssinatura]['stCargo'] = $_GET['stCargo'];
            $arAssinaturasSessao[$arAssinatura]['stCRC'] = $_GET['stCRC'];
            $arAssinaturasSessao[$arAssinatura]['arCodModulos'] = $_GET['inCodModulosSelecionados'];
        }
    }

    Sessao::write('assinaturas',$arAssinaturasSessao);

    $stJs = montaSpnListaAssinaturas();

    $stJs.= "limpaFormularioAssinaturas();";

    return $stJs;
}

function excluirAssinatura($inIdExcluir)
{
    $arAssinaturas = array();

    $inId = 0;

    foreach ( Sessao::read('assinaturas') as $arAssinatura ) {
        if ($arAssinatura['inId'] != $inIdExcluir) {
            $arAssinaturas[$inId]['inId'] = $inId;
            $arAssinaturas[$inId]['inCGM']    = $arAssinatura['inCGM'];
            $arAssinaturas[$inId]['stNomCGM'] = $arAssinatura['stNomCGM'];
            $arAssinaturas[$inId]['inCodEntidade'] = $arAssinatura['inCodEntidade'];
            $arAssinaturas[$inId]['stCargo'] = $arAssinatura['stCargo'];
            $arAssinaturas[$inId]['stCRC'] = $arAssinatura['stCRC'];
            $arAssinaturas[$inId]['arCodModulos'] = $arAssinatura['arCodModulos'];
            $inId++;
        }
    }

    Sessao::write('assinaturas',$arAssinaturas);

    $stJs = montaSpnListaAssinaturas();

    return $stJs;
}

switch ($stCtrl) {
    case "configuracoesIniciais":
        configuracoesIniciais();
        $js = montaSpnListaAssinaturas();
    break;
    case "incluirAssinaturas":
        $js  = incluirAssinaturas();
        $js .= montaSpnListaAssinaturas();
        $js .= "sortSelect(frm.inCodModulosDisponiveis, 'text');";
    break;
    case "alterarAssinaturas":
        $js  = alterarAssinatura( $_GET['inId'] );
        $js .= "sortSelect(frm.inCodModulosDisponiveis, 'text');";
    break;
    case "excluirAssinatura":
        $js  = excluirAssinatura( $_GET['inId'] );
    break;
    case "carregaAssinatura":
        $js  = carregaAssinatura( $_GET['inId'] );
    break;
}

if ($js) {
    echo $js;
}
