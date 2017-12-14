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
 * Classe de mapeamento da tabela compras.compra_direta
 * Data de Criação: 16/11/2012

 * @author Analista: Gelson
 * @author Desenvolvedor: Carolina

 * @package URBEM
 * @subpackage Mapeamento
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTransparenciaComprasLicitacao extends Persistente
{
    public function recuperaRelacionamentoCompras(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRelacionamentoCompras().$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoCompras()
    {
        $stSql = "
        SELECT DISTINCT
               compra_direta.cod_compra_direta
             , compra_direta.exercicio_entidade
             , compra_direta.cod_modalidade
             , modalidade.descricao as modalidade
             , empenho.exercicio as exercicio_empenho
             , empenho.cod_empenho
             , compra_direta.cod_entidade
             , tipo_objeto.cod_tipo_objeto
             , tipo_objeto.descricao as descricao_tipo_objeto
             , objeto.cod_objeto
             , objeto.descricao as descricao_objeto
             , tipo_licitacao.cod_tipo_licitacao
             , tipo_licitacao.descricao as descricao_tipo_licitacao
             , to_char(julgamento.timestamp::DATE,'ddmmyyyy') as dt_compra_licitacao

          FROM compras.compra_direta

          JOIN compras.mapa
            ON compra_direta.exercicio_mapa = mapa.exercicio
           AND compra_direta.cod_mapa = mapa.cod_mapa

          JOIN compras.objeto
            ON objeto.cod_objeto = mapa.cod_objeto

          JOIN compras.mapa_cotacao
            ON mapa_cotacao.exercicio_mapa = mapa.exercicio
           AND mapa_cotacao.cod_mapa = mapa.cod_mapa

          JOIN compras.cotacao
            ON cotacao.exercicio = mapa_cotacao.exercicio_mapa
           AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

          JOIN compras.modalidade
            ON modalidade.cod_modalidade = compra_direta.cod_modalidade

          JOIN compras.tipo_licitacao
            ON tipo_licitacao.cod_tipo_licitacao = mapa.cod_tipo_licitacao

          JOIN compras.tipo_objeto
            ON tipo_objeto.cod_tipo_objeto = compra_direta.cod_tipo_objeto

     LEFT JOIN compras.julgamento
            ON julgamento.exercicio = cotacao.exercicio
           AND julgamento.cod_cotacao = cotacao.cod_cotacao

     LEFT JOIN compras.julgamento_item
            ON julgamento_item.exercicio = julgamento.exercicio
           AND julgamento_item.cod_cotacao = julgamento.cod_cotacao

     LEFT JOIN empenho.item_pre_empenho_julgamento
            ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio
           AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao
           AND item_pre_empenho_julgamento.cod_item  = julgamento_item.cod_item
           AND item_pre_empenho_julgamento.lote  = julgamento_item.lote
           AND item_pre_empenho_julgamento.cgm_fornecedor  = julgamento_item.cgm_fornecedor

     LEFT JOIN empenho.item_pre_empenho
            ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
           AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
           AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item

     LEFT JOIN empenho.pre_empenho
            ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
           AND pre_empenho.exercicio      = item_pre_empenho.exercicio

     LEFT JOIN empenho.empenho
            ON empenho.exercicio = pre_empenho.exercicio
           AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

         WHERE compra_direta.exercicio_entidade = '".$this->getDado('exercicio') ."'
           AND compra_direta.cod_entidade IN (".$this->getDado('stEntidades') .")
           AND compra_direta.timestamp BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'DD-MM-YYYY') AND TO_DATE('".$this->getDado('dtFinal')."', 'DD-MM-YYYY')";

        return $stSql;
    }

    public function recuperaRelacionamentoLicitacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRelacionamentoLicitacao().$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoLicitacao()
    {
        $stSql = "
        SELECT
               licitacao.exercicio as exercicio_entidade
             , licitacao.cod_entidade
             , licitacao.cod_licitacao
             , modalidade.descricao as modalidade
             , empenho.exercicio as exercicio_empenho
             , empenho.cod_empenho
             , tipo_licitacao.descricao as descricao_tipo_licitacao
             , tipo_objeto.descricao as descricao_tipo_objeto
             , objeto.descricao as descricao_objeto

          FROM licitacao.licitacao

    INNER JOIN compras.tipo_licitacao
            ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao

    INNER JOIN compras.modalidade
            ON modalidade.cod_modalidade = licitacao.cod_modalidade

    INNER JOIN compras.objeto
            ON objeto.cod_objeto = licitacao.cod_objeto

    INNER JOIN compras.tipo_objeto
            ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto

    INNER JOIN compras.mapa
            ON mapa.exercicio = licitacao.exercicio_mapa
           AND mapa.cod_mapa = licitacao.cod_mapa

    INNER JOIN compras.mapa_cotacao
            ON mapa_cotacao.exercicio_mapa = mapa.exercicio
           AND mapa_cotacao.cod_mapa = mapa.cod_mapa

    INNER JOIN compras.cotacao
            ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
           AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

     LEFT JOIN compras.julgamento
            ON julgamento.exercicio = cotacao.exercicio
           AND julgamento.cod_cotacao = cotacao.cod_cotacao

     LEFT JOIN compras.julgamento_item
            ON julgamento_item.exercicio = cotacao.exercicio
           AND julgamento_item.cod_cotacao = cotacao.cod_cotacao

     LEFT JOIN empenho.item_pre_empenho_julgamento
            ON item_pre_empenho_julgamento.exercicio_julgamento  = julgamento_item.exercicio
           AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao
           AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item
           AND item_pre_empenho_julgamento.lote = julgamento_item.lote
           AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor

     LEFT JOIN empenho.item_pre_empenho
            ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
           AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
           AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item

     LEFT JOIN empenho.pre_empenho
            ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
           AND pre_empenho.exercicio = item_pre_empenho.exercicio

     LEFT JOIN empenho.empenho
            ON empenho.exercicio = pre_empenho.exercicio
           AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

         WHERE licitacao.exercicio = '".$this->getDado('exercicio') ."'
           AND licitacao.cod_entidade IN (".$this->getDado('stEntidades') .")
           AND licitacao.timestamp BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'DD-MM-YYYY') AND TO_DATE('".$this->getDado('dtFinal')."', 'DD-MM-YYYY')

      GROUP BY licitacao.exercicio
             , licitacao.cod_entidade
             , licitacao.cod_licitacao
             , modalidade.descricao
             , empenho.exercicio
             , empenho.cod_empenho
             , tipo_licitacao.descricao
             , tipo_objeto.descricao
             , objeto.descricao ";

        return $stSql;
    }

    public function recuperaPublicacaoEdital(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaPublicacaoEdital().$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaPublicacaoEdital()
    {
        $stSql = "
        SELECT edital.exercicio as exercicio_edital
             , edital.num_edital
             , edital.exercicio_licitacao
             , licitacao.cod_licitacao
             , licitacao.cod_entidade
             , modalidade.descricao as modalidade
             , tipo_veiculos_publicidade.descricao as veiculo_publicacao
             , TO_CHAR( publicacao_edital.data_publicacao , 'ddmmyyyy') as data_publicacao
             , publicacao_edital.observacao

          FROM licitacao.licitacao

    INNER JOIN compras.modalidade
            ON modalidade.cod_modalidade = licitacao.cod_modalidade

    INNER JOIN licitacao.edital
            ON edital.cod_licitacao = licitacao.cod_licitacao
           AND edital.cod_entidade = licitacao.cod_entidade
           AND edital.exercicio_licitacao = licitacao.exercicio
           AND edital.cod_modalidade = licitacao.cod_modalidade

    INNER JOIN licitacao.publicacao_edital
            ON publicacao_edital.num_edital = edital.num_edital
           AND publicacao_edital.exercicio = edital.exercicio

    INNER JOIN licitacao.veiculos_publicidade
            ON veiculos_publicidade.numcgm = publicacao_edital.numcgm

    INNER JOIN licitacao.tipo_veiculos_publicidade
            ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade

      WHERE edital.exercicio = '".$this->getDado('exercicio') ."'
        AND licitacao.cod_entidade IN (".$this->getDado('stEntidades') .")
        AND publicacao_edital.data_publicacao BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'DD-MM-YYYY') AND TO_DATE('".$this->getDado('dtFinal')."', 'DD-MM-YYYY')";

        return $stSql;
    }
}

?>
