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

    * Data de Criação   : 19/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 19/10/2007

  * @author Analista: Gelson Wolvowski
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTBAConvenio extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaDadosConvenio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosConvenio().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosConvenio()
    {
        $stSql .= " SELECT 1 AS tipo_registro
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , convenio.num_convenio
                        , SUBSTR(TRIM(objeto.descricao), 1, 60) AS objeto_convenio
                        , TO_CHAR(convenio.dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura_convenio
                        , TO_CHAR(convenio.dt_vigencia, 'dd/mm/yyyy') AS dt_vigencia_convenio
                        , norma.num_norma::VARCHAR||'/'||norma.exercicio::VARCHAR AS fundamentacao_legal
                        , SUBSTR(TRIM(cgm_imprensa.nom_cgm), 1, 50) AS imprensa_oficial
                        , TO_CHAR(publicacao_convenio.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao_convenio
                        , convenio.valor
                        , 1 AS tipo_moeda
                        , TO_CHAR(convenio.dt_assinatura, 'yyyymm') AS competencia
                        , TO_CHAR(convenio.inicio_execucao, 'dd/mm/yyyy') AS inicio_execucao
                        , 0 AS num_orgao
                        , 0 AS num_unidade
                        , 0 AS cod_programa
                        , 0 AS tipo_projeto_atividade
                        , 0 AS codigo_projeto_atividade
                        , 0 AS cod_despesa
                        , 0 AS fonte_recurso
                        , convenio.exercicio AS ano
                        , 0 AS cod_funcao
                        , 0 AS cod_subfuncao
                        , 'S'::VARCHAR AS contrato_anterior_siga

                    FROM licitacao.convenio

                    INNER JOIN compras.objeto
                            ON convenio.cod_objeto = objeto.cod_objeto

                    INNER JOIN licitacao.publicacao_convenio
                            ON publicacao_convenio.num_convenio = publicacao_convenio.num_convenio
                        AND publicacao_convenio.exercicio = publicacao_convenio.exercicio

                    INNER JOIN sw_cgm AS cgm_imprensa
                            ON publicacao_convenio.numcgm = cgm_imprensa.numcgm

                    INNER JOIN normas.norma
                            ON norma.cod_norma = convenio.cod_norma_autorizativa

                    WHERE NOT EXISTS (
                                        SELECT 1
                                        FROM licitacao.convenio_anulado
                                        WHERE convenio.num_convenio = convenio_anulado.num_convenio
                                            AND convenio.exercicio = convenio_anulado.exercicio
                                    )";
        return $stSql;
    }

}
