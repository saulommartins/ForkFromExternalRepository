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
  * Página de
  * Data de criação : 25/10/2005

  * @author Analista:
  * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.01.04

    $Id: TPatrimonioGrupo.class.php 65901 2016-06-28 14:22:28Z michel $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioGrupo extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela('patrimonio.grupo');
        $this->setCampoCod('cod_grupo');
        $this->setComplementoChave('cod_natureza');
        $this->AddCampo('cod_grupo'    ,'integer' ,true ,''    ,true  ,false);
        $this->AddCampo('cod_natureza' ,'integer' ,true ,''    ,true  ,"TPatrimonioNatureza");
        $this->AddCampo('nom_grupo'    ,'varchar' ,true ,'60'  ,false ,false);
        $this->AddCampo('depreciacao'  ,'numeric' ,true ,'4.2' ,false ,false);

    }

    public function recuperaGrupo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaGrupo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaGrupo()
    {
        $stSql = "
            SELECT grupo.cod_grupo
                 , grupo.nom_grupo
                 , COALESCE(grupo.depreciacao,'0.00') as depreciacao
                 , natureza.cod_natureza
                 , natureza.nom_natureza
                 , grupo_plano_analitica.cod_plano
                 , plano_conta_contabil.nom_conta
                 , grupo_plano_analitica.cod_plano_doacao
                 , plano_conta_contabil_doacao.nom_conta_doacao
                 , grupo_plano_analitica.cod_plano_perda_involuntaria
                 , plano_conta_contabil_perda.nom_conta_perda
                 , grupo_plano_analitica.cod_plano_transferencia
                 , plano_conta_contabil_transferencia.nom_conta_transferencia
                 , grupo_plano_analitica.cod_plano_alienacao_ganho
                 , plano_conta_contabil_alienacao_ganho.nom_conta_alienacao_ganho
                 , grupo_plano_analitica.cod_plano_alienacao_perda
                 , plano_conta_contabil_alienacao_perda.nom_conta_alienacao_perda
                 , grupo_depreciacao.nom_conta AS nom_conta_depreciacao
                 , grupo_depreciacao.cod_plano AS cod_plano_depreciacao
                 , grupo_reavaliacao.cod_plano AS cod_plano_reavaliacao
                 , grupo_reavaliacao.nom_conta AS nom_conta_reavaliacao

              FROM patrimonio.grupo

        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza

         LEFT JOIN patrimonio.grupo_plano_analitica
                ON grupo_plano_analitica.cod_natureza = grupo.cod_natureza
               AND grupo_plano_analitica.cod_grupo    = grupo.cod_grupo
               AND grupo_plano_analitica.exercicio    = '".Sessao::getExercicio()."'

         LEFT JOIN (
                SELECT plano_analitica.cod_plano
                     , plano_analitica.exercicio
                     , plano_conta.nom_conta AS nom_conta
                  FROM contabilidade.plano_analitica

             LEFT JOIN contabilidade.plano_conta
                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                   AND plano_conta.exercicio = plano_analitica.exercicio
                   ) AS plano_conta_contabil
                ON plano_conta_contabil.cod_plano = grupo_plano_analitica.cod_plano
               AND plano_conta_contabil.exercicio = grupo_plano_analitica.exercicio

         LEFT JOIN (
                SELECT plano_analitica.cod_plano
                     , plano_analitica.exercicio
                     , plano_conta.nom_conta AS nom_conta_doacao
                  FROM contabilidade.plano_analitica

             LEFT JOIN contabilidade.plano_conta
                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                   AND plano_conta.exercicio = plano_analitica.exercicio
                   ) AS plano_conta_contabil_doacao
                ON plano_conta_contabil_doacao.cod_plano = grupo_plano_analitica.cod_plano_doacao
               AND plano_conta_contabil_doacao.exercicio = grupo_plano_analitica.exercicio

         LEFT JOIN (
                SELECT plano_analitica.cod_plano
                     , plano_analitica.exercicio
                     , plano_conta.nom_conta AS nom_conta_perda
                  FROM contabilidade.plano_analitica

             LEFT JOIN contabilidade.plano_conta
                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                   AND plano_conta.exercicio = plano_analitica.exercicio
                   ) AS plano_conta_contabil_perda
                ON plano_conta_contabil_perda.cod_plano = grupo_plano_analitica.cod_plano_perda_involuntaria
               AND plano_conta_contabil_perda.exercicio = grupo_plano_analitica.exercicio

         LEFT JOIN (
                SELECT plano_analitica.cod_plano
                     , plano_analitica.exercicio
                     , plano_conta.nom_conta AS nom_conta_transferencia
                  FROM contabilidade.plano_analitica

             LEFT JOIN contabilidade.plano_conta
                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                   AND plano_conta.exercicio = plano_analitica.exercicio
                   ) AS plano_conta_contabil_transferencia
                ON plano_conta_contabil_transferencia.cod_plano = grupo_plano_analitica.cod_plano_transferencia
               AND plano_conta_contabil_transferencia.exercicio = grupo_plano_analitica.exercicio

    LEFT JOIN (
            SELECT plano_analitica.cod_plano
                 , plano_analitica.exercicio
                 , plano_conta.nom_conta AS nom_conta_alienacao_ganho
              FROM contabilidade.plano_analitica

         LEFT JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
              ) AS plano_conta_contabil_alienacao_ganho
           ON plano_conta_contabil_alienacao_ganho.cod_plano = grupo_plano_analitica.cod_plano_alienacao_ganho
          AND plano_conta_contabil_alienacao_ganho.exercicio = grupo_plano_analitica.exercicio

    LEFT JOIN (
            SELECT plano_analitica.cod_plano
                 , plano_analitica.exercicio
                 , plano_conta.nom_conta AS nom_conta_alienacao_perda
              FROM contabilidade.plano_analitica

         LEFT JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
            ) AS plano_conta_contabil_alienacao_perda
           ON plano_conta_contabil_alienacao_perda.cod_plano = grupo_plano_analitica.cod_plano_alienacao_perda
          AND plano_conta_contabil_alienacao_perda.exercicio = grupo_plano_analitica.exercicio

    LEFT JOIN (
            SELECT grupo_plano_depreciacao.cod_plano AS cod_plano
                 , plano_conta.nom_conta
                 , grupo_plano_depreciacao.cod_natureza
                 , grupo_plano_depreciacao.cod_grupo
                 , grupo_plano_depreciacao.exercicio
              FROM patrimonio.grupo_plano_depreciacao

         LEFT JOIN contabilidade.plano_analitica
                ON plano_analitica.cod_plano = grupo_plano_depreciacao.cod_plano
               AND plano_analitica.exercicio = grupo_plano_depreciacao.exercicio

         LEFT JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
             ) AS grupo_depreciacao
          ON grupo_depreciacao.cod_natureza = grupo.cod_natureza
         AND grupo_depreciacao.cod_grupo    = grupo.cod_grupo
         AND grupo_depreciacao.exercicio    = '".Sessao::getExercicio()."'

    LEFT JOIN (
            SELECT grupo_plano_reavaliacao.cod_plano AS cod_plano
                 , plano_conta.nom_conta
                 , grupo_plano_reavaliacao.cod_natureza
                 , grupo_plano_reavaliacao.cod_grupo
                 , grupo_plano_reavaliacao.exercicio
              FROM patrimonio.grupo_plano_reavaliacao

         LEFT JOIN contabilidade.plano_analitica
                ON plano_analitica.cod_plano = grupo_plano_reavaliacao.cod_plano
               AND plano_analitica.exercicio = grupo_plano_reavaliacao.exercicio

         LEFT JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
             ) AS grupo_reavaliacao
          ON grupo_reavaliacao.cod_natureza = grupo.cod_natureza
         AND grupo_reavaliacao.cod_grupo    = grupo.cod_grupo
         AND grupo_reavaliacao.exercicio    = '".Sessao::getExercicio()."'
         ";

        return $stSql;
    }

    public function recuperaMaxGrupo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMaxGrupo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMaxGrupo()
    {
        $stSql = "
            SELECT max(grupo.cod_grupo) as max
              FROM patrimonio.grupo
        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza
        INNER JOIN patrimonio.grupo_plano_analitica
                ON grupo_plano_analitica.cod_natureza = grupo.cod_natureza
               AND grupo_plano_analitica.cod_grupo = grupo.cod_grupo
        INNER JOIN contabilidade.plano_analitica
                ON plano_analitica.cod_plano = grupo_plano_analitica.cod_plano
               AND plano_analitica.exercicio = grupo_plano_analitica.exercicio
        INNER JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
        ";

        return $stSql;
    }

    public function recuperaMaxGrupoCombo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMaxGrupoCombo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMaxGrupoCombo()
    {
        $stSql = "
            SELECT max(grupo.cod_grupo) as max
              FROM patrimonio.grupo
        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza
        ";

        return $stSql;
    }

    public function recuperaDadosGrupo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosGrupo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDadosGrupo()
    {
        $stSql  = "
            SELECT grupo.*
                 , natureza.*

              FROM patrimonio.grupo

        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza

             WHERE 1=1 ";

        if ($this->getDado('cod_grupo')) {
            $stSql.= " AND grupo.cod_grupo = ".$this->getDado('cod_grupo');
        }

        if ($this->getDado('cod_natureza')) {
            $stSql.= " AND grupo.cod_natureza = ".$this->getDado('cod_natureza');
        }

        return $stSql;
    }

    public function recuperaGrupoPlanoDepreciacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaGrupoPlanoDepreciacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaGrupoPlanoDepreciacao()
    {
        $stSql  = "
                   SELECT grupo_plano_depreciacao.cod_plano
                        , bem.cod_bem
                        , grupo_plano_depreciacao.exercicio

                     FROM patrimonio.grupo_plano_depreciacao

               INNER JOIN patrimonio.grupo
                       ON grupo.cod_natureza = grupo_plano_depreciacao.cod_natureza
                      AND grupo.cod_grupo    = grupo_plano_depreciacao.cod_grupo

               INNER JOIN patrimonio.especie
                       ON especie.cod_grupo    = grupo.cod_grupo
                      AND especie.cod_natureza = grupo.cod_natureza

               INNER JOIN patrimonio.bem
                       ON bem.cod_especie  = especie.cod_especie
                      AND bem.cod_grupo    = especie.cod_grupo
                      AND bem.cod_natureza = especie.cod_natureza

               INNER JOIN patrimonio.depreciacao
                       ON depreciacao.cod_bem = bem.cod_bem

                   WHERE NOT EXISTS ( SELECT 1 
                                 FROM patrimonio.depreciacao_anulada
                                WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                  AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                  AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                             )
                     AND grupo_plano_depreciacao.exercicio        = '".Sessao::getExercicio()."'
                     AND substring(depreciacao.competencia, 1,4 ) = '".Sessao::getExercicio()."' \n";

        if ($this->getDado('cod_bem')) {
            $stSql .= " AND depreciacao.cod_bem = ".$this->getDado('cod_bem');
        }

        if ($this->getDado('cod_plano_grupo')) {
            $stSql .= " AND grupo_plano_depreciacao.cod_plano = ".$this->getDado('cod_plano_grupo');
        }

        if ($this->getDado('cod_grupo')) {
            $stSql.= " AND grupo.cod_grupo = ".$this->getDado('cod_grupo');
        }

        if ($this->getDado('cod_natureza')) {
            $stSql.= " AND grupo.cod_natureza = ".$this->getDado('cod_natureza');
        }

        return $stSql;
    }

}

?>