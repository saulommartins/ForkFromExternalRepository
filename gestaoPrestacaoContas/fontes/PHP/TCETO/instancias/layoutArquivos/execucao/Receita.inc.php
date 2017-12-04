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

  * Layout exportação TCE-TO arquivo Receita.xml 
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
  *
  * @ignore
  * $Id: Receita.inc.php 60852 2014-11-19 12:35:53Z jean $
  * $Date: 2014-11-19 10:35:53 -0200 (Wed, 19 Nov 2014) $
  * $Author: jean $
  * $Rev: 60852 $
  *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOReceita.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTTCETOReceita = new TTCETOReceita();
$obTOrcamentoEntidade = new TOrcamentoEntidade;

$obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
$obTOrcamentoEntidade->setDado( 'cod_entidade', $inCodEntidade );
$obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade );

if( $rsEntidade->inNumLinhas > 0) {
    $CodUndGestora = $inCodEntidade;
}

if( !$CodUndGestora ) {
    $CodUndGestora = $UndGestora[0];
}

$stNomePoder = $rsEntidade->getCampo('entidade');

if ( preg_match("/prefeitura/i", $stNomePoder) ) {
    $stCodOrgaoTCETO    = 'tceto_orgao_prefeitura';
    $stCodUnidadeTCETO  = 'tceto_unidade_prefeitura';
}elseif ( preg_match("/camara/i", $stNomePoder) ) {
    $stCodOrgaoTCEAL    = 'tceto_orgao_camara';
    $stCodUnidadeTCETO  = 'tceto_unidade_camara';        
}elseif ( preg_match("/fundo/i", $stNomePoder) ) {
    $stCodOrgaoTCETO    = 'tceto_orgao_rpps';
    $stCodUnidadeTCETO  = 'tceto_unidade_rpps';
}else{
    $stCodOrgaoTCETO    = 'tceto_orgao_outros';
    $stCodUnidadeTCETO  = 'tceto_unidade_outros';
}

$obTTCETOReceita->setDado('exercicio'         , Sessao::getExercicio());
$obTTCETOReceita->setDado('cod_entidade'      , $inCodEntidade );
$obTTCETOReceita->setDado('und_gestora'       , $inCodEntidade );
$obTTCETOReceita->setDado('dtInicial'         , $dtInicial  );
$obTTCETOReceita->setDado('dtFinal'           , $dtFinal    );
$obTTCETOReceita->setDado('bimestre'          , $inBimestre );
$obTTCETOReceita->setDado('poder_cod_orgao'   , $stCodOrgaoTCETO      );
$obTTCETOReceita->setDado('poder_cod_unidade' , $stCodUnidadeTCETO    );

$obTTCETOReceita->recuperaReceita($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Receita';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']           = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']                   = $rsRecordSet->getCampo('bimestre');;
    $arResult[$idCount]['exercicio']                  = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idOrgao']                    = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['idUnidadeOrcamentaria']      = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['contaContabil']              = $rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['idContaReceitaOrcamentaria'] = $rsRecordSet->getCampo('cod_receita');
    $arResult[$idCount]['idCredor']                   = "";
    $arResult[$idCount]['realizadaJaneiro']           = $rsRecordSet->getCampo('realizada_jan');
    $arResult[$idCount]['realizadaFevereiro']         = $rsRecordSet->getCampo('realizada_fev');
    $arResult[$idCount]['realizadaMarco']             = $rsRecordSet->getCampo('realizada_mar');
    $arResult[$idCount]['realizadaAbril']             = $rsRecordSet->getCampo('realizada_abr');
    $arResult[$idCount]['realizadaMaio']              = $rsRecordSet->getCampo('realizada_mai');
    $arResult[$idCount]['realizadaJunho']             = $rsRecordSet->getCampo('realizada_jun');
    $arResult[$idCount]['realizadaJulho']             = $rsRecordSet->getCampo('realizada_jul');
    $arResult[$idCount]['realizadaAgosto']            = $rsRecordSet->getCampo('realizada_ago');
    $arResult[$idCount]['realizadaSetembro']          = $rsRecordSet->getCampo('realizada_set');
    $arResult[$idCount]['realizadaOutubro']           = $rsRecordSet->getCampo('realizada_out');
    $arResult[$idCount]['realizadaNovembro']          = $rsRecordSet->getCampo('realizada_nov');
    $arResult[$idCount]['realizadaDezembro']          = $rsRecordSet->getCampo('realizada_dez');
    $arResult[$idCount]['caracteristicaPeculiar']     = $rsRecordSet->getCampo('carac_peculiar');

    $idCount++;
    
    $rsRecordSet->proximo();
}

?>