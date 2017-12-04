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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TComprasCompraDiretaProcesso extends Persistente
{
    public function TComprasCompraDiretaProcesso()
    {
        parent::Persistente();
        $this->setTabela("compras.compra_direta_processo");

        $this->setCampoCod('cod_compra_direta');
        $this->setComplementoChave('cod_entidade, exercicio_entidade, cod_modalidade');

        $this->AddCampo( 'cod_compra_direta'	,'integer'      ,true	, ''	,true	,true   );
        $this->AddCampo( 'cod_entidade'       	,'integer'	,true	, '' 	,true  	,true	);
        $this->AddCampo( 'exercicio_entidade'   ,'char'		,true	, '4' 	,true  	,true	);
        $this->AddCampo( 'cod_modalidade'      	,'integer'	,true	, '' 	,true  	,true	);
        $this->AddCampo( 'exercicio_processo'   ,'char'		,true	, '4' 	,false  ,true	);
        $this->AddCampo( 'cod_processo'         ,'integer'	,true	, '11' 	,false 	,true   );
    }

    public function recuperaPorCompraDireta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaPorCompraDireta().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaPorCompraDireta()
    {
        $stSql = "SELECT *
                    FROM compras.compra_direta_processo
                   WHERE cod_compra_direta  = ".$this->getDado('cod_compra_direta')."
                     AND cod_entidade       = ".$this->getDado('cod_entidade')."
                     AND exercicio_entidade = '".$this->getDado('exercicio_entidade')."'
                     AND cod_modalidade     = ".$this->getDado('cod_modalidade');

        return $stSql;
    }
}
