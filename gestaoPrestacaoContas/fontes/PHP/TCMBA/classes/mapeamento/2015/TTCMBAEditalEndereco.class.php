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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 03/09/2015

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Jean Silva

    $Id $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAEditalEndereco extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio());
    }

    public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosTribunal()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , edital.num_edital
                        , CASE WHEN edital.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 1   THEN 1
                               WHEN edital.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 2   THEN 2
                               WHEN edital.cod_modalidade = 3 AND licitacao.registro_precos = TRUE  THEN 3
                               WHEN edital.cod_modalidade = 5                                       THEN 4
                               WHEN edital.cod_modalidade = 1 AND tipo_objeto.cod_tipo_objeto = 1   THEN 5
                               WHEN edital.cod_modalidade = 1 AND tipo_objeto.cod_tipo_objeto = 2   THEN 6
                               WHEN edital.cod_modalidade = 4                                       THEN 7
                               WHEN edital.cod_modalidade = 2 AND tipo_objeto.cod_tipo_objeto = 1   THEN 10
                               WHEN edital.cod_modalidade = 2 AND tipo_objeto.cod_tipo_objeto = 2   THEN 12
                               WHEN edital.cod_modalidade = 6 AND licitacao.registro_precos = FALSE THEN 14
                               WHEN edital.cod_modalidade = 7 AND licitacao.registro_precos = FALSE THEN 15
                               WHEN edital.cod_modalidade = 1 AND licitacao.registro_precos = TRUE  THEN 16
                               WHEN edital.cod_modalidade = 2 AND licitacao.registro_precos = TRUE  THEN 17
                               WHEN edital.cod_modalidade = 6 AND licitacao.registro_precos = TRUE  THEN 18
                               WHEN edital.cod_modalidade = 7 AND licitacao.registro_precos = TRUE  THEN 19
                               WHEN edital.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 4   THEN 22
                               WHEN edital.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 3   THEN 23
                        END AS modalidade
                        , edital.local_entrega_propostas AS logradouro
                        , '' AS endereco
                        , '' AS complemento
                        , '' AS bairro
                        , '' AS cep
                        , (SELECT valor
                             FROM administracao.configuracao
                            WHERE cod_modulo = 2 AND exercicio = edital.exercicio AND parametro = 'cod_municipio_ibge'
                          ) AS municipio_ibge
                        , 1 AS finalidade
                        , TO_CHAR(edital.dt_aprovacao_juridico,'yyyymm') AS competencia

                     FROM licitacao.edital

               INNER JOIN licitacao.licitacao
                       ON licitacao.cod_licitacao  = edital.cod_licitacao
                      AND licitacao.cod_modalidade = edital.cod_modalidade
                      AND licitacao.cod_entidade   = edital.cod_entidade   
                      AND licitacao.exercicio      = edital.exercicio_licitacao

               INNER JOIN compras.tipo_objeto
                       ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto

                    WHERE edital.exercicio = '".$this->getDado('exercicio')."'
                      AND edital.cod_entidade IN (".$this->getDado('entidades').")
                      AND edital.dt_aprovacao_juridico BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                      AND edital.cod_modalidade NOT IN (8,9)
            ";
        return $stSql;
    }

}
