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
 * Esse arquivo pertence ao projeto Urbem
 * This file belogns to the Urbem project
 *
 */

/*
 * Mapeamento de tabela
 *
 * Mapeamento da tabela empenho.ordem_pagamento_recibo_extra.
 *
 * @package SW
 * @version $Id$
 * @author Eduardo Paculski Schitz <eduardo.schitz@cnm.org.br>
 */
class TEmpenhoOrdemPagamentoReciboExtra extends Persistente
{

    /**
     * Método Construtor
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('empenho.ordem_pagamento_recibo_extra');
        $this->setCampoCod('cod_ordem');
        $this->setComplementoChave('exercicio,cod_entidade,cod_recibo_extra,tipo_recibo');

        // campo, tipo, not_null, data_length, pk, fk
        $this->AddCampo('exercicio'       , 'varchar', true, '', true, true);
        $this->AddCampo('cod_entidade'    , 'integer', true, '', true, true);
        $this->AddCampo('cod_ordem'       , 'integer', true, '', true, true);
        $this->AddCampo('cod_recibo_extra', 'integer', true, '', true, true);
        $this->AddCampo('tipo_recibo'     , 'varchar', true, '', true, true);
    }

    public function recuperaOrdemPagamentoReciboExtra(&$rsRecordSet, $stFiltro= "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaOrdemPagamentoReciboExtra().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaOrdemPagamentoReciboExtra()
    {
        $stSql  = "      SELECT  *                                                         \n";
        $stSql .= "        FROM empenho.ordem_pagamento_recibo_extra                       \n";
        $stSql .= "       WHERE exercicio = '".$this->getDado('exercicio')."'              \n";
        $stSql .= "         AND cod_entidade = ".$this->getDado('cod_entidade')."          \n";
        $stSql .= "         AND cod_recibo_extra = ".$this->getDado('cod_recibo_extra')."  \n";

        return $stSql;
    }
}
?>
