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

    * Página de Include Oculta - Exportação Arquivos Relacionais - rubricaFolhaServidores.xml
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    $Id: rubricasFolhaServidores.inc.php 59756 2014-09-09 19:50:47Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALRubricasFolhaServidores.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEAL_NEGOCIO.'RExportacaoRelacionais.class.php';

$obRExportacaoRelacionais = new RExportacaoRelacionais();
$obTTCEALRubricasFolhaServidores = new TTCEALRubricasFolhaServidores();

$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$undGestora            = explode(',', $stEntidades);
$stNomeArquivo         = "RubricasFolhaServidores";
$arEsquemasEntidades   = array();

foreach ($undGestora as $inCodEntidade) {
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);

    if ($rsEsquema->getNumLinhas() > 0 || $codEntidadePrefeitura ==$inCodEntidade ) {
        $arEsquemasEntidades[] = $inCodEntidade;
    }    
}

foreach ($arEsquemasEntidades as $inCodEntidade) {
    if ($codEntidadePrefeitura !=$inCodEntidade) {
        $stEntidade = '_'.$inCodEntidade;
    } else {
        $stEntidade = '';
    }
    $rsRecordSet = "rsRubricasFolhaServidores";
    $rsRecordSet .= $stEntidade;
    $$rsRecordSet = new RecordSet();
    
    $obTTCEALRubricasFolhaServidores->setDado('stExercicio', Sessao::getExercicio());
    $obTTCEALRubricasFolhaServidores->setDado('stEntidade', $stEntidade  );
    $obTTCEALRubricasFolhaServidores->setDado('inCodEntidade', $inCodEntidade  );
    $obTTCEALRubricasFolhaServidores->listarExportacaoRubricasFolhaServidores($$rsRecordSet );
    
    $arResult = array();
    
    while (!$$rsRecordSet->eof()) {
        $arResult[$idCount]['CodUndGestora'] = $$rsRecordSet->getCampo('cod_und_gestora');
        $arResult[$idCount]['CodigoUA'] = $$rsRecordSet->getCampo('codigo_ua');
        $arResult[$idCount]['Bimestre'] = $inBimestre;
        $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
        $arResult[$idCount]['CodRubricaSal'] = $$rsRecordSet->getCampo('cod_rubrica_sal');
        $arResult[$idCount]['Descricao'] = $obRExportacaoRelacionais->strSemAcentos($$rsRecordSet->getCampo('descricao'));
        $arResult[$idCount]['CodTipoRubrica'] = $$rsRecordSet->getCampo('cod_tipo_rubrica');
        $idCount++;
        $$rsRecordSet->proximo();
    }

    return $arResult;
}
?>