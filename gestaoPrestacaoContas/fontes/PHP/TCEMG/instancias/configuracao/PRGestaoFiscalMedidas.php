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
    * Página de Processamento de Gestao Fiscal Medidas
    * Data de Criação: 29/07/2013

    * @author Analista:
    * @author Desenvolvedor: Carolina Schwaab Marcal

    * @ignore

    * Casos de uso:

    $Id:

    $Id:$
    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGMedidas.class.php" );

$stPrograma = "GestaoFiscalMedidas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
$arMedidas = Sessao::read('arMedidas');
$arrMedidasExcluidas = Sessao::read('arMedidasExcluidas');

$obTTCEMGMedidas = new TTCEMGMedidas();

$obErro = new Erro;
$obTransacao = new Transacao;
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

foreach ($arMedidas as $arMedida) {
    
    unset($inCodMedidaExiste);
    
    if ($arMedida["boRiscosFiscais"] == 'Sim') {
        $arMedida["boRiscosFiscais"]= "true";
    } elseif ($arMedida["boRiscosFiscais"] == 'Não') {
        $arMedida["boRiscosFiscais"] = "false";
    }
    if ($arMedida["boMetasFiscais"] == 'Sim') {
        $arMedida["boMetasFiscais"]  = "true";
    } elseif ($arMedida["boMetasFiscais"] == 'Não') {
        $arMedida["boMetasFiscais"]= "false";
    }
    if ($arMedida["boContratacaoARO"] == 'Sim') {
        $arMedida["boContratacaoARO"] = "true";
    } elseif ($arMedida["boContratacaoARO"] == 'Não') {
        $arMedida["boContratacaoARO"] = "false";
    }
    
    $obTTCEMGMedidas->setDado("cod_poder", $arMedida['cod_poder']);
    $obTTCEMGMedidas->setDado("cod_mes",$arMedida['inMes'] );
    $obTTCEMGMedidas->setDado("descricao", $arMedida["medida"]);

    $obErro = $obTTCEMGMedidas->recuperaRelacionamentoMedidas( $rsMedida );
    
    foreach ($rsMedida->getElementos() as $i => $val){
        if ($val['cod_medida'] == $arMedida['cod_medida']){
            $inCodMedidaExiste = $val['cod_medida'];
        }
    }
    
    if ( !empty($inCodMedidaExiste)) {
        
        $obTTCEMGMedidas->setDado("cod_medida", $inCodMedidaExiste );
        $obTTCEMGMedidas->setDado("cod_poder", $arMedida['cod_poder']);
        $obTTCEMGMedidas->setDado("cod_mes",$arMedida['inMes'] );
        $obTTCEMGMedidas->setDado("descricao", $arMedida["medida"]);
        $obTTCEMGMedidas->setDado("riscos_fiscais", $arMedida["boRiscosFiscais"]);
        $obTTCEMGMedidas->setDado("metas_fiscais", $arMedida["boMetasFiscais"]);
        $obTTCEMGMedidas->setDado("contratacao_aro", $arMedida["boContratacaoARO"]);
        $obErro = $obTTCEMGMedidas->alteracao();
        
    } else {
        
        $obTTCEMGMedidas->proximoCod( $inCodMedida );
        $obTTCEMGMedidas->setDado("cod_medida", $inCodMedida );
        $obTTCEMGMedidas->setDado("cod_poder", $arMedida['cod_poder']);
        $obTTCEMGMedidas->setDado("cod_mes",$arMedida['inMes'] );
        $obTTCEMGMedidas->setDado("riscos_fiscais", $arMedida["boRiscosFiscais"]);
        $obTTCEMGMedidas->setDado("metas_fiscais", $arMedida["boMetasFiscais"]);
        $obTTCEMGMedidas->setDado("contratacao_aro", $arMedida["boContratacaoARO"]);
        $obTTCEMGMedidas->setDado("descricao", $arMedida["medida"]);
        $obErro = $obTTCEMGMedidas->inclusao();
        
    }
}

foreach ($arrMedidasExcluidas as $excluidas) {
    
    $obErro = new Erro;
    $obTTCEMGMedidas = new TTCEMGMedidas();
    $obTTCEMGMedidas->setDado("cod_poder", $excluidas['cod_poder']);
    $obTTCEMGMedidas->setDado("cod_mes",$excluidas['inMes'] );
    $obTTCEMGMedidas->setDado("descricao", $excluidas["medida"]);
    $obTTCEMGMedidas->setDado("cod_medida", $excluidas["cod_medida"]);
    $obErro = $obTTCEMGMedidas->recuperaRelacionamentoMedidas( $rsMedidaExcluida );

    while ( !$rsMedidaExcluida->eof() ) {
        
        $obTTCEMGMedidas->setDado("cod_medida", $rsMedidaExcluida->getCampo('cod_medida'));
        $obTTCEMGMedidas->setDado("cod_poder", $rsMedidaExcluida->getCampo('cod_poder'));
        $obTTCEMGMedidas->setDado("cod_mes",$rsMedidaExcluida->getCampo('cod_mes') );
        $obTTCEMGMedidas->setDado("descricao", $rsMedidaExcluida->getCampo('descricao'));
        $obTTCEMGMedidas->setDado("riscos_fiscais", $rsMedidaExcluida->getCampo('riscos_fiscais'));
        $obTTCEMGMedidas->setDado("metas_fiscais", $rsMedidaExcluida->getCampo('metas_fiscais'));
        $obTTCEMGMedidas->setDado("contratacao_aro", $rsMedidaExcluida->getCampo('contratacao_aro'));
        $obTTCEMGMedidas->exclusao();
        
        $rsMedidaExcluida->proximo();
    }

}
SistemaLegado::alertaAviso($pgFilt, "Medida", "incluir", "aviso", '', "../");
Sessao::encerraExcecao();
?>
