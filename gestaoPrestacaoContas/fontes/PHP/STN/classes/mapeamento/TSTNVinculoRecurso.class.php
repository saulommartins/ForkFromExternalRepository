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
    * Classe de mapeamento da tabela STN.VINCULO_RECURSO
    * Data de Criação: 08/05/2008

    * @author Analista: Tonismar Regis Bernardo
    * @author Desenvolvedor: Leopoldo Braga Barreiro

    * $Id: TSTNVinculoRecurso.class.php 66353 2016-08-16 20:04:08Z michel $

    * Casos de uso:

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TSTNVinculoRecurso extends Persistente
{

    /**
        * Método Construtor
    */
    public function TSTNVinculoRecurso()
    {

        parent::Persistente();

        $this->setTabela('stn.vinculo_recurso');

        $this->setCampoCod('');
        $this->setComplementoChave( 'exercicio, cod_entidade, num_orgao, num_unidade, cod_recurso, cod_vinculo, cod_tipo' );

        $this->AddCampo( 'exercicio'   , 'char'   , true, '04', true , true );
        $this->AddCampo( 'cod_entidade', 'integer', true,   '', true , true );
        $this->AddCampo( 'num_orgao'   , 'integer', true,   '', true , true );
        $this->AddCampo( 'num_unidade' , 'integer', true,   '', true , true );
        $this->AddCampo( 'cod_recurso' , 'integer', true,   '', true , true );
        $this->AddCampo( 'cod_vinculo' , 'integer', true,   '', false, true );
        $this->AddCampo( 'cod_tipo'    , 'integer', true,   '', true , true );
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = " SELECT   vinculo_recurso.exercicio
                        ,   vinculo_recurso.cod_entidade
                        ,   vinculo_recurso.num_orgao
                        ,   vinculo_recurso.num_unidade
                        ,   recurso.cod_recurso
                        ,   recurso.nom_recurso
                        ,   vinculo_recurso.cod_vinculo
                        ,   vinculo_recurso.cod_tipo
                        ,   acao.cod_acao
                        ,   acao.num_acao
                        ,   acao_dados.titulo AS nom_acao
                        ,   vinculo_recurso_acao.cod_tipo_educacao
                     FROM   stn.vinculo_recurso
               INNER JOIN   orcamento.recurso
                       ON   recurso.exercicio = vinculo_recurso.exercicio
                      AND   recurso.cod_recurso = vinculo_recurso.cod_recurso
                LEFT JOIN   stn.vinculo_recurso_acao
                       ON   vinculo_recurso_acao.exercicio = vinculo_recurso.exercicio
                      AND   vinculo_recurso_acao.cod_entidade = vinculo_recurso.cod_entidade
                      AND   vinculo_recurso_acao.num_orgao = vinculo_recurso.num_orgao
                      AND   vinculo_recurso_acao.num_unidade = vinculo_recurso.num_unidade
                      AND   vinculo_recurso_acao.cod_recurso = vinculo_recurso.cod_recurso
                      AND   vinculo_recurso_acao.cod_vinculo = vinculo_recurso.cod_vinculo
                      AND   vinculo_recurso_acao.cod_tipo = vinculo_recurso.cod_tipo
                LEFT JOIN   ppa.acao
                       ON   acao.cod_acao = vinculo_recurso_acao.cod_acao
                      AND   acao.ativo = TRUE
                LEFT JOIN   ppa.acao_dados
                       ON   acao.cod_acao = acao_dados.cod_acao
                      AND   acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
                    WHERE   true ";
                    
        if ($this->getDado("exercicio")) {
            $stSql .= " AND vinculo_recurso.exercicio = '".$this->getDado("exercicio")."'";
        }

        if ($this->getDado("cod_entidade")) {
            $stSql .= " AND vinculo_recurso.cod_entidade IN ('".$this->getDado("cod_entidade")."') ";
        }

        if ($this->getDado("num_orgao")) {
            $stSql .= " AND vinculo_recurso.num_orgao = '".$this->getDado("num_orgao")."'";
        }

        if ($this->getDado("num_unidade")) {
            $stSql .= " AND vinculo_recurso.num_unidade = '".$this->getDado("num_unidade")."'";
        }

        if ($this->getDado("cod_recurso")) {
            $stSql .= " AND vinculo_recurso.cod_recurso = ".$this->getDado("cod_recurso");
        }
        
        if ($this->getDado("cod_vinculo")) {
            $stSql .= " AND vinculo_recurso.cod_vinculo = ".$this->getDado("cod_vinculo");
        }

        return $stSql;
    }

    public function montaRecuperaVinculoRecurso()
    {
        $stSql = " SELECT   vinculo_recurso.exercicio
                        ,   vinculo_recurso.cod_entidade
                        ,   vinculo_recurso.num_orgao
                        ,   vinculo_recurso.num_unidade
                        ,   vinculo_recurso.cod_recurso
                        ,   vinculo_recurso.cod_vinculo
                        ,   recurso.nom_recurso
                        ,   acao.cod_acao
                        ,   acao.num_acao
                        ,   acao_dados.titulo AS nom_acao
                        ,   vinculo_recurso_acao.cod_tipo_educacao
                     FROM   stn.vinculo_recurso
               INNER JOIN    orcamento.recurso
                       ON   recurso.exercicio = vinculo_recurso.exercicio
                      AND   recurso.cod_recurso = vinculo_recurso.cod_recurso
                LEFT JOIN   stn.vinculo_recurso_acao
                       ON   vinculo_recurso_acao.exercicio = vinculo_recurso.exercicio
                      AND   vinculo_recurso_acao.cod_entidade = vinculo_recurso.cod_entidade
                      AND   vinculo_recurso_acao.num_orgao = vinculo_recurso.num_orgao
                      AND   vinculo_recurso_acao.num_unidade = vinculo_recurso.num_unidade
                      AND   vinculo_recurso_acao.cod_recurso = vinculo_recurso.cod_recurso
                      AND   vinculo_recurso_acao.cod_vinculo = vinculo_recurso.cod_vinculo
                      AND   vinculo_recurso_acao.cod_tipo = vinculo_recurso.cod_tipo
                LEFT JOIN   ppa.acao
                       ON   acao.cod_acao = vinculo_recurso_acao.cod_acao
                      AND   acao.ativo = TRUE
                LEFT JOIN   ppa.acao_dados
                       ON   acao.cod_acao = acao_dados.cod_acao
                      AND   acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
                    WHERE   true ";
        if ($this->getDado("exercicio")) {
            $stSql .= " AND vinculo_recurso.exercicio = '".$this->getDado("exercicio")."'";
        }

        if ($this->getDado("cod_entidade")) {
            $stSql .= " AND vinculo_recurso.cod_entidade = '".$this->getDado("cod_entidade")."'";
        }

        if ($this->getDado("num_orgao")) {
            $stSql .= " AND vinculo_recurso.num_orgao = '".$this->getDado("num_orgao")."'";
        }

        if ($this->getDado("num_unidade") !== false) {
            $stSql .= " AND vinculo_recurso.num_unidade = '".$this->getDado("num_unidade")."'";
        }

        if ($this->getDado("cod_recurso")) {
            $stSql .= " AND vinculo_recurso.cod_recurso = ".$this->getDado("cod_recurso");
        }

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaVinculoRecurso.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaVinculoRecurso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem)) $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaVinculoRecurso().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaExcluirVinculoRecurso()
    {
        $stSql = "
        DELETE
          FROM stn.vinculo_recurso_acao
         WHERE vinculo_recurso_acao.exercicio = '".$this->getDado('exercicio')."'
           AND vinculo_recurso_acao.cod_entidade = ".$this->getDado('cod_entidade')."
           AND vinculo_recurso_acao.num_orgao = ".$this->getDado('num_orgao')."
           AND vinculo_recurso_acao.num_unidade = ".$this->getDado('num_unidade')."
           AND vinculo_recurso_acao.cod_vinculo = ".$this->getDado('cod_vinculo').";

        DELETE
          FROM stn.vinculo_recurso
         WHERE vinculo_recurso.exercicio = '".$this->getDado('exercicio')."'
           AND vinculo_recurso.cod_entidade = ".$this->getDado('cod_entidade')."
           AND vinculo_recurso.num_orgao = ".$this->getDado('num_orgao')."
           AND vinculo_recurso.num_unidade = ".$this->getDado('num_unidade')."
           AND vinculo_recurso.cod_vinculo = ".$this->getDado('cod_vinculo').";"
        ;

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaVinculoRecurso.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function excluirVinculoRecurso($boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $this->setDebug( 'exclusao' );

        $stSql = $this->montaExcluirVinculoRecurso().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaDML( $stSql, $boTransacao );

        return $obErro;
    }
}

?>
