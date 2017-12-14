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
    * Extensão da Classe de mapeamento TOrcamentoProjetoAtividade
    * Data de Criação: 13/03/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 37865 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2009-02-04 16:21:42 -0200 (Qua, 04 Fev 2009) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 13/03/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTCEAMItens extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEAMItens()
{
    parent::Persistente();
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaTodos()
{
    $stSql  =
    "SELECT * FROM (
                              SELECT
                                   CASE WHEN homologacao.cod_modalidade = 1  THEN 'CC'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 2  THEN 'TP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 3  THEN 'CO'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 4  THEN 'LE'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 5  THEN 'CP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 6  THEN 'PR'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 7  THEN 'PE'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 8  THEN 'DL'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 9  THEN 'IL'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 10 THEN 'OT'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                        WHEN homologacao.cod_modalidade = 11 THEN 'RP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                   END AS processo_licitatorio
                                 , catalogo_item.descricao as descricao
                                 , replace(to_number(to_char(sum(cotacao_item.quantidade),'9999999999.99999'),'9999999999.99999')::varchar,'.',',') as quantidade_itens
                                 , to_char(homologacao.timestamp, 'dd/mm/YYYY') as data_homologacao
                                 , to_char(configuracao_arquivo_licitacao.dt_publicacao_homologacao,'dd/mm/YYYY') as data_publicacao_homologacao
                                 , LPAD(cotacao_item.cod_item::varchar,8,'0')||LPAD(cotacao_item.lote::varchar,2,'0') as controle_item_lote
                              FROM licitacao.homologacao
                        INNER JOIN licitacao.licitacao
                                ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                               AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                               AND homologacao.cod_entidade        = licitacao.cod_entidade
                               AND homologacao.exercicio_licitacao = licitacao.exercicio
                        INNER JOIN compras.julgamento_item
                                ON homologacao.cod_item          = julgamento_item.cod_item
                               AND homologacao.lote              = julgamento_item.lote
                               AND homologacao.cgm_fornecedor    = julgamento_item.cgm_fornecedor
                               AND homologacao.cod_cotacao       = julgamento_item.cod_cotacao
                               AND homologacao.exercicio_cotacao = julgamento_item.exercicio
                        INNER JOIN compras.cotacao_fornecedor_item
                                ON julgamento_item.cod_item          = cotacao_fornecedor_item.cod_item
                               AND julgamento_item.lote              = cotacao_fornecedor_item.lote
                               AND julgamento_item.cgm_fornecedor    = cotacao_fornecedor_item.cgm_fornecedor
                               AND julgamento_item.cod_cotacao       = cotacao_fornecedor_item.cod_cotacao
                               AND julgamento_item.exercicio         = cotacao_fornecedor_item.exercicio
                        INNER JOIN compras.cotacao_item
                                ON cotacao_fornecedor_item.cod_item    = cotacao_item.cod_item
                               AND cotacao_fornecedor_item.lote        = cotacao_item.lote
                               AND cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
                               AND cotacao_fornecedor_item.exercicio   = cotacao_item.exercicio
                        INNER JOIN almoxarifado.catalogo_item
                                ON catalogo_item.cod_item        = cotacao_item.cod_item
                        INNER JOIN compras.mapa_cotacao
                                ON homologacao.cod_cotacao       = mapa_cotacao.cod_cotacao
                               AND homologacao.exercicio_cotacao = mapa_cotacao.exercicio_cotacao
                         LEFT JOIN tceam.configuracao_arquivo_licitacao
                                ON configuracao_arquivo_licitacao.cod_mapa  = mapa_cotacao.cod_mapa
                               AND configuracao_arquivo_licitacao.exercicio = mapa_cotacao.exercicio_mapa
                        WHERE cotacao_item.exercicio = '".$this->getDado('exercicio')."'
                               AND julgamento_item.ordem = 1 \n";

    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND licitacao.cod_entidade  in  ( ".$this->getDado('stEntidades')." )  \n";
    }

    if ( $this->getDado('inMes') ) {
        $stSql .= " AND to_char(licitacao.timestamp, 'mm') = '".$this->getDado('inMes')."' \n";
    }

    $stSql .=
    "GROUP BY                      homologacao.cod_licitacao
                                 , homologacao.cod_modalidade
                                 , homologacao.cod_entidade
                                 , homologacao.exercicio_licitacao
                                 , homologacao.cgm_fornecedor
                                 , homologacao.timestamp
                                 , catalogo_item.descricao
                                 , cotacao_item.cod_item
                                 , cotacao_item.lote
                                 , configuracao_arquivo_licitacao.dt_publicacao_homologacao

                        UNION

                            SELECT
                                   CASE WHEN compra_direta.cod_modalidade = 1 THEN 'CC'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                        WHEN compra_direta.cod_modalidade = 2 THEN 'TP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                        WHEN compra_direta.cod_modalidade = 3 THEN 'CO'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                        WHEN compra_direta.cod_modalidade = 4 THEN 'LE'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                        WHEN compra_direta.cod_modalidade = 5 THEN 'CP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                        WHEN compra_direta.cod_modalidade = 6 THEN 'PR'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                        WHEN compra_direta.cod_modalidade = 7 THEN 'PE'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                        WHEN compra_direta.cod_modalidade = 8 THEN 'DL'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                        WHEN compra_direta.cod_modalidade = 9 THEN 'IL'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                   END AS processo_licitatorio
                                 , catalogo_item.descricao
                                 , replace(to_number(to_char(sum(mapa_item.quantidade),'9999999999.99999'),'9999999999.99999')::varchar,'.',',') as quantidade_itens
                                 , to_char(c_homologacao.timestamp, 'dd/mm/YYYY') as data_homologacao
                                 , to_char(configuracao_arquivo_licitacao.dt_publicacao_homologacao,'dd/mm/YYYY') as data_publicacao_homologacao
                                 , LPAD(mapa_item.cod_item::varchar,8,'0')||LPAD(mapa_item.lote::varchar,2,'0') as controle_item_lote
                              FROM compras.mapa_item
                        INNER JOIN compras.compra_direta
                                ON compra_direta.cod_mapa = mapa_item.cod_mapa
                               AND compra_direta.exercicio_mapa = mapa_item.exercicio
                        INNER JOIN compras.homologacao c_homologacao
                                ON c_homologacao.cod_compra_direta = compra_direta.cod_compra_direta
                               AND c_homologacao.cod_modalidade = compra_direta.cod_modalidade
                               AND c_homologacao.cod_entidade = compra_direta.cod_entidade
                               AND c_homologacao.cod_entidade = compra_direta.cod_entidade
                        INNER JOIN almoxarifado.catalogo_item
                                ON catalogo_item.cod_item = mapa_item.cod_item
                         LEFT JOIN tceam.configuracao_arquivo_licitacao
                                ON configuracao_arquivo_licitacao.cod_mapa = mapa_item.cod_mapa
                               AND configuracao_arquivo_licitacao.exercicio = mapa_item.exercicio
                      WHERE mapa_item.exercicio = '".$this->getDado('exercicio')."' \n";

    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND mapa_item.cod_entidade  in  ( ".$this->getDado('stEntidades')." )  \n";
    }

    if ( $this->getDado('inMes') ) {
        $stSql .= " AND to_char(compra_direta.timestamp, 'mm') = '".$this->getDado('inMes')."'  \n";
    }
    $stSql .= "GROUP BY compra_direta.cod_compra_direta
                                 , compra_direta.exercicio_mapa
                                 , catalogo_item.descricao
                                 , configuracao_arquivo_licitacao.dt_publicacao_homologacao
                                 , mapa_item.cod_mapa
                                 , mapa_item.cod_item
                                 , mapa_item.lote
                                 , compra_direta.cod_modalidade
                                 , compra_direta.exercicio_entidade
                                 , c_homologacao.timestamp
                                 , mapa_item.cod_entidade
                                 , compra_direta.cod_modalidade
              ) AS tabela
              ORDER BY tabela.processo_licitatorio
                     , controle_item_lote";

    return $stSql;
}
public function recuperaItemLicitacaoREM(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaItemLicitacaoREM",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaItemLicitacaoREM()
{
    $stSql  =
    "SELECT * FROM (
                              SELECT CASE WHEN homologacao.cod_modalidade = 1  THEN 'CC'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 2  THEN 'TP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 3  THEN 'CO'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 4  THEN 'LE'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 5  THEN 'CP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 6  THEN 'PR'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 7  THEN 'PE'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 8  THEN 'DL'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 9  THEN 'IL'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 10 THEN 'OT'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                                    WHEN homologacao.cod_modalidade = 11 THEN 'RP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                            END AS processo_licitatorio
                                         , catalogo_item.descricao as descricao
                                         , replace(to_number(to_char(sum(cotacao_item.quantidade),'9999999999.99999'),'9999999999.99999')::varchar,'.',',') as quantidade_itens
                                         , to_char(homologacao.timestamp, 'YYYYmmdd') as data_homologacao
                                         , to_char(configuracao_arquivo_licitacao.dt_publicacao_homologacao,'YYYYmmdd') as data_publicacao_homologacao
                                         , unidade_medida.nom_unidade as unidade_medida
                                         , LPAD(cotacao_item.cod_item::varchar,8,'0')||LPAD(cotacao_item.lote::varchar,2,'0') as controle_item_lote
                                         , CASE WHEN homologacao.homologado = 't' THEN
                                                    01
                                                    WHEN licitacao_anulada.deserta = 't' THEN
                                                    02
                                                    WHEN licitacao_anulada.deserta = 'f' THEN
                                                    05
                                            END status_item
                                FROM licitacao.homologacao
                        INNER JOIN licitacao.licitacao
                                    ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                                  AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                                  AND homologacao.cod_entidade        = licitacao.cod_entidade
                                  AND homologacao.exercicio_licitacao = licitacao.exercicio
                        INNER JOIN compras.julgamento_item
                                    ON homologacao.cod_item          = julgamento_item.cod_item
                                  AND homologacao.lote              = julgamento_item.lote
                                  AND homologacao.cgm_fornecedor    = julgamento_item.cgm_fornecedor
                                  AND homologacao.cod_cotacao       = julgamento_item.cod_cotacao
                                  AND homologacao.exercicio_cotacao = julgamento_item.exercicio
                        INNER JOIN compras.cotacao_fornecedor_item
                                    ON julgamento_item.cod_item          = cotacao_fornecedor_item.cod_item
                                  AND julgamento_item.lote              = cotacao_fornecedor_item.lote
                                  AND julgamento_item.cgm_fornecedor    = cotacao_fornecedor_item.cgm_fornecedor
                                  AND julgamento_item.cod_cotacao       = cotacao_fornecedor_item.cod_cotacao
                                  AND julgamento_item.exercicio         = cotacao_fornecedor_item.exercicio
                        INNER JOIN compras.cotacao_item
                                    ON cotacao_fornecedor_item.cod_item    = cotacao_item.cod_item
                                  AND cotacao_fornecedor_item.lote        = cotacao_item.lote
                                  AND cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
                                  AND cotacao_fornecedor_item.exercicio   = cotacao_item.exercicio
                        INNER JOIN almoxarifado.catalogo_item
                                    ON catalogo_item.cod_item        = cotacao_item.cod_item
                          INNER JOIN administracao.unidade_medida
                                ON unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                               AND  unidade_medida.cod_unidade = catalogo_item.cod_unidade
                        INNER JOIN compras.mapa_cotacao
                                    ON homologacao.cod_cotacao       = mapa_cotacao.cod_cotacao
                                  AND homologacao.exercicio_cotacao = mapa_cotacao.exercicio_cotacao
                          LEFT JOIN tceam.configuracao_arquivo_licitacao
                                   ON configuracao_arquivo_licitacao.cod_mapa  = mapa_cotacao.cod_mapa
                                 AND configuracao_arquivo_licitacao.exercicio = mapa_cotacao.exercicio_mapa
                         LEFT JOIN licitacao.licitacao_anulada 
                                  ON licitacao_anulada.cod_licitacao= licitacao.cod_licitacao 
                                AND licitacao_anulada.cod_modalidade =  licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade 
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                                
                            WHERE cotacao_item.exercicio = '".$this->getDado('exercicio')."'
                                AND julgamento_item.ordem = 1 \n";

    if ( $this->getDado('stEntidades') ) {
        $stSql .= "       AND licitacao.cod_entidade  in  ( ".$this->getDado('stEntidades')." )  \n";
    }

    if ( $this->getDado('inMes') ) {
        $stSql .= "       AND to_char(licitacao.timestamp, 'mm') = '".$this->getDado('inMes')."' \n";
    }

    $stSql .=
    "GROUP BY              homologacao.cod_licitacao
                                 , homologacao.cod_modalidade
                                 , homologacao.cod_entidade
                                 , homologacao.exercicio_licitacao
                                 , homologacao.cgm_fornecedor
                                 , homologacao.timestamp
                                 , catalogo_item.descricao
                                 , cotacao_item.cod_item
                                 , cotacao_item.lote
                                 , unidade_medida
                                 , configuracao_arquivo_licitacao.dt_publicacao_homologacao
                                 , status_item
              ) AS tabela
              ORDER BY tabela.processo_licitatorio
                     , controle_item_lote";

    return $stSql;
}

public function recuperaItemAdesaoAtaREM(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaItemAdesaoAtaREM",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaItemAdesaoAtaREM()
{
    $stSql  =
    "SELECT * FROM (
                        SELECT
                                CASE WHEN homologacao.cod_modalidade = 1  THEN 'CC'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 2  THEN 'TP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 3  THEN 'CO'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 4  THEN 'LE'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 5  THEN 'CP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 6  THEN 'PR'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 7  THEN 'PE'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 8  THEN 'DL'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 9  THEN 'IL'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 10 THEN 'OT'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                     WHEN homologacao.cod_modalidade = 11 THEN 'RP'||homologacao.cod_licitacao::varchar||'-'||homologacao.exercicio_licitacao::varchar
                                END AS processo
                              , replace(to_number(to_char((item_pre_empenho.vl_total / cotacao_item.quantidade),'9999999999.99'),'9999999999.99')::varchar,'.',',') AS valor
                              , catalogo_item.descricao as descricao
                              , replace(to_number(to_char(sum(cotacao_item.quantidade),'9999999999.99999'),'9999999999.99999')::varchar,'.',',') as quantidade
                              , unidade_medida.nom_unidade as unidade_medida
                              , LPAD(cotacao_item.cod_item::varchar,8,'0')||LPAD(cotacao_item.lote::varchar,2,'0') as controle_item_lote
                              , ata.num_ata
                                
                         FROM licitacao.homologacao
                         
                        JOIN licitacao.licitacao
                          ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                         AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                         AND homologacao.cod_entidade        = licitacao.cod_entidade
                         AND homologacao.exercicio_licitacao = licitacao.exercicio
                         
                        JOIN compras.julgamento_item
                          ON homologacao.cod_item          = julgamento_item.cod_item
                         AND homologacao.lote              = julgamento_item.lote
                         AND homologacao.cgm_fornecedor    = julgamento_item.cgm_fornecedor
                         AND homologacao.cod_cotacao       = julgamento_item.cod_cotacao
                         AND homologacao.exercicio_cotacao = julgamento_item.exercicio
                         
                        JOIN compras.cotacao_fornecedor_item
                          ON julgamento_item.cod_item          = cotacao_fornecedor_item.cod_item
                         AND julgamento_item.lote              = cotacao_fornecedor_item.lote
                         AND julgamento_item.cgm_fornecedor    = cotacao_fornecedor_item.cgm_fornecedor
                         AND julgamento_item.cod_cotacao       = cotacao_fornecedor_item.cod_cotacao
                         AND julgamento_item.exercicio         = cotacao_fornecedor_item.exercicio
                         
                        JOIN compras.cotacao_item
                          ON cotacao_fornecedor_item.cod_item    = cotacao_item.cod_item
                         AND cotacao_fornecedor_item.lote        = cotacao_item.lote
                         AND cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
                         AND cotacao_fornecedor_item.exercicio   = cotacao_item.exercicio
                         
                        JOIN almoxarifado.catalogo_item
                          ON catalogo_item.cod_item        = cotacao_item.cod_item
                          
                        JOIN administracao.unidade_medida
                          ON unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                         AND  unidade_medida.cod_unidade = catalogo_item.cod_unidade
                         
                        JOIN compras.mapa_cotacao
                          ON homologacao.cod_cotacao       = mapa_cotacao.cod_cotacao
                         AND homologacao.exercicio_cotacao = mapa_cotacao.exercicio_cotacao
                         
                   LEFT JOIN tceam.configuracao_arquivo_licitacao
                          ON configuracao_arquivo_licitacao.cod_mapa  = mapa_cotacao.cod_mapa
                         AND configuracao_arquivo_licitacao.exercicio = mapa_cotacao.exercicio_mapa
                         
                   LEFT JOIN licitacao.licitacao_anulada 
                          ON licitacao_anulada.cod_licitacao= licitacao.cod_licitacao 
                         AND licitacao_anulada.cod_modalidade =  licitacao.cod_modalidade
                         AND licitacao_anulada.cod_entidade = licitacao.cod_entidade 
                         AND licitacao_anulada.exercicio = licitacao.exercicio
                         
                        JOIN licitacao.edital
                          ON edital.cod_licitacao = licitacao.cod_licitacao
                         AND edital.cod_modalidade = licitacao.cod_modalidade
                         AND edital.cod_entidade = licitacao.cod_entidade
                         AND edital.exercicio = licitacao.exercicio
                         
                        JOIN licitacao.ata
                          ON ata.num_edital = edital.num_edital
                         AND ata.exercicio = edital.exercicio
                         
                        JOIN empenho.item_pre_empenho_julgamento
                          ON item_pre_empenho_julgamento.exercicio = julgamento_item.exercicio
                         AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao
                         AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item
                         AND item_pre_empenho_julgamento.lote = julgamento_item.lote
                         AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor
                         
                        JOIN empenho.item_pre_empenho
                          ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                         AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
                         AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item
                         
                       WHERE cotacao_item.exercicio = '".$this->getDado('exercicio')."'
                         AND julgamento_item.ordem = 1 \n";
                         
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "      AND licitacao.cod_entidade  in  ( ".$this->getDado('stEntidades')." )  \n";
    }

    if ( $this->getDado('inMes') ) {
        $stSql .= "      AND to_char(ata.dt_validade_ata, 'mm') = '".$this->getDado('inMes')."' \n";
    }

    $stSql .=
    "               GROUP BY  homologacao.cod_licitacao
                            , homologacao.cod_modalidade
                            , homologacao.cod_entidade
                            , homologacao.exercicio_licitacao
                            , homologacao.cgm_fornecedor
                            , homologacao.timestamp
                            , catalogo_item.descricao
                            , cotacao_item.cod_item
                            , cotacao_item.lote
                            , unidade_medida
                            , item_pre_empenho.vl_total
                            , cotacao_item.quantidade
                            , ata.num_ata
                ) AS tabela
        ORDER BY tabela.processo
               , controle_item_lote";
               
    return $stSql;
}

}
