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

    * Data de Criação   : 22/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 22/10/2007

  * @author Analista: Gelson Wolvowski Gonçalves
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTBAAdConv extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
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
                         , convenio.num_convenio
                         , convenio_aditivos.num_aditivo
                         , SUBSTR(TRIM(convenio_aditivos.objeto), 1, 300) AS objeto_convenio
                         , TO_CHAR(convenio_aditivos.dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura_aditivo
                         , TO_CHAR(convenio_aditivos.dt_vigencia, 'dd/mm/yyyy') AS dt_vencimento_convenio
                         , norma.num_norma||'/'||norma.exercicio AS fundamentacao_legal
                         , SUBSTR(TRIM(cgm_imprensa.nom_cgm), 1, 50) AS imprensa_oficial
                         , TO_CHAR(convenio_aditivos_publicacao.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao_aditivo
                         , convenio_aditivos.valor_convenio
                         , 1 AS tipo_moeda
                         , TO_CHAR(convenio.dt_assinatura, 'yyyymm') AS competencia
                         , TO_CHAR(convenio_aditivos.inicio_execucao,'dd/mm/yyyy') AS data_inicio
                         , '' AS reservado_tcm

                     FROM licitacao.convenio

               INNER JOIN compras.objeto
                       ON convenio.cod_objeto = objeto.cod_objeto

               INNER JOIN licitacao.convenio_aditivos
                       ON convenio.exercicio = convenio_aditivos.exercicio_convenio
                      AND convenio.num_convenio = convenio_aditivos.num_convenio
                      
               INNER JOIN licitacao.convenio_aditivos_publicacao 
                       ON convenio_aditivos_publicacao.num_convenio = convenio_aditivos.num_convenio
                      AND convenio_aditivos_publicacao.exercicio = convenio_aditivos.exercicio_convenio
                      AND convenio_aditivos_publicacao.num_aditivo = convenio_aditivos.num_aditivo

               INNER JOIN sw_cgm AS cgm_imprensa
                       ON convenio_aditivos_publicacao.numcgm = cgm_imprensa.numcgm


               INNER JOIN normas.norma
                       ON norma.cod_norma = convenio_aditivos.cod_norma_autorizativa

                    WHERE NOT EXISTS (
                                        SELECT 1
                                        FROM licitacao.convenio_anulado
                                        WHERE convenio.num_convenio = convenio_anulado.num_convenio
                                            AND convenio.exercicio = convenio_anulado.exercicio
                                    )
                    AND NOT EXISTS (
                                        SELECT 1
                                        FROM licitacao.convenio_aditivos_anulacao
                                        WHERE convenio_aditivos.num_convenio = convenio_aditivos_anulacao.num_convenio
                                            AND convenio_aditivos.exercicio = convenio_aditivos_anulacao.exercicio
                                            AND convenio_aditivos.exercicio_convenio = convenio_aditivos_anulacao.exercicio_convenio
                                            AND convenio_aditivos.num_aditivo = convenio_aditivos_anulacao.num_aditivo
                                    )
                    AND convenio_aditivos.exercicio = '".$this->getDado('exercicio')."'
                    AND convenio_aditivos.dt_assinatura BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
        ";
        
        return $stSql;
    }

}
