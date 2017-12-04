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
 * Mapeamento da tabela ldo.acao_validada
 *
 * @category    Urbem
 * @package     LDO
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

class TLDOAcaoValidada extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ldo.acao_validada');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_acao, ano, timestamp_acao_dados, cod_recurso, exercicio_recurso');

        $this->addCampo('cod_acao'             ,'integer'  , true, ''    , true,  true);
        $this->addCampo('ano'                  ,'varchar', true, '1'   , true,  true);
        $this->addCampo('timestamp_acao_dados' ,'timestamp', true, ''    , true,  true);
        $this->addCampo('cod_recurso'          ,'integer'  , true, ''    , true,  true);
        $this->addCampo('exercicio_recurso'    ,'varchar', true, '4'   , true,  true);
        $this->addCampo('valor'                ,'numeric'  , true, '14,2', false, false);
        $this->addCampo('quantidade'           ,'numeric'  , true, '14,2', false, false);
    }

    /**
     * Método que retorna as acoes nao validadas para a lista
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listAcaoNaoValidada(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao='')
        {
            $stSql = "
                SELECT acao_dados.cod_acao
                     , LPAD(acao.num_acao::varchar,4,'0') AS num_acao
                     , acao_dados.titulo
                     , acao_dados.timestamp_acao_dados AS timestamp
                     , acao_quantidade.ano
                     , (TO_NUMBER(ppa.ano_inicio, '9999') + TO_NUMBER(acao_quantidade.ano, '9') - 1) as exercicio
                     , SUM(acao_quantidade.quantidade) AS quantidade
                     , SUM(acao_quantidade.valor) AS valor
                     , ppa.cod_ppa
                  FROM ppa.acao
            INNER JOIN ppa.acao_dados
                    ON acao.cod_acao                    = acao_dados.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
            INNER JOIN ppa.acao_quantidade
                    ON acao.cod_acao                    = acao_quantidade.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_quantidade.timestamp_acao_dados
            INNER JOIN ppa.programa
                    ON acao.cod_programa = programa.cod_programa
            INNER JOIN ppa.programa_setorial
                    ON programa.cod_setorial = programa_setorial.cod_setorial
            INNER JOIN ppa.macro_objetivo
                    ON programa_setorial.cod_macro = macro_objetivo.cod_macro
            INNER JOIN ppa.ppa
                    ON macro_objetivo.cod_ppa = ppa.cod_ppa
            --join para os totais de quantidades
             LEFT JOIN ( SELECT acao_quantidade.cod_acao
                              , acao_quantidade.timestamp_acao_dados
                              , (SUM(acao_quantidade.quantidade) - SUM(COALESCE(acao_validada.quantidade,0))) AS quantidade
                           FROM ppa.acao_quantidade
                      LEFT JOIN ldo.acao_validada
                             ON acao_quantidade.cod_acao             = acao_validada.cod_acao
                            AND acao_quantidade.ano                  = acao_validada.ano
                            AND acao_quantidade.timestamp_acao_dados = acao_validada.timestamp_acao_dados
                       GROUP BY acao_quantidade.cod_acao
                              , acao_quantidade.timestamp_acao_dados
                       ) AS acao_quantidade_disponivel
                    ON acao_dados.cod_acao             = acao_quantidade_disponivel.cod_acao
                   AND acao_dados.timestamp_acao_dados = acao_quantidade_disponivel.timestamp_acao_dados
                 WHERE ppa.cod_ppa = ".$this->getDado('cod_ppa')."
                   AND acao_quantidade.ano = '".$this->getDado('ano')."'
                   AND ppa.fn_verifica_homologacao(ppa.cod_ppa)
                   AND NOT EXISTS ( SELECT 1
                                      FROM ldo.acao_validada
                                     WHERE acao_quantidade.cod_acao             = acao_validada.cod_acao
                                       AND acao_quantidade.ano                  = acao_validada.ano
                                       AND acao_quantidade.timestamp_acao_dados = acao_validada.timestamp_acao_dados
                                       AND acao_quantidade.exercicio_recurso    = acao_validada.exercicio_recurso
                                       AND acao_quantidade.cod_recurso          = acao_validada.cod_recurso )
            ";
            $stSql .= $stFiltro;

            $stSql .= '
                GROUP BY  acao_dados.cod_acao
                        , acao.num_acao
                        , acao_dados.titulo
                        , acao_dados.timestamp_acao_dados
                        , acao_quantidade.ano
                        , acao_quantidade_disponivel.quantidade
                        , ppa.ano_inicio
                        , ppa.cod_ppa';

            return $this->executaRecuperaSql($stSql, $rsRecordSet, '', $stOrder, $boTransacao);
        }

    /**
     * Método que retorna as acoes validadas para a lista
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listAcaoValidada(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao='')
    {
        $stSql  = "\n        SELECT acao_dados.cod_acao";
        $stSql .= "\n             , LPAD(acao.num_acao::VARCHAR, 4, '0') AS num_acao";
        $stSql .= "\n             , acao_dados.titulo";
        $stSql .= "\n             , acao_validada.ano";
        $stSql .= "\n             , acao_dados.timestamp_acao_dados AS timestamp";
        $stSql .= "\n             , SUM(acao_validada.valor) AS valor";
        $stSql .= "\n             , (TO_NUMBER(ppa.ano_inicio, '9999') + TO_NUMBER(acao_validada.ano, '9') - 1) as exercicio";
        $stSql .= "\n             , SUM(acao_validada.quantidade) AS quantidade";
        $stSql .= "\n             , ppa.cod_ppa";
        $stSql .= "\n          FROM ldo.acao_validada";
        $stSql .= "\n    INNER JOIN ppa.acao_quantidade";
        $stSql .= "\n            ON acao_quantidade.cod_acao             = acao_validada.cod_acao";
        $stSql .= "\n           AND acao_quantidade.ano                  = acao_validada.ano";
        $stSql .= "\n           AND acao_quantidade.timestamp_acao_dados = acao_validada.timestamp_acao_dados";
        $stSql .= "\n           AND acao_quantidade.cod_recurso          = acao_validada.cod_recurso";
        $stSql .= "\n           AND acao_quantidade.exercicio_recurso    = acao_validada.exercicio_recurso";
        $stSql .= "\n    INNER JOIN ppa.acao_dados";
        $stSql .= "\n            ON acao_dados.cod_acao             = acao_quantidade.cod_acao";
        $stSql .= "\n           AND acao_dados.timestamp_acao_dados = acao_quantidade.timestamp_acao_dados";
        $stSql .= "\n    INNER JOIN ppa.acao";
        $stSql .= "\n            ON acao.cod_acao                    = acao_dados.cod_acao";
        $stSql .= "\n           AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados";
        $stSql .= "\n    INNER JOIN ppa.programa";
        $stSql .= "\n            ON programa.cod_programa = acao.cod_programa";
        $stSql .= "\n    INNER JOIN ppa.programa_setorial";
        $stSql .= "\n            ON programa_setorial.cod_setorial = programa.cod_setorial";
        $stSql .= "\n    INNER JOIN ppa.macro_objetivo";
        $stSql .= "\n            ON macro_objetivo.cod_macro = programa_setorial.cod_macro";
        $stSql .= "\n    INNER JOIN ppa.ppa";
        $stSql .= "\n            ON macro_objetivo.cod_ppa = ppa.cod_ppa";
        $stSql .= "\n         WHERE ppa.cod_ppa         = ".$this->getDado('cod_ppa');
        $stSql .= "\n           AND acao_quantidade.ano = '".$this->getDado('ano')."'";
        $stSql .= "\n           AND ppa.fn_verifica_homologacao(ppa.cod_ppa)";

        $stSql .= $stFiltro;

        $stSql .= "\n      GROUP BY acao_dados.cod_acao";
        $stSql .= "\n             , acao.num_acao";
        $stSql .= "\n             , acao_dados.titulo";
        $stSql .= "\n             , acao_validada.ano";
        $stSql .= "\n             , acao_dados.timestamp_acao_dados";
        $stSql .= "\n             , ppa.ano_inicio";
        $stSql .= "\n             , ppa.cod_ppa";

        return $this->executaRecuperaSql($stSql, $rsRecordSet, '', $stOrder, $boTransacao);
    }

    /**
     * Método que retorna as acoes validadas para a lista de despesas
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listAcaoValidadaDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT LPAD(acao.num_acao::varchar,4,0::varchar) AS num_acao
                 , acao_dados.cod_acao
                 , acao_dados.titulo
                 , acao_dados.timestamp_acao_dados AS timestamp
                 , acao_validada.ano
                 , ppa.cod_ppa
              FROM ppa.acao
        INNER JOIN ppa.acao_dados
                ON acao.cod_acao                    = acao_dados.cod_acao
               AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
        INNER JOIN ldo.acao_validada
                ON acao_dados.cod_acao             = acao_validada.cod_acao
               AND acao_dados.timestamp_acao_dados = acao_validada.timestamp_acao_dados
        INNER JOIN ppa.programa
                ON acao.cod_programa = programa.cod_programa
        INNER JOIN ppa.programa_setorial
                ON programa.cod_setorial = programa_setorial.cod_setorial
        INNER JOIN ppa.macro_objetivo
                ON programa_setorial.cod_macro = macro_objetivo.cod_macro
        INNER JOIN ppa.ppa
                ON macro_objetivo.cod_ppa = ppa.cod_ppa
        --join para os totais de quantidades
         LEFT JOIN ( SELECT acao_quantidade.cod_acao
                          , acao_quantidade.timestamp_acao_dados
                          , (SUM(acao_quantidade.quantidade) - SUM(COALESCE(acao_validada.quantidade,0))) AS quantidade
                          , acao_quantidade.cod_recurso
                       FROM ppa.acao_quantidade
                  LEFT JOIN ldo.acao_validada
                         ON acao_quantidade.cod_acao             = acao_validada.cod_acao
                        AND acao_quantidade.ano                  = acao_validada.ano
                        AND acao_quantidade.timestamp_acao_dados = acao_validada.timestamp_acao_dados
                        AND acao_validada.ano::integer <> " . $this->getDado('ano') . "
                   GROUP BY acao_quantidade.cod_acao
                          , acao_quantidade.timestamp_acao_dados
                          , acao_quantidade.cod_recurso
                   ) AS acao_quantidade_disponivel
                ON acao_dados.cod_acao             = acao_quantidade_disponivel.cod_acao
               AND acao_dados.timestamp_acao_dados = acao_quantidade_disponivel.timestamp_acao_dados
             WHERE ppa.cod_ppa = " . $this->getDado('cod_ppa') . "
               AND acao_validada.ano::integer = " . $this->getDado('ano') . "
               AND ppa.fn_verifica_homologacao(ppa.cod_ppa) ".$stFiltro."
          GROUP BY acao_dados.cod_acao
                 , acao.num_acao
                 , acao_dados.titulo
                 , acao_dados.timestamp_acao_dados
                 , acao_validada.ano
                 , ppa.cod_ppa
        ";

        return $this->executaRecuperaSql($stSql, $rsRecordSet, '', $stOrder, $boTransacao);
    }

    /**
     * Método que retorna os exercicios do ldo para o ppa
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listAcaoLDO(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT acao_validada.ano
                 , ppa.cod_ppa
                 , (ppa.ano_inicio::NUMERIC - 1 + acao_validada.ano::NUMERIC) AS ano_ldo
              FROM ppa.acao
        INNER JOIN ppa.acao_dados
                ON acao.cod_acao                    = acao_dados.cod_acao
               AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
        INNER JOIN ldo.acao_validada
                ON acao_dados.cod_acao             = acao_validada.cod_acao
               AND acao_dados.timestamp_acao_dados = acao_validada.timestamp_acao_dados
        INNER JOIN ppa.programa
                ON acao.cod_programa = programa.cod_programa
        INNER JOIN ppa.programa_setorial
                ON programa.cod_setorial = programa_setorial.cod_setorial
        INNER JOIN ppa.macro_objetivo
                ON programa_setorial.cod_macro = macro_objetivo.cod_macro
        INNER JOIN ppa.ppa
                ON macro_objetivo.cod_ppa = ppa.cod_ppa
             WHERE ppa.cod_ppa = " . $this->getDado('cod_ppa') . "
               AND ppa.fn_verifica_homologacao(ppa.cod_ppa)
               AND NOT ldo.fn_verifica_homologacao_ldo(ppa.cod_ppa,acao_validada.ano)
          GROUP BY ppa.cod_ppa
                 , acao_validada.ano
                 , ppa.ano_inicio
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function recuperaListagemRecursos(&$rsRecordSet, $stCondicao = '' , $stOrdem = '' , $boTransacao = '')
    {
        $stSql  = "\n     SELECT acao_validada.cod_acao";
        $stSql .= "\n          , acao_validada.ano";
        $stSql .= "\n          , acao_validada.timestamp_acao_dados";
        $stSql .= "\n          , acao_validada.cod_recurso";
        $stSql .= "\n          , recurso.cod_fonte AS cod_recurso_formatado";
        $stSql .= "\n          , recurso.nom_recurso";
        $stSql .= "\n          , acao_validada.exercicio_recurso";
        $stSql .= "\n          , acao_validada.valor";
        $stSql .= "\n          , TO_REAL(acao_validada.valor) AS valor_formatado";
        $stSql .= "\n          , acao_validada.quantidade";
        $stSql .= "\n          , TO_REAL(acao_validada.quantidade) AS quantidade_formatado";
        $stSql .= "\n          , TO_REAL(acao_quantidade.quantidade) AS quantidade_disponivel";
        $stSql .= "\n       FROM ldo.acao_validada";
        $stSql .= "\n INNER JOIN ppa.acao_quantidade";
        $stSql .= "\n         ON acao_quantidade.cod_acao             = acao_validada.cod_acao";
        $stSql .= "\n        AND acao_quantidade.ano                  = acao_validada.ano";
        $stSql .= "\n        AND acao_quantidade.timestamp_acao_dados = acao_validada.timestamp_acao_dados";
        $stSql .= "\n        AND acao_quantidade.cod_recurso          = acao_validada.cod_recurso";
        $stSql .= "\n        AND acao_quantidade.exercicio_recurso    = acao_validada.exercicio_recurso";
        $stSql .= "\n INNER JOIN orcamento.recurso";
        $stSql .= "\n         ON recurso.cod_recurso = acao_quantidade.cod_recurso";
        $stSql .= "\n        AND recurso.exercicio = acao_quantidade.exercicio_recurso";

        if ($this->getDado('cod_acao')) {
            $stSql .= "\n        AND acao_validada.cod_acao = ".$this->getDado('cod_acao');
        }
        if ($this->getDado('timestamp_acao_dados')) {
            $stSql .= "\n        AND acao_validada.timestamp_acao_dados = '".$this->getDado('timestamp_acao_dados')."'";
        }
        if ($this->getDado('ano')) {
            $stSql .= "\n        AND acao_validada.ano = '".$this->getDado('ano')."'";
        }
        if ($this->getDado('cod_recurso')) {
            $stSql .= "\n        AND acao_validada.cod_recurso = ".$this->getDado('cod_recurso');
        }
        if ($this->getDado('exercicio_recurso')) {
            $stSql .= "\n        AND acao_validada.exercicio_recurso = '".$this->getDado('exercicio_recurso')."'";
        }
        if ($this->getDado('valor')) {
            $stSql .= "\n        AND acao_validada.valor = '".$this->getDado('valor')."'";
        }
        if ($this->getDado('quantidade')) {
            $stSql .= "\n        AND acao_validada.quantidade = '".$this->getDado('quantidade')."'";
        }

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}

?>
