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
 * Classe de mapeamento da tabela ppa.acao_quantidade
 * Data de Criação: 03/10/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso: uc-02.09.04
 */

class TPPAAcaoQuantidade extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.acao_quantidade');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_acao, timestamp_acao_dados, ano');

        $this->addCampo('cod_acao'            , 'integer'  , true, ''    , true , true);
        $this->addCampo('timestamp_acao_dados', 'timestamp', true, ''    , true , true);
        $this->addCampo('ano'                 , 'varchar'  , true, '1'   , true , false);
        $this->addCampo('valor'               , 'numeric'  , true, '14,2', false, false);
        $this->addCampo('quantidade'          , 'numeric'  , true, '14,2', false, false);
        $this->addCampo('cod_recurso'         , 'integer'  , true, ''    , true , true);
        $this->addCampo('exercicio_recurso'   , 'varchar'  , true, '4'   , true , true);
    }

    public function recuperaListagemRecursos(&$rsRecordSet, $stCondicao = '' , $stOrdem = '' , $boTransacao = '')
    {
        $stSql  = "\n     SELECT acao_quantidade.cod_acao";
        $stSql .= "\n          , acao_quantidade.timestamp_acao_dados";
        $stSql .= "\n          , acao_quantidade.ano";
        $stSql .= "\n          , acao_quantidade.valor";
        $stSql .= "\n          , TO_REAL(acao_quantidade.valor) AS valor_formatado";
        $stSql .= "\n          , acao_quantidade.quantidade";
        $stSql .= "\n          , TO_REAL(acao_quantidade.quantidade, '9,999,999,990.9999') AS quantidade_formatado";
        $stSql .= "\n          , (acao_quantidade.quantidade * acao_quantidade.valor) AS total";
        $stSql .= "\n          , TO_REAL((acao_quantidade.quantidade * acao_quantidade.valor),'99,999,999,999,990.99') AS total_formatado";
        $stSql .= "\n          , acao_quantidade.cod_recurso";
        $stSql .= "\n          , recurso.cod_fonte AS cod_recurso_formatado";
        $stSql .= "\n          , acao_quantidade.exercicio_recurso";
        $stSql .= "\n          , recurso.nom_recurso";
        $stSql .= "\n          , TO_REAL(acao_quantidade.quantidade, '9,999,999,990.9999') AS quantidade_disponivel";
        $stSql .= "\n       FROM ppa.acao_quantidade";
        $stSql .= "\n INNER JOIN orcamento.recurso";
        $stSql .= "\n         ON recurso.cod_recurso = acao_quantidade.cod_recurso";
        $stSql .= "\n        AND recurso.exercicio = acao_quantidade.exercicio_recurso";
        $stSql .= "\n      WHERE NOT EXISTS (";
        $stSql .= "\n                         SELECT 1";
        $stSql .= "\n                           FROM ldo.acao_validada";
        $stSql .= "\n                          WHERE acao_validada.cod_acao             = acao_quantidade.cod_acao";
        $stSql .= "\n                            AND acao_validada.ano                  = acao_quantidade.ano";
        $stSql .= "\n                            AND acao_validada.timestamp_acao_dados = acao_quantidade.timestamp_acao_dados";
        $stSql .= "\n                            AND acao_validada.cod_recurso          = acao_quantidade.cod_recurso";
        $stSql .= "\n                            AND acao_validada.exercicio_recurso    = acao_quantidade.exercicio_recurso";
        $stSql .= "\n                       )";

        if ($this->getDado('cod_acao')) {
            $stSql .= "\n        AND acao_quantidade.cod_acao = ".$this->getDado('cod_acao');
        }
        if ($this->getDado('timestamp_acao_dados')) {
            $stSql .= "\n        AND acao_quantidade.timestamp_acao_dados = '".$this->getDado('timestamp_acao_dados')."'";
        }
        if ($this->getDado('ano')) {
            $stSql .= "\n        AND acao_quantidade.ano = '".$this->getDado('ano')."'";
        }
        if ($this->getDado('cod_recurso')) {
            $stSql .= "\n        AND acao_quantidade.cod_recurso = ".$this->getDado('cod_recurso');
        }
        if ($this->getDado('exercicio_recurso')) {
            $stSql .= "\n        AND acao_quantidade.exercicio_recurso = '".$this->getDado('exercicio_recurso')."'";
        }
        if ($this->getDado('valor')) {
            $stSql .= "\n        AND acao_quantidade.valor = '".$this->getDado('valor')."'";
        }
        if ($this->getDado('quantidade')) {
            $stSql .= "\n        AND acao_quantidade.quantidade = '".$this->getDado('quantidade')."'";
        }

        return $this->executaRecuperaSql($stSql, $rsRecordSet, $stCondicao, $stOrdem, $boTransacao);
    }

    public function recuperaQuantidadesAcao(&$rsRecordSet, $stCondicao = '' , $stOrdem = '' , $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaQuantidadesAcao($stCondicao, $stOrdem);
        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    public function montaRecuperaQuantidadesAcao($stFiltro, $stOrdem)
    {
        $stSQL = '';

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL .= "\n SELECT ano1.cod_acao";
        $stSQL .= "\n      , ano1.timestamp_acao_dados";
        $stSQL .= "\n      , ano1.cod_recurso";
        $stSQL .= "\n      , ano1.exercicio_recurso";
        $stSQL .= "\n      , ano1.quantidade AS ano1";
        $stSQL .= "\n      , ano1.valor AS ano1_valor";
        $stSQL .= "\n      , COALESCE(ano2.quantidade, 0.00) AS ano2";
        $stSQL .= "\n      , COALESCE(ano2.valor, 0.00) AS ano2_valor";
        $stSQL .= "\n      , COALESCE(ano3.quantidade, 0.00) AS ano3";
        $stSQL .= "\n      , COALESCE(ano3.valor, 0.00) AS ano3_valor";
        $stSQL .= "\n      , COALESCE(ano4.quantidade, 0.00) AS ano4";
        $stSQL .= "\n      , COALESCE(ano4.valor, 0.00) AS ano4_valor";
        $stSQL .= "\n      , ano1.quantidade + COALESCE(ano2.quantidade, 0.00) + COALESCE(ano3.quantidade, 0.00) + COALESCE(ano4.quantidade, 0.00) as total";
        $stSQL .= "\n      , ano1.valor + COALESCE(ano2.valor, 0.00) + COALESCE(ano3.valor, 0.00) + COALESCE(ano4.valor, 0.00) as total_valor";
        $stSQL .= "\n   FROM ppa.acao_quantidade as ano1";
        $stSQL .= "\n   LEFT JOIN ppa.acao_quantidade as ano2";
        $stSQL .= "\n          ON ano2.ano = '2'";
        $stSQL .= "\n         AND ano1.cod_acao             = ano2.cod_acao";
        $stSQL .= "\n         AND ano1.timestamp_acao_dados = ano2.timestamp_acao_dados";
        $stSQL .= "\n         AND ano1.cod_recurso          = ano2.cod_recurso";
        $stSQL .= "\n   LEFT JOIN ppa.acao_quantidade as ano3";
        $stSQL .= "\n          ON ano3.ano = '3'";
        $stSQL .= "\n         AND ano1.cod_acao             = ano3.cod_acao";
        $stSQL .= "\n         AND ano1.timestamp_acao_dados = ano3.timestamp_acao_dados";
        $stSQL .= "\n         AND ano1.cod_recurso          = ano3.cod_recurso";
        $stSQL .= "\n   LEFT JOIN ppa.acao_quantidade as ano4";
        $stSQL .= "\n          ON ano4.ano = '4'";
        $stSQL .= "\n         AND ano1.cod_acao             = ano4.cod_acao";
        $stSQL .= "\n         AND ano1.timestamp_acao_dados = ano4.timestamp_acao_dados";
        $stSQL .= "\n         AND ano1.cod_recurso          = ano4.cod_recurso";
        $stSQL .= "\n       WHERE ano1.ano = '1'";
        $stSQL .= $stFiltro . $stOrdem;
        
        return $stSQL;
    }
}

?>
