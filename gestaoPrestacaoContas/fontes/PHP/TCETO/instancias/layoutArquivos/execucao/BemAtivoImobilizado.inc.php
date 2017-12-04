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

  * Layout exportação TCE-TO arquivo : 
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: 
  *
  * @ignore
  * $Id: BemAtivoImobilizado.inc.php 60755 2014-11-13 13:34:59Z lisiane $
  * $Date: 2014-11-13 11:34:59 -0200 (Thu, 13 Nov 2014) $
  * $Author: lisiane $
  * $Rev: 60755 $
  *
*/
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOBemAtivoImobilizado.class.php';

$obTTCETOBemAtivoImobilizado= new TTCETOBemAtivoImobilizado();
$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$stNomeArquivo         = 'BemAtivoImobilizado';
$stEntidades = $inCodEntidade;
foreach (explode(',',$stEntidades) as $inCodEntidade) {
    $arEsquemasEntidades[] = $inCodEntidade;
}
    
foreach ($arEsquemasEntidades as $inCodEntidade) {
    
    $rsRecordSet = "rsAtivoPermanente";
    $rsRecordSet .= $stEntidade;
    $$rsRecordSet = new RecordSet();
    
    $obTTCETOBemAtivoImobilizado->setDado('stExercicio'  , Sessao::getExercicio());
    $obTTCETOBemAtivoImobilizado->setDado('inCodEntidade', $inCodEntidade        );
    $obTTCETOBemAtivoImobilizado->setDado('bimestre'     , $inBimestre           );
    $obTTCETOBemAtivoImobilizado->setDado('dt_inicial'   , $stDataInicial        );
    $obTTCETOBemAtivoImobilizado->setDado('dt_final'     , $stDataFinal          );
    $obTTCETOBemAtivoImobilizado->recuperaBemAtivoImobilizado($rsRecordSet       );

    $idCount=0;
    $arResult = array();
    
    while (!$rsRecordSet->eof()) {
        $arResult[$idCount]['idUnidadeGestora']             = $rsRecordSet->getCampo('cod_und_gestora'                );
        $arResult[$idCount]['bimestre']                     = $rsRecordSet->getCampo('bimestre'                       );
        $arResult[$idCount]['exercicio']                    = $rsRecordSet->getCampo('exercicio'                      );
        $arResult[$idCount]['idOrgao']                      = $rsRecordSet->getCampo('cod_orgao'                      );
        $arResult[$idCount]['idUnidadeOrcamentaria']        = $rsRecordSet->getCampo('cod_und_orcamentaria'           );
        $arResult[$idCount]['numeroRegistroBem']            = $rsRecordSet->getCampo('num_bem'                        );
        $arResult[$idCount]['descricao']                    = $rsRecordSet->getCampo('descricao'                      );
        $arResult[$idCount]['numeroEmpenho']                = $rsRecordSet->getCampo('num_empenho'                    );
        $arResult[$idCount]['data']                         = $rsRecordSet->getCampo('data_inscricao'                 );
        $arResult[$idCount]['valor']                        = $rsRecordSet->getCampo('valor_bem'                      );
        $arResult[$idCount]['setor']                        = $rsRecordSet->getCampo('setor'                          );
        $arResult[$idCount]['numeroTombamento']             = $rsRecordSet->getCampo('num_tombamento'                 );
        $arResult[$idCount]['contaContabil']                = $rsRecordSet->getCampo('conta_contabil'                 );
        $arResult[$idCount]['estadoBem']                    = $rsRecordSet->getCampo('estado_bem'                     );
        $arResult[$idCount]['alteracaoBemAtivoImobilizado'] = $rsRecordSet->getCampo('alteracao_bem_ativo_imobilizado');
        $arResult[$idCount]['percentual']                   = $rsRecordSet->getCampo('percentual'                     );
        
        $idCount++;
        
        $rsRecordSet->proximo();
    }
}
    
unset($UndGestora, $CodUndGestora, $obTTCEALServidor, $stPeriodoMovimentacao, $obTEntidade);
?>