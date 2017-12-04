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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 31/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    $Id: TTCEAMParticip.class.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * @package URBEM
    *
*/

// NÃO DEVE TRAZER NESTE ARQUIVO LICITAÇÕES OU COMPRAS DIRETAS ANULADAS

class TTCEAMParticip extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMParticip()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT 0 AS reservado_tc
                 , processo_licitatorio
                 , cpf_cnpj
                 , tipo_pessoa
                 , nom_cgm
                 , 1 AS tipo_participacao
                 , 0 AS cgc_consorcio
              FROM (
                SELECT CASE WHEN licitacao.cod_modalidade = 1  THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 2  THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 3  THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 4  THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 5  THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 6  THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 7  THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 8  THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 9  THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 10 THEN 'OT'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 11 THEN 'RP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                       END AS processo_licitatorio
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                1
                            ELSE
                                2
                       END AS tipo_pessoa
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                sw_cgm_pessoa_fisica.cpf
                            ELSE
                                sw_cgm_pessoa_juridica.cnpj
                       END AS cpf_cnpj
                     , sw_cgm.nom_cgm
                  FROM compras.julgamento_item
                  JOIN compras.cotacao_fornecedor_item
                    ON cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                   AND cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                   AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                   AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                   AND cotacao_fornecedor_item.lote           = julgamento_item.lote
                  JOIN compras.cotacao_item
                    ON cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                   AND cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                   AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                   AND cotacao_item.lote        = cotacao_fornecedor_item.lote
                  JOIN compras.cotacao
                    ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                   AND cotacao.exercicio   = cotacao_item.exercicio
                  JOIN compras.mapa_cotacao
                    ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                   AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                  JOIN compras.mapa
                    ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                   AND mapa.exercicio = mapa_cotacao.exercicio_mapa
                  JOIN licitacao.licitacao
                    ON licitacao.cod_mapa       = mapa.cod_mapa
                   AND licitacao.exercicio_mapa = mapa.exercicio
                 JOIN licitacao.participante
                    ON licitacao.cod_licitacao  = participante.cod_licitacao
                   AND licitacao.cod_entidade   = participante.cod_entidade
                   AND licitacao.cod_modalidade = participante.cod_modalidade
                   AND licitacao.exercicio      = participante.exercicio
             LEFT JOIN licitacao.licitacao_anulada
                    ON licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao
                   AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade
                   AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade
                   AND licitacao.exercicio      = licitacao_anulada.exercicio
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = julgamento_item.cgm_fornecedor
             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
             LEFT JOIN sw_cgm_pessoa_juridica
                    ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                 WHERE mapa.exercicio = '".$this->getDado('exercicio')."'
                   AND to_char(licitacao.timestamp,'mm') = '".$this->getDado('mes')."'
                   AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                   AND licitacao_anulada.cod_licitacao IS NULL
              GROUP BY sw_cgm_pessoa_fisica.numcgm
                     , sw_cgm_pessoa_fisica.cpf
                     , sw_cgm_pessoa_juridica.cnpj
                     , licitacao.cod_modalidade
                     , licitacao.cod_licitacao
                     , licitacao.exercicio
                     , licitacao_anulada.cod_licitacao
                     , sw_cgm.nom_cgm

             
                ) AS registros

        ";

        return $stSql;
    }

    public function recuperaParticipanteLicitacaoEConta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaParticipanteLicitacaoEConta().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaParticipanteLicitacaoEConta(){
      $stSql ="
          SELECT   processo_licitatorio
                 , cpf_cnpj
                 , tipo_pessoa
                 , nom_cgm
                 , 1 AS tipo_participacao
                 , 0 AS cgc_consorcio
                 , 'S'::varchar as tipo_convidado
              FROM (
                SELECT CASE WHEN licitacao.cod_modalidade = 1  THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 2  THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 3  THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 4  THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 5  THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 6  THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 7  THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 8  THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 9  THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 10 THEN 'OT'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            WHEN licitacao.cod_modalidade = 11 THEN 'RP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                       END AS processo_licitatorio
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                1
                            ELSE
                                2
                       END AS tipo_pessoa
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                sw_cgm_pessoa_fisica.cpf
                            ELSE
                                sw_cgm_pessoa_juridica.cnpj
                       END AS cpf_cnpj
                     , sw_cgm.nom_cgm as nom_cgm
                  FROM licitacao.licitacao                    
                  JOIN licitacao.participante
                    ON licitacao.cod_licitacao  = participante.cod_licitacao
                   AND licitacao.cod_entidade   = participante.cod_entidade
                   AND licitacao.cod_modalidade = participante.cod_modalidade
                   AND licitacao.exercicio      = participante.exercicio
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = participante.numcgm_representante
             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
             LEFT JOIN sw_cgm_pessoa_juridica
                    ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                 WHERE to_char(participante.dt_inclusao,'mm') = '".$this->getDado('mes')."'
                   AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
              GROUP BY sw_cgm_pessoa_fisica.numcgm
                     , sw_cgm_pessoa_fisica.cpf
                     , sw_cgm_pessoa_juridica.cnpj
                     , licitacao.cod_modalidade
                     , licitacao.cod_licitacao
                     , licitacao.exercicio                     
                     , participante.numcgm_representante
                     , sw_cgm.nom_cgm 
                ) AS registros
        ";

        return $stSql;

    }

}
?>
