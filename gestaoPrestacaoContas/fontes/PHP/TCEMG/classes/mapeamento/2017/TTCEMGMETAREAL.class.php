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
    * Classe de mapeamento da tabela TTCEMGMETAREAL
    * Data de Criação: 20/04/2016

    * @author Analista:      Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Michel Teixeira

    $Id: TTCEMGMETAREAL.class.php 65079 2016-04-20 18:19:53Z michel $

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEMGMETAREAL extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosMETAREAL10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosMETAREAL10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;
        else
            $stOrdem = "ORDER BY meta_realizada.cod_acao";

        $stSql = $this->montaRecuperaDadosMETAREAL10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosMETAREAL10()
    {
        $stSql  = " SELECT 10 AS tiporegistro
                         , ( SELECT valor
                              FROM administracao.configuracao_entidade
                             WHERE exercicio    = meta_realizada.exercicio_recurso
                               AND parametro    = 'tcemg_codigo_orgao_entidade_sicom'
                               AND cod_entidade = ".$this->getDado('entidade')."
                           ) AS codOrgao
                         , LPAD((LPAD(''||acao_unidade_executora.num_orgao,2, '0')||LPAD(''||acao_unidade_executora.num_unidade,2, '0')), 5, '0') AS codunidadesub
                         , LPAD((acao.num_acao::varchar), 4, '0') AS cod_acao
                         , NULL::INTEGER AS cod_sub_acao
                         , LPAD(acao_dados.cod_funcao::VARCHAR, 2, '0') AS cod_funcao
                         , acao_dados.cod_subfuncao
                         , LPAD(programa.num_programa::VARCHAR, 4, '0') AS cod_programa
                         , meta_realizada.valor
                         , meta_realizada.justificativa

                      FROM ppa.acao_meta_fisica_realizada AS meta_realizada

                INNER JOIN ppa.acao_recurso
                        ON acao_recurso.cod_acao = meta_realizada.cod_acao
                       AND acao_recurso.timestamp_acao_dados = meta_realizada.timestamp_acao_dados
                       AND acao_recurso.cod_recurso = meta_realizada.cod_recurso
                       AND acao_recurso.exercicio_recurso = meta_realizada.exercicio_recurso
                       AND acao_recurso.ano = meta_realizada.ano

                INNER JOIN ppa.acao_dados
                        ON acao_recurso.cod_acao = acao_dados.cod_acao
                       AND acao_recurso.timestamp_acao_dados = acao_dados.timestamp_acao_dados

                INNER JOIN ppa.acao_unidade_executora
                        ON acao_unidade_executora.cod_acao = acao_dados.cod_acao
                       AND acao_unidade_executora.timestamp_acao_dados = acao_dados.timestamp_acao_dados

                INNER JOIN ppa.acao
                        ON acao.cod_acao = acao_dados.cod_acao

                INNER JOIN ppa.programa
                        ON programa.cod_programa = acao.cod_programa

                     WHERE meta_realizada.exercicio_recurso = '".$this->getDado('exercicio')."'
        ";
        return $stSql;
    }

    public function __destruct(){}

}
