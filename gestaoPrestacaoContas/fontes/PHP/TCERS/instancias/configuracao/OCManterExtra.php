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
    * Página Formulário - Parâmetros do Arquivo RDEXTRA.
    * Data de Criação   : 11/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: $

    * Casos de uso: uc-02.08.04, uc-02.08.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EXP_NEGOCIO ."RExportacaoTCERSArqRDExtra.class.php"    );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"  );
//include_once( CAM_REGRA . "RTCERSArqRDExtra.class.php"    );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRTCERSArqRDExtra = new RExportacaoTCERSArqRDExtra();
//$obRTCERSArqRDExtra->obRTCERSRDExtra->setExercicio(Sessao::getExercicio());
//$obRTCERSArqRDExtra->obRTCERSRDExtra->listar($rsRDExtra);

function listaValores($arRecordSet, $executa=true)
{
    $rsRDExtra = new RecordSet();
    $rsRDExtra->preenche($arRecordSet);
    if ($rsRDExtra->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        //$obLista->setTitulo( "Conta Contábil" );

        $obLista->setRecordSet( $rsRDExtra );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Contas Contábeis" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Classificação" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_estrutural] - [nom_conta]" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[classificacao] - [nom_classificacao]" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirdados('Excluir');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $js .= "d.getElementById('spnExtra').innerHTML = '".$stHtml."';";
    if ($executa==true) {
        SistemaLegado::executaFrameOculto($js);
    } else {
        return $js;
    }
}

// Acoes por pagina
switch ($stCtrl) {
    //monta HTML com os ATRIBUTOS relativos a Conta Contábil selecionada
    case "MontaDescricaoConta":

        $obTPlanoConta = new TContabilidadePlanoConta();
        $obTPlanoConta->recuperaContaPlanoAnalitica($rsPlanoConta, " AND pa.exercicio='".Sessao::getExercicio()."' AND pa.cod_plano='".$_POST['stCodReduzido']."'");
        $stCodEstrutural = $rsPlanoConta->getCampo('cod_estrutural');

        $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->setExercicio(Sessao::getExercicio());
        $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->setCodEstrutural($stCodEstrutural);
        $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->consultar();
        $stNomConta = $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->getNomConta();

        if ($stNomConta) {
            $stJs = "d.getElementById('stDescricaoClassificacao').innerHTML = '".$stNomConta."';";
        } else {
            $stMensagem = "Esse Código Estrutural da Conta Contábil não existe.";
            $stJs  = "alertaAviso('@$stMensagem','form','erro','".Sessao::getId()."');";
            $stJs .= "f.stCodReduzido.value = '';";
            $stJs .= "d.getElementById('stDescricaoClassificacao').innerHTML = '&nbsp;';";
        }
    break;
    case "MontaListaSessao":
        //$obRTCERSArqRDExtra = new RTCERSArqRDExtra();
        $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->setExercicio(Sessao::getExercicio());
        $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->listar($rsRDExtra);

        $inCount = 0;
        $arRDExtra = array();
        while (!$rsRDExtra->eof()) {
            $arRDExtra[$inCount]["inId"]                = $inCount;
            $arRDExtra[$inCount]["cod_estrutural"]      = $rsRDExtra->getCampo("cod_estrutural");
            $arRDExtra[$inCount]["nom_conta"]           = $rsRDExtra->getCampo("nom_conta");
            $arRDExtra[$inCount]["classificacao"]       = $rsRDExtra->getCampo("classificacao");
            $arRDExtra[$inCount]["nom_classificacao"]   = $obRTCERSArqRDExtra->consultaClassificacao( $rsRDExtra->getCampo("classificacao") );
            $inCount++;
            $rsRDExtra->proximo();
        }
        Sessao::write('arRDExtra', $arRDExtra);
        listaValores( $arRDExtra );
    break;
    case "Incluir":
        $inControle = 1;
        $obTPlanoConta = new TContabilidadePlanoConta();
        $obTPlanoConta->recuperaContaPlanoAnalitica($rsPlanoConta, " AND pa.exercicio='".Sessao::getExercicio()."' AND pa.cod_plano='".$_POST['stCodReduzido']."'");
        $stCodEstrutural = $rsPlanoConta->getCampo('cod_estrutural');

        $arRDExtra = Sessao::read('arRDExtra');
        foreach ($arRDExtra as $Recordset) {
            if($Recordset["cod_estrutural"] == $stCodEstrutural) $inControle = 0;
            }
        if ($inControle) {
            $inI = count($arRDExtra);
            $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->setExercicio(Sessao::getExercicio());
            $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->setCodEstrutural($stCodEstrutural);
            $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->consultar();
            $arRDExtra[$inI]['inId']                = $inI;
            $arRDExtra[$inI]['classificacao']       = $_POST["inClassificacao"];
            $arRDExtra[$inI]['nom_classificacao']   = $obRTCERSArqRDExtra->consultaClassificacao( $_POST["inClassificacao"] );
            $arRDExtra[$inI]['cod_estrutural']      = $stCodEstrutural ;
            $arRDExtra[$inI]['nom_conta']           = $obRTCERSArqRDExtra->obRExportacaoTCERSRDExtra->getNomConta();
        } else {
            $stMensagem = "Esse Código Estrutural da Conta Contábil já foi inserido na lista.";
            $stJs  = "alertaAviso('@$stMensagem','form','erro','".Sessao::getId()."');";
        }
        Sessao::write('arRDExtra', $arRDExtra);
        listaValores( $arRDExtra );
    break;
    case "Excluir":
        $inApagar = $_POST["inApagar"];
        $inCount = 0;
        $arRDExtra = Sessao::read('arRDExtra');
        $arRDExtra_temp = array();
        foreach ($arRDExtra as $Recordset) {
            $inIndice = sizeof($arRDExtra_temp);
            if ($inCount != $inApagar) {
                $arRDExtra_temp[$inIndice]["inId"]            = $inIndice;
                $arRDExtra_temp[$inIndice]["cod_estrutural"]  = $Recordset["cod_estrutural"];
                $arRDExtra_temp[$inIndice]["nom_conta"]       = $Recordset["nom_conta"];
                $arRDExtra_temp[$inIndice]["classificacao"]   = $Recordset["classificacao"];
                $arRDExtra_temp[$inIndice]["nom_classificacao"] = $Recordset["nom_classificacao"];
                }
            $inCount++;
            }
        Sessao::write('arRDExtra', $arRDExtra_temp);
        listaValores( $arRDExtra_temp );
    break;
}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);
SistemaLegado::LiberaFrames();

?>
