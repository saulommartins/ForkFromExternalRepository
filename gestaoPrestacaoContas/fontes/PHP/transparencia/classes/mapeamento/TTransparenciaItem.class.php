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
 * Classe de mapeamento da tabela EMPENHO.ITEM_PRE_EMPENHO
 * Data de Criação: 30/11/2004

 * @author Desenvolvedor: Diogo Zarpelon

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso: uc-02.03.03

 $Id:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTransparenciaItem extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TTransparenciaItem()
    {
        parent::Persistente();
        $this->setTabela('empenho.item_pre_empenho');

        $this->setCampoCod('num_item');
        $this->setComplementoChave('cod_pre_empenho,exercicio');

        $this->AddCampo('cod_pre_empenho', 'integer', true, '',     true,  true);
        $this->AddCampo('exercicio'      , 'char'   , true, '04',   true,  true);
        $this->AddCampo('num_item'       , 'integer', true, '',     true,  false);
        $this->AddCampo('cod_unidade'    , 'integer', true, '',     false, true);
        $this->AddCampo('cod_grandeza'   , 'integer', true, '',     false, true);
        $this->AddCampo('quantidade'     , 'numeric', true, '14.4', false, false);
        $this->AddCampo('nom_unidade'    , 'varchar', true, '80',   false, false);
        $this->AddCampo('sigla_unidade'  , 'varchar', true, '20',   false, false);
        $this->AddCampo('vl_total'       , 'numeric', true, '14.2', false, false);
        $this->AddCampo('nom_item'       , 'varchar', true, '160',  false, false);
        $this->AddCampo('complemento'    , 'text'   , true, '',     false, false);
    }

    public function recuperaTransparenciaItem(&$rsRecordSet, $stCondicao = "", $boTransacao = "" , $stExercicio = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaTransparenciaItem($stFiltro);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL ($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaTransparenciaItem($stFiltro)
    {
        $stSql = "
        SELECT
               (empenho.exercicio || LPAD(empenho.cod_entidade::varchar,2,'0') || LPAD(empenho.cod_empenho::varchar,7,'0')) AS numero_empenho
             , empenho.cod_entidade                    AS cod_entidade
             , empenho.exercicio                       AS exercicio
             , TO_CHAR(empenho.dt_empenho, 'ddmmyyyy') AS data
             , item_pre_empenho.num_item               AS numero_item
             , item_pre_empenho.nom_item               AS descricao
             , item_pre_empenho.nom_unidade            AS unidade
             , item_pre_empenho.vl_total               AS valor
             , item_pre_empenho.quantidade             AS quantidade
             , '+'                                     AS sinal_valor
             , item_pre_empenho.complemento            AS complemento

          FROM empenho.item_pre_empenho

    INNER JOIN empenho.pre_empenho
            ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
           AND pre_empenho.exercicio       = item_pre_empenho.exercicio

    INNER JOIN empenho.empenho
            ON empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND empenho.exercicio       = pre_empenho.exercicio

         WHERE 1=1 ";

        if ($this->getDado('stExercicio')) {
            $stSql .= " AND empenho.exercicio = '".$this->getDado('stExercicio')."'";
        }

        if ($this->getDado('dtInicial') && $this->getDado('dtFinal')) {
           $stSql .= " AND empenho.dt_empenho BETWEEN to_date('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFinal')."','dd/mm/yyyy')";
        }

        if ($this->getDado('stCodEntidades')) {
           $stSql .= " AND empenho.cod_entidade IN ('".$this->getDado('stCodEntidades')."')";
        }

    $stSql .= "

     UNION ALL

        SELECT
               (empenho.exercicio || LPAD(empenho.cod_entidade::varchar,2,'0') || LPAD(empenho.cod_empenho::varchar,7,'0')) AS numero_empenho
             , empenho.cod_entidade                                      AS cod_entidade
             , empenho.exercicio                                         AS exercicio
             , TO_CHAR(empenho_anulado_item.timestamp::DATE, 'ddmmyyyy') AS data
             , empenho_anulado_item.num_item                             AS numero_item
             , 'Estorno de Empenho'                                      AS descricao
             , ''                                                        AS unidade
             , empenho_anulado_item.vl_anulado                           AS valor
             , 0                                                         AS quantidade
             , '-'                                                       AS sinal_valor
             , empenho_anulado.motivo                                    AS complemento

          FROM empenho.empenho_anulado_item

    INNER JOIN empenho.empenho_anulado
            ON empenho_anulado.exercicio    = empenho_anulado_item.exercicio
           AND empenho_anulado.cod_entidade = empenho_anulado_item.cod_entidade
           AND empenho_anulado.cod_empenho  = empenho_anulado_item.cod_empenho
           AND empenho_anulado.timestamp    = empenho_anulado_item.timestamp

    INNER JOIN empenho.empenho
            ON empenho.cod_empenho  = empenho_anulado.cod_empenho
           AND empenho.exercicio    = empenho_anulado.exercicio
           AND empenho.cod_entidade = empenho_anulado.cod_entidade

         WHERE 1=1 ";

        if ($this->getDado('stExercicio')) {
            $stSql .= " AND empenho.exercicio = '".$this->getDado('stExercicio')."'";
        }

        if ($this->getDado('dtInicial') && $this->getDado('dtFinal')) {
           $stSql .= " AND empenho.dt_empenho BETWEEN to_date('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFinal')."','dd/mm/yyyy')";
        }

        if ($this->getDado('stCodEntidades')) {
           $stSql .= " AND empenho.cod_entidade IN ('".$this->getDado('stCodEntidades')."')";
        }

        return $stSql;
    }

}
